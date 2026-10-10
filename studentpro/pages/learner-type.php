<?php
/**
 * View: Onboarding Step 2 - Learner Pathway Selection
 */
?>
<section id="screen-learner-type" class="app-screen">
  <div class="onboarding-screen-header">
    <h1 class="onboarding-screen-title" data-i18n="learner_type_title">Tell us how you want to learn</h1>
    <p class="onboarding-screen-subtitle" data-i18n="learner_type_subtitle">
      Choose the learning pathway that best describes your current stage or objective.
    </p>
  </div>

  <div class="pathways-grid">
    <!-- Pathway A: School -->
    <div class="pathway-card" data-pathway="school" role="button" tabindex="0">
      <div class="pathway-icon-wrap">🎒</div>
      <span class="pathway-badge" data-i18n="path_school_badge">K-12 Education</span>
      <h3 class="pathway-title" data-i18n="path_school_title">School Student</h3>
      <p class="pathway-desc" data-i18n="path_school_desc">
        Standard 1 through 12 curriculum tailored by grade level, conceptual foundations, and senior secondary streams.
      </p>
      <div class="pathway-arrow">
        <span data-i18n="btn_continue">Continue</span> →
      </div>
    </div>

    <!-- Pathway B: College & Higher Ed -->
    <div class="pathway-card" data-pathway="college" role="button" tabindex="0">
      <div class="pathway-icon-wrap">🎓</div>
      <span class="pathway-badge" data-i18n="path_college_badge">University & Degrees</span>
      <h3 class="pathway-title" data-i18n="path_college_title">College & Higher Education</h3>
      <p class="pathway-desc" data-i18n="path_college_desc">
        Undergraduate, Postgraduate, Technical & Professional degrees across Computer Science, Commerce, Sciences & Arts.
      </p>
      <div class="pathway-arrow">
        <span data-i18n="btn_continue">Continue</span> →
      </div>
    </div>

    <!-- Pathway C: Lifelong Learning & Skills -->
    <div class="pathway-card" data-pathway="skills" role="button" tabindex="0">
      <div class="pathway-icon-wrap">🚀</div>
      <span class="pathway-badge" data-i18n="path_skills_badge">Skills & Career</span>
      <h3 class="pathway-title" data-i18n="path_skills_title">I’m Here to Learn Something New</h3>
      <p class="pathway-desc" data-i18n="path_skills_desc">
        Lifelong learning, tech & coding, modern languages, design, and career leadership for adults and professionals.
      </p>
      <div class="pathway-arrow">
        <span data-i18n="btn_continue">Continue</span> →
      </div>
    </div>
  </div>
</section>
