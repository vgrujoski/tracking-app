<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pixel extends Model
{
  protected $table = 'pixels';

  protected $fillable = [
    'pixelType',
    'userId',
    'occuredOn',
    'portalId'
  ];

  public function user()
  {
      return $this->belongsTo(User::class);
  }

  public function portal()
  {
      return $this->belongsTo(Portal::class);
  }
}