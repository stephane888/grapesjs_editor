(function ($, Drupal, grapesjs) {
  Drupal.grapesjs = null;

  Drupal.editors.grapesjs_editor = {
    attach(element, format) {
      format.editorSettings.grapesSettings.plugins.forEach((name, plugin) => {
        if (typeof format.editorSettings.grapesSettings.pluginsOpts[name] === 'undefined') {
          format.editorSettings.grapesSettings.pluginsOpts[name] = {};
        }

        format.editorSettings.grapesSettings.pluginsOpts[name].element = element;
      });

      const grapesSettings = {
        // Indicate where to init the editor. You can also pass an HTMLElement
        container: $('.gjs').get(0),
        ...format.editorSettings.grapesSettings
      };

      Drupal.grapesjs = grapesjs.init(grapesSettings);

      Drupal.grapesjs.on('load', () => {
        /* Disable Drupal form submit */
        $('.gjs input').on('keydown', function (e) {
          if (e.keyCode === 13) {
            e.preventDefault();
          }
        });
      })
    },

    detach() {
      Drupal.grapesjs.destroy();
      $('.gjs').removeAttr('style');
    },

    onChange() {
    }
  };
})(jQuery, Drupal, grapesjs);
