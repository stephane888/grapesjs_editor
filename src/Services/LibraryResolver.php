<?php

namespace Drupal\grapesjs_editor\Services;

use Drupal\Core\Asset\AssetResolverInterface;
use Drupal\Core\Asset\AttachedAssets;
use Drupal\Core\Extension\ThemeHandlerInterface;
use Drupal\Core\Theme\ThemeInitializationInterface;
use Drupal\Core\Url;

/**
 * Defines a class to retrieve default theme libraries.
 */
class LibraryResolver {

  /**
   * The theme handler service.
   *
   * @var \Drupal\Core\Extension\ThemeHandlerInterface
   */
  protected $themeHandler;

  /**
   * The theme initialization service.
   *
   * @var \Drupal\Core\Theme\ThemeInitializationInterface
   */
  protected $themeInitialization;

  /**
   * The asset resolver service.
   *
   * @var \Drupal\Core\Asset\AssetResolverInterface
   */
  protected $assetResolver;

  /**
   * LibraryResolver constructor.
   *
   * @param \Drupal\Core\Extension\ThemeHandlerInterface $theme_handler
   *   The theme handler service.
   * @param \Drupal\Core\Theme\ThemeInitializationInterface $theme_initialization
   *   The theme initialization service.
   * @param \Drupal\Core\Asset\AssetResolverInterface $asset_resolver
   *   The asset resolver service.
   */
  public function __construct(ThemeHandlerInterface $theme_handler, ThemeInitializationInterface $theme_initialization, AssetResolverInterface $asset_resolver) {
    $this->themeHandler = $theme_handler;
    $this->themeInitialization = $theme_initialization;
    $this->assetResolver = $asset_resolver;
  }

  /**
   * Returns css file array.
   *
   * @return array
   *   The css file array.
   */
  public function getStyles() {
    /* @var \Drupal\Core\Theme\ActiveTheme $theme */
    $default_theme = $this->themeHandler->getDefault();
    $theme = $this->themeInitialization->initTheme($default_theme);
    $assets = AttachedAssets::createFromRenderArray([
      '#attached' => [
        'library' => $theme->getLibraries(),
      ],
    ]);

    $css = [];
    $front = Url::fromRoute('<front>', [], ['absolute' => TRUE])->toString();
    foreach ($this->assetResolver->getCssAssets($assets, FALSE) as $uri => $css_asset) {
      if ($css_asset['media'] === 'all') {
        $css[] = $front . $uri;
      }
    }

    return $css;
  }

}
