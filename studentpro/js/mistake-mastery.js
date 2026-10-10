/**
 * Mistake-to-Mastery Review Engine
 * Renders flagged questions, provides explanations, and allows re-testing for mastery.
 */
const MistakeMastery = {
  init() {
    this.render();
    window.addEventListener('synapse:language_changed', () => this.render());
  },

  render() {
    const listEl = document.getElementById('mistake-mastery-list');
    const countEl = document.getElementById('mistake-mastery-count');
    if (!listEl) return;

    const mistakes = StorageService.getMistakes();
    if (countEl) countEl.textContent = I18n.t('mistake_logged_items', { count: mistakes.length });

    listEl.innerHTML = '';

    if (mistakes.length === 0) {
      listEl.innerHTML = `
        <div style="text-align: center; padding: 2rem; color: var(--text-muted);">
          ${I18n.t('mistake_empty_state')}
        </div>
      `;
      return;
    }

    mistakes.forEach(item => {
      const card = document.createElement('div');
      card.style.cssText = `padding: 1rem; border: 1px solid ${item.mastered ? 'rgba(52, 211, 153, 0.3)' : 'rgba(251, 191, 36, 0.3)'}; border-radius: 10px; background: ${item.mastered ? 'rgba(52, 211, 153, 0.05)' : 'rgba(251, 191, 36, 0.05)'};`;
      card.innerHTML = `
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
          <span class="badge-popular" style="font-size: 0.75rem;">${I18n.t('mistake_topic_label', { topic: item.topic })}</span>
          <span style="font-size: 0.75rem; color: ${item.mastered ? 'var(--color-emerald)' : '#fbbf24'}; font-weight: 600;">
            ${item.mastered ? I18n.t('mistake_mastered_badge') : I18n.t('mistake_action_needed')}
          </span>
        </div>
        <div style="font-size: 0.95rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">
          ${item.question}
        </div>
        <div style="display: flex; flex-direction: column; gap: 0.3rem; font-size: 0.85rem; margin-bottom: 0.75rem;">
          <div style="color: var(--color-rose);">${I18n.t('mistake_your_response', { answer: item.userAnswer })}</div>
          <div style="color: var(--color-emerald);">${I18n.t('mistake_correct_solution', { answer: item.correctAnswer })}</div>
          <div style="color: var(--text-dim); margin-top: 0.2rem;">${I18n.t('mistake_hint_label', { hint: item.explanation })}</div>
        </div>
        ${!item.mastered ? `
          <button type="button" class="btn btn-primary btn-sm btn-master-item" data-id="${item.id}">
            ${I18n.t('mistake_btn_master')}
          </button>
        ` : ''}
      `;
      listEl.appendChild(card);
    });

    // Bind re-test mastery buttons
    setTimeout(() => {
      const masterBtns = listEl.querySelectorAll('.btn-master-item');
      masterBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
          const id = e.target.getAttribute('data-id');
          StorageService.markMistakeMastered(id);
          this.render();
        });
      });
    }, 50);
  }
};

if (typeof module !== 'undefined' && module.exports) {
  module.exports = { MistakeMastery };
}
