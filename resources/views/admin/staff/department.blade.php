@extends('layouts.admin')

@section('header')
<div class="d-flex align-items-center">
    <a href="{{ route('admin.staff.index') }}" class="btn btn-sm btn-light border rounded-circle me-3 shadow-sm">
        <i class="fas fa-arrow-left"></i>
    </a>
    <span class="fw-bold">{{ $department->name }} - Staff List</span>
</div>
@endsection

@section('content')
<style>
    @media (max-width: 768px) {
        .mobile-clickable-row { cursor: pointer; transition: background-color 0.2s; }
        .mobile-clickable-row:active { background-color: #f0f0f0; }
        .mobile-expand-icon { transition: transform 0.3s ease; }
        tr[aria-expanded="true"] .mobile-expand-icon { transform: rotate(180deg); color: #CEAA0C !important; }
        .detail-row td { padding: 0 !important; border: none; }
        .detail-content { 
            padding: 1.25rem; 
            border-left: 4px solid #192C57; 
            background-color: #f8f9fc; 
            box-shadow: inset 0 3px 6px rgba(0,0,0,0.02);
        }
    }
</style>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0 w-100">
            <thead class="table-light text-secondary">
                <tr class="text-nowrap">
                    <th class="ps-3 ps-md-4">Staff Name</th>
                    <th class="d-none d-md-table-cell">Staff ID</th>
                    <th class="d-none d-md-table-cell">Daily Allocation</th>
                    <th class="d-none d-md-table-cell text-end pe-4">Actions</th>
                    <th class="d-md-none text-end pe-3">Info</th>
                </tr>
            </thead>
            <tbody>
                @forelse($department->users as $user)
                <tr class="mobile-clickable-row" data-bs-toggle="collapse" data-bs-target="#staff-{{ $user->id }}">
                    <td class="ps-3 ps-md-4 fw-bold" style="color: #192C57;">
                        {{ $user->name }}
                        <div class="d-md-none small text-muted fw-normal mt-1">{{ $user->staff_number ?? 'No ID' }}</div>
                    </td>
                    
                    <td class="d-none d-md-table-cell">{{ $user->staff_number ?? 'N/A' }}</td>
                    <td class="d-none d-md-table-cell text-success fw-bold">KES {{ number_format($user->daily_allocation) }}</td>
                    
                    <td class="d-none d-md-table-cell text-end pe-4">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.staff.show', $user->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill shadow-sm" title="View Trends"><i class="fas fa-chart-pie"></i></a>
                            <a href="{{ route('admin.staff.edit', $user->id) }}" class="btn btn-sm text-white rounded-pill shadow-sm" style="background-color: #CEAA0C;" title="Edit Staff"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.staff.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently remove this staff member?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger rounded-pill shadow-sm" title="Remove Staff"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </div>
                    </td>
                    
                    <td class="d-md-none text-end pe-3"><i class="fas fa-chevron-down text-muted mobile-expand-icon"></i></td>
                </tr>

                <tr id="staff-{{ $user->id }}" class="collapse d-md-none detail-row">
                    <td colspan="3">
                        <div class="detail-content">
                            <div class="d-flex justify-content-between mb-3 pb-2 border-bottom">
                                <span class="text-muted small">Daily Allocation:</span>
                                <span class="fw-bold text-success small">KES {{ number_format($user->daily_allocation) }}</span>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.staff.show', $user->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill flex-fill"><i class="fas fa-chart-pie me-1"></i> Trends</a>
                                <a href="{{ route('admin.staff.edit', $user->id) }}" class="btn btn-sm text-white rounded-pill flex-fill" style="background-color: #CEAA0C;"><i class="fas fa-edit me-1"></i> Edit</a>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">No staff members assigned to this department yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection