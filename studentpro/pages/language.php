<?php
/**
 * View: Onboarding Step 1 - Interface Language Selection
 */
?>
<section id="screen-language" class="app-screen">
  <div class="onboarding-screen-header">
    <h1 class="onboarding-screen-title" data-i18n="lang_step_title">Which language would you like to learn in?</h1>
    <p class="onboarding-screen-subtitle" data-i18n="lang_step_subtitle">
      Select the primary interface language for your entire learning environment. You can switch this at any time.
    </p>
  </div>

  <div class="app-card language-selection-panel">
    <!-- Search Bar -->
    <div class="search-bar-wrap">
      <span class="search-icon" aria-hidden="true">🔍</span>
      <input type="text" id="lang-search-input" class="search-bar-input" data-i18n-placeholder="lang_search_placeholder" placeholder="Search English, Hindi, or Gujarati..." data-i18n-aria-label="lang_search_aria" aria-label="Search languages">
    </div>

    <!-- Language Grid -->
    <div id="language-grid" class="language-grid" role="group" data-i18n-aria-label="lang_selector_aria" aria-label="Choose an interface language">
      <!-- Injected dynamically by Onboarding.renderLanguageCards() -->
    </div>

    <!-- Action Bar -->
    <div class="screen-actions-bar">
      <button type="button" id="btn-confirm-language" class="btn btn-primary" style="min-width: 180px;">
        <span data-i18n="btn_continue">Continue</span> →
      </button>
    </div>
  </div>
</section>
