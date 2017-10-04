<?php

namespace view;

class LayoutView {

  public function render($isLoggedIn, $wantsToRegisterUser, \model\LoginModel $loginModel, \view\LoginView $v, \view\DateTimeView $dtv) {
    echo '<!DOCTYPE html>
      <html>
        <head>
          <meta charset="utf-8">
          <title>Login Example</title>
        </head>
        <body>

          <h1>Assignment 2</h1>
          ' . $this->renderRegisterUser($isLoggedIn, $wantsToRegisterUser) .
              $this->renderIsLoggedIn($isLoggedIn) . '

          <div class="container">
              ' . $v->response($loginModel) . '

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

  private function renderRegisterUser($isLoggedIn, $wantsToRegisterUser) {
    if ($isLoggedIn) {
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
