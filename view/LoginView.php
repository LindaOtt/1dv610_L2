<?php
namespace view;

class LoginView {
	private static $login = 'LoginView::Login';
	private static $logout = 'LoginView::Logout';
	private static $name = 'LoginView::UserName';
	private static $password = 'LoginView::Password';
	private static $cookieName = 'LoginView::CookieName';
	private static $cookiePassword = 'LoginView::CookiePassword';
	private static $keep = 'LoginView::KeepMeLoggedIn';
	private static $messageId = 'LoginView::Message';

	private $nameValue = '';
	private $passwordValue = '';
	private $message = '';
	private $hasJustTriedToLogIn = false;
	private $hasLoggedOut = false;
	private $keepUserLoggedIn = false;

	function __construct() {
		$this->hasJustTriedToLogIn = $this->hasJustTriedToLogIn();
		$this->hasLoggedOut = $this->hasLoggedOut();
		$this->keepUserLoggedIn = $this->keepUserLoggedIn();
	}

	/**
	 * Create HTTP response
	 *
	 * Should be called after a login attempt has been determined
	 *
	 * @return  void BUT writes to standard output and cookies!
	 * TO DO: Simplify function
	 */
	public function response(\model\User $user) {
		$response = '';
		if ($user->getIsLoggedInWithSession()) {
			$response = $this->generateLogoutButtonHTML($this->message);
		}
		else if ($user->getHasJustTriedToLogin() && $user->getIsLoggedInWithForm()) {
			if ($user->getKeepUserLoggedIn()) {
				$this->message = 'Welcome and you will be remembered';
			}
			else {
				$this->message = 'Welcome';
			}
			$response = $this->generateLogoutButtonHTML($this->message);
		}
		else if ($user->getHasJustTriedToLogin()) {
			if ($user->getUserNameMissing()) {
				$this->message = 'Username is missing';
			}
			else if ($user->getPasswordMissing()) {
				$this->message = 'Password is missing';
			}
			else {
				$this->message = 'Wrong name or password';
			}
			$response = $this->generateLoginFormHTML($this->message);
		}
		else if ($user->getHasLoggedOut()) {
			if ($user->getHasLoggedOutWithoutSession() == true) {
				$this->message = '';
			}
			else {
				$this->message = 'Bye bye!';
			}
			$response = $this->generateLoginFormHTML($this->message);
		}
		else {
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

	public function createUser() : \model\User {
		//RETURN REQUEST VARIABLE: USERNAME
		if (isset($_REQUEST[self::$name])) {
			$this->nameValue = $_REQUEST[self::$name];
		}

		if (isset($_REQUEST[self::$password])) {
			$this->passwordValue = $_REQUEST[self::$password];
		}

		//TO DO: Check that $retName and $retPassword are in the correct format
		return new \model\User($this->nameValue, $this->passwordValue, $this->hasJustTriedToLogIn, $this->hasLoggedOut, $this->keepUserLoggedIn);
	}
}
