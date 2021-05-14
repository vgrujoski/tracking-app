<?php

namespace App\Helpers;

use App\Models\User;

class VerifyAccount
{
  public static function verifyAccount($email, $password)
  {
    $providedPassword = "";

    $count = User::where(["email" => $email])->count();

    if($count == false)
    {
      return false;
    }

    $getUser = User::where("email", $email)->get();

    foreach($getUser as $userInfo)
    {
      $providedPassword = $userInfo->password;
      $active = $userInfo->activ;
    }

    if ($active == 0){
      return false;
    }

    $verufyPassword = password_verify($password, $providedPassword);

    if($verufyPassword == false)
    {
      return false;
    }

    return true;
  }
}
