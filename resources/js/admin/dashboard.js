// resources/js/admin/dashboard.js

document.addEventListener('DOMContentLoaded', () => {
    // 1. Line Chart: Rekap Ticket
    const ctxRekap = document.getElementById('rekapTicketChart');
    if (ctxRekap) {
        const rekapData = window.rekapTicketData || Array(12).fill(0);

        new Chart(ctxRekap.getContext('2d'), {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    data: rekapData,
                    borderColor: '#052e23',
                    borderWidth: 2,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#052e23',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    tension: 0.3,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0, color: '#94a3b8' },
                        grid: { borderDash: [4, 4], color: '#f1f5f9' }
                    },
                    x: {
                        ticks: { color: '#94a3b8' },
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // 2. Doughnut Chart: Prioritas Ticket
    const ctxPrioritas = document.getElementById('prioritasChart');
    if (ctxPrioritas) {
        const prioritasValues = window.prioritasValuesData || [0, 0, 0, 0];

        new Chart(ctxPrioritas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Urgent', 'High', 'Medium', 'Low'],
                datasets: [{
                    data: prioritasValues,
                    backgroundColor: ['#f43f5e', '#f59e0b', '#3b82f6', '#10b981'],
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: { legend: { display: false } }
            }
        });
    }
});