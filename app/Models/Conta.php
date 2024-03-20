<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conta extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'contas';

    protected $fillable = ['nome', 'valor', 'vencimento', 'situacao_conta_id'];

    // tabela filha utiliza o belognsTo
    public function situacaoConta()
    {
        return $this->belongsTo(SituacaoConta::class);
    }
}
