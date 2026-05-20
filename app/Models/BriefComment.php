<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BriefComment extends Model
{
  use HasFactory;

  protected $fillable = ['brief_id', 'user_id', 'comment'];

  public function brief()
  {
    return $this->belongsTo(Brief::class);
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
