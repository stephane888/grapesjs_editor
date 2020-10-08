/**
 * @file
 * Contains blocks.js
 */
export default (editor, opts = {}) => {
  const blockManager = editor.BlockManager;

  /* Blocks : Drupal Block */
  opts.blocks.forEach((block) => {
    const {plugin_id, label} = block;
    const blockId = `drupal-block-${plugin_id}`;

    blockManager.add(blockId, {
      label: label,
      category: opts.category,
      attributes: {class: 'fa fa-drupal'},
      content: {
        type: 'drupal-block',
        attributes: {
          'block-plugin-id': plugin_id,
        }
      }
    });
  });
};
