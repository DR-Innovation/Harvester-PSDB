<?php
namespace CHAOS\Harvester\PSDB;

use CHAOS\Harvester\IExternalClient;
use CHAOS\Harvester\Loadable;
use DR\PSDB\PSDBClient;

class LoadablePSDBClient extends PSDBClient implements IExternalClient {

	/**
	 * Constructs the PSDB Client.
	 * @param \CHAOS\Harvester\ChaosHarvester $harvester
	 * @param string $name The name of the Loadable in the harvester.
	 */
	public function __construct($harvester, $name, $parameters) {
		parent::__construct($parameters['baseUrl']);
  }

  public function sanityCheck() {
    return true;
  }

}
