<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProdutoRequest;
use App\Models\Produto;
use Illuminate\Support\Facades\Auth;

class ProdutoController extends Controller
{
    // R1: listagem pública, carregando o vendedor com with() (evita N+1)
    public function index()
    {
        $produtos = Produto::with('vendedor')->get();

        return view('produtos.index', compact('produtos'));
    }

    public function create()
    {
        return view('produtos.create');
    }

    // R5: valida com StoreProdutoRequest | R6: vendedor_id vem do usuário logado
    public function store(StoreProdutoRequest $request)
    {
        $dados = $request->validated();
        $dados['vendedor_id'] = Auth::id();

        Produto::create($dados);

        return redirect()->route('produtos.index')
            ->with('sucesso', 'Produto cadastrado!');
    }
}
