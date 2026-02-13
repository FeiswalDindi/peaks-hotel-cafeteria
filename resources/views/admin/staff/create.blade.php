@extends('layouts.admin')

@section('header')
<div class="d-flex align-items-center">
    <a href="{{ route('admin.staff.index') }}" class="btn btn-sm btn-light border rounded-circle me-3 shadow-sm">
        <i class="fas fa-arrow-left"></i>
    </a>
    <span class="fw-bold">Register New Staff Member</span>
</div>
@endsection

@section('content')
<div class="card border-0 shadow-sm rounded-4" style="max-width: 800px;">
    <div class="card-body p-4">
        @if ($errors->any())
            <div class="alert alert-danger pb-0 rounded-4 border-0 shadow-sm mb-4">
                <ul class="mb-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.staff.store') }}" method="POST">
            @csrf

            <div class="row g-3">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold text-muted small">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="e.g., John Doe" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold text-muted small">Email Address <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" placeholder="staff@kcau.ac.ke" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold text-muted small">Staff Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" name="password" id="staffPassword" class="form-control" placeholder="Minimum 8 characters" required>
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold text-muted small">Staff ID</label>
                    <input type="text" name="staff_number" class="form-control" placeholder="e.g., STF-009">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold text-muted small">Department</label>
                    <select name="department_id" class="form-select">
                        <option value="">No Department (Guest/Walk-in)</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label fw-bold text-muted small">Daily Wallet (KES)</label>
                    <input type="number" name="daily_allocation" class="form-control" value="0" min="0">
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-bold text-muted small">Starting Balance</label>
                    <input type="number" name="wallet_balance" class="form-control" value="0" min="0">
                </div>
            </div>

            <hr class="my-4 opacity-25">

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.staff.index') }}" class="btn btn-light border px-4">Cancel</a>
                <button type="submit" class="btn text-white px-4" style="background-color: #192C57;">
                    <i class="fas fa-user-plus me-2"></i> Register Staff
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('togglePassword').addEventListener('click', function (e) {
        const passwordInput = document.getElementById('staffPassword');
        const icon = document.getElementById('toggleIcon');
        
        // Toggle the type attribute
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        // Toggle the eye / eye-slash icon
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
    });
</script>
@endsection