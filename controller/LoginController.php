<?php
namespace controller;

class LoginController {
  private $loginModel;
  private $layoutView;
  private $loginView;
  private $dateTimeView;

  private $wantsToRegisterUser = false;
  private $isLoggedIn = false;

  function __construct(\model\LoginModel $loginModel, \view\LayoutView $layoutView, \view\LoginView $loginView, \view\DateTimeView $dateTimeView) {
    $this->loginModel = $loginModel;
    $this->layoutView = $layoutView;
    $this->loginView = $loginView;
    $this->dateTimeView = $dateTimeView;

    //Checks if user wants to register a new user
    if($this->loginView->wantsToRegisterUser()) {
      //The user wants to register a new user
      $this->wantsToRegisterUser = true;
    }
  }

  public function runLoginSystem() {
    $this->isLoggedIn = $this->loginModel->getIsLoggedIn();
    $this->layoutView->render($this->isLoggedIn, $this->wantsToRegisterUser, $this->loginModel, $this->loginView, $this->dateTimeView);
  }

}
