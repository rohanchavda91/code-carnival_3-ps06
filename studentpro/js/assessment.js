/**
 * Diagnostic Knowledge & Learning Assessment Engine
 * Selects calibrated question sets, tracks learner responses, and calculates proficiency scores.
 */
const Assessment = {
  currentQuestions: [],
  currentIndex: 0,
  userAnswers: {}, // questionId -> selectedOptionIndex
  checkedAnswers: {}, // questionId -> selected option checked against the answer key
  currentDomainName: '',

  init() {
    this.bindEvents();
    window.addEventListener('synapse:language_changed', () => {
      if (this.currentQuestions && this.currentQuestions.length > 0) {
        this.renderCurrentQuestion();
      }
    });
  },

  bindEvents() {
    const btnNext = document.getElementById('btn-assessment-next');
    const btnPrev = document.getElementById('btn-assessment-prev');
    const btnSubmit = document.getElementById('btn-assessment-submit');
    const btnCheck = document.getElementById('btn-assessment-check');

    if (btnNext) btnNext.addEventListener('click', () => this.nextQuestion());
    if (btnPrev) btnPrev.addEventListener('click', () => this.previousQuestion());
    if (btnSubmit) btnSubmit.addEventListener('click', () => this.submitAssessment());
    if (btnCheck) btnCheck.addEventListener('click', () => this.checkCurrentAnswer());
  },

  /**
   * Loads appropriate question bank depending on user's selected track and subject
   */
  startForProfile(profile) {
    this.userAnswers = {};
    this.checkedAnswers = {};
    this.currentIndex = 0;

    const bank = this.selectQuestionBank(profile);
    this.currentQuestions = bank.questions;
    this.currentDomainName = bank.domainName;

    this.renderCurrentQuestion();
    this.updatePaginationState();
  },

  selectQuestionBank(profile) {
    if (typeof ASSESSMENT_QUESTION_BANKS === 'undefined') {
      return { domainName: I18n.t('assess_domain_general'), questions: [] };
    }

    const subjectId = (profile.selectedSubjectId || '').toLowerCase();
    const subjectName = (profile.selectedSubjectName || '').toLowerCase();
    const learnerType = profile.learnerType;

    // School Math
    if (subjectId.includes('math') || subjectName.includes('math')) {
      return ASSESSMENT_QUESTION_BANKS.school_math;
    }

    // Physics or Senior Science
    if (subjectId.includes('phy') || subjectName.includes('physics') || subjectName.includes('science')) {
      return ASSESSMENT_QUESTION_BANKS.physics_science;
    }

    // Accountancy or Commerce
    if (subjectId.includes('acc') || subjectName.includes('account') || subjectName.includes('commerce') || subjectName.includes('finance')) {
      return ASSESSMENT_QUESTION_BANKS.commerce_accountancy;
    }

    // JavaScript / Web / BCA
    if (subjectId.includes('js') || subjectId.includes('javascript') || subjectId.includes('web') || subjectId.includes('bca') || subjectName.includes('javascript') || subjectName.includes('bca')) {
      return ASSESSMENT_QUESTION_BANKS.javascript_web;
    }

    // Python / Data Science
    if (subjectId.includes('python') || subjectName.includes('python') || subjectName.includes('data science')) {
      return ASSESSMENT_QUESTION_BANKS.python_tech;
    }

    // Spoken English / Languages
    if (subjectId.includes('eng') || subjectName.includes('english') || subjectName.includes('spoken')) {
      return ASSESSMENT_QUESTION_BANKS.english_language;
    }

    // Fallback to adaptive general diagnostic
    return ASSESSMENT_QUESTION_BANKS.general_diagnostic;
  },

  renderCurrentQuestion() {
    if (!this.currentQuestions || this.currentQuestions.length === 0) return;

    const q = this.currentQuestions[this.currentIndex];
    const total = this.currentQuestions.length;

    // Domain and counter labels
    const domainBadge = document.getElementById('assessment-domain-tag');
    const counterEl = document.getElementById('assessment-question-counter');
    const diffBadge = document.getElementById('assessment-difficulty-badge');
    const topicTag = document.getElementById('assessment-topic-tag');
    const questionText = document.getElementById('assessment-question-text');
    const optionsContainer = document.getElementById('assessment-options-container');
    const progressBar = document.getElementById('assessment-progress-bar');

    if (domainBadge) domainBadge.textContent = this.currentDomainName;
    if (counterEl) counterEl.textContent = I18n.t('question_of', { current: this.currentIndex + 1, total: total });
    if (topicTag) topicTag.textContent = q.topic;

    if (diffBadge) {
      diffBadge.textContent = I18n.t(`difficulty_${q.difficulty}`) || q.difficulty.toUpperCase();
      diffBadge.className = `badge-difficulty diff-${q.difficulty}`;
    }

    if (questionText) questionText.textContent = q.question;

    // Progress bar
    if (progressBar) {
      const pct = Math.round(((this.currentIndex + 1) / total) * 100);
      progressBar.style.width = `${pct}%`;
    }

    // Render Options
    if (optionsContainer) {
      optionsContainer.innerHTML = '';
      const selectedIndex = this.userAnswers[q.id];
      const checkedIndex = this.checkedAnswers[q.id];

      q.options.forEach((optText, idx) => {
        const optionLabel = document.createElement('label');
        optionLabel.className = 'assessment-option-card';
        const isChecked = checkedIndex !== undefined;
        const isCorrectOption = isChecked && idx === q.correctIndex;
        const isWrongSelection = isChecked && idx === checkedIndex && checkedIndex !== q.correctIndex;
        if (selectedIndex === idx) optionLabel.classList.add('selected');
        if (isCorrectOption) optionLabel.classList.add('is-correct');
        if (isWrongSelection) optionLabel.classList.add('is-incorrect');
        if (isChecked) optionLabel.classList.add('has-feedback');

        const letters = ['A', 'B', 'C', 'D'];
        const letterBadge = document.createElement('div');
        letterBadge.className = 'option-letter-badge';
        letterBadge.textContent = letters[idx] || String(idx + 1);

        const optionText = document.createElement('div');
        optionText.className = 'option-text';
        optionText.textContent = optText;

        const radio = document.createElement('input');
        radio.type = 'radio';
        radio.name = `assessment_opt_${q.id}`;
        radio.value = String(idx);
        radio.className = 'sr-only';
        radio.checked = selectedIndex === idx;
        radio.disabled = isChecked;

        const checkCircle = document.createElement('div');
        checkCircle.className = 'option-check-circle';

        optionLabel.append(letterBadge, optionText, radio, checkCircle);

        optionLabel.addEventListener('click', () => {
          this.selectAnswer(q.id, idx);
        });

        optionsContainer.appendChild(optionLabel);
      });
    }

    this.renderQuestionDots();
  },

  selectAnswer(questionId, optionIndex) {
    if (this.checkedAnswers[questionId] !== undefined) return;
    this.userAnswers[questionId] = optionIndex;

    // Highlight option
    const options = document.querySelectorAll('.assessment-option-card');
    options.forEach((card, idx) => {
      card.classList.toggle('selected', idx === optionIndex);
    });

    this.updatePaginationState();
    this.renderQuestionDots();
  },

  checkCurrentAnswer() {
    const q = this.currentQuestions[this.currentIndex];
    if (!q || this.userAnswers[q.id] === undefined || this.checkedAnswers[q.id] !== undefined) return;

    this.checkedAnswers[q.id] = this.userAnswers[q.id];
    this.renderCurrentQuestion();
    this.updatePaginationState();
  },

  nextQuestion() {
    const currentQuestion = this.currentQuestions[this.currentIndex];
    if (currentQuestion
      && this.checkedAnswers[currentQuestion.id] !== undefined
      && this.currentIndex < this.currentQuestions.length - 1) {
      this.currentIndex++;
      this.renderCurrentQuestion();
      this.updatePaginationState();
    }
  },

  previousQuestion() {
    if (this.currentIndex > 0) {
      this.currentIndex--;
      this.renderCurrentQuestion();
      this.updatePaginationState();
    }
  },

  renderQuestionDots() {
    const dotsContainer = document.getElementById('assessment-dots-nav');
    if (!dotsContainer) return;

    dotsContainer.innerHTML = '';
    this.currentQuestions.forEach((q, idx) => {
      const dot = document.createElement('button');
      dot.type = 'button';
      dot.className = 'dot-nav-item';
      if (idx === this.currentIndex) dot.classList.add('current');
      if (this.checkedAnswers[q.id] !== undefined) dot.classList.add('answered');

      dot.setAttribute('title', I18n.t('assess_go_to_question', { number: idx + 1 }));
      const firstIncompleteIndex = this.currentQuestions.findIndex(question => this.checkedAnswers[question.id] === undefined);
      dot.disabled = firstIncompleteIndex !== -1 && idx > firstIncompleteIndex;
      dot.addEventListener('click', () => {
        if (dot.disabled) return;
        this.currentIndex = idx;
        this.renderCurrentQuestion();
        this.updatePaginationState();
      });

      dotsContainer.appendChild(dot);
    });
  },

  updatePaginationState() {
    const btnNext = document.getElementById('btn-assessment-next');
    const btnPrev = document.getElementById('btn-assessment-prev');
    const btnSubmit = document.getElementById('btn-assessment-submit');
    const btnCheck = document.getElementById('btn-assessment-check');
    const total = this.currentQuestions.length;
    const currentQuestion = this.currentQuestions[this.currentIndex];
    const allQuestionsChecked = total > 0
      && this.currentQuestions.every(question => this.checkedAnswers[question.id] !== undefined);
    const currentQuestionChecked = currentQuestion
      && this.checkedAnswers[currentQuestion.id] !== undefined;

    if (btnPrev) {
      btnPrev.disabled = (this.currentIndex === 0);
    }
    if (btnCheck) {
      btnCheck.disabled = !currentQuestion
        || this.userAnswers[currentQuestion.id] === undefined
        || currentQuestionChecked;
      btnCheck.classList.toggle('hidden', !!currentQuestionChecked);
    }

    const isLast = (this.currentIndex === total - 1);
    if (btnNext) {
      btnNext.classList.toggle('hidden', isLast || !currentQuestionChecked);
      btnNext.disabled = !currentQuestionChecked;
    }
    if (btnSubmit) {
      btnSubmit.classList.toggle('hidden', !isLast || !currentQuestionChecked);
      btnSubmit.disabled = !allQuestionsChecked;
    }
  },

  submitAssessment() {
    if (this.currentQuestions.length === 0) return;

    const firstIncompleteIndex = this.currentQuestions.findIndex(question => this.checkedAnswers[question.id] === undefined);
    if (firstIncompleteIndex !== -1) {
      this.currentIndex = firstIncompleteIndex;
      this.renderCurrentQuestion();
      this.updatePaginationState();
      return;
    }

    // Calculate score & topic diagnostics
    const result = this.calculateResults();
    
    // Save to profile
    StorageService.saveProfile({
      assessmentCompleted: true,
      assessmentAnswers: this.userAnswers,
      assessmentResult: result
    });

    // Advance to result page
    Onboarding.goToStep('result');
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

    // Determine Level
    let level = 'Beginner';
    let difficultyLabel = 'Fundamentals';
    if (percentage >= 85) {
      level = 'Advanced';
      difficultyLabel = 'High Pace / Mastery';
    } else if (percentage >= 60) {
      level = 'Intermediate';
      difficultyLabel = 'Standard Intermediate';
    } else if (percentage >= 40) {
      level = 'Developing';
      difficultyLabel = 'Foundation Reinforcement';
    } else {
      level = 'Beginner';
      difficultyLabel = 'Guided Essentials';
    }

    // Determine Topic Breakdown
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
        status = 'Focus Needed';
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

    // Fallbacks if lists are empty
    if (strongAreas.length === 0 && topicBreakdown.length > 0) {
      strongAreas.push(topicBreakdown[0].topic);
    }
    if (focusAreas.length === 0 && topicBreakdown.length > 1) {
      focusAreas.push(topicBreakdown[topicBreakdown.length - 1].topic);
    }

    const currentProfile = StorageService.getProfile();
    const roadmap = Personalization.generateRoadmap(currentProfile, level, focusAreas);

    return {
      score: correctCount,
      total: totalQuestions,
      percentage: percentage,
      level: level,
      calibratedDifficulty: difficultyLabel,
      topicBreakdown: topicBreakdown,
      strongAreas: strongAreas,
      focusAreas: focusAreas,
      recommendedStartingTopic: focusAreas[0] || (currentProfile.selectedTopics[0] || 'Core Foundations'),
      recommendedDifficulty: difficultyLabel,
      dailyTime: currentProfile.dailyTime || '30m',
      generatedRoadmap: roadmap,
      evaluatedAt: new Date().toISOString()
    };
  }
};

if (typeof module !== 'undefined' && module.exports) {
  module.exports = { Assessment };
}
