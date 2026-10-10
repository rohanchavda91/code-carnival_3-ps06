<?php
/**
 * View: Onboarding Step 4 - Learning Goals, Cognitive Style & Daily Time Commitment
 */
?>
<section id="screen-learning-preferences" class="app-screen">
  <div class="onboarding-screen-header">
    <h1 class="onboarding-screen-title" data-i18n="prefs_title">Personalize Your Learning Strategy</h1>
    <p class="onboarding-screen-subtitle" data-i18n="prefs_subtitle">
      Fine-tune how your AI curriculum adapts to your schedule, cognitive preferences, and goals.
    </p>
  </div>

  <div class="app-card" style="max-width: 960px; margin: 0 auto;">
    <form id="form-learning-preferences">

      <!-- SECTION 1: PRIMARY GOAL (MULTI-SELECT) -->
      <div class="pref-section">
        <h3 class="pref-heading" data-i18n="goal_heading">What is your primary goal?</h3>
        <p class="pref-subheading" data-i18n="goal_subheading">Select all that apply to your current learning focus:</p>

        <div class="pref-cards-grid">
          <label class="pref-selectable-card">
            <input type="checkbox" name="learning_goals" value="exams" class="sr-only">
            <span class="pref-card-icon">📝</span>
            <div class="pref-card-body">
              <span class="pref-card-title" data-i18n="goal_exams">Prepare for School / University Exams</span>
            </div>
          </label>

          <label class="pref-selectable-card">
            <input type="checkbox" name="learning_goals" value="grades" class="sr-only">
            <span class="pref-card-icon">📈</span>
            <div class="pref-card-body">
              <span class="pref-card-title" data-i18n="goal_grades">Improve Grades & Academic Rank</span>
            </div>
          </label>

          <label class="pref-selectable-card">
            <input type="checkbox" name="learning_goals" value="concepts" class="sr-only" checked>
            <span class="pref-card-icon">💡</span>
            <div class="pref-card-body">
              <span class="pref-card-title" data-i18n="goal_concepts">Deeply Understand Core Concepts</span>
            </div>
          </label>

          <label class="pref-selectable-card">
            <input type="checkbox" name="learning_goals" value="career" class="sr-only">
            <span class="pref-card-icon">💼</span>
            <div class="pref-card-body">
              <span class="pref-card-title" data-i18n="goal_career">Build Job-Ready Career Skills</span>
            </div>
          </label>

          <label class="pref-selectable-card">
            <input type="checkbox" name="learning_goals" value="new_skill" class="sr-only">
            <span class="pref-card-icon">🛠️</span>
            <div class="pref-card-body">
              <span class="pref-card-title" data-i18n="goal_new_skill">Learn a Brand New Skill from Scratch</span>
            </div>
          </label>

          <label class="pref-selectable-card">
            <input type="checkbox" name="learning_goals" value="language" class="sr-only">
            <span class="pref-card-icon">🗣️</span>
            <div class="pref-card-body">
              <span class="pref-card-title" data-i18n="goal_language">Master a New Spoken Language</span>
            </div>
          </label>

          <label class="pref-selectable-card">
            <input type="checkbox" name="learning_goals" value="competitive" class="sr-only">
            <span class="pref-card-icon">🏆</span>
            <div class="pref-card-body">
              <span class="pref-card-title" data-i18n="goal_competitive">Crack Competitive Entrance Exams</span>
            </div>
          </label>

          <label class="pref-selectable-card">
            <input type="checkbox" name="learning_goals" value="explore" class="sr-only">
            <span class="pref-card-icon">🧭</span>
            <div class="pref-card-body">
              <span class="pref-card-title" data-i18n="goal_explore">Curious Exploration & Intellectual Growth</span>
            </div>
          </label>
        </div>
      </div>

      <!-- SECTION 2: LEARNING STYLE (SINGLE-SELECT) -->
      <div class="pref-section">
        <h3 class="pref-heading" data-i18n="style_heading">How do you learn best?</h3>
        <p class="pref-subheading" data-i18n="style_subheading">Select your preferred cognitive instruction method:</p>

        <div class="pref-cards-grid">
          <label class="pref-selectable-card">
            <input type="radio" name="learning_style" value="step_by_step" class="sr-only" checked>
            <span class="pref-card-icon">🪜</span>
            <div class="pref-card-body">
              <span class="pref-card-title" data-i18n="style_step_by_step">Step-by-step conceptual breakdowns</span>
            </div>
          </label>

          <label class="pref-selectable-card">
            <input type="radio" name="learning_style" value="examples" class="sr-only">
            <span class="pref-card-icon">🌐</span>
            <div class="pref-card-body">
              <span class="pref-card-title" data-i18n="style_examples">Real-world examples & analogies</span>
            </div>
          </label>

          <label class="pref-selectable-card">
            <input type="radio" name="learning_style" value="practice" class="sr-only">
            <span class="pref-card-icon">✏️</span>
            <div class="pref-card-body">
              <span class="pref-card-title" data-i18n="style_practice">Practice questions & problem sets</span>
            </div>
          </label>

          <label class="pref-selectable-card">
            <input type="radio" name="learning_style" value="visual" class="sr-only">
            <span class="pref-card-icon">📊</span>
            <div class="pref-card-body">
              <span class="pref-card-title" data-i18n="style_visual">Visual diagrams, flowcharts & mind maps</span>
            </div>
          </label>

          <label class="pref-selectable-card">
            <input type="radio" name="learning_style" value="challenges" class="sr-only">
            <span class="pref-card-icon">🎯</span>
            <div class="pref-card-body">
              <span class="pref-card-title" data-i18n="style_challenges">Project challenges & hands-on exercises</span>
            </div>
          </label>

          <label class="pref-selectable-card">
            <input type="radio" name="learning_style" value="quick" class="sr-only">
            <span class="pref-card-icon">⚡</span>
            <div class="pref-card-body">
              <span class="pref-card-title" data-i18n="style_quick">Quick bite-sized summaries & speed drills</span>
            </div>
          </label>
        </div>
      </div>

      <!-- SECTION 3: DAILY TIME COMMITMENT -->
      <div class="pref-section" style="margin-bottom: 1.5rem;">
        <h3 class="pref-heading" data-i18n="time_heading">How much time can you learn each day?</h3>
        <p class="pref-subheading" data-i18n="time_subheading">Consistency is key to mastery. Choose a realistic daily target:</p>

        <div class="time-cards-grid">
          <label class="time-card">
            <input type="radio" name="daily_time" value="10m" class="sr-only">
            <span class="time-duration" data-i18n="pref_duration_10m">10 min</span>
            <span class="time-label" data-i18n="time_10m">Casual Pace</span>
          </label>

          <label class="time-card">
            <input type="radio" name="daily_time" value="20m" class="sr-only">
            <span class="time-duration" data-i18n="pref_duration_20m">20 min</span>
            <span class="time-label" data-i18n="time_20m">Steady Habit</span>
          </label>

          <label class="time-card">
            <input type="radio" name="daily_time" value="30m" class="sr-only" checked>
            <span class="time-duration text-cyan" data-i18n="pref_duration_30m">30 min</span>
            <span class="time-label text-cyan" data-i18n="time_30m">★ Recommended</span>
          </label>

          <label class="time-card">
            <input type="radio" name="daily_time" value="45m" class="sr-only">
            <span class="time-duration" data-i18n="pref_duration_45m">45 min</span>
            <span class="time-label" data-i18n="time_45m">Deep Learning</span>
          </label>

          <label class="time-card">
            <input type="radio" name="daily_time" value="1h" class="sr-only">
            <span class="time-duration" data-i18n="pref_duration_1h">1 hour</span>
            <span class="time-label" data-i18n="time_1h">Intensive Mastery</span>
          </label>

          <label class="time-card">
            <input type="radio" name="daily_time" value="2h" class="sr-only">
            <span class="time-duration" data-i18n="pref_duration_2h">2+ hours</span>
            <span class="time-label" data-i18n="time_2h">Fast-track Sprint</span>
          </label>
        </div>
      </div>

      <!-- Form Submit Action -->
      <div class="screen-actions-bar">
        <button type="submit" id="btn-submit-preferences" class="btn btn-primary" style="min-width: 220px;">
          <span data-i18n="btn_continue">Continue to Level Assessment</span> →
        </button>
      </div>

    </form>
  </div>
</section>
