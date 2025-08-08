<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'surname',
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
    public function isSuperAdmin()
    {
        // Adapte esta lógica para o seu sistema de permissões.
        // Abaixo estão algumas opções comuns:

        // Opção 1: Baseado em um campo na tabela 'users' (ex: 'is_admin')
        return $this->is_super_admin;

        // Opção 2: Baseado em um e-mail específico (bom para testes e ambientes pequenos)
        //return $this->email === 'admin@example.com';

        // Opção 3: Baseado em um relacionamento com papéis (se você usa um pacote como Spatie)
        // return $this->hasRole('super-admin');

        // Opção 4: Se você tem um campo 'role' (string)
        // return $this->role === 'super-admin';
    }
    /**
     * A instituição preferencial do usuário (se houver).
     */
    public function preferredInstitution()
    {
        return $this->belongsTo(Institution::class, 'prefer_institution_id');
    }

    // --- NOVOS RELACIONAMENTOS ---

    /**
     * As instituições às quais o usuário tem acesso.
     */
    public function institutions()
    {
        // O segundo parâmetro é o nome da tabela pivot (institution_user)
        // O terceiro é a chave estrangeira do modelo atual (user_id) na tabela pivot
        // O quarto é a chave estrangeira do modelo relacionado (institution_id) na tabela pivot
        return $this->belongsToMany(Institution::class, 'institution_users', 'user_id', 'institution_id');
    }

    /**
     * Os dashboards aos quais o usuário tem acesso.
     */
    public function dashboards()
    {
        // O segundo parâmetro é o nome da tabela pivot (dashboard_user)
        // O terceiro é a chave estrangeira do modelo atual (user_id) na tabela pivot
        // O quarto é a chave estrangeira do modelo relacionado (dashboard_id) na tabela pivot
        return $this->belongsToMany(Dashboard::class, 'dashboard_users', 'user_id', 'dashboard_id');
    }
}
