/**
 * @file
 * Contains drupal-storage.js
 */
import $ from 'jquery';

export default (editor, opts = {}) => {
  const storageManager = editor.StorageManager;

  storageManager.add('drupal', {
    load: (keys, clb) => {
      // Load with components parameter
      clb({});
    },
    store: (data, clb) => {
      const $container = $(editor.getContainer());
      const fieldName = $container.data('field-name');
      const $element = $container.parent().find(`[name="${fieldName}"]`);
      const css = editor.getCss({avoidProtected: true});
      const style = css && `<style>${css}</style>`;
      $element.val(style + editor.getHtml());
      $element.attr('data-editor-value-is-changed', 'true');
      clb();
    },
  });

  storageManager.setAutosave = true;
  storageManager.setStepsBeforeSave = 1;
};
