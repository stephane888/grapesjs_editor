/**
 * @file
 * Contains commands.js
 */
import $ from 'jquery';

export default (editor, opts = {}) => {
  const blockManager = editor.BlockManager;
  const commands = editor.Commands;
  const modal = editor.Modal;

  const pfx = editor.getConfig().stylePrefix;
  const btnEdit = document.createElement('button');
  btnEdit.type = 'button';
  btnEdit.innerHTML = opts.modalSaveButtonLabel;
  btnEdit.className = pfx + 'btn-prim ' + pfx + 'btn-import';
  btnEdit.onclick = (e) => {
    const $parent = $(e.target).parent('.create-block-form');
    const $statusMessage = $('.status-message', $parent);
    const selected = editor.getSelected();
    const css = editor.CodeManager.getCode(selected, 'css', {cssc: editor.CssComposer});
    const style = css && `<style>${css}</style>`;
    const blockTitle = $('[name="block-title"]', $parent).val();
    const blockBody = style + selected.toHTML();

    $statusMessage.empty();
    $.post(opts.block_create_route, {
      title: blockTitle,
      body: blockBody
    }).then((response) => {
      const blockId = `drupal-reusable-block-${response.id}`;
      blockManager.add(blockId, {
        label: response.label,
        category: opts.category,
        attributes: {class: 'fa fa-drupal'},
        content: {
          type: 'drupal-reusable-block',
          attributes: {
            'block-plugin-id': response.id,
          }
        }
      });

      modal.close();
    })
      .catch((response) => {
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
        text: opts.modalNameInputLabel,
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
  commands.add(opts.commandId, {
    run() {
      modal.open({
        title: opts.modalTitle,
        content: $form,
      }).onceClose(() => this.stopCommand());
    },
    stop() {
      modal.close();
    },
  });
};
