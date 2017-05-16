#!/bin/bash
source ./environment.sh

HARVESTER_PATH="$BASEPATH/abstract-harvester-base/src/CHAOS/Harvester/ChaosHarvester.php"
CONFIGURATION_PATH="$DIR/configurations/PSDBConfiguration.xml"
# php $HARVESTER_PATH --configuration=$CONFIGURATION_PATH --mode=single-reference --reference=urn:dr:mu:programcard:590065166187a414a0495c63 --debug --debug-metadata
php $HARVESTER_PATH --configuration=$CONFIGURATION_PATH --mode=single-reference --reference=urn:dr:mu:programcard:5914202ca11f9f12ec2c0f9d --debug --debug-metadata
