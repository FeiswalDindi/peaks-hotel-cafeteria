@extends('layouts.admin')

@section('header', 'Staff Directory')

@section('content')
<div class="container-fluid">

    <div class="row mb-4">
        <div class="col-md-6">
            <form action="{{ route('admin.staff.index') }}" method="GET" class="position-relative">
                <input type="text" name="search" class="form-control form-control-lg ps-5 rounded-pill shadow-sm border-0" 
                       placeholder="Search for a department..." value="{{ request('search') }}">
                <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
            </form>
        </div>
    </div>

    <div class="row g-4">
        @forelse($departments as $dept)
        <div class="col-md-4 col-lg-3">
            <a href="{{ route('admin.staff.show', $dept->id) }}" class="text-decoration-none">
                <div class="card h-100 border-0 shadow-sm hover-card" style="transition: transform 0.2s;">
                    <div class="card-body text-center py-5">
                        <div class="mb-3 position-relative d-inline-block">
                            <i class="fas fa-folder fa-4x text-warning"></i>
                            <span class="position-absolute top-50 start-50 translate-middle text-white fw-bold" style="font-size: 0.9rem; margin-top: 5px;">
                                {{ $dept->staff_count }}
                            </span>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">{{ $dept->name }}</h5>
                        <small class="text-muted">{{ $dept->code ?? 'No Code' }}</small>
                    </div>
                    <div class="card-footer bg-white border-0 text-center pb-3">
                        <small class="text-primary fw-bold">View Staff <i class="fas fa-arrow-right ms-1"></i></small>
                    </div>
                </div>
            </a>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <i class="fas fa-folder-open fa-3x text-muted mb-3 opacity-50"></i>
            <h5 class="text-muted">No departments found.</h5>
            <a href="{{ route('admin.departments.index') }}" class="btn btn-outline-primary mt-2">Manage Departments</a>
        </div>
        @endforelse
    </div>

</div>

<style>
    .hover-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
</style>
@endsection