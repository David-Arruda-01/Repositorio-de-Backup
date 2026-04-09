<?php

namespace App\Models;

use Fmk\MVC\Model;
use App\Db\Database;

class Pagamento extends Model
{
    protected $fillable = [
        'atendimento_id',
        'pagamento_tipo_id',
        'valor',
        'observacao',
    ];

    protected $visible = [
        'id',
        'atendimento_id',
        'pagamento_tipo_id',
        'valor',
        'observacao',
        'criacao_data',
        'alteracao_data',
        'exclusao_data',
    ];

    public function atendimento()
    {
        return $this->belongsTo(Atendimento::class, 'atendimento_id');
    }

    public function tipo()
    {
        return $this->belongsTo(PagamentoTipo::class, 'pagamento_tipo_id');
    }

    public function scopeDoAtendimento($query, $atendimentoId)
    {
        return $query->where('atendimento_id', $atendimentoId);
    }

    public function scopeOrdenado($query)
    {
        return $query->orderBy('criacao_data', 'desc');
    }

    public function getValorFormatadoAttribute()
    {
        return number_format($this->valor ?? 0, 2, ',', '.');
    }

    public function getMetodoFormatadoAttribute()
    {
        return $this->tipo->descricao ?? '';
    }
}
