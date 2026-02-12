@extends('layouts.admin')

@section('header')
    {{ $department->name }} Staff
@endsection

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="fas fa-arrow-left me-2"></i> Back to Folders
        </a>

        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addStaffModal" style="background-color: #192C57; border: none;">
            <i class="fas fa-user-plus me-2"></i> Add Staff Member
        </button>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm border-0">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary">
                        <tr>
                            <th class="ps-4">Staff Name</th>
                            <th>Staff No.</th>
                            <th>Daily Allowance</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($staffMembers as $staff)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                                         style="width: 40px; height: 40px; font-weight: bold;">
                                        {{ strtoupper(substr($staff->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold text-dark">{{ $staff->name }}</h6>
                                        <small class="text-muted">{{ $staff->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $staff->staff_number ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="fw-bold text-dark">
                                KES {{ number_format($staff->daily_allocation ?? 0) }}
                            </td>
                            <td>
                                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">
                                    Active
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <button class="btn btn-sm btn-outline-secondary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editStaffModal{{ $staff->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                
                                <form action="{{ route('admin.staff.destroy', $staff->id) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to delete this staff member?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger ms-1">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                No staff found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- EDIT STAFF MODALS (MOVED OUTSIDE TABLE) --}}
    @foreach($staffMembers as $staff)
    <div class="modal fade" id="editStaffModal{{ $staff->id }}" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('admin.staff.update', $staff->id) }}" method="POST" class="modal-content">
                @csrf
                @method('PUT')
                <input type="hidden" name="department_id" value="{{ $department->id }}">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Edit {{ $staff->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Full Name</label>
                        <input type="text" name="name" class="form-control" value="{{ $staff->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="{{ $staff->email }}" required>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label>Staff No</label>
                            <input type="text" name="staff_number" class="form-control" value="{{ $staff->staff_number }}">
                        </div>
                        <div class="col-6 mb-3">
                            <label>Allowance (KES)</label>
                            <input type="number" name="daily_allocation" class="form-control" value="{{ $staff->daily_allocation }}" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endforeach

</div>

{{-- ADD STAFF MODAL --}}
<div class="modal fade" id="addStaffModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.staff.store') }}" method="POST" class="modal-content">
            @csrf
            <input type="hidden" name="department_id" value="{{ $department->id }}">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add Staff to {{ $department->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Staff Number</label>
                        <input type="text" name="staff_number" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Default Password</label>
                        <input type="text" name="password" class="form-control" value="kca12345" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-success">Daily Allowance</label>
                        <input type="number" name="daily_allocation" class="form-control" value="500" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary" style="background-color: #192C57;">
                    Save Staff Member
                </button>
            </div>
        </form>
    </div>
</div>

@endsection