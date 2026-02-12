@extends('layouts.admin')

@section('header', 'Register New Staff')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-5">
                <h4 class="fw-bold kca-navy mb-4">Staff Details</h4>
                
                <form action="{{ route('admin.staff.store') }}" method="POST">
                    @csrf
                    
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-uppercase">Full Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-uppercase">Staff Number</label>
                            <input type="text" name="staff_number" class="form-control" 
                                   placeholder="e.g. C1850" pattern="[A-Z][0-9]{4}" 
                                   title="Must be 1 Capital Letter followed by 4 Digits (e.g. C1850)" required>
                            <div class="form-text text-primary small">
                                <i class="fas fa-info-circle"></i> Format: 1 Capital Letter + 4 Digits
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase">KCA Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="@kcau.ac.ke" required>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-uppercase">Department</label>
                            <select name="department" class="form-select" required>
                                <option value="" selected disabled>Select Department</option>
                                <option value="IT & Computing">IT & Computing</option>
                                <option value="Business">Business</option>
                                <option value="Education">Education</option>
                                <option value="Finance">Finance</option>
                                <option value="Administration">Administration</option>
                                <option value="Research Innovation OutReach">Research Innovation Outreach</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-uppercase">Daily Allowance (KES)</label>
                            <input type="number" name="daily_allocation" class="form-control" value="200" required>
                        </div>
                    </div>

                  <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase">
                            Assign Password <span class="text-muted fw-normal">(Optional)</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fas fa-lock text-muted"></i></span>
                            <input type="text" name="password" class="form-control" 
                                   placeholder="Leave blank to use default: Staff@123">
                        </div>
                        <div class="form-text text-muted small">
                            If you leave this empty, the password will automatically be set to <strong>Staff@123</strong>.
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold" style="background-color: #192C57;">
                        CREATE STAFF ACCOUNT
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection