/**
 * Local Storage & State Management Layer
 * Provides centralized schema-checked persistence, reactive events, and backend-ready models.
 */
const STORAGE_KEYS = {
  AUTH: 'synapse_auth_session',
  PROFILE: 'synapse_learner_profile',
  APP_STATE: 'synapse_app_state',
  AI_MENTOR_CONVERSATION: 'synapse_ai_mentor_conversation'
};

const DEFAULT_PROFILE = {
  name: '',
  email: '',
  interfaceLanguage: 'en',
  learnerType: '', // 'school' | 'college' | 'skills'
  standard: null,   // 1 - 12
  stream: null,     // 'science' | 'commerce' | 'arts'
  courseId: null,
  courseName: null,
  skillId: null,
  skillName: null,
  selectedSubjectId: null,
  selectedSubjectName: '',
  selectedTopics: [],
  learningGoals: [],
  learningStyle: 'step_by_step',
  dailyTime: '30m',
  assessmentCompleted: false,
  assessmentAnswers: {},
  assessmentResult: null,
  completedTopics: [],
  inProgressTopics: [],
  quizHistory: [],
  lastActiveTopic: null,
  xpPoints: 120,
  streakDays: 3,
  userLevel: 1,
  badges: ['welcome_badge', 'first_quiz'],
  savedNotes: [
    {
      id: 'note_1',
      title: 'Rational Numbers Summary',
      content: 'A rational number is any number that can be expressed as p/q where p and q are integers and q is not zero.',
      tags: ['Math', 'Std 8'],
      updatedAt: new Date().toISOString()
    }
  ],
  mistakesLog: [],
  aiConfig: {
    provider: 'ollama',
    endpoint: 'http://localhost:11434/api/generate',
    model: 'llama3.1:latest',
    socraticMode: false
  },
  createdAt: null,
  updatedAt: null
};

// Localized default text for stored records. Falls back to the English
// default if the i18n engine has not loaded yet.
function storageText(key, fallback) {
  return (typeof I18n !== 'undefined') ? I18n.t(key, {}, fallback) : fallback;
}

