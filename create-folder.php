<?php
// Add the base harvester to the path
define('BASE_HARVESTER_PATH', '../abstract-harvester-base');
set_include_path(get_include_path() . PATH_SEPARATOR . BASE_HARVESTER_PATH);

// Add the CHAOS client to the path
define('CHAOS_CLIENT_PATH', BASE_HARVESTER_PATH . '/lib/CHAOS-Client/src/');
set_include_path(get_include_path() . PATH_SEPARATOR . CHAOS_CLIENT_PATH);

// Reuse the case sensitive autoloader.
require_once('CaseSensitiveAutoload.php');

// Register this autoloader.
spl_autoload_extensions('.php');
spl_autoload_register('CaseSensitiveAutoload');

$servicePath = getenv('CHAOS_URL');
$clientGUID = 'cffbece0-2413-11e7-a9f3-ef0a3ff3cf90';
$chaos = new \CHAOS\Portal\Client\PortalClient($servicePath, $clientGUID);

$email = getenv('CHAOS_EMAIL');
$password = getenv('CHAOS_PASSWORD');
$chaos->EmailPassword()->login($email, $password);

$parentID = 437;
// $new_folder = $chaos->Folder()->Create(null, 'DR PSDB', 1, $parentID);
// var_dump($new_folder);
$folders = $chaos->Folder()->Get(null, $parentID);
var_dump($folders);
