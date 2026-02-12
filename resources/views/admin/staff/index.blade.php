@extends('layouts.admin')

@section('header', 'Staff Directory')

@section('content')
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold kca-navy mb-0">Registered Staff</h5>
            <a href="{{ route('admin.staff.create') }}" class="btn btn-primary rounded-pill px-4" style="background-color: #192C57;">
                <i class="fas fa-plus me-2"></i> Add Staff
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-light text-secondary small text-uppercase">
                    <tr>
                        <th class="ps-4">Staff No.</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Allowance</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($staffMembers as $staff)
                    <tr>
                        <td class="ps-4 fw-bold text-primary">{{ $staff->staff_number }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; color: #192C57; font-weight: bold;">
                                    {{ substr($staff->name, 0, 1) }}
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">{{ $staff->name }}</h6>
                                    <small class="text-muted">{{ $staff->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ $staff->department }}</span></td>
                        <td class="fw-bold text-success">KES {{ $staff->daily_allocation }}</td>
                        <td><span class="badge bg-success">Active</span></td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.staff.edit', $staff->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <form action="{{ route('admin.staff.destroy', $staff->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection