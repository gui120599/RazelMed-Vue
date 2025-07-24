<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Institution extends Model
{
    /** @use HasFactory<\Database\Factories\InstitutionFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'cnpj',
        'profile_photo_path',
    ];


    public function dashboards()
    {
        return $this->hasMany(Dashboard::class);
    }

    /**
     * Get the users associated with this institution. (Many-to-Many relationship)
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'institution_user');
    }
}
