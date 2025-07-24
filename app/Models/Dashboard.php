<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dashboard extends Model
{
    /** @use HasFactory<\Database\Factories\DashboardFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'iframe_link',
        'icon',
        'institution_id'
    ];

    public function institution()
    {
        return $this->belongsTo(Institution::class,'institution_id');
    }

    /**
     * Get the users that have explicit access to this dashboard.
     */
    public function permittedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'dashboard_user');
    }
}
