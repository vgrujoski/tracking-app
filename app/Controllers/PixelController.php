<?php

namespace App\Controllers;

use App\Models\Pixel;
use App\Models\Portal;
use App\Models\User;
use App\Response\CustomResponse;

class PixelController
{
  protected $customResponse;

  public function __construct()
  {
    $this->customResponse = new CustomResponse();
  }

  public function post($request, $response)
  {
    if(isset($_SESSION['email'])) {
      // Get authenticatted user id
      $authenticatedUserId = User::where('email', '=', $_SESSION['email'])->value('id');

      // Get if user is active
      $activeUser = User::where('email', '=', $_SESSION['email'])->value('activ');
    } else {
      $responseMessage = "Invalid input, object invalid";
      return $this->customResponse->is400Response($response, $responseMessage);
    }

    if($activeUser == false)
    {
      $pixelType = "SOI";
    }
    else {
      $pixelType = "DOI";
    }

    $portalId = Portal::where('name', '=', $_SERVER['HTTP_HOST'])->value('id');

    $pixel = Pixel::create([
      "pixelType" => $pixelType,
      "userId" => $authenticatedUserId,
      "occuredOn" => date("Y-m-d"),
      "portalId" => $portalId,
    ]);

    session_destroy();

    $responseMessage = "Data saved";

    return $this->customResponse->is201Response($response, $responseMessage);
  }
}