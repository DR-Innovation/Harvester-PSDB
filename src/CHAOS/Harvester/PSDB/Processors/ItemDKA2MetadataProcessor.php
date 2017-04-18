<?php

namespace CHAOS\Harvester\PSDB\Processors;

use \SimpleXMLElement;

use CHAOS\Harvester\Processors\MetadataProcessor;
use CHAOS\Harvester\Shadows\ObjectShadow;

class ItemDKA2MetadataProcessor extends MetadataProcessor {

  /**
	 * This name will be used as the organisation when generating XML.
	 * @var string
	 */
  const ORGANIZATION_NAME = 'DR';

	/**
	 * This string will be used as RightsDescription when generating XML.
	 * @var string
	 */
	const RIGHTS_DESCIPTION = 'Copyright © DR';

  public function generateMetadata($externalObject, &$shadow = null) {
		// Precondition
		assert($shadow instanceof ObjectShadow);
    // Add the primary image as a file shadow
		$result = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" standalone="yes"?><DKA xmlns="http://www.danskkulturarv.dk/DKA2.xsd"></DKA>');

    // Mapping metadata
    $title = $externalObject->Title;
    $result->addChild('Title', trim($title));
    $result->addChild('Abstract', '');
    $result->addChild('Description', $externalObject->Description);
    $result->addChild('Organization', self::ORGANIZATION_NAME);
    $external_url = 'https://www.dr.dk/tv/se/-/-/' . $externalObject->Slug;
    $result->addChild('ExternalURL', $external_url);
    $result->addChild('ExternalIdentifier', $externalObject->Urn);
    $result->addChild('Type', 'Video');

    $result->addChild('Contributors');
    $result->addChild('Creators');
    $result->addChild('TechnicalComment');
    $result->addChild('Location');
		$result->addChild('RightsDescription', self::RIGHTS_DESCIPTION);
    $result->addChild('Categories');

    $tags = $result->addChild('Tags');
    $subtitleExploded = explode(';', $externalObject->Subtitle);
    foreach($subtitleExploded as $tagString) {
      $tags->addChild('Tag', htmlspecialchars(trim($tagString)));
    }

    return $result;
  }
}
