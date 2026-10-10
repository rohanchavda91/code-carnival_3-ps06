<?php
/**
 * Minimal Application Header (Production EdTech Standard)
 * Replaces generic marketing navbar with focused utility controls.
 */
?>
<header id="app-minimal-header" class="minimal-app-header">
  <!-- Left: Back Button (brand logo lives in the left sidebar only) -->
  <div class="header-brand-wrap">
    <button type="button" id="header-btn-back" class="btn btn-ghost btn-header-back hidden" data-i18n-title="btn_back_title" title="Go to previous step" data-i18n-aria-label="btn_back_aria" aria-label="Go Back">
      ← <span data-i18n="btn_back">Back</span>
    </button>
  </div>

  <!-- Right: Minimal Utilities (Language, Theme, Account) -->
  <div class="header-utilities">
    <button type="button" id="header-theme-toggle" class="header-pill-btn" aria-label="Switch to dark mode" aria-pressed="false">
      <span id="header-theme-icon" aria-hidden="true">🌙</span>
    </button>

    <button type="button" id="header-lang-switcher" class="header-pill-btn" data-i18n-title="hdr_lang_switcher" title="Switch interface language" aria-haspopup="dialog">
      <span class="flag" aria-hidden="true">🌐</span>
      <span id="header-active-lang-text">English</span>
    </button>

    <button type="button" id="header-user-menu-btn" class="header-pill-btn" data-i18n-title="hdr_user_menu" title="User Account Menu" data-i18n-aria-label="hdr_user_menu" aria-label="User Account Menu">
      <span id="header-user-avatar-icon" aria-hidden="true">
        <svg class="header-user-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <circle cx="12" cy="8" r="3.5" stroke="currentColor" stroke-width="1.8"/>
          <path d="M5.5 20c.6-3.5 3-5.5 6.5-5.5s5.9 2 6.5 5.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
      </span>
    </button>
  </div>
</header>

<!-- GUEST MODE LIMIT BANNER (anonymous sessions: expiry countdown + daily credits) -->
<div id="guest-limit-banner" class="guest-limit-banner hidden" role="status" aria-live="polite">
  <span class="guest-limit-badge" aria-hidden="true">🎒</span>
  <span id="guest-limit-time" class="guest-limit-time"></span>
  <div id="guest-limit-chips" class="guest-limit-chips"></div>
  <button type="button" id="guest-limit-cta" class="guest-limit-cta" data-i18n="guest_banner_cta">Sign up free</button>
</div>

<!-- Guest Limit Upgrade Gate Modal -->
<div id="modal-guest-limit" class="modal-overlay hidden" role="dialog" aria-modal="true" aria-labelledby="guest-limit-title">
  <div class="modal-card" style="max-width: 430px; width: 100%; padding: 1.75rem; border-radius: var(--radius-xl);">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
      <div style="display: flex; align-items: center; gap: 0.5rem;">
        <span style="font-size: 1.5rem;">🎒</span>
        <h3 id="guest-limit-title" style="font-size: 1.15rem; font-weight: 700; color: var(--text-primary);" data-i18n="guest_limit_title">Daily guest limit reached</h3>
      </div>
      <button type="button" id="guest-limit-close" class="modal-close-btn" data-i18n-aria-label="modal_close_aria" aria-label="Close dialog">✕</button>
    </div>

    <p id="guest-limit-message" style="font-size: 0.95rem; line-height: 1.55; color: var(--text-secondary); margin-bottom: 0.75rem;"></p>
    <div id="guest-limit-detail" class="guest-limit-detail hidden"></div>

    <div style="display: flex; flex-direction: column; gap: 0.6rem; margin-top: 1.25rem;">
      <button type="button" id="guest-limit-signup" class="btn btn-primary" style="width: 100%;" data-i18n="guest_limit_signup">
        Create free account
      </button>
      <button type="button" id="guest-limit-dismiss" class="btn btn-secondary" style="width: 100%;" data-i18n="guest_limit_dismiss">
        Maybe later
      </button>
    </div>
  </div>
</div>

<!-- Quick Language Selector Modal -->
<div id="modal-quick-language" class="modal-overlay hidden" role="dialog" aria-modal="true" aria-labelledby="modal-lang-title">
  <div class="modal-card">
    <div class="modal-header">
      <h3 id="modal-lang-title" data-i18n="lang_step_title">Select Interface Language</h3>
      <button type="button" id="modal-close-lang" class="modal-close-btn" data-i18n-aria-label="modal_close_aria" aria-label="Close dialog">✕</button>
    </div>
    <div class="modal-body">
      <div id="modal-language-list" class="modal-lang-grid">
        <!-- Dynamically injected language items -->
      </div>
    </div>
  </div>
</div>
