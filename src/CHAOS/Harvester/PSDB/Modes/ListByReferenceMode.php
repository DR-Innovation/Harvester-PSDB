<?php

namespace CHAOS\Harvester\PSDB\Modes;

use CHAOS\Harvester\Modes\SetByReferenceMode;
use CHAOS\Harvester\Loadable;

use RuntimeException;

assert_options(ASSERT_CALLBACK, function($msg) {
	throw new RuntimeException($msg);
});

class ListByReferenceMode extends SetByReferenceMode implements Loadable {

  const LIMIT = 10;

  public function execute($reference) {
    $psdb = $this->_harvester->getExternalClient('psdb');
    // Fetch the list by reference
    $this->_harvester->info("Fetching the `$reference` list");

    $offset = 0;
    do {
      $list = $psdb->getList($reference, self::LIMIT, $offset);

      if (
          !is_object($list) ||
          !isset($list->TotalSize) ||
          !is_int($list->TotalSize) ||
          !isset($list->Items) ||
          !is_array($list->Items)
      ) {
          $this->_harvester->info('Unexpected response from PSDB: ' . var_export($list, true));
          throw new \RuntimeException('Unexpected response from PSDB');
      }

      $this->_harvester->info('TotalSize of list: ' . $list->TotalSize);
      $items = $list->Items;
      foreach($items as $item) {
        if($item->Type === 'ProgramCard') {
          try {
            $detailedItem = $psdb->getProgramCard($item->Urn, true);
            $itemShadow = $this->_harvester->process('item', $detailedItem);
          } catch(\Exception $e) {
            $this->_harvester->registerProcessingException($e, $movieObject, $movieShadow);
          }
        } else {
          throw new \RuntimeException("Unexpected item type {$item->Type}");
        }
      }
      $offset += self::LIMIT;
    } while($offset < $list->TotalSize);
  }

	public function shouldCleanUp() {
		return true;
	}
}
