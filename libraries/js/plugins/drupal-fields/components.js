/**
 * @file
 * Contains components.js
 */
import $ from 'jquery';
import {addLoadingBlock, renderComponentContent} from "../../utils/component";

export default (editor, opts = {}) => {
  const domComponents = editor.DomComponents;
  const defaultType = domComponents.getType('default');

  /* Component type : Drupal Field */
  domComponents.addType('drupal-field', {
    isComponent: (el) => {
      return el && el.tagName && el.tagName === 'DRUPAL-FIELD';
    },
    model: {
      defaults: {
        name: opts.componentLabel,
        tagName: `drupal-field`,
        editable: false,
        droppable: false,
        stylable: false,
        propagate: ['editable', 'droppable', 'stylable'],
        traits: [],
      },
      toHTML: () => {
        const defaultHTML = defaultType.model.prototype.toHTML.call(this);
        const $element = $(defaultHTML);
        $element.empty();
        return $element.get(0).outerHTML;
      },
    },
    view: {
      init: (component) => {
        if (component.model.components().length === 0) {
          addLoadingBlock(component);

          $.get(opts.field_route, component.model.get('attributes')).then((response) => {
            renderComponentContent(editor, component, response);
          }).catch((response) => {
            renderComponentContent(editor, component, {
              tagName: `div`,
              attributes: {
                class: 'gjs-drupal-block gjs-block-error',
              },
              content: response.responseJSON,
            });
          });
        }
      }
    }
  });
};
