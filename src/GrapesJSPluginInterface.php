<?php

namespace Drupal\grapesjs_editor;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\editor\Entity\Editor;

/**
 * Defines an interface for GrapesJS plugin.
 */
interface GrapesJSPluginInterface extends PluginInspectionInterface {

  /**
   * Returns a list of libraries this plugin requires.
   *
   * These libraries will be attached to the text_format element on which the
   * editor is being loaded.
   *
   * @param \Drupal\editor\Entity\Editor $editor
   *   A configured text editor object.
   *
   * @return array
   *   An array of libraries suitable for usage in a render API #attached
   *   property.
   */
  public function getLibraries(Editor $editor);

  /**
   * The editor's settings can be retrieved via $editor->getSettings().
   *
   * But be aware that it may not yet contain plugin-specific settings,
   * because the user may not yet have configured the form.
   * If there are plugin-specific settings (verify with isset()), they can be
   * found with following code.
   *
   * @code
   * $settings = $editor->getSettings();
   * $plugin_specific_settings = $settings['plugins'][$plugin_id];
   * @endcode
   *
   * @param \Drupal\editor\Entity\Editor $editor
   *   A configured text editor object.
   *
   * @return array
   *   A keyed array.
   */
  public function getConfig(Editor $editor);

}
