<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
  protected $fillable = [
    'name',
    'skill_level',
    'user_id'
  ];

  public function user()
  {
    return $this->belongsTo(User::class, 'user_id');
  }
}