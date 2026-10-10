<?php
/**
 * View: Personal Notes Maker
 * Create, edit, search, tag, and export study notes locally with offline support.
 */
?>
<section id="screen-notes-maker" class="app-screen hidden">
  <div style="max-width: 1000px; margin: 0 auto; padding: 1rem 0;">

    <!-- Banner -->
    <div class="hub-hero-card" style="margin-bottom: 1.5rem;">
      <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
          <span class="badge-popular" style="margin-bottom: 0.4rem; display: inline-block;" data-i18n="notes_feature_badge">📝 Feature 13: Offline Notes</span>
          <h1 style="font-size: 1.6rem; font-weight: 700; color: var(--text-primary);" data-i18n="notes_title">Personal Notes Maker</h1>
          <p style="font-size: 0.9rem; color: var(--text-muted);" data-i18n="notes_subtitle">
            Capture key concepts, organizing by tags and exporting downloadable text files offline.
          </p>
        </div>
        <button type="button" class="btn btn-secondary btn-back-to-hub">
          ← <span data-i18n="btn_back_to_dashboard">Back to Dashboard</span>
        </button>
      </div>
    </div>

    <!-- Main Workspace Layout -->
    <div style="display: grid; grid-template-columns: 300px 1fr; gap: 1.25rem;">
      
      <!-- Left Sidebar: Saved Notes List -->
      <div class="hub-hero-card" style="margin-bottom: 0; padding: 1rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
          <h4 style="font-size: 0.95rem; font-weight: 600; color: var(--text-primary);" data-i18n="notes_saved_heading">Saved Notes</h4>
          <button type="button" id="btn-notes-new" class="btn btn-primary btn-sm" style="padding: 0.25rem 0.5rem;" data-i18n="notes_btn_new">
            + New Note
          </button>
        </div>

        <input type="text" id="notes-search-input" class="input-text" style="font-size: 0.8rem; padding: 0.4rem 0.6rem; margin-bottom: 0.75rem;" data-i18n-placeholder="notes_search_placeholder" placeholder="Filter notes..." />

        <div id="notes-list-container" style="display: flex; flex-direction: column; gap: 0.5rem; max-height: 400px; overflow-y: auto;">
          <!-- Injected dynamically -->
        </div>
      </div>

      <!-- Right Column: Note Editor -->
      <div class="hub-hero-card" style="margin-bottom: 0; padding: 1.25rem;">
        <form id="form-note-editor">
          <input type="hidden" id="note-edit-id" value="" />

          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <input type="text" id="note-edit-title" class="input-text" style="font-size: 1.2rem; font-weight: 700; border: none; background: transparent; padding: 0; color: var(--text-primary);" data-i18n-placeholder="notes_title_placeholder" placeholder="Note Title..." required />
            
            <div style="display: flex; gap: 0.5rem;">
              <button type="button" id="btn-note-export-txt" class="btn btn-secondary btn-sm" title="Export note as TXT file" data-i18n-title="notes_tooltip_export" data-i18n="notes_btn_export_txt">
                📥 Export TXT
              </button>
              <button type="button" id="btn-note-delete" class="btn btn-secondary btn-sm" style="color: var(--color-rose);" title="Delete note" data-i18n-title="notes_tooltip_delete" data-i18n="notes_btn_delete">
                🗑️ Delete
              </button>
            </div>
          </div>

          <div class="form-group">
            <input type="text" id="note-edit-tags" class="input-text" style="font-size: 0.85rem;" data-i18n-placeholder="notes_tags_placeholder" placeholder="Tags (comma separated, e.g. Math, Formulas, ExamPrep)" />
          </div>

          <div class="form-group">
            <textarea id="note-edit-content" class="input-text" rows="12" style="font-family: var(--font-sans); line-height: 1.5; font-size: 0.95rem;" data-i18n-placeholder="notes_content_placeholder" placeholder="Type your personal study notes, key takeaways, and formulas here..." required></textarea>
          </div>

          <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1rem;">
            <span id="note-save-status" style="font-size: 0.8rem; color: var(--color-emerald);" data-i18n="notes_autosave_status">Autosaved locally</span>
            <button type="submit" class="btn btn-primary" data-i18n="notes_btn_save">
              💾 Save Note
            </button>
          </div>
        </form>
      </div>

    </div>

  </div>
</section>
