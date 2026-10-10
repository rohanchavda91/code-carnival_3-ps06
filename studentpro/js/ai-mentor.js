/**
 * AI Mentor UI, conversation state, and replaceable demo response generator.
 */
const AiMentor = {
  messages: [],
  pendingTimer: null,
  requestId: 0,
  maxMessageLength: 2000,
  maxStoredMessages: 80,
  contextSubjectOverride: null,

  init() {
    this.messages = StorageService.getAIMentorConversation()
      .filter(message => message && ['user', 'assistant'].includes(message.role) && typeof message.text === 'string')
      .slice(-this.maxStoredMessages);
    this.bindEvents();
    this.renderMessages();
    this.updateLearnerContext();
    window.addEventListener('synapse:language_changed', () => this.renderMessages());
    window.addEventListener('synapse:ollama_status_changed', () => this.updateLearnerContext());
  },

  bindEvents() {
    const form = document.getElementById('ai-mentor-form');
    const input = document.getElementById('ai-mentor-input');
    const newChatButton = document.getElementById('ai-mentor-new-chat');

    if (form) {
      form.addEventListener('submit', event => {
        event.preventDefault();
        this.sendMessage();
      });
    }

    if (input) {
      input.addEventListener('keydown', event => {
        if (event.key === 'Enter' && !event.shiftKey) {
          event.preventDefault();
          this.sendMessage();
        }
      });
      input.addEventListener('input', () => this.setStatus(''));
    }

    const socraticToggle = document.getElementById('ai-socratic-toggle');
    if (socraticToggle) {
      const cfg = StorageService.getAIConfig();
      socraticToggle.checked = !!cfg.socraticMode;
      socraticToggle.addEventListener('change', (e) => {
        StorageService.saveAIConfig({ socraticMode: e.target.checked });
        this.setStatus(e.target.checked ? I18n.t('ai_mentor_socratic_on') : I18n.t('ai_mentor_mode_standard'));
      });
    }

    if (newChatButton) {
      newChatButton.addEventListener('click', () => this.startNewConversation());
    }

    document.querySelectorAll('.ai-mentor-starter').forEach(button => {
      button.addEventListener('click', () => {
        const prompt = I18n.t(button.getAttribute('data-prompt-key'));
        this.sendMessage(prompt, button.getAttribute('data-prompt-kind'));
      });
    });
  },

  sendMessage(text = null, kindHint = null, context = null) {
    const input = document.getElementById('ai-mentor-input');
    const content = (text === null ? input?.value : text || '').trim();
    if (this.pendingTimer !== null || !content) return;

    if (content.length > this.maxMessageLength) {
      this.setStatus(I18n.t('ai_mentor_error_long'));
      return;
    }

    // Guest mode: spend one daily AI Mentor credit before generating a reply.
    if (typeof GuestLimits !== 'undefined' && !GuestLimits.consume('ai_mentor')) {
      this.setStatus(GuestLimits.statusText('ai_mentor'));
      return;
    }

    if (input && text === null) input.value = '';
    this.setStatus('');
    this.messages.push({ role: 'user', text: content, createdAt: Date.now() });

    const profile = StorageService.getProfile();
    const subjectOverride = context && typeof context.subject === 'string' ? context.subject : null;
    if (subjectOverride) this.contextSubjectOverride = subjectOverride;
    const requestId = ++this.requestId;
    const typing = document.getElementById('ai-mentor-typing');
    const sendButton = document.getElementById('ai-mentor-send');
    if (typing) typing.classList.remove('hidden');
    if (sendButton) sendButton.disabled = true;
    this.renderMessages();
    this.scrollToLatest();

    // Generate response using LocalAIEngine (100% On-Device / Local Ollama)
    (async () => {
      let aiResponse;
      if (typeof LocalAIEngine !== 'undefined') {
        aiResponse = await LocalAIEngine.generateResponse(content, profile, {
          subject: subjectOverride,
          topicContext: context && context.topic
        });
      } else {
        const fallback = this.generateAIResponse(content, profile, kindHint, subjectOverride);
        aiResponse = {
          text: fallback.text,
          badgeText: I18n.t('ai_mentor_badge_local_model'),
          inferenceSource: 'ondevice'
        };
      }

      if (requestId !== this.requestId) return;
      if (typing) typing.classList.add('hidden');
      if (sendButton) sendButton.disabled = false;

      this.messages.push({
        role: 'assistant',
        text: aiResponse.text,
        badgeText: aiResponse.badgeText || I18n.t('ai_mentor_badge_offline'),
        inferenceSource: aiResponse.inferenceSource || 'ondevice_neural',
        createdAt: Date.now()
      });

      this.messages = this.messages.slice(-this.maxStoredMessages);
      StorageService.saveAIMentorConversation(this.messages);
      this.renderMessages();
      this.pendingTimer = null;
    })();
  },

  generateAIResponse(message, learnerProfile, kindHint = null, subjectOverride = null) {
    const aiConfig = StorageService.getAIConfig();
    const isSocratic = !!aiConfig.socraticMode;

    const kind = kindHint || this.classifyRequest(message);
    const topic = subjectOverride
      || learnerProfile.selectedSubjectName
      || learnerProfile.courseName
      || learnerProfile.skillName
      || (learnerProfile.learnerType && I18n.t(`path_${learnerProfile.learnerType}_title`))
      || I18n.t('ai_mentor_topic_fallback');

    const previousResponses = this.messages.filter(item => item.role === 'assistant' && item.kind === kind).length;
    const variant = previousResponses % 2 + 1;
    const text = I18n.t(`ai_mentor_response_${kind}_${variant}`, { subject: topic, prompt: message });
    return { text, kind, variant, topic };
  },

  classifyRequest(message) {
    const text = message.toLocaleLowerCase();
    if (/^(hi|hello|hey|नमस्ते|हेलो|હેલો|નમસ્તે|hola|bonjour|salut|hallo)\b/.test(text)) return 'greeting';
    if (/mistake|wrong|error|गलत|गलती|ભૂલ|equivoc|erreur|fehler/.test(text)) return 'mistake';
    if (/practice|question|quiz|अभ्यास|सवाल|प्रश्न|પ્રશ્ન|અભ્યાસ|práctica|pregunta|exercice|frage/.test(text)) return 'practice';
    if (/example|उदाहरण|દાખલો|ejemplo|exemple|beispiel/.test(text)) return 'example';
    if (/simple|simpler|easy words|सरल|आसान|સરળ|fácil|simplement|einfach/.test(text)) return 'simple';
    if (/explain|meaning|समझा|समझें|व्याख्या|સમજાવો|समझाइए|explica|explique|erklär/.test(text)) return 'explanation';
    return 'general';
  },

  renderMessages() {
    const list = document.getElementById('ai-mentor-messages');
    const emptyState = document.getElementById('ai-mentor-empty');
    if (!list || !emptyState) return;

    list.replaceChildren();
    emptyState.classList.toggle('hidden', this.messages.length > 0);

    this.messages.forEach(message => {
      const row = document.createElement('article');
      row.className = `ai-mentor-message ${message.role}`;

      const avatar = document.createElement('span');
      avatar.className = 'ai-mentor-avatar';
      avatar.setAttribute('aria-hidden', 'true');
      avatar.textContent = message.role === 'assistant' ? '✦' : I18n.t('ai_mentor_you').slice(0, 1).toLocaleUpperCase();

      const body = document.createElement('div');
      body.className = 'ai-mentor-message-body';
      const nameRow = document.createElement('div');
      nameRow.style.cssText = 'display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; margin-bottom: 0.25rem;';
      
      const name = document.createElement('span');
      name.className = 'ai-mentor-message-name';
      name.textContent = message.role === 'assistant' ? I18n.t('ai_mentor_title') : I18n.t('ai_mentor_you');

      nameRow.appendChild(name);

      if (message.role === 'assistant') {
        const badge = document.createElement('span');
        badge.style.cssText = 'font-size: 0.68rem; font-weight: 700; padding: 0.15rem 0.5rem; border-radius: 99px; background: rgba(79, 70, 229, 0.12); color: var(--color-violet-light); border: 1px solid rgba(79, 70, 229, 0.25);';
        let badgeKey = 'local_ai_badge_ondevice';
        if (message.inferenceSource === 'local_ollama') {
          badgeKey = 'local_ai_badge_ollama';
        } else if (message.inferenceSource === 'ondevice') {
          badgeKey = 'ai_mentor_badge_local_model';
        } else if (!message.badgeText) {
          badgeKey = (typeof LocalAIEngine !== 'undefined' && LocalAIEngine.activeMode === 'local_ollama')
            ? 'ai_mentor_badge_short_ollama'
            : 'ai_mentor_badge_offline';
        }
        badge.textContent = I18n.t(badgeKey);
        nameRow.appendChild(badge);
      }

      const bubble = document.createElement('div');
      bubble.className = 'ai-mentor-bubble';
      bubble.style.whiteSpace = 'pre-wrap';
      bubble.textContent = message.text;

      body.append(nameRow, bubble);
      row.append(avatar, body);
      list.appendChild(row);
    });

    this.updateLearnerContext();
    this.scrollToLatest();
  },

  onOpen() {
    this.renderMessages();
  },

  updateLearnerContext() {
    const context = document.getElementById('ai-mentor-context-label');
    if (context) {
      const profile = StorageService.getProfile();
      const subject = this.contextSubjectOverride || profile.selectedSubjectName || profile.courseName || profile.skillName;
      context.textContent = subject
        ? I18n.t('ai_mentor_context_subject', { subject })
        : I18n.t('ai_mentor_context_default');
    }

    const providerBadge = document.getElementById('ai-mentor-provider-badge');
    if (providerBadge && typeof LocalAIEngine !== 'undefined') {
      if (LocalAIEngine.activeMode === 'local_ollama' && LocalAIEngine.ollamaAvailable) {
        providerBadge.textContent = I18n.t('ai_mentor_provider_ollama_model', { model: LocalAIEngine.model });
        providerBadge.style.background = 'rgba(6, 182, 212, 0.15)';
        providerBadge.style.color = 'var(--color-cyan-light)';
      } else {
        providerBadge.textContent = I18n.t('ai_mentor_provider_ondevice');
        providerBadge.style.background = 'rgba(124, 58, 237, 0.15)';
        providerBadge.style.color = 'var(--color-violet-light)';
      }
    }
  },

  startNewConversation() {
    this.requestId += 1;
    if (this.pendingTimer !== null) {
      window.clearTimeout(this.pendingTimer);
      this.pendingTimer = null;
    }
    this.messages = [];
    const typing = document.getElementById('ai-mentor-typing');
    const sendButton = document.getElementById('ai-mentor-send');
    if (typing) typing.classList.add('hidden');
    if (sendButton) sendButton.disabled = false;
    if (!StorageService.clearAIMentorConversation()) {
      this.setStatus(I18n.t('ai_mentor_error_storage'));
    } else {
      this.setStatus('');
    }
    this.renderMessages();
    const input = document.getElementById('ai-mentor-input');
    if (input) input.focus();
  },

  setStatus(message) {
    const status = document.getElementById('ai-mentor-message-status');
    if (status) status.textContent = message;
  },

  scrollToLatest() {
    const conversation = document.getElementById('ai-mentor-conversation');
    if (conversation) conversation.scrollTop = conversation.scrollHeight;
  }
};

if (typeof module !== 'undefined' && module.exports) {
  module.exports = { AiMentor };
}
