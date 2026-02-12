<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Checkout | KCA Cafeteria</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
body {
    background-color: #f4f7f6;
    font-family: 'Figtree', sans-serif;
}
.payment-card {
    border-radius: 20px;
    border: none;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
}
.kca-navy { color: #192C57; }
.bg-kca { background-color: #192C57; color: white; }
</style>
</head>

<body>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
<div class="card border-0 shadow-sm rounded-4 p-4">
                <h4 class="fw-bold mb-4">Finalize Order</h4>

                <div class="d-flex justify-content-between mb-2">
                    <span>Total Bill:</span>
                    <span class="fw-bold">KES {{ number_format($total) }}</span>
                </div>

                @php
                    $walletBalance = 0;
                    $walletCovered = 0;
                    if(Auth::check() && Auth::user()->hasRole('staff')) {
                        $walletBalance = Auth::user()->daily_allocation;
                        $walletCovered = min($total, $walletBalance);
                    }
                    $mpesaToPay = $total - $walletCovered;
                @endphp

                @if($walletCovered > 0)
                <div class="d-flex justify-content-between mb-2 text-primary">
                    <span><i class="fas fa-wallet me-1"></i> Wallet Coverage:</span>
                    <span class="fw-bold">- KES {{ number_format($walletCovered) }}</span>
                </div>
                @endif

                <div class="d-flex justify-content-between pt-3 border-top mb-4">
                    <span class="h5 fw-bold text-danger">M-Pesa Total:</span>
                    <span class="h5 fw-bold text-danger">KES {{ number_format($mpesaToPay) }}</span>
                </div>

                <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm">
                    @csrf
                    
                    @if($mpesaToPay > 0)
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase">M-Pesa Number</label>
                        <div class="input-group">
                            <span class="input-group-text fw-bold bg-light text-dark" style="border: 1px solid #ced4da;">+254</span>
                            
                            <input type="text" name="phone" 
                                   class="form-control form-control-lg" 
                                   placeholder="712345678" 
                                   maxlength="9" 
                                   pattern="\d{9}"
                                   inputmode="numeric"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 9)"
                                   required
                                   style="border-left: 0;">
                        </div>
                        <div class="form-text small">Enter the 9 digits after +254 (e.g., 700123456)</div>
                    </div>
                    @else
                        <div class="alert alert-success small mb-3">
                            <i class="fas fa-check-circle me-1"></i> Your Staff Wallet covers the entire bill. No M-Pesa required.
                        </div>
                    @endif

                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold" style="background-color: #192C57;">
                        {{ $mpesaToPay > 0 ? 'PAY NOW (M-PESA)' : 'CONFIRM ORDER (WALLET)' }} 
                        <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
@include('layouts.footer')
</html>