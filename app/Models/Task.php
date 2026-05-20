<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
  use HasFactory;

  protected $fillable = [
    'brief_id',
    'title',
    'description',
    'priority',
    'status',
    'created_by',
    'assigned_to',
    'reviewed_by',
    'due_date',
    'checked_in_at',
    'checked_out_at',
    'progress',
  ];

  protected $casts = [
    'due_date' => 'date',
    'checked_in_at' => 'datetime',
    'checked_out_at' => 'datetime',
  ];

  // Relationships
  public function brief()
  {
    return $this->belongsTo(Brief::class, 'brief_id');
  }

  public function creator()
  {
    return $this->belongsTo(User::class, 'created_by');
  }

  public function assignedTo()
  {
    return $this->belongsTo(User::class, 'assigned_to');
  }

  public function reviewedBy()
  {
    return $this->belongsTo(User::class, 'reviewed_by');
  }

  public function attachments()
  {
    return $this->hasMany(TaskAttachment::class);
  }

  public function comments()
  {
    return $this->hasMany(TaskComment::class)->orderBy('created_at', 'desc');
  }

  // Methods
  public function isCheckedIn()
  {
    return $this->checked_in_at && !$this->checked_out_at;
  }

  public function canCheckIn()
  {
    // Can check in if: not checked in yet AND either status is 'In Progress' or 'Not Started'
    return !$this->isCheckedIn() && in_array($this->status, ['Not Started', 'In Progress']);
  }

  public function canCheckOut()
  {
    return $this->isCheckedIn();
  }

  public function isOverdue()
  {
    return $this->due_date && $this->due_date->isPast() && !in_array($this->status, ['Completed', 'Approved']);
  }
}
