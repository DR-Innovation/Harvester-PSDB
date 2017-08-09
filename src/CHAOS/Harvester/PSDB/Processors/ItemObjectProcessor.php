<?php

namespace CHAOS\Harvester\PSDB\Processors;

use CHAOS\Harvester\Loadable;
use CHAOS\Harvester\Processors\ObjectProcessor;
use CHAOS\Harvester\Shadows\ObjectShadow;

class ItemObjectProcessor extends ObjectProcessor {

  protected function generateQuery($externalObject) {
    $urn = $externalObject->Urn;
    $format = '(FolderID:%u AND ObjectTypeID:%u AND DKA-ExternalIdentifier:"%s")';
		return sprintf($format, $this->_folderId, $this->_objectTypeId, $urn);
	}

  public function process(&$externalObject, &$shadow = null) {
		$shadow = new ObjectShadow();
		$shadow = $this->initializeShadow($externalObject, $shadow);

    $urn = $externalObject->Urn;
    $this->_harvester->info("Processing `$urn`");

    // Process the files
    $this->_harvester->process('item_file_video', $externalObject, $shadow);
    $this->_harvester->process('item_file_audio', $externalObject, $shadow);
    // If no video or audio exists - let's skip this
    if(empty($shadow->fileShadows)) {
      $this->_harvester->debug('Skip object because empty file shadows.');
      $shadow->skipped = true;
    }

		$this->_harvester->process('item_file_thumbnail', $externalObject, $shadow);
		$this->_harvester->process('item_metadata_dka2', $externalObject, $shadow);

    //var_dump($shadow);
    $shadow->commit($this->_harvester);
    return $shadow;
  }
}
