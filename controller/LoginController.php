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
      //echo "1. getIsLoggedInWithSession and getHasLoggedOut";
      $this->user->setIsLoggedInWithSession(false);
      $this->user->terminateLoginSession();
    }
    else if (!$this->user->getIsLoggedInWithSession() && $this->user->getHasLoggedOut()) {
      //echo "2. NOT getIsLoggedInWithSession and getHasLoggedOut";
      $this->user->setHasLoggedOutWithoutSession(true);
    }
    else if ($this->user->getIsLoggedInWithSession() && !$this->user->getHasLoggedOut()) {
      //echo "3. getIsLoggedInWithSession and NOT getHasLoggedOut";
      $isLoggedIn = true;
    }
    else if ($this->user->getHasJustTriedToLogIn() && $this->user->getIsLoggedInWithForm()) {
      //echo "4. getHasJustTriedToLogIn and getIsLoggedInWithForm";
      $isLoggedIn = true;
      $keepUserLogin = false;
      $this->user->createLoginSession();
      if ($this->user->getKeepUserLoggedIn() == true) {
        $this->user->createLoginCookies(time()+180);
      }
    }
    else if ($this->user->getIsLoggedInWithSession()) {
      //echo "5. getIsLoggedInWithSession";
      $isLoggedIn = true;
    }

    else {
      //Check if there are log in cookies
      if ($this->user->getIsLoggedInWithCookies()) {
        //echo "6. getIsLoggedInWithCookies";
        if ($this->user->getIsCookieContentOK()) {
          //echo "7. getIsCookieContentOK";
          $isLoggedIn = true;
        }
        else {
          //echo "8. else";
          //Remove cookies
          $this->user->createLoginCookies(time()-1000);
          $isLoggedIn = false;
        }
      }
    }

    $this->layoutView->render($isLoggedIn, $this->user, $this->loginView, $this->dateTimeView);
  }
}
