<?php
/**
 * View: AI Mistake-to-Mastery Review Loop
 * Tracks incorrect questions, provides explanations, and re-tests for topic mastery.
 */
?>
<section id="screen-mistake-mastery" class="app-screen hidden">
  <div style="max-width: 900px; margin: 0 auto; padding: 1rem 0;">

    <!-- Banner -->
    <div class="hub-hero-card" style="margin-bottom: 1.5rem;">
      <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
          <span class="badge-popular" style="margin-bottom: 0.4rem; display: inline-block;" data-i18n="mistake_feature_badge">🔁 Feature 11: Mistake Loop</span>
          <h1 style="font-size: 1.6rem; font-weight: 700; color: var(--text-primary);" data-i18n="mistake_title">Mistake-to-Mastery Hub</h1>
          <p style="font-size: 0.9rem; color: var(--text-muted);" data-i18n="mistake_subtitle">
            Turn missed quiz questions into learning wins through guided re-testing and explanations.
          </p>
        </div>
        <button type="button" class="btn btn-secondary btn-back-to-hub">
          ← <span data-i18n="btn_back_to_dashboard">Back to Dashboard</span>
        </button>
      </div>
    </div>

    <!-- Mistakes List Container -->
    <div class="hub-hero-card">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h3 style="font-size: 1.15rem; font-weight: 600; color: var(--color-cyan);">
          🎯 <span data-i18n="mistake_heading">Flagged Questions &amp; Review Topics</span>
        </h3>
        <span id="mistake-mastery-count" class="level-badge level-intermediate">0 Items</span>
      </div>

      <div id="mistake-mastery-list" style="display: flex; flex-direction: column; gap: 1rem;">
        <!-- Injected dynamically -->
      </div>
    </div>

  </div>
</section>
