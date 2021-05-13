<?php

namespace App\Controllers\Auth;

use \Firebase\JWT\JWT;

class GenerateTokenController
{
  public static function generateToken($email)
  {
    $now = time();
    $expire = strtotime("+1 hour", $now);
    $secret = $_ENV['GENERATOR_TOKEN_SECRRET_KEY'];

    $payload = [
      "jti" => $email,
      "iat" => $now,
      "exp" => $expire
    ];

    return JWT::encode($payload, $secret, "HS256");
  }
}
