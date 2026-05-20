<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskAttachment extends Model
{
  use HasFactory;

  protected $fillable = ['task_id', 'type', 'file_path', 'link', 'original_name', 'uploaded_by'];

  public function task()
  {
    return $this->belongsTo(Task::class);
  }

  public function uploadedBy()
  {
    return $this->belongsTo(User::class, 'uploaded_by');
  }
}
