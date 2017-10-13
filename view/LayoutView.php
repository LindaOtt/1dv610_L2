<?php

namespace view;
class LayoutView {

  public function render(\model\LoginModel $loginModel, \view\LoginView $v, \view\DateTimeView $dtv, \model\RegisterModel $registerModel, \view\RegisterView $registerView) {
    echo '<!DOCTYPE html>
      <html>
        <head>
          <meta charset="utf-8">
          <title>Login Example</title>
        </head>
        <body>

          <h1>Assignment 2</h1>
          ' . $this->renderRegisterUser($loginModel->getIsLoggedIn(), $loginModel->getFailedLoginAttempt(), $registerView->wantsToRegisterUser()) .
              $this->renderIsLoggedIn($loginModel->getIsLoggedIn()) . '

          <div class="container">
              ' . $v->response($loginModel,$registerModel,$registerView) . '

              ' . $dtv->showDateAndTime() . '
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
