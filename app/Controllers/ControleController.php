<?php

namespace App\Controllers;

use App\Models\Pagamento;
use App\Models\Atendimento;
use Fmk\MVC\Controller;
use Fmk\Utils\Router;
use App\Components\NotifyComponent;

class ControleController extends Controller
{
    public function __construct()
    {
        $this->middlewares('auth', 'admin');
    }

    public function index()
    {
        // 🔥 já carrega os pagamentos direto na home do controle
        return $this->mostrarPagamentos();
    }

    public function mostrarPagamentos()
    {
        try {
            // Buscar todos os pagamentos invertidos (do último para o primeiro)
            $pagamentos = Pagamento::query()->latest('id')->get() ?? [];

            $resumoDiario = [];

            // Adicionar atendimento e tipo de pagamento relacionados e calcular resumo
            foreach ($pagamentos as $pagamento) {
                $pagamento->atendimento = Atendimento::find($pagamento->atendimento_id);
                $pagamento->tipo        = \App\Models\PagamentoTipo::find($pagamento->pagamento_tipo_id);

                // Lógica do Resumo Diário
                $data = date('Y-m-d', strtotime($pagamento->criacao_data));
                if (!isset($resumoDiario[$data])) {
                    $resumoDiario[$data] = [
                        'valor_total' => 0,
                        'mesas' => []
                    ];
                }
                $resumoDiario[$data]['valor_total'] += $pagamento->valor;
                
                // Contabiliza mesas únicas atendidas no dia
                if ($pagamento->atendimento && !in_array($pagamento->atendimento->id, $resumoDiario[$data]['mesas'])) {
                    $resumoDiario[$data]['mesas'][] = $pagamento->atendimento->id;
                }
            }

            // 🔥 CORREÇÃO: usar a view correta e passar o resumo
            return view('controle.list', [
                'pagamentos' => $pagamentos,
                'resumoDiario' => $resumoDiario
            ], 'main');
        } catch (\Exception $e) {
            NotifyComponent::error("Erro ao buscar pagamentos: " . $e->getMessage());
            return Router::getRouteByName('home')->redirect();
        }
    }
}
