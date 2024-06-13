<?php

// app/Models/Cliente.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
        'email', // Usa 'email' en lugar de 'correo'
    ];

    public function prestamos()
    {
        return $this->hasMany(Prestamo::class);
    }
}
