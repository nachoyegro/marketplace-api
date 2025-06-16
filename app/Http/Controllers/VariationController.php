<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVariationRequest;
use App\Http\Requests\UpdateVariationRequest;
use App\Models\Variation;
use App\Http\Resources\VariationResource;
use Illuminate\Http\Request;

class VariationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Variation::class);

        $variations = Variation::query()
            ->when($request->filled('title'), fn($q) => $q->filterByName($request->title))
            ->paginate(10);

        return VariationResource::collection($variations);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
        $this->authorize('create', Variation::class);

        $validated = $request->validated();
        $variation = Variation::create([
            ...$validated
        ]);

        return new VariationResource($variation);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVariationRequest $request)
    {
        $this->authorize('create', Variation::class);

        $validated = $request->validated();
        $variation = Variation::create([
            ...$validated
        ]);

        return new VariationResource($variation);
    }

    /**
     * Display the specified resource.
     */
    public function show(Variation $variation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Variation $variation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVariationRequest $request, Variation $variation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Variation $variation)
    {
        //
    }
}
