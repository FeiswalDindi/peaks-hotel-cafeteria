@extends('layouts.admin')

@section('header', 'Department Management')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <form action="{{ route('admin.departments.index') }}" method="GET" class="d-flex gap-2" style="max-width: 400px;">
            <input type="text" name="search" class="form-control" placeholder="Search departments..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-outline-primary"><i class="fas fa-search"></i></button>
        </form>
        
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDeptModal">
            <i class="fas fa-plus me-2"></i> New Department
        </button>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary">
                        <tr>
                            <th class="ps-4">Department Name</th>
                            <th>Code</th>
                            <th>Staff Count</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($departments as $dept)
                        <tr>
                            <td class="ps-4 fw-bold text-dark">{{ $dept->name }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $dept->code ?? 'N/A' }}</span></td>
                            <td>
                                <span class="badge bg-info text-dark rounded-pill">
                                    {{ $dept->staff_count }} Staff
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <button class="btn btn-sm btn-outline-secondary me-1" 
                                        onclick="editDept({{ $dept->id }}, '{{ $dept->name }}', '{{ $dept->code }}')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                
                                <form action="{{ route('admin.departments.destroy', $dept->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this department?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="fas fa-building fa-2x mb-3 opacity-50"></i>
                                <p>No departments found. Create one to get started!</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            {{ $departments->links() }}
        </div>
    </div>

</div>

<div class="modal fade" id="addDeptModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.departments.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add New Department</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Department Name</label>
                    <input type="text" name="name" class="form-control" required placeholder="e.g. IT Services">
                </div>
                <div class="mb-3">
                    <label class="form-label">Code (Optional)</label>
                    <input type="text" name="code" class="form-control" placeholder="e.g. IT-001">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Department</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editDeptModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editForm" method="POST" class="modal-content">
            @csrf @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Edit Department</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Department Name</label>
                    <input type="text" id="editName" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Code (Optional)</label>
                    <input type="text" id="editCode" name="code" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
    function editDept(id, name, code) {
        // Populate the modal fields
        document.getElementById('editName').value = name;
        document.getElementById('editCode').value = code || '';
        
        // Update the form action URL dynamically
        document.getElementById('editForm').action = `/admin/departments/${id}`;
        
        // Show the modal using Bootstrap API
        new bootstrap.Modal(document.getElementById('editDeptModal')).show();
    }
</script>
@endsection