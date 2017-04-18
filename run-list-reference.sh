#!/bin/bash
source ./environment.sh

HARVESTER_PATH="$BASEPATH/abstract-harvester-base/src/CHAOS/Harvester/ChaosHarvester.php"
CONFIGURATION_PATH="$DIR/configurations/PSDBConfiguration.xml"
php $HARVESTER_PATH --configuration=$CONFIGURATION_PATH --mode=list-reference --reference=kulturarv-dka
