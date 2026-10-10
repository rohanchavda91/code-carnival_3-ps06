<?php
/**
 * AI Mentor conversation screen
 */
?>
<section id="screen-ai-mentor" class="app-screen hidden">
  <div class="ai-mentor-page">
    <div class="ai-mentor-heading">
      <div>
        <p class="ai-mentor-eyebrow" data-i18n="ai_mentor_eyebrow">PERSONAL LEARNING SUPPORT</p>
        <h1 class="ai-mentor-title" data-i18n="ai_mentor_title">AI Mentor</h1>
        <p class="ai-mentor-subtitle" data-i18n="ai_mentor_subtitle">Ask questions, work through tricky ideas, and practice at your own pace.</p>
      </div>
      <button type="button" id="ai-mentor-new-chat" class="btn btn-secondary ai-mentor-new-chat">
        <span aria-hidden="true">＋</span>
        <span data-i18n="ai_mentor_new_conversation">New conversation</span>
      </button>
    </div>

    <div class="ai-mentor-card">
      <div class="ai-mentor-context" aria-live="polite" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
        <div style="display: flex; align-items: center; gap: 0.5rem;">
          <span class="ai-mentor-status-dot" aria-hidden="true"></span>
          <span id="ai-mentor-context-label" data-i18n="ai_mentor_context_default">Your learning mentor</span>
        </div>

        <div style="display: flex; align-items: center; gap: 1rem;">
          <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.8rem; font-weight: 600; color: var(--color-cyan); cursor: pointer;">
            <input type="checkbox" id="ai-socratic-toggle" />
            <span>🧩 <span data-i18n="ai_mentor_socratic_mode">Socratic Guiding Mode</span></span>
          </label>
          <span id="ai-mentor-provider-badge" class="ai-mentor-demo-label">Local LLM / Ollama</span>
        </div>
      </div>

      <div id="ai-mentor-conversation" class="ai-mentor-conversation" role="log" aria-live="polite" aria-relevant="additions text" data-i18n-aria-label="ai_mentor_conversation_label" aria-label="AI Mentor conversation">
        <div id="ai-mentor-empty" class="ai-mentor-empty">
          <span class="ai-mentor-empty-mark" aria-hidden="true">✦</span>
          <h2 data-i18n="ai_mentor_empty_title">How can I help you learn today?</h2>
          <p data-i18n="ai_mentor_empty_text">Choose a prompt to get started, or ask your own question below.</p>
          <div class="ai-mentor-starters" data-i18n-aria-label="ai_mentor_starters_label" aria-label="Suggested prompts">
            <button type="button" class="ai-mentor-starter" data-prompt-key="ai_mentor_prompt_explain" data-prompt-kind="explanation" data-i18n="ai_mentor_prompt_explain">Explain this topic to me</button>
            <button type="button" class="ai-mentor-starter" data-prompt-key="ai_mentor_prompt_example" data-prompt-kind="example" data-i18n="ai_mentor_prompt_example">Give me an example</button>
            <button type="button" class="ai-mentor-starter" data-prompt-key="ai_mentor_prompt_mistake" data-prompt-kind="mistake" data-i18n="ai_mentor_prompt_mistake">Help me understand my mistake</button>
            <button type="button" class="ai-mentor-starter" data-prompt-key="ai_mentor_prompt_practice" data-prompt-kind="practice" data-i18n="ai_mentor_prompt_practice">Give me practice questions</button>
            <button type="button" class="ai-mentor-starter" data-prompt-key="ai_mentor_prompt_simple" data-prompt-kind="simple" data-i18n="ai_mentor_prompt_simple">Explain it in simple words</button>
          </div>
        </div>
        <div id="ai-mentor-messages" class="ai-mentor-messages"></div>
        <div id="ai-mentor-typing" class="ai-mentor-typing hidden" role="status">
          <span class="ai-mentor-avatar" aria-hidden="true">✦</span>
          <span data-i18n="ai_mentor_typing">Your mentor is thinking</span>
          <span class="ai-mentor-typing-dots" aria-hidden="true"><i></i><i></i><i></i></span>
        </div>
      </div>

      <form id="ai-mentor-form" class="ai-mentor-composer">
        <label class="sr-only" for="ai-mentor-input" data-i18n="ai_mentor_input_label">Type your question</label>
        <textarea id="ai-mentor-input" class="ai-mentor-input" rows="1" maxlength="2000" data-i18n-placeholder="ai_mentor_placeholder" placeholder="Ask a question about what you’re learning..." aria-describedby="ai-mentor-message-status"></textarea>
        <button type="submit" id="ai-mentor-send" class="btn btn-primary ai-mentor-send" data-i18n-aria-label="ai_mentor_send" aria-label="Send message">
          <span data-i18n="ai_mentor_send">Send</span>
          <span aria-hidden="true">↑</span>
        </button>
        <p id="ai-mentor-message-status" class="ai-mentor-message-status" role="status" aria-live="polite"></p>
      </form>
    </div>
  </div>
</section>
