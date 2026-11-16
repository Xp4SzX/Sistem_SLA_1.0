// resources/js/pelapor/dashboard.js

document.addEventListener('DOMContentLoaded', function () {
    const stats = window.pelaporStats || {};
    const categoryData = window.pelaporCategoryData || { labels: [], values: [] };

    // 1. Doughnut Chart Status Tiket
    const ctxStatus = document.getElementById('statusChart');
    if (ctxStatus) {
        new Chart(ctxStatus.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Diproses', 'Selesai', 'Perlu Rating'],
                datasets: [{
                    data: [
                        stats.in_progress ?? 0,
                        stats.resolved ?? 0,
                        stats.need_rating ?? 0
                    ],
                    backgroundColor: ['#2563EB', '#10B981', '#F59E0B'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { font: { size: 11, family: 'Plus Jakarta Sans' }, usePointStyle: true } }
                },
                cutout: '70%'
            }
        });
    }

    // 2. Bar Chart Kategori Laporan
    const ctxCategory = document.getElementById('categoryChart');
    if (ctxCategory) {
        new Chart(ctxCategory.getContext('2d'), {
            type: 'bar',
            data: {
                labels: categoryData.labels,
                datasets: [{
                    label: 'Jumlah Tiket',
                    data: categoryData.values,
                    backgroundColor: '#022C22',
                    borderRadius: 6,
                    barThickness: 24
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1, font: { family: 'Plus Jakarta Sans' } } },
                    x: { ticks: { font: { family: 'Plus Jakarta Sans' } } }
                }
            }
        });
    }
});