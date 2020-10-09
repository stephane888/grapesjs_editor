/**
 * @file
 * Contains components.js
 */
import tagNameTrait from '../../traits/tag-name-trait';

export default (editor, opts = {}) => {
  const domComponents = editor.DomComponents;

  /* Component type : Comment */
  domComponents.addType('comment', {
    model: {
      toHTML: function () {
        return '';
      },
    },
  });

  /* Component type : Heading */
  domComponents.addType('heading', {
    extend: 'text',
    isComponent: (el) => {
      return el && el.tagName && ['H1', 'H2', 'H3', 'H4', 'H5', 'H6'].indexOf(el.tagName) >= 0;
    },
    model: {
      defaults: {
        name: opts.headingLabel,
        tagName: opts.headingDefaultTagName,
        traits: ['id', tagNameTrait(opts.headingLabelTagNameTrait, opts.headingOptionsTagNameTrait)],
      },
    },
  });

  /* Component type : Paragraph */
  domComponents.addType('paragraph', {
    extend: 'text',
    isComponent: (el) => {
      return el && el.tagName === 'P';
    },
    model: {
      defaults: {
        name: opts.paragraphLabel,
        tagName: 'p',
        traits: ['id'],
      },
    },
  });

  /* Component type : List */
  domComponents.addType('list', {
    isComponent: (el) => {
      const tagNames = opts.listOptionsTagNameTrait.map(tagName => tagName.id.toUpperCase());
      return el && el.tagName && tagNames.indexOf(el.tagName) >= 0;
    },
    model: {
      defaults: {
        name: opts.listLabel,
        tagName: opts.listDefaultTagName,
        droppable: 'li',
        traits: ['id', tagNameTrait(opts.listLabelTagNameTrait, opts.listOptionsTagNameTrait)],
      },
    },
  });

  /* Component type : List item */
  domComponents.addType('list-item', {
    extend: 'text',
    isComponent: (el) => {
      return el && el.tagName && el.tagName === 'LI';
    },
    model: {
      defaults: {
        name: opts.listItemLabel,
        tagName: 'li',
        draggable: 'ul, ol',
        traits: ['id'],
      },
    },
  });

  /* Component type : Section */
  domComponents.addType('section', {
    isComponent: (el) => {
      const tagNames = opts.sectionOptionsTagNameTrait.map(tagName => tagName.id.toUpperCase());
      return el && el.tagName && tagNames.indexOf(el.tagName) >= 0;
    },
    model: {
      defaults: {
        name: opts.sectionLabel,
        tagName: opts.sectionDefaultTagName,
        traits: ['id', tagNameTrait(opts.sectionLabelTagNameTrait, opts.sectionOptionsTagNameTrait)],
      },
    },
  });
};
