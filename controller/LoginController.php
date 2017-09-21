<?php
namespace controller;

class LoginController {

  public function runLoginSystem() {

    //Create main layout view and datetime view
    $lv = new \view\LayoutView();
    $dtv = new \view\DateTimeView();

    //Create the loginview
    $v = new \view\LoginView();

    //Get the user object from the login view
    $user = $v->createUser();

    //Let the user object check if it is logged in
    if ($user->isLoggedIn()) {
      $lv->render(true, $v, $dtv);
    }
    else {
      $lv->render(false, $v, $dtv);
    }


    /*
    //Checking to see if user has logged in
    if ($v->userHasTriedToLogIn()) {
      //Create a user object with login username and password, and correct username and password
      $user = $v->createUser();
      //Send the user into the login view
      $v->setUser($user);

      //Let the user object check if it is logged in
      if ($user->isLoggedIn()) {
        $lv->render(true, $v, $dtv);
      }
      else {
        $lv->render(false, $v, $dtv);
      }
    }
    */

  }
}
