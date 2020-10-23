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
      const componentAttrs = component.getAttributes();
      const asset = assetManager.get(componentAttrs.src);
      const assetData = asset && asset.attributes && asset.attributes.data;

      if (assetData) {
        const attrs = Object.keys(assetData).reduce((accumulator, key) => {
          if (!componentAttrs[`data-${key}`] || componentAttrs[`data-${key}`] !== assetData[key]) {
            accumulator[`data-${key}`] = assetData[key];
          }

          return accumulator;
        }, {});

        attrs.length > 0 && component.addAttributes(attrs);
      }
    }
  });
};
