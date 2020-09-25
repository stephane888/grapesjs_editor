<?php

namespace Drupal\grapesjs_editor\Services;

use Drupal\Core\Image\ImageFactory;
use Drupal\file\Entity\File;
use Drupal\file\FileInterface;

/**
 * Defines a class to manage asset object.
 */
class AssetManager {

  /**
   * The image factory service.
   *
   * @var \Drupal\Core\Image\ImageFactory
   */
  protected $imageFactory;

  /**
   * AssetManager constructor.
   *
   * @param \Drupal\Core\Image\ImageFactory $image_factory
   *   The image factory service.
   */
  public function __construct(ImageFactory $image_factory) {
    $this->imageFactory = $image_factory;
  }

  /**
   * Build asset array with file parameter.
   *
   * @param \Drupal\file\FileInterface $file
   *   The file to transform.
   *
   * @return array
   *   The asset array with file data.
   */
  public function buildAsset(FileInterface $file) {
    $asset = [
      'type' => 'image',
      'src' => $file->createFileUrl(),
      'data' => [
        'entity-uuid' => $file->uuid(),
        'entity-type' => 'file',
      ],
    ];

    $image = $this->imageFactory->get($file->getFileUri());
    if ($image->isValid()) {
      $asset['width'] = $image->getWidth();
      $asset['height'] = $image->getHeight();
    }

    return $asset;
  }

  /**
   * Returns assets array.
   *
   * @return array
   *   The assets array.
   */
  public function getAssets() {
    /* @var FileInterface[] $files */
    $files = File::loadMultiple();

    return array_reduce($files, function ($carry, FileInterface $file) {
      $carry[] = $this->buildAsset($file);
      return $carry;
    }, []);
  }

}
