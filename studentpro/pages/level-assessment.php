<?php
/**
 * View: Learning Level Assessment - Standalone Assessment Tool
 * Helps learners understand their current knowledge and identify weak areas
 */
?>
<section id="screen-level-assessment" class="app-screen">
  <div class="level-assessment-container">
    
    <!-- BEFORE STARTING: Intro Screen -->
    <div id="level-assessment-intro" class="level-assessment-intro hidden">
      <div class="level-assessment-hero">
        <div style="text-align: center; margin-bottom: 2rem;">
          <div style="font-size: 3.5rem; margin-bottom: 1rem;">📊</div>
          <h1 class="onboarding-screen-title" data-i18n="level_assessment_title">
            Learning Level Assessment
          </h1>
          <p class="onboarding-screen-subtitle" data-i18n="level_assessment_subtitle">
            Understand your current knowledge level and identify areas for growth
          </p>
        </div>

        <div class="level-assessment-info-box">
          <p data-i18n="level_assessment_description">
            This short assessment helps personalize your learning experience by understanding what you already know and where you need more practice.
          </p>
        </div>

        <!-- Assessment Details Card -->
        <div class="assessment-details-card">
          <div class="detail-item">
            <span class="detail-icon">❓</span>
            <div class="detail-content">
              <span class="detail-label" data-i18n="assessment_questions_label">Number of Questions</span>
              <span id="level-assessment-question-count" class="detail-value">8-10 questions</span>
            </div>
          </div>

          <div class="detail-item">
            <span class="detail-icon">⏱️</span>
            <div class="detail-content">
              <span class="detail-label" data-i18n="assessment_time_label">Estimated Time</span>
              <span class="detail-value" data-i18n="level_assessment_time_range">10-15 minutes</span>
            </div>
          </div>

          <div class="detail-item">
            <span class="detail-icon">📚</span>
            <div class="detail-content">
              <span class="detail-label" data-i18n="assessment_subject_label">Subject</span>
              <span id="level-assessment-subject-name" class="detail-value">Your Selected Subject</span>
            </div>
          </div>

          <div class="detail-item">
            <span class="detail-icon">✓</span>
            <div class="detail-content">
              <span class="detail-label" data-i18n="assessment_result_label">You'll Get</span>
              <span class="detail-value" data-i18n="assessment_result_value">Personalized insights & recommendations</span>
            </div>
          </div>
        </div>

        <!-- Start Button -->
        <div style="display: flex; gap: 1rem; margin-top: 2rem; flex-wrap: wrap; justify-content: center;">
          <button type="button" id="btn-start-level-assessment" class="btn btn-primary" style="padding: 1rem 2rem; font-size: 1rem;">
            ▶ <span data-i18n="level_assessment_btn_start_assessment">Start Assessment</span>
          </button>
          <button type="button" id="btn-cancel-level-assessment-intro" class="btn btn-secondary" style="padding: 1rem 2rem; font-size: 1rem;">
            ✕ <span data-i18n="btn_cancel">Cancel</span>
          </button>
        </div>
      </div>
    </div>

    <!-- DURING ASSESSMENT: Question Screen -->
    <div id="level-assessment-quiz" class="level-assessment-quiz hidden">
      <div class="assessment-container">
        
        <!-- Header Titles -->
        <div class="onboarding-screen-header" style="margin-bottom: 1.5rem;">
          <h2 class="onboarding-screen-title" data-i18n="level_assessment_quiz_title">
            Learning Level Assessment
          </h2>
          <p class="onboarding-screen-subtitle" data-i18n="level_assessment_quiz_subtitle">
            Answer each question to the best of your ability
          </p>
        </div>

        <!-- Assessment Card -->
        <div class="assessment-card">
          
          <!-- Top Meta Bar: Subject, Topic, Question Counter -->
          <div class="assessment-top-bar">
            <div class="assessment-tags-group">
              <span id="level-assessment-subject-tag" class="assessment-domain-tag">Subject</span>
              <span id="level-assessment-topic-tag" class="topic-pill">Topic</span>
              <span id="level-assessment-difficulty-badge" class="badge-difficulty diff-medium">Medium</span>
            </div>
            <div id="level-assessment-counter" class="assessment-counter-text">Question 1 of 10</div>
          </div>

          <!-- Linear Progress Bar -->
          <div class="assessment-progress-track">
            <div id="level-assessment-progress-bar" class="assessment-progress-fill" style="width: 10%;"></div>
          </div>

          <!-- Question Text -->
          <div id="level-assessment-question-text" class="assessment-question-text">
            Loading question...
          </div>

          <!-- Options Container -->
          <div id="level-assessment-options-container" class="assessment-options-list">
            <!-- Rendered dynamically -->
          </div>

          <!-- Question Navigation Dots -->
          <div id="level-assessment-dots-nav" class="assessment-dots-nav">
            <!-- Dot buttons injected dynamically -->
          </div>

          <!-- Navigation & Action Buttons -->
          <div class="screen-actions-bar" style="margin-top: 1.5rem; justify-content: space-between;">
            <button type="button" id="btn-level-assessment-prev" class="btn btn-secondary">
              ← <span data-i18n="btn_previous">Previous</span>
            </button>

            <div style="display: flex; gap: 0.75rem;">
              <button type="button" id="btn-level-assessment-next" class="btn btn-primary">
                <span data-i18n="btn_next">Next</span> →
              </button>
              <button type="button" id="btn-level-assessment-submit" class="btn btn-primary hidden" style="background: linear-gradient(135deg, var(--color-emerald) 0%, var(--color-indigo) 100%);">
                ✓ <span data-i18n="btn_submit_assessment">Submit & Analyze</span>
              </button>
            </div>
          </div>

        </div>
      </div>
    </div>

    <!-- AFTER COMPLETION: Results Screen -->
    <div id="level-assessment-results" class="level-assessment-results hidden">
      <div class="onboarding-screen-header">
        <h1 class="onboarding-screen-title" data-i18n="level_assessment_results_title">
          Your Assessment Results
        </h1>
        <p class="onboarding-screen-subtitle" data-i18n="level_assessment_results_subtitle">
          Here's your personalized analysis and recommended learning path
        </p>
      </div>

      <div style="max-width: 1000px; margin: 0 auto;">
        
        <!-- Score & Level Banner -->
        <div class="result-header-card">
          <div class="result-badge-row">
            <span id="level-assessment-result-level-badge" class="level-badge level-intermediate">Intermediate</span>
            <span id="level-assessment-result-score-badge" class="score-badge">65% • Competent</span>
          </div>

          <h2 data-i18n="level_assessment_analysis_title" style="font-size: 1.5rem; margin-bottom: 0.5rem; color: var(--text-primary);">
            Your Learning Level Analysis
          </h2>
          <p data-i18n="level_assessment_analysis_text" style="font-size: 0.95rem; color: var(--text-secondary); max-width: 720px; line-height: 1.5;">
            Based on your responses, we've identified your current knowledge level and areas for growth.
          </p>

          <!-- Key Metrics -->
          <div class="result-metrics-grid">
            <div class="result-metric-card">
              <div class="result-metric-label" data-i18n="score_label">Your Score</div>
              <div id="level-assessment-score-display" class="result-metric-value text-cyan">65%</div>
            </div>

            <div class="result-metric-card">
              <div class="result-metric-label" data-i18n="level_label">Current Level</div>
              <div id="level-assessment-level-display" class="result-metric-value text-violet">Intermediate</div>
            </div>

            <div class="result-metric-card">
              <div class="result-metric-label" data-i18n="level_assessment_questions_answered">Questions Answered</div>
              <div id="level-assessment-answered-display" class="result-metric-value text-emerald">8 / 10</div>
            </div>
          </div>
        </div>

        <!-- Topic Performance Breakdown -->
        <div class="areas-breakdown-row">
          <div class="areas-card">
            <h4 class="areas-title text-emerald">
              <span>✓</span> <span data-i18n="strong_areas_label">Strong Topics</span>
            </h4>
            <div id="level-assessment-strong-topics" class="areas-pills-wrap">
              <!-- Strong topic badges injected -->
            </div>
          </div>

          <div class="areas-card">
            <h4 class="areas-title text-warning">
              <span>⚠</span> <span data-i18n="focus_areas_label">Areas for Growth</span>
            </h4>
            <div id="level-assessment-focus-topics" class="areas-pills-wrap">
              <!-- Focus topic badges injected -->
            </div>
          </div>
        </div>

        <!-- Topic-wise Understanding Breakdown -->
        <div class="topic-meters-wrap">
          <h3 data-i18n="level_assessment_performance_header" style="font-size: 1.25rem; margin-bottom: 1rem;">Topic-Wise Performance</h3>
          <div id="level-assessment-topic-breakdown" class="topic-meters-grid">
            <!-- Rendered dynamically -->
          </div>
        </div>

        <!-- Recommendations -->
        <div class="app-card" style="background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.25);">
          <h4 style="font-size: 1.1rem; margin-bottom: 1rem; color: var(--color-emerald-light);">
            📚 <span data-i18n="recommendations_label">Recommendations</span>
          </h4>
          <ul id="level-assessment-recommendations" style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 0.75rem;">
            <li style="padding-left: 0;">
              <span style="margin-right: 0.5rem;">→</span>
              <span id="recommendation-1" style="color: var(--text-secondary);">Focus on strengthening your weak areas with targeted practice questions.</span>
            </li>
            <li style="padding-left: 0;">
              <span style="margin-right: 0.5rem;">→</span>
              <span id="recommendation-2" style="color: var(--text-secondary);">Review the explanations for incorrect answers to deepen understanding.</span>
            </li>
            <li style="padding-left: 0;">
              <span style="margin-right: 0.5rem;">→</span>
              <span id="recommendation-3" style="color: var(--text-secondary);">Take the assessment again after studying to track your progress.</span>
            </li>
          </ul>
        </div>

        <!-- Action Buttons -->
        <div class="app-card" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
          <div>
            <h4 data-i18n="level_assessment_ready_heading" style="font-size: 1rem; margin-bottom: 0.25rem;">Ready to continue your learning?</h4>
            <p data-i18n="level_assessment_ready_text" style="font-size: 0.85rem; color: var(--text-muted);">You can retake this assessment anytime to track your progress.</p>
          </div>
          <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <button type="button" id="btn-retake-level-assessment" class="btn btn-secondary">
              🔄 <span data-i18n="btn_retake_assessment">Retake Assessment</span>
            </button>
            <button type="button" id="btn-back-to-hub-from-assessment" class="btn btn-primary">
              ← <span data-i18n="level_assessment_btn_back_hub">Back to Hub</span>
            </button>
          </div>
        </div>

      </div>
    </div>

  </div>
</section>
