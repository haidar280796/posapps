<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\CashierShift;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Show the login page.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $startOfDay = Carbon::now()->startOfDay(); // Hari ini jam 00:00:00
        $endOfDay = Carbon::now()->endOfDay();     // Hari ini jam 23:59:59
        $cashierShift = CashierShift::where('user_id', Auth::id())->whereBetween('mulai_shift', [$startOfDay, $endOfDay])
            ->whereNull('selesai_shift')->first();
        if ($cashierShift) {
            session()->put('warehouse_user', $cashierShift->warehouse_id);
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
