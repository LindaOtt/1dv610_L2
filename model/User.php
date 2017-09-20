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
}
