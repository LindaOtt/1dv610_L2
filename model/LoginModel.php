<?php
//To do: Add md5-hash encryption and salt when saving usernames and passwords
//Add salt to settings file
//Don't save username and password on GitHub, generate each time
//Add plenty of validation in register class
namespace model;

class LoginModel {
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

  private $isLoggedIn = false;
  private $keepUserLoggedIn = false;
  private $failedLoginAttempt = false;
  private $isLoggedInWithCookiesAndNoSession = false;

  private $firstLoginWithoutSession = false;

  private static $LOGIN_SESSION_ID = 'model::LoginModel::userLogin';
  //private static $COOKIE_NAME = "model::LoginModel::CookieName";
  private static $COOKIE_NAME = "LoginView::CookieName";
  //private static $COOKIE_PASSWORD = "model::LoginModel::CookiePassword";
  //LoginView::CookiePassword
  private static $COOKIE_PASSWORD = "LoginView::CookiePassword";

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

    //The user is logged in with a session
    if ($this->isLoggedInWithSession()) {
      error_log("Model: isLoggedInWithSession\n", 3, "errors.log");
      $this->checkIfLoggedOutWithSession();
    }

    //The user is not logged in with a session
    else {
      error_log("Model: is NOT logged in with session\n", 3, "errors.log");
      //Checks if the user is logged out without session, if so stop executing main function
      if ($this->checkIfLoggedOutWithoutSession() == true) {
        error_log("Model: checkIfLoggedOutWithoutSession\n", 3, "errors.log");
        return true;
      }

      //Check if the user is logged in with cookies, if so stop executing main function
      if ($this->checkIfLoggedInWithCookies() == true) {
        error_log("Model: checkIfLoggedInWithCookies was true\n", 3, "errors.log");
        return true;
      }

      //Check if the user logged in successfully, if so stop executing main function
      if ($this->checkIfLoggedInSuccessfully() == true) {
        error_log("Model: checkIfLoggedInSuccessfully\n", 3, "errors.log");
        return true;
      }

    }
  }

  function getIsLoggedIn() {
    return $this->isLoggedIn;
  }

  function getIsLoggedInWithSession() {
    return $this->isLoggedInWithSession();
  }

  function checkIfLoggedOutWithSession() {
    //The user has just clicked the "logout" button
    if ($this->hasLoggedOut == true) {
      $this->setIsLoggedInWithSession(false);
      $this->terminateLoginSession();
    }
    //The user has not logged out
    else {
      $this->isLoggedIn = true;
    }
  }

  function checkIfLoggedOutWithoutSession() {
    //The user has just clicked the "logout" button
    if ($this->hasLoggedOut == true) {
        $this->setHasLoggedOutWithoutSession(true);
        return true;
    }
    return false;
  }

  function checkIfLoggedInSuccessfully() {
    //The user has just tried to log in with the log in form
    //The login was successful, username and password are correct
    if ($this->getHasJustTriedToLogIn() && $this->getIsLoggedInWithForm()) {
      $this->isLoggedIn = true;
      $this->firstLoginWithoutSession = true;
      $this->keepUserLogin = false;
      $this->createLoginSession();
      //The user has clicked "keep me logged in" in the form
      if ($this->getKeepUserLoggedIn() == true) {
        $this->createLoginCookies(time()+180, true);
      }
      return true;
    }
    return false;
  }

  function getFailedLoginAttempt() {
    return $this->failedLoginAttempt;
  }

  function checkIfLoggedInWithCookies() {
    error_log("Model: Inside checkIfLoggedInWithCookies()\n", 3, "errors.log");

    //Check if the user is logged in with cookies
    if ($this->getIsLoggedInWithCookies()) {
      error_log("Model: function checkIfLoggedInWithCookies(), Inside getIsLoggedInWithCookies()\n", 3, "errors.log");
      //Check if the cookies are ok ()
      if ($this->isCookieContentOK()) {
        error_log("Model: function checkIfLoggedInWithCookies(), isCookieContentOK\n", 3, "errors.log");
        $this->isLoggedIn = true;
        $this->isLoggedInWithCookiesAndNoSession = true;
        return true;
      }
      else {
        error_log("Model: function checkIfLoggedInWithCookies(), else\n", 3, "errors.log");
        //Remove cookies
        $this->createLoginCookies(time()-1000, false);
        $this->isLoggedIn = false;
        $this->failedLoginAttempt = true;
        return false;
      }

    }
    return false;
  }

  function setIsLoggedInWithSession($isLoggedInWithSession) {
    $this->isLoggedInWithSession = false;
  }

  function getHasLoggedOutWithoutSession() {
    return $this->hasLoggedOutWithoutSession;
  }

  function getIsLoggedInWithCookiesAndNoSession() {
    return $this->isLoggedInWithCookiesAndNoSession;
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

  function getFirstLoginWithoutSession() {
    return $this->firstLoginWithoutSession;
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
    if (isset ($_SESSION[self::$LOGIN_SESSION_ID])) {
      return true;
    }
    else {
      return false;
    }
  }

  function isLoggedInWithForm() {
    return ($this->loginIsCorrect()) == true;
  }

  function createLoginSession() {
      $_SESSION[self::$LOGIN_SESSION_ID] = true;
  }

  function createLoginCookies($time, $storeTime) {
    setcookie(self::$COOKIE_NAME, $this->correctUserName, $time);
    setcookie(self::$COOKIE_PASSWORD, $this->encrypt($this->correctPassword), $time);
    $this->storeCookieTime($time);
  }

  function storeCookieTime($time) {
    // Open the file to get existing content
    $content = file_get_contents('db/db.txt');
    //Append session id to file
    $content .= "\n[".$this->getSessionID()."]\n";
    // Append the time to the file
    $content .= $time;
    // Write the contents back to the file
    file_put_contents('db/db.txt', $content);
  }

  function getLineWithString($fileName, $str) {
    $lines = file($fileName);
    foreach ($lines as $lineNumber => $line) {
        if (strpos($line, $str) !== false) {
            return $line;
        }
    }
    return -1;
  }

  function isCookieTimeOK() : bool {
    error_log("Inside isCookieTimeOK()\n", 3, "errors.log");
    // Open the file to get existing content
    $content = file_get_contents('db/db.txt');
    //Find the line with the current session
    $posOfCurrentSession = strpos($content, $this->getSessionID());
    error_log("posOfCurrentSession: $posOfCurrentSession\n", 3, "errors.log");
    //Check if the current session exists in the text file
    if ($posOfCurrentSession === false) {
      return false;
    }
    else {
      //If it does, check if the time in the text file matches the time of the cookie
      $contentLine = $this->getLineWithString('db/db.txt', $this->getSessionID());
      error_log("contentLine: $contentLine\n", 3, "errors.log");
      //Get the last part of the line that contains the time
      //$contents = explode(']', $contentLine);
      //$timeText = end($contents);
      //$text = 'ignore everything except this (text)';
      preg_match('/\[(.*?)\]/', $contentLine, $match);
      $timeText=$match[1];
      error_log("timeText: $timeText\n", 3, "errors.log");

      if ($timeText == $this->getSessionID()) {
        error_log("timeText equals session id\n", 3, "errors.log");
        return true;
      }
      else {
        error_log("timeText does NOT equal session id\n", 3, "errors.log");
        return false;
      }
    }
  }

  function isThereALoginCookie() : bool {
    $isTheCookieSet = isset($_COOKIE[self::$COOKIE_NAME]);
    return $isTheCookieSet;
  }

  function terminateLoginSession() {
    unset($_SESSION[self::$LOGIN_SESSION_ID]);
    unset($_SESSION[self::$COOKIE_NAME]);
    unset($_SESSION[self::$COOKIE_PASSWORD]);
  }

  function isCookieNameOK() : bool {
    error_log("Inside isCookieNameOK()\n", 3, "errors.log");
    if (isset($_COOKIE[self::$COOKIE_NAME])) {
      error_log("Cookie name is set\n", 3, "errors.log");
      //return ($_COOKIE[self::$COOKIE_NAME] == $this->correctUserName);
      if (($_COOKIE[self::$COOKIE_NAME] == $this->correctUserName)) {
        error_log("Cookie name equals correct user name\n", 3, "errors.log");
        return true;
      }
      else {
        error_log("Cookie name does not equal correct user name\n", 3, "errors.log");
        return false;
      }
    }
    return false;
  }

  function isCookiePasswordOK() : bool {
    error_log("Inside isCookiePasswordOK()\n", 3, "errors.log");
    if (isset($_COOKIE[self::$COOKIE_PASSWORD])) {
      error_log("Cookie password is set\n", 3, "errors.log");
      //return ($_COOKIE[self::$COOKIE_PASSWORD] == $this->encrypt($this->correctPassword));
      $storedCookiePassword = $_COOKIE[self::$COOKIE_PASSWORD];
      $encryptedPassword = $this->encrypt($this->correctPassword);
      error_log("Cookie password: $storedCookiePassword\n", 3, "errors.log");
      error_log("Encrypted password: $encryptedPassword\n", 3, "errors.log");
      if ($_COOKIE[self::$COOKIE_PASSWORD] == $this->encrypt($this->correctPassword)) {
        error_log("Cookie password equals correct password\n", 3, "errors.log");
        return true;
      }
      else {
        error_log("Cookie password does NOT equal correct password\n", 3, "errors.log");
        return false;
      }
    }
    return false;
  }

  function encrypt($contentToEncrypt) : string {
    return md5($contentToEncrypt);
  }

  function isCookieContentOK() : bool{
    error_log("Inside isCookieContentOK()\n", 3, "errors.log");
    if ($this->isCookieNameOK() && $this->isCookiePasswordOK()) {
      error_log("Cookie name and password are ok\n", 3, "errors.log");
      //Only check cookie time if there is an active session id
      if ($this->isLoggedInWithSession()) {
        if ($this->isCookieTimeOK()) {
          error_log("Cookie time is ok\n", 3, "errors.log");
          return true;
        }
        else {
          return false;
        }
      }
      return true;
    }
    else {
      return false;
    }
  }

  function getSessionID() : string{
    return session_id();
  }
}
