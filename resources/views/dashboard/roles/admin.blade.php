<!-- KPI Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Completed KPI -->
    <div class="bg-card p-6 border border-border rounded-2xl shadow-sm flex items-center gap-5 premium-hover">
        <span class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </span>
        <div>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">{{ __('messages.kpi_completed') }}</p>
            <h3 class="text-2xl font-black text-foreground">{{ $totalCompleted }}</h3>
            <p class="text-[10px] text-gray-400 mt-1 font-medium">{{ __('messages.kpi_completed_desc') }}</p>
        </div>
    </div>

    <!-- Processing KPI -->
    <div class="bg-card p-6 border border-border rounded-2xl shadow-sm flex items-center gap-5 premium-hover">
        <span class="p-3 bg-[#d97706]/10 text-[#d97706] rounded-xl">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </span>
        <div>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">{{ __('messages.kpi_processing') }}</p>
            <h3 class="text-2xl font-black text-foreground">{{ $totalProcessing }}</h3>
            <p class="text-[10px] text-gray-400 mt-1 font-medium">{{ __('messages.kpi_processing_desc') }}</p>
        </div>
    </div>

    <!-- Pending KPI -->
    <div class="bg-card p-6 border border-border rounded-2xl shadow-sm flex items-center gap-5 premium-hover">
        <span class="p-3 bg-rose-50 text-rose-600 rounded-xl">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </span>
        <div>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">{{ __('messages.kpi_pending') }}</p>
            <h3 class="text-2xl font-black text-foreground">{{ $totalPending }}</h3>
            <p class="text-[10px] text-gray-400 mt-1 font-medium">{{ __('messages.kpi_pending_desc') }}</p>
        </div>
    </div>
</div>

<!-- Charts Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Bar Chart -->
    <div class="bg-card p-6 border border-border rounded-2xl shadow-sm lg:col-span-2 flex flex-col premium-hover">
        <h3 class="font-extrabold text-foreground text-sm tracking-wide mb-4">
            {{ __('messages.chart_by_dept') }}
        </h3>
        <div id="completionChart" class="flex-1 min-h-[300px]"></div>
    </div>

    <!-- Status Donut Chart -->
    <div class="bg-card p-6 border border-border rounded-2xl shadow-sm flex flex-col premium-hover">
        <h3 class="font-extrabold text-foreground text-sm tracking-wide mb-4">
            {{ __('messages.chart_status_dist') }}
        </h3>
        <div id="statusChart" class="flex-1 min-h-[300px] flex items-center justify-center"></div>
    </div>
</div>

