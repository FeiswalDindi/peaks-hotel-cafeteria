<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #{{ $order->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #e0e0e0; font-family: 'Courier New', Courier, monospace; }
        .receipt-container {
            max-width: 380px;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            position: relative;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .status-badge {
            text-align: center; padding: 10px; margin-bottom: 20px; font-weight: bold; color: white;
        }
        .bg-pending { background-color: #dc3545; } /* Red for pending */
        .bg-paid { background-color: #198754; } /* Green for paid */
        
        .blur-content {
            filter: blur(4px);
            pointer-events: none;
            user-select: none;
        }
        .locked-overlay {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            background: rgba(255,255,255,0.9);
            padding: 20px;
            border: 2px solid #dc3545;
            width: 80%;
            z-index: 10;
        }
    </style>
</head>
<body>

    <div class="receipt-container">
        
        @if($order->status == 'pending')
            <div class="status-badge bg-pending">
                <i class="fas fa-clock me-2"></i> PAYMENT PENDING
            </div>
            
            <div class="text-center mb-3">
                <small class="text-muted">Check your phone for the M-Pesa PIN.</small>
                <div class="spinner-border spinner-border-sm text-danger ms-2" role="status"></div>
            </div>

            <div class="locked-overlay rounded">
                <i class="fas fa-lock fa-3x text-danger mb-3"></i>
                <h5 class="fw-bold text-danger">RECEIPT LOCKED</h5>
                <p class="small text-muted mb-0">Complete payment to download.</p>
             <a href="{{ route('receipt.check', $order->id) }}" class="btn btn-sm btn-outline-dark mt-3">
    <i class="fas fa-sync-alt me-1"></i> Check Status
</a>
            </div>
        @else
            <div class="status-badge bg-paid">
                <i class="fas fa-check-circle me-2"></i> PAID & VERIFIED
            </div>
        @endif

        <div class="{{ $order->status == 'pending' ? 'blur-content' : '' }}">
            <div class="text-center fw-bold h5 mb-1">PEAKS HOTEL CAFETERIA</div>
            <div class="text-center small text-muted mb-3">KCA University Main Campus</div>
            
            <div class="border-bottom border-dark border-2 border-dashed mb-2"></div>
            
            <div class="d-flex justify-content-between small fw-bold mb-1">
                <span>Receipt #:</span>
                <span>{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="d-flex justify-content-between small fw-bold mb-3">
                <span>Date:</span>
                <span>{{ $order->created_at->format('d-M-Y H:i') }}</span>
            </div>

            <table class="table table-sm table-borderless small mb-0">
                @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->quantity }}x {{ $item->menu_name }}</td>
                    <td class="text-end">{{ number_format($item->price * $item->quantity) }}</td>
                </tr>
                @endforeach
            </table>

            <div class="border-top border-dark border-2 border-dashed mt-2 mb-2"></div>

            <div class="d-flex justify-content-between fw-bold h6">
                <span>TOTAL</span>
                <span>KES {{ number_format($order->total_amount) }}</span>
            </div>

            <div class="small mt-2">
                @if($order->wallet_paid > 0)
                <div class="d-flex justify-content-between text-muted">
                    <span>Staff Wallet:</span>
                    <span>-{{ number_format($order->wallet_paid) }}</span>
                </div>
                @endif
                @if($order->mpesa_paid > 0)
                <div class="d-flex justify-content-between text-muted">
                    <span>M-Pesa ({{ $order->mpesa_code ?? 'Pending' }}):</span>
                    <span>{{ number_format($order->mpesa_paid) }}</span>
                </div>
                @endif
            </div>

            <div class="text-center mt-4 mb-3">
                <i class="fas fa-barcode fa-3x"></i>
            </div>
        </div>
        
        @if($order->status == 'paid')
        <div class="mt-3">
            <button onclick="window.print()" class="btn btn-dark w-100 fw-bold py-2">
                <i class="fas fa-download me-2"></i> DOWNLOAD RECEIPT
            </button>
            <a href="{{ route('home') }}" class="btn btn-link w-100 text-muted btn-sm mt-2 text-decoration-none">
                Start New Order
            </a>
        </div>
        @endif

    </div>

</body>
</html>