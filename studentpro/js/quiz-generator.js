/**
 * Offline Quiz Generator
 * Builds quizzes from vetted local question banks, checks submitted answers, and records results.
 */
const QuizGenerator = {
  currentQuestions: [],
  currentIndex: 0,
  userAnswers: {},
  submittedAnswers: {},
  currentTopic: '',
  currentDifficulty: 'Intermediate',
  lastResult: null,
  generationInProgress: false,

  init() {
    this.bindEvents();
    window.addEventListener('synapse:language_changed', () => this.refreshOnLanguageChange());
    window.addEventListener('synapse:ollama_status_changed', () => this.renderSourceNotice());
    this.renderSourceNotice();
  },

  bindEvents() {
    const form = document.getElementById('form-quiz-generator');
    if (form) {
      form.addEventListener('submit', (e) => {
        e.preventDefault();
        this.generateQuiz();
      });
    }

    const btnNext = document.getElementById('btn-quiz-gen-next');
    if (btnNext) {
      btnNext.addEventListener('click', () => this.nextQuestion());
    }

    const btnSubmitAnswer = document.getElementById('btn-quiz-gen-submit-answer');
    if (btnSubmitAnswer) {
      btnSubmitAnswer.addEventListener('click', () => this.submitAnswer());
    }

    const btnPrev = document.getElementById('btn-quiz-gen-prev');
    if (btnPrev) {
      btnPrev.addEventListener('click', () => this.previousQuestion());
    }

    const btnRetry = document.getElementById('btn-quiz-gen-retry');
    if (btnRetry) {
      btnRetry.addEventListener('click', () => {
        this.currentQuestions = [];
        this.currentIndex = 0;
        this.userAnswers = {};
        this.submittedAnswers = {};
        this.lastResult = null;
        const setupMessage = document.getElementById('quiz-gen-setup-message');
        if (setupMessage) {
          setupMessage.textContent = '';
          setupMessage.classList.add('hidden');
        }
        document.getElementById('quiz-gen-setup-card')?.classList.remove('hidden');
        document.getElementById('quiz-gen-active-card')?.classList.add('hidden');
        document.getElementById('quiz-gen-results-card')?.classList.add('hidden');
      });
    }

    document.querySelectorAll('.btn-back-to-hub').forEach(btn => {
      btn.addEventListener('click', () => Onboarding.goToStep('hub'));
    });
  },

  async generateQuiz() {
    if (this.generationInProgress) return;
    const topicInput = document.getElementById('quiz-gen-topic');
    const diffInput = document.getElementById('quiz-gen-difficulty');
    const countInput = document.getElementById('quiz-gen-count');
    const studyTextInput = document.getElementById('quiz-gen-text');

    const topic = topicInput ? topicInput.value.trim() : I18n.t('quiz_default_topic');
    const difficulty = diffInput ? diffInput.value : 'Intermediate';
    const count = countInput ? parseInt(countInput.value, 10) : 5;
    const studyText = studyTextInput ? studyTextInput.value.trim() : '';
    const setupMessage = document.getElementById('quiz-gen-setup-message');
    const generateButton = document.getElementById('btn-generate-quiz-submit');
    if (setupMessage) {
      setupMessage.textContent = '';
      setupMessage.classList.add('hidden');
    }

    if (!topic || topic.length > 300 || ![3, 5, 10].includes(count)
      || !['Beginner', 'Intermediate', 'Advanced'].includes(difficulty)
      || studyText.length > 6000) {
      this.showSetupMessage(I18n.t('quiz_invalid_request'));
      return;
    }

    if (typeof GuestLimits !== 'undefined' && !GuestLimits.consume('quiz_generator')) return;

    this.generationInProgress = true;
    if (generateButton) generateButton.disabled = true;
    this.showSetupMessage(I18n.t('quiz_generating_local'));

    try {
      let questions;
      let generatedByOllama = false;
      if (typeof LocalAIEngine !== 'undefined') {
        try {
          const result = await LocalAIEngine.generateQuiz({
            topic,
            subject: this.getLearnerSubject(),
            difficulty,
            count,
            studyText
          });
          if (result) {
            questions = this.validateGeneratedQuestions(result.questions, count, topic);
            generatedByOllama = Boolean(questions);
            if (!questions) {
              console.warn('Ollama returned malformed quiz data; checking the verified offline question bank.');
            }
          }
        } catch (error) {
          console.warn('Ollama quiz generation failed; checking the verified offline question bank.', error);
          if (error instanceof TypeError) {
            LocalAIEngine.ollamaAvailable = false;
            LocalAIEngine.activeMode = 'ondevice_neural';
            window.dispatchEvent(new CustomEvent('synapse:ollama_status_changed', {
              detail: { available: false, model: LocalAIEngine.model }
            }));
          }
        }
      }

      if (!questions) {
        questions = this.getLocalQuestions(topic, difficulty, count);
        if (questions.length !== count) {
          throw new Error(I18n.t('quiz_local_insufficient', {
            available: questions.length,
            requested: count
          }));
        }
      }

      this.currentTopic = topic;
      this.currentDifficulty = difficulty;
      this.userAnswers = {};
      this.submittedAnswers = {};
      this.currentIndex = 0;
      this.lastResult = null;
      this.currentQuestions = questions;

      document.getElementById('quiz-gen-setup-card')?.classList.add('hidden');
      document.getElementById('quiz-gen-results-card')?.classList.add('hidden');
      document.getElementById('quiz-gen-active-card')?.classList.remove('hidden');

      const topicBadge = document.getElementById('quiz-gen-active-topic');
      if (topicBadge) topicBadge.textContent = I18n.t('quiz_topic_badge', {
        topic,
        difficulty: this.difficultyLabel(difficulty)
      });
      const modelBadge = document.getElementById('quiz-gen-model-badge');
      if (modelBadge) {
        modelBadge.textContent = generatedByOllama
          ? I18n.t('quiz_generated_by_ollama', { model: LocalAIEngine.model })
          : I18n.t('quiz_generated_from_bank');
        modelBadge.classList.remove('hidden');
      }
      this.renderQuestion();
    } catch (error) {
      console.error('Quiz generation failed:', error);
      this.showSetupMessage(error instanceof Error ? error.message : I18n.t('quiz_generation_failed'));
    } finally {
      this.generationInProgress = false;
      if (generateButton) generateButton.disabled = false;
      this.renderSourceNotice();
    }
  },

  getLearnerSubject() {
    const profile = typeof StorageService !== 'undefined' ? StorageService.getProfile() : {};
    return profile.selectedSubjectName || profile.courseName || profile.skillName || I18n.t('local_ai_default_subject');
  },

  validateGeneratedQuestions(questions, count, topic) {
    if (!Array.isArray(questions) || questions.length !== count) return null;
    const validated = [];
    for (const question of questions) {
      if (!question || typeof question.question !== 'string' || !question.question.trim()
        || !Array.isArray(question.options) || question.options.length !== 4
        || !Number.isInteger(question.correctIndex) || question.correctIndex < 0 || question.correctIndex > 3
        || typeof question.explanation !== 'string' || !question.explanation.trim()
        || question.options.some(option => typeof option !== 'string' || !option.trim())
        || new Set(question.options.map(option => option.trim().toLocaleLowerCase())).size !== 4) {
        return null;
      }
      validated.push({
        id: `ollama_quiz_${Date.now()}_${validated.length}`,
        topic,
        question: this.cleanQuestionText(question.question),
        options: question.options.map(option => this.cleanQuestionText(option)),
        correctIndex: question.correctIndex,
        explanation: this.cleanQuestionText(question.explanation)
      });
    }
    return validated;
  },

  showSetupMessage(message) {
    const setupMessage = document.getElementById('quiz-gen-setup-message');
    if (!setupMessage) return;
    setupMessage.textContent = message;
    setupMessage.classList.remove('hidden');
  },

  renderSourceNotice() {
    const notice = document.getElementById('quiz-gen-source-notice');
    if (!notice) return;
    const ollamaReady = typeof LocalAIEngine !== 'undefined'
      && LocalAIEngine.statusChecked
      && LocalAIEngine.ollamaAvailable;
    notice.textContent = ollamaReady
      ? I18n.t('quiz_ollama_source_notice', { model: LocalAIEngine.model })
      : I18n.t('quiz_offline_source_notice');
  },

  /**
   * Localizes a difficulty value (Beginner / Intermediate / Advanced) for display.
   */
  difficultyLabel(difficulty) {
    const key = `level_${String(difficulty).toLowerCase()}`;
    const label = I18n.t(key);
    return label === key ? difficulty : label;
  },

  getLocalQuestions(topic, difficulty, count) {
    if (typeof ASSESSMENT_QUESTION_BANKS === 'undefined'
      || typeof Assessment === 'undefined'
      || typeof StorageService === 'undefined') {
      return [];
    }

    const profile = StorageService.getProfile();
    const bank = Assessment.selectQuestionBank(profile);
    const selectedSubject = String(profile.selectedSubjectName || profile.courseName || profile.skillName || '').toLowerCase();
    if (bank === ASSESSMENT_QUESTION_BANKS.general_diagnostic
      && selectedSubject
      && !/general|reasoning|critical thinking/.test(selectedSubject)) {
      return [];
    }
    const bankQuestions = bank && Array.isArray(bank.questions)
      ? bank.questions.filter(question => Array.isArray(question.options)
        && question.options.length === 4
        && Number.isInteger(question.correctIndex)
        && question.correctIndex >= 0
        && question.correctIndex < question.options.length
        && typeof question.explanation === 'string'
        && question.explanation.trim())
      : [];
    const normalizedTopic = topic.toLowerCase().trim();
    const allTopics = /^(maths?|mathematics|science|physics|accountancy|commerce|english|python|programming|general practice)$/i
      .test(normalizedTopic);
    const topicAliases = {
      algebra: ['equation', 'variable', 'algebra'],
      equation: ['algebra', 'equation', 'variable'],
      linear: ['algebra', 'equation', 'variable'],
      fraction: ['fraction', 'proportion', 'arithmetic'],
      rational: ['fraction', 'proportion', 'arithmetic', 'number sense'],
      'number systems': ['number sense', 'arithmetic', 'fraction'],
      percentage: ['fraction', 'proportion', 'discount'],
      geometry: ['geometry', 'area', 'mensuration', 'triangle'],
      motion: ['kinematics', 'motion', 'velocity'],
      energy: ['energy', 'work, energy'],
      force: ['force', 'dynamics', 'newton'],
      grammar: ['grammar', 'sentence structure', 'tenses'],
      python: ['python', 'lists', 'dictionaries', 'control flow', 'functions'],
      accountancy: ['account', 'commerce', 'financial']
    };
    const aliasTerms = Object.entries(topicAliases)
      .filter(([term]) => normalizedTopic.includes(term))
      .flatMap(([, terms]) => terms);
    const searchableText = question => `${question.topic} ${question.question}`.toLowerCase();
    const matchedQuestions = allTopics
      ? bankQuestions
      : bankQuestions.filter(question => {
        const text = searchableText(question);
        return text.includes(normalizedTopic)
          || aliasTerms.some(term => text.includes(term))
          || normalizedTopic.split(/\s+/).some(term => term.length > 3 && text.includes(term));
      });
    const ranked = matchedQuestions
      .slice()
      .sort((a, b) => this.difficultyRank(b.difficulty, difficulty) - this.difficultyRank(a.difficulty, difficulty));

    return ranked.slice(0, count).map((question, index) => ({
      id: `local_quiz_${Date.now()}_${index}`,
      topic: question.topic || topic,
      question: this.cleanQuestionText(question.question),
      options: question.options.map(option => this.cleanQuestionText(option)),
      correctIndex: question.correctIndex,
      explanation: question.explanation
    }));
  },

  difficultyRank(questionDifficulty, selectedDifficulty) {
    const order = { easy: 0, medium: 1, hard: 2 };
    const target = { Beginner: 0, Intermediate: 1, Advanced: 2 }[selectedDifficulty] ?? 1;
    return 2 - Math.abs((order[questionDifficulty] ?? 1) - target);
  },

  cleanQuestionText(text) {
    return String(text || '').replace(/^\s*\[(?:Beginner|Intermediate|Advanced)\s+Quiz\s+Q\d+\]\s*/i, '').trim();
  },

  renderQuestion() {
    if (!this.currentQuestions || this.currentQuestions.length === 0) return;
    const q = this.currentQuestions[this.currentIndex];
    const total = this.currentQuestions.length;

    const counterEl = document.getElementById('quiz-gen-counter');
    const textEl = document.getElementById('quiz-gen-question-text');
    const optionsContainer = document.getElementById('quiz-gen-options-container');
    const expEl = document.getElementById('quiz-gen-explanation');
    const btnNext = document.getElementById('btn-quiz-gen-next');
    const btnSubmit = document.getElementById('btn-quiz-gen-submit-answer');
    const btnPrev = document.getElementById('btn-quiz-gen-prev');
    const progressBar = document.getElementById('quiz-gen-progress-bar');
    const progressTrack = progressBar?.parentElement;

    if (counterEl) counterEl.textContent = I18n.t('question_of', { current: this.currentIndex + 1, total: total });
    if (textEl) textEl.textContent = q.question;
    const submitted = this.submittedAnswers[q.id] !== undefined;
    if (expEl) {
      expEl.classList.toggle('hidden', !submitted);
      if (submitted) this.renderFeedback(expEl, q, this.submittedAnswers[q.id]);
      else {
        expEl.replaceChildren();
        expEl.classList.remove('is-correct', 'is-incorrect');
      }
    }
    const progress = Math.round((Object.keys(this.submittedAnswers).length / total) * 100);
    if (progressBar) progressBar.style.width = `${progress}%`;
    if (progressTrack) progressTrack.setAttribute('aria-valuenow', String(progress));

    if (optionsContainer) {
      optionsContainer.innerHTML = '';
      q.options.forEach((optText, idx) => {
        const label = document.createElement('label');
        label.className = 'quiz-option-card';
        if (submitted) {
          if (idx === q.correctIndex) label.classList.add('is-correct');
          if (idx === this.submittedAnswers[q.id] && idx !== q.correctIndex) label.classList.add('is-incorrect');
          label.classList.add('is-locked');
        }

        const radio = document.createElement('input');
        radio.type = 'radio';
        radio.name = `quiz_option_${q.id}`;
        radio.value = String(idx);
        radio.checked = this.userAnswers[q.id] === idx;
        radio.disabled = submitted;
        radio.addEventListener('change', () => {
          this.userAnswers[q.id] = idx;
          optionsContainer.querySelectorAll('.quiz-option-card').forEach(card => card.classList.remove('selected'));
          label.classList.add('selected');
          this.updateQuestionActions();
        });

        const letter = document.createElement('span');
        letter.className = 'quiz-option-letter';
        letter.textContent = String.fromCharCode(65 + idx);
        const answer = document.createElement('span');
        answer.className = 'quiz-option-text';
        answer.textContent = optText;
        label.append(radio, letter, answer);
        if (this.userAnswers[q.id] === idx) label.classList.add('selected');
        optionsContainer.appendChild(label);
      });
    }

    if (btnNext) btnNext.textContent = this.currentIndex === total - 1 ? I18n.t('quiz_btn_finish') : I18n.t('quiz_btn_next_question');
    if (btnPrev) btnPrev.disabled = this.currentIndex === 0;
    this.updateQuestionActions();
  },

  updateQuestionActions() {
    const q = this.currentQuestions[this.currentIndex];
    if (!q) return;
    const submitted = this.submittedAnswers[q.id] !== undefined;
    const btnSubmit = document.getElementById('btn-quiz-gen-submit-answer');
    const btnNext = document.getElementById('btn-quiz-gen-next');
    if (btnSubmit) {
      btnSubmit.classList.toggle('hidden', submitted);
      btnSubmit.disabled = this.userAnswers[q.id] === undefined;
    }
    if (btnNext) {
      btnNext.classList.toggle('hidden', !submitted);
      btnNext.textContent = this.currentIndex === this.currentQuestions.length - 1
        ? I18n.t('quiz_btn_finish')
        : I18n.t('quiz_btn_next_question');
    }
  },

  submitAnswer() {
    const q = this.currentQuestions[this.currentIndex];
    if (!q || this.userAnswers[q.id] === undefined || this.submittedAnswers[q.id] !== undefined) return;
    this.submittedAnswers[q.id] = this.userAnswers[q.id];
    this.renderQuestion();
  },

  renderFeedback(container, question, selectedIndex) {
    container.replaceChildren();
    const correct = selectedIndex === question.correctIndex;
    container.classList.toggle('is-correct', correct);
    container.classList.toggle('is-incorrect', !correct);

    const title = document.createElement('strong');
    title.textContent = I18n.t(correct ? 'quiz_feedback_correct' : 'quiz_feedback_incorrect');
    const explanation = document.createElement('p');
    explanation.textContent = correct
      ? question.explanation
      : I18n.t('quiz_feedback_solution', {
        answer: question.options[question.correctIndex],
        explanation: question.explanation
      });
    container.append(title, explanation);
  },

  nextQuestion() {
    const q = this.currentQuestions[this.currentIndex];
    if (!q || this.submittedAnswers[q.id] === undefined) return;

    if (this.currentIndex < this.currentQuestions.length - 1) {
      this.currentIndex++;
      this.renderQuestion();
    } else {
      this.finishQuiz();
    }
  },

  previousQuestion() {
    if (this.currentIndex === 0) return;
    this.currentIndex--;
    this.renderQuestion();
  },

  finishQuiz() {
    if (this.lastResult || this.currentQuestions.some(question => this.submittedAnswers[question.id] === undefined)) return;
    let score = 0;
    const total = this.currentQuestions.length;

    this.currentQuestions.forEach(q => {
      const selected = this.submittedAnswers[q.id];
      if (selected === q.correctIndex) {
        score++;
      } else {
        // Record mistake for Mistake-to-Mastery Loop
        StorageService.recordMistake({
          topic: q.topic,
          question: q.question,
          userAnswer: selected !== undefined ? q.options[selected] : I18n.t('quiz_no_answer'),
          correctAnswer: q.options[q.correctIndex],
          explanation: q.explanation
        });
      }
    });

    const percentage = Math.round((score / total) * 100);

    // Record quiz result & award XP
    StorageService.recordQuizResult({
      title: I18n.t('quiz_result_title', { topic: this.currentTopic }),
      score: score,
      total: total,
      percentage: percentage
    });

    this.lastResult = { score, total, percentage, correct: score, incorrect: total - score };

    // Render results view
    document.getElementById('quiz-gen-active-card')?.classList.add('hidden');
    const resultsCard = document.getElementById('quiz-gen-results-card');
    if (resultsCard) resultsCard.classList.remove('hidden');

    const scoreDisplay = document.getElementById('quiz-gen-score-display');
    const resultCounts = document.getElementById('quiz-gen-result-counts');
    const rewardText = document.getElementById('quiz-gen-reward-text');
    if (scoreDisplay) scoreDisplay.textContent = `${score} / ${total} (${percentage}%)`;
    if (resultCounts) resultCounts.textContent = I18n.t('quiz_result_counts', {
      correct: score,
      incorrect: total - score
    });
    if (rewardText) rewardText.textContent = I18n.t('quiz_xp_earned', { xp: score * 10 + 20 });
  },

  /**
   * Re-renders any visible quiz text after an interface language change.
   */
  refreshOnLanguageChange() {
    const activeCard = document.getElementById('quiz-gen-active-card');
    const resultsCard = document.getElementById('quiz-gen-results-card');

    if (activeCard && !activeCard.classList.contains('hidden') && this.currentQuestions.length > 0) {
      const topicBadge = document.getElementById('quiz-gen-active-topic');
      if (topicBadge) {
        topicBadge.textContent = I18n.t('quiz_topic_badge', {
          topic: this.currentTopic,
          difficulty: this.difficultyLabel(this.currentDifficulty)
        });
      }
      this.renderQuestion();
    }

    if (resultsCard && !resultsCard.classList.contains('hidden') && this.lastResult) {
      const scoreDisplay = document.getElementById('quiz-gen-score-display');
      const rewardText = document.getElementById('quiz-gen-reward-text');
      const { score, total, percentage, correct, incorrect } = this.lastResult;
      if (scoreDisplay) scoreDisplay.textContent = `${score} / ${total} (${percentage}%)`;
      const resultCounts = document.getElementById('quiz-gen-result-counts');
      if (resultCounts) resultCounts.textContent = I18n.t('quiz_result_counts', { correct, incorrect });
      if (rewardText) rewardText.textContent = I18n.t('quiz_xp_earned', { xp: score * 10 + 20 });
    }
  }
};

if (typeof module !== 'undefined' && module.exports) {
  module.exports = { QuizGenerator };
}
