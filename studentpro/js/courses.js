/**
 * Higher Education Courses & Lifelong Skills Module
 * Manages search, filter pills, syllabus inspection, and selection for Pathways B & C.
 */
const CoursesAndSkills = {
  // College state
  currentLevelFilter: 'all',
  currentCategoryFilter: 'all',
  collegeSearchQuery: '',
  selectedCourse: null,

  // Skills state
  currentSkillCategoryFilter: 'all',
  skillsSearchQuery: '',
  selectedSkill: null,

  init() {
    this.bindCollegeEvents();
    this.bindSkillsEvents();
    window.addEventListener('synapse:language_changed', () => {
      this.renderCollegeCourses();
      this.renderSkills();
    });
  },

  // ==========================================
  // COLLEGE & HIGHER EDUCATION
  // ==========================================
  bindCollegeEvents() {
    const searchInput = document.getElementById('college-search-input');
    if (searchInput) {
      searchInput.addEventListener('input', (e) => {
        this.collegeSearchQuery = e.target.value.toLowerCase().trim();
        this.renderCollegeCourses();
      });
    }

    // Level filter pills
    const levelContainer = document.getElementById('college-level-filters');
    if (levelContainer) {
      levelContainer.addEventListener('click', (e) => {
        const pill = e.target.closest('.filter-pill');
        if (pill) {
          levelContainer.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('active'));
          pill.classList.add('active');
          this.currentLevelFilter = pill.getAttribute('data-level');
          this.renderCollegeCourses();
        }
      });
    }

    // Category filter pills
    const catContainer = document.getElementById('college-category-filters');
    if (catContainer) {
      catContainer.addEventListener('click', (e) => {
        const pill = e.target.closest('.filter-pill');
        if (pill) {
          catContainer.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('active'));
          pill.classList.add('active');
          this.currentCategoryFilter = pill.getAttribute('data-category');
          this.renderCollegeCourses();
        }
      });
    }

    // Confirm button
    const confirmBtn = document.getElementById('btn-confirm-college-course');
    if (confirmBtn) {
      confirmBtn.addEventListener('click', () => this.confirmCollegeSelection());
    }
  },

  renderCollegeCourses() {
    const grid = document.getElementById('college-courses-grid');
    if (!grid || typeof COLLEGE_CATALOG === 'undefined') return;

    let filtered = COLLEGE_CATALOG.courses.filter(course => {
      // Level check
      if (this.currentLevelFilter !== 'all' && course.level !== this.currentLevelFilter) {
        return false;
      }
      // Category check
      if (this.currentCategoryFilter !== 'all' && course.category !== this.currentCategoryFilter) {
        return false;
      }
      // Search query check
      if (this.collegeSearchQuery) {
        const q = this.collegeSearchQuery;
        const matchName = course.name.toLowerCase().includes(q);
        const matchCode = course.code.toLowerCase().includes(q);
        const matchDesc = course.description.toLowerCase().includes(q);
        const matchTopics = (course.topics || []).some(t => t.toLowerCase().includes(q));
        return matchName || matchCode || matchDesc || matchTopics;
      }
      return true;
    });

    grid.innerHTML = '';
    if (filtered.length === 0) {
      grid.innerHTML = `
        <div class="empty-state-search">
          <span class="empty-icon">🔍</span>
          <p>${I18n.t('college_empty_state')}</p>
        </div>
      `;
      return;
    }

    filtered.forEach(course => {
      const card = document.createElement('div');
      card.className = 'course-card';
      if (this.selectedCourse && this.selectedCourse.id === course.id) {
        card.classList.add('selected');
      }
      card.setAttribute('data-course-id', course.id);

      const topicTags = (course.topics || []).slice(0, 3)
        .map(t => `<span class="topic-pill">${t}</span>`).join('');

      card.innerHTML = `
        <div class="course-card-top">
          <span class="course-icon">${course.icon || '🎓'}</span>
          <span class="course-badge">${course.code}</span>
        </div>
        <h4 class="course-name">${course.name}</h4>
        <p class="course-desc">${course.description}</p>
        <div class="course-meta">
          <span class="meta-item">⏱️ ${course.duration}</span>
          <span class="meta-item">🏷️ ${this.getCategoryLabel(course.category)}</span>
        </div>
        <div class="course-topics-list">
          ${topicTags}
          ${(course.topics && course.topics.length > 3) ? `<span class="topic-pill-more">+${course.topics.length - 3}</span>` : ''}
        </div>
      `;

      card.addEventListener('click', () => this.selectCourse(course));
      grid.appendChild(card);
    });
  },

  selectCourse(course) {
    this.selectedCourse = course;
    const cards = document.querySelectorAll('.course-card');
    cards.forEach(c => {
      c.classList.toggle('selected', c.getAttribute('data-course-id') === course.id);
    });

    const confirmBtn = document.getElementById('btn-confirm-college-course');
    if (confirmBtn) confirmBtn.disabled = false;
  },

  confirmCollegeSelection() {
    if (!this.selectedCourse) return;

    const updates = {
      learnerType: 'college',
      courseId: this.selectedCourse.id,
      courseName: `${this.selectedCourse.code} - ${this.selectedCourse.name}`,
      selectedSubjectId: this.selectedCourse.id,
      selectedSubjectName: `${this.selectedCourse.code}: ${this.selectedCourse.name}`,
      selectedTopics: this.selectedCourse.topics || []
    };

    StorageService.saveProfile(updates);
    Onboarding.goToStep('learning-preferences');
  },

  // ==========================================
  // LIFELONG LEARNING & SKILLS
  // ==========================================
  bindSkillsEvents() {
    const searchInput = document.getElementById('skills-search-input');
    if (searchInput) {
      searchInput.addEventListener('input', (e) => {
        this.skillsSearchQuery = e.target.value.toLowerCase().trim();
        this.renderSkills();
      });
    }

    const catContainer = document.getElementById('skills-category-filters');
    if (catContainer) {
      catContainer.addEventListener('click', (e) => {
        const pill = e.target.closest('.filter-pill');
        if (pill) {
          catContainer.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('active'));
          pill.classList.add('active');
          this.currentSkillCategoryFilter = pill.getAttribute('data-category');
          this.renderSkills();
        }
      });
    }

    const confirmBtn = document.getElementById('btn-confirm-skill');
    if (confirmBtn) {
      confirmBtn.addEventListener('click', () => this.confirmSkillSelection());
    }
  },

  renderSkills() {
    const grid = document.getElementById('skills-catalog-grid');
    if (!grid || typeof SKILLS_CATALOG === 'undefined') return;

    let filtered = SKILLS_CATALOG.skills.filter(skill => {
      if (this.currentSkillCategoryFilter !== 'all' && skill.category !== this.currentSkillCategoryFilter) {
        return false;
      }
      if (this.skillsSearchQuery) {
        const q = this.skillsSearchQuery;
        const matchName = skill.name.toLowerCase().includes(q);
        const matchDesc = skill.description.toLowerCase().includes(q);
        const matchTopics = (skill.topics || []).some(t => t.toLowerCase().includes(q));
        return matchName || matchDesc || matchTopics;
      }
      return true;
    });

    grid.innerHTML = '';
    if (filtered.length === 0) {
      grid.innerHTML = `
        <div class="empty-state-search">
          <span class="empty-icon">🔍</span>
          <p>${I18n.t('skills_empty_state')}</p>
        </div>
      `;
      return;
    }

    filtered.forEach(skill => {
      const card = document.createElement('div');
      card.className = 'skill-card';
      if (this.selectedSkill && this.selectedSkill.id === skill.id) {
        card.classList.add('selected');
      }
      card.setAttribute('data-skill-id', skill.id);

      const topicTags = (skill.topics || []).slice(0, 3)
        .map(t => `<span class="topic-pill">${t}</span>`).join('');

      card.innerHTML = `
        <div class="skill-card-top">
          <span class="skill-icon">${skill.icon || '🌟'}</span>
          <span class="skill-badge">${skill.badge || skill.difficulty}</span>
        </div>
        <h4 class="skill-name">${skill.name}</h4>
        <p class="skill-desc">${skill.description}</p>
        <div class="skill-meta">
          <span class="meta-item">🎯 ${skill.difficulty}</span>
        </div>
        <div class="skill-topics-list">
          ${topicTags}
        </div>
      `;

      card.addEventListener('click', () => this.selectSkill(skill));
      grid.appendChild(card);
    });
  },

  selectSkill(skill) {
    this.selectedSkill = skill;
    const cards = document.querySelectorAll('.skill-card');
    cards.forEach(c => {
      c.classList.toggle('selected', c.getAttribute('data-skill-id') === skill.id);
    });

    const confirmBtn = document.getElementById('btn-confirm-skill');
    if (confirmBtn) confirmBtn.disabled = false;
  },

  confirmSkillSelection() {
    if (!this.selectedSkill) return;

    const updates = {
      learnerType: 'skills',
      skillId: this.selectedSkill.id,
      skillName: this.selectedSkill.name,
      selectedSubjectId: this.selectedSkill.id,
      selectedSubjectName: this.selectedSkill.name,
      selectedTopics: this.selectedSkill.topics || []
    };

    StorageService.saveProfile(updates);
    Onboarding.goToStep('learning-preferences');
  },

  getCategoryLabel(catId) {
    const map = {
      cs_it: I18n.t('college_cat_cs_it'),
      comm_mgmt: I18n.t('college_cat_comm_mgmt'),
      science: I18n.t('college_cat_science'),
      arts_humanities: I18n.t('college_cat_arts_humanities'),
      professional: I18n.t('college_cat_professional')
    };
    return map[catId] || catId;
  }
};

if (typeof module !== 'undefined' && module.exports) {
  module.exports = { CoursesAndSkills };
}
