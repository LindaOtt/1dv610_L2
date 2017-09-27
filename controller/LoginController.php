<?php
namespace controller;

class LoginController {
  private $user;
  private $layoutView;
  private $loginView;
  private $dateTimeView;

  function __construct(\model\User $user, \view\LayoutView $layoutView, \view\LoginView $loginView, \view\DateTimeView $dateTimeView) {
    $this->user = $user;
    $this->layoutView = $layoutView;
    $this->loginView = $loginView;
    $this->dateTimeView = $dateTimeView;
  }

  public function runLoginSystem() {
    //Let the user object check if it is logged in
    if ($this->user->getIsLoggedInWithSession()) {
      $this->layoutView->render(true, $this->user, $this->loginView, $this->dateTimeView);
    }
    else if ($this->user->getHasJustTriedToLogIn() && $this->user->getIsLoggedInWithForm()) {
      $this->layoutView->render(true, $this->user, $this->loginView, $this->dateTimeView);
      $this->user->createLoginSession();
    }
    else {
      $this->layoutView->render(false, $this->user, $this->loginView, $this->dateTimeView);
    }
  }

}
