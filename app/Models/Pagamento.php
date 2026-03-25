<?php

namespace App\Models;


use Fmk\Databases\Query;
use Fmk\MVC\Model;

class Pagamento extends Model
{
    public function atendimento()
    {
        return $this->belongsTo(Atendimento::class, 'atendimentos_id');
    }

    public function pagamentos_tipos()
    {
        return $this->hasMany(pagamento::class, 'pagamentos_tipos_id');
    }
}
