/**
 * Personal Notes Maker Engine
 * Create, edit, delete, search, and export notes locally with offline support.
 */
const NotesMaker = {
  activeNoteId: null,

  init() {
    this.bindEvents();
    this.renderNotesList();
    window.addEventListener('synapse:language_changed', () => {
      const searchInput = document.getElementById('notes-search-input');
      this.renderNotesList(searchInput ? searchInput.value : '');
    });
  },

  bindEvents() {
    const btnNew = document.getElementById('btn-notes-new');
    if (btnNew) {
      btnNew.addEventListener('click', () => this.createNewNote());
    }

    const searchInput = document.getElementById('notes-search-input');
    if (searchInput) {
      searchInput.addEventListener('input', (e) => this.renderNotesList(e.target.value));
    }

    const form = document.getElementById('form-note-editor');
    if (form) {
      form.addEventListener('submit', (e) => {
        e.preventDefault();
        this.saveCurrentNote();
      });
    }

    const btnDelete = document.getElementById('btn-note-delete');
    if (btnDelete) {
      btnDelete.addEventListener('click', () => this.deleteCurrentNote());
    }

    const btnExportTxt = document.getElementById('btn-note-export-txt');
    if (btnExportTxt) {
      btnExportTxt.addEventListener('click', () => this.exportCurrentNoteTXT());
    }
  },

  renderNotesList(filterQuery = '') {
    const listContainer = document.getElementById('notes-list-container');
    if (!listContainer) return;

    const notes = StorageService.getNotes();
    const query = filterQuery.toLowerCase();

    const filtered = notes.filter(n =>
      n.title.toLowerCase().includes(query) ||
      n.content.toLowerCase().includes(query) ||
      (n.tags && n.tags.some(t => t.toLowerCase().includes(query)))
    );

    listContainer.innerHTML = '';

    if (filtered.length === 0) {
      listContainer.innerHTML = `<div style="font-size: 0.8rem; color: var(--text-muted); padding: 0.5rem;">${I18n.t('notes_empty_list')}</div>`;
      return;
    }

    filtered.forEach(note => {
      const item = document.createElement('div');
      item.style.cssText = `padding: 0.6rem 0.75rem; border: 1px solid ${this.activeNoteId === note.id ? 'var(--color-cyan-light)' : 'var(--border-subtle)'}; border-radius: 8px; background: ${this.activeNoteId === note.id ? 'rgba(6, 182, 212, 0.12)' : 'var(--bg-surface-elevated)'}; cursor: pointer;`;
      item.innerHTML = `
        <div style="font-size: 0.88rem; font-weight: 600; color: var(--text-primary);">${note.title || I18n.t('note_untitled')}</div>
        <div style="font-size: 0.75rem; color: var(--text-dim); margin-top: 0.2rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
          ${note.content || I18n.t('notes_empty_note')}
        </div>
      `;
      item.addEventListener('click', () => this.loadNoteIntoEditor(note.id));
      listContainer.appendChild(item);
    });

    if (!this.activeNoteId && filtered.length > 0) {
      this.loadNoteIntoEditor(filtered[0].id);
    }
  },

  loadNoteIntoEditor(id) {
    const notes = StorageService.getNotes();
    const note = notes.find(n => n.id === id);
    if (!note) return;

    this.activeNoteId = note.id;

    document.getElementById('note-edit-id').value = note.id;
    document.getElementById('note-edit-title').value = note.title || '';
    document.getElementById('note-edit-content').value = note.content || '';
    document.getElementById('note-edit-tags').value = (note.tags || []).join(', ');

    this.renderNotesList();
  },

  createNewNote() {
    this.activeNoteId = null;
    document.getElementById('note-edit-id').value = '';
    document.getElementById('note-edit-title').value = I18n.t('notes_new_title');
    document.getElementById('note-edit-content').value = '';
    document.getElementById('note-edit-tags').value = I18n.t('tag_general');

    this.saveCurrentNote();
  },

  saveCurrentNote() {
    const id = document.getElementById('note-edit-id').value;

    // Guest mode: only creating a NEW note spends a daily credit (edits are free).
    if (!id && typeof GuestLimits !== 'undefined' && !GuestLimits.consume('notes_maker')) return;

    const title = document.getElementById('note-edit-title').value.trim();
    const content = document.getElementById('note-edit-content').value;
    const tagsRaw = document.getElementById('note-edit-tags').value;
    const tags = tagsRaw.split(',').map(t => t.trim()).filter(Boolean);

    const saved = StorageService.saveNote({
      id: id || undefined,
      title: title || I18n.t('note_untitled'),
      content: content,
      tags: tags.length > 0 ? tags : [I18n.t('tag_general')]
    });

    if (saved && saved.savedNotes && saved.savedNotes.length > 0) {
      this.activeNoteId = saved.savedNotes[0].id;
      document.getElementById('note-edit-id').value = this.activeNoteId;
    }

    const statusEl = document.getElementById('note-save-status');
    if (statusEl) {
      statusEl.textContent = I18n.t('notes_saved_status');
      setTimeout(() => { statusEl.textContent = I18n.t('notes_autosave_status'); }, 2000);
    }

    this.renderNotesList();
  },

  deleteCurrentNote() {
    if (!this.activeNoteId) return;
    if (confirm(I18n.t('notes_delete_confirm'))) {
      StorageService.deleteNote(this.activeNoteId);
      this.activeNoteId = null;
      document.getElementById('note-edit-title').value = '';
      document.getElementById('note-edit-content').value = '';
      document.getElementById('note-edit-tags').value = '';
      this.renderNotesList();
    }
  },

  exportCurrentNoteTXT() {
    const title = document.getElementById('note-edit-title').value || I18n.t('notes_export_fallback_title');
    const content = document.getElementById('note-edit-content').value || '';
    const tags = document.getElementById('note-edit-tags').value || '';

    const fileContent = `========================================\n${I18n.t('notes_export_header')}\n${I18n.t('notes_export_title_label')} ${title}\n${I18n.t('notes_export_tags_label')} ${tags}\n${I18n.t('notes_export_date_label')} ${new Date().toLocaleString()}\n========================================\n\n${content}\n`;

    const blob = new Blob([fileContent], { type: 'text/plain;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `${title.replace(/[^a-z0-9]/gi, '_').toLowerCase()}_note.txt`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
  }
};

if (typeof module !== 'undefined' && module.exports) {
  module.exports = { NotesMaker };
}
