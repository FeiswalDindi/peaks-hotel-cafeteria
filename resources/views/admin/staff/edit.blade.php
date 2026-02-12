@extends('layouts.admin')

@section('header', 'Edit Staff Member')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-5">
                <div class="d-flex justify-content-between mb-4">
                    <h4 class="fw-bold kca-navy">Update Details</h4>
                    <span class="badge bg-warning text-dark align-self-center">{{ $staff->staff_number }}</span>
                </div>
                
                <form action="{{ route('admin.staff.update', $staff->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-uppercase">Full Name</label>
                            <input type="text" name="name" class="form-control" value="{{ $staff->name }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-uppercase">Staff Number</label>
                            <input type="text" name="staff_number" class="form-control" value="{{ $staff->staff_number }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase">KCA Email</label>
                        <input type="email" name="email" class="form-control" value="{{ $staff->email }}" required>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-uppercase">Department</label>
                            <select name="department" class="form-select" required>
                                <option value="IT & Computing" {{ $staff->department == 'IT & Computing' ? 'selected' : '' }}>IT & Computing</option>
                                <option value="Business" {{ $staff->department == 'Business' ? 'selected' : '' }}>Business</option>
                                <option value="Education" {{ $staff->department == 'Education' ? 'selected' : '' }}>Education</option>
                                <option value="Finance" {{ $staff->department == 'Finance' ? 'selected' : '' }}>Finance</option>
                                <option value="Administration" {{ $staff->department == 'Administration' ? 'selected' : '' }}>Administration</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-uppercase">Daily Allowance</label>
                            <input type="number" name="daily_allocation" class="form-control" value="{{ $staff->daily_allocation }}" required>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="mb-4">
                        <label class="form-label fw-bold text-danger small text-uppercase">Reset Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-danger"><i class="fas fa-lock"></i></span>
                            <input type="text" name="password" class="form-control" placeholder="Leave empty to keep current password">
                        </div>
                        <div class="form-text small">Only type here if you want to change the staff member's password.</div>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.staff.index') }}" class="btn btn-light rounded-pill px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold" style="background-color: #192C57;">
                            UPDATE STAFF
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection