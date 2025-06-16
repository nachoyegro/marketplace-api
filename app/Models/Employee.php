<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    /** @use HasFactory<\Database\Factories\EmployeeFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name', 
        'last_name', 
        'birth_date', 
        'credits', 
        'user_id', 
        'company_id'
    ];

    /**
     * Get the user associated with the Employee.
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    /**
     * Get the company associated with the Employee.
     */
    public function company(): HasOne
    {
        return $this->hasOne(Company::class);
    }

    /**
     * Scope to filter employees by last name
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $last_name
     */
    public function scopeFilterByLastName($query, $last_name)
    {
        return $query->where('last_name', 'like', "%$last_name%");
    }

    /**
     * Scope to filter employees by company name
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $company
     */
    public function scopeFilterCompany($query, $company)
    {
        return $query->where('company', 'like', "%$company%");
    }
}
