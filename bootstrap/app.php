<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


session_start();

require_once __DIR__."/../vendor/autoload.php";

$repository = Dotenv\Repository\RepositoryBuilder::createWithNoAdapters()
    ->addAdapter(Dotenv\Repository\Adapter\EnvConstAdapter::class)
    ->addWriter(Dotenv\Repository\Adapter\PutenvAdapter::class)
    ->immutable()
    ->make();

$dotenv = Dotenv\Dotenv::create($repository, __DIR__);
$dotenv->load();

$app = new \Slim\App([
  "settings" => [
    'baseUrl' => $_ENV['BASE_URL'],
    "displayErrorDetails" => true,
    "logErrors" => true,
    "logErrorDetails" => true,
    "db" => [
      "driver" => $_ENV['DB_DRIVER'],
      "host" => $_ENV['DB_HOST'].':'.$_ENV['DB_PORT'],
      "database" => $_ENV['DB_NAME'],
      "username" => $_ENV['DB_USER'],
      "password" => $_ENV['DB_PASS'],
      "charset" => "utf8",
      "collation" => "utf8_unicode_ci",
      "prrefix" => "",
    ],
    "mailer" => [
      'host' => $_ENV['MAIL_HOST'],
      'username' => $_ENV['MAIL_USERNAME'],
      'password' => $_ENV['MAIL_PASSWORD'],
      'secure' => $_ENV['MAIL_SECURE'],
    ]
  ]
]);

$container = $app->getContainer();

$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($container["settings"]["db"]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$container["db"] = function ($container) use ($capsule) {
  return $capsule;
};

$container['mailer'] = function($container) {
  return new Nette\Mail\SmtpMailer($container['settings']['mailer']);
};

require_once __DIR__."/errorHandler.php";

$container["AuthController"] = function ($container) {
  return new \App\Controllers\Auth\AuthController($container->mailer, $container->db);
};

$container["PixelController"] = function ($container) {
  return new \App\Controllers\PixelController($container->db);
};

require_once __DIR__."/../app/routes.php";

$middleware = require_once __DIR__."/middleware.php";
$middleware($app);