const StorageService = {
  // --- Auth Session ---
  getAuth() {
    try {
      const data = localStorage.getItem(STORAGE_KEYS.AUTH);
      return data ? JSON.parse(data) : null;
    } catch (e) {
      console.warn('Storage read failed', e);
      return null;
    }
  },

  setAuth(session) {
    try {
      localStorage.setItem(STORAGE_KEYS.AUTH, JSON.stringify(session));
      window.dispatchEvent(new CustomEvent('synapse:auth_changed', { detail: session }));
    } catch (e) {
      console.warn('Storage write failed', e);
    }
  },

  clearAuth() {
    localStorage.removeItem(STORAGE_KEYS.AUTH);
    window.dispatchEvent(new CustomEvent('synapse:auth_changed', { detail: null }));
  },

  isAuthenticated() {
    const auth = this.getAuth();
    return !!(auth && auth.isLoggedIn);
  },

  // --- Learner Profile ---
  getProfile() {
    try {
      const raw = localStorage.getItem(STORAGE_KEYS.PROFILE);
      if (!raw) return { ...DEFAULT_PROFILE };
      return { ...DEFAULT_PROFILE, ...JSON.parse(raw) };
    } catch (e) {
      console.warn('Profile read failed', e);
      return { ...DEFAULT_PROFILE };
    }
  },

  saveProfile(updates) {
    try {
      const current = this.getProfile();
      const updated = {
        ...current,
        ...updates,
        updatedAt: new Date().toISOString()
      };
      if (!updated.createdAt) {
        updated.createdAt = new Date().toISOString();
      }
      localStorage.setItem(STORAGE_KEYS.PROFILE, JSON.stringify(updated));
      window.dispatchEvent(new CustomEvent('synapse:profile_updated', { detail: updated }));
      return updated;
    } catch (e) {
      console.warn('Profile write failed', e);
      return null;
    }
  },

  markTopicCompleted(topicTitle) {
    const profile = this.getProfile();
    const completed = Array.isArray(profile.completedTopics) ? [...profile.completedTopics] : [];
    if (!completed.includes(topicTitle)) {
      completed.push(topicTitle);
    }
    const inProgress = (profile.inProgressTopics || []).filter(t => (typeof t === 'string' ? t : t.title) !== topicTitle);
    return this.saveProfile({
      completedTopics: completed,
      inProgressTopics: inProgress,
      lastActiveTopic: topicTitle
    });
  },

  recordQuizResult(quizResult) {
    const profile = this.getProfile();
    const history = Array.isArray(profile.quizHistory) ? [...profile.quizHistory] : [];
    history.unshift({
      id: 'quiz_' + Date.now(),
      title: quizResult.title || storageText('quiz_default_title', 'Diagnostic Quiz'),
      score: quizResult.score || 0,
      total: quizResult.total || 0,
      percentage: quizResult.percentage || 0,
      timestamp: new Date().toISOString()
    });
    this.addXP(quizResult.score ? quizResult.score * 10 : 20, 'Quiz Completed');
    return this.saveProfile({
      quizHistory: history.slice(0, 10)
    });
  },

  // --- Gamification Helpers ---
  addXP(amount, reason = '') {
    const profile = this.getProfile();
    const newXP = (profile.xpPoints || 0) + amount;
    const newLevel = Math.floor(newXP / 100) + 1;
    const badges = Array.isArray(profile.badges) ? [...profile.badges] : ['welcome_badge'];
    if (newLevel >= 2 && !badges.includes('level_2')) badges.push('level_2');
    if (newLevel >= 5 && !badges.includes('level_5')) badges.push('level_5');

    return this.saveProfile({
      xpPoints: newXP,
      userLevel: newLevel,
      badges: badges
    });
  },

  // --- Notes Maker Helpers ---
  getNotes() {
    const profile = this.getProfile();
    return Array.isArray(profile.savedNotes) ? profile.savedNotes : [];
  },

  saveNote(noteData) {
    const profile = this.getProfile();
    const notes = this.getNotes();
    const existingIndex = notes.findIndex(n => n.id === noteData.id);

    if (existingIndex >= 0) {
      notes[existingIndex] = { ...notes[existingIndex], ...noteData, updatedAt: new Date().toISOString() };
    } else {
      notes.unshift({
        id: 'note_' + Date.now(),
        title: noteData.title || storageText('note_untitled', 'Untitled Note'),
        content: noteData.content || '',
        tags: Array.isArray(noteData.tags) ? noteData.tags : [storageText('tag_general', 'General')],
        updatedAt: new Date().toISOString()
      });
    }

    this.addXP(5, 'Note Saved');
    return this.saveProfile({ savedNotes: notes });
  },

  deleteNote(id) {
    const profile = this.getProfile();
    const notes = this.getNotes().filter(n => n.id !== id);
    return this.saveProfile({ savedNotes: notes });
  },

  // --- Mistake-to-Mastery Helpers ---
  getMistakes() {
    const profile = this.getProfile();
    return Array.isArray(profile.mistakesLog) ? profile.mistakesLog : [];
  },

  recordMistake(mistake) {
    const profile = this.getProfile();
    const mistakes = this.getMistakes();
    mistakes.unshift({
      id: 'mistake_' + Date.now(),
      topic: mistake.topic || storageText('tag_general', 'General'),
      question: mistake.question,
      userAnswer: mistake.userAnswer,
      correctAnswer: mistake.correctAnswer,
      explanation: mistake.explanation || storageText('mistake_default_explanation', 'Review conceptual foundations.'),
      mastered: false,
      timestamp: new Date().toISOString()
    });
    return this.saveProfile({ mistakesLog: mistakes.slice(0, 30) });
  },

  markMistakeMastered(id) {
    const profile = this.getProfile();
    const mistakes = this.getMistakes().map(m => m.id === id ? { ...m, mastered: true } : m);
    this.addXP(15, 'Mistake Mastered');
    return this.saveProfile({ mistakesLog: mistakes });
  },

  // --- AI Provider Config Helpers ---
  getAIConfig() {
    const profile = this.getProfile();
    return profile.aiConfig || {
      provider: 'ollama',
      endpoint: 'http://localhost:11434/api/generate',
      model: 'llama3.1:latest',
      socraticMode: false
    };
  },

  saveAIConfig(config) {
    const current = this.getAIConfig();
    return this.saveProfile({
      aiConfig: { ...current, ...config }
    });
  },

  // --- App Navigation State ---
  getNavigationState() {
    try {
      const state = localStorage.getItem(STORAGE_KEYS.APP_STATE);
      return state ? JSON.parse(state) : { currentStep: 'login' };
    } catch (e) {
      return { currentStep: 'login' };
    }
  },

  setNavigationState(state) {
    try {
      localStorage.setItem(STORAGE_KEYS.APP_STATE, JSON.stringify(state));
      window.dispatchEvent(new CustomEvent('synapse:step_changed', { detail: state }));
    } catch (e) {
      console.warn('Nav write failed', e);
    }
  },

  getAIMentorConversation() {
    try {
      const raw = localStorage.getItem(STORAGE_KEYS.AI_MENTOR_CONVERSATION);
      if (!raw) return [];
      const messages = JSON.parse(raw);
      return Array.isArray(messages) ? messages : [];
    } catch (e) {
      console.warn('AI Mentor conversation read failed', e);
      return [];
    }
  },

  saveAIMentorConversation(messages) {
    try {
      localStorage.setItem(STORAGE_KEYS.AI_MENTOR_CONVERSATION, JSON.stringify(messages));
      return true;
    } catch (e) {
      console.warn('AI Mentor conversation write failed', e);
      return false;
    }
  },

  clearAIMentorConversation() {
    try {
      localStorage.removeItem(STORAGE_KEYS.AI_MENTOR_CONVERSATION);
      return true;
    } catch (e) {
      console.warn('AI Mentor conversation clear failed', e);
      return false;
    }
  },

  // --- Full Demo Reset ---
  resetAll() {
    localStorage.removeItem(STORAGE_KEYS.AUTH);
    localStorage.removeItem(STORAGE_KEYS.PROFILE);
    localStorage.removeItem(STORAGE_KEYS.APP_STATE);
    localStorage.removeItem(STORAGE_KEYS.AI_MENTOR_CONVERSATION);
    window.dispatchEvent(new CustomEvent('synapse:reset_completed'));
  }
};

if (typeof module !== 'undefined' && module.exports) {
  module.exports = { StorageService, STORAGE_KEYS, DEFAULT_PROFILE };
}
