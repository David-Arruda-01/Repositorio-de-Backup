<?php

namespace App\Models;

use Fmk\Database\Query;
use Fmk\MVC\Model;

class Pedido extends Model
{
    public function produtos()
    {
        return $this->belongsToMany(Produto::class, 'pedido')
                    ->withPivot('quantidade', 'valor_unitario');
    }
}
