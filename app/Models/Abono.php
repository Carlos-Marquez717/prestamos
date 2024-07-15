<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Abono extends Model
{
    use HasFactory;

    protected $fillable = [
        'prestamo_id',
        'monto',
        'fecha',
    ];

    protected $dates = [
        'fecha',
    ];
    
    public function prestamo()
    {
        return $this->belongsTo(Prestamo::class);
    }
    
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
