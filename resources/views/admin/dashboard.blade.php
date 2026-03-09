@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
        <!-- Header & Quick Actions -->
        <div class="card-minimal p-8 lg:p-12 mb-8 animate-slide-up">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8">
                <div class="flex items-center gap-6">
                    <div class="w-16 h-16 bg-black text-white rounded-full flex items-center justify-center shadow-none">
                        <i class="fas fa-shield-alt text-xl font-bold"></i>
                    </div>
                    <div>
                        <h1 class="text-5xl font-black text-black tracking-tighter uppercase mb-1">ADMIN PANEL</h1>
                        <p class="text-lg text-gray-500 font-medium uppercase tracking-widest">SYSTEM CONTROL CENTER</p>
                    </div>
                </div>
                
                <!-- Admin Quick Actions -->
                <div class="flex flex-wrap gap-4 justify-center lg:justify-end">
                    <a href="/admin/parking-spaces" class="btn-secondary px-8 py-4 text-sm flex items-center gap-3">
                        <i class="fas fa-parking"></i> SPACES
                    </a>
                    <a href="/admin/owners/create" class="btn-secondary px-8 py-4 text-sm flex items-center gap-3">
                        <i class="fas fa-user-plus"></i> ADD OWNER
                    </a>
                    <a href="/admin/vehicle-categories" class="btn-secondary px-8 py-4 text-sm flex items-center gap-3">
                        <i class="fas fa-tags"></i> CATEGORIES
                    </a>
                </div>
            </div>
        </div>

        <!-- Live Parking Overview -->
        <div class="card-minimal p-8 lg:p-12 animate-slide-up" style="animation-delay: 0.1s;">
            <h2 class="text-3xl font-black text-black mb-8 flex items-center gap-3 uppercase tracking-tighter">
                <i class="fas fa-car text-black text-2xl"></i>
                LIVE PARKING OVERVIEW
            </h2>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="border-b-2 border-black">
                            <th class="py-4 px-6 font-bold text-black uppercase tracking-widest bg-light">PARKING SPACE</th>
                            <th class="py-4 px-6 font-bold text-black uppercase tracking-widest bg-light text-center">CAPACITY</th>
                            <th class="py-4 px-6 font-bold text-black uppercase tracking-widest bg-light text-center">AVAILABLE</th>
                            <th class="py-4 px-6 font-bold text-black uppercase tracking-widest bg-light text-center">OCCUPIED</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($parkingSpaces as $space)
                            <tr class="hover:bg-gray-50 transition-colors h-20">
                                <td class="py-5 px-6 font-bold text-black text-lg">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 bg-black text-white rounded-full flex items-center justify-center">
                                            <i class="fas fa-parking"></i>
                                        </div>
                                        {{ $space->name }}
                                    </div>
                                </td>
                                <td class="py-5 px-6 text-center text-gray-800 font-bold text-xl">{{ $space->capacity }}</td>
                                <td class="py-5 px-6 text-center">
                                    <span class="inline-flex items-center px-5 py-2 status-available rounded-full font-bold text-lg">
                                        <i class="fas fa-check-circle mr-2"></i> {{ $space->available_slots_count }}
                                    </span>
                                </td>
                                <td class="py-5 px-6 text-center">
                                    <span class="inline-flex items-center px-5 py-2 status-occupied rounded-full font-bold text-lg">
                                        <i class="fas fa-times-circle mr-2"></i> {{ $space->occupied_slots }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Financial Analytics -->
        <div class="card-minimal p-8 lg:p-12 animate-slide-up mt-8" style="animation-delay: 0.2s;">
            <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
                <h3 class="text-3xl font-black text-black flex items-center gap-3 uppercase tracking-tighter">
                    <i class="fas fa-chart-pie text-black text-2xl"></i>
                    FINANCIAL ANALYTICS
                </h3>
                <a href="{{ route('admin.report.download') }}" class="btn-primary px-8 py-3 text-sm flex items-center gap-3">
                    <i class="fas fa-download"></i> DOWNLOAD REPORT
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <div class="lg:col-span-8 card-light border border-gray-200 p-8">
                    <h6 class="text-gray-500 font-bold uppercase tracking-widest text-sm mb-6 pb-4 border-b border-gray-200">REVENUE TREND ({{ date('Y') }})</h6>
                    <div style="position: relative; height: 350px; width: 100%;">
                        <canvas id="revenueLineChart"></canvas>
                    </div>
                </div>
                
                <div class="lg:col-span-4 card-light border border-gray-200 p-8">
                    <h6 class="text-gray-500 font-bold uppercase tracking-widest text-sm mb-6 pb-4 border-b border-gray-200">OWNER METRICS</h6>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left bg-transparent">
                            <thead>
                                <tr class="border-b-2 border-black">
                                    <th class="py-3 px-2 font-bold text-black uppercase tracking-widest text-xs">Space / Owner</th>
                                    <th class="py-3 px-2 font-bold text-black uppercase tracking-widest text-right text-xs">Rev</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($reportData ?? [] as $data)
                                <tr class="hover:bg-gray-100 transition-colors">
                                    <td class="py-4 px-2 font-bold text-black text-sm uppercase">
                                        {{ $data->name }}<br>
                                        <small class="text-gray-500 text-xs font-bold uppercase tracking-widest">{{ data_get($data, 'owner.name', 'System') }}</small>
                                    </td>
                                    <td class="py-4 px-2 text-right font-mono font-black text-green-700 text-lg">
                                        ₹{{ number_format($data->unified_total_revenue ?? 0, 2) }}<br>
                                        <small class="text-gray-500 text-xs font-bold uppercase tracking-widest">{{ $data->unified_bookings_count ?? 0 }} Appts</small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartData = @json($chartData ?? ['labels' => [], 'datasets' => []]);

    const ctxLine = document.getElementById('revenueLineChart').getContext('2d');
    new Chart(ctxLine, {
        type: 'line', // Strictly set to Line Chart
        data: chartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                y: { 
                    beginAtZero: true,
                    grid: { borderDash: [5, 5] },
                    ticks: { callback: function(value) { return '₹' + value; } }
                },
                x: {
                    grid: { display: false }
                }
            },
            plugins: {
                legend: { position: 'bottom', labels: { font: { family: 'Inter', weight: 'bold' }, usePointStyle: true, boxWidth: 8 } },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ₹' + context.parsed.y;
                        }
                    }
                }
            }
        }
    });
});
</script>
@endsection
