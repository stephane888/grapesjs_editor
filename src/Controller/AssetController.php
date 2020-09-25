<?php

namespace Drupal\grapesjs_editor\Controller;

use Drupal\Component\Utility\Bytes;
use Drupal\Component\Utility\Environment;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\editor\Entity\Editor;
use Drupal\file\Entity\File;
use Drupal\grapesjs_editor\Services\AssetManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Returns responses for Asset routes.
 */
class AssetController extends ControllerBase {

  /**
   * The file system service.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

  /**
   * The asset manager service.
   *
   * @var \Drupal\grapesjs_editor\Services\AssetManager
   */
  protected $assetManager;

  /**
   * AssetController constructor.
   *
   * @param \Drupal\Core\File\FileSystemInterface $file_system
   *   The file system service.
   * @param \Drupal\grapesjs_editor\Services\AssetManager $asset_manager
   *   The asset manager service.
   */
  public function __construct(FileSystemInterface $file_system, AssetManager $asset_manager) {
    $this->fileSystem = $file_system;
    $this->assetManager = $asset_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('file_system'),
      $container->get('grapesjs_editor.asset_manager')
    );
  }

  /**
   * Returns a Json response with all asset.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The Json response.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   *   Thrown if the storage can't be found.
   */
  public function uploadAssets() {
    /* @var Editor $editor */
    if ($editor = Editor::load('grapesjs_editor')) {
      $image_upload = $editor->getImageUploadSettings();
      $max_filesize = Environment::getUploadMaxSize();
      $max_dimensions = 0;
      $directory = 'public://upload/grapesjs-editor';
      $assets = [];
      $errors = [];

      if (!empty($image_upload['status'])) {
        if (!empty($image_upload['max_dimensions']['width']) || !empty($image_upload['max_dimensions']['height'])) {
          $max_dimensions = $image_upload['max_dimensions']['width'] . 'x' . $image_upload['max_dimensions']['height'];
        }
        $max_filesize = min(Bytes::toInt($image_upload['max_size']), Environment::getUploadMaxSize());
        $directory = $image_upload['scheme'] . '://' . $image_upload['directory'];
      }

      if ($this->fileSystem->prepareDirectory($directory, FileSystemInterface::CREATE_DIRECTORY)) {
        $uploaded_files = file_save_upload('files', [
          'file_validate_extensions' => ['gif png jpg jpeg'],
          'file_validate_size' => [$max_filesize],
          'file_validate_image_resolution' => [$max_dimensions],
        ], $directory);
        $has_errors = array_reduce($uploaded_files, function ($carry, $file) {
          if (!($file instanceof File)) {
            return TRUE;
          }

          return $carry;
        }, FALSE);

        if ($has_errors) {
          $errors = $this->messenger()->deleteByType(MessengerInterface::TYPE_ERROR);
        }

        foreach ($uploaded_files as $file) {
          if ($file instanceof File) {
            $file->setPermanent();
            $file->save();

            $assets[] = $this->assetManager->buildAsset($file);
          }
        }
      }

      return new JsonResponse(['data' => $assets, 'errors' => $errors]);
    }

    return new JsonResponse(['error' => $this->t('Editor not found')], Response::HTTP_NOT_FOUND);
  }

}
