<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
  protected $fillable = [
    'from_user_id',
    'to_user_id',
    'hours',
    'description',
    'status'
  ];

  public function fromUser()
  {
    return $this->belongsTo(User::class, 'from_user_id');
  }

  public function toUser()
  {
    return $this->belongsTo(User::class, 'to_user_id');
  }
}
