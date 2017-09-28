<?php
//$loggedInUser, $isThereALoggedInUser
//Add md5-hash encryption and salt when saving usernames and passwords
//Add salt to settings file
//Don't save username and password on GitHub, generate each time
//Make sure .gitignore is not available through web browser
//Add plenty of validation in register class
namespace model;

class User {
  private $submitUsername;
  private $submitPassword;
  private $correctUserName;
  private $correctPassword;

  private $sessionUserName;
  private $sessionPassword;
  private $isLoggedInWithSession = false;
  private $isLoggedInWithForm = false;
  private $userNameMissing = false;
  private $passwordMissing = false;
  private $hasJustTriedToLogIn = false;
  private $hasLoggedOut = false;
  private $hasLoggedOutWithoutSession = false;
  private $keepUserLoggedIn = false;

  private static $LOGIN_SESSION_ID = "model::User::userLogin";
  private static $COOKIE_NAME = "model::User::CookieName";
  private static $COOKIE_PASSWORD = "model::User::CookiePassword";


  function __construct($formLoginName, $formPassword, $hasJustTriedToLogin, $hasLoggedOut, $keepUserLoggedIn) {
    assert(session_status() != PHP_SESSION_NONE);

    //Setting whether the login form has just been submitted
    $this->hasJustTriedToLogin = $hasJustTriedToLogin;

    //Setting whether the user has logged out
    $this->hasLoggedOut = $hasLoggedOut;

    //Setting whether the user wants to be kept logged in
    $this->keepUserLoggedIn = $keepUserLoggedIn;

    //Setting the username and password
    $this->submitUsername = $formLoginName;
    $this->submitPassword = $formPassword;

    //Getting the correct username and password from the settings file
    $settings = parse_ini_file('./settings/settings.ini');
    $this->correctUserName = $settings['user'];
    $this->correctPassword = $settings['password'];

    $this->submitUsername = $formLoginName;
    $this->submitPassword = $formPassword;

    //Check if the user is logged in
    $this->checkLoginState();

  }

  function checkLoginState() {
    $this->isLoggedInWithSession = $this->isLoggedInWithSession();
    if ($this->hasJustTriedToLogin) {
      $this->isLoggedInWithForm = $this->isLoggedInWithForm();
    }
    $this->userNameMissing = $this->isUserNameMissing();
    $this->passwordMissing = $this->isPasswordMissing();
  }

  function getIsLoggedInWithSession() {
    return $this->isLoggedInWithSession;
  }

  function setIsLoggedInWithSession($isLoggedInWithSession) {
    $this->isLoggedInWithSession = false;
  }

  function getHasLoggedOutWithoutSession() {
    return $this->hasLoggedOutWithoutSession;
  }

  function setHasLoggedOutWithoutSession($hasLoggedOutWithoutSession) {
    $this->hasLoggedOutWithoutSession = $hasLoggedOutWithoutSession;
  }

  function getIsLoggedInWithForm() {
    return $this->isLoggedInWithForm;
  }

  function getKeepUserLoggedIn() {
    return $this->keepUserLoggedIn;
  }

  function getUserNameMissing() {
    return $this->userNameMissing;
  }

  function getPasswordMissing() {
    return $this->passwordMissing;
  }

  function getHasJustTriedToLogIn() {
    return $this->hasJustTriedToLogin;
  }

  function getHasLoggedOut() {
    return $this->hasLoggedOut;
  }

  function isUserNameMissing() {
    return $this->submitUsername == '';
  }

  function isPasswordMissing() {
    return $this->submitPassword == '';
  }

  /**
  * Check that the submitted username matches the username in the settings file
  */
  function userNameIsCorrect() {
    return ($this->submitUsername == $this->correctUserName) == true;
  }

  /**
  * Check that the submitted password matches the password in the settings file
  */
  function passwordIsCorrect() {
    return ($this->submitPassword == $this->correctPassword) == true;
  }

  function loginIsCorrect() {
    return ($this->userNameIsCorrect() && $this->passwordIsCorrect()) == true;
  }

  function isLoggedInWithSession() {
    return isset ($_SESSION[self::$LOGIN_SESSION_ID]) == true;
  }

  function isLoggedInWithForm() {
    return ($this->loginIsCorrect()) == true;
  }

  function createLoginSession($keepUserLogin) {
    //if ($keepUserLogin == true) {
      $_SESSION[self::$LOGIN_SESSION_ID] = true;
      /*
      $_SESSION[self::$COOKIE_NAME] = $this->correctUserName;
      //Create a random password
      $_SESSION[self::$COOKIE_PASSWORD] = password_hash($this->correctPassword, PASSWORD_DEFAULT);
      */
      $_SESSION[self::$COOKIE_NAME] = $this->correctUserName;
      $_SESSION[self::$COOKIE_PASSWORD] = password_hash($this->correctPassword, PASSWORD_DEFAULT);
    //}
    //else {
      //$_SESSION[self::$LOGIN_SESSION_ID] = true;
    //}
  }

  function terminateLoginSession() {
    unset($_SESSION['self::$LOGIN_SESSION_ID']);
    unset($_SESSION['self::$COOKIE_NAME']);
    unset($_SESSION['self::$COOKIE_PASSWORD']);
  }

}
