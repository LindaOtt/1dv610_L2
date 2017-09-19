<?php
namespace controller;

class LoginController {

  public function runLoginSystem() {

    //CREATE OBJECTS OF THE VIEWS
    $v = new \view\LoginView();
    $dtv = new \view\DateTimeView();
    $lv = new \view\LayoutView();

    $lv->render(false, $v, $dtv);
  }
}
