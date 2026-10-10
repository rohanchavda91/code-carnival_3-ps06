/**
 * Personalization & Curriculum Synthesis Engine
 * Demonstrates adaptive intelligence: generates distinct roadmaps and tailored learning tracks.
 */
const Personalization = {
  init() {
    this.bindEvents();
    window.addEventListener('synapse:language_changed', () => {
      const profile = StorageService.getProfile();
      if (profile && profile.assessmentResult) {
        this.renderResult(profile);
      }
    });
  },

  bindEvents() {
    const startLearningBtn = document.getElementById('btn-start-learning-path');
    if (startLearningBtn) {
      startLearningBtn.addEventListener('click', () => {
        Onboarding.goToStep('hub');
      });
    }

    const retakeBtn = document.getElementById('btn-retake-assessment-action');
    if (retakeBtn) {
      retakeBtn.addEventListener('click', () => {
        const profile = StorageService.getProfile();
        Assessment.startForProfile(profile);
        Onboarding.goToStep('assessment');
      });
    }
  },

  /**
   * Synthesizes a custom sequence of learning modules based on user path and diagnostics
   */
  generateRoadmap(profile, level, focusAreas = []) {
    const type = profile.learnerType;
    const subjectName = (profile.selectedSubjectName || '').toLowerCase();
    const style = profile.learningStyle || 'step_by_step';

    // Tailored Roadmap for School Student (Class 8 Math vs Class 10 Science vs Class 11 Physics)
    if (type === 'school') {
      const grade = profile.standard || 8;
      if (subjectName.includes('math')) {
        return [
          {
            id: 1,
            title: `Module 1: Number Systems & Rational Operations`,
            focus: 'Foundational Numbers',
            duration: '5 Lessons • 2h Total',
            status: 'Recommended Start',
            description: `Visual mastery of rational numbers, fractions, and order of operations tailored for Grade ${grade}.`,
            icon: '🔢'
          },
          {
            id: 2,
            title: `Module 2: Algebraic Thinking & Single Variable Equations`,
            focus: focusAreas.includes('Algebra Basics') ? 'High Priority Growth' : 'Core Curriculum',
            duration: '7 Lessons • 3h Total',
            status: 'Up Next',
            description: `Transposition methods, word problem modeling, and balanced equations with step-by-step interactive proofs.`,
            icon: '📐'
          },
          {
            id: 3,
            title: `Module 3: Geometry, Angles & Polygons`,
            focus: 'Spatial Reasoning',
            duration: '6 Lessons • 2.5h Total',
            status: 'Locked',
            description: `Properties of quadrilaterals, angle sum properties, and practical geometric constructions.`,
            icon: '📐'
          },
          {
            id: 4,
            title: `Module 4: Mensuration & Surface Areas`,
            focus: 'Applied Math',
            duration: '8 Lessons • 3.5h Total',
            status: 'Locked',
            description: `Real-world measurement of perimeters, areas of 2D shapes, and volumes of solid figures.`,
            icon: '📏'
          }
        ];
      } else if (subjectName.includes('phy') || subjectName.includes('sci')) {
        return [
          {
            id: 1,
            title: `Module 1: Physical Quantities, Units & Vector Kinematics`,
            focus: 'Conceptual Vectors',
            duration: '6 Lessons • 2.5h Total',
            status: 'Recommended Start',
            description: `Dimensional analysis, 1D motion equations, and vector decomposition in 2D space.`,
            icon: '⚛️'
          },
          {
            id: 2,
            title: `Module 2: Newton’s Laws & Friction Dynamics`,
            focus: 'Mechanics Core',
            duration: '8 Lessons • 3.5h Total',
            status: 'Up Next',
            description: `Free-body diagrams, conservation of linear momentum, and static vs kinetic friction.`,
            icon: '⚙️'
          },
          {
            id: 3,
            title: `Module 3: Work, Kinetic Energy & Conservation Laws`,
            focus: 'Energy Transformations',
            duration: '5 Lessons • 2h Total',
            status: 'Locked',
            description: `Work-energy theorem, conservative forces, and potential energy functions.`,
            icon: '⚡'
          },
          {
            id: 4,
            title: `Module 4: Rotational Motion & Gravitation`,
            focus: 'Advanced Physics',
            duration: '7 Lessons • 3h Total',
            status: 'Locked',
            description: `Torque, moment of inertia, Kepler’s planetary laws, and gravitational potential.`,
            icon: '🪐'
          }
        ];
      }
    }

    // Tailored Roadmap for College / BCA / Web Tech
    if (type === 'college' || subjectName.includes('javascript') || subjectName.includes('bca') || subjectName.includes('web')) {
      return [
        {
          id: 1,
          title: `Module 1: Execution Context, Scope & Closures`,
          focus: 'Core Engine Dynamics',
          duration: '6 Lessons • 2.5h Total',
          status: 'Recommended Start',
          description: `Master let vs const, lexical environments, the call stack, and real-world closure patterns.`,
          icon: '⚡'
        },
        {
          id: 2,
          title: `Module 2: Functional JavaScript & Array Methods`,
          focus: 'Data Manipulation',
          duration: '7 Lessons • 3h Total',
          status: 'Up Next',
          description: `Deep dive into map, filter, reduce, pure functions, object destructuring, and immutable patterns.`,
          icon: '🔄'
        },
        {
          id: 3,
          title: `Module 3: The DOM, Events & Performance`,
          focus: 'Interactive UI',
          duration: '6 Lessons • 2.5h Total',
          status: 'Locked',
          description: `Event bubbling and delegation, DOM mutation efficiency, and building responsive client apps.`,
          icon: '🖥️'
        },
        {
          id: 4,
          title: `Module 4: Asynchronous JavaScript & The Event Loop`,
          focus: 'Async Programming',
          duration: '8 Lessons • 4h Total',
          status: 'Locked',
          description: `Microtasks vs macrotasks, Promises, async/await error boundaries, and REST API integration.`,
          icon: '🚀'
        }
      ];
    }

    // Tailored Roadmap for Spoken English & Language Learners
    if (subjectName.includes('english') || type === 'skills' && subjectName.includes('language')) {
      return [
        {
          id: 1,
          title: `Module 1: Conversational Foundations & Confidence`,
          focus: 'Spoken Fluency',
          duration: '5 Lessons • 2h Total',
          status: 'Recommended Start',
          description: `Overcome speaking hesitation, master common idioms, and build natural greeting dialogues.`,
          icon: '🗣️'
        },
        {
          id: 2,
          title: `Module 2: Tenses, Modals & Sentence Nuances`,
          focus: 'Structural Grammar',
          duration: '6 Lessons • 2.5h Total',
          status: 'Up Next',
          description: `Past vs present perfect distinction, conditional statements, and passive voice in daily context.`,
          icon: '📖'
        },
        {
          id: 3,
          title: `Module 3: Professional Business Communication`,
          focus: 'Career Mastery',
          duration: '7 Lessons • 3h Total',
          status: 'Locked',
          description: `Crafting sharp emails, leading video meetings, handling Q&A, and concise executive reporting.`,
          icon: '💼'
        },
        {
          id: 4,
          title: `Module 4: Spontaneous Impromptu Speaking & Pronunciation`,
          focus: 'Advanced Articulation',
          duration: '6 Lessons • 2.5h Total',
          status: 'Locked',
          description: `Vocal modulation, intonation patterns, eliminating filler words, and storytelling frameworks.`,
          icon: '🎙️'
        }
      ];
    }

    // Default Dynamic Roadmap (Synthesized from Selected Topics)
    const customTopics = profile.selectedTopics && profile.selectedTopics.length > 0
      ? profile.selectedTopics
      : ['Fundamental Architecture', 'Applied Principles', 'Practical Case Studies', 'System Synthesis'];

    return customTopics.slice(0, 4).map((topic, i) => ({
      id: i + 1,
      title: `Module ${i + 1}: ${topic}`,
      focus: (i === 0) ? 'Foundation & Diagnostics' : (i === 1) ? 'Core Competency' : 'Advanced Application',
      duration: `${4 + i} Lessons • ${2 + i * 0.5}h Total`,
      status: (i === 0) ? 'Recommended Start' : (i === 1) ? 'Up Next' : 'Locked',
      description: `Targeted cognitive drills in ${topic} matching your ${style.replace('_', ' ')} learning style.`,
      icon: ['🎯', '💡', '🛠️', '🏆'][i % 4]
    }));
  },

  /**
   * Renders the Assessment Result UI
   */
  renderResult(profile) {
    const res = profile.assessmentResult;
    if (!res) return;

    // Badges & Scores
    const levelBadge = document.getElementById('result-level-badge');
    const scoreBadge = document.getElementById('result-score-badge');
    const answerSummary = document.getElementById('result-answer-summary');
    const strongList = document.getElementById('result-strong-areas-list');
    const focusList = document.getElementById('result-focus-areas-list');
    const recTopic = document.getElementById('result-rec-topic');
    const recDiff = document.getElementById('result-rec-difficulty');
    const recTime = document.getElementById('result-rec-time');
    const breakdownGrid = document.getElementById('result-topic-breakdown-grid');
    const roadmapContainer = document.getElementById('result-roadmap-container');

    // Localized label maps for result chrome rendered below
    const calibLabels = {
      'Fundamentals': I18n.t('calib_fundamentals'),
      'High Pace / Mastery': I18n.t('calib_high_pace_mastery'),
      'Standard Intermediate': I18n.t('calib_standard_intermediate'),
      'Foundation Reinforcement': I18n.t('calib_foundation_reinforcement'),
      'Guided Essentials': I18n.t('calib_guided_essentials'),
      'Standard': I18n.t('calib_standard')
    };
    const topicStatusLabels = {
      'Strong': I18n.t('topic_status_strong'),
      'Developing': I18n.t('topic_status_developing'),
      'Focus Needed': I18n.t('topic_status_focus_needed')
    };
    const moduleStatusLabels = {
      'Recommended Start': I18n.t('path_status_start'),
      'Up Next': I18n.t('path_status_next'),
      'Locked': I18n.t('path_status_locked')
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

    if (levelBadge) {
      levelBadge.textContent = I18n.t(`level_${String(res.level || 'intermediate').toLowerCase()}`);
      levelBadge.className = `level-badge level-${res.level.toLowerCase()}`;
    }

    if (scoreBadge) {
      scoreBadge.textContent = I18n.t('result_score_line', { pct: res.percentage, score: res.score, total: res.total });
    }
    if (answerSummary) {
      answerSummary.textContent = I18n.t('result_answers_correct', { score: res.score, total: res.total });
    }

    if (recTopic) recTopic.textContent = res.recommendedStartingTopic;
    if (recDiff) recDiff.textContent = calibLabels[res.calibratedDifficulty] || res.calibratedDifficulty;
    if (recTime) {
      const timeKey = `time_${profile.dailyTime || '30m'}`;
      const translatedTime = I18n.t(timeKey);
      recTime.textContent = (translatedTime === timeKey)
        ? (profile.dailyTime || I18n.t('label_daily_time_default'))
        : translatedTime;
    }

    // Strong areas pills
    if (strongList) {
      strongList.innerHTML = '';
      (res.strongAreas || []).forEach(area => {
        const span = document.createElement('span');
        span.className = 'area-badge strong';
        span.innerHTML = `✓ ${area}`;
        strongList.appendChild(span);
      });
    }

    // Focus areas pills
    if (focusList) {
      focusList.innerHTML = '';
      (res.focusAreas || []).forEach(area => {
        const span = document.createElement('span');
        span.className = 'area-badge focus';
        span.innerHTML = `▲ ${area}`;
        focusList.appendChild(span);
      });
    }

    // Topic breakdown progress meters
    if (breakdownGrid) {
      breakdownGrid.innerHTML = '';
      (res.topicBreakdown || []).forEach(item => {
        const itemDiv = document.createElement('div');
        itemDiv.className = 'topic-meter-item';
        itemDiv.innerHTML = `
          <div class="topic-meter-header">
            <span class="topic-meter-name">${item.topic}</span>
            <span class="topic-meter-badge status-${item.status.toLowerCase().replace(' ', '-')}">${topicStatusLabels[item.status] || item.status} (${item.correct}/${item.total})</span>
          </div>
          <div class="topic-meter-track">
            <div class="topic-meter-fill fill-${item.status.toLowerCase().replace(' ', '-')}" style="width: ${item.percentage}%;"></div>
          </div>
        `;
        breakdownGrid.appendChild(itemDiv);
      });
    }

    // Roadmap items
    if (roadmapContainer) {
      roadmapContainer.innerHTML = '';
      (res.generatedRoadmap || []).forEach((mod, idx) => {
        const modCard = document.createElement('div');
        modCard.className = `roadmap-module-card ${mod.status === 'Recommended Start' ? 'featured' : ''}`;
        modCard.innerHTML = `
          <div class="mod-card-header">
            <span class="mod-icon">${mod.icon}</span>
            <div class="mod-meta">
              <span class="mod-status-tag ${mod.status === 'Recommended Start' ? 'start-tag' : ''}">${moduleStatusLabels[mod.status] || mod.status}</span>
              <span class="mod-duration">${tDuration(mod.duration)}</span>
            </div>
          </div>
          <h4 class="mod-title">${mod.title}</h4>
          <p class="mod-desc">${mod.description}</p>
          <div class="mod-footer">
            <span class="mod-focus-label">🎯 ${tFocus(mod.focus)}</span>
          </div>
        `;
        roadmapContainer.appendChild(modCard);
      });
    }
  }
};

if (typeof module !== 'undefined' && module.exports) {
  module.exports = { Personalization };
}
