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

  private static $LOGIN_SESSION_ID = "model::User::userLogin";
	private $userLogin;

  function __construct($formLoginName, $formPassword) {
    //To do: Add assert to check that session is started
    //Setting the username and password from the submitted form
    $this->submitUsername = $formLoginName;
    $this->submitPassword = $formPassword;

    //Getting the correct username and password from the settings file
    $settings = parse_ini_file('./settings/settings.ini');
    $this->correctUserName = $settings['user'];
    $this->correctPassword = $settings['password'];
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

  function isLoggedIn() : bool {
    //Is the user already logged in?
    if ($this->sessionHasLogin()) {
      return true;
    }
    else {
      if ($this->passwordIsCorrect() && $this->userNameIsCorrect()) {
        return true;
      }
    }
    return false;
  }

  function sessionHasLogin() {
		return isset ($_SESSION[self::$LOGIN_SESSION_ID]) == true;
  }

}
