<?php
namespace model;

class LoginModel {
  private $submitUsername;
  private $submitPassword;
  private $databaseUserName;
  private $databasePassword;

  private $sessionUserName;
  private $sessionPassword;
  private $isLoggedInWithSession = false;
  private $isLoggedInWithForm = false;
  private $isLoggedInWithCookies = false;
  private $isCookieContentOk = false;
  private $userNameMissing = false;
  private $passwordMissing = false;
  private $hasJustTriedToLogIn = false;
  private $hasLoggedOut = false;
  private $hasLoggedOutWithoutSession = false;

  private $isLoggedIn = false;
  private $keepUserLoggedIn = false;
  private $failedLoginAttempt = false;
  private $isLoggedInWithCookiesAndNoSession = false;

  private $firstLoginWithoutSession = false;

  private static $LOGIN_SESSION_ID = 'model::LoginModel::userLogin';
  private static $COOKIE_NAME = "LoginView::CookieName";
  private static $COOKIE_PASSWORD = "LoginView::CookiePassword";

  private static $DB_USER_NAME = 'user';
  private static $DB_PASSWORD_NAME = 'password';

  private static $DB_COOKIES = 'db/db_cookies.txt';
  private static $DB_SESSION = 'db/db_session.txt';
  private static $DB_USERS = 'db/db_users.ini';

  //Salt used to encrypt user passwords
  private static $SALT = '5FeWq21O&3/+\643Bxlll$_?%3Fz72B..PYaS';

  function __construct($formLoginName, $formPassword, $hasJustTriedToLogin, $hasLoggedOut, $keepUserLoggedIn) {
    assert(session_status() != PHP_SESSION_NONE);

    //Setting whether the login form has just been submitted
    $this->hasJustTriedToLogin = $hasJustTriedToLogin;

    //Setting whether the user has logged out
    $this->hasLoggedOut = $hasLoggedOut;

    //Setting whether the user wants to be kept logged in
    $this->keepUserLoggedIn = $keepUserLoggedIn;

    //Setting the username and password
    $this->submitUsername = $formLoginName;
    $this->submitPassword = $formPassword;

    //Getting the database username and password from the users file
    $users = parse_ini_file(self::$DB_USERS);
    $this->databaseUserName = $users[self::$DB_USER_NAME];
    $this->databasePassword = $users[self::$DB_PASSWORD_NAME];

    $this->submitUsername = $formLoginName;
    $this->submitPassword = $formPassword;

    //Check if the user is logged in
    $this->checkLoginState();
  }

  function checkLoginState() {
    $this->isLoggedInWithSession = $this->isLoggedInWithSession();
    if ($this->hasJustTriedToLogin) {
      $this->isLoggedInWithForm = $this->isLoggedInWithForm();
    }
    $this->userNameMissing = $this->isUserNameMissing();
    $this->passwordMissing = $this->isPasswordMissing();
    $this->isLoggedInWithCookies = $this->isThereALoginCookie();
    $this->isCookieContentOk = $this->isCookieContentOk();

    //Check if user is logged in with a session
    if ($this->isLoggedInWithSession()) {
      $this->checkIfLoggedOutWithSession();
    }

    //The user is not logged in with a session
    else {
      //Checks if the user is logged out without session, if so stop executing main function
      if ($this->checkIfLoggedOutWithoutSession() == true) {
        //Set that the user has logged out without session
        $this->setHasLoggedOutWithoutSession(true);
        return true;
      }

      //Check if the user is logged in with cookies, if so stop executing main function
      if ($this->getIsLoggedInWithCookies() == true) {
        $this->checkIsCookieContentOk();
        return true;
      }

      //Check if the user logged in successfully, if so stop executing main function
      if ($this->checkIfLoggedInSuccessfully() == true) {
        //Try to create a login session
        if ($this->createLoginSession()) {
          //If a login session was created, store the session data
          $this->storeSessionData();
        }
        //The user has clicked "keep me logged in" in the form
        if ($this->getKeepUserLoggedIn() == true) {
          $time = time()+180;
          $this->createLoginCookies($time, true);
          $this->storeSessionCookieData($time);
        }
        return true;
      }
    }
  }

  //Creates random string of known length
  function createRandomString() : string {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
    $string = '';

    for ($i = 0; $i < 20; $i++) {
        $string .= $characters[mt_rand(0, strlen($characters) - 1)];
    }

    return $string;
  }

  //Takes original password as parameter and creates encrypted password
  //that can be stored in cookie
  function createEncryptedPassword($originalPassword) : string{

    $encryptedPassword = md5($originalPassword . self::$SALT);

    error_log("EncryptedPassword: $encryptedPassword\n", 3, "errors.log");
    return $encryptedPassword;
  }

  function getIsLoggedIn() {
    return $this->isLoggedIn;
  }

  function getIsLoggedInWithSession() {
    return $this->isLoggedInWithSession();
  }

