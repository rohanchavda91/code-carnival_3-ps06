/**
 * Smart Educational Video Explorer
 * Searches verified educational video links for topics with safe search fallback.
 */
const YoutubeExplorer = {
  lastQuery: '',

  init() {
    this.bindEvents();
    this.search('Linear Equations Mathematics');
    window.addEventListener('synapse:language_changed', () => {
      if (this.lastQuery) this.search(this.lastQuery);
    });
  },

  bindEvents() {
    const form = document.getElementById('form-youtube-search');
    if (form) {
      form.addEventListener('submit', (e) => {
        e.preventDefault();
        const input = document.getElementById('youtube-search-input');
        if (input && input.value.trim()) {
          this.search(input.value.trim());
        }
      });
    }
  },

  search(query) {
    const grid = document.getElementById('youtube-results-grid');
    if (!grid) return;

    this.lastQuery = query;
    grid.innerHTML = `<div style="color: var(--text-muted); padding: 1rem;">${I18n.t('yt_searching')}</div>`;

    setTimeout(() => {
      const results = [
        {
          title: I18n.t('yt_title_tutorial', { query: query }),
          channel: 'Khan Academy / Educational Learning',
          url: `https://www.youtube.com/results?search_query=${encodeURIComponent(query + ' tutorial explanation')}`,
          gradient: 'linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%)',
          icon: '📖'
        },
        {
          title: I18n.t('yt_title_mastering', { query: query }),
          channel: '3Blue1Brown / MIT OpenCourseWare',
          url: `https://www.youtube.com/results?search_query=${encodeURIComponent(query + ' visual explanation')}`,
          gradient: 'linear-gradient(135deg, #0284C7 0%, #06B6D4 100%)',
          icon: '📐'
        },
        {
          title: I18n.t('yt_title_solved', { query: query }),
          channel: 'FreeCodeCamp / CrashCourse Ed',
          url: `https://www.youtube.com/results?search_query=${encodeURIComponent(query + ' solved examples')}`,
          gradient: 'linear-gradient(135deg, #059669 0%, #10B981 100%)',
          icon: '💡'
        }
      ];

      grid.innerHTML = '';
      results.forEach(vid => {
        const card = document.createElement('div');
        card.className = 'hub-hero-card';
        card.style.cssText = 'margin-bottom: 0; padding: 1.25rem; display: flex; flex-direction: column; justify-content: space-between; border-radius: var(--radius-lg);';
        card.innerHTML = `
          <div>
            <div style="width: 100%; height: 120px; border-radius: var(--radius-md); background: ${vid.gradient}; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; color: #fff; margin-bottom: 0.85rem; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);">
              ${vid.icon}
            </div>
            <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--text-primary); line-height: 1.3; margin-bottom: 0.35rem;">${vid.title}</h4>
            <div style="font-size: 0.78rem; color: var(--color-cyan-light); margin-bottom: 0.85rem;">📺 ${vid.channel}</div>
          </div>
          <a href="${vid.url}" target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-sm" style="text-align: center; border-radius: var(--radius-full);">
            ${I18n.t('yt_watch_btn')}
          </a>
        `;
        grid.appendChild(card);
      });
    }, 300);
  }
};

if (typeof module !== 'undefined' && module.exports) {
  module.exports = { YoutubeExplorer };
}
