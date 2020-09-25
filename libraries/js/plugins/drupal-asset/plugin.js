/**
 * @file
 * Contains drupal-asset.js
 */
(function ($, Drupal, grapesjs) {
  grapesjs.plugins.add('drupal-asset', function (editor, options) {
    const assetManager = editor.AssetManager;

    editor.on('asset:upload', function (response) {
      // @TODO : show status message
      console.log(response);
    });

    /* Add data attributes to track Drupal files */
    editor.on('component:update:src', function (component, editor) {
      if (component.is('image')) {
        const asset = assetManager.get(component.attributes.src);

        if (!component.attributes['data-entity-uuid'] && asset && asset.attributes && asset.attributes.data) {
          const attrs = Object.keys(asset.attributes.data).reduce(function (accumulator, key) {
            accumulator[`data-${key}`] = asset.attributes.data[key];
            return accumulator;
          }, {});

          component.addAttributes(attrs);
        }
      }
    });
  });

})(jQuery, Drupal, grapesjs);
