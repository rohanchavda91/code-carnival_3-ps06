<?php
/**
 * View: Onboarding Step 3B - College & Higher Education Courses
 */
?>
<div id="subview-college" class="academic-subview hidden">
  <div class="onboarding-screen-header">
    <h2 class="onboarding-screen-title" data-i18n="college_title">Explore College & University Courses</h2>
    <p class="onboarding-screen-subtitle" data-i18n="college_subtitle">
      Search across degree levels and specialized faculties to customize your syllabus.
    </p>
  </div>

  <div class="app-card">
    <!-- Search Input -->
    <div class="search-bar-wrap">
      <span class="search-icon" aria-hidden="true">🔍</span>
      <input type="text" id="college-search-input" class="search-bar-input" data-i18n-placeholder="college_search_placeholder" placeholder="Search courses (e.g. BCA, Data Science, MBA, B.Tech, Psychology...)" data-i18n-aria-label="college_search_aria" aria-label="Search college courses">
    </div>

    <!-- Degree Level Filters -->
    <div id="college-level-filters" class="filter-pills-row">
      <button type="button" class="filter-pill active" data-level="all" data-i18n="college_level_all">All Degree Levels</button>
      <button type="button" class="filter-pill" data-level="ug" data-i18n="college_level_ug">Undergraduate (UG)</button>
      <button type="button" class="filter-pill" data-level="pg" data-i18n="college_level_pg">Postgraduate (PG)</button>
      <button type="button" class="filter-pill" data-level="diploma" data-i18n="college_level_diploma">Diploma</button>
      <button type="button" class="filter-pill" data-level="cert" data-i18n="college_level_cert">Certifications</button>
    </div>

    <!-- Discipline / Faculty Category Filters -->
    <div id="college-category-filters" class="filter-pills-row">
      <button type="button" class="filter-pill active" data-category="all" data-i18n="college_cat_all">All Disciplines</button>
      <button type="button" class="filter-pill" data-category="cs_it" data-i18n="college_cat_cs_it">💻 Computer Science & IT</button>
      <button type="button" class="filter-pill" data-category="comm_mgmt" data-i18n="college_cat_comm_mgmt">📈 Commerce & Management</button>
      <button type="button" class="filter-pill" data-category="science" data-i18n="college_cat_science">🔬 Sciences & Research</button>
      <button type="button" class="filter-pill" data-category="arts_humanities" data-i18n="college_cat_arts_humanities">🏛️ Arts & Humanities</button>
      <button type="button" class="filter-pill" data-category="professional" data-i18n="college_cat_professional">⚖️ Professional & Design</button>
    </div>

    <!-- Courses Grid -->
    <div id="college-courses-grid" class="courses-catalog-grid">
      <!-- Injected dynamically by CoursesAndSkills.renderCollegeCourses() -->
    </div>

    <!-- Action Bar -->
    <div class="screen-actions-bar">
      <button type="button" id="btn-confirm-college-course" class="btn btn-primary" disabled style="min-width: 180px;">
        <span data-i18n="btn_continue">Continue</span> →
      </button>
    </div>
  </div>
</div>
