// Variável global para controlar se já foi inicializado
let dashboardInitialized = false;
let chartInstances = {};

document.addEventListener('DOMContentLoaded', function () {
    if (dashboardInitialized) {
        console.log('Dashboard já foi inicializado, ignorando...');
        return;
    }
    
    console.log('Dashboard Associados - Iniciando carregamento');
    dashboardInitialized = true;
    
    // Aguardar um pouco para garantir que o dashboardData foi localizado
    setTimeout(function() {
        if (typeof dashboardData === 'undefined' || !dashboardData.apiUrl) {
            console.error('dashboardData não está definido ou apiUrl está faltando');
            console.log('Tentando usar URL padrão...');
            
            // Fallback para URL padrão
            const apiUrl = '/wp-json/associados/v1/stats';
            loadDashboardData(apiUrl, '');
            return;
        }
        
        console.log('API URL:', dashboardData.apiUrl);
        loadDashboardData(dashboardData.apiUrl, dashboardData.nonce);
    }, 100);
});

function loadDashboardData(apiUrl, nonce) {
    showLoadingInCards();

    const headers = {
        'Content-Type': 'application/json'
    };
    
    if (nonce) {
        headers['X-WP-Nonce'] = nonce;
    }

    fetch(apiUrl, {
        method: 'GET',
        headers: headers
    })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error('Erro na resposta da API: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('Dados recebidos:', data);
            if (data && typeof data === 'object') {
                updateCards(data);
                createCharts(data);
            } else {
                throw new Error('Dados inválidos recebidos da API');
            }
        })
        .catch(error => {
            console.error('Erro ao carregar dados:', error);
            showErrorMessage();
        });
}

function showLoadingInCards() {
    const cards = ['total-associados', 'novos-associados-mes', 'associados-ativos', 'com-medico-prescritor'];
    cards.forEach(cardId => {
        const element = document.getElementById(cardId);
        if (element) {
            element.textContent = '...';
        }
    });
}

function updateCards(data) {
    console.log('Atualizando cards com dados:', data);
    
    const totalElement = document.getElementById('total-associados');
    const novosElement = document.getElementById('novos-associados-mes');
    const ativosElement = document.getElementById('associados-ativos');
    const medicoElement = document.getElementById('com-medico-prescritor');
    
    // Atualizar Total de Associados
    if (totalElement) {
        const total = data.total || 0;
        totalElement.textContent = total;
        console.log('Total associados:', total);
    }
    
    // Atualizar Novos este Mês
    if (novosElement) {
        const novos = data.novos_mes || 0;
        novosElement.textContent = novos;
        console.log('Novos associados:', novos);
    }
    
    // Atualizar Associados Ativos
    if (ativosElement) {
        const ativos = data.ativos || 0;
        ativosElement.textContent = ativos;
        console.log('Associados ativos:', ativos);
    }
    
    // Atualizar Com Médico Prescritor
    if (medicoElement) {
        let comMedico = 0;
        if (data.com_medico_prescritor && data.com_medico_prescritor.data && data.com_medico_prescritor.data.length > 0) {
            comMedico = data.com_medico_prescritor.data[0];
        }
        medicoElement.textContent = comMedico;
        console.log('Com médico prescritor:', comMedico);
    }
    
    console.log('Cards atualizados com sucesso');
}

function destroyExistingCharts() {
    // Destruir gráficos existentes se houver
    Object.keys(chartInstances).forEach(key => {
        if (chartInstances[key]) {
            chartInstances[key].destroy();
            delete chartInstances[key];
        }
    });
}

function createCharts(data) {
    // Destruir gráficos existentes primeiro
    destroyExistingCharts();
    
    // Gráfico de Crescimento de Associados
    if (data.crescimento) createGrowthChart(data.crescimento);

    // Gráfico de Associados por Estado
    if (data.por_estado) createStateChart(data.por_estado);

    // Gráfico de Tipos de Associação
    if (data.por_tipo) createTypeChart(data.por_tipo);

    // Gráfico de Distribuição por Gênero
    if (data.por_genero) createGenderChart(data.por_genero);

    // Gráfico de Plano de Saúde
    if (data.com_plano_saude) createHealthInsuranceChart(data.com_plano_saude);

    // Gráfico de Associados Ativos por Estado (PRINCIPAL)
    if (data.ativos_por_estado) createActiveStateChart(data.ativos_por_estado);

    // Gráfico de Histórico de Uso de Cannabis
    if (data.historico_uso_cannabis) createCannabisHistoryChart(data.historico_uso_cannabis);
}

function createGrowthChart(data) {
    const ctx = document.getElementById('crescimentoAssociadosChart')?.getContext('2d');
    if (!ctx || !data || !data.labels || !data.data) return;

    chartInstances.crescimento = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Novos Associados',
                data: data.data,
                fill: true,
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                borderColor: 'rgba(34, 197, 94, 1)',
                borderWidth: 3,
                tension: 0.4,
                pointBackgroundColor: 'rgba(34, 197, 94, 1)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    cornerRadius: 8
                }
            }
        }
    });
}

function createStateChart(data) {
    const ctx = document.getElementById('associadosPorEstadoChart')?.getContext('2d');
    if (!ctx || !data || !data.labels || !data.data) return;

    chartInstances.estado = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: data.labels,
            datasets: [{
                data: data.data,
                backgroundColor: [
                    '#3B82F6', '#EF4444', '#10B981', '#F59E0B',
                    '#8B5CF6', '#EC4899', '#06B6D4', '#84CC16'
                ],
                borderWidth: 0,
                hoverBorderWidth: 2,
                hoverBorderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    cornerRadius: 8
                }
            }
        }
    });
}

