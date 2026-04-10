<?php

namespace App\Models;

use Fmk\MVC\Model;
use App\Db\Database;

class Pagamento extends Model
{
    protected $visible = [
        'id',
        'atendimento_id',
        'pagamento_tipo_id',
        'valor',
        'observacao',
        'criacao_data',
        'alteracao_data',
        'exclusao_data'
    ];

    // ===========================
    // 💾 CADASTRAR
    // ===========================

    public function cadastrar()
    {
        $this->criacao_data   = $this->criacao_data ?? date('Y-m-d H:i:s');
        $this->alteracao_data = date('Y-m-d H:i:s');

        $id = (new Database('pagamentos'))->insert([
            'atendimento_id'     => $this->atendimento_id,
            'pagamento_tipo_id'  => $this->pagamento_tipo_id,
            'valor'              => $this->valor,
            'observacao'         => $this->observacao,
            'criacao_data'       => $this->criacao_data,
            'alteracao_data'     => $this->alteracao_data,
            'exclusao_data'      => $this->exclusao_data
        ]);

        $this->id = $id;

        return $id;
    }

    // ===========================
    // 🔗 RELACIONAMENTOS
    // ===========================

    public function atendimento()
    {
        return $this->belongsTo(Atendimento::class, 'atendimento_id');
    }

    public function tipo()
    {
        return $this->belongsTo(PagamentoTipo::class, 'pagamento_tipo_id');
    }

    // ===========================
    // 📊 SCOPES
    // ===========================

    public function scopeDoAtendimento($query, $atendimentoId)
    {
        return $query->where('atendimento_id', $atendimentoId);
    }

    public function scopeOrdenado($query)
    {
        return $query->orderBy('criacao_data', 'desc');
    }

    // ===========================
    // 💰 ACCESSORS
    // ===========================

    public function getValorFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->valor ?? 0, 2, ',', '.');
    }

    public function getTipoFormatadoAttribute()
    {
        return $this->tipo->descricao ?? 'Não informado';
    }
    public function with()
    {
        return $this;
    }
}
