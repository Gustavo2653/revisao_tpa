<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Produto extends Model
{
    protected $fillable = [
        'nome',
        'categoria',
        'garantia_meses',
        'preco',
        'estoque',
        'vendedor_id',
    ];

    protected function casts(): array
    {
        return [
            'garantia_meses' => 'integer',
            'preco' => 'decimal:2',
            'estoque' => 'integer',
        ];
    }

    // R1: cada Produto pertence a um vendedor (users.id via vendedor_id)
    public function vendedor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendedor_id');
    }

    // Lado inverso do N:M com Venda (tabela pivô produto_venda)
    public function vendas(): BelongsToMany
    {
        return $this->belongsToMany(Venda::class)
            ->withPivot('quantidade', 'preco_unitario')
            ->withTimestamps();
    }
}
