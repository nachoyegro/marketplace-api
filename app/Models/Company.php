<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;

class Company extends Model
{
    /** @use HasFactory<\Database\Factories\CompanyFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name', 'billing_address'];


    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Scope to filter companies by name
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $name
     */
    public function scopeFilterByName($query, $name)
    {
        return $query->where('name', 'like', "%$name%");
    }

}
