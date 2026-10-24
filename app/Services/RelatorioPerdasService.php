<?php

namespace App\Services;

use App\Repositories\RelatorioPerdasRepository;
use Carbon\CarbonImmutable;

class RelatorioPerdasService
{
    public function __construct(private RelatorioPerdasRepository $repository) {}

    public function gerar(string $periodo = 'mes'): array
    {
        $configuracoes = [
            'mes' => ['dias' => 30, 'agrupamento' => 7, 'titulo' => 'Último mês'],
            'trimestre' => ['dias' => 90, 'agrupamento' => 15, 'titulo' => 'Último trimestre'],
            'semestre' => ['dias' => 180, 'agrupamento' => 30, 'titulo' => 'Último semestre'],
            'ano' => ['dias' => 365, 'agrupamento' => 30, 'titulo' => 'Último ano'],
        ];

        $config = $configuracoes[$periodo] ?? $configuracoes['mes'];
        $fim = CarbonImmutable::today();
        $inicio = $fim->subDays($config['dias'] - 1);
        $tamanho = $config['agrupamento'];
        $barras = [];

        for ($i = 0; $i < $config['dias']; $i += $tamanho) {
            $de = $inicio->addDays($i);
            $ate = $inicio->addDays(min($config['dias'] - 1, $i + $tamanho - 1));
            $barras[] = [
                'inicio' => $de->toDateString(),
                'fim' => $ate->toDateString(),
                'rotulo' => $de->format('d/m') . '–' . $ate->format('d/m'),
                'pedidos' => 0,
                'entregues' => 0,
                'nao_retirados' => 0,
            ];
        }

        $totais = [
            'pedidos' => 0,
            'entregues' => 0,
            'nao_retirados' => 0,
            'frangos_nao_retirados' => 0,
        ];

        foreach ($this->repository->consultarPorPeriodo($inicio->toDateString(), $fim->toDateString()) as $linha) {
            $indice = intdiv((int) $inicio->diffInDays(CarbonImmutable::parse($linha->data)), $tamanho);
            // The report counts orders (not chickens) in all three chart series.
            $quantidadePedidos = (int) $linha->total_pedidos;
            $indice = (int) $indice;

            if (!isset($barras[$indice])) {
                continue;
            }

            $barras[$indice]['pedidos'] += $quantidadePedidos;
            $totais['pedidos'] += $quantidadePedidos;

            if ($linha->status === 'entregue') {
                $barras[$indice]['entregues'] += $quantidadePedidos;
                $totais['entregues'] += $quantidadePedidos;
            }

            if ($linha->status === 'nao_retirado') {
                $barras[$indice]['nao_retirados'] += $quantidadePedidos;
                $totais['nao_retirados'] += $quantidadePedidos;
                $totais['frangos_nao_retirados'] += (int) $linha->total_frangos;
            }
        }

        return [
            'periodo' => $periodo,
            'titulo' => $config['titulo'],
            'inicio' => $inicio->format('d/m/Y'),
            'fim' => $fim->format('d/m/Y'),
            'totais' => $totais,
            'grafico' => [
                'labels' => array_column($barras, 'rotulo'),
                'pedidos' => array_column($barras, 'pedidos'),
                'entregues' => array_column($barras, 'entregues'),
                'nao_retirados' => array_column($barras, 'nao_retirados'),
            ],
        ];
    }
}
