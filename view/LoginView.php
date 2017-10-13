<?php
namespace view;

require_once('RegisterView.php');

class LoginView {
	private static $login = 'LoginView::Login';
	private static $logout = 'LoginView::Logout';
	private static $name = 'LoginView::UserName';
	private static $password = 'LoginView::Password';
	private static $cookieName = 'LoginView::CookieName';
	private static $cookiePassword = 'LoginView::CookiePassword';
	private static $keep = 'LoginView::KeepMeLoggedIn';
	private static $messageId = 'LoginView::Message';
	/*
	private static $nameregister = 'RegisterView::UserName';
	private static $nameid = 'RegisterView::Message';
	private static $passwordregister = 'RegisterView::Password';
	private static $passwordrepeat = 'RegisterView::PasswordRepeat';
	private static $registerbutton = 'RegisterView::RegisterButton';
	*/
	private $nameValue = '';
	private $passwordValue = '';
	private $message = '';
	private $hasJustTriedToLogIn = false;
	private $hasLoggedOut = false;
	private $keepUserLoggedIn = false;
	private $wantsToRegisterUser = false;

	private $registerView;

	function __construct() {
		$this->hasJustTriedToLogIn = $this->hasJustTriedToLogIn();
		$this->hasLoggedOut = $this->hasLoggedOut();
		$this->keepUserLoggedIn = $this->keepUserLoggedIn();
		$this->wantsToRegisterUser = $this->wantsToRegisterUser();
		$this->registerView = new \view\RegisterView();
	}

	/**
	 * Create HTTP response
	 *
	 * Should be called after a login attempt has been determined
	 *
	 * @return  void BUT writes to standard output and cookies!
	 * TO DO: Simplify function
	 */
	public function response(\model\LoginModel $loginModel) {
		$response = '';
		if ($this->wantsToRegisterUser()) {
			error_log("R0: wantsToRegisterUser", 3, "errors.log");
			$response = $this->registerView->generateRegisterNewUserHTML();
		}

		//The user is logged in with session
		else if ($loginModel->getIsLoggedInWithSession()) {
			error_log("R1: getIsLoggedInWithSession\n", 3, "errors.log");
			//The user has just tried to log in and is logged in successfully
			if ($loginModel->getHasJustTriedToLogin() && $loginModel->getIsLoggedInWithForm()) {
				error_log("R2: getHasJustTriedToLogin and getIsLoggedInWithForm\n", 3, "errors.log");
				//The user has selected "Keep me logged in"
				if ($loginModel->getKeepUserLoggedIn()) {
					if ($loginModel->getFirstLoginWithoutSession() == true) {
						error_log("R3: getKeepUserLoggedIn and getFirstLoginWithoutSession\n", 3, "errors.log");
						$this->message = 'Welcome and you will be remembered';
					}
					else {
						error_log("R4: else\n", 3, "errors.log");
						$this->message = '';
					}
				}
				else if ($loginModel->getFirstLoginWithoutSession() == true) {
					error_log("R5: getFirstLoginWithoutSession\n", 3, "errors.log");
					$this->message = 'Welcome';
				}
				//The user is already logged in with a session
				else if ($loginModel->getIsLoggedInWithSession()) {
					error_log("R6: getIsLoggedInWithSession\n", 3, "errors.log");
					$this->message = '';
				}
				else {
					error_log("R7: else", 3, "errors.log");
					$this->message = 'Welcome';
				}
				//$response = $this->generateLogoutButtonHTML($this->message);
			}
			$response = $this->generateLogoutButtonHTML($this->message);
		}
		//The user is logged in with session and is not logged in with cookies
		else if ($loginModel->getIsLoggedInWithSession() && !$loginModel->getIsLoggedInWithCookies()) {
			error_log("R8: getIsLoggedInWithSession and NOT getIsLoggedInWithCookies\n", 3, "errors.log");
			$this->message = '';
			$response = $this->generateLogoutButtonHTML($this->message);
		}
		//The user is logged in with cookies but not logged in with session
		else if ($loginModel->getIsLoggedInWithCookies() == true && !$loginModel->getIsLoggedInWithSession()) {
				error_log("R12: getIsLoggedInWithCookies and NOT getIsLoggedInWithSession\n", 3, "errors.log");
				//The cookies are ok
				if ($loginModel->isCookieContentOK() == true) {
					error_log("R13: getIsCookieContentOK\n", 3, "errors.log");
					$this->message = 'Welcome back with cookie';
					$response = $this->generateLogoutButtonHTML($this->message);
				}
				else {
					error_log("R14: else\n", 3, "errors.log");
					$this->message = 'Wrong information in cookies';
					$response = $this->generateLoginFormHTML($this->message);
				}
		}
		//The user has just tried to log in but is not logged in successfully
		else if ($loginModel->getHasJustTriedToLogin()) {
			error_log("R9: getHasJustTriedToLogin\n", 3, "errors.log");
			//The user name is missing from the form
			if ($loginModel->getUserNameMissing()) {
				error_log("R10: getUserNameMissing\n", 3, "errors.log");
				$this->message = 'Username is missing';
			}
			//The password is missing from the form
			else if ($loginModel->getPasswordMissing()) {
				error_log("R11: getPasswordMissing\n", 3, "errors.log");
				$this->message = 'Password is missing';
			}
			else {
				error_log("R12: getPasswordMissing\n", 3, "errors.log");
				$this->message = 'Wrong name or password';
			}
			$response = $this->generateLoginFormHTML($this->message);
		}
		//The user has just pressed the logout button
		else if ($this->hasLoggedOut()) {
			error_log("R9: hasLoggedOut\n", 3, "errors.log");
			if ($loginModel->getHasLoggedOutWithoutSession() == true) {
				error_log("R10: getHasLoggedOutWithoutSession\n", 3, "errors.log");
				$this->message = '';
			}
			else {
				error_log("R11: else\n", 3, "errors.log");
				$this->message = 'Bye bye!';
			}
			$response = $this->generateLoginFormHTML($this->message);
		}
		else {
				error_log("R15: else\n", 3, "errors.log");
				$response = $this->generateLoginFormHTML($this->message);
		}

		return $response;
	}

