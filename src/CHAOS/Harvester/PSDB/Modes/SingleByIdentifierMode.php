<?php

namespace CHAOS\Harvester\PSDB\Modes;

use CHAOS\Harvester\Modes\SingleByReferenceMode;
use CHAOS\Harvester\Loadable;

class SingleByIdentifierMode extends SingleByReferenceMode implements Loadable {

	/**
	 * Constructs the PSDB Client.
	 * @param \CHAOS\Harvester\ChaosHarvester $harvester
	 * @param string $name The name of the Loadable in the harvester.
	 */
	public function __construct($harvester, $name, $parameters) {

  }

  public function execute($reference) {
    echo "Executing SingleByIdentifierMode with $reference\n";
  }
}
