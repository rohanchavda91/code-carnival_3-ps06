<?php
/**
 * View: Dedicated Progress Analytics Dashboard
 * Displays personalized progress data retrieved from localStorage using the existing data model.
 */
?>
<section id="screen-progress" class="app-screen hidden">
  <div class="hub-container" style="max-width: 1160px; margin: 0 auto; padding-bottom: 2rem;">

    <!-- Screen Navigation Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
      <div>
        <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--text-primary); margin-bottom: 0.25rem;" data-i18n="progress_dashboard_title">
          Learning Progress & Analytics
        </h1>
        <p style="font-size: 0.9rem; color: var(--text-secondary);" data-i18n="course_progress_intro">
          Track your mastery across curriculum topics and competencies.
        </p>
      </div>
      <button type="button" id="progress-btn-back-hub" class="btn btn-secondary" style="border-radius: var(--radius-full); padding: 0.55rem 1.25rem; font-weight: 600;">
        ← <span data-i18n="nav_home">Home</span>
      </button>
    </div>

    <!-- 1. LEARNING OVERVIEW -->
    <div style="margin-bottom: 2rem;">
      <div style="background: linear-gradient(135deg, #EEF2FF 0%, #E0E7FF 45%, #F5F3FF 100%); border: 1px solid rgba(99, 102, 241, 0.25); border-radius: var(--radius-xl); padding: 2rem; box-shadow: var(--shadow-sm);">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1.25rem; align-items: stretch;">
          
          <!-- Level Card -->
          <div style="padding: 1.25rem; background: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border-medium); display: flex; flex-direction: column; justify-content: space-between;">
            <div style="font-size: 0.75rem; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;" data-i18n="dash_knowledge_level">Knowledge Level</div>
            <div style="font-size: 1.85rem; font-weight: 800; color: var(--text-primary); margin: 0.5rem 0;" id="progress-level">Level 1</div>
            <div style="font-size: 0.8rem; color: var(--color-indigo-light); font-weight: 600;" id="progress-level-badge">Calibrated Baseline</div>
          </div>
          
          <!-- XP Card -->
          <div style="padding: 1.25rem; background: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border-medium); display: flex; flex-direction: column; justify-content: space-between;">
            <div style="font-size: 0.75rem; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;" data-i18n="score_label">Diagnostic Score</div>
            <div style="font-size: 1.85rem; font-weight: 800; color: var(--color-cyan-light); margin: 0.5rem 0;" id="progress-xp">120 XP</div>
            <div style="font-size: 0.8rem; color: var(--text-muted);" id="progress-xp-sub">Total earned points</div>
          </div>
          
          <!-- Overall Progress Card -->
          <div style="padding: 1.25rem; background: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border-medium); display: flex; flex-direction: column; justify-content: space-between;">
            <div style="font-size: 0.75rem; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;" data-i18n="dash_overall_progress">Overall Progress</div>
            <div style="font-size: 1.85rem; font-weight: 800; color: var(--color-emerald-light); margin: 0.5rem 0;" id="progress-percentage">0%</div>
            <div>
              <div style="height: 8px; width: 100%; background: var(--bg-surface); border-radius: var(--radius-full); overflow: hidden;">
                <div id="progress-bar-fill" style="height: 100%; width: 0%; background: linear-gradient(90deg, #4F46E5, #7C3AED); transition: width 0.4s ease;"></div>
              </div>
              <div style="display: flex; justify-content: space-between; font-size: 0.75rem; margin-top: 0.4rem; color: var(--text-secondary);">
                <span id="progress-completed">Completed: 0</span>
                <span id="progress-inprogress">In Progress: 0</span>
              </div>
            </div>
          </div>
          
          <!-- Sessions Card -->
          <div style="padding: 1.25rem; background: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border-medium); display: flex; flex-direction: column; justify-content: space-between;">
            <div style="font-size: 0.75rem; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;" data-i18n="progress_sessions_completed">Sessions Completed</div>
            <div style="font-size: 1.85rem; font-weight: 800; color: var(--text-primary); margin: 0.5rem 0;" id="progress-sessions">0</div>
            <div style="font-size: 0.8rem; color: var(--text-muted);">Learning sessions</div>
          </div>
          
          <!-- Time Card -->
          <div style="padding: 1.25rem; background: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border-medium); display: flex; flex-direction: column; justify-content: space-between;">
            <div style="font-size: 0.75rem; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;" data-i18n="progress_time_spent">Time Spent</div>
            <div style="font-size: 1.6rem; font-weight: 800; color: var(--text-primary); margin: 0.5rem 0;" id="progress-time">30m / day</div>
            <div style="font-size: 0.8rem; color: var(--text-muted);">Daily target pace</div>
          </div>
          
          <!-- Streak Card -->
          <div style="padding: 1.25rem; background: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border-medium); display: flex; flex-direction: column; justify-content: space-between;">
            <div style="font-size: 0.75rem; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;" data-i18n="progress_current_streak">Current Streak</div>
            <div style="font-size: 1.85rem; font-weight: 800; color: #D97706; margin: 0.5rem 0;" id="progress-streak-current">🔥 3 days</div>
            <div style="font-size: 0.8rem; color: var(--text-muted);" id="progress-streak-longest">Best: 3 days</div>
          </div>

        </div>
      </div>
    </div>

    <!-- 2. COURSE AND TOPIC PROGRESS -->
    <div style="margin-bottom: 2rem;">
      <h2 style="font-size: 1.25rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1rem;" data-i18n="course_progress_title">Course & Topic Progress</h2>
      <div style="background: var(--bg-card); border-radius: var(--radius-xl); padding: 1.5rem; border: 1px solid var(--border-medium);">
        <p style="font-size: 0.88rem; color: var(--text-secondary); margin-bottom: 1rem;" data-i18n="course_progress_intro">Curriculum mastery based on your active pathway:</p>
        <div id="progress-subjects-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1rem;">
          <!-- Subjects injected dynamically -->
        </div>
      </div>
    </div>

    <!-- 3. QUIZ AND PRACTICE PERFORMANCE -->
    <div style="margin-bottom: 2rem;">
      <h2 style="font-size: 1.25rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1rem;" data-i18n="quiz_performance_title">Quiz & Practice Performance</h2>
      <div style="background: var(--bg-card); border-radius: var(--radius-xl); padding: 1.5rem; border: 1px solid var(--border-medium);">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
          <div style="padding: 1rem; background: var(--bg-surface); border-radius: var(--radius-lg); border: 1px solid var(--border-subtle);">
            <div style="font-size: 0.75rem; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;" data-i18n="quizzes_attempted">Quizzes Attempted</div>
            <div style="font-size: 1.75rem; font-weight: 800; color: var(--text-primary); margin-top: 0.35rem;" id="quiz-attempted">0</div>
          </div>
          <div style="padding: 1rem; background: var(--bg-surface); border-radius: var(--radius-lg); border: 1px solid var(--border-subtle);">
            <div style="font-size: 0.75rem; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;" data-i18n="quiz_average">Average Score</div>
            <div style="font-size: 1.75rem; font-weight: 800; color: var(--color-cyan-light); margin-top: 0.35rem;" id="quiz-average">0%</div>
          </div>
          <div style="padding: 1rem; background: var(--bg-surface); border-radius: var(--radius-lg); border: 1px solid var(--border-subtle);">
            <div style="font-size: 0.75rem; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;" data-i18n="quiz_highest">Highest Score</div>
            <div style="font-size: 1.75rem; font-weight: 800; color: var(--color-emerald-light); margin-top: 0.35rem;" id="quiz-highest">0%</div>
          </div>
        </div>

        <h3 style="font-size: 0.95rem; font-weight: 700; color: var(--color-violet); margin-bottom: 0.75rem;" data-i18n="subject_wise_performance">Subject-wise Performance</h3>
        <div id="subject-performance-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 0.75rem;">
          <!-- Subject performance cards injected dynamically -->
        </div>
      </div>
    </div>

    <!-- 4. LEARNING ACTIVITY AND HISTORY -->
    <div style="margin-bottom: 2rem;">
      <h2 style="font-size: 1.25rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1rem;" data-i18n="learning_activity_title">Learning Activity & History</h2>
      <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 1.5rem;">
        
        <div style="background: var(--bg-card); border-radius: var(--radius-xl); padding: 1.5rem; border: 1px solid var(--border-medium);">
          <h3 style="font-size: 0.95rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.75rem;">Recent Learning Logs</h3>
          <div id="activity-history-list" style="max-height: 240px; overflow-y: auto; display: flex; flex-direction: column; gap: 0.5rem;">
            <!-- Activity items injected dynamically -->
          </div>
        </div>

        <div style="background: var(--bg-card); border-radius: var(--radius-xl); padding: 1.5rem; border: 1px solid var(--border-medium);">
          <h3 style="font-size: 0.95rem; font-weight: 700; color: var(--color-violet); margin-bottom: 0.75rem;" data-i18n="streak_history_title">Streak History</h3>
          <div id="streak-history-list" style="max-height: 240px; overflow-y: auto; display: flex; flex-direction: column; gap: 0.5rem;">
            <!-- Streak history items injected dynamically -->
          </div>
        </div>

      </div>
    </div>

    <!-- 5. STRENGTHS AND AREAS TO IMPROVE -->
    <div style="margin-bottom: 2rem;">
      <h2 style="font-size: 1.25rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1rem;" data-i18n="strengths_title">Strengths & Areas to Improve</h2>
      <div style="background: var(--bg-card); border-radius: var(--radius-xl); padding: 1.5rem; border: 1px solid var(--border-medium);">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
          <div>
            <div style="font-size: 0.85rem; color: var(--color-emerald-light); font-weight: 700; margin-bottom: 0.5rem;" data-i18n="strong_topics_title">✓ Strong Topics</div>
            <div id="strong-topics-list" style="display: flex; flex-wrap: wrap; gap: 0.45rem;">
              <!-- Strong topics injected dynamically -->
            </div>
          </div>
          <div>
            <div style="font-size: 0.85rem; color: var(--color-warning); font-weight: 700; margin-bottom: 0.5rem;" data-i18n="areas_improve_title">▲ Areas to Improve</div>
            <div id="areas-improve-list" style="display: flex; flex-wrap: wrap; gap: 0.45rem;">
              <!-- Areas to improve injected dynamically -->
            </div>
          </div>
        </div>

        <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--border-subtle); display: flex; justify-content: flex-end;">
          <button type="button" id="resume-learning-btn" class="btn btn-primary" style="padding: 0.75rem 1.75rem; font-size: 0.95rem; font-weight: 700; border-radius: var(--radius-full); background: linear-gradient(135deg, #10B981 0%, #059669 100%); box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35);">
            ▶ <span data-i18n="resume_learning">Resume Learning</span>
          </button>
        </div>
      </div>
    </div>

    <!-- 6. ACHIEVEMENTS -->
    <div>
      <h2 style="font-size: 1.25rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1rem;" data-i18n="achievements_title">Achievements</h2>
      <div style="background: var(--bg-card); border-radius: var(--radius-xl); padding: 1.5rem; border: 1px solid var(--border-medium);">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
          <div>
            <div style="font-size: 0.75rem; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; margin-bottom: 0.5rem;" data-i18n="badges_earned">Badges Earned</div>
            <div id="badges-earned-list" style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
              <!-- Badges injected dynamically -->
            </div>
          </div>
          <div>
            <div style="font-size: 0.75rem; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; margin-bottom: 0.5rem;" data-i18n="xp_milestones">XP Milestones</div>
            <div id="xp-milestones-list" style="display: flex; flex-direction: column; gap: 0.5rem;">
              <!-- XP milestones injected dynamically -->
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>