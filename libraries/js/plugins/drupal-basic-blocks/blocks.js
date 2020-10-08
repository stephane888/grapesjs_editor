/**
 * @file
 * Contains blocks.js
 */
export default (editor, opts = {}) => {
  const blockManager = editor.BlockManager;

  /* Block : Heading */
  if (opts.blocks.indexOf('heading') >= 0) {
    blockManager.add('heading', {
      label: opts.headingLabel,
      category: opts.basicCategory,
      attributes: {class: 'fa fa-header'},
      content: {
        type: 'heading',
        content: opts.headingDefaultContent,
      }
    });
  }

  /* Block : Paragraph */
  if (opts.blocks.indexOf('paragraph') >= 0) {
    blockManager.add('paragraph', {
      label: opts.paragraphLabel,
      category: opts.basicCategory,
      attributes: {class: 'fa fa-paragraph'},
      content: {
        type: 'paragraph',
        content: opts.paragraphDefaultContent,
      }
    });
  }

  /* Block : Image */
  if (opts.blocks.indexOf('image') >= 0) {
    blockManager.add('image', {
      label: opts.imageLabel,
      category: opts.basicCategory,
      attributes: {class: 'fa fa-picture-o'},
      select: true,
      activate: true,
      content: {
        type: 'image',
      }
    });
  }

  /* Block : List */
  if (opts.blocks.indexOf('list') >= 0) {
    blockManager.add('list', {
      label: opts.listLabel,
      category: opts.basicCategory,
      attributes: {class: 'fa fa-list'},
      content: {
        type: 'list',
        components: opts.listDefaultComponents,
      }
    });
  }

  /* Block : Section */
  if (opts.blocks.indexOf('section') >= 0) {
    blockManager.add('section', {
      label: opts.sectionLabel,
      category: opts.layoutCategory,
      attributes: {class: 'fa fa-object-group'},
      content: {
        type: 'section',
      }
    });
  }
};
