/**
 * Learning Level Assessment Engine
 * Standalone offline assessment tool for knowledge evaluation and personalized recommendations
 */
const LevelAssessment = {
  currentQuestions: [],
  currentIndex: 0,
  userAnswers: {}, // questionId -> selectedOptionIndex
  currentSubjectName: '',
  startTime: null,
  lastResult: null,

  init() {
    this.bindEvents();
    window.addEventListener('synapse:language_changed', () => this.refreshOnLanguageChange());
  },

  bindEvents() {
    // Intro screen
    const btnStart = document.getElementById('btn-start-level-assessment');
    const btnCancelIntro = document.getElementById('btn-cancel-level-assessment-intro');
    
    if (btnStart) btnStart.addEventListener('click', () => this.startAssessment());
    if (btnCancelIntro) btnCancelIntro.addEventListener('click', () => this.closeAssessment());

    // Quiz screen
    const btnNext = document.getElementById('btn-level-assessment-next');
    const btnPrev = document.getElementById('btn-level-assessment-prev');
    const btnSubmit = document.getElementById('btn-level-assessment-submit');

    if (btnNext) btnNext.addEventListener('click', () => this.nextQuestion());
    if (btnPrev) btnPrev.addEventListener('click', () => this.previousQuestion());
    if (btnSubmit) btnSubmit.addEventListener('click', () => this.submitAssessment());

    // Results screen
    const btnRetake = document.getElementById('btn-retake-level-assessment');
    const btnBackHub = document.getElementById('btn-back-to-hub-from-assessment');

    if (btnRetake) btnRetake.addEventListener('click', () => this.restartAssessment());
    if (btnBackHub) btnBackHub.addEventListener('click', () => this.closeAssessment());

    // Hub button to open level assessment
    const hubBtnLevelAssessment = document.getElementById('hub-btn-level-assessment');
    if (hubBtnLevelAssessment) {
      hubBtnLevelAssessment.addEventListener('click', () => this.openAssessment());
    }
  },

  openAssessment() {
    const profile = StorageService.getProfile();
    this.currentSubjectName = profile.selectedSubjectName || I18n.t('level_assessment_default_subject');
    this.showScreen('intro');
  },

  startAssessment() {
    // Guest mode: spend one daily Level Assessment credit.
    if (typeof GuestLimits !== 'undefined' && !GuestLimits.consume('level_assessment')) return;
    this.userAnswers = {};
    this.currentIndex = 0;
    this.startTime = Date.now();

    const profile = StorageService.getProfile();
    const bank = this.selectQuestionBank(profile);
    this.currentQuestions = bank.questions;
    this.currentSubjectName = bank.subjectName;

    if (this.currentQuestions.length === 0) {
      alert(I18n.t('level_assessment_no_questions'));
      this.closeAssessment();
      return;
    }

    // Update question count in intro
    document.getElementById('level-assessment-question-count').textContent = I18n.t('level_assessment_questions_count', { count: this.currentQuestions.length });
    document.getElementById('level-assessment-subject-name').textContent = this.currentSubjectName;

    this.renderCurrentQuestion();
    this.updatePaginationState();
    this.showScreen('quiz');
  },

  selectQuestionBank(profile) {
    if (typeof LEVEL_ASSESSMENT_QUESTIONS === 'undefined') {
      return { subjectName: I18n.t('level_assessment_subject_general'), questions: [] };
    }

    const subjectId = (profile.selectedSubjectId || '').toLowerCase();
    const subjectName = (profile.selectedSubjectName || '').toLowerCase();
    const learnerType = profile.learnerType;

    // School Math
    if (subjectId.includes('math') || subjectName.includes('math')) {
      return LEVEL_ASSESSMENT_QUESTIONS.school_math;
    }

    // Physics or Science
    if (subjectId.includes('phy') || subjectId.includes('sci') || subjectName.includes('physics') || subjectName.includes('science')) {
      return LEVEL_ASSESSMENT_QUESTIONS.physics_science;
    }

    // Accountancy or Commerce
    if (subjectId.includes('acc') || subjectId.includes('commerce') || subjectName.includes('account') || subjectName.includes('commerce')) {
      return LEVEL_ASSESSMENT_QUESTIONS.commerce_accountancy;
    }

    // JavaScript / Web / BCA / Tech
    if (subjectId.includes('js') || subjectId.includes('javascript') || subjectId.includes('web') || subjectId.includes('bca') || subjectId.includes('tech') || subjectName.includes('javascript') || subjectName.includes('web') || subjectName.includes('bca')) {
      return LEVEL_ASSESSMENT_QUESTIONS.javascript_web;
    }

    // Python / Data Science / Tech
    if (subjectId.includes('python') || subjectId.includes('data') || subjectName.includes('python') || subjectName.includes('data science')) {
      return LEVEL_ASSESSMENT_QUESTIONS.python_tech;
    }

    // Spoken English / Language
    if (subjectId.includes('eng') || subjectId.includes('lang') || subjectName.includes('english') || subjectName.includes('language') || subjectName.includes('spoken')) {
      return LEVEL_ASSESSMENT_QUESTIONS.english_language;
    }

    // Fallback to general
    return LEVEL_ASSESSMENT_QUESTIONS.general_diagnostic;
  },

  renderCurrentQuestion() {
    if (!this.currentQuestions || this.currentQuestions.length === 0) return;

    const q = this.currentQuestions[this.currentIndex];
    const total = this.currentQuestions.length;

    // Update labels
    document.getElementById('level-assessment-subject-tag').textContent = this.currentSubjectName;
    document.getElementById('level-assessment-counter').textContent = I18n.t('question_of', { current: this.currentIndex + 1, total: total });
    document.getElementById('level-assessment-topic-tag').textContent = q.topic;

    const diffBadge = document.getElementById('level-assessment-difficulty-badge');
    if (diffBadge) {
      diffBadge.textContent = I18n.t(`difficulty_${q.difficulty}`) || q.difficulty.toUpperCase();
      diffBadge.className = `badge-difficulty diff-${q.difficulty}`;
    }

    // Question text
    document.getElementById('level-assessment-question-text').textContent = q.question;

    // Progress bar
    const pct = Math.round(((this.currentIndex + 1) / total) * 100);
    document.getElementById('level-assessment-progress-bar').style.width = `${pct}%`;

    // Render options
    const optionsContainer = document.getElementById('level-assessment-options-container');
    optionsContainer.innerHTML = '';
    const selectedIndex = this.userAnswers[q.id];

    q.options.forEach((optText, idx) => {
      const optionLabel = document.createElement('label');
      optionLabel.className = 'assessment-option-card';
      if (selectedIndex === idx) {
        optionLabel.classList.add('selected');
      }

      const letters = ['A', 'B', 'C', 'D'];
      optionLabel.innerHTML = `
        <div class="option-letter-badge">${letters[idx]}</div>
        <div class="option-text">${optText}</div>
        <input type="radio" name="level_opt_${q.id}" value="${idx}" class="sr-only" ${selectedIndex === idx ? 'checked' : ''}>
        <div class="option-check-circle"></div>
      `;

      optionLabel.addEventListener('click', () => {
        this.selectAnswer(q.id, idx);
      });

      optionsContainer.appendChild(optionLabel);
    });

    this.renderQuestionDots();
  },

  selectAnswer(questionId, optionIndex) {
    this.userAnswers[questionId] = optionIndex;

    const options = document.querySelectorAll('#level-assessment-options-container .assessment-option-card');
    options.forEach((card, idx) => {
      card.classList.toggle('selected', idx === optionIndex);
    });

    this.updatePaginationState();
    this.renderQuestionDots();
  },

  nextQuestion() {
    if (this.currentIndex < this.currentQuestions.length - 1) {
      this.currentIndex++;
      this.renderCurrentQuestion();
      this.updatePaginationState();
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }
  },

  previousQuestion() {
    if (this.currentIndex > 0) {
      this.currentIndex--;
      this.renderCurrentQuestion();
      this.updatePaginationState();
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }
  },

  renderQuestionDots() {
    const dotsContainer = document.getElementById('level-assessment-dots-nav');
    if (!dotsContainer) return;

    dotsContainer.innerHTML = '';
    this.currentQuestions.forEach((q, idx) => {
      const dot = document.createElement('button');
      dot.type = 'button';
      dot.className = 'dot-nav-item';
      if (idx === this.currentIndex) dot.classList.add('current');
      if (this.userAnswers[q.id] !== undefined) dot.classList.add('answered');

      dot.setAttribute('title', I18n.t('level_assessment_question_n', { n: idx + 1 }));
      dot.addEventListener('click', () => {
        this.currentIndex = idx;
        this.renderCurrentQuestion();
        this.updatePaginationState();
      });

      dotsContainer.appendChild(dot);
    });
  },

  updatePaginationState() {
    const btnNext = document.getElementById('btn-level-assessment-next');
    const btnPrev = document.getElementById('btn-level-assessment-prev');
    const btnSubmit = document.getElementById('btn-level-assessment-submit');
    const total = this.currentQuestions.length;

    if (btnPrev) {
      btnPrev.disabled = (this.currentIndex === 0);
    }

    const isLast = (this.currentIndex === total - 1);
    if (btnNext) {
      btnNext.classList.toggle('hidden', isLast);
    }
    if (btnSubmit) {
      btnSubmit.classList.toggle('hidden', !isLast);
    }
  },

  submitAssessment() {
    // Calculate results
    const result = this.calculateResults();
    
    // Show results screen
    this.showResults(result);
    this.showScreen('results');
    window.scrollTo({ top: 0, behavior: 'smooth' });
  },

  calculateResults() {
    let correctCount = 0;
    const topicScores = {};

    this.currentQuestions.forEach(q => {
      const selected = this.userAnswers[q.id];
      const isCorrect = (selected === q.correctIndex);

      if (!topicScores[q.topic]) {
        topicScores[q.topic] = { total: 0, correct: 0 };
      }
      topicScores[q.topic].total++;

      if (isCorrect) {
        correctCount++;
        topicScores[q.topic].correct++;
      }
    });

    const totalQuestions = this.currentQuestions.length;
    const percentage = Math.round((correctCount / totalQuestions) * 100);

    // Determine level based on percentage
    let level = 'Beginner';
    if (percentage >= 85) {
      level = 'Advanced';
    } else if (percentage >= 60) {
      level = 'Intermediate';
    } else if (percentage >= 40) {
      level = 'Developing';
    } else {
      level = 'Beginner';
    }

    // Topic breakdown
    const topicBreakdown = [];
    const strongAreas = [];
    const focusAreas = [];

    Object.keys(topicScores).forEach(topic => {
      const data = topicScores[topic];
      const topicPct = data.total > 0 ? (data.correct / data.total) : 0;

      let status = 'Developing';
      if (topicPct >= 0.8) {
        status = 'Strong';
        strongAreas.push(topic);
      } else if (topicPct >= 0.5) {
        status = 'Developing';
      } else {
        status = 'Needs Practice';
        focusAreas.push(topic);
      }

      topicBreakdown.push({
        topic: topic,
        correct: data.correct,
        total: data.total,
        percentage: Math.round(topicPct * 100),
        status: status
      });
    });

    // Fallbacks
    if (strongAreas.length === 0 && topicBreakdown.length > 0) {
      strongAreas.push(topicBreakdown[0].topic);
    }
    if (focusAreas.length === 0 && topicBreakdown.length > 1) {
      focusAreas.push(topicBreakdown[topicBreakdown.length - 1].topic);
    }

    return {
      score: correctCount,
      total: totalQuestions,
      percentage: percentage,
      level: level,
      topicBreakdown: topicBreakdown,
      strongAreas: strongAreas,
      focusAreas: focusAreas,
      timeSeconds: Math.round((Date.now() - this.startTime) / 1000),
      completedAt: new Date().toISOString()
    };
  },

  /**
   * Localizes an assessed level label (Beginner / Developing / Intermediate / Advanced).
   */
  levelLabel(level) {
    const key = `level_${String(level).toLowerCase()}`;
    const label = I18n.t(key);
    return label === key ? level : label;
  },

  /**
   * Localizes a topic status label (Strong / Developing / Needs Practice).
   */
  statusLabel(status) {
    const key = `level_assessment_status_${String(status).toLowerCase().replace(/ /g, '_')}`;
    const label = I18n.t(key);
    return label === key ? status : label;
  },

  showResults(result) {
    this.lastResult = result;

    // Badges & scores
    const levelBadge = document.getElementById('level-assessment-result-level-badge');
    const scoreBadge = document.getElementById('level-assessment-result-score-badge');
    const scoreDisplay = document.getElementById('level-assessment-score-display');
    const levelDisplay = document.getElementById('level-assessment-level-display');
    const answeredDisplay = document.getElementById('level-assessment-answered-display');

    if (levelBadge) {
      levelBadge.textContent = this.levelLabel(result.level);
      levelBadge.className = `level-badge level-${result.level.toLowerCase()}`;
    }

    if (scoreBadge) {
      const assessmentType = result.percentage >= 70 ? I18n.t('level_assessment_perf_strong') :
                             result.percentage >= 50 ? I18n.t('level_assessment_perf_good') :
                             I18n.t('level_assessment_perf_needs_improvement');
      scoreBadge.textContent = `${result.percentage}% • ${assessmentType}`;
    }

    if (scoreDisplay) scoreDisplay.textContent = `${result.percentage}%`;
    if (levelDisplay) levelDisplay.textContent = this.levelLabel(result.level);
    if (answeredDisplay) answeredDisplay.textContent = `${result.score} / ${result.total}`;

    // Strong topics
    const strongList = document.getElementById('level-assessment-strong-topics');
    if (strongList) {
      strongList.innerHTML = '';
      (result.strongAreas || []).forEach(area => {
        const span = document.createElement('span');
        span.className = 'area-badge strong';
        span.innerHTML = `✓ ${area}`;
        strongList.appendChild(span);
      });
    }

    // Focus topics
    const focusList = document.getElementById('level-assessment-focus-topics');
    if (focusList) {
      focusList.innerHTML = '';
      (result.focusAreas || []).forEach(area => {
        const span = document.createElement('span');
        span.className = 'area-badge focus';
        span.innerHTML = `⚠ ${area}`;
        focusList.appendChild(span);
      });
    }

    // Topic breakdown
    const breakdownGrid = document.getElementById('level-assessment-topic-breakdown');
    if (breakdownGrid) {
      breakdownGrid.innerHTML = '';
      (result.topicBreakdown || []).forEach(item => {
        const itemDiv = document.createElement('div');
        itemDiv.className = 'topic-meter-item';
        itemDiv.innerHTML = `
          <div class="topic-meter-header">
            <span class="topic-meter-name">${item.topic}</span>
            <span class="topic-meter-badge status-${item.status.toLowerCase().replace(/ /g, '-')}">${this.statusLabel(item.status)} (${item.correct}/${item.total})</span>
          </div>
          <div class="topic-meter-track">
            <div class="topic-meter-fill fill-${item.status.toLowerCase().replace(/ /g, '-')}" style="width: ${item.percentage}%;"></div>
          </div>
        `;
        breakdownGrid.appendChild(itemDiv);
      });
    }

    // Recommendations based on performance
    this.generateRecommendations(result);
  },

  generateRecommendations(result) {
    const recs = [];

    if (result.percentage >= 85) {
      recs.push(I18n.t('level_assessment_rec_excellent'));
      recs.push(I18n.t('level_assessment_rec_maintain'));
    } else if (result.percentage >= 70) {
      recs.push(I18n.t('level_assessment_rec_good'));
      recs.push(I18n.t('level_assessment_rec_practice'));
    } else if (result.percentage >= 50) {
      recs.push(I18n.t('level_assessment_rec_improving'));
      recs.push(I18n.t('level_assessment_rec_review'));
    } else {
      recs.push(I18n.t('level_assessment_rec_foundational'));
      recs.push(I18n.t('level_assessment_rec_step_by_step'));
    }

    recs.push(I18n.t('level_assessment_rec_retake'));

    for (let i = 1; i <= 3 && i <= recs.length; i++) {
      const el = document.getElementById(`recommendation-${i}`);
      if (el) {
        el.textContent = recs[i - 1];
      }
    }
  },

  restartAssessment() {
    this.startAssessment();
  },

  /**
   * Re-renders the visible assessment screen after an interface language change.
   */
  refreshOnLanguageChange() {
    const intro = document.getElementById('level-assessment-intro');
    if (intro && !intro.classList.contains('hidden') && this.currentQuestions.length > 0) {
      const countEl = document.getElementById('level-assessment-question-count');
      const subjectEl = document.getElementById('level-assessment-subject-name');
      if (countEl) countEl.textContent = I18n.t('level_assessment_questions_count', { count: this.currentQuestions.length });
      if (subjectEl) subjectEl.textContent = this.currentSubjectName;
    }

    const quiz = document.getElementById('level-assessment-quiz');
    if (quiz && !quiz.classList.contains('hidden') && this.currentQuestions.length > 0) {
      this.renderCurrentQuestion();
      this.updatePaginationState();
    }

    const results = document.getElementById('level-assessment-results');
    if (results && !results.classList.contains('hidden') && this.lastResult) {
      this.showResults(this.lastResult);
    }
  },

  closeAssessment() {
    Onboarding.goToStep('hub');
  },

  showScreen(screenName) {
    // Hide all assessment screens
    document.getElementById('level-assessment-intro').classList.add('hidden');
    document.getElementById('level-assessment-quiz').classList.add('hidden');
    document.getElementById('level-assessment-results').classList.add('hidden');

    // Show target screen
    const targetScreen = {
      'intro': 'level-assessment-intro',
      'quiz': 'level-assessment-quiz',
      'results': 'level-assessment-results'
    };

    if (targetScreen[screenName]) {
      document.getElementById(targetScreen[screenName]).classList.remove('hidden');
    }
  }
};

if (typeof module !== 'undefined' && module.exports) {
  module.exports = { LevelAssessment };
}
