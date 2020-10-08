/**
 * @file
 * Contains blocks.js
 */
export default (editor, opts = {}) => {
  const blockManager = editor.BlockManager;

  /* Blocks : Drupal Reusable Block */
  opts.blocks.forEach((block) => {
    const {plugin_id, label} = block;
    const blockId = `drupal-reusable-block-${plugin_id}`;

    blockManager.add(blockId, {
      label: label,
      category: opts.category,
      attributes: {class: 'fa fa-drupal'},
      content: {
        type: 'drupal-reusable-block',
        attributes: {
          'block-plugin-id': plugin_id,
        }
      }
    });
  });
};
