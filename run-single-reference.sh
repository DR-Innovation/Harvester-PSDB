#!/bin/bash
source ./environment.sh

HARVESTER_PATH="$BASEPATH/abstract-harvester-base/src/CHAOS/Harvester/ChaosHarvester.php"
CONFIGURATION_PATH="$DIR/configurations/PSDBConfiguration.xml"
php $HARVESTER_PATH --configuration=$CONFIGURATION_PATH --mode=single-reference --reference=urn:dr:mu:programcard:58f9f2c0a11fa0112c32e29d
