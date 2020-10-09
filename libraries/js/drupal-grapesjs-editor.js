(function ($, Drupal, grapesjs) {
  Drupal.editors.grapesjs_editor = {
    editors: {},
    getFieldName(element) {
      return $(element).attr('name').split('[')[0];
    },
    attach(element, format) {
      /* Rebuild body field */
      const fieldName = this.getFieldName(element);
      const gjsContainer = $('<div/>', {
        id: `gjs-container-${fieldName}`,
        class: 'gjs',
        'data-field-name': $(element).attr('name')
      });
      $(element).parent().prepend(gjsContainer);
      $(element).hide();

      const grapesSettings = {
        // Indicate where to init the editor. You can also pass an HTMLElement
        container: gjsContainer.get(0),
        components: $(element).val(),
        plugins: [],
        ...format.editorSettings.grapesSettings
      };

      /* Add body field element to plugin options */
      grapesSettings.plugins.forEach((name, plugin) => {
        if (typeof grapesSettings.pluginsOpts[name] === 'undefined') {
          grapesSettings.pluginsOpts[name] = {};
        }

        grapesSettings.pluginsOpts[name].element = element;
      });

      this.editors[fieldName] = grapesjs.init(grapesSettings);

      /* Load current locale */
      const locale = format.editorSettings.currentLanguage;
      if (locale !== 'en') {
        import(/* webpackMode: "eager" */ `grapesjs/src/i18n/locale/${locale}`).then((module) => {
          const messages = {[locale]: module.default};
          this.editors[fieldName].I18n.setLocale(locale);
          this.editors[fieldName].I18n.addMessages(messages);
        }).catch((err) => {
          console.error(`Locale "${locale}" not found.`);
        });
      }

      this.editors[fieldName].on('load', () => {
        /* Disable Drupal form submit */
        $('input', gjsContainer).on('keydown', function (e) {
          if (e.keyCode === 13) {
            e.preventDefault();
          }
        });
      });
    },

    detach(element) {
      const fieldName = this.getFieldName(element);
      const gjsContainer = $(`#gjs-container-${fieldName}`);

      $(element).show();
      this.editors[fieldName].destroy();
      gjsContainer.remove();
    },

    onChange() {
    }
  };
})(jQuery, Drupal, grapesjs);
