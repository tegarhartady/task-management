<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReimbursementAttachment extends Model
{
  use HasFactory;

  protected $fillable = ['reimbursement_id', 'file_path', 'original_name', 'file_type'];

  public function reimbursement()
  {
    return $this->belongsTo(Reimbursement::class);
  }
}
