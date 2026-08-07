<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::latest()->get();
        return view('content.roles.index', compact('roles'));
    }

    public function create()
    {
        $menuFile = resource_path('menu/verticalMenu.json');
        $menuData = json_decode(File::get($menuFile), true);
        $menus = $menuData['menu'];
        
        return view('content.roles.create', compact('menus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'menus' => 'nullable|array'
        ]);

        $slug = Str::slug($request->name);
        
        // Ensure slug is unique
        if (Role::where('slug', $slug)->exists()) {
            $slug = $slug . '-' . time();
        }

        $role = Role::create([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description
        ]);

        $this->updateMenuJson($slug, $request->menus ?? []);

        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        $menuFile = resource_path('menu/verticalMenu.json');
        $menuData = json_decode(File::get($menuFile), true);
        $menus = $menuData['menu'];
        
        return view('content.roles.edit', compact('role', 'menus'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'menus' => 'nullable|array'
        ]);

        $slug = Str::slug($request->name);
        if ($slug !== $role->slug && Role::where('slug', $slug)->exists()) {
            $slug = $slug . '-' . time();
        }

        $oldSlug = $role->slug;

        $role->update([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description
        ]);

        $this->updateMenuJson($slug, $request->menus ?? [], $oldSlug);

        return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        if (in_array($role->slug, ['superadmin', 'admin'])) {
            return back()->with('error', 'Cannot delete system roles.');
        }

        $this->updateMenuJson(null, [], $role->slug);
        
        // Optionally update users with this role to a default role
        \App\Models\User::where('role', $role->slug)->update(['role' => 'tim_internal']);

        $role->delete();

        return back()->with('success', 'Role deleted successfully.');
    }

    private function updateMenuJson($newSlug, $selectedMenus, $oldSlug = null)
    {
        $menuFile = resource_path('menu/verticalMenu.json');
        $menuData = json_decode(File::get($menuFile), true);
        
        foreach ($menuData['menu'] as &$menuItem) {
            // Remove old slug if it exists
            if ($oldSlug && in_array($oldSlug, $menuItem['roles'])) {
                $menuItem['roles'] = array_values(array_diff($menuItem['roles'], [$oldSlug]));
            }
            
            // Add new slug if this menu was selected
            if ($newSlug && in_array($menuItem['slug'], $selectedMenus)) {
                if (!in_array($newSlug, $menuItem['roles'])) {
                    $menuItem['roles'][] = $newSlug;
                }
            } else if ($newSlug && !in_array($menuItem['slug'], $selectedMenus)) {
                // If the new slug was previously in this menu but is now unchecked, remove it
                if (in_array($newSlug, $menuItem['roles'])) {
                    $menuItem['roles'] = array_values(array_diff($menuItem['roles'], [$newSlug]));
                }
            }
        }

        File::put($menuFile, json_encode($menuData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}
