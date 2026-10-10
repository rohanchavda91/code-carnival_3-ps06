<?php
/**
 * View: Smart YouTube Educational Video Explorer
 * Allows searching educational video content with safe YouTube search link fallbacks.
 */
?>
<section id="screen-youtube-explorer" class="app-screen hidden">
  <div style="max-width: 960px; margin: 0 auto; padding: 1rem 0;">

    <!-- Banner -->
    <div class="hub-hero-card" style="margin-bottom: 1.5rem;">
      <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
          <span class="badge-popular" style="margin-bottom: 0.4rem; display: inline-block;" data-i18n="yt_feature_badge">▶ Feature 8: Video Explorer</span>
          <h1 style="font-size: 1.6rem; font-weight: 700; color: var(--text-primary);" data-i18n="yt_title">Smart Video Search</h1>
          <p style="font-size: 0.9rem; color: var(--text-muted);" data-i18n="yt_subtitle">
            Find curated educational video tutorials and visual explanations for any concept.
          </p>
        </div>
        <button type="button" class="btn btn-secondary btn-back-to-hub">
          ← <span data-i18n="btn_back_to_dashboard">Back to Dashboard</span>
        </button>
      </div>
    </div>

    <!-- Search Form -->
    <div class="hub-hero-card" style="margin-bottom: 1.5rem;">
      <form id="form-youtube-search" style="display: flex; gap: 0.75rem;">
        <input type="text" id="youtube-search-input" class="input-text" style="flex: 1;" data-i18n-placeholder="yt_search_placeholder" placeholder="Search topic (e.g. Calculus Derivatives, Organic Chemistry, Python Functions)" required />
        <button type="submit" class="btn btn-primary" data-i18n="yt_btn_search">
          🔍 Search Videos
        </button>
      </form>
    </div>

    <!-- Video Results Grid -->
    <div id="youtube-results-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem;">
      <!-- Injected dynamically -->
    </div>

  </div>
</section>
