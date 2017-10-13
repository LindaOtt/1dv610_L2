<?php
namespace model;
class RegisterModel {

  private static $DESIRED_LENGTH_USERNAME = 3;
  private static $DESIRED_LENGTH_PASSWORD = 6;

  private $registerUserNameOk = false;
  private $registerPasswordOk = false;

  function __construct($registerUserName, $registerPassword) {
      //Check that username is a string of the right length
      if ($this->checkIfIsStringAndIsRightLength($registerUserName, self::$DESIRED_LENGTH_USERNAME)) {
        $this->registerUserNameOk = true;
      }

      //Check that password is a string of the right length
      if ($this->checkIfIsStringAndIsRightLength($registerPassword, self::$DESIRED_LENGTH_PASSWORD)) {
        $this->registePasswordOk = true;
      }
  }


  private function checkIfIsStringAndIsRightLength($variableToCheck, $desiredLength) : bool {
    if ($this->checkIfString($variableToCheck) && $this->checkisRightLength($variableToCheck, $desiredLength)) {
      return true;
    }
    return false;
  }

  private function checkIfString($variableToCheck) : bool {
    if (is_string($variableToCheck)) {
      return true;
    }
    return false;
  }

  private function checkisRightLength($variableToCheck, $desiredLength) : bool {
    //Check that the variable is of the desired length
    if (strlen($variableToCheck)>= $desiredLength) {
      return true;
    }
    return false;
  }

  function getIsRegisterNameOk() {
    if ($this->registerUserNameOk == true) {
      return true;
    }
    return false;
  }

  function getIsPasswordOk() {
    if ($this->registerPasswordOk == true) {
      return true;
    }
    return false;
  }
}
