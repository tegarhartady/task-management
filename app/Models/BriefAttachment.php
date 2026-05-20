<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BriefAttachment extends Model
{
  use HasFactory;

  protected $fillable = ['brief_id', 'file_path', 'original_name', 'file_type'];

  public function brief()
  {
    return $this->belongsTo(Brief::class);
  }
}
