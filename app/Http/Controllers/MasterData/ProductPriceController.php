<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Http\Requests\MasterData\ProductPrice\ProductPriceStoreRequest;
use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductPriceController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductPriceStoreRequest $request, string $id) : RedirectResponse
    {
        $product = Product::find($id);
        $currentProductPrice = ProductPrice::where('product_id', $id)
        ->where('warehouse_id', $request->warehouse_id)
        ->where('satuan_id', $request->satuan_id)->first();
        if ($product->satuan_dasar_id == $request->satuan_id && $currentProductPrice) {
            
            return to_route('md.products.edit', $id)->with('error', 'Product Unit already exists');
        }
        $productPrice = new ProductPrice();
        $productPrice->product_id = $product->id;
        $productPrice->warehouse_id = $request->warehouse_id;
        $productPrice->satuan_id = $request->satuan_id;
        $productPrice->konversi = $request->konversi;
        $productPrice->harga_beli = $request->harga_beli;
        $productPrice->harga_jual = $request->harga_jual;
        $productPrice->barcode = $request->barcode ? $request->barcode : $product->barcode;
        $productPrice->save();
        return to_route('md.products.edit', $id)->with('success', 'Product Unit saved'); 
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductPriceStoreRequest $request, string $id, string $priceId) : RedirectResponse
    {
        $product = Product::find($id);
        $productPrice = ProductPrice::find($priceId);
        $productPrice->warehouse_id = $request->warehouse_id;
        $productPrice->satuan_id = $request->satuan_id;
        $productPrice->konversi = $request->konversi;
        $productPrice->harga_beli = $request->harga_beli;
        $productPrice->harga_jual = $request->harga_jual;
        $productPrice->barcode = $request->barcode ? $request->barcode : $product->barcode;
        $productPrice->save();
        return to_route('md.products.edit', $id)->with('success', 'Product Unit saved'); 
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, string $priceId) : RedirectResponse
    {
        $productPrice = ProductPrice::find($priceId);
        // cek apakah yang dihapus satuan dasar?
        $product = Product::find($id);
        if ($product->satuan_dasar_id == $productPrice->satuan_id) {
            return to_route('md.products.edit', $id)->with('error', 'Product Unit can\'t be Deleted');
        }
        $productPrice->delete();
        return to_route('md.products.edit', $id)->with('success', 'Product Unit Deleted');  
    }
}
