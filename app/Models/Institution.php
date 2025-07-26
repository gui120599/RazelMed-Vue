<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Institution extends Model
{
    use HasFactory, SoftDeletes; // Usar o trait SoftDeletes

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'institutions'; // Definir explicitamente o nome da tabela

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'cnpj',
        'address',
        'profile_photo_path', // Adicionar o novo campo
    ];

    public function dashboard()
    {
        return $this->hasMany(Dashboard::class);
    }
}
