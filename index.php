<?php


function shutdown(){
  var_dump(error_get_last());
}

register_shutdown_function('shutdown');


/* To do: Add exception handling */
session_start();
require_once('controller/LoginController.php');
/*
require_once('model/LoginModel.php');
require_once('view/LoginView.php');
require_once('view/DateTimeView.php');
require_once('view/LayoutView.php');
require_once('view/RegisterView.php');
require_once('model/RegisterModel.php');
*/

//MAKE SURE ERRORS ARE SHOWN... MIGHT WANT TO TURN THIS OFF ON A PUBLIC SERVER
error_reporting(E_ALL);
ini_set('display_errors', 'On');

//Create main layout view and datetime view
/*
$layoutView = new \view\LayoutView();
$dateTimeView = new \view\DateTimeView();
*/

/*
//Create the login view
$loginView = new \view\LoginView();

//Create the register view
$registerView = new \view\RegisterView();

//Get the register object from the register view
$registerModel = $registerView->createRegister();

//Get the user object from the login view
$loginModel = $loginView->createLogin();
*/

//$controller = new \controller\LoginController($loginModel, $layoutView, $loginView, $dateTimeView, $registerModel, $registerView);
$controller = new \controller\LoginController();
try {
  $controller->runLoginSystem();
}
catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}
