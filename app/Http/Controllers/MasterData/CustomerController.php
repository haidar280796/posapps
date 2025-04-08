<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Http\Requests\MasterData\Customer\CustomerStoreRequest;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) : Response
    {
        $search = $request->input('search');

        $customers = Customer::query()
        ->when($search, function ($query, $search) {
            $query->where('nama', 'like', "%{$search}%");
        })
        ->paginate(10)
        ->withQueryString();

        return Inertia::render('masterdata/customer/View', [
            'customers' => $customers,
            'filters' => ['search' => $search],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('masterdata/customer/Edit', [
            'status' => 'create'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CustomerStoreRequest $request) : RedirectResponse
    {
        $customer = new Customer();
        $customer->nama = $request->nama;
        $customer->email = $request->email;
        $customer->telepon = $request->telepon;
        $customer->alamat = $request->alamat;
        $customer->save();
        return to_route('md.customers.index');   
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id) : Response
    {
        $customer = Customer::find($id);
        return Inertia::render('masterdata/customer/Edit', [
            'status' => 'edit',
            'customer' => $customer
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CustomerStoreRequest $request, string $id) : RedirectResponse
    {
        $customer = Customer::find($id);
        $customer->nama = $request->nama;
        $customer->email = $request->email;
        $customer->telepon = $request->telepon;
        $customer->alamat = $request->alamat;
        $customer->save();
        return to_route('md.customers.index');   
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) : RedirectResponse
    {
        $customer = Customer::find($id);
        $customer->delete();
        return to_route('md.customers.index');   
    }
}
