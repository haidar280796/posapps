<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaksi\Stock\StockStoreRequest;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\Stock;
use App\Models\StockTransaction;
use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ProductStockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $warehouse_id = $request->query('warehouse_id');
        $product_id = $request->query('product_id');

        $stocks = Stock::query()
            ->with(['warehouse:id,nama_gudang', 'product:id,nama_produk,satuan_dasar_id', 'product.satuanDasar:id,nama_satuan'])
            ->when($warehouse_id, function ($query, $warehouse_id) {
                $query->where('warehouse_id', $warehouse_id);
            })
            ->when($product_id, function ($query, $product_id) {
                $query->where('product_id', $product_id);
            })
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('transaction/stock/View', [
            'stocks' => $stocks,
            'filters' => ['warehouse_id' => $warehouse_id, 'product_id' => $product_id],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $units = Unit::all();
        return Inertia::render('transaction/stock/Edit', [
            'status' => 'create',
            'units' => $units,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StockStoreRequest $request): RedirectResponse
    {
        $stock = Stock::where('warehouse_id', $request->warehouse_id)->where('product_id', $request->product_id)->first();
        // catat stok transaksi untuk history
        StockTransaction::create([
            'user_id' => Auth::id(),
            'warehouse_id' => $request->warehouse_id,
            'product_id' => $request->product_id,
            'jumlah' => $request->jumlah,
            'satuan_id' => $request->satuan_id
        ]);
        $jumlahKonversi = $request->jumlah;
        // konversi satuan stok ke satuan dasar
        $unitConvertion = ProductPrice::where('product_id', $request->product_id)
            ->where('satuan_id', $request->satuan_id)
            ->where('warehouse_id', session('warehouse_user'))
            ->first();
        if ($unitConvertion) {
            $jumlahKonversi = $unitConvertion->konversi * $request->jumlah;
        }
        // tambah stok
        if ($stock) {
            $stock->jumlah = $stock->jumlah + $jumlahKonversi;
            $stock->save();
            return to_route('trn.product_stocks.index');
        } else {
            $stock = new Stock();
            $stock->warehouse_id = $request->warehouse_id;
            $stock->product_id = $request->product_id;
            $stock->jumlah = $jumlahKonversi;
            $stock->save();
            return to_route('trn.product_stocks.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $stock = Stock::find($id);
        $product = Product::where('id', $stock->product_id)->first();
        // catat stok transaksi untuk history
        StockTransaction::create([
            'user_id' => Auth::id(),
            'warehouse_id' => $stock->warehouse_id,
            'product_id' => $stock->product_id,
            'jumlah' => -1 * $stock->jumlah,
            'satuan_id' => $product->satuan_dasar_id
        ]);

        $stock->delete();
        return to_route('trn.product_stocks.index');
    }

    /**
     * convertToBaseUnit.
     */
    public function convertToBaseUnit($product_id, $satuan_id, $jumlah)
    {
        $productPrice = ProductPrice::where('product_id', $product_id)
            ->where('satuan_id', $satuan_id)
            ->where('warehouse_id', session('warehouse_user'))
            ->first();

        return $jumlah * $productPrice->konversi_ke_dasar;
    }
}
