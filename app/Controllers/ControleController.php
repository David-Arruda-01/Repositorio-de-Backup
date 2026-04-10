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
        $this->middlewares('auth');
    }

    public function index()
    {
        // 🔥 já carrega os pagamentos direto na home do controle
        return $this->mostrarPagamentos();
    }

    public function mostrarPagamentos()
    {
        try {
            // Buscar todos os pagamentos
            $pagamentos = Pagamento::all() ?? [];

            // Adicionar atendimento e tipo de pagamento relacionados
            foreach ($pagamentos as $pagamento) {
                $pagamento->atendimento = Atendimento::find($pagamento->atendimento_id);
                $pagamento->tipo        = \App\Models\PagamentoTipo::find($pagamento->pagamento_tipo_id);
            }

            // 🔥 CORREÇÃO: usar a view correta
            return view('controle.list', [
                'pagamentos' => $pagamentos
            ], 'main');
        } catch (\Exception $e) {
            NotifyComponent::error("Erro ao buscar pagamentos: " . $e->getMessage());
            return Router::getRouteByName('home')->redirect();
        }
    }
}
