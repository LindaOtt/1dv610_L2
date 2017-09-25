<?php
//To do: Let model keep session state
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

  private $userLogin;
  private $sessionUserName;
  private $sessionPassword;

  private static $LOGIN_SESSION_ID = "model::User::userLogin";
  private static $LOGIN_NAME = "model::User::sessionUserName";
  private static $LOGIN_PASSWORD = "model::User::sessionPassword";


  function __construct($formLoginName, $formPassword) {
    assert(session_status() != PHP_SESSION_NONE);

    //Setting the username and password
    $this->submitUsername = $formLoginName;
    $this->submitPassword = $formPassword;

    //Getting the correct username and password from the settings file
    $settings = parse_ini_file('./settings/settings.ini');
    $this->correctUserName = $settings['user'];
    $this->correctPassword = $settings['password'];

    $this->submitUsername = $formLoginName;
    $this->submitPassword = $formPassword;

  }

  function getSubmitUserName() {
    return $this->submitUsername;
  }

  function getSubmitPassword() {
    return $this->submitPassword;
  }

  function getCorrectUserName() {
    return $this->correctUserName;
  }

  function getCorrectPassword() {
    return $this->correctPassword;
  }

  /**
  * Check that the submitted password matches the password in the settings file
  */
  function passwordIsCorrect() {
    if ($this->submitPassword == $this->correctPassword) {
      return true;
    }
    return false;
  }

  /**
  * Check that the submitted username matches the username in the settings file
  */
  function userNameIsCorrect() {
    if ($this->submitUsername == $this->correctUserName) {
      return true;
    }
    return false;
  }

  function isLoggedInWithSession() : bool {
    if ($this->sessionHasLogIn()) {
      return true;
    }
    return false;
  }

  function loginDetailsAreCorrect() : bool {
      if ($this->loginIsCorrect()) {
        $this->createLoginSession();
        return true;
      }
    }
    return false;
  }

  function loginIsCorrect() : bool {
    if ($this->passwordIsCorrect() && $this->userNameIsCorrect()) {
      return true;
    }
    return false;
  }

  function sessionHasLogIn() {
		return isset ($_SESSION[self::$LOGIN_SESSION_ID]) == true;
  }

  function createLoginSession() {
    $_SESSION[self::$LOGIN_SESSION_ID] = true;
  }

}
