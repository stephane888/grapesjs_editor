/**
 * @file
 * Contains drupal-asset.js
 */
export default (editor, opts = {}) => {
  const assetManager = editor.AssetManager;

  editor.on('asset:upload', (response) => {
    // @TODO : show status message
    console.log(response);
  });

  /* Add data attributes to track Drupal files */
  editor.on('component:update:src', (component) => {
    if (component.is('image')) {
      const asset = assetManager.get(component.attributes.src);

      if (!component.attributes['data-entity-uuid'] && asset && asset.attributes && asset.attributes.data) {
        const attrs = Object.keys(asset.attributes.data).reduce((accumulator, key) => {
          accumulator[`data-${key}`] = asset.attributes.data[key];
          return accumulator;
        }, {});

        component.addAttributes(attrs);
      }
    }
  });
};
