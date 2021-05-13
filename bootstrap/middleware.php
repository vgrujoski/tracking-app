<?php

return function($app)
{
  $app->add(new Tuupola\Middleware\JwtAuthentication([
    "ignore" => ["/auth/signup", "/auth/signin", "/auth/confirm"],
    "secret" => $_ENV['GENERATOR_TOKEN_SECRRET_KEY'],
    "error" => function ($response, $arguments) {
      $data["status"] = "error";
      $data["message"] = $arguments["message"];
      $data["status_code"] = "401";
      return $response
          ->withHeader("Content-Type", "application/json")
          ->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
      }
  ]));

  $app->add(function($req, $res, $next) {
    $response = $next($req, $res);
    return $response->withHeader("Access-Control-Allow-Origin", "*")
    ->withHeader("Access-Control-Allow-Headers", "X-Requested-With,Content-Type,Accept,Origin,Authorization")
    ->withHeader("Access-Control-Allow-Methods", "GET,POST,PUT,PATCH,OPTIONS,DELETE")
    ->withHeader("Access-Control-Allow-Credentials", "true");
  });
};
