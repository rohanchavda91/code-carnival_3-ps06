/**
 * Onboarding Flow Controller & Step Orchestrator
 * Manages step transitions, minimal utility header, progress tracking, and validation.
 */
const Onboarding = {
  currentStep: 'login',
  stepHistory: [],
  selectedLanguageCode: 'en',

  // Metadata for Onboarding Steps (excluding auth)
  stepsMeta: {
    'language': { stepNum: 1, totalSteps: 6, titleKey: 'lang_step_title' },
    'learner-type': { stepNum: 2, totalSteps: 6, titleKey: 'learner_type_title' },
    'academic-selection': { stepNum: 3, totalSteps: 6, titleKey: 'school_subject_title' },
    'learning-preferences': { stepNum: 4, totalSteps: 6, titleKey: 'prefs_title' },
    'assessment': { stepNum: 5, totalSteps: 6, titleKey: 'assessment_title' },
    'result': { stepNum: 5, totalSteps: 6, titleKey: 'result_title' },
    'hub': { stepNum: 5, totalSteps: 6, titleKey: 'hub_status' },
    'progress': { stepNum: 6, totalSteps: 6, titleKey: 'progress_dashboard_title' }
  },

  init() {
    const langParam = new URLSearchParams(window.location.search).get('lang');
    const savedLanguage = StorageService.getProfile().interfaceLanguage;
    const isSupportedLanguage = (code) => SUPPORTED_LANGUAGES.some(lang => lang.code === code);
    const initialLanguage = isSupportedLanguage(langParam)
      ? langParam
      : (isSupportedLanguage(savedLanguage) ? savedLanguage : 'en');
    this.selectedLanguageCode = initialLanguage;
    if (!isSupportedLanguage(savedLanguage)) {
      StorageService.saveProfile({ interfaceLanguage: initialLanguage });
    }
    if (I18n.getLanguage() !== initialLanguage) I18n.setLanguage(initialLanguage);
    this.renderLanguageCards();
    this.bindEvents();
    // Keep the dynamic language card/modal lists in sync with the active language.
    window.addEventListener('synapse:language_changed', () => {
      this.selectedLanguageCode = isSupportedLanguage(I18n.getLanguage()) ? I18n.getLanguage() : 'en';
      this.renderLanguageCards();
      this.renderQuickLanguageModalList();
    });
  },

  bindEvents() {
    // Header Back button
    const backBtn = document.getElementById('header-btn-back');
    if (backBtn) {
      backBtn.addEventListener('click', () => this.goBack());
    }

    // Step 1: Language search & confirm
    const langSearch = document.getElementById('lang-search-input');
    if (langSearch) {
      langSearch.addEventListener('input', (e) => this.filterLanguages(e.target.value));
    }

    const btnConfirmLang = document.getElementById('btn-confirm-language');
    if (btnConfirmLang) {
      btnConfirmLang.addEventListener('click', () => {
        this.goToStep('learner-type');
      });
    }

    // Step 2: Learner Type selection cards
    const pathCards = document.querySelectorAll('.pathway-card');
    pathCards.forEach(card => {
      card.addEventListener('click', () => {
        const type = card.getAttribute('data-pathway');
        this.selectLearnerType(type);
      });
    });

    // Step 4: Learning Preferences Form
    const prefsForm = document.getElementById('form-learning-preferences');
    if (prefsForm) {
      prefsForm.addEventListener('submit', (e) => {
        e.preventDefault();
        this.submitPreferences();
      });
    }

    // Quick Language Switcher in Header
    const headerLangBtn = document.getElementById('header-lang-switcher');
    if (headerLangBtn) {
      headerLangBtn.addEventListener('click', () => {
        this.openLanguageModal();
      });
    }

    const closeLangModalBtn = document.getElementById('modal-close-lang');
    if (closeLangModalBtn) {
      closeLangModalBtn.addEventListener('click', () => {
        this.closeLanguageModal();
      });
    }
  },

  renderLanguageCards() {
    const grid = document.getElementById('language-grid');
    if (!grid || typeof SUPPORTED_LANGUAGES === 'undefined') return;

    grid.innerHTML = '';
    const currentLang = this.getSelectedLanguageCode();

    SUPPORTED_LANGUAGES.forEach(lang => {
      const card = document.createElement('button');
      card.type = 'button';
      card.className = `lang-card ${lang.code === currentLang ? 'selected' : ''}`;
      card.setAttribute('data-lang-code', lang.code);
      card.setAttribute('data-lang-name', lang.name.toLowerCase());
      card.setAttribute('data-lang-native', lang.nativeName.toLowerCase());
      card.setAttribute('aria-label', `${lang.nativeName} (${lang.name})`);
      card.setAttribute('aria-pressed', String(lang.code === currentLang));

      const icon = document.createElement('span');
      icon.className = 'language-option-icon';
      icon.setAttribute('aria-hidden', 'true');
      icon.textContent = lang.code === 'en' ? '🌐' : 'IN';

      const nativeName = document.createElement('span');
      nativeName.className = 'lang-native-name';
      nativeName.textContent = lang.nativeName;
      nativeName.lang = lang.code;

      const englishName = document.createElement('span');
      englishName.className = 'lang-english-name';
      englishName.textContent = lang.name;

      const checkIcon = document.createElement('span');
      checkIcon.className = 'lang-check-icon';
      checkIcon.setAttribute('aria-hidden', 'true');
      checkIcon.textContent = '✓';

      card.append(icon, nativeName, englishName, checkIcon);

      card.addEventListener('click', () => {
        this.selectLanguage(lang.code);
      });

      grid.appendChild(card);
    });
  },

  filterLanguages(query) {
    const q = query.toLowerCase().trim();
    const cards = document.querySelectorAll('.lang-card');
    cards.forEach(card => {
      const name = card.getAttribute('data-lang-name') || '';
      const native = card.getAttribute('data-lang-native') || '';
      const match = name.includes(q) || native.includes(q);
      card.classList.toggle('hidden', !match);
    });
  },

  getSelectedLanguageCode() {
    return SUPPORTED_LANGUAGES.some(lang => lang.code === this.selectedLanguageCode) ? this.selectedLanguageCode : 'en';
  },

  selectLanguage(langCode) {
    if (!SUPPORTED_LANGUAGES.some(lang => lang.code === langCode)) return;

    this.selectedLanguageCode = langCode;

    // 1. Update I18n state & dynamic DOM labels
    I18n.setLanguage(langCode);

    // 2. Persist in profile
    StorageService.saveProfile({ interfaceLanguage: langCode });

    // 3. Update header indicator
    this.updateHeaderLanguageBadge(langCode);

    // 4. Re-render from the persisted selection, independent of translation fallback.
    this.renderLanguageCards();
  },

  updateHeaderLanguageBadge(langCode) {
    const badge = document.getElementById('header-active-lang-text');
    if (badge && typeof SUPPORTED_LANGUAGES !== 'undefined') {
      const found = SUPPORTED_LANGUAGES.find(l => l.code === langCode);
      if (found) {
        badge.textContent = found.nativeName;
      }
    }
  },

  selectLearnerType(type) {
    // Save to profile
    StorageService.saveProfile({ learnerType: type });

    // Mark active in UI
    const cards = document.querySelectorAll('.pathway-card');
    cards.forEach(c => {
      c.classList.toggle('selected', c.getAttribute('data-pathway') === type);
    });

    // Configure Academic Selection sub-views
    const schoolContainer = document.getElementById('subview-school');
    const collegeContainer = document.getElementById('subview-college');
    const skillsContainer = document.getElementById('subview-skills');

    if (schoolContainer) schoolContainer.classList.add('hidden');
    if (collegeContainer) collegeContainer.classList.add('hidden');
    if (skillsContainer) skillsContainer.classList.add('hidden');

    if (type === 'school') {
      if (schoolContainer) schoolContainer.classList.remove('hidden');
      SchoolSubjects.init();
    } else if (type === 'college') {
      if (collegeContainer) collegeContainer.classList.remove('hidden');
      CoursesAndSkills.renderCollegeCourses();
    } else if (type === 'skills') {
      if (skillsContainer) skillsContainer.classList.remove('hidden');
      CoursesAndSkills.renderSkills();
    }

    this.goToStep('academic-selection');
  },

  submitPreferences() {
    // Collect Goals
    const checkedGoals = [];
    const goalCheckboxes = document.querySelectorAll('input[name="learning_goals"]:checked');
    goalCheckboxes.forEach(cb => checkedGoals.push(cb.value));

    // Collect Learning Style
    const selectedStyleRadio = document.querySelector('input[name="learning_style"]:checked');
    const style = selectedStyleRadio ? selectedStyleRadio.value : 'step_by_step';

    // Collect Daily Time
    const selectedTimeRadio = document.querySelector('input[name="daily_time"]:checked');
    const time = selectedTimeRadio ? selectedTimeRadio.value : '30m';

    StorageService.saveProfile({
      learningGoals: checkedGoals.length > 0 ? checkedGoals : ['concepts'],
      learningStyle: style,
      dailyTime: time
    });

    // Start assessment with user profile
    const profile = StorageService.getProfile();
    Assessment.startForProfile(profile);

    this.goToStep('assessment');
  },

  /**
   * Main step navigation function
   */
  goToStep(stepId, pushHistory = true) {
    if (stepId !== 'ai-mentor' && typeof AiMentor !== 'undefined') {
      AiMentor.contextSubjectOverride = null;
    }
    if (pushHistory && this.currentStep && this.currentStep !== stepId) {
      this.stepHistory.push(this.currentStep);
    }
    this.currentStep = stepId;
    StorageService.setNavigationState({ currentStep: stepId });

    // Hide all step sections
    const allScreens = document.querySelectorAll('.app-screen');
    allScreens.forEach(screen => screen.classList.add('hidden'));

    // Show target step section
    const targetScreen = document.getElementById(`screen-${stepId}`);
    if (targetScreen) {
      targetScreen.classList.remove('hidden');
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Header visibility & controls
    this.updateHeader(stepId);

    if (stepId === 'ai-mentor' && typeof AiMentor !== 'undefined') {
      AiMentor.onOpen();
    }

    // If assessment screen, ensure questions are loaded
    if (stepId === 'assessment') {
      const profile = StorageService.getProfile();
      if (!Assessment.currentQuestions || Assessment.currentQuestions.length === 0) {
        Assessment.startForProfile(profile);
      }
    }

    // If result screen, trigger render with fallback calculation if needed
    if (stepId === 'result') {
      let profile = StorageService.getProfile();
      if (!profile.assessmentResult) {
        if (!Assessment.currentQuestions || Assessment.currentQuestions.length === 0) {
          Assessment.startForProfile(profile);
        }
        // Simulate default baseline answers for preview
        Assessment.currentQuestions.forEach((q, idx) => {
          Assessment.userAnswers[q.id] = (idx % 2 === 0) ? q.correctIndex : ((q.correctIndex + 1) % 4);
        });
        const calcRes = Assessment.calculateResults();
        profile = StorageService.saveProfile({
          assessmentCompleted: true,
          assessmentAnswers: Assessment.userAnswers,
          assessmentResult: calcRes
        });
      }
      Personalization.renderResult(profile);
    }

    // If hub screen, trigger render with fallback roadmap if needed
    if (stepId === 'hub') {
      let profile = StorageService.getProfile();
      if (!profile.assessmentResult) {
        if (!Assessment.currentQuestions || Assessment.currentQuestions.length === 0) {
          Assessment.startForProfile(profile);
        }
        Assessment.currentQuestions.forEach((q, idx) => {
          Assessment.userAnswers[q.id] = q.correctIndex;
        });
        const calcRes = Assessment.calculateResults();
        profile = StorageService.saveProfile({
          assessmentCompleted: true,
          assessmentAnswers: Assessment.userAnswers,
          assessmentResult: calcRes
        });
      }
      LearnerProfile.renderHub(profile);
    }

    // If progress screen, trigger progress analytics dashboard render
    if (stepId === 'progress') {
      if (typeof LearnerProfile !== 'undefined' && typeof LearnerProfile.renderProgressDashboard === 'function') {
        LearnerProfile.renderProgressDashboard();
      }
    }
  },

  goBack() {
    if (this.stepHistory.length > 0) {
      const prevStep = this.stepHistory.pop();
      this.goToStep(prevStep, false);
    } else {
      if (this.currentStep !== 'hub') {
        this.goToStep('hub', false);
      }
    }
  },

  updateHeader(stepId) {
    const header = document.getElementById('app-minimal-header');
    const backBtn = document.getElementById('header-btn-back');
    const progressWrap = document.getElementById('header-progress-wrap');
    const progressBar = document.getElementById('header-progress-bar-fill');
    const stepLabel = document.getElementById('header-step-counter');

    if (!header) return;

    if (stepId === 'login') {
      // In Auth, hide onboarding back & progress bar
      if (backBtn) backBtn.classList.add('hidden');
      if (progressWrap) progressWrap.classList.add('hidden');
      return;
    }

    // During Onboarding
    if (backBtn) {
      backBtn.classList.toggle('hidden', stepId === 'language' || stepId === 'hub');
    }

    if (progressWrap) {
      progressWrap.classList.add('hidden');
    }

    // Update active language label
    this.updateHeaderLanguageBadge(I18n.getLanguage());
  },

  openLanguageModal() {
    const modal = document.getElementById('modal-quick-language');
    if (modal) {
      modal.classList.remove('hidden');
      this.renderQuickLanguageModalList();
    }
  },

  closeLanguageModal() {
    const modal = document.getElementById('modal-quick-language');
    if (modal) modal.classList.add('hidden');
  },

  renderQuickLanguageModalList() {
    const container = document.getElementById('modal-language-list');
    if (!container || typeof SUPPORTED_LANGUAGES === 'undefined') return;

    container.innerHTML = '';
    const current = this.getSelectedLanguageCode();

    SUPPORTED_LANGUAGES.forEach(lang => {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = `modal-lang-item ${lang.code === current ? 'selected' : ''}`;
      btn.setAttribute('aria-pressed', String(lang.code === current));
      btn.setAttribute('data-lang-code', lang.code);
      btn.innerHTML = `
        <span class="flag">${lang.flag}</span>
        <span class="native">${lang.nativeName}</span>
        <span class="eng">(${lang.name})</span>
        <span class="lang-check-icon" aria-hidden="true">✓</span>
      `;
      btn.addEventListener('click', () => {
        this.selectLanguage(lang.code);
        this.closeLanguageModal();
      });
      container.appendChild(btn);
    });
  }
};

if (typeof module !== 'undefined' && module.exports) {
  module.exports = { Onboarding };
}
