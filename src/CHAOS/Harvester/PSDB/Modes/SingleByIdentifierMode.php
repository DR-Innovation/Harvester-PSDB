<?php

namespace CHAOS\Harvester\PSDB\Modes;

use CHAOS\Harvester\Modes\SingleByReferenceMode;
use CHAOS\Harvester\Loadable;

use RuntimeException;

assert_options(ASSERT_CALLBACK, function($msg) {
	throw new RuntimeException($msg);
});

class SingleByIdentifierMode extends SingleByReferenceMode implements Loadable {

	public function execute($reference) {
		$psdb = $this->_harvester->getExternalClient('psdb');
		// Fetch the list by reference
		$this->_harvester->info("Fetching the `$reference` ProgramCard");

		$detailedItem = $psdb->getProgramCard($reference, true);
		try {
			$itemShadow = $this->_harvester->process('item', $detailedItem);
		} catch(\Exception $e) {
			$this->_harvester->registerProcessingException($e, $movieObject, $movieShadow);
		}
	}
}
