/**
 * @file
 * Contains blocks.js
 */
export default (editor, opts = {}) => {
  const blockManager = editor.BlockManager;

  /* Blocks : Drupal Field */
  opts.fields.forEach((field) => {
    const fieldId = `drupal-field-${field.name}`;

    blockManager.add(fieldId, {
      label: field.label,
      category: opts.category,
      attributes: {class: 'fa fa-drupal'},
      content: {
        type: 'drupal-field',
        attributes: {
          'field-name': field.name,
        }
      }
    });
  });
};
