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

	//Variables for the values in the input fieldset
	private $nameValue = '';
	private $passwordValue = '';

	/**
	 * Create HTTP response
	 *
	 * Should be called after a login attempt has been determined
	 *
	 * @return  void BUT writes to standard output and cookies!
	 */
	public function response() {

		$message = $this->generateMessage();

		$response = $this->generateLoginFormHTML($message);
		//$response .= $this->generateLogoutButtonHTML($message);
		return $response;
	}

	/**
	* Generate message that will be shown in the login form
	*/
	private function generateMessage() : string {
		$ret = "";
		//Checking to see if the form has been submitted
		if ($this->userHasTriedToLogIn()) {

			//Create a user object with the details from the submitted form
			$user = $this->getRequestSubmitDetails();

			$userName = $user->getUserName();
			$userPassword = $user->getPassword();

			if ($userName == NULL) {
				$ret = 'Username is missing';
			}
			//TO DO: Move the submitted username to the form instead (test case 1.3)
			else {
				if ($userPassword == NULL) {
					$ret = 'Password is missing';
				}
				else {
					$ret = 'You submitted the username ' . $userName;
				}
			}
		}
		return $ret;
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

	private function userHasTriedToLogIn() : bool {
		return isset($_REQUEST[self::$name]);
	}

	private function getRequestSubmitDetails() : \model\User {
		//RETURN REQUEST VARIABLE: USERNAME
		if (isset($_REQUEST[self::$name])) {
			$this->nameValue = $_REQUEST[self::$name];
		}
		else {
			$this->nameValue = NULL;
		}

		if (isset($_REQUEST[self::$password])) {
			$this->passwordValue = $_REQUEST[self::$password];
		}
		else {
			$this->passwordValue = NULL;
		}
		//TO DO: Check that $retName and $retPassword are in the correct format
		return new \model\User($this->nameValue, $this->passwordValue);
	}

}
