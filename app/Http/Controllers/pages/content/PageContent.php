<?php

namespace App\Http\Controllers\pages\content;

use App\Http\Controllers\Controller;
use App\Models\Brief;
use App\Models\BriefAttachment;
use App\Models\BriefComment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PageContent extends Controller
{
  public function index(Request $request)
  {
    $query = Brief::with(['attachments', 'assignees']);

    // Filter by status
    if ($request->has('status') && $request->status != 'All') {
      $query->where('status', $request->status);
    }

    // Filter by brand
    if ($request->has('brand') && $request->brand != 'All') {
      $query->where('brand', $request->brand);
    }

    // Filter by type
    if ($request->has('type') && $request->type != 'All') {
      $query->where('type', $request->type);
    }

    // Search
    if ($request->has('search') && $request->search) {
      $query
        ->where('title', 'like', '%' . $request->search . '%')
        ->orWhere('concept', 'like', '%' . $request->search . '%');
    }

    $user = auth()->user();
    if ($user->role === 'karyawan') {
      $query->whereHas('assignees', function ($q) use ($user) {
        $q->where('user_id', $user->id);
      });
    }

    $briefs = $query->latest()->paginate(5);

    // Get stats
    $allBriefsQuery = Brief::query();
    if ($user->role === 'karyawan') {
      $allBriefsQuery->whereHas('assignees', function ($q) use ($user) {
        $q->where('user_id', $user->id);
      });
    }
    $allBriefs = $allBriefsQuery->get();

    $stats = [
      'total' => $allBriefs->count(),
      'draft' => $allBriefs->where('status', 'DRAFT')->count(),
      'taken' => $allBriefs->where('status', 'TAKEN')->count(),
      'submitted' => $allBriefs->where('status', 'SUBMITTED')->count(),
      'revision' => $allBriefs->where('status', 'REVISION')->count(),
      'approved' => $allBriefs->where('status', 'APPROVED')->count(),
    ];

    return view('content.pages.pages-content', [
      'briefs' => $briefs,
      'stats' => $stats,
      'filters' => [
        'status' => $request->status,
        'brand' => $request->brand,
        'type' => $request->type,
        'search' => $request->search,
      ],
    ]);
  }

  public function show($id)
  {
    $brief = Brief::with(['attachments', 'briefComments.user', 'assignees'])->findOrFail($id);
    return view('content.pages.pages-content-detail', ['brief' => $brief]);
  }

  public function create()
  {
    $karyawan = User::where('role', 'karyawan')->where('is_active', true)->get();
    return view('content.pages.pages-content-create', compact('karyawan'));
  }

  public function store(Request $request)
  {
    $validated = $request->validate([
      'title' => 'required|string|max:255',
      'brand' => 'required|string',
      'type' => 'required|string',
      'hook' => 'nullable|string',
      'concept' => 'nullable|string',
      'visual_direction' => 'nullable|string',
      'voiceover' => 'nullable|string',
      'due_date' => 'nullable|date',
      'is_ai' => 'boolean',
      'files.*' => 'nullable|file|mimes:jpg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,txt|max:10240',
      'assignees' => 'nullable|array',
      'assignees.*' => 'exists:users,id',
    ]);

    $validated['created_by'] = auth()->id();
    $validated['status'] = 'DRAFT';
    $validated['is_ai'] = $request->has('is_ai') && $request->is_ai;

    $brief = Brief::create($validated);

    // Handle multiple assignments
    if ($request->has('assignees')) {
      $brief->assignees()->sync($request->assignees);
    }

    // Handle file uploads
    if ($request->hasFile('files')) {
      foreach ($request->file('files') as $file) {
        $path = $file->store('brief-attachments', 'public');

        // Determine file type
        $mimeType = $file->getMimeType();
        if (str_starts_with($mimeType, 'image/')) {
          $fileType = 'image';
        } elseif ($mimeType === 'application/pdf') {
          $fileType = 'pdf';
        } else {
          $fileType = 'document';
        }

        BriefAttachment::create([
          'brief_id' => $brief->id,
          'file_path' => $path,
          'original_name' => $file->getClientOriginalName(),
          'file_type' => $fileType,
        ]);
      }
    }

    return redirect('pages-content')->with('success', 'Brief created successfully!');
  }

  public function addComment(Request $request)
  {
    $validated = $request->validate([
      'brief_id' => 'required|integer|exists:briefs,id',
      'comment' => 'required|string|max:500',
    ]);

    $comment = BriefComment::create([
      'brief_id' => $validated['brief_id'],
      'user_id' => auth()->id(),
      'comment' => $validated['comment'],
    ]);

    // Get the comment with user info
    $comment = $comment->load('user');

    return response()->json([
      'success' => true,
      'comment' => $comment,
    ]);
  }
}
