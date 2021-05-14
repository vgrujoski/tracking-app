<?php

namespace App\Helpers;

use App\Models\Portal;

class PortalExists
{
  public static function checkPortal($url)
  {
    $portalExists = Portal::where("name", $url)->count();

    if($portalExists == false)
    {
      Portal::create([
        'name' => $url
      ]);
    }
  }
}
