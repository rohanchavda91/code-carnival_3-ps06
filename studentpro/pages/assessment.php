<?php
/**
 * View: Onboarding Step 5 - Knowledge & Learning Level Assessment
 */
?>
<section id="screen-assessment" class="app-screen">
  <div class="assessment-container">
    
    <!-- Header Titles -->
    <div class="onboarding-screen-header" style="margin-bottom: 1.5rem;">
      <h1 class="onboarding-screen-title" data-i18n="assessment_title">Knowledge & Learning Assessment</h1>
      <p class="onboarding-screen-subtitle" data-i18n="assessment_subtitle">
        Diagnostic evaluation to benchmark your current conceptual grasp and calibrate difficulty.
      </p>
    </div>

    <!-- Active Assessment Card -->
    <div class="assessment-card">
      
      <!-- Top Meta Bar: Domain, Tags, Question Counter -->
      <div class="assessment-top-bar">
        <div class="assessment-tags-group">
          <span id="assessment-domain-tag" class="assessment-domain-tag">Mathematics</span>
          <span id="assessment-topic-tag" class="topic-pill">Algebra Basics</span>
          <span id="assessment-difficulty-badge" class="badge-difficulty diff-medium">Medium</span>
        </div>
        <div id="assessment-question-counter" class="assessment-counter-text">Question 1 of 5</div>
      </div>

      <!-- Linear Progress Fill -->
      <div class="assessment-progress-track">
        <div id="assessment-progress-bar" class="assessment-progress-fill" style="width: 20%;"></div>
      </div>

      <!-- Question Text -->
      <div id="assessment-question-text" class="assessment-question-text">
        Loading diagnostic question...
      </div>

      <!-- Options Container -->
      <div id="assessment-options-container" class="assessment-options-list">
        <!-- Rendered dynamically by Assessment.renderCurrentQuestion() -->
      </div>

      <!-- Question Jump Dots -->
      <div id="assessment-dots-nav" class="assessment-dots-nav">
        <!-- Dot buttons injected dynamically -->
      </div>

      <!-- Navigation & Action Buttons -->
      <div class="screen-actions-bar" style="margin-top: 1.5rem; justify-content: space-between;">
        <button type="button" id="btn-assessment-prev" class="btn btn-secondary">
          ← <span data-i18n="btn_previous">Previous</span>
        </button>

        <div class="assessment-navigation-actions">
          <button type="button" id="btn-assessment-check" class="btn btn-secondary" disabled>
            <span data-i18n="assessment_check_answer">Check answer</span>
          </button>
          <button type="button" id="btn-assessment-next" class="btn btn-primary">
            <span data-i18n="assessment_next_question">Next Question</span> →
          </button>
          <button type="button" id="btn-assessment-submit" class="btn btn-primary hidden" style="background: linear-gradient(135deg, var(--color-emerald) 0%, var(--color-indigo) 100%);">
            ✓ <span data-i18n="btn_submit_assessment">Submit & Analyze Results</span>
          </button>
        </div>
      </div>

    </div>
  </div>
</section>
