<?php
/**
 * View: Onboarding Step 3C - Lifelong Learning & Professional Skills Catalog
 */
?>
<div id="subview-skills" class="academic-subview hidden">
  <div class="onboarding-screen-header">
    <h2 class="onboarding-screen-title" data-i18n="skills_title">Choose a Domain or Skill to Master</h2>
    <p class="onboarding-screen-subtitle" data-i18n="skills_subtitle">
      Tailored for adults, career switchers, and curious learners. Pick your target focus area.
    </p>
  </div>

  <div class="app-card">
    <!-- Search Bar -->
    <div class="search-bar-wrap">
      <span class="search-icon" aria-hidden="true">🔍</span>
      <input type="text" id="skills-search-input" class="search-bar-input" data-i18n-placeholder="skills_search_placeholder" placeholder="Search skills (e.g. Python, UI/UX, Spoken English, Public Speaking...)" data-i18n-aria-label="skills_search_aria" aria-label="Search skills">
    </div>

    <!-- Domain Categories -->
    <div id="skills-category-filters" class="filter-pills-row">
      <button type="button" class="filter-pill active" data-category="all" data-i18n="skills_cat_all">All Domains</button>
      <button type="button" class="filter-pill" data-category="tech" data-i18n="skills_cat_tech">💻 Technology & Code</button>
      <button type="button" class="filter-pill" data-category="languages" data-i18n="skills_cat_languages">🗣️ Spoken Languages</button>
      <button type="button" class="filter-pill" data-category="design" data-i18n="skills_cat_design">🎨 Design & Creative</button>
      <button type="button" class="filter-pill" data-category="career" data-i18n="skills_cat_career">🚀 Career & Leadership</button>
      <button type="button" class="filter-pill" data-category="business" data-i18n="skills_cat_business">📊 Business & Finance</button>
    </div>

    <!-- Skills Grid -->
    <div id="skills-catalog-grid" class="skills-catalog-grid">
      <!-- Injected dynamically by CoursesAndSkills.renderSkills() -->
    </div>

    <!-- Action Bar -->
    <div class="screen-actions-bar">
      <button type="button" id="btn-confirm-skill" class="btn btn-primary" disabled style="min-width: 180px;">
        <span data-i18n="btn_continue">Continue</span> →
      </button>
    </div>
  </div>
</div>
