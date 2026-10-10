/**
 * Guest Mode Limitations
 * Keeps anonymous ("Continue in Guest Mode") users inside a bounded session:
 *  - a 24 hour guest session that expires,
 *  - a daily credit budget per premium feature (AI Mentor, Quiz Generator,
 *    Notes Maker, Level Assessment),
 *  - an upgrade gate modal when a credit is exhausted or the session expired.
 * Registered accounts are never limited.
 */
const GuestLimits = {
  STORAGE_KEY: 'synapse_guest_state',
  SESSION_HOURS: 24,
  FEATURES: {
    ai_mentor: {
      limit: 10,
      chipKey: 'guest_chip_ai',
      msgKey: 'guest_limit_msg_ai_mentor'
    },
    quiz_generator: {
      limit: 5,
      chipKey: 'guest_chip_quiz',
      msgKey: 'guest_limit_msg_quiz_generator'
    },
    notes_maker: {
      limit: 3,
      chipKey: 'guest_chip_notes',
      msgKey: 'guest_limit_msg_notes_maker'
    },
    level_assessment: {
      limit: 2,
      chipKey: 'guest_chip_test',
      msgKey: 'guest_limit_msg_level_assessment'
    }
  },
  bannerTimer: null,

  // Localized text helper (falls back to English key text when missing).
  t(key, vars = {}, fallback = null) {
    return (typeof I18n !== 'undefined') ? I18n.t(key, vars, fallback) : (fallback || key);
  },

  init() {
    this.bindGateEvents();

    window.addEventListener('synapse:language_changed', () => this.renderBanner());
    window.addEventListener('resize', () => this.renderBanner());
    window.addEventListener('synapse:auth_changed', (event) => {
      const session = event && event.detail;
      // A signed in learner is never limited: drop any leftover guest budget.
      if (session && session.isLoggedIn) {
        try { localStorage.removeItem(this.STORAGE_KEY); } catch (e) { /* ignore */ }
      }
      this.renderBanner();
    });

    // Keep the countdown (and expiry) fresh while the tab stays open.
    this.stopWatching();
    this.bannerTimer = setInterval(() => this.renderBanner(), 30000);
    this.renderBanner();
  },

  stopWatching() {
    if (this.bannerTimer) {
      clearInterval(this.bannerTimer);
      this.bannerTimer = null;
    }
  },

  isGuest() {
    return !(typeof StorageService !== 'undefined' && StorageService.isAuthenticated());
  },

  today() {
    const d = new Date();
    return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
  },

  /**
   * Read (and lazily create / roll over) the guest session state.
   * state = { startedAt, expiresAt, day, usage: { feature: count } }
   */
  getState() {
    let state = null;
    try {
      state = JSON.parse(localStorage.getItem(this.STORAGE_KEY));
    } catch (e) {
      state = null;
    }

    const now = Date.now();
    if (!state || typeof state.expiresAt !== 'number' || typeof state.startedAt !== 'number') {
      state = {
        startedAt: now,
        expiresAt: now + this.SESSION_HOURS * 60 * 60 * 1000,
        day: this.today(),
        usage: {}
      };
      this.saveState(state);
    }

    // Daily credits reset on a new calendar day.
    if (state.day !== this.today()) {
      state.day = this.today();
      state.usage = {};
      this.saveState(state);
    }

    if (!state.usage || typeof state.usage !== 'object') {
      state.usage = {};
    }

    return state;
  },

  saveState(state) {
    try {
      localStorage.setItem(this.STORAGE_KEY, JSON.stringify(state));
    } catch (e) {
      console.warn('Guest state write failed', e);
    }
  },

  isExpired(state = null) {
    const s = state || this.getState();
    return Date.now() >= s.expiresAt;
  },

  getUsage(feature, state = null) {
    const s = state || this.getState();
    return (s.usage && typeof s.usage[feature] === 'number') ? s.usage[feature] : 0;
  },

  /**
   * Non-mutating permission check.
   * Returns { allowed, reason: 'unlimited'|'ok'|'limit'|'expired', used, limit, remaining }
   */
  check(feature) {
    if (!this.isGuest()) {
      return { allowed: true, reason: 'unlimited', used: 0, limit: null, remaining: Infinity };
    }

    const state = this.getState();
    if (this.isExpired(state)) {
      return { allowed: false, reason: 'expired', used: 0, limit: null, remaining: 0, state };
    }

    const cfg = this.FEATURES[feature];
    if (!cfg) {
      return { allowed: true, reason: 'ok', used: 0, limit: null, remaining: Infinity, state };
    }

    const used = this.getUsage(feature, state);
    if (used >= cfg.limit) {
      return { allowed: false, reason: 'limit', used, limit: cfg.limit, remaining: 0, state };
    }

    return {
      allowed: true,
      reason: 'ok',
      used,
      limit: cfg.limit,
      remaining: cfg.limit - used,
      state
    };
  },

  /**
   * Spend one credit for a feature. Shows the upgrade gate when blocked.
   * Returns true when the action may proceed.
   */
  consume(feature) {
    const result = this.check(feature);
    if (result.allowed) {
      if (result.reason === 'ok') {
        const state = result.state || this.getState();
        state.usage[feature] = this.getUsage(feature, state) + 1;
        this.saveState(state);
        this.renderBanner();
      }
      return true;
    }

    this.showGate(feature, result);
    return false;
  },

  /**
   * Human readable status line for a feature (used by feature screens).
   */
  statusText(feature) {
    const result = this.check(feature);
    if (result.reason === 'unlimited') return '';
    if (result.reason === 'expired') {
      return this.t('guest_limit_msg_expired', {}, 'Your free guest session has ended. Create a free account to keep your progress and continue.');
    }
    const cfg = this.FEATURES[feature] || {};
    return this.t(cfg.msgKey || 'guest_limit_short', { limit: result.limit }, 'Daily guest limit reached.');
  },

  // --- UI: banner -------------------------------------------------------

  renderBanner() {
    const banner = document.getElementById('guest-limit-banner');
    if (!banner) return;

    if (!this.isGuest()) {
      banner.classList.add('hidden');
      return;
    }

    // Keep the sticky banner pinned right below the (variable height) header.
    const header = document.getElementById('app-minimal-header');
    if (header) {
      document.documentElement.style.setProperty('--header-height', `${header.offsetHeight}px`);
    }

    const state = this.getState();
    const expired = this.isExpired(state);
    banner.classList.remove('hidden');
    banner.classList.toggle('is-expired', expired);

    const timeEl = document.getElementById('guest-limit-time');
    if (timeEl) {
      if (expired) {
        timeEl.textContent = this.t('guest_banner_expired', {}, 'Guest session expired');
      } else {
        timeEl.textContent = this.t(
          'guest_banner_time_left',
          { time: this.formatTimeLeft(state.expiresAt - Date.now()) },
          `Guest session ends in ${this.formatTimeLeft(state.expiresAt - Date.now())}`
        );
      }
    }

    const chipsEl = document.getElementById('guest-limit-chips');
    if (chipsEl) {
      chipsEl.innerHTML = '';
      Object.keys(this.FEATURES).forEach(feature => {
        const cfg = this.FEATURES[feature];
        const used = this.getUsage(feature, state);
        const chip = document.createElement('span');
        chip.className = `guest-limit-chip${expired || used >= cfg.limit ? ' is-out' : ''}`;
        chip.textContent = `${this.t(cfg.chipKey, {}, feature)} ${used}/${cfg.limit}`;
        chipsEl.appendChild(chip);
      });
    }
  },

  formatTimeLeft(ms) {
    if (ms <= 0) return '0m';
    const totalMinutes = Math.floor(ms / 60000);
    const hours = Math.floor(totalMinutes / 60);
    const minutes = totalMinutes % 60;
    if (hours > 0) return `${hours}h ${minutes}m`;
    if (totalMinutes > 0) return `${totalMinutes}m`;
    return '<1m';
  },

  // --- UI: upgrade gate -------------------------------------------------

  bindGateEvents() {
    const closeBtn = document.getElementById('guest-limit-close');
    const dismissBtn = document.getElementById('guest-limit-dismiss');
    const signupBtn = document.getElementById('guest-limit-signup');
    const bannerCta = document.getElementById('guest-limit-cta');

    if (closeBtn) closeBtn.addEventListener('click', () => this.hideGate());
    if (dismissBtn) dismissBtn.addEventListener('click', () => this.hideGate());

    const openSignup = () => {
      this.hideGate();
      if (typeof Auth !== 'undefined') {
        Auth.openLogin('hub');
        Auth.switchTab('signup');
      }
    };

    if (signupBtn) signupBtn.addEventListener('click', openSignup);
    if (bannerCta) bannerCta.addEventListener('click', openSignup);

    const gateModal = document.getElementById('modal-guest-limit');
    if (gateModal) {
      gateModal.addEventListener('click', (e) => {
        if (e.target === gateModal) this.hideGate();
      });
    }
  },

  showGate(feature, result) {
    const modal = document.getElementById('modal-guest-limit');
    const titleEl = document.getElementById('guest-limit-title');
    const messageEl = document.getElementById('guest-limit-message');
    const detailEl = document.getElementById('guest-limit-detail');

    const expired = result && result.reason === 'expired';
    const cfg = this.FEATURES[feature] || {};

    if (titleEl) {
      titleEl.textContent = expired
        ? this.t('guest_limit_expired_title', {}, 'Guest session expired')
        : this.t('guest_limit_title', {}, 'Daily guest limit reached');
    }

    if (messageEl) {
      messageEl.textContent = expired
        ? this.t(
            'guest_limit_msg_expired',
            {},
            'Your free guest session has ended. Create a free account to keep your progress and continue learning.'
          )
        : this.t(
            cfg.msgKey || 'guest_limit_short',
            { limit: result ? result.limit : '' },
            'You have used all free guest credits for this feature today.'
          );
    }

    if (detailEl) {
      detailEl.textContent = expired
        ? this.t('guest_limit_detail_expired', { hours: this.SESSION_HOURS }, `Guest sessions last ${this.SESSION_HOURS} hours.`)
        : this.t('guest_limit_detail_daily', {}, 'Guest credits reset every day. A free account has no daily limits.');
      detailEl.classList.remove('hidden');
    }

    if (modal) modal.classList.remove('hidden');
    this.renderBanner();
  },

  hideGate() {
    const modal = document.getElementById('modal-guest-limit');
    if (modal) modal.classList.add('hidden');
  }
};

if (typeof module !== 'undefined' && module.exports) {
  module.exports = { GuestLimits };
}
