/**
 * @file
 * Contains drupal-blocks.js
 */
(function ($, Drupal, grapesjs) {
  grapesjs.plugins.add('drupal-blocks', function (editor, options) {
    const blockManager = editor.BlockManager;
    const domComponents = editor.DomComponents;
    const category = {
      id: 'drupal-blocks',
      label: Drupal.t('Drupal Blocks'),
      open: false,
    };
    const setComponentName = function (component) {
      const attrs = component.model.getAttributes();
      if (typeof attrs['block-plugin-id'] !== 'undefined') {
        const blockId = `drupal-block-${attrs['block-plugin-id']}`;
        const block = blockManager.get(blockId);

        if (block) {
          component.model.set('name', block.get('label'));
        }
      }
    };
    const disableChildComponents = function (components) {
      components.forEach(function (c) {
        c.set({
          badgable: false,
          copyable: false,
          draggable: false,
          highlightable: false,
          hoverable: false,
          selectable: false,
          removable: false,
        });

        if (c.components().length > 0) {
          disableChildComponents(c.components());
        }
      });
    };
    const renderComponentContent = function (component, content) {
      component.model.empty().components().add(content);
      setComponentName(component);
      disableChildComponents(component.model.components());
    };

    /* Component type : Drupal Block */
    domComponents.addType('drupal-block', {
      isComponent: function (el) {
        return el && el.tagName && el.tagName === 'DRUPAL-BLOCK';
      },
      model: {
        defaults: {
          name: Drupal.t('Drupal Block'),
          tagName: `drupal-block`,
          editable: false,
          droppable: false,
          stylable: false,
          propagate: ['editable', 'droppable', 'stylable'],
          traits: [],
        },
        toHTML: function () {
          const model = this;
          const attrs = [];
          const attributes = this.getAttrToHTML();
          const tag = model.get('tagName');
          const isString = function (obj) {
            return toString.call(obj) === '[object String]';
          };
          const isBoolean = function (obj) {
            return obj === true || obj === false || toString.call(obj) === '[object Boolean]';
          };
          const isUndefined = function (obj) {
            return obj === void 0;
          };

          for (let attr in attributes) {
            const val = attributes[attr];
            const value = isString(val) ? val.replace(/"/g, '&quot;') : val;

            if (!isUndefined(value)) {
              if (isBoolean(value)) {
                value && attrs.push(attr);
              }
              else {
                attrs.push(`${attr}="${value}"`);
              }
            }
          }
          const attrString = attrs.length > 0 ? ` ${attrs.join(' ')}` : '';

          return `<${tag}${attrString}></${tag}>`;
        },
      },
      view: {
        init: function (component) {
          if (component.model.components().length === 0) {
            component.model.empty().components().add({
              tagName: `div`,
              attributes: {
                class: 'gjs-drupal-block',
              },
              content: '<div class="lds-dual-ring"></div> ' + Drupal.t('Loading...'),
            });
            $.get(options.block_route, component.model.get('attributes')).then(function (response) {
              renderComponentContent(component, response);
            }).catch(function (response) {
              renderComponentContent(component, {
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

    /* Blocks : Drupal Block */
    options.blocks.forEach(function (block) {
      const blockId = `drupal-block-${block.plugin_id}`;

      blockManager.add(blockId, {
        label: block.label,
        category: category,
        attributes: { class: 'fa fa-drupal' },
        content: {
          type: 'drupal-block',
          attributes: {
            'block-plugin-id': block.plugin_id,
          }
        }
      });
    });
  });

})(jQuery, Drupal, grapesjs);
