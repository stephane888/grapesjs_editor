/**
 * @file
 * Contains drupal-reusable-blocks.js
 */
(function ($, Drupal, grapesjs) {
  grapesjs.plugins.add('drupal-reusable-blocks', function (editor, options) {
    const blockManager = editor.BlockManager;
    const domComponents = editor.DomComponents;
    const commands = editor.Commands;
    const commandId = 'open-reusable-block-modal';
    const category = {
      id: 'reusable',
      label: Drupal.t('Drupal Reusable Blocks'),
      order: 40,
      open: false,
    };
    const pfx = editor.getConfig().stylePrefix;
    const btnEdit = document.createElement('button');
    btnEdit.type = 'button';
    btnEdit.innerHTML = Drupal.t('Save');
    btnEdit.className = pfx + 'btn-prim ' + pfx + 'btn-import';
    btnEdit.onclick = function (e) {
      const $parent = $(e.target).parent('.create-block-form');
      const $statusMessage = $('.status-message', $parent);
      const selected = editor.getSelected();
      const css = editor.CodeManager.getCode(selected, 'css', {cssc: editor.CssComposer});
      const style = css && `<style>${css}</style>`;
      const blockTitle = $('[name="block-title"]', $parent).val();
      const blockBody = style + selected.toHTML();

      $statusMessage.empty();
      $.post(options.block_create_route, {title: blockTitle, body: blockBody}).then(function (response) {
        const blockId = `drupal-reusable-block-${response.id}`;
        blockManager.add(blockId, {
          label: response.label,
          category: category,
          attributes: {class: 'fa fa-drupal'},
          content: {
            type: 'drupal-reusable-block',
            attributes: {
              'block-plugin-id': response.id,
            }
          }
        });

        editor.Modal.close();
      })
        .catch(function (response) {
          $statusMessage.append(
            $('<div/>', {html: response.responseJSON, class: pfx + 'alert-error'})
          );
        });
    };
    const $form = $('<div/>', {class: 'create-block-form'}).append(
      $('<div/>', {class: 'status-message'}),
      $('<div/>', {class: pfx + 'form-item form-item'}).append(
        $('<label/>', {
          class: pfx + 'form-required form-required',
          for: 'block-title',
          text: Drupal.t('Reusable block name')
        }),
        $('<input />', {
          class: pfx + 'form-text form-text required',
          id: 'block-title',
          name: 'block-title',
          type: 'text',
          required: 'required'
        })
      ),
      btnEdit
    )

    /* Commands : add reusable command */
    commands.add(commandId, {
      run(editor) {
        editor.Modal.open({
          title: Drupal.t('Reusable block'),
          content: $form,
        }).onceClose(() => this.stopCommand());
      },
      stop(editor) {
        editor.Modal.close();
      },
    });

    /* Component type : Drupal Reusable Block */
    domComponents.addType('drupal-reusable-block', {
      isComponent: function (el) {
        return false;
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
              component.model.empty().replaceWith(response);
            }).catch(function (response) {
              component.model.empty().components().add({
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
    domComponents.getTypes().forEach(function (domComponent) {
      domComponents.addType(domComponent.id, {
        extendFn: ['initToolbar'],
        model: {
          initToolbar: function () {
            const tb = this.get('toolbar');
            const tbExists = tb.some(item => item.command === commandId);

            if (!tbExists) {
              tb.unshift({
                command: commandId,
                label: '<i class="fa fa-recycle"/>',
              });
              this.set('toolbar', tb);
            }
          }
        },
      });
    });

    /* Blocks : Drupal Reusable Block */
    options.blocks.forEach(function (block) {
      const blockId = `drupal-reusable-block-${block.plugin_id}`;

      blockManager.add(blockId, {
        label: block.label,
        category: category,
        attributes: {class: 'fa fa-drupal'},
        content: {
          type: 'drupal-reusable-block',
          attributes: {
            'block-plugin-id': block.plugin_id,
          }
        }
      });
    });
  });

})(jQuery, Drupal, grapesjs);
