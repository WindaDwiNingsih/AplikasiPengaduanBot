<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
    {{-- Header Compact --}}
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            Statistik Pengaduan
        </h3>
        
        {{-- Filter Tanggal Compact --}}
        <div class="flex items-center space-x-2">
            <input type="date" id="startDate" class="text-xs px-2 py-1 border border-gray-300 rounded">
            <span class="text-xs text-gray-500">s/d</span>
            <input type="date" id="endDate" class="text-xs px-2 py-1 border border-gray-300 rounded">
            <button onclick="loadStats()" class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1 rounded transition duration-200">
                Filter
            </button>
        </div>
    </div>

    {{-- Card Statistics Compact --}}
    <div class="grid grid-cols-5 gap-2 mb-4" id="cardStats">
        {{-- Loading Skeleton --}}
        <div class="text-center p-2 bg-gray-50 rounded border">
            <div class="animate-pulse">
                <div class="h-3 bg-gray-300 rounded w-3/4 mx-auto mb-1"></div>
                <div class="h-4 bg-gray-300 rounded w-1/2 mx-auto"></div>
            </div>
        </div>
    </div>

    {{-- Charts Container Compact --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        {{-- Pie Chart Compact --}}
        <div class="bg-gray-50 rounded border p-3">
            <h4 class="text-sm font-medium text-gray-700 mb-2 text-center">Distribusi Status</h4>
            <div class="h-48"> {{-- Lebih pendek --}}
                <canvas id="complaintPieChart"></canvas>
            </div>
        </div>
        
        {{-- Bar Chart Compact --}}
        <div class="bg-gray-50 rounded border p-3">
            <h4 class="text-sm font-medium text-gray-700 mb-2 text-center">Perbandingan Status</h4>
            <div class="h-48"> {{-- Lebih pendek --}}
                <canvas id="complaintBarChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Loading Indicator Compact --}}
    <div id="loadingStats" class="hidden text-center py-2">
        <div class="inline-flex items-center text-sm text-gray-600">
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Memuat data...
        </div>
    </div>
</div>

<script>
let pieChart, barChart;

// Load data saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    // Set default date range (30 hari terakhir)
    const endDate = new Date();
    const startDate = new Date();
    startDate.setDate(startDate.getDate() - 30);
    
    document.getElementById('startDate').value = startDate.toISOString().split('T')[0];
    document.getElementById('endDate').value = endDate.toISOString().split('T')[0];
    
    loadStats();
});

function loadStats() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    
    showLoading();
    
     const url = `{{ route('complaints.stats') }}?start_date=${startDate}&end_date=${endDate}`;

    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        
        if (data.success) {
            updateCardStats(data.card_stats);
            updateCharts(data.chart_data);
        } else {
            showNotification('Gagal memuat data statistik', 'error');
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error:', error);
        showNotification('Terjadi kesalahan saat memuat data', 'error');
    });
}

function updateCardStats(stats) {
    const cardStatsHtml = `
        <div class="text-center p-2 bg-blue-50 rounded border border-blue-200">
            <div class="text-lg font-bold text-blue-600">${stats.total}</div>
            <div class="text-xs text-blue-800 font-medium">Total</div>
        </div>
        <div class="text-center p-2 bg-yellow-50 rounded border border-yellow-200">
            <div class="text-lg font-bold text-yellow-600">${stats.pending}</div>
            <div class="text-xs text-yellow-800 font-medium">Pending</div>
        </div>
        <div class="text-center p-2 bg-blue-50 rounded border border-blue-200">
            <div class="text-lg font-bold text-blue-600">${stats.process}</div>
            <div class="text-xs text-blue-800 font-medium">Proses</div>
        </div>
        <div class="text-center p-2 bg-green-50 rounded border border-green-200">
            <div class="text-lg font-bold text-green-600">${stats.resolved}</div>
            <div class="text-xs text-green-800 font-medium">Selesai</div>
        </div>
        <div class="text-center p-2 bg-red-50 rounded border border-red-200">
            <div class="text-lg font-bold text-red-600">${stats.rejected}</div>
            <div class="text-xs text-red-800 font-medium">Ditolak</div>
        </div>
    `;
    
    document.getElementById('cardStats').innerHTML = cardStatsHtml;
}

function updateCharts(chartData) {
    // Destroy existing charts
    if (pieChart) pieChart.destroy();
    if (barChart) barChart.destroy();
    
    // Pie Chart - Compact
    const pieCtx = document.getElementById('complaintPieChart').getContext('2d');
    pieChart = new Chart(pieCtx, {
        type: 'pie',
        data: chartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 10,
                        boxWidth: 12,
                        font: {
                            size: 10
                        }
                    }
                },
                tooltip: {
                    titleFont: { size: 11 },
                    bodyFont: { size: 11 },
                    padding: 8
                }
            }
        }
    });
    
    // Bar Chart - Compact
    const barCtx = document.getElementById('complaintBarChart').getContext('2d');
    barChart = new Chart(barCtx, {
        type: 'bar',
        data: chartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        font: { size: 10 },
                        stepSize: 1
                    }
                },
                x: {
                    ticks: {
                        font: { size: 10 }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    titleFont: { size: 11 },
                    bodyFont: { size: 11 },
                    padding: 8
                }
            }
        }
    });
}

function showLoading() {
    document.getElementById('loadingStats').classList.remove('hidden');
}

function hideLoading() {
    document.getElementById('loadingStats').classList.add('hidden');
}

function showNotification(message, type = 'info') {
    // Simple notification
    const bgColor = type === 'error' ? 'bg-red-500' : 'bg-blue-500';
    
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 ${bgColor} text-white px-4 py-2 rounded-lg shadow-lg z-50`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>