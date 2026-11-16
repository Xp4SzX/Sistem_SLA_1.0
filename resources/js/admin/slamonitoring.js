// resources/js/admin/slamonitoring.js

document.addEventListener("DOMContentLoaded", function() {
    const kpiData = window.kpiData || {};
    const monthlyData = window.monthlyData || Array(12).fill(0);

    // 1. Bar Chart: Tren Volume Tiket
    const ctxBar = document.getElementById('slaBarChart');
    if (ctxBar) {
        new Chart(ctxBar.getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [{
                    data: monthlyData,
                    backgroundColor: '#052e23',
                    borderRadius: 4,
                    barThickness: 18,
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

    // 2. Doughnut Chart: Status Kepatuhan SLA
    const ctxKepatuhan = document.getElementById('statusKepatuhanChart');
    if (ctxKepatuhan) {
        new Chart(ctxKepatuhan.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Memenuhi SLA', 'Mendekati SLA', 'Melewati SLA'],
                datasets: [{
                    data: [
                        kpiData.on_track ?? 0, 
                        kpiData.warning ?? 0, 
                        kpiData.overdue ?? 0
                    ],
                    backgroundColor: ['#052e23', '#f59e0b', '#f43f5e'],
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