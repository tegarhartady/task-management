<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brief extends Model
{
  use HasFactory;

  protected $fillable = [
    'title',
    'brand',
    'type',
    'status',
    'hook',
    'concept',
    'visual_direction',
    'voiceover',
    'created_by',
    'assigned_to',
    'comments',
    'is_ai',
    'due_date',
  ];

  protected $casts = [
    'is_ai' => 'boolean',
    'due_date' => 'date',
  ];

  // Relationships
  public function creator()
  {
    return $this->belongsTo(User::class, 'created_by');
  }

  public function assignedTo()
  {
    return $this->belongsTo(User::class, 'assigned_to');
  }

  public function assignees()
  {
    return $this->belongsToMany(User::class, 'brief_user');
  }

  public function attachments()
  {
    return $this->hasMany(BriefAttachment::class);
  }

  public function briefComments()
  {
    return $this->hasMany(BriefComment::class)->latest();
  }
}
