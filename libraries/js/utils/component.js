/**
 * @file
 * Contains component.js
 */
export const addLoadingBlock = (component) => {
  component.model.empty().components().add({
    tagName: `div`,
    attributes: {
      class: 'gjs-drupal-block',
    },
    content: '<div class="lds-dual-ring"></div> ' + Drupal.t('Loading...'),
  });
};

export const setComponentName = (editor, component) => {
  const blockManager = editor.BlockManager;
  const attrs = component.model.getAttributes();
  if (typeof attrs['block-plugin-id'] !== 'undefined') {
    const blockId = `drupal-block-${attrs['block-plugin-id']}`;
    const block = blockManager.get(blockId);

    if (block) {
      component.model.set('name', block.get('label'));
    }
  }
};

export const disableChildComponents = (components) => {
  components.forEach((c) => {
    c.set({
      badgable: false,
      copyable: false,
      draggable: false,
      highlightable: false,
      hoverable: false,
      selectable: false,
      removable: false,
    });

    if (c.components().length > 0) {
      disableChildComponents(c.components());
    }
  });
};

export const renderComponentContent = (editor, component, content) => {
  component.model.empty().components().add(content);
  setComponentName(editor, component);
  disableChildComponents(component.model.components());
};
