<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dashboard extends Model
{
    /** @use HasFactory<\Database\Factories\DashboardFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'dashboards';

    protected $fillable = [
        'name',
        'iframe_link',
        'institution_id',
        'icon',
    ];

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    /**
     * Os usuários que têm acesso a este dashboard.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'dashboard_users', 'dashboard_id', 'user_id');
    }
}
