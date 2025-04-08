<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Http\Requests\MasterData\Product\ProductStoreRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\Unit;
use App\Models\Warehouse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $search = $request->input('search');

        $products = Product::query()
            ->when($search, function ($query, $search) {
                $query->where('kode_produk', $search);
                $query->orWhere('nama_produk', 'like', "%{$search}%");
            })
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('masterdata/product/View', [
            'products' => $products,
            'filters' => ['search' => $search],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $categories = Category::all();
        $units = Unit::all();
        return Inertia::render('masterdata/product/Edit', [
            'status' => 'create',
            'categories' => $categories,
            'units' => $units
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductStoreRequest $request): RedirectResponse
    {
        $product = new Product();
        $product->kode_produk = $request->kode_produk;
        $product->nama_produk = $request->nama_produk;
        $product->kategori_id = $request->kategori_id;
        $product->satuan_dasar_id = $request->satuan_dasar_id;
        $product->barcode = $request->barcode ? $request->barcode : $request->kode_produk;
        $product->harga_beli = $request->harga_beli;
        $product->harga_jual = $request->harga_jual;
        $product->deskripsi = $request->deskripsi;
        $product->save();

        return to_route('md.products.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): Response
    {
        $product = Product::find($id);
        $categories = Category::all();
        $units = Unit::all();
        $warehouses = Warehouse::all();
        $productPrices = ProductPrice::where('product_id', $id)->get();
        return Inertia::render('masterdata/product/Edit', [
            'status' => 'edit',
            'product' => $product,
            'categories' => $categories,
            'units' => $units,
            'warehouses' => $warehouses,
            'productPrices' => $productPrices,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductStoreRequest $request, string $id): RedirectResponse
    {
        $product = Product::find($id);
        $product->nama_produk = $request->nama_produk;
        $product->kategori_id = $request->kategori_id;
        $product->satuan_dasar_id = $request->satuan_dasar_id;
        $product->barcode = $request->barcode ? $request->barcode : $product->kode_produk;
        $product->harga_beli = $request->harga_beli;
        $product->harga_jual = $request->harga_jual;
        $product->deskripsi = $request->deskripsi;
        $product->save();
        return to_route('md.products.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        $product = Product::find($id);
        $product->delete();
        return to_route('md.products.index');
    }

    /**
     * generate code product.
     */
    public function generateCode()
    {
        // Generate kode produk unik
        $productCode = generateUniqueProductCode();
        return response()->json(['code' => $productCode]);
    }

    /**
     * clone product.
     */
    public function clone(string $id): RedirectResponse
    {
        // Mengambil data produk dari database yang akan di clone
        $product = Product::find($id);

        // clone dengan kode produk yang lain;
        $productCode = generateUniqueProductCode();
        $cloneProduct = Product::create([
            'kode_produk' => $productCode,
            'nama_produk' => $product->nama_produk,
            'kategori_id' => $product->kategori_id,
            'satuan_dasar_id' => $product->satuan_dasar_id,
            'barcode' => $product->barcode,
            'harga_beli' => $product->harga_beli,
            'harga_jual' => $product->harga_jual,
            'deskripsi' => $product->deskripsi,
        ]);

        return to_route('md.products.edit', $cloneProduct->id);
    }

    /**
     * get lists products.
     */
    public function getProducts(Request $request): JsonResponse
    {
        $search = $request->query('search');

        $products = Product::query()
            ->select('id', 'nama_produk', 'kode_produk', 'barcode', 'harga_beli', 'harga_jual', 'satuan_dasar_id')
            ->with([
                'productPricings:id,product_id,satuan_id,konversi,harga_jual,barcode',
                'productPricings.satuan:id,nama_satuan',
                'stock:id,product_id,warehouse_id,jumlah'
            ])
            ->when($search, function ($query, $search) {
                $query->where('nama_produk', 'like', "%{$search}%");
            })
            ->paginate(10);

        return response()->json([
            'data' => $products->items(),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
            ],
        ]);
    }

    public function getProduct(Request $request): JsonResponse
    {
        $search = $request->query('search');
        $type = $request->query('tipe') ?? 'id';
        $type = $request->query('tipe') ?? 'id';

        $product = Product::query()
            ->select('id', 'nama_produk', 'kode_produk', 'barcode', 'harga_beli', 'harga_jual', 'satuan_dasar_id')
            ->with([
                'productPricings:id,product_id,satuan_id,konversi,harga_jual,barcode',
                'productPricings.satuan:id,nama_satuan',
                'stock:id,product_id,warehouse_id,jumlah'
            ])
            ->where(function ($query) use ($type, $search) {
                if ($type == 'kode_produk') {
                    $query->where('kode_produk', $search);
                    $query->orWhere('barcode', $search);
                } else {
                    $query->where('id', $search);
                }
            })
            ->first();
        
       

        return response()->json([
            'data' => $product,
        ]);
    }
}
