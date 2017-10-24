<?php

namespace view;

require_once('view/DateTimeView.php');

class LayoutView {

  private $dateTimeView;

  function __construct() {
    $this->dateTimeView = new \view\DateTimeView();
  }

  public function render($isLoggedIn, $failedLoginAttempt, $wantsToRegisterUser, $response) {
    echo '<!DOCTYPE html>
      <html>
        <head>
          <meta charset="utf-8">
          <title>Login Example</title>
        </head>
        <body>

          <h1>Assignment 2</h1>
          ' . $this->renderRegisterUser($isLoggedIn, $failedLoginAttempt, $wantsToRegisterUser) .
              $this->renderIsLoggedIn($isLoggedIn) . '

          <div class="container">
              ' . $response . '

              ' . $this->dateTimeView->showDateAndTime() . '
          </div>
         </body>
      </html>
    ';
  }

  private function renderIsLoggedIn($isLoggedIn) {
    if ($isLoggedIn) {
      return '<h2>Logged in</h2>';
    }
    else {
      return '<h2>Not logged in</h2>';
    }
  }

  private function renderRegisterUser($isLoggedIn, $failedLoginAttempt, $wantsToRegisterUser) {
    if ($isLoggedIn || $failedLoginAttempt) {
      return '';
    }
    else {
      if ($wantsToRegisterUser) {
        return '<p><a href="index.php">Back to login</a></p>';
      }
      else {
        return '<p><a href="index.php?register">Register a new user</a></p>';
      }
    }
  }

}
