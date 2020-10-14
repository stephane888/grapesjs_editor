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

  /* Block : Link */
  if (opts.blocks.indexOf('link') >= 0) {
    blockManager.add('link', {
      label: opts.linkLabel,
      category: opts.basicCategory,
      attributes: {class: 'fa fa-link'},
      content: {
        type: 'link',
        content: opts.linkDefaultContent,
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

  /* Block : Video */
  if (opts.blocks.indexOf('video') >= 0) {
    blockManager.add('video', {
      label: opts.videoLabel,
      category: opts.basicCategory,
      attributes: { class: 'fa fa-youtube-play' },
      content: {
        type: 'video',
        style: {
          height: '350px',
          width: '615px'
        }
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

  /* Block : Map */
  if (opts.blocks.indexOf('map') >= 0) {
    blockManager.add('map', {
      label: opts.mapLabel,
      category: opts.basicCategory,
      attributes: {class: 'fa fa-map-o'},
      content: {
        type: 'map',
        style: { height: '350px' }
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
