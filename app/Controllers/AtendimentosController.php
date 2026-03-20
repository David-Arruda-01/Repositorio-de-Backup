<?php
namespace App\Controllers;

use App\Components\NotifyComponent;
use App\Models\Atendimento;
use App\Models\Pagamento;
use App\Models\Produto;
use Fmk\MVC\Controller;
use App\Models\Pedido;
use Fmk\Utils\Request;
use Fmk\Utils\Router;

class AtendimentosController extends Controller
{
    public function __construct()
    {
        $this->middlewares('auth');
    }

    public function index()
    {
        $atendimentos = Atendimento::all();
        return view('atendimentos.list', [
            'atendimentos' => $atendimentos,
            'atendimento'  => $atendimentos
        ], 'main');
    }

    public function create()
    {
        try {
            $pedidos = Pedido::all();
            return view('atendimentos.card-list-pedidos', [
                'pedidos' => $pedidos
            ], 'main');
        } catch (\Exception $e) {
            return NotifyComponent::error('Erro ao carregar a página de criação de atendimento: ') . $e->getMessage();
        }
    }

    public function adicionarProduto(Request $request, $atendimento_id)
    {
        $request->validate([
            'produto_id' => 'required|exists:produtos,id',
            'quantidade' => 'required|integer|min:1',
        ]);

        $produto = Produto::find($request->produto_id);

        Pedido::create([
            'atendimento_id' => $atendimento_id,
            'produto_id'    => $request->produto_id,
            'quantidade'    => $request->quantidade,
            'valor_un'      => $produto->preco,
        ]);

        NotifyComponent::success('Produto adicionado com sucesso!');

        return (route('mesa.atendimento', ['id' => $atendimento_id]))->redirect();
    }

    public function update($atendimento_id)
    {
        $request = Request::getInstance();
        $atendimento_id = $request->validate('atendimento_id')->required()->isInt()->dbExists(Atendimento::class);

        if (!$request->validation()) {
            NotifyComponent::error("Existem erros de preenchimento no formulário.");
            return $request->old()->redirect();
        }

        $produto_id = $request->validate(name: 'produto_id', label: 'Produto')->required();
        $valor_un   = $request->validate(name: 'valor_un', label: 'Valor Unitário')->isFloat()->required();
        $quantidade = $request->validate(name: 'quantidade', label: 'Quantidade')->required();
        
        $data    = compact('atendimento_id', 'produto_id', 'valor_un', 'quantidade');
        $produto = Pedido::find($atendimento_id);
        
        if (!$produto) {
            NotifyComponent::error("Pedido não encontrado.");
            return $request->old()->redirect();
        }

        $produto->update($data);
        NotifyComponent::success("Pedido atualizado com sucesso!");

        return (route('mesa.atendimento', ['id' => $atendimento_id]))->redirect();
    }

    public function storage()
    {
        $request = Request::getInstance();
        $atendimento_id = $request->validate(name: 'atendimento_id', label: 'Id do atendimento')->required();
        $produto_id     = $request->validate(name: 'produto_id', label: 'Produto')->required();
        $valor_un       = $request->validate(name: 'valor_un', label: 'Valor Unitário')->isFloat()->required();
        $quantidade     = $request->validate(name: 'quantidade', label: 'Quantidade')->required();

        if (!$request->validation()) {
            NotifyComponent::error(msg: "Existem erros de preenchimento no formulário.");
            return $request->old()->redirect();
        }
       
        $data = compact('atendimento_id','valor_un','produto_id','quantidade');
        Pedido::create($data);

        NotifyComponent::success("Pedido cadastrado com sucesso!");

        return (route('mesa.atendimento', ['id' => $atendimento_id]))->redirect();
    }

    public function atendimentoId($mesa_id)
    {
        $nMesas = $_SESSION['N_MESAS'] ?? constant('N_MESAS');

        if ($mesa_id < 1 || $mesa_id > $nMesas) {
            NotifyComponent::error("Número de mesa inválido.");
            return Router::getRouteByName('home')->redirect();
        }

        $atendimento = Atendimento::where('mesa', $mesa_id)
            ->where('pagamento_data', 'is', null)
            ->first();

        if (!$atendimento) {
            $atendimento = Atendimento::create([
                'mesa' => $mesa_id
            ]);
        }

        // Busca a view correta para exibir o atendimento
        return view('atendimentos.list', ['atendimento' => $atendimento], 'main');
    }

    public function finalizarAtendimento($id)
    {
        $atendimento = Atendimento::find($id);
        
        if (!$atendimento) {
            NotifyComponent::error("Atendimento não encontrado.");
            return Router::getRouteByName('home')->redirect();
        }

        $atendimento->update([
            'pagamento_data' => date('Y-m-d H:i:s')
        ]);

        NotifyComponent::success("Atendimento da mesa {$atendimento->mesa} finalizado com sucesso!");
        return Router::getRouteByName('home')->redirect();
    }
}
