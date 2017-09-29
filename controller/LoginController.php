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
    
    $isLoggedIn = false;
    $wantsToRegisterUser = false;

    //Checks if user wants to register a new user
    if($this->loginView->wantsToRegisterUser()) {
      $wantsToRegisterUser = true;
    }

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
      $keepUserLogin = false;
      $this->user->createLoginSession();
      if ($this->user->getKeepUserLoggedIn() == true) {
        $this->user->createLoginCookies(time()+180, true);
      }
    }
    else if ($this->user->getIsLoggedInWithSession()) {
      $isLoggedIn = true;
    }
    else {
      //Check if there are log in cookies
      if ($this->user->getIsLoggedInWithCookies()) {
        if ($this->user->getIsCookieContentOK()) {
          //echo "7. getIsCookieContentOK";
          $isLoggedIn = true;
        }
        else {
          //Remove cookies
          $this->user->createLoginCookies(time()-1000, false);
          $isLoggedIn = false;
        }
      }
    }
    $this->layoutView->render($isLoggedIn, $wantsToRegisterUser, $this->user, $this->loginView, $this->dateTimeView);
  }
}
