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

  private $sessionUserName;
  private $sessionPassword;
  private $isLoggedInWithSession = false;
  private $userNameMissing = false;
  private $passwordMissing = false;
  private $hasJustTriedToLogIn = false;
  private $wrongUserNameOrPassword = false;

  private static $LOGIN_SESSION_ID = "model::User::userLogin";
  private static $LOGIN_NAME = "model::User::sessionUserName";
  private static $LOGIN_PASSWORD = "model::User::sessionPassword";


  function __construct($formLoginName, $formPassword, $hasJustTriedToLogin) {
    assert(session_status() != PHP_SESSION_NONE);

    //Setting whether the login form has just been submitted
    $this->hasJustTriedToLogin = $hasJustTriedToLogin;

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
    $this->isLoggedInWithForm = $this->isLoggedInWithForm();
    $this->userNameMissing = $this->isUserNameMissing();
    $this->passwordMissing = $this->isPasswordMissing();
    $this->wrongUserNameOrPassword = $this->WrongUserNameOrPassword();
  }

  function isUserNameMissing() {
    return $this->submitUsername == '';
  }

  function isPasswordMissing() {
    return $this->submitPassword == '';
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
    return ($this->submitPassword == $this->correctPassword) == true;
  }

  /**
  * Check that the submitted username matches the username in the settings file
  */
  function userNameIsCorrect() {
    return ($this->submitUsername == $this->correctUserName) == true;
  }

  function isLoggedInWithSession() {
    return isset ($_SESSION[self::$LOGIN_SESSION_ID]) == true;
  }

  function isLoggedInWithForm() {
    return ($this->loginIsCorrect()) {
        $this->createLoginSession();
        return true;
      }
    return false;
  }

  function WrongUserNameOrPassword() : bool {
    return ($this->passwordIsCorrect() && $this->userNameIsCorrect()) == true;
  }

  function createLoginSession() {
    $_SESSION[self::$LOGIN_SESSION_ID] = true;
  }


}
