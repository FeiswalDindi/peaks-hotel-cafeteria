@extends('layouts.admin')

@section('header', 'Department Management')

@section('content')
<style>
    /* 🌟 Mobile Accordion Standard */
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

<div class="container-fluid p-0">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        
        <form action="{{ route('admin.departments.index') }}" method="GET" class="d-flex gap-2 w-100" style="max-width: 400px;">
            <input type="text" name="search" class="form-control rounded-pill px-4 shadow-sm border-0" placeholder="Search departments..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary rounded-pill px-3 shadow-sm" style="background-color: #192C57; border-color: #192C57;">
                <i class="fas fa-search"></i>
            </button>
        </form>
        
        <button class="btn text-white fw-bold rounded-pill px-4 shadow-sm w-100" style="background-color: #0d6efd; max-width: 200px; margin-left: auto; margin-right: auto;" data-bs-toggle="modal" data-bs-target="#addDeptModal">
            <i class="fas fa-plus me-2"></i> New Department
        </button>
    </div>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0 w-100">
                <thead class="bg-light text-secondary">
                    <tr class="text-nowrap">
                        <th class="ps-3 ps-md-4 py-3">Department Name</th>
                        <th class="d-none d-md-table-cell">Code</th>
                        <th>Staff Count</th>
                        <th class="d-none d-md-table-cell text-end pe-4">Actions</th>
                        <th class="d-md-none text-end pe-3">Info</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($departments as $dept)
                    <tr class="mobile-clickable-row" data-bs-toggle="collapse" data-bs-target="#dept-{{ $dept->id }}">
                        <td class="ps-3 ps-md-4 fw-bold text-dark" style="color: #192C57;">{{ $dept->name }}</td>
                        
                        <td class="d-none d-md-table-cell"><span class="badge bg-light text-dark border">{{ $dept->code ?? 'N/A' }}</span></td>
                        
                        <td>
                            <span class="badge bg-info-subtle text-primary rounded-pill px-3">
                                {{ $dept->staff_count }} Staff
                            </span>
                        </td>
                        
                        <td class="d-none d-md-table-cell text-end pe-4">
                            <button class="btn btn-sm btn-outline-secondary me-1 rounded-pill" 
                                    onclick="editDept({{ $dept->id }}, '{{ addslashes($dept->name) }}', '{{ addslashes($dept->code) }}')">
                                <i class="fas fa-edit"></i>
                            </button>
                            
                            <form action="{{ route('admin.departments.destroy', $dept->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this department?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                        
                        <td class="d-md-none text-end pe-3"><i class="fas fa-chevron-down text-muted mobile-expand-icon"></i></td>
                    </tr>

                    <tr id="dept-{{ $dept->id }}" class="collapse d-md-none detail-row">
                        <td colspan="3">
                            <div class="detail-content border-start border-primary border-4">
                                <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                                    <span class="text-muted small">Department Code:</span>
                                    <span class="fw-bold text-dark small">{{ $dept->code ?? 'N/A' }}</span>
                                </div>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-secondary flex-fill rounded-pill" onclick="editDept({{ $dept->id }}, '{{ addslashes($dept->name) }}', '{{ addslashes($dept->code) }}')">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </button>
                                    <form action="{{ route('admin.departments.destroy', $dept->id) }}" method="POST" class="flex-fill d-flex" onsubmit="return confirm('Are you sure you want to delete this department?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger w-100 rounded-pill"><i class="fas fa-trash me-1"></i> Delete</button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <div class="bg-light rounded-circle d-inline-block p-4 mb-3">
                                <i class="fas fa-building fa-2x opacity-50"></i>
                            </div>
                            <p class="mb-0">No departments found. Create one to get started!</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 d-flex justify-content-center py-3">
            {{ $departments->links('pagination::bootstrap-5') }}
        </div>
    </div>

</div>

<div class="modal fade" id="addDeptModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.departments.store') }}" method="POST" class="modal-content rounded-4 border-0 shadow">
            @csrf
            <div class="modal-header border-0 bg-light rounded-top-4">
                <h5 class="modal-title fw-bold" style="color: #192C57;">Add New Department</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">Department Name</label>
                    <input type="text" name="name" class="form-control rounded-3" required placeholder="e.g. IT Services">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">Code (Optional)</label>
                    <input type="text" name="code" class="form-control rounded-3" placeholder="e.g. IT-001">
                </div>
            </div>
            <div class="modal-footer border-0 bg-light rounded-bottom-4">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4" style="background-color: #192C57;">Create Department</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editDeptModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="editForm" method="POST" class="modal-content rounded-4 border-0 shadow">
            @csrf @method('PUT')
            <div class="modal-header border-0 bg-light rounded-top-4">
                <h5 class="modal-title fw-bold" style="color: #192C57;">Edit Department</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">Department Name</label>
                    <input type="text" id="editName" name="name" class="form-control rounded-3" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">Code (Optional)</label>
                    <input type="text" id="editCode" name="code" class="form-control rounded-3">
                </div>
            </div>
            <div class="modal-footer border-0 bg-light rounded-bottom-4">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4" style="background-color: #192C57;">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
    function editDept(id, name, code) {
        document.getElementById('editName').value = name;
        document.getElementById('editCode').value = code || '';
        document.getElementById('editForm').action = `/admin/departments/${id}`;
        new bootstrap.Modal(document.getElementById('editDeptModal')).show();
    }
</script>
@endsection