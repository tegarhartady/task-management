<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reimbursement extends Model
{
  use HasFactory;

  protected $fillable = [
    'title',
    'description',
    'category',
    'amount',
    'date',
    'status',
    'submitted_by',
    'approved_by',
    'rejection_reason',
    'approved_at',
  ];

  protected $casts = [
    'date' => 'date',
    'approved_at' => 'datetime',
    'amount' => 'decimal:2',
  ];

  // Relationships
  public function submittedBy()
  {
    return $this->belongsTo(User::class, 'submitted_by');
  }

  public function approvedBy()
  {
    return $this->belongsTo(User::class, 'approved_by');
  }

  public function attachments()
  {
    return $this->hasMany(ReimbursementAttachment::class);
  }
}
