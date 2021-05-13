<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Portal extends Model
{
  protected $table = 'portals';

  protected $fillable = [
    'name'
  ];

  public function pixels()
  {
      return $this->hasMany(Pixel::class, 'portalId');
  }
}