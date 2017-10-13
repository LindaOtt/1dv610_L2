<?php
namespace controller;

class LoginController {
  private $loginModel;
  private $layoutView;
  private $loginView;
  private $dateTimeView;
  private $registerModel;
  private $registerView;
  //private $wantsToRegisterUser = false;
  private $isLoggedIn = false;
  private $failedLoginAttempt = false;

  function __construct(\model\LoginModel $loginModel, \view\LayoutView $layoutView, \view\LoginView $loginView, \view\DateTimeView $dateTimeView, \model\RegisterModel $registerModel, \view\RegisterView $registerView) {
    $this->loginModel = $loginModel;
    $this->layoutView = $layoutView;
    $this->loginView = $loginView;
    $this->dateTimeView = $dateTimeView;
    $this->registerModel = $registerModel;
    $this->registerView = $registerView;

    //Checks if user wants to register a new user
    /*
    if($this->loginView->wantsToRegisterUser()) {
      //The user wants to register a new user
      $this->wantsToRegisterUser = true;
    }
    */
  }

  public function runLoginSystem() {
    error_log("In runLoginSystem()\n", 3, "errors.log");
    $this->layoutView->render($this->loginModel, $this->loginView, $this->dateTimeView, $this->registerModel, $this->registerView);
  }

}
