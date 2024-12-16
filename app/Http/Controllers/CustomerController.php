<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = Customer::with('business')->orderByDesc('created_at')->paginate(10);
        return $customers;
    }

    public function byKey($key)
    {
        $customers = Customer::where(['dni' => $key])->orWhere('name', 'like', "%$key%")->with('business')->get();
        if ($customers->count()) {
            return $customers;
        } else {
            return response('Sin resultados', 400);
        }
    }

    public function byDni($dni)
    {
        $customer = Customer::where(['dni' => $dni])->first();
        if ($customer) {
            return $customer;
        } else {
            return response('Sin resultados', 400);
        }
    }

    public function byBusiness($businessId)
    {
        $customers = Customer::where(['business_id' => $businessId])->get();
        return $customers;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $customer = new Customer($request->customer);
        $foundCustomer = Customer::where(['dni' => $customer->dni])->first();
        if ($foundCustomer) {
            return response('Hay un cliente con este mismo N° de DNI', 400);
        } else {
            $customer->save();
            return $customer;
        }
    }

    public function storeMassive(Request $request)
    {
        $customers = $request->customers;
        foreach ($customers as $item) {
            $customer = Customer::where([
                'dni' => $item['dni'],
            ])->first();
            if (!$customer) {
                Customer::create([
                    'business_id' => $item['business_id'],
                    'dni' => $item['dni'],
                    'name' => $item['name'],
                ]);
            } else {
                $customer->fill($item);
                $customer->save();
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $customer = Customer::find($id);
        return $customer;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $customer = Customer::find($id);
        $foundCustomer = Customer::where(['dni' => $request->customer['dni']])->first();
        if ($foundCustomer && $foundCustomer->id != $id) {
            return response('Hay otro cliente con este N° de DNI', 400);
        } else {
            $customer->fill($request->customer)->save();
            return $customer;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Customer::find($id)->forceDelete();
    }
}
