@extends('layouts.dashboard')

@section('title', __('messages.supervisor_perf'))

@section('dashboard_content')
<!-- Header -->
<div class="mb-8">
    <h2 class="text-2xl font-black text-foreground tracking-tight mb-1">
        {{ __('messages.supervisor_perf') }}
    </h2>
    <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wider">
        {{ __('messages.app_name') }}
    </p>
</div>

<!-- Supervisors Table Card -->
<div class="bg-card border border-border rounded-2xl shadow-sm overflow-hidden transition-colors duration-200">
    <div class="overflow-x-auto">
        <table class="w-full text-start border-collapse">
            <thead>
                <tr class="bg-muted/40 border-b border-border text-muted-foreground font-extrabold uppercase text-[10px] tracking-wider text-start">
                    <th class="p-4 text-start font-extrabold">#</th>
                    <th class="p-4 text-start font-extrabold">{{ __('messages.supervisor') }}</th>
                    <th class="p-4 text-start font-extrabold">{{ __('messages.filter_dept') }}</th>
                    <th class="p-4 text-center font-extrabold">{{ __('messages.groups_count') }}</th>
                    <th class="p-4 text-center font-extrabold text-rose-600">{{ __('messages.stuck_groups') }}</th>
                    <th class="p-4 text-center font-extrabold">{{ __('messages.avg_days') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border text-xs font-semibold text-foreground">
                @forelse($supervisors as $s)
                    <tr class="hover:bg-muted/30 transition-colors">
                        <td class="p-4 text-muted-foreground font-extrabold">{{ $s['id'] }}</td>
                        <td class="p-4 font-bold text-foreground">
                            {{ app()->getLocale() == 'ar' ? $s['name_ar'] : $s['name_en'] }}
                        </td>
                        <td class="p-4">
                            <span class="inline-flex px-2 py-0.5 bg-muted rounded text-[10px] font-bold text-muted-foreground">
                                {{ $s['department'] }}
                            </span>
                        </td>
                        <td class="p-4 text-center font-bold">{{ $s['groupsCount'] }}</td>
                        <td class="p-4 text-center text-rose-600 font-bold">{{ $s['stuckGroups'] }}</td>
                        <td class="p-4 text-center font-bold">{{ $s['avgDays'] }} {{ __('messages.days') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-12 text-center text-muted-foreground font-bold">
                            {{ __('messages.no_data') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
