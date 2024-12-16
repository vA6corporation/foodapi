<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DateTime;
use App\Models\Food;
use App\Models\Customer;
use App\Models\Turn;

class FoodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function byCustomerRangeDate($customerId, $sd, $ed)
    {
        $startDate = (new DateTime($sd))->format('Y-m-d');
        $endDate = (new DateTime($ed))->modify('+1 day')->format('Y-m-d');
        $foods = Food::whereBetween('created_at', [$startDate, $endDate])
            ->where(['customer_id' => $customerId])
            ->with('turn')
            ->get();
        $group = $foods->groupBy(function ($item, $key) {
            return (new DateTime($item->created_at))->format('Y-m-d');
        });
        return ['foods' => $group];
    }

    public function byCustomerDate($customerId, $date)
    {
        $startDate = (new DateTime($date))->format('Y-m-d');
        $endDate = (new DateTime($date))->modify('+1 day')->format('Y-m-d');
        $foods = Food::whereBetween('created_at', [$startDate, $endDate])
            ->where(['customer_id' => $customerId])
            ->with('turn')
            ->get();
        return $foods;
    }

    public function byBusinessRangeDate($businessId, $sd, $ed)
    {
        $customers = Customer::where(['business_id' => $businessId])->get();
        $customerIds = $customers->map(function($item) {
            return $item->id;
        });
        $startDate = (new DateTime($sd))->format('Y-m-d');
        $endDate = (new DateTime($ed))->modify('+1 day')->format('Y-m-d');
        $foods = Food::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('customer_id', $customerIds->all())
            ->with('turn')
            ->get();
        $group = $foods->groupBy(function ($item, $key) {
            return $item->customer_id;
        });

        $groupTwo = $group->map(function($item, $key) {
            $groupFoods = $item->groupBy(function($food) {
                return (new DateTime($food->created_at))->format('Y-m-d');
            });
            $customer = Customer::find($key);
            $customer->groupFoods = $groupFoods;
            return $customer;
        });
        return $groupTwo->all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $turn = Turn::whereNull('closed_at')->first();
        
        if (!$turn) {
            return response('No hay turno disponible', 400);
        }
        
        $customer = Customer::where(['dni' => $request->dni])->with('business')->first();
        
        if (!$customer) {
            return response('N° de DNI desconocido', 400);
        }
        
        $foundFood = Food::where([
            'customer_id' => $customer->id,
            'turn_id' => $turn->id,
        ])->first();

        if ($foundFood) {
            return response('El ticket ya ha sido impreso', 400);   
        } else {
            $food = new Food([
                'customer_id' => $customer->id,
                'turn_id' => $turn->id,
            ]);
            $food->save();
            $countFood = Food::where(['turn_id' => $food->turn_id])->count();
            return [
                'food' => $food,
                'customer' => $customer,
                'turn' => $turn,
                'countFood' => $countFood,
            ];
        }
    }

    public function storeFood(Request $request)
    {
        $turn = Turn::find($request->turnId);
        
        if (!$turn) {
            return response('No hay turno disponible', 400);
        }
        
        $customer = Customer::find($request->customerId);
        
        if (!$customer) {
            return response('N° de DNI desconocido', 400);
        }
        
        $foundFood = Food::where([
            'customer_id' => $customer->id,
            'turn_id' => $turn->id,
        ])->first();

        if ($foundFood) {
            return response('El ticket ya ha sido impreso', 400);
        } else {
            $food = new Food([
                'customer_id' => $customer->id,
                'turn_id' => $turn->id,
                'created_at' => $turn->created_at,
                'updated_at' => $turn->updated_at,
            ]);
            $food->save();
            return $food;
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
