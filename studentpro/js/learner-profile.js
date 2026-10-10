/**
 * Learner Profile & Hub Workspace Module
 * Renders the active learner dashboard, synthesized profile, and demo controls.
 */
const LearnerProfile = {
  currentLessonTopic: null,
  currentLesson: null,
  lessonPracticeAnswers: [],

  init() {
    this.bindEvents();
    window.addEventListener('synapse:profile_updated', (e) => {
      this.renderHub(e.detail);
      if (typeof Onboarding !== 'undefined' && Onboarding.currentStep === 'progress') {
        this.renderProgressDashboard(e.detail);
      }
    });
    window.addEventListener('synapse:language_changed', () => {
      this.renderHub();
      if (typeof Onboarding !== 'undefined' && Onboarding.currentStep === 'progress') {
        this.renderProgressDashboard();
      }
    });
  },

  bindEvents() {
    // Retake assessment button in hub
    const retakeHubBtn = document.getElementById('hub-btn-retake');
    if (retakeHubBtn) {
      retakeHubBtn.addEventListener('click', () => {
        const profile = StorageService.getProfile();
        Assessment.startForProfile(profile);
        Onboarding.goToStep('assessment');
      });
    }

    // Level Assessment Button in Hub
    const levelAssessmentBtn = document.getElementById('hub-btn-level-assessment');
    if (levelAssessmentBtn) {
      levelAssessmentBtn.addEventListener('click', () => {
        Onboarding.goToStep('level-assessment');
      });
    }

    const aiMentorBtn = document.getElementById('hub-btn-ai-mentor');
    if (aiMentorBtn) {
      aiMentorBtn.addEventListener('click', () => Onboarding.goToStep('ai-mentor'));
    }

    // Working "Continue Learning" action button
    const btnContinue = document.getElementById('dashboard-btn-continue-learning');
    if (btnContinue) {
      btnContinue.addEventListener('click', () => {
        this.startLearningSession();
      });
    }

    const lessonBackBtn = document.getElementById('lesson-btn-back');
    if (lessonBackBtn) {
      lessonBackBtn.addEventListener('click', () => Onboarding.goToStep('hub'));
    }

    const lessonCompleteBtn = document.getElementById('lesson-btn-complete');
    if (lessonCompleteBtn) {
      lessonCompleteBtn.addEventListener('click', () => this.completeCurrentLesson());
    }

    // Working "View Progress" action button -> Navigates to dedicated Progress Dashboard screen
    const btnViewProgress = document.getElementById('dashboard-btn-view-progress');
    if (btnViewProgress) {
      btnViewProgress.addEventListener('click', () => {
        if (typeof Onboarding !== 'undefined') {
          Onboarding.goToStep('progress');
        }
      });
    }

    // Quick Learning Shortcuts
    const scAiTutor = document.getElementById('hub-shortcut-ai-tutor');
    if (scAiTutor) {
      scAiTutor.addEventListener('click', () => Onboarding.goToStep('ai-mentor'));
    }
    const scQuizGen = document.getElementById('hub-shortcut-quiz-gen');
    if (scQuizGen) {
      scQuizGen.addEventListener('click', () => Onboarding.goToStep('quiz-generator'));
    }
    const scMistake = document.getElementById('hub-shortcut-mistake-loop');
    if (scMistake) {
      scMistake.addEventListener('click', () => Onboarding.goToStep('mistake-mastery'));
    }
    const scYoutube = document.getElementById('hub-shortcut-youtube');
    if (scYoutube) {
      scYoutube.addEventListener('click', () => Onboarding.goToStep('youtube-explorer'));
    }

    // Progress page back button & resume button
    const btnBackHub = document.getElementById('progress-btn-back-hub');
    if (btnBackHub) {
      btnBackHub.addEventListener('click', () => Onboarding.goToStep('hub'));
    }
    const btnResumeLearning = document.getElementById('resume-learning-btn');
    if (btnResumeLearning) {
      btnResumeLearning.addEventListener('click', () => {
        this.startLearningSession();
      });
    }

    // Toggle JSON profile viewer
    const toggleJsonBtn = document.getElementById('hub-toggle-raw-json');
    const jsonContainer = document.getElementById('hub-raw-json-viewer');
    if (toggleJsonBtn && jsonContainer) {
      toggleJsonBtn.addEventListener('click', () => {
        const isHidden = jsonContainer.classList.contains('hidden');
        jsonContainer.classList.toggle('hidden', !isHidden);
        toggleJsonBtn.textContent = isHidden ? I18n.t('raw_profile_hide') : I18n.t('raw_profile_toggle');
      });
    }

    // Lesson 1 Preview Action
    const lesson1Btn = document.getElementById('hub-btn-lesson1');
    if (lesson1Btn) {
      lesson1Btn.addEventListener('click', () => {
        this.startLearningSession();
      });
    }

    // Reset All Demo Data button
    const resetBtns = document.querySelectorAll('.btn-action-reset-demo');
    resetBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        if (confirm(I18n.t('confirm_reset_demo'))) {
          StorageService.resetAll();
          window.location.reload();
        }
      });
    });
  },

  renderHub(profile = null) {
    if (!profile) profile = StorageService.getProfile();

    const welcomeEl = document.getElementById('hub-welcome-title');
    const trackBadge = document.getElementById('hub-track-badge');
    const levelEl = document.getElementById('hub-level-badge');
    const goalsListEl = document.getElementById('hub-goals-list');
    const roadmapHubEl = document.getElementById('hub-roadmap-container');
    const rawJsonEl = document.getElementById('hub-json-code');

    const name = profile.name || I18n.t('hub_learner_fallback');
    if (welcomeEl) welcomeEl.textContent = I18n.t('hub_welcome', { name: name });

    if (trackBadge) {
      const typeMap = {
        school: I18n.t('label_track_school'),
        college: I18n.t('label_track_college'),
        skills: I18n.t('label_track_skills')
      };
      trackBadge.textContent = typeMap[profile.learnerType] || I18n.t('label_track_default');
    }

    // Localized label maps for roadmap/result chrome rendered below
    const calibLabels = {
      'Fundamentals': I18n.t('calib_fundamentals'),
      'High Pace / Mastery': I18n.t('calib_high_pace_mastery'),
      'Standard Intermediate': I18n.t('calib_standard_intermediate'),
      'Foundation Reinforcement': I18n.t('calib_foundation_reinforcement'),
      'Guided Essentials': I18n.t('calib_guided_essentials'),
      'Standard': I18n.t('calib_standard')
    };
    const tFocus = (f) => {
      const key = 'path_focus_' + String(f || '').toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g, '');
      const translated = I18n.t(key);
      return translated === key ? f : translated;
    };
    const tDuration = (d) => {
      const m = /^(\d+) Lessons • (.+)h Total$/.exec(d || '');
      return m ? I18n.t('path_duration_format', { lessons: m[1], hours: m[2] }) : d;
    };

    // --- 1. ASSESSMENT LEVEL CARD (Only displays level if assessment exists, else Empty State) ---
    const assessmentStatusBadge = document.getElementById('dashboard-assessment-status');
    const assessmentBodyEl = document.getElementById('dashboard-assessment-body');

    if (profile.assessmentCompleted && profile.assessmentResult) {
      const res = profile.assessmentResult;
      const levelKey = `level_${(res.level || 'intermediate').toLowerCase()}`;
      if (levelEl) {
        levelEl.textContent = I18n.t(levelKey);
        levelEl.className = `level-badge level-${(res.level || 'intermediate').toLowerCase()}`;
        levelEl.style.display = 'inline-block';
      }
      if (assessmentStatusBadge) {
        assessmentStatusBadge.textContent = I18n.t(levelKey);
        assessmentStatusBadge.className = `level-badge level-${(res.level || 'intermediate').toLowerCase()}`;
      }
      if (assessmentBodyEl) {
        const evalDate = res.evaluatedAt ? new Date(res.evaluatedAt).toLocaleDateString() : I18n.t('hub_recent_session');
        assessmentBodyEl.innerHTML = `
          <div style="margin-top: 0.4rem;">
            <div style="font-size: 1.4rem; font-weight: 700; color: var(--text-primary);">${I18n.t('hub_accuracy_value', { pct: res.percentage || 0 })}</div>
            <div style="font-size: 0.85rem; color: var(--text-secondary); margin-top: 0.25rem;">
              ${I18n.t('rec_difficulty_label')}: <strong style="color: var(--text-primary);">${calibLabels[res.calibratedDifficulty] || res.calibratedDifficulty || I18n.t('calib_standard')}</strong>
            </div>
            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.4rem;">
              ${I18n.t('hub_evaluated_on', { date: evalDate })}
            </div>
          </div>
        `;
      }
    } else {
      // Empty state for new students who have not taken diagnostic test
      if (levelEl) {
        levelEl.textContent = I18n.t('hub_not_assessed');
        levelEl.className = 'level-badge level-beginner';
      }
      if (assessmentStatusBadge) {
        assessmentStatusBadge.textContent = I18n.t('hub_status_pending');
        assessmentStatusBadge.className = 'level-badge level-beginner';
      }
      if (assessmentBodyEl) {
        assessmentBodyEl.innerHTML = `
          <div style="padding: 0.5rem 0;">
            <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.75rem;">
              ${I18n.t('hub_no_diagnostic_text')}
            </p>
            <button type="button" id="dashboard-btn-start-assess" class="btn btn-primary btn-sm">
              ${I18n.t('hub_start_diagnostic_btn')}
            </button>
          </div>
        `;
        setTimeout(() => {
          const btnStart = document.getElementById('dashboard-btn-start-assess');
          if (btnStart) {
            btnStart.addEventListener('click', () => {
              Assessment.startForProfile(profile);
              Onboarding.goToStep('assessment');
            });
          }
        }, 50);
      }
    }

    // --- 2. OVERALL PROGRESS & TOPICS METRICS ---
    const roadmap = (profile.assessmentResult && profile.assessmentResult.generatedRoadmap)
      ? profile.assessmentResult.generatedRoadmap
      : [
          { title: 'Core Foundations & Essentials', icon: '📖', duration: I18n.t('hub_default_duration_1'), focus: I18n.t('path_focus_concepts') },
          { title: 'Applied Practice & Problem Solving', icon: '⚡', duration: I18n.t('hub_default_duration_2'), focus: I18n.t('path_focus_application') },
          { title: 'Advanced Concepts & Applications', icon: '🚀', duration: I18n.t('hub_default_duration_3'), focus: I18n.t('path_focus_mastery') }
        ];

    const completedList = Array.isArray(profile.completedTopics) ? profile.completedTopics : [];
    const totalTopics = roadmap.length;
    const completedCount = completedList.length;
    const inProgressCount = Math.max(0, Math.min(totalTopics - completedCount, 1));
    const overallPct = totalTopics > 0 ? Math.round((completedCount / totalTopics) * 100) : 0;

    const overallPctEl = document.getElementById('dashboard-overall-pct');
    const ratioEl = document.getElementById('dashboard-progress-ratio');
    const barFillEl = document.getElementById('dashboard-progress-bar-fill');
    const completedCountEl = document.getElementById('dashboard-completed-count');
    const inprogressCountEl = document.getElementById('dashboard-inprogress-count');

    if (overallPctEl) overallPctEl.textContent = `${overallPct}%`;
    if (ratioEl) ratioEl.textContent = I18n.t('hub_progress_ratio', { completed: completedCount, total: totalTopics });
    if (barFillEl) barFillEl.style.width = `${overallPct}%`;
    if (completedCountEl) completedCountEl.textContent = completedCount;
    if (inprogressCountEl) inprogressCountEl.textContent = inProgressCount;

    // --- 3. PRACTICE QUIZ RESULTS & ACCURACY ---
    const quizBodyEl = document.getElementById('dashboard-quiz-body');
    const quizBadgeEl = document.getElementById('dashboard-quiz-accuracy-badge');

    const history = Array.isArray(profile.quizHistory) ? profile.quizHistory : [];
    if (profile.assessmentResult) {
      const diagQuiz = {
        title: I18n.t('hub_quiz_diagnostic_baseline'),
        score: profile.assessmentResult.score || 0,
        total: profile.assessmentResult.total || 0,
        percentage: profile.assessmentResult.percentage || 0
      };
      if (history.length === 0) history.push(diagQuiz);
    }

    if (history.length > 0) {
      const latestQuiz = history[0];
      const avgAccuracy = Math.round(history.reduce((acc, q) => acc + (q.percentage || 0), 0) / history.length);
      
      if (quizBadgeEl) {
        quizBadgeEl.style.display = 'inline-block';
        quizBadgeEl.textContent = I18n.t('hub_avg_accuracy', { pct: avgAccuracy });
      }
      if (quizBodyEl) {
        quizBodyEl.innerHTML = `
          <div style="margin-top: 0.4rem;">
            <div style="font-size: 1.4rem; font-weight: 700; color: var(--color-emerald);">${I18n.t('hub_quiz_score_line', { score: latestQuiz.score, total: latestQuiz.total, pct: latestQuiz.percentage })}</div>
            <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.25rem;">
              ${I18n.t('hub_quiz_latest_label')} <strong>${latestQuiz.title}</strong>
            </div>
            <div style="font-size: 0.75rem; color: var(--text-dim); margin-top: 0.4rem;">
              ${I18n.t('hub_quiz_sessions_logged', { count: history.length })}
            </div>
          </div>
        `;
      }
    } else {
      // Empty state for quiz history
      if (quizBadgeEl) quizBadgeEl.style.display = 'none';
      if (quizBodyEl) {
        quizBodyEl.innerHTML = `
          <div style="padding: 0.5rem 0;">
            <p style="font-size: 0.85rem; color: var(--text-muted);">
              ${I18n.t('hub_no_quiz_records')}
            </p>
          </div>
        `;
      }
    }

    // --- 4. IDENTIFIED STRENGTHS & REVISION TOPICS ---
    const strengthsEl = document.getElementById('dashboard-strengths-container');
    const revisionEl = document.getElementById('dashboard-revision-container');

    const strongAreas = (profile.assessmentResult && profile.assessmentResult.strongAreas) ? profile.assessmentResult.strongAreas : [];
    const focusAreas = (profile.assessmentResult && profile.assessmentResult.focusAreas) ? profile.assessmentResult.focusAreas : [];

    if (strengthsEl) {
      strengthsEl.innerHTML = '';
      if (strongAreas.length > 0) {
        strongAreas.forEach(topic => {
          const pill = document.createElement('span');
          pill.className = 'area-badge strong';
          pill.textContent = `✓ ${topic}`;
          strengthsEl.appendChild(pill);
        });
      } else {
        strengthsEl.innerHTML = `<span style="font-size: 0.85rem; color: var(--text-muted);">${I18n.t('hub_no_strengths_text')}</span>`;
      }
    }

    if (revisionEl) {
      revisionEl.innerHTML = '';
      if (focusAreas.length > 0) {
        focusAreas.forEach(topic => {
          const pill = document.createElement('span');
          pill.className = 'area-badge focus';
          pill.textContent = `▲ ${topic}`;
          revisionEl.appendChild(pill);
        });
      } else {
        revisionEl.innerHTML = `<span style="font-size: 0.85rem; color: var(--text-muted);">${I18n.t('hub_no_focus_areas_text')}</span>`;
      }
    }

    // --- 5. PERSONALIZED NEXT-TOPIC RECOMMENDATIONS ---
    const recsListEl = document.getElementById('dashboard-recommendations-list');
    if (recsListEl) {
      recsListEl.innerHTML = '';
      const recommendedTopics = [];

      if (focusAreas.length > 0) {
        recommendedTopics.push({ title: focusAreas[0], reason: I18n.t('rec_reason_focus_area') });
      }
      roadmap.forEach(mod => {
        if (!completedList.includes(mod.title) && recommendedTopics.length < 3) {
          recommendedTopics.push({ title: mod.title, reason: I18n.t('rec_reason_progression') });
        }
      });

      if (recommendedTopics.length === 0) {
        recommendedTopics.push({ title: 'Mastery Challenge & Synthesis', reason: I18n.t('rec_reason_complete') });
      }

      recommendedTopics.forEach(rec => {
        const card = document.createElement('div');
        card.style.cssText = 'display: flex; justify-content: space-between; align-items: center; padding: 0.6rem 0.8rem; background: var(--bg-surface-elevated); border: 1px solid var(--border-subtle); border-radius: 8px;';
        card.innerHTML = `
          <div>
            <div style="font-size: 0.88rem; font-weight: 600; color: var(--text-primary);">${rec.title}</div>
            <div style="font-size: 0.75rem; color: var(--text-muted);">${rec.reason}</div>
          </div>
          <button type="button" class="btn btn-secondary btn-sm btn-rec-start" data-topic="${rec.title}">
            ${I18n.t('btn_start')}
          </button>
        `;
        recsListEl.appendChild(card);
      });

      // Bind recommendation start buttons
      setTimeout(() => {
        const recBtns = recsListEl.querySelectorAll('.btn-rec-start');
        recBtns.forEach(b => {
          b.addEventListener('click', (e) => {
            const topic = e.target.getAttribute('data-topic');
            this.startLearningSession(topic);
          });
        });
      }, 50);
    }

    // --- 6. TOPICS IN PROGRESS & COMPLETED LISTS ---
    const inProgressContainer = document.getElementById('dashboard-in-progress-list');
    const completedContainer = document.getElementById('dashboard-completed-list');

    if (inProgressContainer) {
      inProgressContainer.innerHTML = '';
      const uncompletedRoadmap = roadmap.filter(mod => !completedList.includes(mod.title));

      if (uncompletedRoadmap.length > 0) {
        const activeMod = uncompletedRoadmap[0];
        const card = document.createElement('div');
        card.style.cssText = 'padding: 0.85rem; background: rgba(56, 189, 248, 0.06); border: 1px solid rgba(56, 189, 248, 0.25); border-radius: 10px; display: flex; justify-content: space-between; align-items: center;';
        card.innerHTML = `
          <div>
            <div style="font-size: 0.95rem; font-weight: 600; color: var(--color-cyan);">${activeMod.icon} ${activeMod.title}</div>
            <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.2rem;">${activeMod.description || I18n.t('hub_active_unit_fallback')}</div>
          </div>
          <button type="button" class="btn btn-primary btn-sm btn-mark-topic-complete" data-topic="${activeMod.title}">
            ${I18n.t('btn_mark_complete')}
          </button>
        `;
        inProgressContainer.appendChild(card);

        setTimeout(() => {
          const btnComplete = card.querySelector('.btn-mark-topic-complete');
          if (btnComplete) {
            btnComplete.addEventListener('click', () => {
              StorageService.markTopicCompleted(activeMod.title);
            });
          }
        }, 50);
      } else {
        inProgressContainer.innerHTML = `<div style="font-size: 0.85rem; color: var(--color-emerald);">${I18n.t('hub_all_topics_completed')}</div>`;
      }
    }

    if (completedContainer) {
      completedContainer.innerHTML = '';
      if (completedList.length > 0) {
        completedList.forEach(t => {
          const badge = document.createElement('span');
          badge.className = 'goal-pill';
          badge.style.borderColor = 'rgba(52, 211, 153, 0.4)';
          badge.style.background = 'rgba(52, 211, 153, 0.12)';
          badge.style.color = '#34d399';
          badge.textContent = `✅ ${t}`;
          completedContainer.appendChild(badge);
        });
      } else {
        completedContainer.innerHTML = `<span style="font-size: 0.85rem; color: var(--text-muted);">${I18n.t('hub_no_completed_topics')}</span>`;
      }
    }

    // --- 7. ROADMAP LIST RENDERING ---
    if (roadmapHubEl) {
      roadmapHubEl.innerHTML = '';
      roadmap.forEach((mod, idx) => {
        const isDone = completedList.includes(mod.title);
        const item = document.createElement('div');
        item.className = `hub-module-item ${idx === 0 && !isDone ? 'active' : ''}`;
        item.innerHTML = `
          <div class="hub-mod-icon">${isDone ? '✅' : mod.icon}</div>
          <div class="hub-mod-body">
            <div class="hub-mod-title-row">
              <h5>${mod.title}</h5>
              <span class="hub-mod-status">${isDone ? I18n.t('hub_status_completed') : (idx === 0 ? I18n.t('hub_status_in_progress') : I18n.t('hub_status_upcoming'))}</span>
            </div>
            <p>${mod.description}</p>
            <div class="hub-mod-meta">
              <span>⏱️ ${tDuration(mod.duration)}</span>
              <span>🎯 ${tFocus(mod.focus)}</span>
            </div>
          </div>
        `;
        roadmapHubEl.appendChild(item);
      });
    }

    // --- 8. GOALS & RAW JSON INSPECTOR ---
    if (goalsListEl) {
      goalsListEl.innerHTML = '';
      (profile.learningGoals || []).forEach(g => {
        const badge = document.createElement('span');
        badge.className = 'goal-pill';
        badge.textContent = `🎯 ${I18n.t(`goal_${g}`, {}, g)}`;
        goalsListEl.appendChild(badge);
      });
    }

    if (rawJsonEl) {
      rawJsonEl.textContent = JSON.stringify(profile, null, 2);
    }

    // --- 9. DETAILED ANALYTICS BREAKDOWN ---
    const topicBreakdownEl = document.getElementById('dashboard-detailed-topic-breakdown');
    const quizHistoryEl = document.getElementById('dashboard-detailed-quiz-history');

    if (topicBreakdownEl && profile.assessmentResult && profile.assessmentResult.topicBreakdown) {
      topicBreakdownEl.innerHTML = '';
      profile.assessmentResult.topicBreakdown.forEach(tb => {
        const row = document.createElement('div');
        row.style.cssText = 'display: flex; justify-content: space-between; padding: 0.4rem 0; border-bottom: 1px solid var(--border-subtle); font-size: 0.85rem;';
        row.innerHTML = `
          <span>${tb.topic}</span>
          <strong style="color: ${tb.percentage >= 80 ? 'var(--color-emerald)' : (tb.percentage >= 50 ? 'var(--color-cyan)' : '#fbbf24')};">
            ${tb.correct} / ${tb.total} (${tb.percentage}%)
          </strong>
        `;
        topicBreakdownEl.appendChild(row);
      });
    } else if (topicBreakdownEl) {
      topicBreakdownEl.innerHTML = `<p style="font-size: 0.85rem; color: var(--text-muted);">${I18n.t('analytics_no_topic_breakdown')}</p>`;
    }

    if (quizHistoryEl) {
      quizHistoryEl.innerHTML = '';
      if (history.length > 0) {
        history.forEach(q => {
          const row = document.createElement('div');
          row.style.cssText = 'display: flex; justify-content: space-between; padding: 0.4rem 0; border-bottom: 1px solid var(--border-subtle); font-size: 0.85rem;';
          row.innerHTML = `
            <span>${q.title}</span>
            <strong style="color: var(--color-cyan);">${q.score}/${q.total} (${q.percentage}%)</strong>
          `;
          quizHistoryEl.appendChild(row);
        });
      } else {
        quizHistoryEl.innerHTML = `<p style="font-size: 0.85rem; color: var(--text-muted);">${I18n.t('analytics_no_quiz_logs')}</p>`;
      }
    }
  },

  startLearningSession(customTopic = null) {
    const profile = StorageService.getProfile();
    const result = profile.assessmentResult || {};
    const completedTopics = Array.isArray(profile.completedTopics) ? profile.completedTopics : [];
    const roadmap = Array.isArray(result.generatedRoadmap) ? result.generatedRoadmap : [];
    const getTopicTitle = (topic) => typeof topic === 'string' ? topic : topic && topic.title;
    const inProgressTopics = Array.isArray(profile.inProgressTopics) ? profile.inProgressTopics : [];
    const candidates = customTopic
      ? [customTopic]
      : [
          profile.lastActiveTopic,
          ...inProgressTopics,
          ...roadmap,
          result.recommendedStartingTopic,
          ...(Array.isArray(profile.selectedTopics) ? profile.selectedTopics : [])
        ].map(getTopicTitle);
    const topic = candidates
      .map(getTopicTitle)
      .find(candidate => candidate && !completedTopics.includes(candidate)
        && typeof LessonContent !== 'undefined'
        && LessonContent.getLesson(profile, candidate));
    const lessonTopic = topic || (customTopic || getTopicTitle(profile.lastActiveTopic) || '');

    Onboarding.goToStep('lesson');
    if (!lessonTopic) {
      return this.loadLesson('');
    }

    if (topic) {
      const normalizedInProgress = inProgressTopics
        .map(getTopicTitle)
        .filter(existingTopic => existingTopic && existingTopic !== topic && !completedTopics.includes(existingTopic));
      normalizedInProgress.push(topic);
      StorageService.saveProfile({
        inProgressTopics: normalizedInProgress,
        lastActiveTopic: topic
      });
    }
    return this.loadLesson(lessonTopic);
  },

  loadLesson(topic) {
    this.currentLessonTopic = topic;
    this.currentLesson = null;
    this.lessonPracticeAnswers = [];
    const profile = StorageService.getProfile();
    const title = document.getElementById('lesson-topic-title');
    const pageTitle = document.getElementById('lesson-page-title');
    const context = document.getElementById('lesson-page-context');
    const status = document.getElementById('lesson-page-status');
    const content = document.getElementById('lesson-page-content');
    const completeButton = document.getElementById('lesson-btn-complete');
    const practice = document.getElementById('lesson-practice');
    const completionStatus = document.getElementById('lesson-completion-status');

    if (title) title.textContent = topic || I18n.t('lesson_page_title');
    if (pageTitle) pageTitle.textContent = I18n.t('lesson_page_title');
    if (context) {
      const grade = Number(profile.standard);
      const classLabel = Number.isInteger(grade) && grade > 0
        ? I18n.t('lesson_grade_context', { grade })
        : '';
      context.textContent = [
        profile.selectedSubjectName || profile.courseName || profile.skillName,
        classLabel,
        I18n.t('lesson_daily_time', { time: profile.dailyTime || '30m' })
      ].filter(Boolean).join(' · ');
    }
    if (status) status.textContent = I18n.t('lesson_loading');
    if (content) content.textContent = '';
    if (practice) practice.classList.add('hidden');
    if (completionStatus) {
      completionStatus.classList.add('hidden');
      completionStatus.textContent = '';
    }
    if (completeButton) completeButton.disabled = true;

    if (!topic || !profile.selectedSubjectId && !profile.selectedSubjectName && !profile.courseName && !profile.skillName) {
      if (status) status.textContent = I18n.t('lesson_select_subject');
      return;
    }

    if (typeof LessonContent === 'undefined') {
      if (status) status.textContent = I18n.t('lesson_content_unavailable');
      console.error('Offline lesson library is unavailable.');
      return;
    }
    const lesson = LessonContent.getLesson(profile, topic);
    const validLesson = lesson
      && Array.isArray(lesson.objectives) && lesson.objectives.length > 0
      && typeof lesson.introduction === 'string' && lesson.introduction.trim()
      && Array.isArray(lesson.steps) && lesson.steps.length > 0
      && Array.isArray(lesson.concepts) && lesson.concepts.length > 0
      && Array.isArray(lesson.examples) && lesson.examples.length >= 2
      && typeof lesson.application === 'string' && lesson.application.trim()
      && Array.isArray(lesson.summary) && lesson.summary.length > 0
      && Array.isArray(lesson.practice) && lesson.practice.length > 0
      && lesson.practice.every(question => Array.isArray(question.options)
        && question.options.length > 1
        && Number.isInteger(question.answerIndex)
        && question.answerIndex >= 0 && question.answerIndex < question.options.length);
    if (!validLesson) {
      if (status) status.textContent = I18n.t('lesson_content_unavailable');
      if (lesson) console.error('Offline lesson content is malformed for topic:', topic);
      return;
    }

    this.currentLesson = lesson;
    this.currentLessonTopic = topic;
    if (title) title.textContent = lesson.title;
    if (pageTitle) pageTitle.textContent = lesson.title;
    this.renderLessonContent(lesson, content);
    if (status) status.textContent = I18n.t('lesson_ready');
    this.renderLessonPractice(lesson);
  },

  renderLessonContent(lesson, container) {
    if (!container) return;
    container.replaceChildren();
    const addSection = (headingKey, className = '') => {
      const section = document.createElement('section');
      section.className = `lesson-section ${className}`.trim();
      const heading = document.createElement('h3');
      heading.textContent = I18n.t(headingKey);
      section.appendChild(heading);
      container.appendChild(section);
      return section;
    };
    const addList = (parent, items, ordered = false) => {
      const list = document.createElement(ordered ? 'ol' : 'ul');
      items.forEach(item => {
        const li = document.createElement('li');
        li.textContent = item;
        list.appendChild(li);
      });
      parent.appendChild(list);
    };

    const objectives = addSection('lesson_objectives');
    addList(objectives, lesson.objectives);

    const introduction = addSection('lesson_introduction');
    const introductionText = document.createElement('p');
    introductionText.textContent = lesson.introduction;
    introduction.appendChild(introductionText);

    const explanation = addSection('lesson_explanation');
    const explanationList = document.createElement('ol');
    lesson.steps.forEach(step => {
      const item = document.createElement('li');
      const heading = document.createElement('strong');
      heading.textContent = step.title;
      const text = document.createElement('p');
      text.textContent = step.explanation;
      item.append(heading, text);
      explanationList.appendChild(item);
    });
    explanation.appendChild(explanationList);

    const concepts = addSection('lesson_key_concepts');
    const definitions = document.createElement('dl');
    lesson.concepts.forEach(concept => {
      const term = document.createElement('dt');
      term.textContent = concept.term;
      const definition = document.createElement('dd');
      definition.textContent = concept.definition;
      definitions.append(term, definition);
    });
    concepts.appendChild(definitions);

    const examples = addSection('lesson_worked_examples');
    lesson.examples.forEach(example => {
      const card = document.createElement('article');
      card.className = 'lesson-example';
      const heading = document.createElement('h4');
      heading.textContent = example.title;
      const problem = document.createElement('p');
      problem.className = 'lesson-example-problem';
      problem.textContent = example.problem;
      const solution = document.createElement('ol');
      example.steps.forEach(step => {
        const item = document.createElement('li');
        item.textContent = step;
        solution.appendChild(item);
      });
      const answer = document.createElement('p');
      answer.className = 'lesson-example-answer';
      answer.textContent = example.answer;
      card.append(heading, problem, solution, answer);
      examples.appendChild(card);
    });

    const application = addSection('lesson_real_world');
    const applicationText = document.createElement('p');
    applicationText.textContent = lesson.application;
    application.appendChild(applicationText);

    const summary = addSection('lesson_summary');
    addList(summary, lesson.summary);
  },

  renderLessonPractice(lesson) {
    const section = document.getElementById('lesson-practice');
    const container = document.getElementById('lesson-practice-questions');
    const progress = document.getElementById('lesson-practice-progress');
    const completeButton = document.getElementById('lesson-btn-complete');
    if (!section || !container || !progress || !completeButton) return;

    container.replaceChildren();
    section.classList.remove('hidden');
    this.lessonPracticeAnswers = lesson.practice.map(() => false);
    progress.textContent = I18n.t('lesson_practice_progress', { complete: 0, total: lesson.practice.length });
    completeButton.disabled = true;

    lesson.practice.forEach((question, questionIndex) => {
      const form = document.createElement('form');
      form.className = 'lesson-question';
      const legend = document.createElement('h3');
      legend.textContent = `${questionIndex + 1}. ${question.question}`;
      form.appendChild(legend);
      question.options.forEach((option, optionIndex) => {
        const label = document.createElement('label');
        label.className = 'lesson-answer-option';
        const input = document.createElement('input');
        input.type = 'radio';
        input.name = `lesson-answer-${questionIndex}`;
        input.value = String(optionIndex);
        const text = document.createElement('span');
        text.textContent = option;
        label.append(input, text);
        form.appendChild(label);
      });

      const checkButton = document.createElement('button');
      checkButton.type = 'submit';
      checkButton.className = 'btn btn-secondary btn-sm';
      checkButton.textContent = I18n.t('lesson_check_answer');
      const feedback = document.createElement('p');
      feedback.className = 'lesson-answer-feedback';
      feedback.setAttribute('aria-live', 'polite');
      form.append(checkButton, feedback);
      form.addEventListener('change', () => {
        if (!this.lessonPracticeAnswers[questionIndex]) return;
        this.lessonPracticeAnswers[questionIndex] = false;
        const correctCount = this.lessonPracticeAnswers.filter(Boolean).length;
        progress.textContent = I18n.t('lesson_practice_progress', { complete: correctCount, total: lesson.practice.length });
        completeButton.disabled = true;
        const completionStatus = document.getElementById('lesson-completion-status');
        if (completionStatus) {
          completionStatus.classList.add('hidden');
          completionStatus.textContent = '';
        }
      });
      form.addEventListener('submit', event => {
        event.preventDefault();
        const selected = form.querySelector('input[type="radio"]:checked');
        if (!selected) {
          feedback.className = 'lesson-answer-feedback is-error';
          feedback.textContent = I18n.t('lesson_answer_required');
          return;
        }
        const isCorrect = Number(selected.value) === question.answerIndex;
        this.lessonPracticeAnswers[questionIndex] = isCorrect;
        feedback.className = `lesson-answer-feedback ${isCorrect ? 'is-correct' : 'is-error'}`;
        feedback.textContent = `${I18n.t(isCorrect ? 'lesson_answer_correct' : 'lesson_answer_incorrect')} ${question.feedback}`;
        const correctCount = this.lessonPracticeAnswers.filter(Boolean).length;
        progress.textContent = I18n.t('lesson_practice_progress', { complete: correctCount, total: lesson.practice.length });
        completeButton.disabled = correctCount !== lesson.practice.length;
        if (!completeButton.disabled) {
          const completionStatus = document.getElementById('lesson-completion-status');
          if (completionStatus) {
            completionStatus.classList.remove('hidden');
            completionStatus.textContent = I18n.t('lesson_practice_passed');
          }
        }
      });
      container.appendChild(form);
    });
  },

  completeCurrentLesson() {
    if (!this.currentLessonTopic || !this.currentLesson
      || this.lessonPracticeAnswers.some(answer => !answer)) return;
    const savedProfile = StorageService.markTopicCompleted(this.currentLessonTopic);
    if (!savedProfile) {
      const status = document.getElementById('lesson-completion-status');
      if (status) {
        status.classList.remove('hidden');
        status.textContent = I18n.t('lesson_complete_save_error');
      }
      return;
    }
    const completionStatus = document.getElementById('lesson-completion-status');
    if (completionStatus) {
      completionStatus.classList.remove('hidden');
      completionStatus.textContent = I18n.t('lesson_complete_saved');
    }
    this.currentLessonTopic = null;
    this.currentLesson = null;
    Onboarding.goToStep('hub');
  },

  /**
   * Dedicated Progress Analytics Dashboard Renderer
   */
  renderProgressDashboard(profile = null) {
    if (!profile) profile = StorageService.getProfile();

    // 1. Overall stats
    const lvlEl = document.getElementById('progress-level');
    const lvlBadgeEl = document.getElementById('progress-level-badge');
    const xpEl = document.getElementById('progress-xp');
    const pctEl = document.getElementById('progress-percentage');
    const barEl = document.getElementById('progress-bar-fill');
    const compEl = document.getElementById('progress-completed');
    const inProgEl = document.getElementById('progress-inprogress');
    const sessEl = document.getElementById('progress-sessions');
    const timeEl = document.getElementById('progress-time');
    const curStreakEl = document.getElementById('progress-streak-current');
    const maxStreakEl = document.getElementById('progress-streak-longest');

    const res = profile.assessmentResult;
    const completedTopics = Array.isArray(profile.completedTopics) ? profile.completedTopics : [];
    const inProgressTopics = Array.isArray(profile.inProgressTopics) ? profile.inProgressTopics : [];
    const quizHistory = Array.isArray(profile.quizHistory) ? profile.quizHistory : [];
    const mistakesLog = Array.isArray(profile.mistakesLog) ? profile.mistakesLog : [];
    const roadmap = (res && res.moduleRoadmap) ? res.moduleRoadmap : [];

    const totalTopicsCount = Math.max(completedTopics.length + inProgressTopics.length + roadmap.length, 1);
    const overallPct = Math.min(100, Math.round((completedTopics.length / totalTopicsCount) * 100));

    if (lvlEl) lvlEl.textContent = `Level ${profile.userLevel || 1}`;
    if (lvlBadgeEl) {
      lvlBadgeEl.textContent = res ? (res.calibratedDifficulty || 'Calibrated Baseline') : 'Active Journey';
    }
    if (xpEl) xpEl.textContent = `${profile.xpPoints || 120} XP`;
    if (pctEl) pctEl.textContent = `${overallPct}%`;
    if (barEl) barEl.style.width = `${overallPct}%`;
    if (compEl) compEl.textContent = `Completed: ${completedTopics.length}`;
    if (inProgEl) inProgEl.textContent = `In Progress: ${inProgressTopics.length}`;
    if (sessEl) sessEl.textContent = String(completedTopics.length + quizHistory.length);
    if (timeEl) timeEl.textContent = `${profile.dailyTime || '30m'} / day`;
    if (curStreakEl) curStreakEl.textContent = `🔥 ${profile.streakDays || 3} days`;
    if (maxStreakEl) maxStreakEl.textContent = `Best: ${Math.max(profile.streakDays || 3, 5)} days`;

    // 2. Course & Topic Progress
    const subjectsGrid = document.getElementById('progress-subjects-grid');
    if (subjectsGrid) {
      subjectsGrid.innerHTML = '';
      const activeSubject = profile.selectedSubjectName || 'Core Curriculum';
      
      const card = document.createElement('div');
      card.style.cssText = 'padding: 1.25rem; background: var(--bg-surface); border-radius: var(--radius-lg); border: 1px solid var(--border-medium);';
      card.innerHTML = `
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
          <h4 style="font-size: 1rem; font-weight: 700; color: var(--text-primary);">${activeSubject}</h4>
          <span class="badge-popular" style="font-size: 0.75rem;">${overallPct}% Mastered</span>
        </div>
        <div style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 0.75rem;">
          Topics tracked: ${completedTopics.length} completed, ${inProgressTopics.length} in progress
        </div>
        <div style="display: flex; flex-wrap: wrap; gap: 0.4rem;">
          ${completedTopics.map(t => `<span class="badge-completed" style="font-size: 0.75rem;">✓ ${typeof t === 'string' ? t : t.title}</span>`).join('')}
          ${inProgressTopics.map(t => `<span class="badge-in-progress" style="font-size: 0.75rem;">⚡ ${typeof t === 'string' ? t : t.title}</span>`).join('')}
          ${completedTopics.length === 0 && inProgressTopics.length === 0 ? `<span style="font-size: 0.8rem; color: var(--text-muted);">Start lessons to track competency breakdown</span>` : ''}
        </div>
      `;
      subjectsGrid.appendChild(card);
    }

    // 3. Quiz & Practice Performance
    const quizAttEl = document.getElementById('quiz-attempted');
    const quizAvgEl = document.getElementById('quiz-average');
    const quizHighEl = document.getElementById('quiz-highest');
    const subjPerfGrid = document.getElementById('subject-performance-grid');

    if (quizAttEl) quizAttEl.textContent = String(quizHistory.length);
    if (quizHistory.length > 0) {
      const avg = Math.round(quizHistory.reduce((acc, q) => acc + (q.percentage || 0), 0) / quizHistory.length);
      const high = Math.max(...quizHistory.map(q => q.percentage || 0));
      if (quizAvgEl) quizAvgEl.textContent = `${avg}%`;
      if (quizHighEl) quizHighEl.textContent = `${high}%`;
    } else {
      if (quizAvgEl) quizAvgEl.textContent = '0%';
      if (quizHighEl) quizHighEl.textContent = '0%';
    }

    if (subjPerfGrid) {
      subjPerfGrid.innerHTML = '';
      if (quizHistory.length > 0) {
        quizHistory.slice(0, 4).forEach(q => {
          const item = document.createElement('div');
          item.style.cssText = 'padding: 0.85rem; background: var(--bg-surface); border-radius: var(--radius-md); border: 1px solid var(--border-subtle); display: flex; justify-content: space-between; align-items: center;';
          item.innerHTML = `
            <div>
              <div style="font-size: 0.85rem; font-weight: 600; color: var(--text-primary);">${q.title}</div>
              <div style="font-size: 0.75rem; color: var(--text-muted);">${new Date(q.timestamp).toLocaleDateString()}</div>
            </div>
            <strong style="color: ${(q.percentage || 0) >= 70 ? 'var(--color-emerald-light)' : 'var(--color-cyan-light)'}; font-size: 0.95rem;">${q.percentage || 0}%</strong>
          `;
          subjPerfGrid.appendChild(item);
        });
      } else {
        subjPerfGrid.innerHTML = `<p style="font-size: 0.85rem; color: var(--text-muted); grid-column: 1/-1;">No practice quizzes attempted yet. Use Practice Quiz in the sidebar to test your knowledge.</p>`;
      }
    }

    // 4. Learning Activity & Streak Logs
    const actList = document.getElementById('activity-history-list');
    const streakList = document.getElementById('streak-history-list');

    if (actList) {
      actList.innerHTML = '';
      const logs = [];
      if (completedTopics.length > 0) {
        completedTopics.forEach(t => logs.push({ icon: '✅', text: `Completed topic: ${typeof t === 'string' ? t : t.title}` }));
      }
      if (quizHistory.length > 0) {
        quizHistory.forEach(q => logs.push({ icon: '⚡', text: `Quiz completed: ${q.title} (${q.score}/${q.total})` }));
      }
      if (profile.savedNotes && profile.savedNotes.length > 0) {
        profile.savedNotes.forEach(n => logs.push({ icon: '📝', text: `Note saved: ${n.title}` }));
      }
      if (logs.length > 0) {
        logs.slice(0, 6).forEach(l => {
          const row = document.createElement('div');
          row.style.cssText = 'display: flex; align-items: center; gap: 0.6rem; padding: 0.45rem 0; border-bottom: 1px solid var(--border-subtle); font-size: 0.85rem; color: var(--text-secondary);';
          row.innerHTML = `<span>${l.icon}</span><span>${l.text}</span>`;
          actList.appendChild(row);
        });
      } else {
        actList.innerHTML = `<p style="font-size: 0.85rem; color: var(--text-muted);">No activity recorded yet. Start your first lesson or quiz to see your progress here.</p>`;
      }
    }

    if (streakList) {
      streakList.innerHTML = '';
      const days = profile.streakDays || 3;
      for (let i = 0; i < Math.min(days, 5); i++) {
        const item = document.createElement('div');
        item.style.cssText = 'display: flex; justify-content: space-between; align-items: center; padding: 0.45rem 0; border-bottom: 1px solid var(--border-subtle); font-size: 0.85rem;';
        item.innerHTML = `
          <span style="color: var(--text-secondary);">Day ${days - i} Consistency</span>
          <span style="color: #D97706; font-weight: 600;">🔥 Completed</span>
        `;
        streakList.appendChild(item);
      }
    }

    // 5. Strengths & Areas to Improve
    const strongList = document.getElementById('strong-topics-list');
    const areasList = document.getElementById('areas-improve-list');

    if (strongList) {
      strongList.innerHTML = '';
      const strengths = (res && res.strengths && res.strengths.length > 0)
        ? res.strengths
        : (completedTopics.length > 0 ? completedTopics.slice(0, 3) : ['Foundations', 'Core Concepts']);
      strengths.forEach(s => {
        const span = document.createElement('span');
        span.className = 'pill-strength';
        span.textContent = `✓ ${typeof s === 'string' ? s : s.title}`;
        strongList.appendChild(span);
      });
    }

    if (areasList) {
      areasList.innerHTML = '';
      const revisions = (res && res.revisionNeeded && res.revisionNeeded.length > 0)
        ? res.revisionNeeded
        : (mistakesLog.length > 0 ? mistakesLog.map(m => m.topic).slice(0, 3) : ['Application Problems', 'Speed & Review']);
      revisions.forEach(r => {
        const span = document.createElement('span');
        span.className = 'pill-revision';
        span.textContent = `▲ ${typeof r === 'string' ? r : r.title}`;
        areasList.appendChild(span);
      });
    }

    // 6. Achievements & Badges
    const badgesList = document.getElementById('badges-earned-list');
    const xpMilestones = document.getElementById('xp-milestones-list');

    if (badgesList) {
      badgesList.innerHTML = '';
      const badges = (profile.badges && profile.badges.length > 0) ? profile.badges : ['welcome_badge', 'first_quiz'];
      badges.forEach(b => {
        const badgeEl = document.createElement('div');
        badgeEl.style.cssText = 'display: flex; align-items: center; gap: 0.4rem; padding: 0.35rem 0.75rem; background: var(--bg-surface); border-radius: var(--radius-full); border: 1px solid var(--border-medium); font-size: 0.8rem; font-weight: 600; color: var(--text-primary);';
        const label = b.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
        badgeEl.innerHTML = `<span>🏆</span><span>${label}</span>`;
        badgesList.appendChild(badgeEl);
      });
    }

    if (xpMilestones) {
      xpMilestones.innerHTML = '';
      const currentXP = profile.xpPoints || 120;
      const milestones = [
        { name: 'Bronze Scholar', target: 100 },
        { name: 'Silver Achiever', target: 250 },
        { name: 'Gold Master', target: 500 }
      ];
      milestones.forEach(m => {
        const mEl = document.createElement('div');
        const pct = Math.min(100, Math.round((currentXP / m.target) * 100));
        mEl.innerHTML = `
          <div style="display: flex; justify-content: space-between; font-size: 0.8rem; margin-bottom: 0.25rem;">
            <span style="font-weight: 600; color: var(--text-primary);">${m.name} (${m.target} XP)</span>
            <span style="color: var(--text-muted);">${currentXP}/${m.target} XP</span>
          </div>
          <div style="height: 6px; width: 100%; background: var(--bg-surface); border-radius: var(--radius-full); overflow: hidden;">
            <div style="height: 100%; width: ${pct}%; background: ${pct >= 100 ? 'var(--color-emerald)' : 'var(--color-indigo)'};"></div>
          </div>
        `;
        xpMilestones.appendChild(mEl);
      });
    }
  }
};

if (typeof module !== 'undefined' && module.exports) {
  module.exports = { LearnerProfile };
}
