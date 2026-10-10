<?php
/**
 * View: AI Quiz Generator
 * Generates custom quizzes from topics or pasted study material with multiple difficulty levels.
 */
?>
<section id="screen-quiz-generator" class="app-screen hidden">
  <div style="max-width: 900px; margin: 0 auto; padding: 1rem 0;">

    <!-- Header Banner -->
    <div class="hub-hero-card" style="margin-bottom: 1.5rem;">
      <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
          <span class="badge-popular" style="margin-bottom: 0.4rem; display: inline-block;" data-i18n="quiz_feature_badge">⚡ Feature 5: AI Engine</span>
          <h1 style="font-size: 1.6rem; font-weight: 700; color: var(--text-primary);" data-i18n="quiz_title">AI Quiz Generator</h1>
          <p style="font-size: 0.9rem; color: var(--text-muted);" data-i18n="quiz_subtitle">
            Generate custom interactive practice quizzes from any topic name or study text.
          </p>
        </div>
        <button type="button" class="btn btn-secondary btn-back-to-hub">
          ← <span data-i18n="btn_back_to_dashboard">Back to Dashboard</span>
        </button>
      </div>
    </div>

    <!-- Generator Setup Card -->
    <div id="quiz-gen-setup-card" class="hub-hero-card">
      <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 1rem; color: var(--color-cyan);" data-i18n="quiz_configure_heading">
        ⚙️ Configure Quiz Parameters
      </h3>
      <p id="quiz-gen-source-notice" class="quiz-gen-source-notice" data-i18n="quiz_source_checking">
        Checking the local Ollama model...
      </p>

      <form id="form-quiz-generator">
        <div class="form-group">
          <label class="form-label" data-i18n="quiz_label_topic">Topic Name or Concept</label>
          <input type="text" id="quiz-gen-topic" class="input-text" data-i18n-placeholder="quiz_placeholder_topic" placeholder="e.g. Linear Equations, Photosynthesis, Python Loops, Financial Accounting" required />
        </div>

        <div class="form-group">
          <label class="form-label" data-i18n="quiz_label_study_text">Optional Study Text / Notes Excerpt</label>
          <textarea id="quiz-gen-text" class="input-text" rows="3" maxlength="6000" data-i18n-placeholder="quiz_placeholder_study_text" placeholder="Optional: paste up to 6,000 characters of study notes for local quiz generation..."></textarea>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1rem;">
          <div class="form-group">
            <label class="form-label" data-i18n="quiz_label_difficulty">Difficulty Level</label>
            <select id="quiz-gen-difficulty" class="input-text">
              <option value="Beginner" data-i18n="quiz_option_beginner">Beginner (Fundamentals)</option>
              <option value="Intermediate" selected data-i18n="quiz_option_intermediate">Intermediate (Standard)</option>
              <option value="Advanced" data-i18n="quiz_option_advanced">Advanced (High Challenge)</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label" data-i18n="quiz_label_question_count">Number of Questions</label>
            <select id="quiz-gen-count" class="input-text">
              <option value="3" data-i18n="quiz_option_count_3">3 Questions (Quick Practice)</option>
              <option value="5" selected data-i18n="quiz_option_count_5">5 Questions (Standard)</option>
              <option value="10" data-i18n="quiz_option_count_10">10 Questions (Mastery Evaluation)</option>
            </select>
          </div>
        </div>

        <button type="submit" id="btn-generate-quiz-submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;" data-i18n="quiz_btn_generate">
          ✨ Generate Quiz Now
        </button>
        <p id="quiz-gen-setup-message" class="quiz-gen-message hidden" role="status" aria-live="polite"></p>
      </form>
    </div>

    <!-- Active Quiz Execution Container -->
    <div id="quiz-gen-active-card" class="hub-hero-card hidden">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <span id="quiz-gen-active-topic" class="badge-popular">Topic: Practice</span>
        <span id="quiz-gen-model-badge" class="badge-popular quiz-gen-model-badge hidden"></span>
        <span id="quiz-gen-counter" style="font-weight: 600; color: var(--color-cyan-light);">Question 1 of 5</span>
      </div>

      <div class="quiz-gen-progress-track" role="progressbar" aria-label="Quiz progress" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
        <div id="quiz-gen-progress-bar" class="quiz-gen-progress-fill"></div>
      </div>

      <h3 id="quiz-gen-question-text" style="font-size: 1.15rem; font-weight: 600; color: var(--text-primary); margin-bottom: 1.25rem;">
        Loading question...
      </h3>

      <div id="quiz-gen-options-container" class="quiz-gen-options">
        <!-- Injected dynamically -->
      </div>

      <div id="quiz-gen-explanation" class="quiz-gen-feedback hidden" role="status" aria-live="polite">
        <!-- Explanation injected -->
      </div>

      <div class="quiz-gen-actions">
        <button type="button" id="btn-quiz-gen-prev" class="btn btn-secondary" disabled>
          ← <span data-i18n="btn_previous">Previous</span>
        </button>
        <div class="quiz-gen-actions-next">
          <button type="button" id="btn-quiz-gen-submit-answer" class="btn btn-primary" disabled data-i18n="quiz_submit_answer">
            Submit Answer
          </button>
          <button type="button" id="btn-quiz-gen-next" class="btn btn-primary hidden">
            Next Question →
          </button>
        </div>
      </div>
    </div>

    <!-- Quiz Results Card -->
    <div id="quiz-gen-results-card" class="hub-hero-card hidden text-center" style="padding: 2rem;">
      <div style="font-size: 3rem; margin-bottom: 0.5rem;">🏆</div>
      <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary);" data-i18n="quiz_completed_title">Quiz Completed!</h2>
      <div id="quiz-gen-score-display" style="font-size: 2rem; font-weight: 800; color: var(--color-emerald); margin: 0.75rem 0;">
        0 / 0 (0%)
      </div>
      <p id="quiz-gen-result-counts" class="quiz-gen-result-counts"></p>
      <p id="quiz-gen-reward-text" style="color: var(--color-cyan); margin-bottom: 1.5rem;">+50 XP Earned!</p>

      <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
        <button type="button" id="btn-quiz-gen-retry" class="btn btn-secondary" data-i18n="quiz_btn_generate_another">
          🔄 Generate Another Quiz
        </button>
        <button type="button" class="btn btn-primary btn-back-to-hub" data-i18n="btn_return_to_dashboard">
          📊 Return to Dashboard
        </button>
      </div>
    </div>

  </div>
</section>
