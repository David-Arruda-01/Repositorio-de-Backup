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


    public function cadastrar()
    {
        $this->criacao_data   = $this->criacao_data ?? date('Y-m-d H:i:s');
        $this->alteracao_data = date('Y-m-d H:i:s');

        $id = (new Database('pagamentos'))->insert([
            'atendimento_id'     => $this->atendimento_id,
            'pagamento_tipo_id' => $this->pagamento_tipo_id,
            'valor'             => $this->valor,
            'observacao'        => $this->observacao,
            'criacao_data'      => $this->criacao_data,
            'alteracao_data'    => $this->alteracao_data,
            'exclusao_data'     => $this->exclusao_data
        ]);

        $this->id = $id;

        return $id;
    }
    /**
     * 🔗 Pagamento pertence a um atendimento
     */
    public function atendimento()
    {
        return $this->belongsTo(Atendimento::class, 'atendimento_id');
    }

    /**
     * 🔗 Pagamento pertence a um tipo de pagamento (opcional, se tiver essa tabela)
     */
    public function tipoPagamento()
    {
        return $this->belongsTo(PagamentoTipo::class, 'pagamento_tipo_id');
    }

    /**
     * 📊 Escopo: pagamentos de um atendimento
     */
    public function scopeDoAtendimento($query, $atendimentoId)
    {
        return $query->where('atendimento_id', $atendimentoId);
    }

    /**
     * 📊 Escopo: ordenar por data de criação
     */
    public function scopeOrdenado($query)
    {
        return $query->orderBy('criacao_data', 'desc');
    }

    /**
     * 💰 Formatar valor (Accessor)
     */
    public function getValorFormatadoAttribute()
    {
        return number_format($this->valor ?? 0, 2, ',', '.');
    }

    /**
     * 💳 Nome do tipo de pagamento (se tiver relacionamento)
     */
    public function getMetodoFormatadoAttribute()
    {
        return $this->tipoPagamento ? $this->tipoPagamento->nome : 'Não informado';
    }
}