  function checkIfLoggedOutWithSession() {
    //The user has just clicked the "logout" button
    if ($this->hasLoggedOut == true) {
      $this->setIsLoggedInWithSession(false);
      $this->terminateLoginSession();
    }
    //The user has not logged out
    else {
      $this->isLoggedIn = true;
    }
  }

  function checkIfLoggedOutWithoutSession() {
    //The user has just clicked the "logout" button
    if ($this->hasLoggedOut == true) {
        return true;
    }
    return false;
  }

  function checkIfLoggedInSuccessfully() {
    //The user has just tried to log in with the log in form
    //The login was successful, username and password are correct
    if ($this->getHasJustTriedToLogIn() && $this->getIsLoggedInWithForm()) {
      $this->isLoggedIn = true;
      $this->firstLoginWithoutSession = true;
      $this->keepUserLogin = false;
      return true;
    }
    return false;
  }

  function getFailedLoginAttempt() {
    return $this->failedLoginAttempt;
  }

  function checkIsCookieContentOk() {
    if ($this->isCookieContentOk()) {
      $this->isLoggedIn = true;
      $this->isLoggedInWithCookiesAndNoSession = true;
      return true;
    }
    else {
      //Remove cookies
      $this->createLoginCookies(time()-1000, false);
      $this->isLoggedIn = false;
      $this->failedLoginAttempt = true;
      return false;
    }
  }

  function setIsLoggedInWithSession($isLoggedInWithSession) {
    $this->isLoggedInWithSession = false;
  }

  function getHasLoggedOutWithoutSession() {
    return $this->hasLoggedOutWithoutSession;
  }

  function setHasLoggedOutWithoutSession($hasLoggedOutWithoutSession) {
    $this->hasLoggedOutWithoutSession = $hasLoggedOutWithoutSession;
  }

  function getIsLoggedInWithForm() {
    return $this->isLoggedInWithForm;
  }

  function getIsLoggedInWithCookies() {
    return $this->isLoggedInWithCookies;
  }

  function getKeepUserLoggedIn() {
    return $this->keepUserLoggedIn;
  }

  function getUserNameMissing() {
    return $this->userNameMissing;
  }

  function getPasswordMissing() {
    return $this->passwordMissing;
  }

  function getHasJustTriedToLogIn() {
    return $this->hasJustTriedToLogin;
  }

  function getFirstLoginWithoutSession() {
    return $this->firstLoginWithoutSession;
  }

  function isUserNameMissing() {
    return $this->submitUsername == '';
  }

  function isPasswordMissing() {
    return $this->submitPassword == '';
  }

  function getIsCookieContentOk() {
    return $this->isCookieContentOk;
  }

  /**
  * Check that the submitted username matches the username in the settings file
  */
  function userNameIsCorrect() {
    return ($this->submitUsername == $this->databaseUserName) == true;
  }

  /**
  * Check that the submitted password matches the password in the database
  */
  function passwordIsCorrect() {
    //Encrypt the submitted password
    $encryptedSubmitPassword = $this->createEncryptedPassword($this->submitPassword);

    //Remove the random string from the password in the database
    $databasePasswordWithoutRandom = substr($this->databasePassword, 0, -20);
    return ($encryptedSubmitPassword == $databasePasswordWithoutRandom) == true;
  }

  function loginIsCorrect() {
    return ($this->userNameIsCorrect() && $this->passwordIsCorrect()) == true;
  }

  function isLoggedInWithSession() {
    if (isset ($_SESSION[self::$LOGIN_SESSION_ID])) {
      //Check if browser session, ip and user agent corresponds with database
      if ($this->isSessionOk($_SESSION[self::$LOGIN_SESSION_ID])) {
          return true;
      }
    }
    return false;
  }

  //Check if session variable exists in database
  //and if it's user agent and ip address corresponds with the current user agent and ip address
  function isSessionOk($sessionId) {
    // Open the file to get existing content
    $content = file_get_contents(self::$DB_SESSION);
    //Find the line with the current session
    $posOfCurrentSession = strpos($content, $this->getSessionID());
    //Check if the current session exists in the text file
    if ($posOfCurrentSession === false) {
      return false;
    }
    else {
      //If it does, check if the time in the text file matches the time of the cookie
      $contentLine = $this->getLineWithString(self::$DB_SESSION, $this->getSessionID());
      //Dividing the content line by divider ",,," into an array $sessionData
      //Where sessionData[0]=session id, sessionData[1]=user agent, sessionData[2]=ip address
      $sessionData = explode(",,,", $contentLine);

      //Getting the session id of the current browser
      $currentSessionId = $this->getSessionID();

      //Getting the user agent of the current browser
      $currentUserAgent = $this->getUserAgent();

      //Getting the ip address of the current browser
      $currentIpAddress = $this->getIpAddress();

      //Removing line break from ip address
      $sessionData[2] = str_replace(PHP_EOL, '', $sessionData[2]);

      if ($sessionData[0] == $currentSessionId) {
        if ($sessionData[1] == $currentUserAgent) {
          if ($sessionData[2] == $currentIpAddress) {
              return true;
          }
        }
      }
      return false;
    }
  }

