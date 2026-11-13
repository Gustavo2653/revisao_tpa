<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Venda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CompraController extends Controller
{
    // R8: só as vendas do usuário logado, com os produtos (e a pivô) carregados
    public function index()
    {
        $vendas = Venda::with('produtos')
            ->where('cliente_id', Auth::id())
            ->latest()
            ->get();

        return view('compras.index', compact('vendas'));
    }

    public function create()
    {
        $produtos = Produto::orderBy('nome')->get();

        return view('compras.create', compact('produtos'));
    }

    // R7: cria a venda e associa os produtos pela pivô | Bônus: controla o estoque
    public function store(Request $request)
    {
        $request->validate([
            'itens'   => ['required', 'array'],
            'itens.*' => ['integer', 'min:0'],
        ], [
            'itens.required'  => 'Escolha pelo menos um produto.',
            'itens.*.integer' => 'As quantidades devem ser números inteiros.',
            'itens.*.min'     => 'As quantidades não podem ser negativas.',
        ]);

        // 1. ignora os itens com quantidade 0  ->  [produto_id => quantidade]
        $itens = collect($request->input('itens'))
            ->map(fn ($quantidade) => (int) $quantidade)
            ->filter(fn ($quantidade) => $quantidade > 0);

        if ($itens->isEmpty()) {
            return back()->withErrors(['itens' => 'Escolha pelo menos um produto.'])->withInput();
        }

        $produtos = Produto::whereIn('id', $itens->keys())->get()->keyBy('id');

        if ($produtos->count() !== $itens->count()) {
            return back()->withErrors(['itens' => 'Algum produto escolhido não existe mais.'])->withInput();
        }

        // Bônus: recusa a compra se alguma quantidade passar do estoque
        foreach ($itens as $produtoId => $quantidade) {
            $produto = $produtos[$produtoId];

            if ($quantidade > $produto->estoque) {
                return back()
                    ->withErrors(['itens' => "Estoque insuficiente para \"{$produto->nome}\": pedido {$quantidade}, disponível {$produto->estoque}."])
                    ->withInput();
            }
        }

        // Tudo ou nada: se algo falhar no meio, nenhuma venda fica pela metade
        DB::transaction(function () use ($itens, $produtos) {
            // 2. venda do usuário logado
            $venda = Venda::create(['cliente_id' => Auth::id()]);

            foreach ($itens as $produtoId => $quantidade) {
                $produto = $produtos[$produtoId];

                // 3. attach com quantidade e preço atual do produto
                $venda->produtos()->attach($produto->id, [
                    'quantidade'     => $quantidade,
                    'preco_unitario' => $produto->preco,
                ]);

                // Bônus: baixa o estoque
                $produto->decrement('estoque', $quantidade);
            }
        });

        // 4. redireciona para Minhas compras
        return redirect()->route('compras.index')->with('sucesso', 'Compra realizada!');
    }
}
