<?php

$app->post("/pixel", "PixelController:post")->setName("pixel-create");
$app->post("/auth/signup", "AuthController:postSignUp")->setName("post-signup");
$app->post("/auth/signin", "AuthController:postSignIn")->setName("post-signin");
$app->post("/auth/confirm", "AuthController:confirmEmail")->setName("confirm-email");

?>