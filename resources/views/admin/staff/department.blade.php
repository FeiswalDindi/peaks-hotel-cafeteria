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
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-secondary">
                <tr>
                    <th class="ps-4">Staff Name</th>
                    <th>Staff ID</th>
                    <th>Daily Allocation</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($department->users as $user)
                <tr>
                    <td class="ps-4 fw-bold" style="color: #192C57;">{{ $user->name }}</td>
                    <td>{{ $user->staff_number ?? 'N/A' }}</td>
                    
                    <td class="text-success fw-bold">KES {{ number_format($user->daily_allocation) }}</td>
                    
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
                    <td colspan="4" class="text-center py-5 text-muted">No staff members assigned to this department yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection