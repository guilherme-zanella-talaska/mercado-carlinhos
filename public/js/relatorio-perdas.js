document.addEventListener('DOMContentLoaded', () => {
    const grafico = document.getElementById('graficoPerdas');
    const dados = document.getElementById('relatorioPerdasDados');
    if (!grafico || !dados) return;

    if (typeof Chart === 'undefined') {
        grafico.replaceWith(document.createTextNode('Não foi possível carregar o gráfico. Verifique a conexão com a internet.'));
        return;
    }

    let valores;
    try {
        valores = JSON.parse(dados.dataset.grafico || '{}');
    } catch {
        return;
    }

    new Chart(grafico, {
        type: 'bar',
        data: {
            labels: valores.labels || [],
            datasets: [
                { label: 'Solicitados', data: valores.pedidos || [], backgroundColor: '#A43D47', borderRadius: 5, maxBarThickness: 34 },
                { label: 'Entregues', data: valores.entregues || [], backgroundColor: '#218568', borderRadius: 5, maxBarThickness: 34 },
                { label: 'Não retirados', data: valores.nao_retirados || [], backgroundColor: '#B58127', borderRadius: 5, maxBarThickness: 34 },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20, font: { family: 'DM Sans', size: 12 } } },
                tooltip: { callbacks: { label: item => `${item.dataset.label}: ${item.parsed.y} ${item.parsed.y === 1 ? 'pedido' : 'pedidos'}` } },
            },
            scales: {
                x: { grid: { display: false }, ticks: { maxRotation: 45, font: { size: 11 } } },
                y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#ECEEF2' } },
            },
        },
    });
});
