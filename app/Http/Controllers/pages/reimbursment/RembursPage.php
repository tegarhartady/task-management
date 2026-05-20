<?php

namespace App\Http\Controllers\pages\reimbursment;

use App\Http\Controllers\Controller;
use App\Models\Reimbursement;
use App\Models\ReimbursementAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RembursPage extends Controller
{
  public function index()
  {
    $query = Reimbursement::with('submittedBy', 'approvedBy', 'attachments');

    // Filter by status
    if (request('status') && request('status') !== 'All') {
      $query->where('status', request('status'));
    }

    // Filter by category
    if (request('category') && request('category') !== 'All') {
      $query->where('category', request('category'));
    }

    // Search by title or description
    if (request('search')) {
      $search = request('search');
      $query->where(function ($q) use ($search) {
        $q->where('title', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%");
      });
    }

    $reimbursements = $query->latest()->paginate(5);

    // Get unique categories untuk filter dropdown
    $categories = Reimbursement::distinct('category')->pluck('category');

    return view('content.pages.pages-reimburs', compact('reimbursements', 'categories'));
  }

  public function create()
  {
    return view('content.pages.pages-reimburs-create');
  }

  public function store(Request $request)
  {
    $validated = $request->validate([
      'title' => 'required|string|max:255',
      'description' => 'nullable|string',
      'category' => 'required|string',
      'amount' => 'required|numeric|min:0',
      'date' => 'required|date',
      'attachments.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,gif|max:5120', // 5MB per file
    ]);

    $validated['submitted_by'] = auth()->id();
    $validated['status'] = 'Pending';

    $reimbursement = Reimbursement::create($validated);

    // Handle file uploads
    if ($request->hasFile('attachments')) {
      foreach ($request->file('attachments') as $file) {
        $path = $file->store('reimbursement-attachments', 'public');
        $fileType = $file->getClientMimeType();

        if (str_contains($fileType, 'pdf')) {
          $type = 'pdf';
        } elseif (str_contains($fileType, 'image')) {
          $type = 'image';
        } else {
          $type = 'document';
        }

        ReimbursementAttachment::create([
          'reimbursement_id' => $reimbursement->id,
          'file_path' => $path,
          'original_name' => $file->getClientOriginalName(),
          'file_type' => $type,
        ]);
      }
    }

    return redirect()
      ->route('pages-reimburs')
      ->with('success', 'Reimbursement request created successfully!');
  }

  public function detail($id)
  {
    $reimbursement = Reimbursement::with('submittedBy', 'approvedBy', 'attachments')->findOrFail($id);

    return view('content.pages.pages-reimburs-detail', ['reimburs' => $reimbursement]);
  }

  public function approve(Request $request, $id)
  {
    $user = auth()->user();

    if (!$user->isSupervisor() && !$user->isAdmin()) {
      abort(403, 'Only supervisors can approve reimbursements');
    }

    $reimbursement = Reimbursement::findOrFail($id);

    if ($reimbursement->status !== 'Pending') {
      return back()->with('error', 'Only pending reimbursements can be approved');
    }

    $reimbursement->update([
      'status' => 'Approved',
      'approved_by' => auth()->id(),
      'approved_at' => now(),
    ]);

    return back()->with('success', 'Reimbursement approved successfully!');
  }

  public function reject(Request $request, $id)
  {
    $user = auth()->user();

    if (!$user->isSupervisor() && !$user->isAdmin()) {
      abort(403, 'Only supervisors can reject reimbursements');
    }

    $request->validate(['reason' => 'required|string']);

    $reimbursement = Reimbursement::findOrFail($id);

    if ($reimbursement->status !== 'Pending') {
      return back()->with('error', 'Only pending reimbursements can be rejected');
    }

    $reimbursement->update([
      'status' => 'Rejected',
      'rejection_reason' => $request->reason,
      'approved_by' => auth()->id(),
      'approved_at' => now(),
    ]);

    return back()->with('success', 'Reimbursement rejected successfully!');
  }
}
