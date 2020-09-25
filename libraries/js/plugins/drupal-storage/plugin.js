/**
 * @file
 * Contains drupal-storage.js
 */
(function ($, Drupal, grapesjs) {
  grapesjs.plugins.add('drupal-storage', function (editor, options) {
    const storageManager = editor.StorageManager;

    storageManager.add('drupal', {
      load: function (keys, clb) {
        // Load with init function parameter "fromElement"
        clb({});
      },
      store: function (data, clb) {
        if (options.element) {
          const css = editor.getCss({ avoidProtected: true });
          const style = css && `<style>${css}</style>`;
          $(options.element).val(style + editor.getHtml()).attr('data-editor-value-is-changed', 'true');;
        }
        clb();
      },
    });

    storageManager.setAutosave = true;
    storageManager.setStepsBeforeSave = 1;
  });

})(jQuery, Drupal, grapesjs);