	/**
	* Generate HTML code on the output buffer for the logout button
	* @param $message, String output message
	* @return  void, BUT writes to standard output!
	*/
	private function generateLogoutButtonHTML($message) {
		return '
			<form  method="post" >
				<p id="' . self::$messageId . '">' . $message .'</p>
				<input type="submit" name="' . self::$logout . '" value="logout"/>
			</form>
		';
	}

	/**
	* Generate HTML code on the output buffer for the logout button
	* @param $message, String output message
	* @return  void, BUT writes to standard output!
	*/
	private function generateLoginFormHTML($message) {
		return '
			<form method="post" >
				<fieldset>
					<legend>Login - enter Username and password</legend>
					<p id="' . self::$messageId . '">' . $message . '</p>

					<label for="' . self::$name . '">Username :</label>
					<input type="text" id="' . self::$name . '" name="' . self::$name . '" value="'. $this->nameValue .'" />

					<label for="' . self::$password . '">Password :</label>
					<input type="password" id="' . self::$password . '" name="' . self::$password . '" />

					<label for="' . self::$keep . '">Keep me logged in  :</label>
					<input type="checkbox" id="' . self::$keep . '" name="' . self::$keep . '" />

					<input type="submit" name="' . self::$login . '" value="login" />
				</fieldset>
			</form>
		';
	}

	public function hasJustTriedToLogIn() : bool {
		return (isset($_REQUEST[self::$name]) || isset($_REQUEST[self::$password])) == true;
	}

	public function hasLoggedOut() : bool {
		return (isset($_REQUEST[self::$logout])) == true;
	}

	public function keepUserLoggedIn() : bool {
		return (isset($_REQUEST[self::$keep])) == true;
	}

	public function wantsToRegisterUser() : bool {

		if (isset($_GET['register'])) {
			return true;
		}
		else {
			return false;
		}
	}

	public function createLogin() : \model\LoginModel {
		//RETURN REQUEST VARIABLE: USERNAME
		if (isset($_REQUEST[self::$name])) {
			$this->nameValue = $_REQUEST[self::$name];
		}

		if (isset($_REQUEST[self::$password])) {
			$this->passwordValue = $_REQUEST[self::$password];
		}

		//TO DO: Check that $retName and $retPassword are in the correct format
		return new \model\LoginModel($this->nameValue, $this->passwordValue, $this->hasJustTriedToLogIn, $this->hasLoggedOut, $this->keepUserLoggedIn);
	}
}
