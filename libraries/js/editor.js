(function ($, Drupal, grapesjs) {
  Drupal.grapesjs = null;

  Drupal.editors.grapesjs_editor = {
    attach(element, format) {
      console.log(format);

      format.editorSettings.grapesSettings.plugins.forEach(function (name, plugin) {
        if (typeof format.editorSettings.grapesSettings.pluginsOpts[name] === 'undefined') {
          format.editorSettings.grapesSettings.pluginsOpts[name] = {};
        }

        format.editorSettings.grapesSettings.pluginsOpts[name].element = element;
      });

      const grapesSettings = Object.assign({
        // Indicate where to init the editor. You can also pass an HTMLElement
        container: $('.gjs').get(0),
      }, format.editorSettings.grapesSettings);

      Drupal.grapesjs = grapesjs.init(grapesSettings);

      Drupal.grapesjs.on('load', function (editor) {
        /* Disable Drupal form submit */
        $('#gjs-clm-new').on('keydown', function (e) {
          if (e.keyCode === 13) {
            e.preventDefault();
          }
        });
      })
    },

    detach(element, format, trigger) {
      Drupal.grapesjs.destroy();
      $('.gjs').removeAttr('style');
    },

    onChange(element, callback) {
    }
  };
})(jQuery, Drupal, grapesjs);
