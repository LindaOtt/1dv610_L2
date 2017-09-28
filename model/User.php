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
  private $isLoggedInWithCookies = false;
  private $isCookieContentOK = false;
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
    $this->isLoggedInWithCookies = $this->isThereALoginCookie();
    $this->isCookieContentOK = $this->isCookieContentOK();
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

  function getIsLoggedInWithCookies() {
    return $this->isLoggedInWithCookies;
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

  function getIsCookieContentOK() {
    return $this->isCookieContentOK;
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

  function createLoginSession() {
      $_SESSION[self::$LOGIN_SESSION_ID] = true;
  }

  function createLoginCookies($time) {
    setcookie(self::$COOKIE_NAME, $this->correctUserName, $time);
    setcookie(self::$COOKIE_PASSWORD, $this->encrypt($this->correctPassword), $time);
  }

  function isThereALoginCookie() : bool {
    $isTheCookieSet = isset($_COOKIE[self::$COOKIE_NAME]);
    //echo nl2br("\nisTheCookieSet: " . $isTheCookieSet. "\n");
    return isset($_COOKIE[self::$COOKIE_NAME]);
  }

  function terminateLoginSession() {
    unset($_SESSION[self::$LOGIN_SESSION_ID]);
    unset($_SESSION[self::$COOKIE_NAME]);
    unset($_SESSION[self::$COOKIE_PASSWORD]);
  }

  function isCookieNameOK() : bool {
    if (isset($_COOKIE[self::$COOKIE_NAME])) {
        return ($_COOKIE[self::$COOKIE_NAME] == $this->correctUserName);
    }
    return false;
  }

  function isCookiePasswordOK() : bool {
    if (isset($_COOKIE[self::$COOKIE_PASSWORD])) {
        return ($_COOKIE[self::$COOKIE_PASSWORD] == $this->encrypt($this->correctPassword));
    }
    return false;
  }

  function encrypt($contentToEncrypt) : string {
    return md5($contentToEncrypt);
  }

  function isCookieContentOK() : bool{
    return ($this->isCookieNameOK() && $this->isCookiePasswordOK());
  }

  function removeCookies() {

  }

}
