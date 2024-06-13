<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prestamo extends Model
{
    protected $fillable = [
       
        'cliente_id',
        'cantidad_prestamo',
        'fecha',
    ];
    

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}


