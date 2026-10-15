@extends('layouts.dashboard')

@section('title', __('messages.document_requests'))

@section('dashboard_content')
<!-- Header -->
<div class="mb-8">
    <h2 class="text-2xl font-black text-foreground tracking-tight mb-1">
        {{ __('messages.document_requests') }}
    </h2>
    <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wider">
        {{ __('messages.app_name') }}
    </p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Request List (Takes full width) -->
    <div class="bg-card border border-border rounded-2xl shadow-sm overflow-hidden lg:col-span-3 transition-colors duration-200">
        <!-- Filters Bar (For admin, super_admin, alumni_officer) -->
        @if(in_array(auth()->user()->role, ['super_admin', 'admin', 'alumni_officer']))
            <div class="p-4 border-b border-border bg-muted/30 no-print">
                <form action="{{ route('alumni.requests.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                    <div>
                        <label class="block text-[10px] font-extrabold text-muted-foreground/85 uppercase mb-1.5">{{ __('messages.search') }}</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('messages.search') }}" class="w-full text-xs font-semibold px-3.5 py-2 border border-border rounded-xl focus:border-primary focus:ring-1 focus:ring-primary bg-card text-foreground outline-none">
                    </div>

                    <div>
                        <label class="block text-[10px] font-extrabold text-muted-foreground/85 uppercase mb-1.5">{{ __('messages.filter_status') }}</label>
                        <select name="status" class="w-full text-xs font-semibold px-3.5 py-2 border border-border rounded-xl bg-card text-foreground outline-none">
                            <option value="all">{{ __('messages.all') }}</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ __('messages.status_completed') }}</option>
                            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>{{ __('messages.status_processing') }}</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('messages.status_pending') }}</option>
                            <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>{{ __('messages.status_overdue') }}</option>
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="flex-grow px-4 py-2 bg-primary hover:bg-primary/95 text-white text-xs font-bold rounded-xl transition-all shadow-sm cursor-pointer">
                            {{ __('messages.generate') }}
                        </button>
                        <a href="{{ route('alumni.requests.index') }}" class="px-4 py-2 border border-border text-muted-foreground hover:bg-muted text-xs font-bold rounded-xl transition-all flex items-center justify-center">
                            {{ __('messages.reset_filters') }}
                        </a>
                    </div>
                </form>
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-start border-collapse">
                <thead>
                    <tr class="bg-muted/50 border-b border-border text-muted-foreground font-extrabold uppercase text-[10px] tracking-wider text-start">
                        <th class="p-4 text-start font-extrabold">#</th>
                        @if(in_array(auth()->user()->role, ['super_admin', 'admin', 'alumni_officer']))
                            <th class="p-4 text-start font-extrabold">{{ __('messages.role_alumni') }}</th>
                        @endif
                        <th class="p-4 text-start font-extrabold">{{ __('messages.doc_type') }}</th>
                        <th class="p-4 text-start font-extrabold">{{ __('messages.request_date') }}</th>
                        <th class="p-4 text-start font-extrabold">{{ __('messages.ministry_status') }}</th>
                        <th class="p-4 text-start font-extrabold">{{ __('messages.modification_reason_type') }}</th>
                        <th class="p-4 text-center font-extrabold no-print">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border text-xs font-semibold text-foreground">
                    @forelse($requests as $req)
                        <tr class="hover:bg-muted/40 transition-colors">
                            <td class="p-4 text-muted-foreground font-extrabold">REQ-{{ 3000 + $req->id }}</td>
                            @if(in_array(auth()->user()->role, ['super_admin', 'admin', 'alumni_officer']))
                                <td class="p-4 font-bold text-foreground">
                                    {{ app()->getLocale() == 'ar' ? $req->alumnus->name_ar : $req->alumnus->name_en }}
                                    <div class="text-[10px] text-muted-foreground font-normal">
                                        {{ $req->alumnus->department ?? 'قسم أكاديمي' }}
                                    </div>
                                </td>
                            @endif
                            <td class="p-4 text-foreground font-bold">
                                {{ app()->getLocale() == 'ar' ? $req->doc_type_ar : $req->doc_type_en }}
                            </td>
                            <td class="p-4 text-muted-foreground font-medium">{{ $req->created_at->format('Y-m-d') }}</td>
                            <td class="p-4">
                                <!-- Ministry Status Badge -->
                                @php $mState = $req->ministry_status ?? 'in_ministry'; @endphp
                                @if($mState == 'delivered' || $req->status == 'completed')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        {{ __('messages.ministry_status_delivered') }}
                                    </span>
                                @elseif($mState == 'approved')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-teal-50 text-teal-700 border border-teal-200">
                                        {{ __('messages.ministry_status_approved') }}
                                    </span>
                                @elseif($mState == 'ready')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        {{ __('messages.ministry_status_ready') }}
                                    </span>
                                @elseif($mState == 'returned')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        {{ __('messages.ministry_status_returned') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        {{ __('messages.ministry_status_in_ministry') }}
                                    </span>
                                @endif
                            </td>

                            <td class="p-4 text-xs font-medium">
                                @if($req->modification_reason_type && $req->modification_reason_type !== 'reason_none')
                                    <span class="text-rose-600 font-bold block">{{ __('messages.' . $req->modification_reason_type) }}</span>
                                    <span class="text-[10px] text-muted-foreground block">{{ $req->modification_details }}</span>
                                @else
                                    <span class="text-muted-foreground font-semibold">-</span>
                                @endif
                            </td>

                            <!-- Action buttons or status selectors -->
                            @if(in_array(auth()->user()->role, ['super_admin', 'admin', 'alumni_officer']))
                                <td class="p-4 text-center no-print">
                                    <form action="{{ route('alumni.requests.status', $req->id) }}" method="POST" class="flex flex-col gap-1 items-center">
                                        @csrf
                                        <div class="flex items-center gap-1">
                                            <select name="ministry_status" class="text-[10px] font-bold px-2 py-1 border border-border rounded-lg outline-none bg-card text-foreground">
                                                <option value="in_ministry" {{ ($req->ministry_status ?? 'in_ministry') == 'in_ministry' ? 'selected' : '' }}>{{ __('messages.ministry_status_in_ministry') }}</option>
                                                <option value="approved" {{ ($req->ministry_status ?? '') == 'approved' ? 'selected' : '' }}>{{ __('messages.ministry_status_approved') }}</option>
                                                <option value="ready" {{ ($req->ministry_status ?? '') == 'ready' ? 'selected' : '' }}>{{ __('messages.ministry_status_ready') }}</option>
                                                <option value="delivered" {{ ($req->ministry_status ?? '') == 'delivered' ? 'selected' : '' }}>{{ __('messages.ministry_status_delivered') }}</option>
                                                <option value="returned" {{ ($req->ministry_status ?? '') == 'returned' ? 'selected' : '' }}>{{ __('messages.ministry_status_returned') }}</option>
                                            </select>

                                            <select name="status" class="text-[10px] font-bold px-2 py-1 border border-border rounded-lg outline-none bg-card text-foreground">
                                                <option value="processing" {{ $req->status == 'processing' ? 'selected' : '' }}>{{ __('messages.status_processing') }}</option>
                                                <option value="completed" {{ $req->status == 'completed' ? 'selected' : '' }}>{{ __('messages.status_completed') }}</option>
                                                <option value="pending" {{ $req->status == 'pending' ? 'selected' : '' }}>{{ __('messages.status_pending') }}</option>
                                            </select>
                                        </div>

                                        <button type="submit" class="px-3 py-1 bg-primary text-white text-[10px] font-bold rounded-lg hover:bg-primary/90 transition-all cursor-pointer">
                                            {{ __('messages.save') }}
                                        </button>
                                    </form>
                                </td>
                            @else
                                <td class="p-4 text-center no-print">
                                    @if($req->status == 'completed' || $req->ministry_status == 'delivered')
                                        <a href="{{ route('alumni.requests.download', $req->id) }}" target="_blank" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold text-[10px] shadow-sm transition-all inline-block">
                                            تحميل الوثيقة
                                        </a>
                                    @else
                                        <span class="text-muted-foreground font-bold text-[10px]">-</span>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-12 text-center text-muted-foreground font-bold">
                                {{ __('messages.no_data') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
