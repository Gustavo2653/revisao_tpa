<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProdutoRequest extends FormRequest
{
    /**
     * Quem pode cadastrar já é controlado pelos middlewares auth + vendedor.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * R5: regras da tabela de contratos.
     * vendedor_id NÃO entra aqui: é definido pelo sistema (R6).
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nome'           => ['required', 'string', 'max:255'],
            'categoria'      => ['required', Rule::in(['Eletrônicos', 'Vestuário', 'Casa', 'Alimentos'])],
            'garantia_meses' => ['required', 'integer', 'between:0,36'],
            'preco'          => ['required', 'numeric', 'gt:0'],
            'estoque'        => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'string'   => 'O campo :attribute deve ser um texto.',
            'integer'  => 'O campo :attribute deve ser um número inteiro.',
            'numeric'  => 'O campo :attribute deve ser um número.',
            'nome.max' => 'O campo :attribute deve ter no máximo :max caracteres.',
            'categoria.in'           => 'Escolha uma categoria válida: Eletrônicos, Vestuário, Casa ou Alimentos.',
            'garantia_meses.between' => 'A garantia deve ser entre :min e :max meses.',
            'preco.gt'    => 'O preço deve ser maior que zero.',
            'estoque.min' => 'O estoque não pode ser negativo.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nome'           => 'nome',
            'categoria'      => 'categoria',
            'garantia_meses' => 'garantia (meses)',
            'preco'          => 'preço',
            'estoque'        => 'estoque',
        ];
    }
}
