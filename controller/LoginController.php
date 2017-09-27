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
    //Important: Keep order of if statements
    $isLoggedIn = false;

    if ($this->user->getIsLoggedInWithSession() && $this->user->getHasLoggedOut()) {
      $this->user->setIsLoggedInWithSession(false);
      $this->user->terminateLoginSession();
    }
    else if (!$this->user->getIsLoggedInWithSession() && $this->user->getHasLoggedOut()) {
      $this->user->setHasLoggedOutWithoutSession(true);
    }
    else if ($this->user->getIsLoggedInWithSession() && !$this->user->getHasLoggedOut()) {
      $isLoggedIn = true;
    }
    else if ($this->user->getHasJustTriedToLogIn() && $this->user->getIsLoggedInWithForm()) {
      $isLoggedIn = true;
      $this->user->createLoginSession();
    }
    $this->layoutView->render($isLoggedIn, $this->user, $this->loginView, $this->dateTimeView);

    /*
    if ($this->user->getHasLoggedOut()) {
      //Check if user is logged in
      if ($this->user->getIsLoggedInWithSession()) {
        $this->user->setIsLoggedInWithSession(false);
        $this->user->terminateLoginSession();
      }
      else {

      }
      $this->layoutView->render(false, $this->user, $this->loginView, $this->dateTimeView);
    }
    */
  }

}
