<?php
include_once 'api.php';
$api = new FF_Modules_Manager_API();
return $api->uninstall($payload['module']);