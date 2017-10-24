<?php

/*
function shutdown(){
  var_dump(error_get_last());
}

register_shutdown_function('shutdown');
*/

/* To do: Add exception handling */
session_start();
require_once('controller/LoginController.php');

//MAKE SURE ERRORS ARE SHOWN... MIGHT WANT TO TURN THIS OFF ON A PUBLIC SERVER
error_reporting(E_ALL);
ini_set('display_errors', 'On');

$controller = new \controller\LoginController();
try {
  $controller->runLoginSystem();
}
catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}
