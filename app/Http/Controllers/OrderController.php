<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

use App\Models\Company;
use App\Models\Order;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new OrderResource(Order::paginate());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }

    /**
     * Get the total consumption of a company in the last week.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function consumptionLastWeek(Request $request)
    {
        $request->validate([
            'company_id' => 'required',
        ]);

        $company = Company::findOrFail($request->company_id);

        // From last week to today
        $from = Carbon::now()->subWeek()->startOfDay();
        $to = Carbon::now()->endOfDay();

        // Retrieve the orders of the company within the last week
        $orders = Order::where('company_id', $company->id)
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('DATE(created_at) as date')
            ->groupByRaw('DATE(created_at)')
            ->orderBy('date')
            ->paginate(10);

        return response()->json([
            'company_id' => $company->id,
            'company_name' => $company->name,
            'consumptions' => $orders,
        ]);
    }
}
