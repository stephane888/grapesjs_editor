/**
 * @file
 * Contains drupal-storage.js
 */
export default (editor, opts = {}) => {
  const storageManager = editor.StorageManager;

  storageManager.add('drupal', {
    load: (keys, clb) => {
      // Load with components parameter
      clb({});
    },
    store: (data, clb) => {
      if (opts.element) {
        const css = editor.getCss({avoidProtected: true});
        const style = css && `<style>${css}</style>`;
        opts.element.value =style + editor.getHtml();
        opts.element.setAttribute('data-editor-value-is-changed', 'true');
      }
      clb();
    },
  });

  storageManager.setAutosave = true;
  storageManager.setStepsBeforeSave = 1;
};
