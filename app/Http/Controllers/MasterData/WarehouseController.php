<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Http\Requests\MasterData\Warehouse\WarehouseStoreRequest;
use App\Models\Warehouse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $search = $request->input('search');

        $warehouses = Warehouse::query()
            ->when($search, function ($query, $search) {
                $query->where('nama_gudang', 'like', "%{$search}%");
            })
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('masterdata/warehouse/View', [
            'warehouses' => $warehouses,
            'filters' => ['search' => $search],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('masterdata/warehouse/Edit', [
            'status' => 'create',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(WarehouseStoreRequest $request): RedirectResponse
    {
        $warehouse = new Warehouse();
        $warehouse->nama_gudang = $request->nama_gudang;
        $warehouse->lokasi = $request->lokasi;
        $warehouse->phone = $request->phone;
        $warehouse->tipe = $request->tipe;
        $warehouse->save();
        return to_route('md.warehouses.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): Response
    {
        $warehouse = Warehouse::find($id);
        return Inertia::render('masterdata/warehouse/Edit', [
            'status' => 'edit',
            'warehouse' => $warehouse,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(WarehouseStoreRequest $request, string $id): RedirectResponse
    {
        $warehouse = Warehouse::find($id);
        $warehouse->nama_gudang = $request->nama_gudang;
        $warehouse->lokasi = $request->lokasi;
        $warehouse->phone = $request->phone;
        $warehouse->tipe = $request->tipe;
        $warehouse->save();
        return to_route('md.warehouses.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        $warehouse = Warehouse::find($id);
        $warehouse->delete();
        return to_route('md.warehouses.index');
    }

    /**
     * get lists warehouses.
     */
    public function getWarehouses(Request $request): JsonResponse
    {
        $search = $request->input('search');

        $warehouses = Warehouse::query()
            ->select('id', 'nama_gudang as name') 
            ->when($search, function ($query, $search) {
                $query->where('nama_gudang', 'like', "%{$search}%");
            })
            ->paginate(10);

        return response()->json([
            'data' => $warehouses->items(),
            'pagination' => [
                'current_page' => $warehouses->currentPage(),
                'last_page' => $warehouses->lastPage(),
            ],
        ]);
    }
}
