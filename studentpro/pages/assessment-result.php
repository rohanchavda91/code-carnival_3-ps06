<?php
/**
 * View: Onboarding Step 6 - Diagnostic Result & Tailored Curriculum Synthesis
 */
?>
<section id="screen-result" class="app-screen">
  <div class="onboarding-screen-header">
    <h1 class="onboarding-screen-title" data-i18n="result_title">Your Personalized Learning Path is Ready!</h1>
    <p class="onboarding-screen-subtitle" data-i18n="result_subtitle">
      Our cognitive engine has analyzed your responses and synthesized a customized roadmap.
    </p>
  </div>

  <div style="max-width: 1000px; margin: 0 auto;">
    
    <!-- Hero Analytics Result Banner -->
    <div class="result-header-card">
      <div class="result-badge-row">
        <span id="result-level-badge" class="level-badge level-intermediate">Intermediate</span>
        <span id="result-score-badge" class="score-badge">80% Proficiency • Demo Diagnostic Score</span>
      </div>
      <p id="result-answer-summary" class="result-answer-summary" role="status"></p>

      <h2 data-i18n="result_calibration_title" style="font-size: 1.5rem; margin-bottom: 0.5rem; color: var(--text-primary);">
        Diagnostic Learning Calibration
      </h2>
      <p data-i18n="result_calibration_text" style="font-size: 0.95rem; color: var(--text-secondary); max-width: 720px; line-height: 1.5;">
        Your cognitive profile demonstrates strong foundational reasoning with immediate opportunity for high-leverage growth in core applied topics.
      </p>

      <!-- Key Metrics -->
      <div class="result-metrics-grid">
        <div class="result-metric-card">
          <div class="result-metric-label" data-i18n="rec_topic_label">Recommended Starting Topic</div>
          <div id="result-rec-topic" class="result-metric-value text-cyan">Linear Equations</div>
        </div>

        <div class="result-metric-card">
          <div class="result-metric-label" data-i18n="rec_difficulty_label">Calibrated Difficulty</div>
          <div id="result-rec-difficulty" class="result-metric-value text-violet">Standard Intermediate</div>
        </div>

        <div class="result-metric-card">
          <div class="result-metric-label" data-i18n="rec_time_label">Recommended Daily Time</div>
          <div id="result-rec-time" class="result-metric-value text-emerald">30 mins / day</div>
        </div>
      </div>
    </div>

    <!-- Strong Areas vs Focus Areas -->
    <div class="areas-breakdown-row">
      <div class="areas-card">
        <h4 class="areas-title text-emerald">
          <span>✓</span> <span data-i18n="strong_areas_label">Strongest Conceptual Areas</span>
        </h4>
        <div id="result-strong-areas-list" class="areas-pills-wrap">
          <!-- Strong badges injected -->
        </div>
      </div>

      <div class="areas-card">
        <h4 class="areas-title text-warning">
          <span>▲</span> <span data-i18n="focus_areas_label">Focus & Growth Areas</span>
        </h4>
        <div id="result-focus-areas-list" class="areas-pills-wrap">
          <!-- Focus badges injected -->
        </div>
      </div>
    </div>

    <!-- Topic-wise Understanding Meters -->
    <div class="topic-meters-wrap">
      <h3 data-i18n="result_topic_breakdown_heading" style="font-size: 1.25rem; margin-bottom: 1rem;">Topic-Wise Understanding Breakdown</h3>
      <div id="result-topic-breakdown-grid" class="topic-meters-grid">
        <!-- Rendered dynamically -->
      </div>
    </div>

    <!-- Dynamic Tailored Curriculum Roadmap Modules -->
    <div class="roadmap-section" style="margin-bottom: 2.5rem;">
      <h3 class="roadmap-section-title" data-i18n="roadmap_heading">Your Personalized Learning Modules</h3>
      <p class="roadmap-section-subtitle" data-i18n="result_roadmap_subtitle">
        Custom-sequenced modules calibrated to your knowledge baseline and learning goals:
      </p>

      <div id="result-roadmap-container" class="roadmap-grid">
        <!-- Injected dynamically by Personalization.renderResult() -->
      </div>
    </div>

    <!-- Start Action Bar -->
    <div class="app-card" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
      <div>
        <h4 data-i18n="result_ready_heading" style="font-size: 1.1rem; margin-bottom: 0.25rem;">Ready to begin your adaptive learning journey?</h4>
        <p data-i18n="result_ready_text" style="font-size: 0.85rem; color: var(--text-muted);">You can recalibrate your assessment or adjust your goals anytime from your hub.</p>
      </div>
      <div style="display: flex; gap: 1rem;">
        <button type="button" id="btn-retake-assessment-action" class="btn btn-secondary">
          🔄 <span data-i18n="btn_retake_assessment">Retake Assessment</span>
        </button>
        <button type="button" id="btn-start-learning-path" class="btn btn-primary" style="padding: 0.85rem 1.75rem;">
          🚀 <span data-i18n="btn_start_learning">Start My Learning Path</span>
        </button>
      </div>
    </div>

  </div>
</section>
