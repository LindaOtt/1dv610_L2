<?php

namespace model;

class User {
  private $username;
  private $password;

  function __construct($formLoginName, $formPassword) {
    $this->username = $formLoginName;
    $this->password = $formPassword;
  }

  function getUserName() {
    return $this->username;
  }

  function getPassword() {
    return $this->password;
  }
}
