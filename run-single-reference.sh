#!/bin/bash
source ./environment.sh

HARVESTER_PATH="$BASEPATH/abstract-harvester-base/src/CHAOS/Harvester/ChaosHarvester.php"
CONFIGURATION_PATH="$DIR/configurations/PSDBConfiguration.xml"
php $HARVESTER_PATH --configuration=$CONFIGURATION_PATH --mode=single-reference --reference=urn:dr:mu:programcard:5901ad3da11f9f112c7544e6 --debug
