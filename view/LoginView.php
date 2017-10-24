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
	private $wantsToRegisterUser = false;
	private $registerView;
	private $messageState = 0;

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
	 */

	public function response($message,$generateLogout) : string {
		if ($generateLogout == true) {
			$response = $this->generateLogoutButtonHTML($message);
		}
		else {
			$response = $this->generateLoginFormHTML($message);
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
					<input type="text" id="' . self::$name . '" name="' . self::$name . '" value="'. $this->getFormNameValue() .'" />

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

	public function createLogin() : \model\LoginModel {
		return new \model\LoginModel($this->getFormNameValue(), $this->getFormPasswordValue());
	}

	public function getFormNameValue() : string {
		if (isset($_REQUEST[self::$name])) {
			return $_REQUEST[self::$name];
		}
		return '';
	}

	public function getFormPasswordValue() : string {
		if (isset($_REQUEST[self::$password])) {
			return $_REQUEST[self::$password];
		}
		return '';
	}
}
