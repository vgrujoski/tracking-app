<?php

$container["notFoundHandler"] = function ($container)
{
  return function ($request, $response, $exception) use ($container)
  {
      return $response->withStatus(404)
          ->withHeader("Content-Type", "application/json")
          ->write(json_encode(
            array(
              "success"=>false,
              "error"=>"NOT_FOUND",
              "message"=>"Endpoint was not found",
              "STATUS_CODE"=>"404"
            ),
            JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
          ));
  };
};

$container["errorHandler"] = function ($container)
{
  return function ($request, $response, $exception) use ($container)
  {
      return $response->withStatus(500)
          ->withHeader("Content-Type", "application/json")
          ->write(json_encode(
            array(
              "success"=>false,
              "error"=>"INTERNAL_ERROR",
              "message"=>"Something went wrong internally",
              "STATUS_CODE"=>"500",
              "trace"=>$exception->getTraceAsString()
            ),
            JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
          ));
  };
};

$container["phpErrorHandler"] = function ($container)
{
  return $container["errorHandler"];
};
