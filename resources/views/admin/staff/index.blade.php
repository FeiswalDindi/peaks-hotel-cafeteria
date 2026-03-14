@extends('layouts.admin')

@section('header', 'Staff Directory')

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
        .hover-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
    }
</style>

<div class="container-fluid p-0">

    <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-center mb-4 gap-3">
        
        <form action="{{ route('admin.staff.index') }}" method="GET" class="position-relative w-100" style="max-width: 600px;">
            <input type="text" name="search" class="form-control form-control-lg ps-5 rounded-pill shadow-sm border-0" 
                   placeholder="Search for a department or staff..." value="{{ request('search') }}">
            <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-4 text-muted"></i>
        </form>

        <div class="d-flex flex-column flex-md-row justify-content-xl-end align-items-stretch gap-2 w-100">
            <button type="button" class="btn btn-warning fw-bold rounded-pill px-4 shadow-sm w-100 w-md-auto" data-bs-toggle="modal" data-bs-target="#adminOverrideModal" style="color: #192C57;">
                <i class="fas fa-random me-2"></i> Force Override
            </button>
            
            <a href="{{ route('admin.staff.create') }}" class="btn text-white rounded-pill shadow-sm px-4 w-100 w-md-auto text-center" style="background-color: #192C57; padding-top: 0.55rem; padding-bottom: 0.55rem;">
                <i class="fas fa-plus-circle me-2"></i> Add New Staff
            </a>
        </div>
    </div>

    <div class="card shadow-sm mb-4 border-0" style="background-color: #f8f9fa;">
        <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div class="text-center text-md-start">
                <h5 class="mb-1 fw-bold text-dark"><i class="fas fa-wallet text-warning me-2"></i> Daily Wallet Reset (KES 200)</h5>
                <p class="text-muted mb-0" style="font-size: 0.9rem;">
                    Last Reset: 
                    <span class="badge bg-secondary">
                        {{ Cache::has('last_wallet_reset') ? Cache::get('last_wallet_reset')->format('d M Y, h:i A') : 'Never' }}
                    </span>
                </p>
            </div>
            
            <form action="{{ route('admin.staff.reset-allocations') }}" method="POST" class="w-100 w-md-auto" onsubmit="return confirm('Are you sure you want to reset EVERYONE to a KES 200 limit and clear their daily usage?');">
                @csrf
                <button type="submit" class="btn btn-warning fw-bold shadow-sm w-100">
                    <i class="fas fa-sync-alt me-1"></i> Reset All to KES 200
                </button>
            </form>
        </div>
    </div>

    @if(request('search'))
    <div class="mb-5">
        <h5 class="fw-bold kca-navy mb-3">Search Results for "{{ request('search') }}"</h5>
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0 w-100">
                    <thead class="table-light text-secondary">
                        <tr class="text-nowrap">
                            <th class="ps-3 ps-md-4 py-3">Staff Name</th>
                            <th class="d-none d-md-table-cell">Staff ID</th>
                            <th class="d-none d-md-table-cell text-end pe-4">Actions</th>
                            <th class="d-md-none text-end pe-3">Info</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users ?? [] as $user)
                        <tr class="mobile-clickable-row" data-bs-toggle="collapse" data-bs-target="#search-staff-{{ $user->id }}">
                            <td class="ps-3 ps-md-4 fw-bold text-dark">
                                {{ $user->name }}
                                <div class="d-md-none small text-muted fw-normal mt-1">{{ $user->staff_number ?? 'No ID' }}</div>
                            </td>
                            
                            <td class="d-none d-md-table-cell">{{ $user->staff_number ?? 'N/A' }}</td>
                            
                            <td class="d-none d-md-table-cell text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.staff.show', $user->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill shadow-sm" title="View Trends">
                                        <i class="fas fa-chart-pie"></i>
                                    </a>
                                    <a href="{{ route('admin.staff.edit', $user->id) }}" class="btn btn-sm text-white rounded-pill shadow-sm" style="background-color: #CEAA0C;" title="Edit Staff">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.staff.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently remove this staff member?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger rounded-pill shadow-sm" title="Remove Staff">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>

                            <td class="d-md-none text-end pe-3"><i class="fas fa-chevron-down text-muted mobile-expand-icon"></i></td>
                        </tr>

                        <tr id="search-staff-{{ $user->id }}" class="collapse d-md-none detail-row">
                            <td colspan="3">
                                <div class="detail-content border-start border-warning border-4">
                                    <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                                        <span class="text-muted small">Staff ID:</span>
                                        <span class="fw-bold text-dark small">{{ $user->staff_number ?? 'N/A' }}</span>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.staff.show', $user->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill flex-fill"><i class="fas fa-chart-pie me-1"></i> Trends</a>
                                        <a href="{{ route('admin.staff.edit', $user->id) }}" class="btn btn-sm text-white rounded-pill flex-fill" style="background-color: #CEAA0C;"><i class="fas fa-edit me-1"></i> Edit</a>
                                    </div>
                                    <form action="{{ route('admin.staff.destroy', $user->id) }}" method="POST" class="mt-2" onsubmit="return confirm('Are you sure you want to permanently remove this staff member?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill w-100"><i class="fas fa-trash-alt me-1"></i> Remove Staff</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">No staff members found matching your search.</td>
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
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
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
            <a href="{{ route('admin.departments.index') }}" class="btn btn-outline-primary mt-2 rounded-pill px-4">Manage Departments</a>
        </div>
        @endforelse
    </div>

</div>

<div class="modal fade" id="adminOverrideModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-warning text-dark border-0">
                <h5 class="modal-title fw-bold fs-5"><i class="fas fa-exclamation-triangle me-2"></i> Force Transfer Funds</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.wallet.override') }}" method="POST">
                @csrf
                <div class="modal-body p-4 bg-light">
                    <div class="alert alert-danger small border-0 shadow-sm mb-4">
                        <i class="fas fa-info-circle me-1"></i> <strong>Admin Action:</strong> This will forcefully deduct funds from one user and grant them to another. This action will be logged in the ledger.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-danger small">Deduct From (Sender)</label>
                        <select name="sender_id" class="form-select border-danger" required>
                            <option value="" disabled selected>-- Select Staff to Deduct From --</option>
                            @foreach($allStaff as $staff)
                                <option value="{{ $staff->id }}">{{ $staff->name }} (Bal: KES {{ $staff->wallet_balance }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-success small">Give To (Receiver)</label>
                        <select name="receiver_id" class="form-select border-success" required>
                            <option value="" disabled selected>-- Select Staff to Receive --</option>
                            @foreach($allStaff as $staff)
                                <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small" style="color: #192C57;">Amount to Move (KES)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 fw-bold">KES</span>
                            <input type="number" name="amount" class="form-control border-start-0" min="1" placeholder="e.g. 50" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-white">
                    <button type="button" class="btn btn-light rounded-pill w-100 mb-2 w-sm-auto mb-sm-0" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill fw-bold w-100 w-sm-auto">Execute Override <i class="fas fa-bolt ms-1"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection