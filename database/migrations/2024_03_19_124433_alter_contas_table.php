<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('contas', function (Blueprint $table) {
            $table->foreignId('situacao_conta_id')->default(2)->after('vencimento')->constrained('situacoes_contas');
            // onde esta after, queremos que este campo situacao_conta_id cria-se depois do campo vencimento e no constrained é dessa tabela que deve ser utilizado a chave primaria
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contas', function (Blueprint $table) {
            $table->dropColumn('situacao_conta_id');
        });
    }
};
