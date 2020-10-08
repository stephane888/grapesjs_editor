/**
 * @file
 * Contains drupal-fields.js
 */
import loadComponents from './components';
import loadBlocks from './blocks';

export default (editor, opts = {}) => {
  const config = {
    category: {
      id: 'drupal-fields',
      label: Drupal.t('Drupal Fields'),
      open: false,
      order: 10,
    },
    componentLabel: Drupal.t('Drupal Field'),
    ...opts,
  };

  loadComponents(editor, config);
  loadBlocks(editor, config);
};
