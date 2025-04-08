<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Http\Requests\MasterData\Unit\UnitStoreRequest;
use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) : Response
    {
        $search = $request->input('search');

        $units = Unit::query()
        ->when($search, function ($query, $search) {
            $query->where('nama_satuan', 'like', "%{$search}%");
        })
        ->paginate(10)
        ->withQueryString();

        return Inertia::render('masterdata/unit/View', [
            'units' => $units,
            'filters' => ['search' => $search],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('masterdata/unit/Edit', [
            'status' => 'create'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UnitStoreRequest $request) : RedirectResponse
    {
        $unit = new Unit();
        $unit->nama_satuan = $request->nama_satuan;
        $unit->save();
        return to_route('md.units.index'); 
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id) : Response
    {
        $unit = Unit::find($id);
        return Inertia::render('masterdata/unit/Edit', [
            'status' => 'edit',
            'unit' => $unit
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UnitStoreRequest $request, string $id) : RedirectResponse
    {
        $unit = Unit::find($id);
        $unit->nama_satuan = $request->nama_satuan;
        $unit->save();
        return to_route('md.units.index');   
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) : RedirectResponse
    {
        $unit = Unit::find($id);
        $unit->delete();
        return to_route('md.units.index'); 
    }
}
