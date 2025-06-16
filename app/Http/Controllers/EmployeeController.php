<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Http\Resources\EmployeeResource;
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
        if ($request->user()->cannot('viewAny', Employee::class)) {
            abort(403);
        }
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
    public function store(Request $request)
    {
        if ($request->user()->cannot('create', Employee::class)) {
            abort(403);
        }
    
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'company_id' => 'required|exists:companies,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'credits' => 'required|integer|min:0',
        ]);
    
        $employee = Employee::create([
            ...$validated
        ]);
    
        return new EmployeeResource($employee);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Employee $employee)
    {
        if ($request->user()->cannot('view', $employee)) {
            abort(403);
        }
    
        return new EmployeeResource($employee);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        if ($request->user()->cannot('update', $employee)) {
            abort(403);
        }
    
        $employee->update($request->all());
    
        return new EmployeeResource($employee);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Employee $employee)
    {
        if ($request->user()->cannot('delete', $employee)) {
            abort(403);
        }
    
        $employee->delete();
    
        return response()->noContent();
    }
    
}
