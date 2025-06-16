<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Http\Resources\EmployeeResource;
use App\Http\Requests\StoreEmployeeRequest;
use Illuminate\Http\Request;
use App\Enums\UserRole;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param \Illuminate\Http\Request $request
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Employee::class);
        $user = $request->user();

        $employees = Employee::query()
            ->when($request->filled('last_name'), fn($q) => $q->filterByLastName($request->last_name))
            ->when($request->filled('company'), fn($q) => $q->filterByCompany($request->company))
            ->when(
                $user->role === UserRole::ADMIN_COMPANY,
                fn($q) => $q->where('company_id', $user->getCompanyId())
            )
            ->paginate(10);

        return EmployeeResource::collection($employees);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request)
    {        
        $validated = $request->validated();
        $order = Employee::create([
            ...$validated
        ]);
    
        return new EmployeeResource($order);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Employee $employee)
    {
        $this->authorize('view', $employee);
        return new EmployeeResource($employee);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        $this->authorize('update', $employee);
        $employee->update($request->all());
        return new EmployeeResource($employee);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Employee $employee)
    {
        $this->authorize('delete', $employee);
        $employee->delete();
        return response()->noContent();
    }
    
}
