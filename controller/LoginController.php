<?php
namespace controller;

require_once('view/LoginView.php');
require_once('model/LoginModel.php');
require_once('view/RegisterView.php');
require_once('view/LayoutView.php');
require_once('view/DateTimeView.php');

class LoginController {
  private $loginModel;
  private $layoutView;
  private $loginView;
  private $registerModel;
  private $registerView;

  private $isLoggedIn = false;
  private $failedLoginAttempt = false;
  private $wantsToRegisterUser = false;
  private $response = '';

  private static $goodbye = 'Bye bye!';
  private static $welcomeBack = 'Welcome back with cookie';
  private static $wrongInfoCookies = 'Wrong information in cookies';
  private static $welcome = 'Welcome';
  private static $welcomeremembered = 'Welcome and you will be remembered';
  private static $usernamemissing = 'Username is missing';
  private static $passwordmissing = 'Password is missing';
  private static $wrongnameorpassword = 'Wrong name or password';
  private static $registerednewuser = 'Registered new user';
  private static $registeredUsernamePasswordWrong = 'Username has too few characters, at least 3 characters. Password has too few characters, at least 6 characters.';
  private static $usernameWrong = 'Username has too few characters, at least 3 characters.';
  private static $passwordWrong = 'Password has too few characters, at least 6 characters.';


  function __construct() {
    //Create the login view
    $this->loginView = new \view\LoginView();

    //Get the login model from the login view
    $this->loginModel = $this->loginView->createLogin();

    //Create the register view
    $this->registerView = new \view\RegisterView();

    //Get the register model from the register view
    $this->registerModel = $this->registerView->createRegister();

    //Create the datetime view
    $this->dateTimeView = new \view\DateTimeView();

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

    //Check login state
    //If the user is logged in with session and has just logged out,
    //terminate login session
    if ($isLoggedInWithSession) {
      if ($hasLoggedOut) {
        $this->loginModel->terminateLoginSession();
        $message = self::$goodbye;
      }
      else {
        $this->isLoggedIn = true;
        $this->generateLogout = true;
      }
    }
    else {
      //The user is not logged in with session, but has still logged out
      if ($hasLoggedOut) {
        $message = '';
      }
      //The user is not logged in with session, but is logged in with cookies
      else if ($isLoggedInWithCookies) {
        //Cookie has correct username and password
        if ($cookieContentIsOk) {
          $message = self::$welcomeBack;
          $this->isLoggedIn = true;
          $generateLogout = true;
        }
        //The cookie has wrong username and/or password, remove cookies
        else {
          $message = self::$wrongInfoCookies;
          $this->loginModel->removeCookies();
          $this->failedLoginAttempt = true;
        }
      }
      //The user is not logged in with session,
      //Is not logged in with cookies,
      //But has just tried to log in
      else if ($hasJustTriedToLogIn) {
        //The log in was successful
        if ($isLoggedInWithForm) {
            $this->isLoggedIn = true;
            $generateLogout = true;
            //Try to create a login session
            if ($this->loginModel->createLoginSession()) {
              //If a login session was created, store the session data
              $this->loginModel->storeSessionData();
              $message = self::$welcome;
            }
            //The user has clicked "keep me logged in" in the form
            if ($keepUserLoggedIn) {
              $time = time()+180;
              $this->loginModel->createLoginCookies($time, true);
              $this->loginModel->storeSessionCookieData($time);
              $message = self::$welcomeremembered;
            }
      }
      //Has just tried to log in, but is not logged in with form (log in was unsuccessful)
      else {
        //Check if username is missing
        if ($userNameMissing) {
          $message = self::$usernamemissing;
        }
        //Check if password is missing
        else if ($passwordMissing) {
          $message = self::$passwordmissing;
        }
        else {
          $message = self::$wrongnameorpassword;
        }
      }
    }
    //The user wants to register a new user, and has clicked the register link
    else if ($wantsToRegisterUser) {
      if ($registerFormHasBeenPosted) {
        if ($registerUserNameOk && $registerPasswordOk) {
          $message = self::$registerednewuser;
        }
        else if ($registerUserNameOk == false && $registerPasswordOk == false) {
          $message = self::$registeredUsernamePasswordWrong;
          $showLoginViewResponse = false;
        }
        else {
          $showLoginViewResponse = false;
          if ($registerUserNameOk == false) {
            $message = self::$usernameWrong;
          }
          else if ($registerPasswordOk == false) {
            $message .= self::$passwordWrong;
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

  }

}
