<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Http\Resources\CompanyResource;

class CompanyController extends Controller
{
    /**
     * Retrieve a list of companies with optional filtering by name
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function index(Request $request)
    {
        if ($request->user()->cannot('viewAny', Company::class)) {
            abort(403);
        }

        $companies = Company::query()
            ->when($request->filled('name'), fn($q) => $q->filterByName($request->name))
            ->paginate(10);

        return CompanyResource::collection($companies);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        if ($request->user()->cannot('create', Company::class)) {
            abort(403);
        }
    
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'billing_address' => 'required|string|max:255',
        ]);
    
        $employee = Company::create([
            ...$validated
        ]);
    
        return new CompanyResource($employee);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Company $company)
    {
        if ($request->user()->cannot('view', $company)) {
            abort(403);
        }
    
        return new CompanyResource($company);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Company $company)
    {
        if ($request->user()->cannot('update', $company)) {
            abort(403);
        }
    
        $company->update($request->all());
    
        return new CompanyResource($company);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Company $company)
    {
        if ($request->user()->cannot('delete', $company)) {
            abort(403);
        }
    
        $company->delete();
    
        return response()->noContent();
    }

    /**
     * Calculate the billing for each company based on the number of employees (users)
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function billingByCompany(Request $request)
    {
        // Retrieve the cost per user from the configuration
        $costPerUser = config('platform.cost_per_user');

        $billing = Company::withCount('employees')
            ->get()
            ->map(function ($company) use ($costPerUser) {
                return [
                    'company_id' => $company->id,
                    'company_name' => $company->name,
                    'user_count' => $company->employees_count,
                    'total_usd' => $company->employees_count * $costPerUser,
                ];
            });
    
        return response()->json($billing);
    }
}
