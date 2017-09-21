<?php

namespace model;

class User {
  private $submitUsername;
  private $submitPassword;
  private $correctUserName;
  private $correctPassword;

  function __construct($formLoginName, $formPassword) {
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
    if ($this->passwordIsCorrect() && $this->userNameIsCorrect()) {
      return true;
    }
    return false;
  }



}
