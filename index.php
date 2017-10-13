<?php
/* To do: Add exception handling */
session_start();
require_once('controller/LoginController.php');
require_once('model/LoginModel.php');
require_once('view/LoginView.php');
require_once('view/DateTimeView.php');
require_once('view/LayoutView.php');

//MAKE SURE ERRORS ARE SHOWN... MIGHT WANT TO TURN THIS OFF ON A PUBLIC SERVER
error_reporting(E_ALL);
ini_set('display_errors', 'On');

//Create main layout view and datetime view
$layoutView = new \view\LayoutView();
$dateTimeView = new \view\DateTimeView();

//Create the login view
$loginView = new \view\LoginView();

//Get the user object from the login view
$loginModel = $loginView->createLogin();

$controller = new \controller\LoginController($loginModel, $layoutView, $loginView, $dateTimeView);
try {
  $controller->runLoginSystem();
}
catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}
