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
        Schema::create('logs_exceptions', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 255);
            $table->text('mensagem');
            $table->text('arquivo')->nullable();
            $table->unsignedInteger('linha')->nullable();
            $table->string('metodo', 10)->nullable();
            $table->text('url')->nullable();
            $table->foreignId('id_usuario')->nullable()
                ->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs_exceptions');
    }
};
