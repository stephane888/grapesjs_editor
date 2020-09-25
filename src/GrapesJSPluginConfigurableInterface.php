<?php

namespace Drupal\grapesjs_editor;

use Drupal\Core\Form\FormStateInterface;
use Drupal\editor\Entity\Editor;

/**
 * Defines an interface for configurable plugin.
 */
interface GrapesJSPluginConfigurableInterface extends GrapesJSPluginInterface {

  /**
   * Returns a settings form to configure this GrapesJS plugin.
   *
   * If the plugin's behavior depends on extensive options and/or external data,
   * then the implementing module can choose to provide a separate, global
   * configuration page rather than per-text-editor settings. In that case, this
   * form should provide a link to the separate settings page.
   *
   * @param array $form
   *   An empty form array to be populated with a configuration form, if any.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The state of the entire filter administration form.
   * @param \Drupal\editor\Entity\Editor $editor
   *   A configured text editor object.
   *
   * @return array
   *   A render array for the settings form.
   */
  public function settingsForm(array $form, FormStateInterface $form_state, Editor $editor);

}
