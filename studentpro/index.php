<?php
/**
 * SYNAPSE AI — Intelligent Adaptive Learning Platform
 * Master Entry Point & Production Application Shell (STEP 1)
 */
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title data-i18n="meta_title">Synapse AI — Intelligent Personalized Learning Platform</title>
  
  <!-- Meta Tags for Production EdTech Experience -->
  <meta name="description" content="AI-powered adaptive learning system tailoring cognitive curriculum across school grades, college degrees, and professional skills.">
  <meta name="theme-color" content="#070B14">
  <link rel="manifest" href="manifest.json">
  <link rel="icon" type="image/png" sizes="192x192" href="assets/icons/icon-192.png">
  <link rel="apple-touch-icon" href="assets/icons/icon-192.png">

  <script>
    try {
      if (localStorage.getItem('synapse_theme') === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
      }
    } catch (error) {
      console.warn('Theme preference read failed', error);
    }
  </script>

  <!-- Production CSS Stylesheets -->
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/auth.css">
  <link rel="stylesheet" href="css/onboarding.css?v=15">
  <link rel="stylesheet" href="css/assessment.css">
  <link rel="stylesheet" href="css/quiz-generator.css">
  <link rel="stylesheet" href="css/explore-learning.css">
  <link rel="stylesheet" href="css/ai-mentor.css">
  <link rel="stylesheet" href="css/responsive.css?v=15">
  <!-- Glassmorphism Material Layer (loaded last: frosted glass, blur, ambient gradients) -->
  <link rel="stylesheet" href="css/glassmorphism.css?v=11">
  <!-- Left Sidebar Layout (loaded last: sidebar geometry + responsive collapse) -->
  <link rel="stylesheet" href="css/sidebar.css">
</head>
<body>

  <div id="app-root">
    
    <!-- Minimal Application Header (No Marketing Navbar) -->
    <?php include_once __DIR__ . '/includes/header.php'; ?>

    <!-- Main Application Canvas -->
    <main class="main-content">

      <!-- SCREEN 1: AUTHENTICATION (LOGIN & SIGN UP) -->
      <?php include_once __DIR__ . '/pages/login.php'; ?>

      <!-- SCREEN 2: INTERFACE LANGUAGE SELECTION (FIRST ONBOARDING STEP) -->
      <?php include_once __DIR__ . '/pages/language.php'; ?>

      <!-- SCREEN 3: LEARNER TYPE / MAIN PATHWAYS (SCHOOL, COLLEGE, SKILLS) -->
      <?php include_once __DIR__ . '/pages/learner-type.php'; ?>

      <!-- SCREEN 4: ACADEMIC & SUBJECT SELECTION -->
      <section id="screen-academic-selection" class="app-screen hidden">
        <!-- Subview A: School Standards 1-12 & Senior Streams -->
        <?php include_once __DIR__ . '/pages/school-level.php'; ?>

        <!-- Subview B: College & Higher Education Searchable Courses -->
        <?php include_once __DIR__ . '/pages/college-courses.php'; ?>

        <!-- Subview C: Lifelong Learning & Professional Skills -->
        <?php include_once __DIR__ . '/pages/learning-interest.php'; ?>
      </section>

      <!-- SCREEN 5: LEARNING GOALS, COGNITIVE STYLE & DAILY TIME -->
      <?php include_once __DIR__ . '/pages/learning-preferences.php'; ?>

      <!-- SCREEN 6: KNOWLEDGE & LEARNING LEVEL ASSESSMENT -->
      <?php include_once __DIR__ . '/pages/assessment.php'; ?>

      <!-- SCREEN 7: PERSONALIZED LEARNING RESULT & TAILORED ROADMAP -->
      <?php include_once __DIR__ . '/pages/assessment-result.php'; ?>

      <!-- SCREEN 8: ACTIVE LEARNER HUB WORKSPACE & PROFILE -->
      <?php include_once __DIR__ . '/pages/learning-profile.php'; ?>
      <?php include_once __DIR__ . '/pages/progress-page.php'; ?>
      <?php include_once __DIR__ . '/pages/lesson.php'; ?>
      <?php include_once __DIR__ . '/pages/explore-learning.php'; ?>

      <!-- AI MENTOR: CONVERSATIONAL LEARNING SUPPORT -->
      <?php include_once __DIR__ . '/pages/ai-mentor.php'; ?>

      <!-- LEVEL ASSESSMENT: STANDALONE KNOWLEDGE EVALUATION -->
      <?php include_once __DIR__ . '/pages/level-assessment.php'; ?>

      <!-- AI QUIZ GENERATOR -->
      <?php include_once __DIR__ . '/pages/quiz-generator.php'; ?>

      <!-- MISTAKE MASTERY REVIEW -->
      <?php include_once __DIR__ . '/pages/mistake-mastery.php'; ?>

      <!-- YOUTUBE EXPLORER / EXPLANATIONS -->
      <?php include_once __DIR__ . '/pages/youtube-explorer.php'; ?>

      <!-- NOTES MAKER -->
      <?php include_once __DIR__ . '/pages/notes-maker.php'; ?>

    </main>

    <!-- Fixed Bottom Navigation & Subtle Application Footer -->
    <?php include_once __DIR__ . '/includes/footer.php'; ?>

  </div>

  <!-- Data Catalogues -->
  <script src="data/languages.js"></script>
  <script src="data/school-subjects.js"></script>
  <script src="data/college-courses.js"></script>
  <script src="data/skills.js"></script>
  <script src="data/questions.js"></script>
  <script src="data/level-assessment-questions.js"></script>
  <script src="data/lessons.js"></script>

  <!-- Core JavaScript Architecture Modules -->
  <script src="js/translations.js"></script>
  <!-- Centralized TRANSLATIONS extensions (same dictionary/keys, appended at load) -->
  <script src="js/translations-ext.js"></script>
  <script src="js/translations-intl-1.js"></script>
  <script src="js/translations-intl-2.js"></script>
  <script src="js/translations-intl-3.js"></script>
  <script src="js/storage.js"></script>
  <script src="js/local-ai-engine.js"></script>
  <script src="js/auth.js"></script>
  <script src="js/guest-limits.js"></script>
  <script src="js/subjects.js"></script>
  <script src="js/courses.js"></script>
  <script src="js/personalization.js"></script>
  <script src="js/assessment.js"></script>
  <script src="js/level-assessment.js"></script>
  <script src="js/learner-profile.js"></script>
  <script src="js/explore-learning.js"></script>
  <script src="js/quiz-generator.js"></script>
  <script src="js/mistake-mastery.js"></script>
  <script src="js/youtube-explorer.js"></script>
  <script src="js/notes-maker.js"></script>
  <script src="js/onboarding.js"></script>
  <script src="js/ai-mentor.js"></script>
  <script src="js/app.js"></script>

</body>
</html>
