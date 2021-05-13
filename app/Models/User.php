<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
  protected $table = 'users';

  protected $fillable = [
    'name',
    'email',
    'password',
    'activ_code'
  ];

  public function pixels()
  {
      return $this->hasMany(Pixel::class, 'userId');
  }
}