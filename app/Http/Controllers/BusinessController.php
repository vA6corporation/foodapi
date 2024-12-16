<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Business;
use App\Models\Customer;

class BusinessController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $businesses = Business::orderByDesc('created_at')->paginate(10);
        return $businesses;
    }

    public function allBusinesses()
    {
        $businesses = Business::orderByDesc('created_at')->get();
        return $businesses;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $business = new Business($request->business);
        $business->save();
        return $business;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $business = Business::find($id);
        return $business;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $business = Business::find($id);
        $business->fill($request->business)->save();
        return $business;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Customer::where(['business_id' => $id])->forceDelete();
        Business::find($id)->forceDelete();
    }
}
