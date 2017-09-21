<?php

//INCLUDE THE FILES NEEDED...

require_once('controller/LoginController.php');
require_once('model/User.php');

require_once('view/LoginView.php');

require_once('view/DateTimeView.php');

require_once('view/LayoutView.php');


//MAKE SURE ERRORS ARE SHOWN... MIGHT WANT TO TURN THIS OFF ON A PUBLIC SERVER
error_reporting(E_ALL);
ini_set('display_errors', 'On');

$controller = new \controller\LoginController();
$controller->runLoginSystem();
