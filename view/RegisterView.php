<?php
namespace view;

class RegisterView {

	private static $nameregister = 'RegisterView::UserName';
	private static $nameid = 'RegisterView::Message';
	private static $passwordregister = 'RegisterView::Password';
	private static $passwordrepeat = 'RegisterView::PasswordRepeat';
  private static $registerbutton = 'RegisterView::RegisterButton';

  private $nameValue = '';
  private $registerValue = '';

  function __construct() {
    $this->nameValue = $this->getRegisterUserName();
    $this->passwordValue = $this->getRegisterPassword();
  }


  function generateRegisterNewUserHTML() {
		return '
			<h2>Register new user</h2>
			<form action="?register" method="post" enctype="multipart/form-data">
				<fieldset>
					<legend>Register a new user - Write username and password</legend>
					<p id="' . self::$nameid . '"></p>
					<label for="' . self::$nameregister . '">Username :</label>
					<input type="text" size="20" id="' . self::$nameregister . '" name="' . self::$nameregister . '" value="" />
					<br/>
					<label for="' . self::$passwordregister . '">Password :</label>
					<input type="password" id="' . self::$passwordregister . '" name="' . self::$passwordregister . '" />
					<br/>
					<label for="' . self::$passwordrepeat . '">Repeat password :</label>
					<input type="password" size="20" id="' . self::$passwordrepeat . '" name="' . self::$passwordrepeat . '" />
					<br/>
					<input id="submit" type="submit" name="' . self::$registerbutton . '" value="Register" />
					<br/>
				</fieldset>
			</form>
		';
	}

  function getRegisterUserName() : string {
    if (isset($_REQUEST[self::$nameregister])) {
      return $_REQUEST[self::$nameregister];
		}
    return '';
  }

  function getRegisterPassword() : string {
    if (isset($_REQUEST[self::$passwordregister])) {
      $this->registerPassword = $_REQUEST[self::$passwordregister];
			return $this->registerPassword;
		}
    return '';
  }

  function createRegister() : \model\RegisterModel {
    return new \model\RegisterModel($this->nameValue, $this->passwordValue);
  }

  public function wantsToRegisterUser() : bool {
		if (isset($_GET['register'])) {
			return true;
		}
		else {
			return false;
		}
	}
}
