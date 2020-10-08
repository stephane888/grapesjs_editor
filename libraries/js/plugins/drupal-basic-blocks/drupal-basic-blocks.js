/**
 * @file
 * Contains drupal-basic-blocks.js
 */
import loadComponents from './components';
import loadBlocks from './blocks';

export default (editor, opts = {}) => {
  const config = {
    blocks: ['heading', 'paragraph', 'image', 'list', 'section'],
    basicCategory: {
      id: 'basic',
      label: Drupal.t('Basic'),
      order: 0,
    },
    layoutCategory: {
      id: 'layout',
      label: Drupal.t('Layout'),
      order: 5,
      open: false,
    },
    headingLabel: Drupal.t('Heading'),
    headingDefaultTagName: 'h2',
    headingDefaultContent: Drupal.t('Insert your text here'),
    headingLabelTagNameTrait: Drupal.t('Heading level'),
    headingOptionsTagNameTrait: [
      {id: 'h1', name: Drupal.t('Heading 1')},
      {id: 'h2', name: Drupal.t('Heading 2')},
      {id: 'h3', name: Drupal.t('Heading 3')},
      {id: 'h4', name: Drupal.t('Heading 4')},
      {id: 'h5', name: Drupal.t('Heading 5')},
      {id: 'h6', name: Drupal.t('Heading 6')},
    ],
    paragraphLabel: Drupal.t('Paragraph'),
    paragraphDefaultContent: Drupal.t('Insert your text here'),
    imageLabel: Drupal.t('Image'),
    listLabel: Drupal.t('List'),
    listDefaultTagName: 'ul',
    listDefaultComponents: [
      {type: 'list-item', content: 'Option 1'},
      {type: 'list-item', content: 'Option 2'},
      {type: 'list-item', content: 'Option 3'},
    ],
    listLabelTagNameTrait: Drupal.t('Type'),
    listOptionsTagNameTrait: [
      {id: 'ul', name: Drupal.t('Unordered')},
      {id: 'ol', name: Drupal.t('Ordered')},
    ],
    listItemLabel: Drupal.t('List item'),
    sectionLabel: Drupal.t('Section'),
    sectionDefaultTagName: 'div',
    sectionLabelTagNameTrait: Drupal.t('Tag'),
    sectionOptionsTagNameTrait: [
      {id: 'div', name: 'Div'},
      {id: 'section', name: 'Section'},
      {id: 'header', name: 'Header'},
      {id: 'nav', name: 'Nav'},
      {id: 'main', name: 'Main'},
      {id: 'aside', name: 'Aside'},
      {id: 'footer', name: 'Footer'},
      {id: 'article', name: 'Article'},
      {id: 'address', name: 'Address'},
      {id: 'figure', name: 'Figure'},
    ],
    ...opts,
  };

  /* Create new text component on Enter key press */
  editor.on('load', () => {
    editor.Canvas.getBody().addEventListener('keydown', e => {
      const component = editor.getSelected();
      if (component.get('type') === 'paragraph' && e.keyCode === 13 && !e.shiftKey) {
        e.preventDefault();

        // Clone the current text with style
        const newComponent = component.clone();
        component.parent().append(newComponent);
        // Empty the content
        newComponent.empty();
        // Select and focus new component
        editor.select(newComponent);
        newComponent.trigger('focus');
      }
    });
  });

  loadComponents(editor, config);
  loadBlocks(editor, config);
};
