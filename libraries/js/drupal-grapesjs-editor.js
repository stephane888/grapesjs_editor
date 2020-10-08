(function ($, Drupal, grapesjs) {
  Drupal.grapesjs = null;

  Drupal.editors.grapesjs_editor = {
    gjsContainer: $('<div/>', {class: 'gjs'}),
    attach(element, format) {
      /* Rebuild body field */
      $(element).parent().prepend(this.gjsContainer);
      $(element).hide();

      /* Add body field element to plugin options */
      format.editorSettings.grapesSettings.plugins.forEach((name, plugin) => {
        if (typeof format.editorSettings.grapesSettings.pluginsOpts[name] === 'undefined') {
          format.editorSettings.grapesSettings.pluginsOpts[name] = {};
        }

        format.editorSettings.grapesSettings.pluginsOpts[name].element = element;
      });

      const grapesSettings = {
        // Indicate where to init the editor. You can also pass an HTMLElement
        container: this.gjsContainer.get(0),
        ...format.editorSettings.grapesSettings
      };

      Drupal.grapesjs = grapesjs.init(grapesSettings);

      /* Load current locale */
      const locale = format.editorSettings.currentLanguage;
      import(/* webpackMode: "eager" */ `grapesjs/src/i18n/locale/${locale}`).then(function (module) {
        const messages = {[locale]: module.default};
        Drupal.grapesjs.I18n.setLocale(locale);
        Drupal.grapesjs.I18n.addMessages(messages);
      }).catch(() => {
        console.error(`Locale "${locale}" not found.`);
      });

      Drupal.grapesjs.on('load', () => {
        /* Disable Drupal form submit */
        $('input', this.gjsContainer).on('keydown', function (e) {
          if (e.keyCode === 13) {
            e.preventDefault();
          }
        });
      });
    },

    detach(element) {
      $(element).show();
      Drupal.grapesjs.destroy();
      this.gjsContainer.removeAttr('style');
    },

    onChange() {
    }
  };
})(jQuery, Drupal, grapesjs);
