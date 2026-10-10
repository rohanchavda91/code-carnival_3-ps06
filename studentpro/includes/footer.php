<?php
/**
 * Application Left Sidebar Navigation (collapses to a bottom bar on mobile),
 * Slide-Up More menu drawer, and system status panel.
 */
?>
<!-- LEFT SIDEBAR (desktop) / BOTTOM BAR (mobile) -->
<aside id="app-sidebar" class="app-sidebar" data-i18n-aria-label="sb_aria_label" aria-label="Primary sidebar">

  <!-- Brand block -->
  <div class="sidebar-brand">
    <span class="sidebar-brand-icon" aria-hidden="true">⚡</span>
    <span class="sidebar-brand-text">
      <span class="sidebar-brand-name" data-i18n="app_name">SYNAPSE AI</span>
      <span class="sidebar-brand-sub" data-i18n="sb_brand_sub">Adaptive Learning</span>
    </span>
  </div>

  <!-- Primary Navigation (same IDs/classes as before so existing JS keeps working) -->
  <nav id="app-bottom-nav" class="bottom-nav-bar sidebar-nav" data-i18n-aria-label="sb_nav_aria_label" aria-label="Main Navigation">
    <button type="button" id="nav-item-home" class="bottom-nav-item active" data-i18n-title="nav_home_title" title="Home Dashboard">
      <span class="nav-icon">🏠</span>
      <span class="nav-label" data-i18n="nav_home">Home</span>
    </button>

    <button type="button" id="nav-item-path" class="bottom-nav-item" data-i18n-title="nav_path_title" title="Learning Path">
      <span class="nav-icon">🗺️</span>
      <span class="nav-label" data-i18n="nav_path">Path</span>
    </button>

    <button type="button" id="nav-item-explore-learning" class="bottom-nav-item" data-i18n-title="nav_explore_learning_title" title="Explore Learning">
      <span class="nav-icon" aria-hidden="true">🎓</span>
      <span class="nav-label" data-i18n="nav_explore_learning">Explore Learning</span>
    </button>

    <button type="button" id="nav-item-practice" class="bottom-nav-item" data-i18n-title="nav_practice_title" title="Practice Quiz">
      <span class="nav-icon">⚡</span>
      <span class="nav-label" data-i18n="nav_practice">Practice</span>
    </button>

    <button type="button" id="nav-item-ai" class="bottom-nav-item" data-i18n-title="nav_ai_title" title="AI Assistant">
      <span class="nav-icon">✦</span>
      <span class="nav-label" data-i18n="ai_mentor_title">AI Mentor</span>
    </button>

    <button type="button" id="nav-item-explanations" class="bottom-nav-item" data-i18n-title="nav_videos_title" title="Video Explanations">
      <span class="nav-icon">▶</span>
      <span class="nav-label" data-i18n="nav_videos">Videos</span>
    </button>

    <button type="button" id="nav-item-progress" class="bottom-nav-item" data-i18n-title="nav_progress_title" title="Progress & Analytics">
      <span class="nav-icon">📈</span>
      <span class="nav-label" data-i18n="nav_progress">Progress</span>
    </button>

    <button type="button" id="nav-item-more" class="bottom-nav-item" data-i18n-title="nav_more_title" title="More Options">
      <span class="nav-icon">☰</span>
      <span class="nav-label" data-i18n="nav_more">More</span>
    </button>
  </nav>

  <!-- Pushes the status panel to the bottom of the sidebar -->
  <div class="sidebar-spacer" aria-hidden="true"></div>

  <!-- System status panel -->
  <div class="sidebar-footer">
    <div class="sidebar-status-title" data-i18n="app_name">SYNAPSE AI</div>
    <div class="sidebar-status-row">
      <span class="sidebar-status-dot" aria-hidden="true"></span>
      <span data-i18n="sb_status_engine">Local Demo Engine Active</span>
    </div>
    <div class="sidebar-status-dim" data-i18n="sb_status_device">On-Device Personalized Learning</div>
    <div class="sidebar-status-dim" data-i18n="sb_status_arch">Architecture: Vanilla ES6 + PHP</div>
    <div class="sidebar-status-dim" data-i18n="sb_status_storage">Storage: Encrypted Local Profile</div>
  </div>
</aside>

