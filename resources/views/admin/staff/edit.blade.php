@extends('layouts.admin')

@section('header')
<div class="d-flex align-items-center">
    <a href="{{ route('admin.staff.department', $staff->department_id ?? 1) }}" class="btn btn-sm btn-light border rounded-circle me-3 shadow-sm">
        <i class="fas fa-arrow-left"></i>
    </a>
    <span class="fw-bold">Edit Staff Profile: {{ $staff->name }}</span>
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
        
        <form action="{{ route('admin.staff.update', $staff->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold text-muted small">Full Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $staff->name) }}" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold text-muted small">Email Address</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $staff->email) }}" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold text-muted small">Staff ID</label>
                    <input type="text" name="staff_number" class="form-control" value="{{ old('staff_number', $staff->staff_number) }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold text-muted small">Department</label>
                    <select name="department_id" class="form-select">
                        <option value="">No Department</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ $staff->department_id == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold text-muted small">Daily Allocation (KES)</label>
                    <input type="number" name="daily_allocation" class="form-control" value="{{ old('daily_allocation', $staff->daily_allocation) }}" min="0">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold text-muted small">Current Wallet Balance (KES)</label>
                    <input type="number" name="wallet_balance" class="form-control" value="{{ old('wallet_balance', $staff->wallet_balance) }}" min="0">
                </div>
            </div>

            <hr class="my-4 opacity-25">

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ url()->previous() }}" class="btn btn-light border px-4">Cancel</a>
                <button type="submit" class="btn text-white px-4" style="background-color: #192C57;">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection