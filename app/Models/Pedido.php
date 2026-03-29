<?php

namespace App\Models;

use Fmk\Databases\Query;
use Fmk\MVC\Model;


class Pedido extends Model
{


    public function produto()
    {
        return $this->belongsTo(Produto::class, 'produto_id');
    }
}
