<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Venda extends Model
{
    protected $fillable = ['cliente_id'];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    // R7: N:M com Produto pela tabela pivô produto_venda
    public function produtos(): BelongsToMany
    {
        return $this->belongsToMany(Produto::class)
            ->withPivot('quantidade', 'preco_unitario')
            ->withTimestamps();
    }
}
