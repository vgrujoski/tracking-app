<?php

namespace App\Helpers;

use App\Models\User;

class EmailExists
{
  public static function checkEmail($emailAddress)
  {
    $count = User::where("email", $emailAddress)->count();

    if($count == 0)
    {
      return false;
    } else {
      return true;
    }
  }
}
