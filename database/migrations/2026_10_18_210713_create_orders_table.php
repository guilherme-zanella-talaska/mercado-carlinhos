<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('encomendas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_cliente')
                ->constrained('clientes')
                ->restrictOnDelete();

            $table->unsignedInteger('quantidade');

            $table->date('data_entrega');
            $table->time('horario_entrega');

            $table->enum('tipo_entrega', ['retirada', 'entrega'])
                ->default('retirada');

            $table->string('endereco')->nullable();

            $table->enum('status', [
                'pendente',
                'preparado',
                'concluido',
                'cancelado'
            ])->default('pendente');

            $table->text('observacoes')->nullable();

            $table->timestamps();

            $table->index(['data_entrega', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('encomendas');
    }
};
