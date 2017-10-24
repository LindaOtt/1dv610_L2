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

  private static $goodbyeMessage = 'Bye bye!';
  private static $welcomeBackMessage = 'Welcome back with cookie';

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
    //Sets the message that the view should show to the user
    $message = '';
    //Sets whether the view should generate a logout button
    $generateLogout = false;
    //Check if the user is logged in with session
    $isLoggedInWithSession = $this->loginModel->getIsLoggedInWithSession();
    $isLoggedOutWithSession = $this->loginModel->getIsLoggedOutWithSession();
    //$isLoggedOutWithoutSession = $this->loginModel->getIsLoggedOutWithoutSession();
    $isLoggedInWithCookies = $this->loginModel->getIsLoggedInWithCookies();
    $cookieContentIsOk = $this->loginModel->getIsCookieContentOk();
    $hasJustTriedToLogIn = $this->loginView->hasJustTriedToLogIn();
    $isLoggedInWithForm = $this->loginModel->isLoggedInWithForm();
    $keepUserLoggedIn = $this->loginView->keepUserLoggedIn();
    $hasLoggedOut = $this->loginView->hasLoggedOut();
    $userNameMissing = $this->loginModel->isUserNameMissing();
    $passwordMissing = $this->loginModel->isPasswordMissing();
    $wantsToRegisterUser = $this->registerView->wantsToRegisterUser();
    $registerFormHasBeenPosted = $this->registerView->registerFormHasBeenPosted();
    $registerUserNameOk = $this->registerModel->getIsRegisterNameOk();
    $registerPasswordOk = $this->registerModel->getRegisterPasswordOk();
    $showLoginViewResponse = true;

    //$userNameMissing = $this->loginView->checkUserNameMissing();

    //Check login state
    //If the user is logged in with session and has just logged out,
    //terminate login session
    if ($isLoggedInWithSession) {
      error_log("Logged in with session", 3, "errors.log");
      if ($hasLoggedOut) {
        error_log("Logged in with session and has logged out", 3, "errors.log");
        $this->loginModel->terminateLoginSession();
        $message = self::$goodbyeMessage;
      }
      else {
        $this->isLoggedIn = true;
        $this->generateLogout = true;
      }
    }
    else {
      error_log("Not logged in with session", 3, "errors.log");
      //The user is not logged in with session, but has still logged out
      if ($hasLoggedOut) {
        error_log("Not logged in with session and has logged out", 3, "errors.log");
        $message = '';
      }
      //The user is not logged in with session, but is logged in with cookies
      else if ($isLoggedInWithCookies) {
        error_log("Logged in with cookies", 3, "errors.log");
        //Cookie has correct username and password
        if ($cookieContentIsOk) {
          error_log("Cookie content ok", 3, "errors.log");
          $message = self::$welcomeBackMessage;
          $this->isLoggedIn = true;
          $generateLogout = true;
        }
        //The cookie has wrong username and/or password, remove cookies
        else {
          error_log("Cookie content not ok", 3, "errors.log");
          $message = "Wrong information in cookies";
          $this->loginModel->removeCookies();
          $this->failedLoginAttempt = true;
        }
      }
      //The user is not logged in with session,
      //Is not logged in with cookies,
      //But has just tried to log in
      else if ($hasJustTriedToLogIn) {
        error_log("Has just tried to log in", 3, "errors.log");
        //The log in was successful
        if ($isLoggedInWithForm) {
            error_log("Is logged in with form", 3, "errors.log");
            $this->isLoggedIn = true;
            $generateLogout = true;
            //Try to create a login session
            if ($this->loginModel->createLoginSession()) {
              error_log("Has created login session", 3, "errors.log");
              //If a login session was created, store the session data
              $this->loginModel->storeSessionData();
              $message = 'Welcome';
            }
            //The user has clicked "keep me logged in" in the form
            if ($keepUserLoggedIn) {
              error_log("Keep user logged in", 3, "errors.log");
              $time = time()+180;
              $this->loginModel->createLoginCookies($time, true);
              $this->loginModel->storeSessionCookieData($time);
              $message = 'Welcome and you will be remembered';
            }
      }
      //Has just tried to log in, but is not logged in with form (log in was unsuccessful)
      else {
        error_log("Unsuccessful login", 3, "errors.log");
        //Check if username is missing
        if ($userNameMissing) {
          error_log("Username is missing", 3, "errors.log");
          $message = 'Username is missing';
        }
        //Check if password is missing
        else if ($passwordMissing) {
          error_log("Password is missing", 3, "errors.log");
          $message = 'Password is missing';
        }
        else {
          error_log("Wrong name or password", 3, "errors.log");
          $message = 'Wrong name or password';
        }
      }
    }
    //The user wants to register a new user, and has clicked the register link
    else if ($wantsToRegisterUser) {
      error_log("Wants to register user", 3, "errors.log");
      if ($registerFormHasBeenPosted) {
        error_log("Register form has been posted", 3, "errors.log");
        if ($registerUserNameOk && $registerPasswordOk) {
          error_log("Register username is ok and register password is ok", 3, "errors.log");
          $message = "Registered new user";
        }
        else if ($registerUserNameOk == false && $registerPasswordOk == false) {
          $message = "Username has too few characters, at least 3 characters. Password has too few characters, at least 6 characters.";
          $showLoginViewResponse = false;
        }
        else {
          $showLoginViewResponse = false;
          if ($registerUserNameOk == false) {
            $message = "Username has too few characters, at least 3 characters.";
          }
          else if ($registerPasswordOk == false) {
            $message .= "Password has too few characters, at least 6 characters.";  
          }
        }
      }
      else {
        $this->wantsToRegisterUser = true;
        //Since the user now wants to register a new user, do not show the login view
        $showLoginViewResponse = false;
      }
    }
}

    //Check if we should run the loginView response
    if ($showLoginViewResponse) {
        $this->response = $this->loginView->response($message,$generateLogout);
    }
    //If not, show the register view
    else {
      $this->response = $this->registerView->generateRegisterNewUserHTML($message);
    }

    //Send information from what user wants to do to layout view and show the right view
    $this->layoutView->render($this->isLoggedIn, $this->failedLoginAttempt, $this->wantsToRegisterUser, $this->response);

    //$this->layoutView->render($this->loginModel, $this->loginView, $this->dateTimeView, $this->registerModel, $this->registerView);
  }

  public function getUserNameFromView() : string {

  }

}
