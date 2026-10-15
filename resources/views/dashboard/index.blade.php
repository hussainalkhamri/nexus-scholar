@extends('layouts.dashboard')

@section('title', __('messages.nav_dashboard'))

@section('dashboard_content')
<!-- Header -->
<div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h2 class="text-2xl font-black text-gray-900 tracking-tight mb-1">
            {{ __('messages.nav_dashboard') }}
        </h2>
        <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">
            @if(in_array(auth()->user()->role, ['super_admin', 'admin', 'projects_officer', 'alumni_officer']))
                {{ __('messages.dashboard_subtitle') }}
            @elseif(auth()->user()->role === 'supervisor')
                {{ __('messages.supervisor_control_panel') }}
            @elseif(auth()->user()->role === 'student')
                {{ __('messages.student_project_workspace') }}
            @elseif(auth()->user()->role === 'alumnus')
                {{ __('messages.alumni_opportunities_center') }}
            @endif
        </p>
    </div>

</div>

<!-- Dynamic Role Segregated Dashboard Contents -->
@if(in_array(auth()->user()->role, ['super_admin', 'admin', 'projects_officer', 'alumni_officer']))
    @include('dashboard.roles.admin')
@elseif(auth()->user()->role === 'supervisor')
    @include('dashboard.roles.supervisor')
@elseif(auth()->user()->role === 'student')
    @include('dashboard.roles.student')
@elseif(auth()->user()->role === 'alumnus')
    @include('dashboard.roles.alumnus')
@endif

@endsection
