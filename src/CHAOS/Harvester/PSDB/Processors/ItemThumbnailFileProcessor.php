<?php

namespace CHAOS\Harvester\PSDB\Processors;

use CHAOS\Harvester\Processors\FileProcessor;
use CHAOS\Harvester\Shadows\ObjectShadow;

class ItemThumbnailFileProcessor extends FileProcessor {

  public function process(&$externalObject, &$shadow = null) {
    // Precondition
    assert($shadow instanceof ObjectShadow);

    $fileShadow = null;

    // Add the primary image as a file shadow
    $primary_image_url = $externalObject->PrimaryImageUri;

    // Check if image exists before adding as file shadow.
    $cp = curl_init($primary_image_url);
    curl_setopt($cp, CURLOPT_NOBODY, true);
    $result = curl_exec($cp);

    if (false !== $result) {
      $statusCode = curl_getinfo($cp, CURLINFO_HTTP_CODE);

      if (404 !== $statusCode) {
        $fileShadow = $this->createFileShadowFromURL($primary_image_url);
      }
    }

    $shadow->fileShadows[] = $fileShadow;
  }
}
