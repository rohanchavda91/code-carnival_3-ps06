/**
 * Main Application Bootstrap
 * Initializes subsystems, loads saved sessions, and orchestrates page view routing.
 */
document.addEventListener('DOMContentLoaded', () => {
  // Initialize the shared appearance toggle before application UI is used.
  const themeToggle = document.getElementById('header-theme-toggle');
  const themeIcon = document.getElementById('header-theme-icon');
  const applyTheme = (theme) => {
    const isDark = theme === 'dark';
    document.documentElement.setAttribute('data-theme', isDark ? 'dark' : 'light');
    if (themeToggle) {
      themeToggle.setAttribute('aria-pressed', String(isDark));
      const ariaKey = isDark ? 'hdr_theme_to_light' : 'hdr_theme_to_dark';
      const ariaFallback = `Switch to ${isDark ? 'light' : 'dark'} mode`;
      themeToggle.setAttribute('aria-label', (typeof I18n !== 'undefined') ? I18n.t(ariaKey, {}, ariaFallback) : ariaFallback);
    }
    if (themeIcon) themeIcon.textContent = isDark ? '☀️' : '🌙';
  };

  let savedTheme = 'light';
  try {
    savedTheme = localStorage.getItem('synapse_theme') === 'dark' ? 'dark' : 'light';
  } catch (error) {
    console.warn('Theme preference read failed', error);
  }
  applyTheme(savedTheme);

  if (themeToggle) {
    themeToggle.addEventListener('click', () => {
      const nextTheme = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
      applyTheme(nextTheme);
      try {
        localStorage.setItem('synapse_theme', nextTheme);
      } catch (error) {
        console.warn('Theme preference write failed', error);
      }
    });
  }

  // Keep the theme toggle aria-label localized when the interface language changes.
  window.addEventListener('synapse:language_changed', () => {
    applyTheme(document.documentElement.getAttribute('data-theme') === 'dark' ? 'dark' : 'light');
  });

  // 1. Load saved profile & language
  const urlParams = new URLSearchParams(window.location.search);
  const langParam = urlParams.get('lang');
  const savedProfile = StorageService.getProfile();
  const isSupportedLanguage = (code) => SUPPORTED_LANGUAGES.some(lang => lang.code === code);
  const initialLang = isSupportedLanguage(langParam)
    ? langParam
    : (isSupportedLanguage(savedProfile.interfaceLanguage) ? savedProfile.interfaceLanguage : 'en');
  I18n.init(initialLang);

  // 2. Initialize Subsystems
  if (typeof LocalAIEngine !== 'undefined') LocalAIEngine.init();
  Auth.init();
  Onboarding.init();
  CoursesAndSkills.init();
  Assessment.init();
  LevelAssessment.init();
  Personalization.init();
  LearnerProfile.init();
  ExploreLearning.init();
  AiMentor.init();
  if (typeof QuizGenerator !== 'undefined') QuizGenerator.init();
  if (typeof MistakeMastery !== 'undefined') MistakeMastery.init();
  if (typeof YoutubeExplorer !== 'undefined') YoutubeExplorer.init();
  if (typeof NotesMaker !== 'undefined') NotesMaker.init();
  if (typeof GuestLimits !== 'undefined') GuestLimits.init();

  // 3. Determine initial screen based on session & state (Guest Mode default)
  const isAuth = StorageService.isAuthenticated();
  const stepParam = urlParams.get('step');

  if (!savedProfile.name) {
    StorageService.saveProfile({ name: 'Guest Student' });
  }

  if (stepParam && document.getElementById(`screen-${stepParam}`)) {
    if (stepParam === 'academic-selection') {
      const typeParam = urlParams.get('type');
      const type = typeParam || savedProfile.learnerType || 'school';
      Onboarding.selectLearnerType(type);
    } else {
      Onboarding.goToStep(stepParam, false);
    }
  } else {
    // Website open hote hi Home Page (hub) aaye
    Onboarding.goToStep('hub', false);
  }

  // 4. Header Reset / Logout Actions
  const btnHeaderReset = document.getElementById('header-btn-reset');
  if (btnHeaderReset) {
    btnHeaderReset.addEventListener('click', () => {
      const confirmMsg = (typeof I18n !== 'undefined')
        ? I18n.t('hdr_reset_confirm', {}, 'Reset demo state and restart onboarding?')
        : 'Reset demo state and restart onboarding?';
      if (confirm(confirmMsg)) {
        StorageService.resetAll();
        window.location.href = window.location.pathname;
      }
    });
  }

  const btnHeaderLogout = document.getElementById('header-btn-logout');
  if (btnHeaderLogout) {
    btnHeaderLogout.addEventListener('click', () => {
      Auth.logout();
    });
  }

  // 5. PWA: register the offline service worker (HTTPS / localhost only)
  if ('serviceWorker' in navigator && (window.location.protocol === 'https:' || ['localhost', '127.0.0.1'].includes(window.location.hostname))) {
    window.addEventListener('load', () => {
      navigator.serviceWorker.register('./sw.js').catch((err) => {
        console.warn('Service worker registration failed', err);
      });
    });
  }

  console.log('🚀 Synapse AI Platform initialized successfully in production-style mode.');
});