<!-- Slide-Up More Menu Drawer -->
<div id="drawer-more-menu" class="modal-overlay hidden" role="dialog" aria-modal="true" aria-labelledby="drawer-more-title">
  <div class="more-drawer-card">
    <div class="more-drawer-header">
      <div style="display: flex; align-items: center; gap: 0.5rem;">
        <span style="font-size: 1.25rem;">✨</span>
        <h3 id="drawer-more-title" style="font-size: 1.1rem; font-weight: 700; color: var(--text-primary);" data-i18n="drawer_title">More Tools & Options</h3>
      </div>
      <button type="button" id="drawer-close-more" class="modal-close-btn" data-i18n-aria-label="drawer_close_aria" aria-label="Close menu">✕</button>
    </div>

    <div class="more-drawer-grid">
      <button type="button" id="more-item-recommendations" class="more-drawer-item">
        <span class="item-icon" style="background: rgba(6, 182, 212, 0.12); color: var(--color-cyan-light);">💡</span>
        <div class="item-text">
          <div class="item-title" data-i18n="drawer_recommendations_title">Recommendations</div>
          <div class="item-desc" data-i18n="drawer_recommendations_desc">Personalized next topics</div>
        </div>
      </button>

      <button type="button" id="more-item-notes" class="more-drawer-item">
        <span class="item-icon" style="background: rgba(16, 185, 129, 0.12); color: var(--color-emerald);">📝</span>
        <div class="item-text">
          <div class="item-title" data-i18n="drawer_notes_title">Notes Maker</div>
          <div class="item-desc" data-i18n="drawer_notes_desc">AI summary generator</div>
        </div>
      </button>

      <button type="button" id="more-item-level-test" class="more-drawer-item">
        <span class="item-icon" style="background: rgba(124, 58, 237, 0.12); color: var(--color-violet);">📊</span>
        <div class="item-text">
          <div class="item-title" data-i18n="drawer_level_title">Level Assessment</div>
          <div class="item-desc" data-i18n="drawer_level_desc">Diagnostic knowledge test</div>
        </div>
      </button>

      <button type="button" id="more-item-settings" class="more-drawer-item">
        <span class="item-icon" style="background: rgba(99, 102, 241, 0.12); color: var(--color-indigo-light);">⚙️</span>
        <div class="item-text">
          <div class="item-title" data-i18n="drawer_settings_title">Settings & Preferences</div>
          <div class="item-desc" data-i18n="drawer_settings_desc">Pace, goals & pathways</div>
        </div>
      </button>

      <button type="button" id="more-item-auth" class="more-drawer-item">
        <span class="item-icon" style="background: rgba(56, 189, 248, 0.12); color: var(--color-cyan);">🔑</span>
        <div class="item-text">
          <div class="item-title" id="more-auth-title" data-i18n="drawer_auth_title">Login / Register</div>
          <div class="item-desc" id="more-auth-desc" data-i18n="drawer_auth_desc">Optional learner account</div>
        </div>
      </button>

      <button type="button" id="more-item-lang" class="more-drawer-item">
        <span class="item-icon" style="background: rgba(245, 158, 11, 0.12); color: var(--color-warning);">🌐</span>
        <div class="item-text">
          <div class="item-title" data-i18n="drawer_lang_title">Language Switcher</div>
          <div class="item-desc" data-i18n="drawer_lang_desc">Change interface language</div>
        </div>
      </button>

      <button type="button" id="more-item-reset" class="more-drawer-item danger-item">
        <span class="item-icon" style="background: rgba(220, 38, 38, 0.12); color: var(--color-rose);">🔄</span>
        <div class="item-text">
          <div class="item-title" data-i18n="drawer_reset_title">Reset Workspace</div>
          <div class="item-desc" data-i18n="drawer_reset_desc">Clear local learning data</div>
        </div>
      </button>

      <button type="button" id="more-item-json" class="more-drawer-item">
        <span class="item-icon" style="background: rgba(100, 116, 139, 0.12); color: var(--text-muted);">{ }</span>
        <div class="item-text">
          <div class="item-title" data-i18n="drawer_json_title">Inspect Profile JSON</div>
          <div class="item-desc" data-i18n="drawer_json_desc">Debug architecture schema</div>
        </div>
      </button>
    </div>
  </div>
</div>

