/**
 * @file
 * Contains drupal-reusable-blocks.js
 */
import loadCommands from './commands';
import loadComponents from './components';
import loadBlocks from './blocks';

export default (editor, opts = {}) => {
  const config = {
    category: {
      id: 'drupal-reusable-blocks',
      label: Drupal.t('Drupal Reusable Blocks'),
      open: false,
      order: 40,
    },
    commandId: 'tb-reusable-block-modal',
    modalTitle: Drupal.t('Reusable block'),
    modalNameInputLabel: Drupal.t('Reusable block name'),
    modalSaveButtonLabel: Drupal.t('Save'),
    ...opts,
  };

  loadCommands(editor, config);
  loadComponents(editor, config);
  loadBlocks(editor, config);
};
