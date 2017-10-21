<?php
namespace controller;

class LoginController {
  private $loginModel;
  private $layoutView;
  private $loginView;
  private $dateTimeView;
  private $registerModel;
  private $registerView;

  function __construct(\model\LoginModel $loginModel, \view\LayoutView $layoutView, \view\LoginView $loginView, \view\DateTimeView $dateTimeView, \model\RegisterModel $registerModel, \view\RegisterView $registerView) {
    $this->loginModel = $loginModel;
    $this->layoutView = $layoutView;
    $this->loginView = $loginView;
    $this->dateTimeView = $dateTimeView;
    $this->registerModel = $registerModel;
    $this->registerView = $registerView;
  }

  public function runLoginSystem() {
    $this->layoutView->render($this->loginModel, $this->loginView, $this->dateTimeView, $this->registerModel, $this->registerView);
  }

}
