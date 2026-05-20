<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Brief;
use Illuminate\Http\Request;

class HomePage extends Controller
{
  public function index()
  {
    $user = auth()->user();
    $query = Brief::with('creator', 'assignedTo', 'attachments');

    // Filter by assigned user if role is karyawan
    if ($user && $user->role === 'karyawan') {
      $query->whereHas('assignees', function ($q) use ($user) {
        $q->where('user_id', $user->id);
      });
    }

    $allBriefs = $query->latest()->get();

    // Group briefs by status
    $briefsData = [
      'all' => [
        'total' => $allBriefs->count(),
        'items' => $this->formatBriefs($allBriefs),
      ],
      'drafts' => [
        'total' => $allBriefs->where('status', 'DRAFT')->count(),
        'items' => $this->formatBriefs($allBriefs->where('status', 'DRAFT')),
      ],
      'draft_revision' => [
        'total' => $allBriefs->where('status', 'REVISION')->count(),
        'items' => $this->formatBriefs($allBriefs->where('status', 'REVISION')),
      ],
      'new' => [
        'total' => $allBriefs->where('status', 'TAKEN')->count(),
        'items' => $this->formatBriefs($allBriefs->where('status', 'TAKEN')),
      ],
      'assigned' => [
        'total' => $allBriefs->whereNotNull('assigned_to')->count(),
        'items' => $this->formatBriefs($allBriefs->whereNotNull('assigned_to')),
      ],
      'submitted' => [
        'total' => $allBriefs->where('status', 'SUBMITTED')->count(),
        'items' => $this->formatBriefs($allBriefs->where('status', 'SUBMITTED')),
      ],
      'revision' => [
        'total' => $allBriefs->where('status', 'REVISION')->count(),
        'items' => $this->formatBriefs($allBriefs->where('status', 'REVISION')),
      ],
      'approved' => [
        'total' => $allBriefs->where('status', 'APPROVED')->count(),
        'items' => $this->formatBriefs($allBriefs->where('status', 'APPROVED')),
      ],
    ];

    return view('content.pages.pages-home', compact('briefsData'));
  }

  private function formatBriefs($briefs)
  {
    return $briefs
      ->map(function ($brief) {
        $statusColors = [
          'DRAFT' => 'warning',
          'TAKEN' => 'info',
          'SUBMITTED' => 'secondary',
          'REVISION' => 'danger',
          'APPROVED' => 'success',
        ];

        $brandColors = [
          'Dekornata' => 'primary',
          'Supernata' => 'success',
          'Craftnata' => 'warning',
        ];

        $status = $brief->status ?? 'DRAFT';
        $brand = $brief->brand ?? 'Unknown';

        return [
          'id' => $brief->id,
          'title' => $brief->title,
          'author' => $brief->creator?->name ?? 'Unknown',
          'date' => $brief->created_at->format('M d, Y'),
          'brand' => $brand,
          'brand_color' => $brandColors[$brand] ?? 'secondary',
          'type' => $brief->type ?? null,
          'status' => $status,
          'status_color' => $statusColors[$status] ?? 'secondary',
          'is_ai' => $brief->is_ai ?? false,
          'comments' => $brief->briefComments?->count() ?? 0,
          'border_color' => '#' . substr(md5($brief->id), 0, 6),
        ];
      })
      ->toArray();
  }
}
