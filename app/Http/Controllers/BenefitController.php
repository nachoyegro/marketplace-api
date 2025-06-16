<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBenefitRequest;
use App\Http\Requests\UpdateBenefitRequest;
use App\Models\Benefit;
use App\Http\Resources\BenefitResource;
use Illuminate\Http\Request;

class BenefitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Benefit::class);

        $benefits = Benefit::query()
            ->when($request->filled('name'), fn($q) => $q->filterByName($request->name))
            ->when($request->filled('country_code'), fn($q) => $q->filterByCountryCode($request->country_code))
            ->paginate(10);

        return BenefitResource::collection($benefits);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBenefitRequest $request)
    {
        $this->authorize('create', Benefit::class);

        $validated = $request->validated();
        $benefit = Benefit::create([
            ...$validated
        ]);

        return new BenefitResource($benefit);
    }

    /**
     * Display the specified resource.
     */
    public function show(Benefit $benefit)
    {
        $this->authorize('view', $benefit);
        return new BenefitResource($benefit);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBenefitRequest $request, Benefit $benefit)
    {
        $this->authorize('update', $benefit);
        $benefit->update($request->all());
        return new BenefitResource($benefit);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Benefit $benefit)
    {
        $this->authorize('delete', $benefit);
        $benefit->delete();
        return response()->noContent();
    }
}
