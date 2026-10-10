<?php
/**
 * Dedicated lesson screen opened from the learner home page.
 */
?>
<section id="screen-lesson" class="app-screen hidden">
  <div class="lesson-page">
    <header class="lesson-page-header">
      <div>
        <p class="lesson-page-eyebrow" data-i18n="lesson_page_eyebrow">YOUR NEXT LEARNING STEP</p>
        <h1 id="lesson-page-title" data-i18n="lesson_page_title">Your lesson</h1>
        <p id="lesson-page-context" class="lesson-page-subject"></p>
      </div>
      <button type="button" id="lesson-btn-back" class="btn btn-secondary">
        <span aria-hidden="true">←</span>
        <span data-i18n="lesson_back_home">Back to home</span>
      </button>
    </header>

    <article class="lesson-content-card">
      <div class="lesson-topic-heading">
        <span class="lesson-topic-icon" aria-hidden="true">📘</span>
        <h2 id="lesson-topic-title"></h2>
      </div>
      <p id="lesson-page-status" class="lesson-page-status" role="status" aria-live="polite" data-i18n="lesson_loading">
        Preparing your personalized lesson...
      </p>
      <div id="lesson-page-content" class="lesson-page-content"></div>
      <section id="lesson-practice" class="lesson-practice hidden" aria-labelledby="lesson-practice-heading">
        <h2 id="lesson-practice-heading" data-i18n="lesson_practice_heading">Check your understanding</h2>
        <p id="lesson-practice-progress" class="lesson-practice-progress" aria-live="polite"></p>
        <div id="lesson-practice-questions"></div>
      </section>
      <p id="lesson-completion-status" class="lesson-completion-status hidden" role="status" aria-live="polite"></p>
      <div class="lesson-page-actions">
        <button type="button" id="lesson-btn-complete" class="btn btn-primary" disabled>
          <span data-i18n="lesson_mark_complete">Mark lesson complete</span>
          <span aria-hidden="true">✓</span>
        </button>
      </div>
    </article>
  </div>
</section>
