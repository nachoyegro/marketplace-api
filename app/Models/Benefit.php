<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Benefit extends Model
{
    /** @use HasFactory<\Database\Factories\BenefitFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name', 'description', 'country_code'];


    /**
     * Get the variations of the Benefit.
     */
    public function variations(): HasMany
    {
        return $this->hasMany(Variation::class);
    }

}
