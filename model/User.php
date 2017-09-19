<?php

namespace model;

class User {
  private $userName;

  function __construct($formLoginName) {
    $this->userName = $formLoginName;
  }

  function getUserName() {
    return $this->userName;
  }
}
