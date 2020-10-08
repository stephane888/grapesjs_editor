/**
 * @file
 * Contains components.js
 */
import $ from 'jquery';
import {
  addLoadingBlock,
  renderComponentContent
} from "GrapesJsEditor/utils/component";

export default (editor, opts = {}) => {
  const domComponents = editor.DomComponents;

  /* Component type : Drupal Reusable Block */
  domComponents.addType('drupal-reusable-block', {
    isComponent: () => {
      return false;
    },
    view: {
      init: (component) => {
        if (component.model.components().length === 0) {
          addLoadingBlock(component);

          $.get(opts.block_route, component.model.get('attributes')).then((response) => {
            component.model.empty().replaceWith(response);
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

  /* Toolbar : add reusable action */
  editor.on('component:selected', (component) => {
    const toolbar = component.get('toolbar');
    const commandExists = toolbar.some(item => item.command === opts.commandId);

    if (!commandExists) {
      toolbar.unshift({
        command: opts.commandId,
        label: '<i class="fa fa-recycle"/>',
      })
      component.set('toolbar', toolbar)
    }
  });
};
