<?php
namespace App\Http\Controllers;
use App\Models\ContentType;
use Illuminate\Http\Request;

class ContentTypeController extends Controller
{
    public function index()
    {
        $contentTypes = ContentType::latest()->paginate(10);
        return view('content.content_types.index', compact('contentTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->has('is_active');

        ContentType::create($validated);
        return redirect()->route('content_types.index')->with('success', 'Content Type created successfully');
    }

    public function update(Request $request, ContentType $contentType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $contentType->update($validated);
        return redirect()->route('content_types.index')->with('success', 'Content Type updated successfully');
    }

    public function destroy(ContentType $contentType)
    {
        $contentType->delete();
        return redirect()->route('content_types.index')->with('success', 'Content Type deleted successfully');
    }
}