  function isLoggedInWithForm() {
    return ($this->loginIsCorrect()) == true;
  }

  function createLoginSession() : bool {
      $_SESSION[self::$LOGIN_SESSION_ID] = true;
      //Check that the login session was created
      if(isset(self::$LOGIN_SESSION_ID)) {
        return true;
      }
      return false;
  }

  //Create login cookie and store it in database file
  function createLoginCookies($time) {
    setcookie(self::$COOKIE_NAME, $this->databaseUserName, $time);
    //Create random string
    $randomString = $this->createRandomString();
    //Create encrypted password with random string
    $encryptedPassword = $this->createEncryptedPassword($this->databasePassword).$randomString;
    setcookie(self::$COOKIE_PASSWORD, $encryptedPassword, $time);
  }

  //Stores session id and cookie time
  function storeSessionCookieData($time) {
    // Open the cookie data file to get existing content
    $content = file_get_contents(self::$DB_COOKIES);

    //Append session id to file
    $content .= "\n[".$this->getSessionID()."] ";

    // Append the time to the file
    $content .= $time;

    // Write the contents back to the file
    file_put_contents(self::$DB_COOKIES, $content);
  }


  function storeSessionData() {

    // Open the session data file to get existing content
    $content = file_get_contents(self::$DB_SESSION);

    //Append session id to file
    $content .= $this->getSessionID();

    //Get the http user agent
    $userAgent = $this->getUserAgent();

    //Append the user agent to the file
    $content .= ",,," . $userAgent;

    //Get the ip address
    $ipAddress = $this->getIpAddress();

    //Append the ip address to the file, and a line break that adapts to the OS
    $content .= ",,," . $ipAddress . PHP_EOL;

    // Write the contents back to the file
    file_put_contents(self::$DB_SESSION, $content);
  }

  //Get the line in the file $fileName that contains the string $str
  //and return it
  function getLineWithString($fileName, $str) {
    $lines = file($fileName);
    foreach ($lines as $lineNumber => $line) {
        if (strpos($line, $str) !== false) {
            return $line;
        }
    }
    return -1;
  }

  function getUserAgent() : string{
    error_log("In getUserAgent()\n", 3, "errors.log");
    return $_SERVER['HTTP_USER_AGENT'];
  }

  function getIpAddress() : string{
    error_log("In getIpAddress()\n", 3, "errors.log");
    return $_SERVER['REMOTE_ADDR'];
  }

  function isCookieTimeOk() : bool {
    // Open the file to get existing content
    $content = file_get_contents(self::$DB_COOKIES);
    //Find the line with the current session
    $posOfCurrentSession = strpos($content, $this->getSessionID());
    //Check if the current session exists in the text file
    if ($posOfCurrentSession === false) {
      return false;
    }
    else {
      //If it does, check if the time in the text file matches the time of the cookie
      $contentLine = $this->getLineWithString(self::$DB_COOKIES, $this->getSessionID());
      //Using reg exp to get the content within the brackets
      preg_match('/\[(.*?)\]/', $contentLine, $match);
      $timeText=$match[1];
      if ($timeText == $this->getSessionID()) {
        return true;
      }
      else {
        return false;
      }
    }
  }

  function isThereALoginCookie() : bool {
    $isTheCookieSet = isset($_COOKIE[self::$COOKIE_NAME]);
    return $isTheCookieSet;
  }

  function terminateLoginSession() {
    unset($_SESSION[self::$LOGIN_SESSION_ID]);
    unset($_SESSION[self::$COOKIE_NAME]);
    unset($_SESSION[self::$COOKIE_PASSWORD]);
  }

  function isCookieNameOk() : bool {
    if (isset($_COOKIE[self::$COOKIE_NAME])) {
      if (($_COOKIE[self::$COOKIE_NAME] == $this->databaseUserName)) {
        return true;
      }
      else {
        return false;
      }
    }
    return false;
  }

  function isCookiePasswordOk() : bool {
    if (isset($_COOKIE[self::$COOKIE_PASSWORD])) {
      $storedCookiePassword = $_COOKIE[self::$COOKIE_PASSWORD];

      //Removing the random string from the cookie password
      $storedCookiePassword = substr($storedCookiePassword, 0, -20);

      //Encrypt database password
      $encryptedDatabasePassword = $this->createEncryptedPassword($this->databasePassword);

      if ($storedCookiePassword == $encryptedDatabasePassword) {
        return true;
      }
      else {
        return false;
      }
    }
    return false;
  }

  function encrypt($contentToEncrypt) : string {
    //Creating a salted password
    return md5($contentToEncrypt.$self::SALT);
  }

  function isCookieContentOk() : bool{
    if ($this->isCookieNameOk() && $this->isCookiePasswordOk()) {
      //Only check cookie time if there is an active session id
      if ($this->isLoggedInWithSession()) {
        if ($this->isCookieTimeOk()) {
          return true;
        }
        else {
          return false;
        }
      }
      return true;
    }
    else {
      return false;
    }
  }

  function getSessionID() : string{
    return session_id();
  }
}
