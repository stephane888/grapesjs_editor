/**
 * @file
 * Contains drupal-blocks.js
 */
import loadComponents from './components';
import loadBlocks from './blocks';

export default (editor, opts = {}) => {
  const config = {
    category: {
      id: 'drupal-blocks',
      label: Drupal.t('Drupal Blocks'),
      open: false,
      order: 20,
    },
    componentLabel: Drupal.t('Drupal Block'),
    ...opts,
  };

  loadComponents(editor, config);
  loadBlocks(editor, config);
};
