/**
 * @copyright  (C) 2023 Open Source Matters, Inc. <https://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// eslint-disable-next-line import/no-unresolved
import { createFromTextarea, EditorState, keymap } from 'codemirror';

class CodemirrorEditor extends HTMLElement {
  constructor() {
    super();

    this.toggleFullScreen = () => {
      if (!this.classList.contains('fullscreen')) {
        this.classList.add('fullscreen');
        document.documentElement.scrollTop = 0;
        document.documentElement.style.overflow = 'hidden';
      } else {
        this.closeFullScreen();
      }
    };

    this.closeFullScreen = () => {
      this.classList.remove('fullscreen');
      document.documentElement.style.overflow = '';
    };
  }

  get options() { return JSON.parse(this.getAttribute('options')); }

  get fsCombo() { return this.getAttribute('fs-combo'); }

  async connectedCallback() {
    const { options } = this;

    // Configure full screen feature
    if (this.fsCombo) {
      options.customExtensions = options.customExtensions || [];
      options.customExtensions.push(() => keymap.of([
        { key: this.fsCombo, run: this.toggleFullScreen },
        { key: 'Escape', run: this.closeFullScreen },
      ]));

      // Relocate BS modals, to resolve z-index issue in full screen
      this.bsModals = this.querySelectorAll('.joomla-modal.modal');
      this.bsModals.forEach((modal) => {
        document.body.appendChild(modal);
      });
    }

    // Create an editor instance
    this.element = this.querySelector('textarea');
    const editor = await createFromTextarea(this.element, options);
    this.instance = editor;

    // Register Editor for Joomla api
    Joomla.editors.instances[this.element.id] = {
      id: () => this.element.id,
      element: () => this.element,
      getValue: () => editor.state.doc.toString(),
      setValue: (text) => {
        editor.dispatch({
          changes: { from: 0, to: editor.state.doc.length, insert: text },
        });
      },
      getSelection: () => editor.state.sliceDoc(
        editor.state.selection.main.from,
        editor.state.selection.main.to,
      ),
      replaceSelection: (text) => {
        const v = editor.state.replaceSelection(text);
        editor.dispatch(v);
      },
      disable: (disabled) => {
        editor.state.config.compartments.forEach((facet, compartment) => {
          if (compartment.$j_name === 'readOnly') {
            editor.dispatch({
              effects: compartment.reconfigure(EditorState.readOnly.of(disabled)),
            });
          }
        });
      },
      onSave: () => {},
      refresh: () => {},
    };
  }

  disconnectedCallback() {
    if (this.instance) {
      this.element.style.display = '';
      this.instance.destroy();
    }
    // Remove from the Joomla API
    delete Joomla.editors.instances[this.element.id];

    // Restore modals
    if (this.bsModals && this.bsModals.length) {
      this.bsModals.forEach((modal) => {
        this.appendChild(modal);
      });
    }
  }
}

customElements.define('joomla-editor-codemirror', CodemirrorEditor);
