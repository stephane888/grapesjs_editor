/**
 * @file
 * Contains tag-name-trait.js
 */
export default (label = Drupal.t('Tag'), options = []) => {
  return {
    label,
    name: 'tagName',
    changeProp: 1,
    type: 'select',
    options,
  };
};
