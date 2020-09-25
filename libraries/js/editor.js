(function ($, Drupal, grapesjs) {
  Drupal.grapesjs = null;

  Drupal.editors.grapesjs_editor = {
    attach(element, format) {
      console.log(format);
      const defaultValue = $(element).val();
      $('.gjs').html(defaultValue);

      format.editorSettings.grapesSettings.plugins.forEach(function (name, plugin) {
        if (typeof format.editorSettings.grapesSettings.pluginsOpts[name] === 'undefined') {
          format.editorSettings.grapesSettings.pluginsOpts[name] = {};
        }

        format.editorSettings.grapesSettings.pluginsOpts[name].element = element;
      });

      const grapesSettings = Object.assign({
        // Indicate where to init the editor. You can also pass an HTMLElement
        container: $('.gjs').get(0),
        // Get the content for the canvas directly from the element
        // As an alternative we could use: `components: '<h1>Hello World
        // Component!</h1>'`,
        fromElement: true,
        // Size of the editor
        // height: '100vh',
        // width: '100%',
        // Avoid any default panel
        // panels: { defaults: [] },
      }, format.editorSettings.grapesSettings);

      Drupal.grapesjs = grapesjs.init(grapesSettings);

      Drupal.grapesjs.on('load', function (editor) {
        /* Disable Drupal form submit */
        $('#gjs-clm-new').on('keydown', function (e) {
          if(e.keyCode === 13) {
            e.preventDefault();
          }
        })
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