<script>
// Sidebar Navigation & More Drawer Wiring
document.addEventListener('DOMContentLoaded', () => {
  const bottomNav = document.getElementById('app-bottom-nav');
  const moreDrawer = document.getElementById('drawer-more-menu');
  const closeMoreBtn = document.getElementById('drawer-close-more');

  if (!bottomNav) return;

  const navItems = {
    'nav-item-home': 'hub',
    'nav-item-path': 'learner-type',
    'nav-item-explore-learning': 'explore-learning',
    'nav-item-practice': 'quiz-generator',
    'nav-item-ai': 'ai-mentor',
    'nav-item-explanations': 'youtube-explorer',
    'nav-item-progress': 'progress'
  };

  // Bind main nav buttons
  Object.keys(navItems).forEach(btnId => {
    const btn = document.getElementById(btnId);
    if (btn) {
      btn.addEventListener('click', () => {
        if (typeof Onboarding !== 'undefined') {
          Onboarding.goToStep(navItems[btnId]);
        }
      });
    }
  });



  // More menu drawer toggle
  const moreBtn = document.getElementById('nav-item-more');
  if (moreBtn && moreDrawer) {
    moreBtn.addEventListener('click', () => {
      moreDrawer.classList.remove('hidden');
    });
  }

  if (closeMoreBtn && moreDrawer) {
    closeMoreBtn.addEventListener('click', () => {
      moreDrawer.classList.add('hidden');
    });
  }

  // Close drawer when clicking outside card
  if (moreDrawer) {
    moreDrawer.addEventListener('click', (e) => {
      if (e.target === moreDrawer) {
        moreDrawer.classList.add('hidden');
      }
    });
  }

  // More drawer internal item bindings
  const bindMoreItem = (id, callback) => {
    const item = document.getElementById(id);
    if (item) {
      item.addEventListener('click', () => {
        if (moreDrawer) moreDrawer.classList.add('hidden');
        callback();
      });
    }
  };

  bindMoreItem('more-item-recommendations', () => {
    if (typeof Onboarding !== 'undefined') Onboarding.goToStep('hub');
    const recs = document.getElementById('dashboard-recommendations-list');
    if (recs) recs.scrollIntoView({ behavior: 'smooth' });
  });

  bindMoreItem('more-item-notes', () => {
    if (typeof Onboarding !== 'undefined') Onboarding.goToStep('notes-maker');
  });

  bindMoreItem('more-item-level-test', () => {
    if (typeof Onboarding !== 'undefined') Onboarding.goToStep('level-assessment');
  });

  bindMoreItem('more-item-settings', () => {
    if (typeof Onboarding !== 'undefined') Onboarding.goToStep('learning-preferences');
  });

  bindMoreItem('more-item-auth', () => {
    if (typeof Auth !== 'undefined') Auth.openLogin('hub');
  });

  bindMoreItem('more-item-lang', () => {
    if (typeof Onboarding !== 'undefined') Onboarding.openLanguageModal();
  });

  bindMoreItem('more-item-reset', () => {
    const confirmMsg = (typeof I18n !== 'undefined') ? I18n.t('drawer_reset_confirm') : 'Reset all demo workspace data and restart?';
    if (confirm(confirmMsg)) {
      if (typeof StorageService !== 'undefined') StorageService.resetAll();
      window.location.reload();
    }
  });

  bindMoreItem('more-item-json', () => {
    if (typeof Onboarding !== 'undefined') Onboarding.goToStep('hub');
    const jsonViewer = document.getElementById('hub-raw-json-viewer');
    if (jsonViewer) {
      jsonViewer.classList.remove('hidden');
      jsonViewer.scrollIntoView({ behavior: 'smooth' });
    }
  });

  // Highlight active sidebar nav item based on current step
  const updateActiveNavItem = (stepId) => {
    const items = bottomNav.querySelectorAll('.bottom-nav-item');
    items.forEach(item => item.classList.remove('active'));

    if (stepId === 'hub') {
      const home = document.getElementById('nav-item-home');
      if (home) home.classList.add('active');
    } else if (stepId === 'academic-selection' || stepId === 'learner-type') {
      const path = document.getElementById('nav-item-path');
      if (path) path.classList.add('active');
    } else if (stepId === 'explore-learning') {
      const explore = document.getElementById('nav-item-explore-learning');
      if (explore) explore.classList.add('active');
    } else if (stepId === 'quiz-generator') {
      const practice = document.getElementById('nav-item-practice');
      if (practice) practice.classList.add('active');
    } else if (stepId === 'ai-mentor') {
      const ai = document.getElementById('nav-item-ai');
      if (ai) ai.classList.add('active');
    } else if (stepId === 'youtube-explorer') {
      const exp = document.getElementById('nav-item-explanations');
      if (exp) exp.classList.add('active');
    } else if (stepId === 'progress') {
      const prog = document.getElementById('nav-item-progress');
      if (prog) prog.classList.add('active');
    }
  };

  // Hook into Onboarding.goToStep to keep the sidebar indicator in sync
  if (typeof Onboarding !== 'undefined') {
    const origGoToStep = Onboarding.goToStep.bind(Onboarding);
    Onboarding.goToStep = function(stepId, pushHistory = true) {
      origGoToStep(stepId, pushHistory);
      updateActiveNavItem(stepId);
    };
    // Initial highlight
    updateActiveNavItem(Onboarding.currentStep || 'hub');
  }
});
</script>
