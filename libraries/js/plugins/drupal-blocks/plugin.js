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
            $.get(options.block_route, component.model.get('attributes')).then(function (response) {
              component.model.components().add(response);
              disableChildComponents(component.model.components());
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
