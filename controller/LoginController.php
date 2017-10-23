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

    //$userNameMissing = $this->loginView->checkUserNameMissing();

    //Check login state
    //If the user is logged in with session and has just logged out,
    //terminate login session
    if ($isLoggedInWithSession) {
      error_log("Logged in with session", 3, "errors.log");
      if ($hasLoggedOut) {
        error_log("Logged in with session and has logged out", 3, "errors.log");
        $loginView->terminateLoginSession();
        $message = self::$goodbyeMessage;
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
          $generateLogout = true;
        }
        //The cookie has wrong username and/or password, remove cookies
        else {
          error_log("Cookie content not ok", 3, "errors.log");
          $this->view->removeCookies();
          $this->failedLoginAttempt = true;
        }
      }
      //The user is not logged in with session,
      //Is not logged in with cookies,
      //But has just logged in successfully
      else if ($hasJustTriedToLogIn) {
        error_log("Has just tried to log in", 3, "errors.log");
        if ($isLoggedInWithForm) {
            error_log("Is logged in with form", 3, "errors.log");
            //Try to create a login session
            if ($this->loginModel->createLoginSession()) {
              error_log("Has created login session", 3, "errors.log");
              //If a login session was created, store the session data
              $this->loginView->storeSessionData();
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
            $generateLogout = true;
          //}
      }
      //Has tried to log in, but unsuccessfully
      else {
        error_log("Unsuccessful login", 3, "errors.log");
        //Check if username is missing
        if ($userNameMissing) {
          error_log("Username is missing", 3, "errors.log");
          $message = 'Username is missing';
        }
        //Check if password is missing
        else if ($passWordMissing) {
          error_log("Password is missing", 3, "errors.log");
          $message = 'Password is missing';
        }
        else {
          error_log("Wrong name or password", 3, "errors.log");
          $message = 'Wrong name or password';
        }
      //}
    }
  }
}
    $this->response = $this->loginView->response($message,$generateLogout);

    //Send information from what user wants to do to layout view and show the right view
    $this->layoutView->render($this->isLoggedIn, $this->failedLoginAttempt, $this->wantsToRegisterUser, $this->response);

    //$this->layoutView->render($this->loginModel, $this->loginView, $this->dateTimeView, $this->registerModel, $this->registerView);
  }

  public function getUserNameFromView() : string {

  }

}
