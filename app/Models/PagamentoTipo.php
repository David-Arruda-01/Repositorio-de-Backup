<?php

namespace App\Models;

use Fmk\MVC\Model;

class PagamentoTipo extends Model
{
    protected $table = 'pagamentos_tipos';

    protected $visible = [
        'id',
        'descricao'
    ];

    // ===========================
    // 🔗 RELACIONAMENTO INVERSO
    // ===========================

    public function pagamentos()
    {
        return $this->hasMany(Pagamento::class, 'pagamento_tipo_id');
    }
}