function createTypeChart(data) {
    const ctx = document.getElementById('associadosPorTipoChart')?.getContext('2d');
    if (!ctx || !data || !data.labels || !data.data) return;

    chartInstances.tipo = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: [{
                data: data.data,
                backgroundColor: [
                    'rgba(139, 92, 246, 0.8)',
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(245, 158, 11, 0.8)'
                ],
                borderColor: [
                    'rgba(139, 92, 246, 1)',
                    'rgba(59, 130, 246, 1)',
                    'rgba(16, 185, 129, 1)',
                    'rgba(245, 158, 11, 1)'
                ],
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    cornerRadius: 8
                }
            }
        }
    });
}

function createGenderChart(data) {
    const ctx = document.getElementById('associadosPorGeneroChart')?.getContext('2d');
    if (!ctx || !data || !data.labels || !data.data) return;

    chartInstances.genero = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: data.labels,
            datasets: [{
                data: data.data,
                backgroundColor: [
                    '#EC4899', '#3B82F6', '#10B981', '#6B7280'
                ],
                borderWidth: 0,
                hoverBorderWidth: 2,
                hoverBorderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    cornerRadius: 8
                }
            }
        }
    });
}

function createHealthInsuranceChart(data) {
    const ctx = document.getElementById('planoSaudeChart')?.getContext('2d');
    if (!ctx || !data || !data.labels || !data.data) return;

    chartInstances.planoSaude = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: data.labels,
            datasets: [{
                data: data.data,
                backgroundColor: ['#14B8A6', '#EF4444'],
                borderWidth: 0,
                hoverBorderWidth: 2,
                hoverBorderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    cornerRadius: 8,
                    callbacks: {
                        label: function (context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return `${context.label}: ${context.parsed} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}

function createActiveStateChart(data) {
    const ctx = document.getElementById('ativosEstadoChart')?.getContext('2d');
    if (!ctx || !data || !data.labels || !data.data) return;

    // Cores mais vibrantes e diferenciadas para cada estado
    const colors = [
        'rgba(34, 197, 94, 0.8)',   // Verde
        'rgba(59, 130, 246, 0.8)',  // Azul
        'rgba(239, 68, 68, 0.8)',   // Vermelho
        'rgba(245, 158, 11, 0.8)',  // Laranja
        'rgba(139, 92, 246, 0.8)',  // Roxo
        'rgba(236, 72, 153, 0.8)',  // Rosa
        'rgba(6, 182, 212, 0.8)',   // Ciano
        'rgba(132, 204, 22, 0.8)'   // Lima
    ];

    const borderColors = [
        'rgba(34, 197, 94, 1)',
        'rgba(59, 130, 246, 1)',
        'rgba(239, 68, 68, 1)',
        'rgba(245, 158, 11, 1)',
        'rgba(139, 92, 246, 1)',
        'rgba(236, 72, 153, 1)',
        'rgba(6, 182, 212, 1)',
        'rgba(132, 204, 22, 1)'
    ];

    chartInstances.ativosEstado = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Associados Ativos',
                data: data.data,
                backgroundColor: colors.slice(0, data.labels.length),
                borderColor: borderColors.slice(0, data.labels.length),
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        stepSize: 1,
                        font: {
                            size: 12
                        }
                    },
                    title: {
                        display: true,
                        text: 'Número de Associados Ativos',
                        font: {
                            size: 14,
                            weight: 'bold'
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 11
                        },
                        maxRotation: 45
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    cornerRadius: 8,
                    callbacks: {
                        label: function (context) {
                            const total = data.total_ativos || context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed.y / total) * 100).toFixed(1);
                            return `${context.label}: ${context.parsed.y} associados ativos (${percentage}%)`;
                        },
                        afterLabel: function(context) {
                            return `Total de ativos: ${data.total_ativos || 'N/A'}`;
                        }
                    }
                }
            }
        }
    });
}

function createCannabisHistoryChart(data) {
    const ctx = document.getElementById('cannabisHistoryChart')?.getContext('2d');
    if (!ctx || !data || !data.labels || !data.data) return;

    chartInstances.cannabis = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: data.labels,
            datasets: [{
                data: data.data,
                backgroundColor: [
                    '#22C55E', // Verde para "Já Usou"
                    '#EF4444', // Vermelho para "Nunca Usou"
                    '#6B7280'  // Cinza para "Não Informado"
                ],
                borderWidth: 0,
                hoverBorderWidth: 2,
                hoverBorderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    cornerRadius: 8,
                    callbacks: {
                        label: function (context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return `${context.label}: ${context.parsed} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}

function showErrorMessage() {
    console.error('Erro ao carregar dados do dashboard');
    
    const cards = ['total-associados', 'novos-associados-mes', 'associados-ativos', 'com-medico-prescritor'];
    cards.forEach(cardId => {
        const element = document.getElementById(cardId);
        if (element) {
            element.textContent = 'Erro';
            element.style.color = '#ef4444';
        }
    });
    
    // Mostrar mensagem de erro mais detalhada
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.innerHTML = 'Erro ao carregar dados. Verifique se as funções estão carregadas corretamente.';
    
    const container = document.querySelector('.uk-container');
    if (container) {
        container.insertBefore(errorDiv, container.firstChild);
    }
}