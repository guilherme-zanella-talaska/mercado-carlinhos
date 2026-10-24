<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL/MariaDB: permite status novos antes de converter valores existentes.
        DB::statement("ALTER TABLE encomendas MODIFY status ENUM('pendente','preparado','concluido','em_andamento','entregue','nao_retirado','cancelado') NOT NULL DEFAULT 'em_andamento'");
        DB::table('encomendas')->whereIn('status', ['pendente', 'preparado'])->update(['status' => 'em_andamento']);
        DB::table('encomendas')->where('status', 'concluido')->update(['status' => 'entregue']);
        DB::statement("ALTER TABLE encomendas MODIFY status ENUM('em_andamento','entregue','nao_retirado','cancelado') NOT NULL DEFAULT 'em_andamento'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE encomendas MODIFY status ENUM('pendente','preparado','concluido','em_andamento','entregue','nao_retirado','cancelado') NOT NULL DEFAULT 'pendente'");
        DB::table('encomendas')->where('status', 'em_andamento')->update(['status' => 'pendente']);
        DB::table('encomendas')->where('status', 'entregue')->update(['status' => 'concluido']);
        DB::table('encomendas')->where('status', 'nao_retirado')->update(['status' => 'cancelado']);
        DB::statement("ALTER TABLE encomendas MODIFY status ENUM('pendente','preparado','concluido','cancelado') NOT NULL DEFAULT 'pendente'");
    }
};
