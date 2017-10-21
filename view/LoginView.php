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
	private $wantsToRegisterUser = false;

	private $registerView;

	function __construct() {
		$this->hasJustTriedToLogIn = $this->hasJustTriedToLogIn();
		$this->hasLoggedOut = $this->hasLoggedOut();
		$this->keepUserLoggedIn = $this->keepUserLoggedIn();
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
	public function response(\model\LoginModel $loginModel, \model\RegisterModel $registerModel, \view\RegisterView $registerView) {
		$response = '';
		if ($registerView->wantsToRegisterUser()) {
			//Check if the user has submitted the form
			if ($registerView->registerFormHasBeenPosted()) {
				if ($registerModel->getIsRegisterNameOk() && $registerModel->getRegisterPasswordOk()) {
					$this->message = "Registered new user";
					$response = $this->generateLoginFormHTML($this->message);
				}
				else {
					if ($registerModel->getIsRegisterNameOk() == false) {
						$this->message = "Username has too few characters, at least 3 characters.";
					}
					if ($registerModel->getRegisterPasswordOk() == false) {
						if (strlen($this->message)>0) {
							$this->message .= " ";
						}
						$this->message .= "Password has too few characters, at least 6 characters.";
					}
					$response = $registerView->generateRegisterNewUserHTML($this->message);
				}
			}
			else {
				$response = $registerView->generateRegisterNewUserHTML($this->message);
			}

		}

		//The user is logged in with session
		else if ($loginModel->getIsLoggedInWithSession()) {
			//The user has just tried to log in and is logged in successfully
			if ($loginModel->getHasJustTriedToLogin() && $loginModel->getIsLoggedInWithForm()) {
				//The user has selected "Keep me logged in"
				if ($loginModel->getKeepUserLoggedIn()) {
					if ($loginModel->getFirstLoginWithoutSession() == true) {
						$this->message = 'Welcome and you will be remembered';
					}
					else {
						$this->message = '';
					}
				}
				else if ($loginModel->getFirstLoginWithoutSession() == true) {
					$this->message = 'Welcome';
				}
				//The user is already logged in with a session
				else if ($loginModel->getIsLoggedInWithSession()) {
					$this->message = '';
				}
				else {
					$this->message = 'Welcome';
				}
			}
			$response = $this->generateLogoutButtonHTML($this->message);
		}
		//The user is logged in with session and is not logged in with cookies
		else if ($loginModel->getIsLoggedInWithSession() && !$loginModel->getIsLoggedInWithCookies()) {
			$this->message = '';
			$response = $this->generateLogoutButtonHTML($this->message);
		}
		//The user is logged in with cookies but not logged in with session
		else if ($loginModel->getIsLoggedInWithCookies() == true && !$loginModel->getIsLoggedInWithSession()) {
				//The cookies are ok
				if ($loginModel->isCookieContentOK() == true) {
					$this->message = 'Welcome back with cookie';
					$response = $this->generateLogoutButtonHTML($this->message);
				}
				else {
					$this->message = 'Wrong information in cookies';
					$response = $this->generateLoginFormHTML($this->message);
				}
		}
		//The user has just tried to log in but is not logged in successfully
		else if ($loginModel->getHasJustTriedToLogin()) {
			//The user name is missing from the form
			if ($loginModel->getUserNameMissing()) {
				$this->message = 'Username is missing';
			}
			//The password is missing from the form
			else if ($loginModel->getPasswordMissing()) {
				$this->message = 'Password is missing';
			}
			else {
				$this->message = 'Wrong name or password';
			}
			$response = $this->generateLoginFormHTML($this->message);
		}
		//The user has just pressed the logout button
		else if ($this->hasLoggedOut()) {
			if ($loginModel->getHasLoggedOutWithoutSession() == true) {
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

	public function createLogin() : \model\LoginModel {
		if (isset($_REQUEST[self::$name])) {
			$this->nameValue = $_REQUEST[self::$name];
		}

		if (isset($_REQUEST[self::$password])) {
			$this->passwordValue = $_REQUEST[self::$password];
		}

		return new \model\LoginModel($this->nameValue, $this->passwordValue, $this->hasJustTriedToLogIn, $this->hasLoggedOut, $this->keepUserLoggedIn);
	}
}
