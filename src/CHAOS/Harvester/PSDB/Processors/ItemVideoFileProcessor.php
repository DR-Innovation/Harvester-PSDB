<?php

namespace CHAOS\Harvester\PSDB\Processors;

use CHAOS\Harvester\Loadable;
use CHAOS\Harvester\Processors\FileProcessor;
use CHAOS\Harvester\Shadows\ObjectShadow;

class ItemVideoFileProcessor extends FileProcessor {

  public function process(&$externalObject, &$shadow = null) {
		// Precondition
		assert($shadow instanceof ObjectShadow);
    // Add the primary image as a file shadow
    $primary_asset = $externalObject->PrimaryAsset;
    assert($primary_asset->Kind === 'VideoResource', 'Expecting a video');
    $links = $primary_asset->Links;
    $hls_link = array_reduce($links, function($result, $link) {
      if($link->Target === 'HLS') {
        return $link;
      }
    }, null);

    assert($hls_link, 'Expected at least one HLS link');
    $fileShadow = $this->createFileShadowFromURL($hls_link->Uri);
    $shadow->fileShadows[] = $fileShadow;
  }
}
