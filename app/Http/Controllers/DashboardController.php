<?php

namespace App\Http\Controllers;

use App\Models\SaleDetail;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $bulanIni = Carbon::now()->format('Y-m');
        $tahunIni = Carbon::now()->format('Y');

        $dashboard = [
            'total_pendapatan' => DB::table('sales')
                ->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$bulanIni])
                ->sum('total'),

            'total_pengeluaran' => DB::table('expenses')
                ->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$bulanIni])
                ->sum('nominal'),

            'laba_bersih' => DB::table('sales')
                ->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$bulanIni])
                ->sum('total') -
                DB::table('expenses')
                ->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$bulanIni])
                ->sum('nominal'),

            'piutang_belum_lunas' => DB::table('account_receivables')
                ->where('status', 'unpaid')
                ->sum('sisa_piutang'),

            'saldo_kas' => DB::table('payments')
                ->where('status', 'paid')
                ->sum('total_bayar') -
                DB::table('expenses')
                ->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$bulanIni])
                ->sum('nominal')
        ];

        // Total Pendapatan Bulanan dalam Tahun Ini
        $pendapatanBulanan = DB::table('sales')
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as bulan, SUM(total) as total')
            ->where('created_at', 'like', "$tahunIni%")
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        $lowStockItems = Stock::with('product')
            ->where('jumlah', '<=', 10) // Anggap stok <= 10 dianggap hampir habis
            ->orderBy('jumlah', 'asc')
            ->get();

        $topProducts = SaleDetail::with('product')
            ->selectRaw('product_id, SUM(jumlah) as total_sold')
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->limit(5) // Ambil 5 produk terlaris
            ->get();

        return inertia('Dashboard', [
            'dashboard' => $dashboard,
            'pendapatanBulanan' => $pendapatanBulanan,
            'lowStockItems' => $lowStockItems,
            'topProducts' => $topProducts,
        ]);
    }
}
