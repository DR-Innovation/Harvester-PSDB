<?php

namespace CHAOS\Harvester\PSDB\Processors;

use CHAOS\Harvester\Loadable;
use CHAOS\Harvester\Processors\FileProcessor;
use CHAOS\Harvester\Shadows\ObjectShadow;
use DR\PSDB\EncryptedUri;

class ItemAudioFileProcessor extends FileProcessor {

  public function process(&$externalObject, &$shadow = null) {
		// Precondition
		assert($shadow instanceof ObjectShadow);
    // Add the primary image as a file shadow
    if(property_exists($externalObject, 'PrimaryAsset')) {
      $primary_asset = $externalObject->PrimaryAsset;
      if($primary_asset->Kind === 'AudioResource') {
        $links = $primary_asset->Links;
        $hls_link = array_reduce($links, function($result, $link) {
          if($link->Target === 'HLS') {
            return $link;
          }
        }, null);

        try {
            if (empty($hls_link->Uri) && isset($hls_link->EncryptedUri) && is_string($hls_link->EncryptedUri)) {
                $hls_link->Uri = (new EncryptedUri($hls_link->EncryptedUri))->uri();
                $this->_harvester->debug("Decrypted URI: {$hls_link->Uri}");
            }
        } catch (\Exception $e) {
            $this->_harvester->info("Could not decrypt encrypted URI: {$e->getMessage()}");
        }

        assert($hls_link, 'Expected at least one HLS link');
        $fileShadow = $this->createFileShadowFromURL($hls_link->Uri);
        if($fileShadow) {
          $shadow->fileShadows[] = $fileShadow;
        }
      }
    }
  }
}
