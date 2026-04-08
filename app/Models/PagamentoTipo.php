<?php

namespace App\Models;

use Fmk\MVC\Model;

class PagamentoTipo extends Model
{
    public function pagamentos()
    {
        return $this->hasMany(Pagamento::class, 'pagamento_tipo_id');
    }
}
