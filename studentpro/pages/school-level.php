<?php
/**
 * View: Onboarding Step 3A - School Standards (1 - 12) & Streams
 */
?>
<div id="subview-school" class="academic-subview hidden">
  <div class="onboarding-screen-header">
    <h2 class="onboarding-screen-title" data-i18n="school_grade_title">Select Your School Standard</h2>
    <p class="onboarding-screen-subtitle" data-i18n="school_grade_subtitle">
      Choose your current grade level to load standard-appropriate subjects and board curriculum.
    </p>
  </div>

  <div class="app-card">
    <!-- Standards 1 to 12 Grid -->
    <div id="school-grades-grid" class="school-grades-grid">
      <!-- Injected by SchoolSubjects.renderGrades() -->
    </div>

    <!-- Standard 11 & 12 Stream Selection (Initially Hidden) -->
    <div id="school-stream-section" class="stream-selection-wrap hidden">
      <h3 class="stream-section-title" data-i18n="school_stream_title">Select Your Senior Stream</h3>
      <p class="stream-section-subtitle" data-i18n="school_stream_subtitle">Standard 11 and 12 curriculum is organized into specialized academic tracks:</p>

      <div id="school-stream-options" class="stream-cards-grid">
        <div class="stream-card" data-stream="science">
          <div class="stream-card-header">
            <span class="stream-icon">⚛️</span>
            <span class="stream-badge" data-i18n="school_stream_science_badge">STEM</span>
          </div>
          <h4 class="stream-name" data-i18n="school_stream_science_name">Science Stream</h4>
          <p class="stream-desc" data-i18n="school_stream_science_desc">Physics, Chemistry, Advanced Mathematics, Biology & Computing for engineering and medical tracks.</p>
        </div>

        <div class="stream-card" data-stream="commerce">
          <div class="stream-card-header">
            <span class="stream-icon">📈</span>
            <span class="stream-badge" data-i18n="school_stream_commerce_badge">FIN & BIZ</span>
          </div>
          <h4 class="stream-name" data-i18n="school_stream_commerce_name">Commerce Stream</h4>
          <p class="stream-desc" data-i18n="school_stream_commerce_desc">Accountancy, Business Studies, Economics, Statistics & Applied Math for corporate and finance tracks.</p>
        </div>

        <div class="stream-card" data-stream="arts">
          <div class="stream-card-header">
            <span class="stream-icon">🏛️</span>
            <span class="stream-badge" data-i18n="school_stream_arts_badge">HUMANITIES</span>
          </div>
          <h4 class="stream-name" data-i18n="school_stream_arts_name">Arts & Humanities</h4>
          <p class="stream-desc" data-i18n="school_stream_arts_desc">History, Political Science, Psychology, Sociology & Geography for civil services and law tracks.</p>
        </div>
      </div>
    </div>

    <!-- Subjects Section (Dynamically Loaded based on grade/stream) -->
    <div id="school-subject-section" class="hidden" style="margin-top: 2rem;">
      <h3 class="stream-section-title" data-i18n="school_subject_title">Select Your Focus Subject</h3>
      <p class="stream-section-subtitle" data-i18n="school_subject_subtitle">Pick the subject you would like to assess and personalize first:</p>
      
      <div id="school-subjects-list" class="subjects-list-grid">
        <!-- Injected dynamically -->
      </div>
    </div>

    <!-- Action Bar -->
    <div class="screen-actions-bar">
      <button type="button" id="btn-confirm-school-subject" class="btn btn-primary" disabled style="min-width: 180px;">
        <span data-i18n="btn_continue">Continue</span> →
      </button>
    </div>
  </div>
</div>
