const ExploreLearning = {
  apiUrl: new URL('./api/endpoints/explore-learning.php', window.location.href),
  departments: [],
  subjects: [],
  topics: [],
  progress: new Map(),
  progressUpdates: new Set(),
  selectedDepartmentId: null,
  selectedSubjectId: null,
  departmentRequest: 0,
  topicRequest: 0,
  initialized: false,

  init() {
    if (this.initialized) return;
    this.initialized = true;

    document.getElementById('explore-department-search')?.addEventListener('input', event => {
      this.renderDepartments(event.target.value);
    });
    document.getElementById('explore-departments')?.addEventListener('click', event => {
      const button = event.target.closest('[data-department-id]');
      if (button) this.selectDepartment(Number(button.dataset.departmentId));
    });
    document.getElementById('explore-subjects')?.addEventListener('click', event => {
      const button = event.target.closest('[data-subject-id]');
      if (button) this.selectSubject(Number(button.dataset.subjectId));
    });
    document.getElementById('explore-topics')?.addEventListener('click', event => {
      const completeButton = event.target.closest('[data-complete-topic-id]');
      if (completeButton) {
        this.completeTopic(Number(completeButton.dataset.completeTopicId), completeButton);
        return;
      }
      const button = event.target.closest('[data-topic-id]');
      if (button) this.startTopic(Number(button.dataset.topicId));
    });
    document.getElementById('explore-retry')?.addEventListener('click', () => this.loadDepartments());
    window.addEventListener('synapse:language_changed', () => {
      this.renderDepartments(document.getElementById('explore-department-search')?.value || '');
      this.renderSubjects();
      this.renderTopics();
    });
    this.loadDepartments();
  },

  t(key, fallback) {
    return typeof I18n !== 'undefined' ? I18n.t(key, {}, fallback) : fallback;
  },

  async request(resource, params = {}, options = {}) {
    const url = new URL(this.apiUrl);
    url.searchParams.set('resource', resource);
    Object.entries(params).forEach(([key, value]) => url.searchParams.set(key, String(value)));
    const response = await fetch(url, {
      credentials: 'same-origin',
      cache: 'no-store',
      ...options
    });
    let result;
    try {
      result = await response.json();
    } catch (error) {
      throw new Error(this.t('explore_error', 'Learning catalog could not be loaded.'));
    }
    if (!response.ok || !result.success) {
      throw new Error(result.error || this.t('explore_error', 'Learning catalog could not be loaded.'));
    }
    return result.data;
  },

  setLoading(isLoading) {
    const status = document.getElementById('explore-status');
    if (status) {
      status.textContent = isLoading ? this.t('explore_loading', 'Loading learning catalog...') : '';
    }
    const error = document.getElementById('explore-error');
    if (error && isLoading) error.classList.add('hidden');
  },

  showError(error) {
    console.error('Explore Learning request failed:', error);
    this.setLoading(false);
    const container = document.getElementById('explore-error');
    if (container) container.classList.remove('hidden');
  },

  async loadDepartments() {
    this.setLoading(true);
    try {
      this.departments = await this.request('departments');
      this.renderDepartments(document.getElementById('explore-department-search')?.value || '');
      this.setLoading(false);
    } catch (error) {
      this.showError(error);
    }
  },

  renderDepartments(query = '') {
    const container = document.getElementById('explore-departments');
    if (!container) return;
    const normalizedQuery = query.trim().toLocaleLowerCase();
    const filtered = this.departments.filter(department =>
      `${department.name} ${department.description} ${department.code}`.toLocaleLowerCase().includes(normalizedQuery)
    );
    container.replaceChildren();

    if (!filtered.length) {
      this.appendEmpty(container, normalizedQuery
        ? this.t('explore_no_search_results', 'No departments match your search.')
        : this.t('explore_no_departments', 'No departments are available yet.'));
      return;
    }

    filtered.forEach(department => {
      const selected = Number(department.id) === this.selectedDepartmentId;
      const button = document.createElement('button');
      button.type = 'button';
      button.className = `explore-choice${selected ? ' selected' : ''}`;
      button.dataset.departmentId = department.id;
      button.setAttribute('aria-pressed', String(selected));

      const icon = document.createElement('span');
      icon.className = 'explore-choice-icon';
      icon.setAttribute('aria-hidden', 'true');
      icon.textContent = '🎓';
      const copy = document.createElement('span');
      copy.className = 'explore-choice-copy';
      const name = document.createElement('strong');
      name.textContent = department.name;
      const description = document.createElement('span');
      description.textContent = department.description;
      copy.append(name, description);
      button.append(icon, copy);

      if (selected) {
        const check = document.createElement('span');
        check.className = 'explore-choice-check';
        check.setAttribute('aria-hidden', 'true');
        check.textContent = '✓';
        button.appendChild(check);
      }
      container.appendChild(button);
    });
  },

  async selectDepartment(departmentId) {
    if (!this.departments.some(department => Number(department.id) === departmentId)) return;
    this.selectedDepartmentId = departmentId;
    this.selectedSubjectId = null;
    this.subjects = [];
    this.topics = [];
    this.progress.clear();
    this.renderDepartments(document.getElementById('explore-department-search')?.value || '');
    this.renderSubjects();
    this.renderTopics();

    const requestId = ++this.departmentRequest;
    this.setLoading(true);
    try {
      const subjects = await this.request('subjects', { departmentId });
      if (requestId !== this.departmentRequest || departmentId !== this.selectedDepartmentId) return;
      this.subjects = subjects;
      this.renderSubjects();
      this.setLoading(false);
    } catch (error) {
      if (requestId === this.departmentRequest) this.showError(error);
    }
  },

  renderSubjects() {
    const container = document.getElementById('explore-subjects');
    const prompt = document.getElementById('explore-subject-prompt');
    if (!container || !prompt) return;
    container.replaceChildren();

    if (this.selectedDepartmentId === null) {
      prompt.textContent = this.t('explore_select_department', 'Select a department first.');
      return;
    }
    prompt.textContent = '';
    if (!this.subjects.length) {
      this.appendEmpty(container, this.t('explore_no_subjects', 'No subjects are available for this department yet.'));
      return;
    }
    this.subjects.forEach(subject => {
      const selected = Number(subject.id) === this.selectedSubjectId;
      const button = document.createElement('button');
      button.type = 'button';
      button.className = `explore-choice${selected ? ' selected' : ''}`;
      button.dataset.subjectId = subject.id;
      button.setAttribute('aria-pressed', String(selected));
      const icon = document.createElement('span');
      icon.className = 'explore-choice-icon';
      icon.setAttribute('aria-hidden', 'true');
      icon.textContent = '📘';
      const copy = document.createElement('span');
      copy.className = 'explore-choice-copy';
      const name = document.createElement('strong');
      name.textContent = subject.name;
      const description = document.createElement('span');
      description.textContent = subject.description;
      copy.append(name, description);
      button.append(icon, copy);
      if (selected) {
        const check = document.createElement('span');
        check.className = 'explore-choice-check';
        check.setAttribute('aria-hidden', 'true');
        check.textContent = '✓';
        button.appendChild(check);
      }
      container.appendChild(button);
    });
  },

  async selectSubject(subjectId) {
    const selectedSubject = this.subjects.find(subject => Number(subject.id) === subjectId);
    if (!selectedSubject || Number(selectedSubject.department_id) !== this.selectedDepartmentId) return;
    this.selectedSubjectId = subjectId;
    this.topics = [];
    this.progress.clear();
    this.renderSubjects();
    this.renderTopics();

    const requestId = ++this.topicRequest;
    this.setLoading(true);
    try {
      const [topics, progress] = await Promise.all([
        this.request('topics', { subjectId }),
        this.request('progress')
      ]);
      if (requestId !== this.topicRequest || subjectId !== this.selectedSubjectId) return;
      this.topics = topics;
      this.progress = new Map(progress.map(item => [Number(item.topic_id), item.status]));
      this.renderTopics();
      this.setLoading(false);
    } catch (error) {
      if (requestId === this.topicRequest) this.showError(error);
    }
  },

  renderTopics() {
    const container = document.getElementById('explore-topics');
    const prompt = document.getElementById('explore-topic-prompt');
    if (!container || !prompt) return;
    container.replaceChildren();

    if (this.selectedSubjectId === null) {
      prompt.textContent = this.t('explore_select_subject', 'Select a subject to see its chapters and topics.');
      return;
    }
    prompt.textContent = '';
    if (!this.topics.length) {
      this.appendEmpty(container, this.t('explore_no_topics', 'No topics are available for this subject yet.'));
      return;
    }

    this.topics.forEach(topic => {
      const card = document.createElement('article');
      card.className = 'explore-topic-card';
      const title = document.createElement('h3');
      title.textContent = topic.title;
      const description = document.createElement('p');
      description.textContent = topic.description || this.t('explore_topic_description', 'Explore this topic step by step.');
      const meta = document.createElement('div');
      meta.className = 'explore-topic-meta';
      if (topic.chapter_number) this.appendMeta(meta, `${this.t('explore_chapter', 'Chapter')} ${topic.chapter_number}`);
      if (topic.difficulty) this.appendMeta(meta, topic.difficulty);
      const status = this.progress.get(Number(topic.id)) || 'not_started';
      const statusLabel = document.createElement('span');
      statusLabel.className = `explore-topic-status ${status}`;
      statusLabel.textContent = this.t(`explore_status_${status}`, 'Not Started');
      const start = document.createElement('button');
      start.type = 'button';
      start.className = 'btn btn-primary';
      start.dataset.topicId = topic.id;
      start.textContent = this.t('explore_start_learning', 'Start Learning');
      const actions = document.createElement('div');
      actions.className = 'explore-topic-actions';
      actions.appendChild(start);
      if (status === 'in_progress') {
        const complete = document.createElement('button');
        complete.type = 'button';
        complete.className = 'btn btn-secondary';
        complete.dataset.completeTopicId = topic.id;
        complete.textContent = this.t('explore_mark_complete', 'Mark Complete');
        actions.appendChild(complete);
      }
      card.append(title, description, meta, statusLabel, actions);
      container.appendChild(card);
    });
  },

  appendMeta(container, label) {
    const item = document.createElement('span');
    item.textContent = label;
    container.appendChild(item);
  },

  appendEmpty(container, message) {
    const empty = document.createElement('p');
    empty.className = 'explore-empty';
    empty.textContent = message;
    container.appendChild(empty);
  },

  async completeTopic(topicId, button) {
    if (this.progress.get(topicId) !== 'in_progress' || this.progressUpdates.has(topicId)) return;
    this.progressUpdates.add(topicId);
    button.disabled = true;
    this.setLoading(true);
    try {
      await this.request('progress', {}, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ topicId, status: 'completed' })
      });
      this.progress.set(topicId, 'completed');
      this.renderTopics();
    } catch (error) {
      this.showError(error);
    } finally {
      this.progressUpdates.delete(topicId);
      this.setLoading(false);
    }
  },

  async startTopic(topicId) {
    const topic = this.topics.find(item => Number(item.id) === topicId);
    const subject = this.subjects.find(item => Number(item.id) === this.selectedSubjectId);
    const department = this.departments.find(item => Number(item.id) === this.selectedDepartmentId);
    if (!topic || !subject || !department || this.progressUpdates.has(topicId)) return;
    this.progressUpdates.add(topicId);
    const startButton = document.querySelector(`#explore-topics [data-topic-id="${topicId}"]`);
    if (startButton) startButton.disabled = true;

    const status = this.progress.get(topicId) || 'not_started';
    if (status === 'not_started') {
      this.setLoading(true);
      try {
        await this.request('progress', {}, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ topicId, status: 'in_progress' })
        });
        this.progress.set(topicId, 'in_progress');
      } catch (error) {
        this.showError(error);
        this.progressUpdates.delete(topicId);
        if (startButton) startButton.disabled = false;
        return;
      }
      this.setLoading(false);
    }

    const selection = {
      departmentId: Number(department.id),
      departmentName: department.name,
      subjectId: Number(subject.id),
      subjectName: subject.name,
      topicId: Number(topic.id),
      topicName: topic.title
    };
    StorageService.saveProfile({ exploreLearning: selection });

    const language = I18n.getLanguage();
    const languageInstruction = language === 'hi'
      ? 'हिंदी में'
      : language === 'gu' ? 'ગુજરાતીમાં' : 'in simple language';
    const prompt = `Teach me "${topic.title}" in the context of ${subject.name} (${department.name}) ${languageInstruction}. Give a clear step-by-step explanation, useful notes, one relevant example, and a short practice question.`;
    const context = {
      subject: `${department.name} · ${subject.name}`,
      topic: { title: topic.title, description: topic.description }
    };

    this.progressUpdates.delete(topicId);
    Onboarding.goToStep('ai-mentor');
    AiMentor.sendMessage(prompt, 'explanation', context);
    this.renderTopics();
  }
};
