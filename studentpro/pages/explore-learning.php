<?php
/**
 * Database-backed department, subject, and topic browser.
 */
?>
<section id="screen-explore-learning" class="app-screen hidden">
  <div class="explore-learning-page">
    <header class="explore-learning-heading">
      <p class="explore-eyebrow" data-i18n="explore_eyebrow">PERSONALIZED CURRICULUM</p>
      <h1 data-i18n="explore_title">Explore Learning</h1>
      <p data-i18n="explore_subtitle">Choose your department, explore subjects, and learn topic by topic.</p>
    </header>

    <div id="explore-status" class="explore-status" role="status" aria-live="polite"></div>
    <div id="explore-error" class="explore-error hidden" role="alert">
      <span data-i18n="explore_error">Learning catalog could not be loaded.</span>
      <button type="button" id="explore-retry" class="btn btn-secondary" data-i18n="explore_retry">Try again</button>
    </div>

    <section class="explore-section" aria-labelledby="explore-department-title">
      <div class="explore-section-heading">
        <span class="explore-step" aria-hidden="true">1</span>
        <div>
          <h2 id="explore-department-title" data-i18n="explore_departments">Select Department</h2>
          <p data-i18n="explore_department_help">Choose a department to see its subjects.</p>
        </div>
      </div>
      <label class="sr-only" for="explore-department-search" data-i18n="explore_search_label">Search departments</label>
      <input id="explore-department-search" class="input-text explore-search" type="search"
        data-i18n-placeholder="explore_search_placeholder" placeholder="Search departments..." autocomplete="off">
      <div id="explore-departments" class="explore-departments" aria-live="polite"></div>
    </section>

    <section class="explore-section" aria-labelledby="explore-subject-title">
      <div class="explore-section-heading">
        <span class="explore-step" aria-hidden="true">2</span>
        <div>
          <h2 id="explore-subject-title" data-i18n="explore_subjects">Select Subject</h2>
          <p id="explore-subject-prompt" data-i18n="explore_select_department">Select a department first.</p>
        </div>
      </div>
      <div id="explore-subjects" class="explore-subjects" aria-live="polite"></div>
    </section>

    <section class="explore-section" aria-labelledby="explore-topic-title">
      <div class="explore-section-heading">
        <span class="explore-step" aria-hidden="true">3</span>
        <div>
          <h2 id="explore-topic-title" data-i18n="explore_topics">Explore Topics</h2>
          <p id="explore-topic-prompt" data-i18n="explore_select_subject">Select a subject to see its chapters and topics.</p>
        </div>
      </div>
      <div id="explore-topics" class="explore-topics" aria-live="polite"></div>
    </section>
  </div>
</section>
