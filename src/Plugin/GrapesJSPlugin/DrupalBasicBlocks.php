<?php

namespace Drupal\grapesjs_editor\Plugin\GrapesJSPlugin;

use Drupal\Core\Form\FormStateInterface;
use Drupal\editor\Entity\Editor;
use Drupal\grapesjs_editor\GrapesJSPluginBase;
use Drupal\grapesjs_editor\GrapesJSPluginConfigurableInterface;

/**
 * Defines the "drupal_basic_blocks" plugin.
 *
 * @GrapesJSPlugin(
 *   id = "drupal_basic_blocks",
 *   label = @Translation("Basic Blocks"),
 *   module = "grapesjs_editor"
 * )
 */
class DrupalBasicBlocks extends GrapesJSPluginBase implements GrapesJSPluginConfigurableInterface {

  /**
   * The available blocks.
   *
   * @var array
   */
  protected $blocks;

  /**
   * {@inheritDoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->blocks = [
      'heading' => $this->t('Heading'),
      'paragraph' => $this->t('Paragraph'),
      'link' => $this->t('Link'),
      'image' => $this->t('Image'),
      'video' => $this->t('Video'),
      'list' => $this->t('List'),
      'map' => $this->t('Map'),
      'section' => $this->t('Section'),
    ];
  }

  /**
   * {@inheritDoc}
   */
  public function getLibraries(Editor $editor) {
    return [
      'grapesjs_editor/drupal-basic-blocks',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(Editor $editor) {
    $settings = $editor->getSettings();
    $blocks = $settings['plugins']['drupal_basic_blocks'] ?? [];
    $blocks = array_filter($blocks, function ($enable) {
      return $enable;
    });

    return [
      'grapesSettings' => [
        'plugins' => [
          'drupal-basic-blocks',
        ],
        'pluginsOpts' => [
          'drupal-basic-blocks' => [
            'blocks' => array_keys($blocks),
          ],
        ],
      ],
    ];
  }

  /**
   * {@inheritDoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state, Editor $editor) {
    $settings = $editor->getSettings();
    $config = $settings['plugins']['drupal_basic_blocks'] ?? [];

    foreach ($this->blocks as $key => $block) {
      $form['allowed_blocks'][$key] = [
        '#title' => $block,
        '#type' => 'checkbox',
        '#default_value' => !empty($config[$key]),
      ];
    }

    $form['allowed_blocks']['#element_validate'][] = [
      $this,
      'validateAllowedBlocksSettings',
    ];

    return $form;
  }

  /**
   * Validation handler for the "allowed_blocks" element in settingsForm().
   *
   * @param array $element
   *   The render element.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  public function validateAllowedBlocksSettings(array $element, FormStateInterface $form_state) {
    $settings = $form_state->getValue([
      'editor',
      'settings',
      'plugins',
      'drupal_basic_blocks',
      'allowed_blocks',
    ]);
    $form_state->unsetValue([
      'editor',
      'settings',
      'plugins',
      'drupal_basic_blocks',
    ]);
    $form_state->setValue([
      'editor',
      'settings',
      'plugins',
      'drupal_basic_blocks',
    ], $settings);
  }

}
