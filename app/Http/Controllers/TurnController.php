<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Turn;
use DateTime;

class TurnController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $turns = Turn::orderByDesc('created_at')->paginate(10);
        return $turns;
    }

    public function open()
    {
        $turn = Turn::whereNull('closed_at')->first();
        return $turn;
    }

    public function byDate($date)
    {
        $startDate = (new DateTime($date))->format('Y-m-d');
        $endDate = (new DateTime($date))->modify('+1 day')->format('Y-m-d');
        $turns = Turn::whereBetween('created_at', [$startDate, $endDate])->get();
        return $turns;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $foundTurn = Turn::whereNull('closed_at')->first();
        if ($foundTurn) {
            return response('Hay un turno abierto', 400);
        } else {
            $today = (new DateTime())->format('Y-m-d');
            $tomorrow = (new DateTime())->modify('+1 day')->format('Y-m-d');
            $foundTurn = Turn::where([
                'food' => $request->turn['food'],
            ])->whereBetween('created_at', [$today, $tomorrow])->first();
            if ($foundTurn) {
                return response('Hay un turno con la misma comida para el dia de hoy', 400);
            } else {
                $turn = new Turn($request->turn);
                $turn->save();
                return $turn;
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $turn = Turn::find($id);
        return $turn;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $turn = Turn::find($id);
        $turn->fill($request->turn)->save();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
