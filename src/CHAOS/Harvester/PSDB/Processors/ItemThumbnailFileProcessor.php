<?php

namespace CHAOS\Harvester\PSDB\Processors;

use CHAOS\Harvester\Loadable;
use CHAOS\Harvester\Processors\FileProcessor;
use CHAOS\Harvester\Shadows\ObjectShadow;

class ItemThumbnailFileProcessor extends FileProcessor {

  public function process(&$externalObject, &$shadow = null) {
		// Precondition
		assert($shadow instanceof ObjectShadow);
    // Add the primary image as a file shadow
    $primary_image_url = $externalObject->PrimaryImageUri;
    $fileShadow = $this->createFileShadowFromURL($primary_image_url);
    $shadow->fileShadows[] = $fileShadow;
  }
}
