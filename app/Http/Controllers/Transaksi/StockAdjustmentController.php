<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaksi\StockAdjustment\StockAdjustmentStoreRequest;
use App\Models\ProductPrice;
use App\Models\Stock;
use App\Models\StockAdjustment;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class StockAdjustmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $warehouse_id = $request->query('warehouse_id');
        $product_id = $request->query('product_id');

        $stock_adjustments = StockAdjustment::query()
            ->with(['warehouse:id,nama_gudang', 'product:id,nama_produk,satuan_dasar_id', 'product.satuanDasar:id,nama_satuan'])
            ->when($warehouse_id, function ($query, $warehouse_id) {
                $query->where('warehouse_id', $warehouse_id);
            })
            ->when($product_id, function ($query, $product_id) {
                $query->where('product_id', $product_id);
            })
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('transaction/stock_adjustment/View', [
            'stockAdjustments' => $stock_adjustments,
            'filters' => ['warehouse_id' => $warehouse_id, 'product_id' => $product_id],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $units = Unit::all();
        $adjustmentTypes = StockAdjustment::adjustmentTypes();
        return Inertia::render('transaction/stock_adjustment/Edit', [
            'status' => 'create',
            'units' => $units,
            'adjustmentTypes' => $adjustmentTypes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StockAdjustmentStoreRequest $request)
    {
        DB::transaction(function () use ($request) {
            // Catat di stock_adjustments
            $adjustment = StockAdjustment::create([
                'warehouse_id' => $request->warehouse_id,
                'warehouse_target_id' => $request->warehouse_target_id ?? null,
                'product_id' => $request->product_id,
                'user_id' => Auth::id(),
                'adjustment_type' => $request->adjustment_type,
                'satuan_id' => $request->satuan_id,
                'jumlah' => $request->jumlah,
                'reason' => $request->reason
            ]);

            // Update stok di tabel Stock
            $stock = Stock::where('warehouse_id', $request->warehouse_id)
                ->where('product_id', $request->product_id)
                ->first();

            $jumlahKonversi = $request->jumlah;
            // konversi satuan stok ke satuan dasar
            $unitConvertion = ProductPrice::where('product_id', $request->product_id)
                ->where('satuan_id', $request->satuan_id)
                ->where('warehouse_id', session('warehouse_user'))
                ->first();

            if ($unitConvertion) {
                $jumlahKonversi = $unitConvertion->konversi * $request->jumlah;
            }

            if ($stock) {
                switch ($request->adjustment_type) {
                    case 'expired':
                    case 'lost':
                    case 'damaged':
                        $stock->jumlah -= $jumlahKonversi; // Stok berkurang
                        break;
                    case 'transfer':
                        $stock->jumlah -= $jumlahKonversi; // Kurangi stok di gudang asal
                        $targetStock = Stock::firstOrCreate(
                            ['warehouse_id' => $request->warehouse_target_id, 'product_id' => $request->product_id],
                            ['jumlah' => 0]
                        );
                        $targetStock->jumlah += $jumlahKonversi; // Tambah stok di gudang tujuan
                        $targetStock->save();
                        break;
                }
                $stock->save();
            }
        });

        return to_route('trn.stock_adjustments.index');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $adjustment = StockAdjustment::findOrFail($id);
        
        DB::transaction(function () use ($adjustment) {
            // Ambil stok gudang asal
            $stock = Stock::where('warehouse_id', $adjustment->warehouse_id)
            ->where('product_id', $adjustment->product_id)
            ->first();

            $jumlahKonversi = $adjustment->jumlah;
            // konversi satuan stok ke satuan dasar
            $unitConvertion = ProductPrice::where('product_id', $adjustment->product_id)
                ->where('satuan_id', $adjustment->satuan_id)
                ->where('warehouse_id', session('warehouse_user'))
                ->first();

            if ($unitConvertion) {
                $jumlahKonversi = $unitConvertion->konversi * $adjustment->jumlah;
            }

            if ($stock) {
                switch ($adjustment->adjustment_type) {
                    case 'expired':
                    case 'lost':
                    case 'damaged':
                        $stock->jumlah += $jumlahKonversi; // Kembalikan stok
                        break;
                    case 'transfer':
                        // Kembalikan stok di gudang asal
                        $stock->jumlah += $jumlahKonversi;

                        // Kurangi stok di gudang tujuan
                        $targetStock = Stock::where('warehouse_id', $adjustment->warehouse_target_id)
                            ->where('product_id', $adjustment->product_id)
                            ->first();
                        if ($targetStock) {
                            $targetStock->jumlah -= $jumlahKonversi;
                            $targetStock->save();
                        }
                        break;
                }
                $stock->save();
            }

            // Hapus data stock adjustment
            $adjustment->delete();
        });

        return to_route('trn.stock_adjustments.index');
    }
}
