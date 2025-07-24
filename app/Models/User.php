<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'prefer_institution_id',
        'is_super_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Retorna a instituição preferencial (contexto de visualização atual)
    public function preferredInstitution()
    {
        return $this->belongsTo(Institution::class, 'prefer_institution_id');
    }

    /**
     * Get the institutions the user has access to. (Many-to-Many relationship)
     */
    public function institutions(): BelongsToMany
    {
        return $this->belongsToMany(Institution::class, 'institution_user');
    }

    /**
     * Get the dashboards the user has explicit access to. (Many-to-Many relationship with dashboards)
     */
    public function accessibleDashboards(): BelongsToMany
    {
        return $this->belongsToMany(Dashboard::class, 'dashboard_user');
    }


}
