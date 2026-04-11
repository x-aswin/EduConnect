<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::latest()->get();
        return view('admin.manage-category', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        DB::transaction(function () use ($request) {

            $iconPath = 'bi-collection';
            if ($request->hasFile('icon')) {
                $iconPath = $request->file('icon')->store('categories/icons', 'public');
            }

            Category::create([
                'name' => $request->name,
                'description' => $request->description,
                'icon' => $iconPath,
            ]);
        });

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $categories = Category::latest()->get();
        $editCategory = Category::findOrFail($id);
        $viewOnly = true;

        return view('admin.manage-category', compact('categories', 'editCategory', 'viewOnly'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        $categories = Category::latest()->get();
        $isDeleteMode = $request->query('mode') === 'delete';

        if (!$isDeleteMode) {
            $editCategory = Category::findOrFail($id);
            return view('admin.manage-category', compact('categories', 'editCategory'));
        }

        $deleteCategory = Category::findOrFail($id);
        return view('admin.manage-category', compact('categories', 'deleteCategory', 'isDeleteMode'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = Category::findOrFail($id);

        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];

        $request->validate($rules);

        DB::transaction(function () use ($request, $category) {
            $updateData = [
                'name' => $request->name,
                'description' => $request->description,
            ];

            if ($request->hasFile('icon')) {
                if ($category->icon && str_contains($category->icon, '/') && Storage::disk('public')->exists($category->icon)) {
                    Storage::disk('public')->delete($category->icon);
                }

                $updateData['icon'] = $request->file('icon')->store('categories/icons', 'public');
            }

            $category->update([
                ...$updateData,
            ]);
        });

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);

        DB::transaction(function () use ($category) {
            if ($category->icon && str_contains($category->icon, '/') && Storage::disk('public')->exists($category->icon)) {
                Storage::disk('public')->delete($category->icon);
            }

            $category->delete();
        });

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully!');
    }
}
