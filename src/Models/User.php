<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
  protected $fillable = ['name', 'email', 'password'];
  protected $hidden = ['password'];

  public function skills()
  {
    return $this->hasMany(Skill::class);
  }
}