<!-- Weekly Trend and Latest Transactions Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Line Chart (Weekly Trend) -->
    <div class="bg-card p-6 border border-border rounded-2xl shadow-sm lg:col-span-2 flex flex-col premium-hover">
        <h3 class="font-extrabold text-foreground text-sm tracking-wide mb-4">
            {{ __('messages.chart_weekly') }}
        </h3>
        <div id="trendChart" class="flex-1 min-h-[250px]"></div>
    </div>

    <!-- Latest Transactions -->
    <div class="bg-card p-6 border border-border rounded-2xl shadow-sm flex flex-col premium-hover">
        <h3 class="font-extrabold text-foreground text-sm tracking-wide mb-4">
            {{ __('messages.latest_transactions') }}
        </h3>
        <div class="flex-grow space-y-4">
            @forelse($latestTransactions as $t)
                <div class="flex items-center justify-between border-b border-border pb-3 last:border-b-0 last:pb-0">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="inline-flex text-[9px] font-extrabold px-1.5 py-0.5 rounded uppercase {{ $t['type'] == 'project' ? 'bg-blue-50 text-blue-700' : 'bg-emerald-50 text-emerald-700' }}">
                                {{ $t['type'] == 'project' ? __('messages.project_group') : __('messages.nav_requests') }}
                            </span>
                            <span class="text-[10px] text-gray-400 font-semibold">{{ $t['date'] }}</span>
                        </div>
                        <p class="text-xs font-semibold text-foreground truncate">
                            {{ app()->getLocale() == 'ar' ? $t['title_ar'] : $t['title_en'] }}
                        </p>
                    </div>
                    
                    <div>
                        <!-- Status Badge -->
                        @if($t['status'] == 'completed')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                {{ __('messages.status_completed') }}
                            </span>
                        @elseif($t['status'] == 'processing')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-100">
                                {{ __('messages.status_processing') }}
                            </span>
                        @elseif($t['status'] == 'pending')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                                {{ __('messages.status_pending') }}
                            </span>
                        @elseif($t['status'] == 'overdue')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-100">
                                {{ __('messages.status_overdue') }}
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-xs text-gray-400 font-semibold">{{ __('messages.no_data') }}</p>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<!-- ApexCharts CDN -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const isRtl = "{{ app()->getLocale() == 'ar' ? true : false }}" === "1";

        // 1. Completion by Department Bar Chart
        const deptNames = {!! json_encode(array_column($deptChartData, app()->getLocale() == 'ar' ? 'name_ar' : 'name_en')) !!};
        const completedData = {!! json_encode(array_column($deptChartData, 'completed')) !!};
        const processingData = {!! json_encode(array_column($deptChartData, 'processing')) !!};
        const pendingData = {!! json_encode(array_column($deptChartData, 'pending')) !!};

        const deptOptions = {
            chart: {
                type: 'bar',
                height: 320,
                stacked: true,
                toolbar: { show: false },
                fontFamily: 'inherit'
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    borderRadius: 4,
                    columnWidth: '40%',
                },
            },
            dataLabels: { enabled: false },
            stroke: { show: true, width: 2, colors: ['transparent'] },
            series: [
                { name: "{{ __('messages.status_completed') }}", data: completedData, color: '#10b981' },
                { name: "{{ __('messages.status_processing') }}", data: processingData, color: '#d97706' },
                { name: "{{ __('messages.status_pending') }}", data: pendingData, color: '#ef4444' }
            ],
            xaxis: {
                categories: deptNames,
                labels: { style: { colors: '#9ca3af', fontWeight: 600 } }
            },
            yaxis: {
                labels: { style: { colors: '#9ca3af', fontWeight: 600 } },
                opposite: isRtl
            },
            fill: { opacity: 1 },
            legend: {
                position: 'top',
                horizontalAlign: 'center',
                labels: { colors: '#4b5563', fontClass: 'font-semibold' }
            },
            tooltip: { shared: true, intersect: false }
        };
        const deptChart = new ApexCharts(document.querySelector("#completionChart"), deptOptions);
        deptChart.render();

        // 2. Status Distribution Donut Chart
        const statusValues = [
            {{ $statusDistribution['completed'] }},
            {{ $statusDistribution['processing'] }},
            {{ $statusDistribution['pending'] }},
            {{ $statusDistribution['overdue'] }}
        ];

        const statusOptions = {
            chart: {
                type: 'donut',
                height: 300,
                fontFamily: 'inherit'
            },
            labels: [
                "{{ __('messages.status_completed') }}",
                "{{ __('messages.status_processing') }}",
                "{{ __('messages.status_pending') }}",
                "{{ __('messages.status_overdue') }}"
            ],
            colors: ['#10b981', '#d97706', '#3b82f6', '#ef4444'],
            series: statusValues,
            responsive: [{
                breakpoint: 480,
                options: { chart: { width: 200 }, legend: { position: 'bottom' } }
            }],
            legend: {
                position: 'bottom',
                labels: { colors: '#4b5563', fontClass: 'font-semibold' }
            },
            dataLabels: { enabled: false }
        };
        const statusChart = new ApexCharts(document.querySelector("#statusChart"), statusOptions);
        statusChart.render();

        // 3. Weekly Completion Trend Line Chart
        const weeks = {!! json_encode(array_column($weeklyTrend, 'week')) !!};
        const trendCompleted = {!! json_encode(array_column($weeklyTrend, 'completed')) !!};
        const trendProcessing = {!! json_encode(array_column($weeklyTrend, 'processing')) !!};

        const trendOptions = {
            chart: {
                type: 'line',
                height: 250,
                toolbar: { show: false },
                fontFamily: 'inherit'
            },
            stroke: { curve: 'smooth', width: 3 },
            series: [
                { name: "{{ __('messages.status_completed') }}", data: trendCompleted, color: '#10b981' },
                { name: "{{ __('messages.status_processing') }}", data: trendProcessing, color: '#d97706' }
            ],
            xaxis: {
                categories: weeks,
                labels: { style: { colors: '#9ca3af', fontWeight: 600 } }
            },
            yaxis: {
                labels: { style: { colors: '#9ca3af', fontWeight: 600 } },
                opposite: isRtl
            },
            legend: {
                position: 'top',
                horizontalAlign: 'center',
                labels: { colors: '#4b5563', fontClass: 'font-semibold' }
            }
        };
        const trendChart = new ApexCharts(document.querySelector("#trendChart"), trendOptions);
        trendChart.render();
    });
</script>
@endpush
