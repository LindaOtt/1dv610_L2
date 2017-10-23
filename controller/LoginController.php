<?php
namespace controller;

require_once('view/LoginView.php');
require_once('model/LoginModel.php');
require_once('view/RegisterView.php');
require_once('view/LayoutView.php');

class LoginController {
  private $loginModel;
  private $layoutView;
  private $loginView;
  //private $dateTimeView;
  private $registerModel;
  private $registerView;

  //private $isLoggedInWithSession = false;
  //private $hasLoggedOutWithSession = false;

  private $isLoggedIn = false;
  private $failedLoginAttempt = false;
  private $wantsToRegisterUser = false;
  private $response = '';

/*
  function __construct(\model\LoginModel $loginModel, \view\LayoutView $layoutView, \view\LoginView $loginView, \view\DateTimeView $dateTimeView, \model\RegisterModel $registerModel, \view\RegisterView $registerView) {
    $this->loginModel = $loginModel;
    $this->layoutView = $layoutView;
    $this->loginView = $loginView;
    $this->dateTimeView = $dateTimeView;
    $this->registerModel = $registerModel;
    $this->registerView = $registerView;
  }
*/

  function __construct() {
    //Create the login view
    $this->loginView = new \view\LoginView();

    //Get the login model from the login view
    $this->loginModel = $this->loginView->createLogin();

    //Create the register view
    $this->registerView = new \view\RegisterView();

    //Get the register model from the register view
    $this->registerModel = $this->registerView->createRegister();

    //Create the layout view
    $this->layoutView = new \view\LayoutView();
  }

  public function runLoginSystem() {
    //Check if the user is logged in with session
    $isLoggedInWithSession = $this->loginModel->getIsLoggedInWithSession();
    $isLoggedOutWithSession = $this->loginModel->getIsLoggedOutWithSession();
    //$isLoggedOutWithoutSession = $this->loginModel->getIsLoggedOutWithoutSession();
    $isLoggedInWithCookies = $this->loginModel->getIsLoggedInWithCookies();
    $cookieContentIsOk = $this->loginModel->getCookieContentIsOk();
    $hasJustTriedToLogIn = $this->loginView->hasJustTriedToLogIn();
    $keepUserLoggedIn = $this->loginView->keepUserLoggedIn();
    $hasLoggedOut = $this->loginView->hasLoggedOut();

    //Check login state
    if ($isLoggedInWithSession) {

    }

    $this->response = $this->loginView->response();

    //Send information from what user wants to do to layout view and show the right view
    $this->layoutView->render($this->isLoggedIn, $this->failedLoginAttempt, $this->wantsToRegisterUser, $this->response);

    //$this->layoutView->render($this->loginModel, $this->loginView, $this->dateTimeView, $this->registerModel, $this->registerView);
  }

  public function checkRegisterModelState() {

  }

}
