<?php

namespace CHAOS\Harvester\PSDB\Modes;

use CHAOS\Harvester\Modes\SetByReferenceMode;
use CHAOS\Harvester\Loadable;

class ListByReferenceMode extends SetByReferenceMode implements Loadable {

  const LIMIT = 10;

  public function execute($reference) {
    $psdb = $this->_harvester->getExternalClient('psdb');
    // Fetch the list by reference
    $this->_harvester->info("Fetching the `$reference` list");

    $offset = 210;
    do {
      $list = $psdb->getList($reference, self::LIMIT, $offset);
      $items = $list->Items;
      foreach($items as $item) {
        if($item->Type === 'ProgramCard') {
          $detailedItem = $psdb->getProgramCard($item->Urn, true);
          try {
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
}
