/**
 * @file
 * Contains drupal-basic-blocks.js
 */
(function ($, Drupal, grapesjs) {
  grapesjs.plugins.add('drupal-basic-blocks', function (editor, options) {
    const blockManager = editor.BlockManager;
    const domComponents = editor.DomComponents;
    const category = {
      id: 'basic',
      label: Drupal.t('Basic'),
    };

    /* Component type : Heading */
    domComponents.addType('heading', {
      extend: 'text',
      isComponent: function (el) {
        return el && el.tagName && ['H1', 'H2', 'H3', 'H4', 'H5', 'H6'].indexOf(el.tagName) >= 0;
      },
      model: {
        defaults: {
          name: Drupal.t('Heading'),
          tagName: 'h2',
          traits: ['id', 'title', {
            label: Drupal.t('Heading level'),
            name: 'tagName',
            changeProp: 1,
            type: 'select',
            options: [
              { id: 'h1', name: Drupal.t('Heading 1') },
              { id: 'h2', name: Drupal.t('Heading 2') },
              { id: 'h3', name: Drupal.t('Heading 3') },
              { id: 'h4', name: Drupal.t('Heading 4') },
              { id: 'h5', name: Drupal.t('Heading 5') },
              { id: 'h6', name: Drupal.t('Heading 6') },
            ]
          }],
        },
      },
    });

    /* Component type : List */
    domComponents.addType('list', {
      isComponent: function (el) {
        return el && el.tagName && ['UL', 'OL'].indexOf(el.tagName) >= 0;
      },
      model: {
        defaults: {
          name: Drupal.t('List'),
          tagName: 'ul',
          droppable: 'li',
          traits: ['id', 'title', {
            label: Drupal.t('Type'),
            name: 'tagName',
            changeProp: 1,
            type: 'select',
            options: [
              { id: 'ul', name: Drupal.t('Unordered') },
              { id: 'ol', name: Drupal.t('Ordered') },
            ]
          }],
        },
      },
    });

    /* Component type : List item */
    domComponents.addType('list-item', {
      extend: 'text',
      isComponent: function (el) {
        return el && el.tagName && el.tagName === 'LI';
      },
      model: {
        defaults: {
          name: Drupal.t('List item'),
          tagName: 'li',
          draggable: 'ul, ol',
        },
      },
    });

    /* Block : Heading */
    blockManager.add('heading', {
      label: Drupal.t('Heading'),
      category: category,
      attributes: { class: 'fa fa-header' },
      content: {
        type: 'heading',
        content: Drupal.t('Insert your text here'),
      }
    });

    /* Block : Paragraph */
    blockManager.add('text', {
      label: Drupal.t('Text'),
      category: category,
      attributes: { class: 'fa fa-paragraph' },
      content: {
        type: 'text',
        tagName: 'p',
        content: Drupal.t('Insert your text here'),
      }
    });

    /* Block : Image */
    blockManager.add('image', {
      label: Drupal.t('Image'),
      category: category,
      attributes: { class: 'fa fa-picture-o' },
      select: true,
      activate: true,
      content: {
        type: 'image',
      }
    });

    /* Block : List */
    blockManager.add('list', {
      label: Drupal.t('List'),
      category: category,
      attributes: { class: 'fa fa-list' },
      content: {
        type: 'list',
        components: [
          { type: 'list-item', content: 'Option 1' },
          { type: 'list-item', content: 'Option 2' },
          { type: 'list-item', content: 'Option 3' },
        ],
      }
    });
  });

})(jQuery, Drupal, grapesjs);
