<?php
namespace controller;

class LoginController {
  private $loginModel;
  private $layoutView;
  private $loginView;
  private $dateTimeView;

  function __construct(\model\LoginModel $loginModel, \view\LayoutView $layoutView, \view\LoginView $loginView, \view\DateTimeView $dateTimeView) {
    $this->loginModel = $loginModel;
    $this->layoutView = $layoutView;
    $this->loginView = $loginView;
    $this->dateTimeView = $dateTimeView;
  }

/* To do: simplify function */

  public function runLoginSystem() {

    $isLoggedIn = false;
    $wantsToRegisterUser = false;

    //Checks if user wants to register a new user
    if($this->loginView->wantsToRegisterUser()) {
      //The user wants to register a new user
      $wantsToRegisterUser = true;
    }


    //The user is logged in with a session
    if ($this->loginModel->getIsLoggedInWithSession()) {
      //The user has just clicked the "logout" button

      if ($this->loginModel->getHasLoggedOut()) {
        $this->loginModel->setIsLoggedInWithSession(false);
        $this->loginModel->terminateLoginSession();
      }
      //The user has not logged out
      else {
        $isLoggedIn = true;
      }

    }
      
    //The user is not logged in with a session
    else {
      //The user has just clicked the "logout" button
      if ($this->loginModel->getHasLoggedOut()) {
          $this->loginModel->setHasLoggedOutWithoutSession(true);
      }
      //The user has just tried to log in with the log in form
      //The login was successful, username and password are correct
      else if ($this->loginModel->getHasJustTriedToLogIn() && $this->loginModel->getIsLoggedInWithForm()) {
        $isLoggedIn = true;
        $keepUserLogin = false;
        $this->loginModel->createLoginSession();
        //The user has clicked "keep me logged in" in the form
        if ($this->loginModel->getKeepUserLoggedIn() == true) {
          $this->loginModel->createLoginCookies(time()+180, true);
        }
      }
      //The user has not clicked the "logout" button
      //and has not tried to log in with the log in form
      else {
        //Check if the user is logged in with cookies
        if ($this->loginModel->getIsLoggedInWithCookies()) {
          //Check if the cookies are ok ()
          if ($this->loginModel->getIsCookieContentOK()) {
            $isLoggedIn = true;
          }
          else {
            //Remove cookies
            $this->loginModel->createLoginCookies(time()-1000, false);
            $isLoggedIn = false;
          }
        }
      }
    }

    //The user is logged in with a session, but has just clicked the "logout" button
    /*
    if ($this->loginModel->getIsLoggedInWithSession() && $this->loginModel->getHasLoggedOut()) {
      $this->loginModel->setIsLoggedInWithSession(false);
      $this->loginModel->terminateLoginSession();
    }
    //The user is not logged in with a session, but has still logged out, possibly by reloading the page
    else if (!$this->loginModel->getIsLoggedInWithSession() && $this->loginModel->getHasLoggedOut()) {
      $this->loginModel->setHasLoggedOutWithoutSession(true);
    }
    //The user is logged in with a session, and has not logged out yet
    else if ($this->loginModel->getIsLoggedInWithSession() && !$this->loginModel->getHasLoggedOut()) {
      $isLoggedIn = true;
    }
    //The user has just tried to log in with the log in form
    //The login was successful, username and password are correct
    else if ($this->loginModel->getHasJustTriedToLogIn() && $this->loginModel->getIsLoggedInWithForm()) {
      $isLoggedIn = true;
      $keepUserLogin = false;
      $this->loginModel->createLoginSession();
      //The user has clicked "keep me logged in" in the form
      if ($this->loginModel->getKeepUserLoggedIn() == true) {
        $this->loginModel->createLoginCookies(time()+180, true);
      }
    }
    //The user is logged in with a session
    else if ($this->loginModel->getIsLoggedInWithSession()) {
      $isLoggedIn = true;
    }
    //The user is not logged in with a session,
    //the user has not just tried to log in,
    //the user has not logged out without an active session
    else {
      //Check if the user is logged in with cookies
      if ($this->loginModel->getIsLoggedInWithCookies()) {
        //Check if the cookies are ok ()
        if ($this->loginModel->getIsCookieContentOK()) {
          $isLoggedIn = true;
        }
        else {
          //Remove cookies
          $this->loginModel->createLoginCookies(time()-1000, false);
          $isLoggedIn = false;
        }
      }
    }
    */

    $this->layoutView->render($isLoggedIn, $wantsToRegisterUser, $this->loginModel, $this->loginView, $this->dateTimeView);
  }

}
