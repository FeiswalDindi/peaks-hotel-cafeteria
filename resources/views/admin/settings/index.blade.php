@extends('layouts.admin')

@section('header', 'System Settings')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold kca-navy mb-4"><i class="fas fa-user-shield me-2"></i> Admin Security</h5>
                
                <form action="{{ route('admin.settings.password') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Current Password</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">New Password</label>
                        <input type="password" name="new_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Confirm New Password</label>
                        <input type="password" name="new_password_confirmation" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-warning text-dark fw-bold w-100 rounded-pill">
                        Update Password
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 bg-light">
            <div class="card-body p-4 text-center">
                <i class="fas fa-mobile-alt fa-3x text-success mb-3"></i>
                <h5 class="fw-bold">M-Pesa Integration Status</h5>
                <p class="text-muted">Currently using <strong>Simulation Mode</strong>.</p>
                <div class="alert alert-info small text-start">
                    To go live, we will need to update the <strong>.env</strong> file with your:
                    <ul class="mb-0 mt-2">
                        <li>Consumer Key</li>
                        <li>Consumer Secret</li>
                        <li>Passkey</li>
                    </ul>
                </div>
                <button class="btn btn-outline-success disabled rounded-pill">Configuration Locked</button>
            </div>
        </div>
    </div>
</div>
@endsection