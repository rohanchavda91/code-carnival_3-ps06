/**
 * School Subjects & Grade Logic Module
 * Manages Standard 1-12 selection, 11-12 stream workflows, and curriculum filtering.
 */
const SchoolSubjects = {
  selectedGrade: null,
  selectedStream: null,
  selectedSubject: null,

  init() {
    this.renderGrades();
    this.bindEvents();
    window.addEventListener('synapse:language_changed', () => {
      if (!this.selectedGrade || !this.selectedSubject) return;
      if (this.selectedStream) {
        this.renderSubjectsForStream(this.selectedStream);
      } else {
        this.renderSubjectsForGrade(this.selectedGrade);
      }
      const allSubjectCards = document.querySelectorAll('.subject-card');
      allSubjectCards.forEach(card => {
        card.classList.toggle('selected', !!this.selectedSubject && card.getAttribute('data-subject-id') === this.selectedSubject.id);
      });
    });
  },

  bindEvents() {
    // Stream selection clicks
    const streamContainer = document.getElementById('school-stream-options');
    if (streamContainer) {
      streamContainer.addEventListener('click', (e) => {
        const streamCard = e.target.closest('.stream-card');
        if (streamCard) {
          const streamId = streamCard.getAttribute('data-stream');
          this.selectStream(streamId);
        }
      });
    }

    // Continue button from School Subjects
    const confirmBtn = document.getElementById('btn-confirm-school-subject');
    if (confirmBtn) {
      confirmBtn.addEventListener('click', () => this.confirmSelection());
    }
  },

  renderGrades() {
    const grid = document.getElementById('school-grades-grid');
    if (!grid || typeof SCHOOL_CURRICULUM === 'undefined') return;

    grid.innerHTML = '';
    SCHOOL_CURRICULUM.grades.forEach(grade => {
      const card = document.createElement('button');
      card.type = 'button';
      card.className = `grade-card tier-${grade.tier}`;
      card.setAttribute('data-grade', grade.standard);
      card.innerHTML = `
        <div class="grade-number">${grade.standard}</div>
        <div class="grade-info">
          <span class="grade-title">${grade.name}</span>
          <span class="grade-desc">${grade.description}</span>
        </div>
        <div class="grade-check-icon">✓</div>
      `;

      card.addEventListener('click', () => this.selectGrade(grade.standard));
      grid.appendChild(card);
    });
  },

  selectGrade(standard) {
    this.selectedGrade = standard;
    this.selectedStream = null;
    this.selectedSubject = null;

    // Update active UI for grade cards
    const allGradeCards = document.querySelectorAll('.grade-card');
    allGradeCards.forEach(card => {
      const grade = parseInt(card.getAttribute('data-grade'), 10);
      card.classList.toggle('selected', grade === standard);
    });

    const streamSection = document.getElementById('school-stream-section');
    const subjectSection = document.getElementById('school-subject-section');
    const confirmBtn = document.getElementById('btn-confirm-school-subject');

    if (confirmBtn) confirmBtn.disabled = true;

    // Standard 11 or 12 requires Stream Selection FIRST
    if (standard === 11 || standard === 12) {
      if (streamSection) streamSection.classList.remove('hidden');
      if (subjectSection) subjectSection.classList.add('hidden');
      this.clearStreamSelection();
    } else {
      if (streamSection) streamSection.classList.add('hidden');
      if (subjectSection) subjectSection.classList.remove('hidden');
      this.renderSubjectsForGrade(standard);
    }
  },

  selectStream(streamId) {
    this.selectedStream = streamId;
    this.selectedSubject = null;

    // Update active UI for stream cards
    const allStreamCards = document.querySelectorAll('.stream-card');
    allStreamCards.forEach(card => {
      card.classList.toggle('selected', card.getAttribute('data-stream') === streamId);
    });

    const subjectSection = document.getElementById('school-subject-section');
    if (subjectSection) subjectSection.classList.remove('hidden');

    this.renderSubjectsForStream(streamId);
  },

  clearStreamSelection() {
    const allStreamCards = document.querySelectorAll('.stream-card');
    allStreamCards.forEach(card => card.classList.remove('selected'));
  },

  renderSubjectsForGrade(grade) {
    const container = document.getElementById('school-subjects-list');
    if (!container || typeof SCHOOL_CURRICULUM === 'undefined') return;

    const subjects = SCHOOL_CURRICULUM.subjectsByStandard[grade] || [];
    this.renderSubjectCards(subjects, container);
  },

  renderSubjectsForStream(streamId) {
    const container = document.getElementById('school-subjects-list');
    if (!container || typeof SCHOOL_CURRICULUM === 'undefined') return;

    const stream = SCHOOL_CURRICULUM.seniorStreams.find(s => s.id === streamId);
    const subjects = stream ? stream.subjects : [];
    this.renderSubjectCards(subjects, container);
  },

  renderSubjectCards(subjects, container) {
    container.innerHTML = '';
    if (subjects.length === 0) {
      container.innerHTML = `<p class="empty-hint">${I18n.t('school_no_subjects')}</p>`;
      return;
    }

    subjects.forEach(subject => {
      const card = document.createElement('div');
      card.className = 'subject-card';
      card.setAttribute('data-subject-id', subject.id);

      const topicBadges = (subject.topics || []).slice(0, 3)
        .map(t => `<span class="topic-pill">${t}</span>`).join('');

      card.innerHTML = `
        <div class="subject-card-header">
          <span class="subject-icon">${subject.icon || '📚'}</span>
          <div class="subject-title-wrap">
            <h4 class="subject-title">${subject.name}</h4>
            <span class="subject-code">${subject.code}</span>
          </div>
          <div class="subject-radio-circle"></div>
        </div>
        <div class="subject-topics-preview">
          ${topicBadges}
          ${(subject.topics && subject.topics.length > 3) ? `<span class="topic-pill-more">${I18n.t('school_topics_more', { count: subject.topics.length - 3 })}</span>` : ''}
        </div>
      `;

      card.addEventListener('click', () => {
        this.selectSubject(subject);
      });

      container.appendChild(card);
    });
  },

  selectSubject(subject) {
    this.selectedSubject = subject;

    const allSubjectCards = document.querySelectorAll('.subject-card');
    allSubjectCards.forEach(card => {
      card.classList.toggle('selected', card.getAttribute('data-subject-id') === subject.id);
    });

    const confirmBtn = document.getElementById('btn-confirm-school-subject');
    if (confirmBtn) confirmBtn.disabled = false;
  },

  confirmSelection() {
    if (!this.selectedGrade || !this.selectedSubject) return;

    const updates = {
      learnerType: 'school',
      standard: this.selectedGrade,
      stream: this.selectedStream || null,
      selectedSubjectId: this.selectedSubject.id,
      selectedSubjectName: `${this.selectedSubject.name} (Std ${this.selectedGrade}${this.selectedStream ? ` - ${this.selectedStream.toUpperCase()}` : ''})`,
      selectedTopics: this.selectedSubject.topics || []
    };

    StorageService.saveProfile(updates);
    Onboarding.goToStep('learning-preferences');
  }
};

if (typeof module !== 'undefined' && module.exports) {
  module.exports = { SchoolSubjects };
}
