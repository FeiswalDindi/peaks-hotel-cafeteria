@extends('layouts.admin')

@section('header', 'Staff Directory')

@section('content')
<div class="container-fluid">

<div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <form action="{{ route('admin.staff.index') }}" method="GET" class="position-relative">
                <input type="text" name="search" class="form-control form-control-lg ps-5 rounded-pill shadow-sm border-0" 
                       placeholder="Search for a department or staff..." value="{{ request('search') }}">
                <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
            </form>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.staff.create') }}" class="btn btn-lg text-white rounded-pill shadow-sm px-4" style="background-color: #192C57;">
                <i class="fas fa-plus-circle me-2"></i> Add New Staff
            </a>
        </div>
    </div>

    @if(request('search'))
    <div class="mb-5">
        <h5 class="fw-bold kca-navy mb-3">Search Results for "{{ request('search') }}"</h5>
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Staff Name</th>
                            <th>Staff ID</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users ?? [] as $user)
                        <tr>
                            <td class="ps-4 fw-bold text-dark">{{ $user->name }}</td>
                            <td>{{ $user->staff_number ?? 'N/A' }}</td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.staff.show', $user->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill shadow-sm" title="View Trends">
                                        <i class="fas fa-chart-pie"></i>
                                    </a>
                                    
                                    <a href="{{ route('admin.staff.edit', $user->id) }}" class="btn btn-sm text-white rounded-pill shadow-sm" style="background-color: #CEAA0C;" title="Edit Staff">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form action="{{ route('admin.staff.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently remove this staff member?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger rounded-pill shadow-sm" title="Remove Staff">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted">No staff members found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <div class="row g-4">
        @forelse($departments as $dept)
        <div class="col-md-4 col-lg-3">
            <a href="{{ route('admin.staff.department', $dept->id) }}" class="text-decoration-none">
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