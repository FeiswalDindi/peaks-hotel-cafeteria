@extends('layouts.admin')

@section('header', 'System Settings')

@section('content')
<style>
    /* Container for the whole settings area */
    .settings-protection-wrapper {
        position: relative;
    }
    
    /* The permanent blurring effect */
    .blur-content {
        filter: blur(1px); /* Adjust this number to make it more or less blurry */
        pointer-events: none; /* Physically stops any clicking or typing */
        user-select: none;    /* Stops them from highlighting text */
    }

    /* The overlay that floats on top */
    .lock-overlay {
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 999; /* Keeps it on top of everything */
        background: rgba(255, 255, 255, 0.15); /* Slight transparent white tint */
        border-radius: 1rem;
    }

    /* The actual message box inside the overlay */
    .lock-message-box {
        background: white;
        padding: 2.5rem;
        border-radius: 1.5rem;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        text-align: center;
        max-width: 250px;
        border: 1px solid #eee;
    }
</style>

<div class="settings-protection-wrapper">
    
    <div class="lock-overlay">
        <div class="lock-message-box">
            <i class="fas fa-lock fa-3x text-danger mb-3"></i>
            <h4 class="fw-bold text-dark">Configuration Locked</h4>
            <p class="text-muted mb-0">System settings are strictly read-only and cannot be modified from the dashboard for security purposes.</p>
        </div>
    </div>

    <div class="row blur-content">
        
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold kca-navy mb-4"><i class="fas fa-user-shield me-2"></i> Admin Security</h5>
                    
                    <form action="{{ route('admin.settings.password') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Current Password</label>
                            <div class="input-group">
                                <input type="password" name="current_password" id="current_password" class="form-control" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="current_password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">New Password</label>
                            <div class="input-group">
                                <input type="password" name="new_password" id="new_password" class="form-control" placeholder="Minimum 8 characters" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="new_password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted">Confirm New Password</label>
                            <div class="input-group">
                                <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="new_password_confirmation">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn text-white fw-bold w-100 rounded-pill py-2 shadow-sm" style="background-color: #CEAA0C;">
                            <i class="fas fa-key me-2"></i> Update Password
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 bg-light">
                <div class="card-body p-4 text-center">
                    <i class="fas fa-mobile-alt fa-3x text-success mb-3"></i>
                    <h5 class="fw-bold" style="color: #192C57;">M-Pesa Integration Status</h5>
                    <p class="text-muted">Currently using <strong>Simulation Mode</strong>.</p>
                    
                    <div class="text-start mb-3">
                        <label class="small fw-bold text-muted">Consumer Key</label>
                        <input type="text" class="form-control form-control-sm mb-2" value="MSBy7Y...Xyz123" disabled>
                        
                        <label class="small fw-bold text-muted">Consumer Secret</label>
                        <input type="password" class="form-control form-control-sm mb-2" value="hidden_secret" disabled>
                    </div>

                    <div class="alert alert-info small text-start border-0 shadow-sm">
                        Configuration is currently managed via the <strong>.env</strong> file. 
                    </div>
                    
                    <button class="btn btn-outline-success disabled rounded-pill fw-bold w-100">
                        <i class="fas fa-check-circle me-1"></i> System Active
                    </button>
                </div>
            </div>
        </div>
        
    </div>
</div>

<script>
    // This script is technically harmless now because the pointer-events: none 
    // stops anyone from clicking the buttons anyway, but it's fine to leave it.
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const inputField = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (inputField.type === 'password') {
                inputField.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                inputField.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
</script>
@endsection