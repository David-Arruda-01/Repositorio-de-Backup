<?php

namespace App\Models;

use Fmk\MVC\Model;

class PagamentoTipo extends Model
{
    protected $table = 'pagamentos_tipos';

    public function pagamentos()
    {
        return $this->hasMany(Pagamento::class, 'pagamento_tipo_id');
    }
}
