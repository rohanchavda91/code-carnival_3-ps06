<?php
/**
 * View: Onboarding Step 7 - Active Learner Hub Workspace & Redesigned Homepage
 * Clean, spacious, student-friendly, and visually memorable design.
 */
?>
<section id="screen-hub" class="app-screen">
  <div class="hub-container" style="max-width: 1160px; margin: 0 auto; padding-bottom: 2rem;">

    <!-- 1. HERO WELCOME SECTION WITH STUDENT ILLUSTRATION -->
    <div class="hub-hero-banner-new" style="margin-bottom: 1.75rem; padding: 2rem 2.25rem; border-radius: var(--radius-xl); background: linear-gradient(135deg, #EEF2FF 0%, #E0E7FF 45%, #F5F3FF 100%); border: 1px solid rgba(99, 102, 241, 0.25); box-shadow: var(--shadow-md); position: relative; overflow: hidden;">
      
      <div class="hub-hero-layout">
        <!-- Left Hero Content -->
        <div class="hub-hero-copy">
          <div style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.6rem; flex-wrap: wrap;">
            <span id="hub-track-badge" class="badge-popular" style="display: inline-block; background: #EEF2FF; color: #4338CA; border: 1px solid #C7D2FE;">
              Active Track: K-12 School Student
            </span>
            <span id="hub-level-badge" class="level-badge level-intermediate">Intermediate</span>
          </div>

          <h1 id="hub-welcome-title" class="hub-welcome-title" style="font-size: 2rem; font-weight: 800; color: #1E1B4B; margin-bottom: 0.5rem; line-height: 1.25;">
            Welcome back, <span style="color: var(--color-violet);">Student!</span>
          </h1>

          <p class="hub-status-text" id="hub-status-description" data-i18n="hub_ready_text" style="font-size: 0.95rem; color: #475569; margin-bottom: 1.25rem; line-height: 1.5; max-width: 520px;">
            Your personalized adaptive journey is ready. Step by step, achieve your learning goals.
          </p>

          <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center;">
            <button type="button" id="dashboard-btn-continue-learning" class="btn btn-primary" style="padding: 0.75rem 1.6rem; font-size: 0.95rem; font-weight: 700; border-radius: var(--radius-full); background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%); box-shadow: 0 6px 20px rgba(99, 102, 241, 0.35);">
              ▶ <span data-i18n="hub_btn_continue_learning">Continue Learning ›</span>
            </button>
            <button type="button" id="dashboard-btn-view-progress" class="btn btn-secondary" style="padding: 0.75rem 1.25rem; border-radius: var(--radius-full); background: rgba(255, 255, 255, 0.9); border: 1px solid #CBD5E1; font-weight: 600;">
              📈 <span data-i18n="dash_btn_analytics">View Progress</span>
            </button>
          </div>
        </div>

        <!-- Right Hero Illustration & Floating Badge -->
        <div class="hub-hero-visual">
          <div class="hub-hero-image-frame">
            <img class="hub-hero-illustration" src="assets/images/hero-student-cropped.jpg" alt="Student Learning Illustration" aria-label="Student Learning Illustration" data-i18n-aria-label="hub_hero_image_alt">
            
            <!-- Floating Motivational Badge -->
            <div class="hub-motivational-badge" data-i18n="hub_motivational_badge">
              Small Steps, Big Progress 💖
            </div>
          </div>
        </div>
      </div>

      <!-- Gamification Stats Strip -->
      <div class="hub-stats-strip">
        <div class="hub-stats-group">
          <div class="hub-stat">
            <span style="font-size: 0.7rem; color: #64748B; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;" data-i18n="dash_label_learner_level">Learner Level</span>
            <div style="font-size: 1.15rem; font-weight: 800; color: #1E1B4B;" id="dashboard-user-level" data-i18n="dash_stat_level">Level 1</div>
          </div>
          <span class="hub-stats-divider" aria-hidden="true"></span>
          <div class="hub-stat">
            <span style="font-size: 0.7rem; color: #64748B; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;" data-i18n="dash_label_xp_points">XP Points</span>
            <div style="font-size: 1.15rem; font-weight: 800; color: #0284C7;" id="dashboard-xp-points" data-i18n="dash_stat_xp">120 XP</div>
          </div>
          <span class="hub-stats-divider" aria-hidden="true"></span>
          <div class="hub-stat">
            <span style="font-size: 0.7rem; color: #64748B; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;" data-i18n="dash_label_daily_streak">Daily Streak</span>
            <div style="font-size: 1.15rem; font-weight: 800; color: #D97706;" id="dashboard-streak-days" data-i18n="dash_stat_streak">🔥 3 Days</div>
          </div>
        </div>

        <div class="hub-badges-group">
          <span style="font-size: 0.75rem; color: #64748B; font-weight: 600;" data-i18n="dash_label_badges">Badges:</span>
          <div id="dashboard-badges-list" class="hub-badges-list">
            <!-- Badge pills injected -->
          </div>
        </div>
      </div>

    </div>

    <!-- 2. QUICK FEATURE SHORTCUTS -->
    <div style="margin-bottom: 1.75rem;">
      <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.85rem; display: flex; align-items: center; gap: 0.4rem;">
        <span>🚀</span> <span data-i18n="hub_quick_shortcuts">Quick Learning Shortcuts</span>
      </h3>

      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 0.85rem;">
        <button type="button" id="hub-shortcut-ai-tutor" class="btn btn-secondary" style="padding: 0.9rem 1rem; flex-direction: row; gap: 0.75rem; text-align: left; border-radius: var(--radius-lg); background: var(--bg-surface); border: 1px solid var(--border-medium); justify-content: flex-start;">
          <span style="font-size: 1.3rem; padding: 0.45rem; background: rgba(124, 58, 237, 0.1); border-radius: 10px; color: var(--color-violet);">✦</span>
          <div>
            <div style="font-size: 0.88rem; font-weight: 700; color: var(--text-primary);" data-i18n="hub_shortcut_ai_tutor">AI Smart Tutor</div>
            <div style="font-size: 0.75rem; color: var(--text-muted);" data-i18n="hub_shortcut_ai_tutor_desc">Ask questions 24/7</div>
          </div>
        </button>

        <button type="button" id="hub-shortcut-quiz-gen" class="btn btn-secondary" style="padding: 0.9rem 1rem; flex-direction: row; gap: 0.75rem; text-align: left; border-radius: var(--radius-lg); background: var(--bg-surface); border: 1px solid var(--border-medium); justify-content: flex-start;">
          <span style="font-size: 1.3rem; padding: 0.45rem; background: rgba(2, 132, 199, 0.1); border-radius: 10px; color: var(--color-cyan-light);">⚡</span>
          <div>
            <div style="font-size: 0.88rem; font-weight: 700; color: var(--text-primary);" data-i18n="hub_shortcut_quiz">Practice Quiz</div>
            <div style="font-size: 0.75rem; color: var(--text-muted);" data-i18n="hub_shortcut_quiz_desc">Custom practice test</div>
          </div>
        </button>

        <button type="button" id="hub-shortcut-mistake-loop" class="btn btn-secondary" style="padding: 0.9rem 1rem; flex-direction: row; gap: 0.75rem; text-align: left; border-radius: var(--radius-lg); background: var(--bg-surface); border: 1px solid var(--border-medium); justify-content: flex-start;">
          <span style="font-size: 1.3rem; padding: 0.45rem; background: rgba(217, 119, 6, 0.1); border-radius: 10px; color: var(--color-warning);">🔁</span>
          <div>
            <div style="font-size: 0.88rem; font-weight: 700; color: var(--text-primary);" data-i18n="hub_shortcut_mistake_review">Mistake Review</div>
            <div style="font-size: 0.75rem; color: var(--text-muted);" data-i18n="hub_shortcut_mistake_desc">Master weak concepts</div>
          </div>
        </button>

        <button type="button" id="hub-shortcut-youtube" class="btn btn-secondary" style="padding: 0.9rem 1rem; flex-direction: row; gap: 0.75rem; text-align: left; border-radius: var(--radius-lg); background: var(--bg-surface); border: 1px solid var(--border-medium); justify-content: flex-start;">
          <span style="font-size: 1.3rem; padding: 0.45rem; background: rgba(220, 38, 38, 0.1); border-radius: 10px; color: var(--color-rose);">▶</span>
          <div>
            <div style="font-size: 0.88rem; font-weight: 700; color: var(--text-primary);" data-i18n="hub_shortcut_video_search">Video Search</div>
            <div style="font-size: 0.75rem; color: var(--text-muted);" data-i18n="hub_shortcut_video_desc">Curated explanations</div>
          </div>
        </button>
      </div>

      <!-- Secondary shortcuts hidden targets for JS event listener compatibility -->
      <div class="hidden">
        <button type="button" id="hub-shortcut-notes"></button>
        <button type="button" id="hub-shortcut-level-assess"></button>
      </div>
    </div>

    <!-- 3. MAIN WORKSPACE (Clean, Spacious 2-Column Layout, No Crowded Sidebar) -->
    <div style="display: grid; grid-template-columns: 1.6fr 1fr; gap: 1.5rem; align-items: start; margin-bottom: 2rem;">
      
      <!-- LEFT COLUMN: Active Study Lesson & Learning Roadmap -->
      <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        
        <!-- Active Lesson Card -->
        <div class="hub-hero-card" style="margin-bottom: 0; padding: 1.75rem; border-radius: var(--radius-xl); background: var(--bg-card); border: 1px solid var(--border-medium);">
          <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
            <div>
              <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--text-primary);" data-i18n="active_module_heading">Current Active Module: Lesson 1</h3>
              <p style="font-size: 0.88rem; color: var(--text-muted);" data-i18n="active_module_desc">
                Interactive conceptual foundations tailored to your learning style.
              </p>
            </div>
            <button type="button" id="hub-btn-lesson1" class="btn btn-primary" style="padding: 0.65rem 1.35rem; border-radius: var(--radius-full);">
              ▶ <span data-i18n="btn_start_lesson">Start Lesson 1</span>
            </button>
          </div>

          <!-- Topics in Progress section -->
          <div style="margin-top: 1rem; margin-bottom: 1.25rem;">
            <h4 style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 0.6rem; font-weight: 600;" data-i18n="dash_topic_in_progress">⚡ Topic in Progress:</h4>
            <div id="dashboard-in-progress-list" style="display: flex; flex-direction: column; gap: 0.6rem;">
              <!-- Injected dynamically -->
            </div>
          </div>

          <!-- Completed Topics section -->
          <div>
            <h4 style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 0.6rem; font-weight: 600;" data-i18n="dash_completed_topics">✅ Completed Topics:</h4>
            <div id="dashboard-completed-list" style="display: flex; flex-wrap: wrap; gap: 0.45rem;">
              <!-- Injected dynamically -->
            </div>
          </div>
        </div>

        <!-- Sequential Learning Sequence (Roadmap) -->
        <div class="hub-hero-card" style="margin-bottom: 0; padding: 1.75rem; border-radius: var(--radius-xl); background: var(--bg-card); border: 1px solid var(--border-medium);">
          <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
            <h3 style="font-size: 1.2rem; font-weight: 700; color: var(--text-primary);" data-i18n="hub_roadmap_title">Your Learning Roadmap</h3>
            <button type="button" id="hub-btn-retake" class="btn btn-ghost btn-sm" style="font-size: 0.8rem; color: var(--color-indigo-light);">
              🔄 <span data-i18n="btn_retake_assessment">Retake Assessment</span>
            </button>
          </div>
          <p style="font-size: 0.88rem; color: var(--text-muted); margin-bottom: 1.25rem;" data-i18n="hub_roadmap_desc">Personalized module sequence calibrated to your cognitive baseline:</p>
          
          <div id="hub-roadmap-container" class="hub-modules-list" style="margin-top: 0; display: flex; flex-direction: column; gap: 0.85rem;">
            <!-- Injected dynamically -->
          </div>
        </div>

      </div>

      <!-- RIGHT COLUMN: Performance Summary & Personalized Recommendations -->
      <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        
        <!-- Overall Progress & Assessment Card -->
        <div class="hub-hero-card" style="margin-bottom: 0; padding: 1.5rem; border-radius: var(--radius-xl); background: var(--bg-card); border: 1px solid var(--border-medium);">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.6rem;">
            <h4 style="font-size: 1.05rem; font-weight: 700; color: var(--text-primary);" data-i18n="dash_overall_progress">📊 Overall Progress</h4>
            <span id="dashboard-progress-ratio" style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">0 / 0 topics</span>
          </div>

          <div class="header-progress-bar-track" style="height: 10px; margin-bottom: 0.85rem; border-radius: var(--radius-full);">
            <div id="dashboard-progress-bar-fill" class="header-progress-bar-fill" style="width: 0%;"></div>
          </div>

          <div style="display: flex; justify-content: space-between; font-size: 0.85rem; color: var(--text-secondary); padding-bottom: 0.85rem; border-bottom: 1px solid var(--border-subtle);">
            <span><span data-i18n="dash_label_completed">Completed:</span> <strong id="dashboard-completed-count" style="color: var(--color-emerald-light);">0</strong></span>
            <span><span data-i18n="dash_label_in_progress">In Progress:</span> <strong id="dashboard-inprogress-count" style="color: var(--color-cyan-light);">0</strong></span>
          </div>

          <!-- Assessment & Quiz mini summaries -->
          <div style="margin-top: 0.85rem; display: flex; flex-direction: column; gap: 0.85rem;">
            <div>
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.25rem;">
                <span style="font-size: 0.85rem; font-weight: 600; color: var(--text-secondary);" data-i18n="dash_knowledge_level">🧠 Knowledge Level</span>
                <span id="dashboard-assessment-status" class="level-badge level-intermediate" style="font-size: 0.725rem; padding: 0.2rem 0.6rem;">Active</span>
              </div>
              <div id="dashboard-assessment-body">
                <!-- Dynamic body -->
              </div>
            </div>

            <div style="border-top: 1px solid var(--border-subtle); padding-top: 0.85rem;">
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.25rem;">
                <span style="font-size: 0.85rem; font-weight: 600; color: var(--text-secondary);" data-i18n="dash_practice_accuracy">🎯 Practice Accuracy</span>
                <span id="dashboard-quiz-accuracy-badge" class="level-badge level-advanced" style="display: none; font-size: 0.725rem; padding: 0.2rem 0.6rem;">0%</span>
              </div>
              <div id="dashboard-quiz-body">
                <!-- Dynamic body -->
              </div>
            </div>
          </div>
        </div>

        <!-- Strengths, Revision & Recommendations Card -->
        <div class="hub-hero-card" style="margin-bottom: 0; padding: 1.5rem; border-radius: var(--radius-xl); background: var(--bg-card); border: 1px solid var(--border-medium);">
          <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--color-emerald-light); margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.35rem;" data-i18n="dash_identified_strengths">
            ✓ Identified Strengths
          </h4>
          <div id="dashboard-strengths-container" style="display: flex; flex-wrap: wrap; gap: 0.45rem; min-height: 28px; margin-bottom: 1rem;">
            <!-- Injected dynamically -->
          </div>

          <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--color-warning); margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.35rem;" data-i18n="dash_topics_needing_revision">
            ▲ Topics Needing Revision
          </h4>
          <div id="dashboard-revision-container" style="display: flex; flex-wrap: wrap; gap: 0.45rem; min-height: 28px; margin-bottom: 1rem;">
            <!-- Injected dynamically -->
          </div>

          <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--color-cyan-light); margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.35rem;" data-i18n="dash_recommended_topics">
            💡 Recommended Next Topics
          </h4>
          <div id="dashboard-recommendations-list" style="display: flex; flex-direction: column; gap: 0.5rem;">
            <!-- Injected dynamically -->
          </div>
        </div>

      </div>

    </div>

    <!-- Collapsible Raw JSON Architecture Viewer -->
    <div id="hub-raw-json-viewer" class="raw-json-card hidden">
      <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
        <span style="font-size: 0.85rem; font-weight: 600; color: #fff;" data-i18n="hub_json_schema_label">
          Backend Integration Payload Schema
        </span>
        <button type="button" id="hub-toggle-raw-json" class="btn btn-ghost btn-sm" style="color: var(--color-cyan-light); font-size: 0.75rem;">Hide</button>
      </div>
      <pre id="hub-json-code" class="raw-json-code">{}</pre>
    </div>

  </div>
</section>
