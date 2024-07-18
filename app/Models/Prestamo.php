<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Prestamo extends Model
{
    protected $fillable = [
       
        'cliente_id',
        'cantidad_prestamo',
        'fecha',
        'monto',
    ];
    

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
    
    public function abonos()
    {
        return $this->hasMany(Abono::class);
    }


}


