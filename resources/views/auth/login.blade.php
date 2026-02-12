<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login | Peaks Hotel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
<style>
    body {
        background-color: #eef2f6;
        height: 100vh;
        margin: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        overflow: hidden; /* prevents scrolling */
    }

    .login-card {
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.12);
        overflow: hidden;
        width: 100%;
        max-width: 820px;   /* Smaller width */
        height: 500px;      /* Fixed height to prevent scroll */
        display: flex;
    }

    /* Left Side */
    .login-left {
        background: linear-gradient(135deg, #192C57 0%, #0d1b38 100%);
        color: white;
        padding: 40px;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        position: relative;
    }

    .back-btn {
        position: absolute;
        top: 20px;
        left: 20px;
        color: #CEAA0C;
        text-decoration: none;
        font-weight: bold;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: 0.3s;
    }
    .back-btn:hover { color: #fff; }

    /* Right Side */
    .login-right {
        padding: 40px;
        flex: 1.2;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .form-control {
        padding: 10px 14px;
        border-radius: 8px;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        font-size: 0.9rem;
    }

    .form-control:focus {
        background-color: #fff;
        border-color: #192C57;
        box-shadow: 0 0 0 2px rgba(25, 44, 87, 0.1);
    }

    .btn-login {
        background-color: #192C57;
        color: white;
        font-weight: bold;
        padding: 12px;
        border-radius: 8px;
        width: 100%;
        font-size: 0.95rem;
        transition: 0.3s;
    }

    .btn-login:hover {
        background-color: #CEAA0C;
        color: #192C57;
    }

    .form-label {
        font-weight: 600;
        font-size: 0.8rem;
        margin-bottom: 4px;
    }

    .form-hint {
        font-size: 0.7rem;
        margin-top: 3px;
        display: block;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .login-card {
            flex-direction: column;
            height: auto;
            max-width: 95%;
        }

        body {
            overflow: auto;
        }

        
    }
</style>


</head>
<body>

    <div class="login-card">
        
        <div class="login-left">
            <a href="{{ route('home') }}" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back to Home
            </a>
            
            <i class="fas fa-graduation-cap fa-5x mb-3" style="color: #CEAA0C;"></i>
            
            <h2 class="fw-bold mb-2">KCA University</h2>
            <h5 class="fw-light text-white-50 mb-4">Peaks Hotel Cafeteria</h5>
            <hr class="border-light opacity-25 w-50 my-4">
            <p class="small opacity-75">
                "Access your daily meal allowance and manage your staff wallet securely."
            </p>
        </div>

        <div class="login-right">
            <div class="mb-4">
                <h3 class="fw-bold text-dark">Welcome Back!</h3>
                <p class="text-muted">Please authenticate to continue.</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger d-flex align-items-center mb-4 border-0 shadow-sm" role="alert" style="background-color: #fff5f5; color: #c0392b;">
                    <i class="fas fa-exclamation-circle me-3 fa-lg"></i>
                    <div>
                        <strong>Login Failed</strong>
                        <div class="small">Invalid Credentials. Please try again.</div>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <div class="mb-3">
                    <label class="form-label text-uppercase">Staff ID or Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-id-card text-muted"></i></span>
                        <input type="text" name="email" 
                               class="form-control border-start-0 {{ $errors->has('email') ? 'is-invalid' : '' }}" 
                               placeholder="e.g. C1850" 
                               value="{{ old('email') }}" 
                               required autofocus>
                    </div>
                    @if ($errors->has('email'))
                        <span class="text-danger small mt-1"><i class="fas fa-times-circle me-1"></i> {{ $errors->first('email') }}</span>
                    @else
                        <span class="form-hint">Format: <strong>C1234</strong> or <strong>email@kcau.ac.ke</strong></span>
                    @endif
                </div>

                <div class="mb-4">
                    <label class="form-label text-uppercase">Password</label>
                    <div class="input-group password-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock text-muted"></i></span>
                        <input type="password" name="password" id="password"
                               class="form-control border-start-0 {{ $errors->has('password') ? 'is-invalid' : '' }}" 
                               placeholder="••••••••" 
                               required>
                        <i class="fas fa-eye toggle-password" onclick="togglePassword()"></i>
                    </div>
                </div>

                <button type="submit" class="btn btn-login shadow-sm mb-4">
                    LOGIN <i class="fas fa-sign-in-alt ms-2"></i>
                </button>

                <div class="text-center border-top pt-3">
                    <p class="small text-muted mb-1">
                        <i class="fas fa-shield-alt me-1 text-primary"></i> 
                        <strong>Authorized Access Only</strong>
                    </p>
                    <p class="small text-muted mb-0" style="font-size: 0.75rem;">
                        This system is restricted to KCA University Staff & Personel Only. 
                    </p>
                </div>
            </form>
        </div>

    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const icon = document.querySelector('.toggle-password');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>

</body>
</html>