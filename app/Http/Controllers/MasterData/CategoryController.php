<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Http\Requests\MasterData\Category\CategoryStoreRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) : Response
    {
        $search = $request->input('search');

        $categories = Category::query()
        ->when($search, function ($query, $search) {
            $query->where('nama_kategori', 'like', "%{$search}%");
        })
        ->paginate(10)
        ->withQueryString();

        return Inertia::render('masterdata/category/View', [
            'categories' => $categories,
            'filters' => ['search' => $search],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('masterdata/category/Edit', [
            'status' => 'create'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryStoreRequest $request) : RedirectResponse
    {
        $category = new Category();
        $category->nama_kategori = $request->nama_kategori;
        $category->slug = Str::slug($request->nama_kategori);

        $existsSlug = Category::where('slug', Str::slug($request->nama_kategori))->get();
        if (count($existsSlug) > 0) {
            $category->slug = Str::slug($request->nama_kategori) . count($existsSlug) + 1;
        }

        $category->save();
        return to_route('md.categories.index');   
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id) : Response
    {
        $category = Category::find($id);
        return Inertia::render('masterdata/category/Edit', [
            'status' => 'edit',
            'category' => $category
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryStoreRequest $request, string $id) : RedirectResponse
    {
        $category = Category::find($id);
        $category->nama_kategori = $request->nama_kategori;
        $category->save();
        return to_route('md.categories.index');   
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) : RedirectResponse
    {
        $category = Category::find($id);
        $category->delete();
        return to_route('md.categories.index');   
    }
}
