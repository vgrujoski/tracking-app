<?php

namespace App\Helpers;

use App\Models\User;

class CheckActivationCode
{
  public static function checkCode($code)
  {
    $activationCode = User::where('activ_code', '=', $code)->first();

    if($activationCode['activ_code'] != $code)
    {
      return false;
    }

    return true;
  }
}
