<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaksi\PointOfSale\OpenCashierRequest;
use App\Http\Requests\Transaksi\Sale\SaleStoreRequest;
use App\Models\CashierShift;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\Sale;
use App\Models\Stock;
use App\Models\StockTransaction;
use App\Models\Warehouse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use GuzzleHttp\Client;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class PointOfSaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response | RedirectResponse
    {
        $startOfDay = Carbon::now()->startOfDay(); // Hari ini jam 00:00:00
        $endOfDay = Carbon::now()->endOfDay();     // Hari ini jam 23:59:59
        $cashierShift = CashierShift::where('user_id', Auth::id())->whereBetween('mulai_shift', [$startOfDay, $endOfDay])
            ->whereNull('selesai_shift')->first();

        if (!$cashierShift) {

            return to_route('trn.pos.open');
        }
        $noInvoice = generateInvoiceCode();
        $metodePenjualan = Sale::metodePenjualan();
        $metodePembayaran = Sale::metodePembayaran();
        return Inertia::render('transaction/pos/View', [
            'noInvoice' => $noInvoice,
            'metodePenjualan' => $metodePenjualan,
            'metodePembayaran' => $metodePembayaran,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $startOfDay = Carbon::now()->startOfDay(); // Hari ini jam 00:00:00
        $endOfDay = Carbon::now()->endOfDay();     // Hari ini jam 23:59:59
        $cashierShift = CashierShift::where('user_id', Auth::id())->whereBetween('mulai_shift', [$startOfDay, $endOfDay])
            ->whereNull('selesai_shift')->first();
        if ($cashierShift) {

            return to_route('trn.pos.index');
        }
        $warehouses = Warehouse::where('tipe', 'toko')->get();
        return Inertia::render('transaction/pos/Open', [
            'warehouses' => $warehouses,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function openCashier(OpenCashierRequest $request)
    {
        $startOfDay = Carbon::now()->startOfDay(); // Hari ini jam 00:00:00
        $endOfDay = Carbon::now()->endOfDay();     // Hari ini jam 23:59:59
        $cashierShift = CashierShift::where('user_id', Auth::id())->whereBetween('mulai_shift', [$startOfDay, $endOfDay])
            ->whereNull('selesai_shift')->first();
        if (!$cashierShift) {
            CashierShift::create([
                'user_id' => Auth::id(),
                'warehouse_id' => $request->warehouse_id,
                'mulai_shift' => now()
            ]);

            session()->put('warehouse_user', $request->warehouse_id);

            return to_route('trn.pos.index');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SaleStoreRequest $request)
    {
        $startOfDay = Carbon::now()->startOfDay(); // Hari ini jam 00:00:00
        $endOfDay = Carbon::now()->endOfDay();     // Hari ini jam 23:59:59
        $cashierShift = CashierShift::where('user_id', Auth::id())->whereBetween('mulai_shift', [$startOfDay, $endOfDay])
            ->whereNull('selesai_shift')->first();

        $noInvoice = null;
        $qrisUrl = null;

        $collProducts = collect($request->items);
        $collProducts = $collProducts->map(function ($item) {
            return [
                'id' => $item['id'],
                'satuan_cart' => $item['satuan_cart'],
                'quantity' => $item['quantity'],
            ];
        });

        $productIds = collect($request->items)->pluck('id')->toArray();
        $productUnits = collect($request->items)->pluck('satuan_cart')->toArray();
        // Ambil data produk dengan harga yang sesuai dengan satuan_cart
        $products = Product::with(['productPricings' => function ($query) use ($productUnits) {
            $query->whereIn('satuan_id', $productUnits);
        }])->whereIn('id', $productIds)->get();

        // Format hasil dengan menyesuaikan satuan yang sesuai
        $filteredProducts = $products->map(function ($product) use ($collProducts) {
            $cartItem = $collProducts->firstWhere('id', $product->id);

            $price = $product->productPricings->where('satuan_id', $cartItem['satuan_cart'])->first();

            return [
                'id' => $product->id,
                'nama_produk' => $product->nama_produk,
                'kode_produk' => $product->kode_produk,
                'barcode' => $product->barcode,
                'harga_beli' => $price ? $price->harga_beli : null,
                'harga_jual' => $price ? $price->harga_jual : null,
                'quantity' => $cartItem['quantity'],
                'satuan_cart' => $cartItem['satuan_cart'],
            ];
        });

        // dd($filteredProducts);

        DB::transaction(function () use ($request, $cashierShift, &$noInvoice, &$qrisUrl, $filteredProducts) {
            $total = 0;

            $sale = Sale::create([
                'no_invoice' => $request->no_invoice,
                'cashier_shift_id' => $cashierShift->id,
                'total' => 0,
                'metode_penjualan' => $request->metode_penjualan,
                'metode_pembayaran' => $request->metode_pembayaran,
                'status' => 'pending',
            ]);

            foreach ($filteredProducts as $key => $item) {
                $sale->items()->create([
                    'product_id' => $item['id'],
                    'satuan_id' => $item['satuan_cart'],
                    'jumlah' => $item['quantity'],
                    'harga' => $item['harga_jual'],
                    'harga_modal' => $item['harga_beli'],
                    'subtotal' => $item['harga_jual'] * $item['quantity'],
                ]);
                $total = $total + ($item['harga_jual'] * $item['quantity']);

                // Catet Stock
                StockTransaction::create([
                    'user_id' => Auth::id(),
                    'warehouse_id' => $cashierShift->warehouse_id,
                    'product_id' => $item['id'],
                    'jumlah' => -1 * ($item['quantity']),
                    'satuan_id' => $item['satuan_cart'],
                ]);

                // check konversi satuan
                $productPrice = ProductPrice::where('product_id', $item['id'])
                    ->where('satuan_id', $item['satuan_cart'])
                    ->where('warehouse_id', $cashierShift->warehouse_id)
                    ->first();

                // update stock
                $quantityStock = $productPrice->konversi * $item['quantity'];
                Stock::where('warehouse_id', $cashierShift->warehouse_id)
                    ->where('product_id', $item['id'])->decrement('jumlah', $quantityStock);
            }

            if ($request->metode_pembayaran == 'qris') {
                // Biaya admin QRIS
                $feePercentage = 0.007;
                $feeAmount = ceil($total * $feePercentage);
                $totalAmount = $total + $feeAmount;

                // Buat pembayaran QRIS
                $dataQris = $this->createQris([
                    'order_number' => $request->no_invoice,
                    'amount' => $totalAmount,
                ]);

                if (!$dataQris || !isset($dataQris['paymentUrl'])) {
                    return back()->with('error', 'Gagal membuat pembayaran QRIS.');
                }

                // Simpan informasi pembayaran qris
                Payment::create([
                    'sales_id' => $sale->id,
                    'metode_pembayaran' => $request->metode_pembayaran,
                    'total_transaksi' => $total,
                    'total_bayar' => 0, // Belum dibayar, menunggu QRIS
                    'kembalian' => 0,
                    'qris_url' => $request->metode_pembayaran == 'qris' ? $dataQris['paymentUrl'] : null,
                ]);

                $noInvoice = $sale->no_invoice;
                $qrisUrl = $dataQris['paymentUrl'];
            }

            if ($request->metode_pembayaran == 'cash') {
                // Simpan informasi pembayaran cash
                Payment::create([
                    'sales_id' => $sale->id,
                    'metode_pembayaran' => $request->metode_pembayaran,
                    'total_transaksi' => $total,
                    'total_bayar' => $request->paid_amount,
                    'kembalian' => $request->paid_amount - $total,
                    'status' => 'paid',
                ]);
            }

            $sale->update([
                'total' => $total,
                'kembalian' => $request->metode_pembayaran == 'cash' ? $total - $request->paid_amount : 0,
                'status' => $request->metode_pembayaran == 'cash' ? 'completed' : $sale->status,
            ]);
        });

        if ($request->metode_pembayaran == 'qris') {
            return to_route('trn.pos.index')
                ->with('success', 'Penjualan berhasil disimpan')
                ->with('submitedinvoice', $noInvoice)
                ->with('paymentUrl', $qrisUrl);
        }
        return to_route('trn.pos.index')
            ->with('success', 'Penjualan berhasil disimpan');
    }

    public function createQris($dataPayment)
    {
        $orderNumber = $dataPayment['order_number'];
        $amount = $dataPayment['amount'];

        $merchantCode = env('DUITKU_MERCHANT_CODE');
        $apiKey = env('DUITKU_API_KEY');
        $signature = md5($merchantCode . $orderNumber . $amount . $apiKey);

        $params = [
            'merchantCode' => $merchantCode,
            'paymentAmount' => $amount,
            'paymentMethod' => 'SP',
            'merchantOrderId' => $orderNumber,
            'signature' => $signature,
            'productDetails' => 'Pembayaran Menggunakan QRIS',
            'customerVaName' => 'Pembeli',
            'email' => 'customer@example.com',
            'callbackUrl' => route('trn.pos.payment.callback'), // Pastikan route ini ada
            'returnUrl' => route('trn.pos.payment.return'),
        ];

        $client = new Client();
        $response = $client->post('https://sandbox.duitku.com/webapi/api/merchant/v2/inquiry', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => $params
        ]);

        $body = $response->getBody();
        return json_decode($body, true);
    }

    public function handleCallback(Request $request)
    {
        $data = $request->all();
        $orderNumber = $data['merchantOrderId'] ?? null;
        $statusCode = $data['resultCode'] ?? null;

        $sale = Sale::where('no_invoice', $orderNumber)->first();
        $payment = Payment::where('sales_id', $sale->id)->where('status', 'unpaid')->first();

        if (!$payment) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        if ($statusCode == '00') { // 00 = sukses
            // Biaya admin QRIS
            $payment->update([
                'total_bayar' => $payment->total_transaksi,
                'status' => 'paid',
            ]);

            $sale->update(['status' => 'completed']);
        }

        return response()->json(['message' => 'Callback berhasil diproses']);
    }

    public function paymentReturn(Request $request)
    {
        $data = $request->all();
        $orderNumber = $data['merchantOrderId'] ?? null;
        $statusCode = $data['resultCode'] ?? null;

        if ($statusCode == '00') { // success
            return Inertia::render('notification/PaymentNotifSuccess');
        }

        if ($statusCode == '01') { // pending
            return Inertia::render('notification/PaymentNotifPending');
        }

        if ($statusCode == '02') { // canceled
            $sale = Sale::where('no_invoice', $orderNumber)->first();
            $payment = Payment::where('sales_id', $sale->id)->where('status', 'unpaid')->first();

            if ($sale) {
                $sale->update([
                    'status' => 'canceled',
                ]);
            }
            if ($payment) {
                $payment->update([
                    'status' => 'canceled'
                ]);
            }

            return Inertia::render('notification/PaymentNotifFailed');
        }
    }

    public function generateInvoice(Request $request)
    {
        $noIvoice = $request->invoice_no;
        $sale = Sale::where('no_invoice', $noIvoice)->first();
        $sale->load(['items', 'items.product', 'items.unit', 'payment']);

        // Ukuran dasar tinggi kertas (header, footer, dll.)
        $baseHeight = 200;
        $lineHeight = 15; // Tinggi per baris
        $maxCharsPerLine = 32; // Maksimal karakter sebelum break line

        // Hitung total tinggi berdasarkan jumlah baris per item
        $totalHeight = $baseHeight;
        foreach ($sale->items as $item) {
            $productName = $item->product->name;
            $numLines = ceil(strlen($productName) / $maxCharsPerLine); // Hitung jumlah baris
            $totalHeight += $numLines * $lineHeight; // Tambahkan tinggi sesuai jumlah baris
        }

        // Pastikan tinggi tidak terlalu kecil
        if ($totalHeight < 300) {
            $totalHeight = 300;
        }

        // Ukuran kertas 58mm, jika printer 80mm gunakan 302, 1000 panjangnya
        $pdf = Pdf::loadView('pdf.invoice_pos', compact('sale'))->setPaper([0, 0, 220, $totalHeight])
            ->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'defaultFont' => 'sans-serif'])
            ->setOptions(['isPhpEnabled' => true])
            ->setOptions(['isFontSubsettingEnabled' => true])
            ->setOptions(['dpi' => 96]);

        // Pastikan folder invoice ada
        $storagePath = storage_path('app/public/invoice');
        if (!File::exists($storagePath)) {
            File::makeDirectory($storagePath, 0755, true);
        }
        // Simpan PDF ke storage
        $filename = 'invoice_pos_' . Auth::id() . '.pdf';
        $filePath = "invoice/{$filename}";
        Storage::disk('public')->put($filePath, $pdf->output());

        // Kembalikan URL file PDF
        return response()->json([
            'pdf_url' => asset("storage/invoice/{$filename}")
        ]);
    }
}
