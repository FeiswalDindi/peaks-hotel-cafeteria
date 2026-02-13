<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #{{ $order->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    
    <style>
        body { background-color: #e0e0e0; font-family: 'Courier New', Courier, monospace; }
        .receipt-container { max-width: 380px; margin: 50px auto; background: #fff; padding: 20px; position: relative; box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .status-badge { text-align: center; padding: 10px; margin-bottom: 20px; font-weight: bold; color: white; }
        .bg-pending { background-color: #dc3545; } 
        .bg-paid { background-color: #198754; } 
        .blur-content { filter: blur(4px); pointer-events: none; user-select: none; }
        .locked-overlay { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; background: rgba(255,255,255,0.95); padding: 25px 20px; border: 2px solid #dc3545; width: 85%; z-index: 10; box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        
        /* 🌟 NEW: Thank You Floating Card Styles */
        .thank-you-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(25, 44, 87, 0.9); z-index: 9999; align-items: center; justify-content: center; animation: fadeIn 0.3s; }
        .thank-you-card { background: white; padding: 40px 30px; border-radius: 20px; text-align: center; box-shadow: 0 15px 30px rgba(0,0,0,0.3); max-width: 400px; width: 90%; transform: scale(0.9); animation: popIn 0.3s forwards; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes popIn { to { transform: scale(1); } }
    </style>
</head>
<body>

    <div class="thank-you-overlay" id="thankYouOverlay" data-html2canvas-ignore="true">
        <div class="thank-you-card">
            <i class="fas fa-utensils fa-4x mb-3" style="color: #CEAA0C;"></i>
            <h3 class="fw-bold" style="color: #192C57;">Thank You!</h3>
            <p class="text-muted mb-0">Your meal is being prepared. We appreciate you dining with Peaks Hotel Cafeteria!</p>
            <div class="spinner-border text-warning mt-4" role="status" style="width: 1.5rem; height: 1.5rem;"></div>
        </div>
    </div>

    <div class="receipt-container" id="receipt-box">
        
        @if($order->status == 'pending')
            <div class="status-badge bg-pending" data-html2canvas-ignore="true"><i class="fas fa-clock me-2"></i> PAYMENT PENDING</div>
            <div class="text-center mb-3" data-html2canvas-ignore="true">
                <small class="text-muted fw-bold">Awaiting M-Pesa Confirmation...</small>
                <div class="spinner-border spinner-border-sm text-danger ms-2" role="status"></div>
            </div>
            <div class="locked-overlay rounded" data-html2canvas-ignore="true">
                <i class="fas fa-lock fa-3x text-danger mb-3"></i><h5 class="fw-bold text-danger">RECEIPT LOCKED</h5>
                <p class="small text-muted mb-3">Please enter your M-Pesa PIN on your phone to unlock.</p>
                <form action="{{ route('order.cancel', $order->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger w-100 fw-bold shadow-sm" onclick="return confirm('Are you sure you want to cancel this order?');"><i class="fas fa-times-circle me-1"></i> Cancel Order</button>
                </form>
            </div>
        @elseif($order->status == 'cancelled')
            <div class="status-badge bg-pending" style="background-color: #343a40;" data-html2canvas-ignore="true"><i class="fas fa-times-circle me-2"></i> ORDER CANCELLED</div>
        @else
            <div class="status-badge bg-paid"><i class="fas fa-check-circle me-2"></i> PAID & VERIFIED</div>
        @endif

        <div class="{{ in_array($order->status, ['pending', 'cancelled']) ? 'blur-content' : '' }}">
            <div class="text-center fw-bold h5 mb-1">PEAKS HOTEL CAFETERIA</div>
            <div class="text-center small text-muted mb-3">KCA University Main Campus</div>
            <div class="border-bottom border-dark border-2 border-dashed mb-2"></div>
            <div class="d-flex justify-content-between small fw-bold mb-1"><span>Receipt #:</span><span>{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span></div>
            <div class="d-flex justify-content-between small fw-bold mb-3"><span>Date:</span><span>{{ $order->created_at->format('d-M-Y H:i') }}</span></div>

            <table class="table table-sm table-borderless small mb-0">
                @foreach($order->items as $item)
                <tr><td>{{ $item->quantity }}x {{ $item->menu_name }}</td><td class="text-end">{{ number_format($item->price * $item->quantity) }}</td></tr>
                @endforeach
            </table>
            <div class="border-top border-dark border-2 border-dashed mt-2 mb-2"></div>
            <div class="d-flex justify-content-between fw-bold h6"><span>TOTAL</span><span>KES {{ number_format($order->total_amount) }}</span></div>
            
            <div class="small mt-2">
                @if($order->wallet_paid > 0)<div class="d-flex justify-content-between text-muted"><span>Staff Wallet:</span><span>-{{ number_format($order->wallet_paid) }}</span></div>@endif
                @if($order->mpesa_paid > 0)<div class="d-flex justify-content-between text-muted"><span>M-Pesa ({{ $order->status == 'paid' ? $order->mpesa_code : 'Pending' }}):</span><span>{{ number_format($order->mpesa_paid) }}</span></div>@endif
            </div>
            <div class="text-center mt-4 mb-3"><i class="fas fa-barcode fa-3x"></i></div>
        </div>
        
        @if($order->status == 'paid')
        <div class="mt-3" data-html2canvas-ignore="true">
            <button onclick="triggerDone()" class="btn btn-success w-100 fw-bold py-2 mb-2 shadow-sm" style="background-color: #198754; border: none;">
                <i class="fas fa-check-circle me-1"></i> DONE
            </button>
            <button onclick="downloadPDF()" class="btn btn-dark w-100 fw-bold py-2 shadow-sm">
                <i class="fas fa-file-pdf me-2"></i> DOWNLOAD RECEIPT
            </button>
            <div class="text-center mt-3">
                <a href="{{ route('menu.all') }}" class="text-muted small text-decoration-none"><i class="fas fa-arrow-left me-1"></i> Start New Order</a>
            </div>
        </div>
        @endif

        @if($order->status == 'cancelled')
        <div class="mt-3 text-center" data-html2canvas-ignore="true"><a href="{{ route('menu.all') }}" class="btn btn-dark w-100 fw-bold py-2 shadow-sm">Return to Menu</a></div>
        @endif
    </div>

    @if($order->status == 'pending')
    <script>
        setInterval(function() {
            fetch("{{ route('order.status', $order->id) }}").then(response => response.json()).then(data => {
                    if(data.status === 'paid' || data.status === 'cancelled') { window.location.reload(); }
            }).catch(error => console.error('Poller Error:', error));
        }, 3000);
    </script>
    @endif

    <script>
        // 🌟 NEW: Show Thank You card, wait 3 seconds, redirect.
        function triggerDone() {
            document.getElementById('thankYouOverlay').style.display = 'flex';
            setTimeout(() => { window.location.href = "{{ route('menu.all') }}"; }, 2500);
        }

        // 🌟 NEW: Connected download to triggerDone
        function downloadPDF() {
            const element = document.getElementById('receipt-box');
            const opt = { margin: 10, filename: 'KCA_Receipt_{{ $order->id }}.pdf', image: { type: 'jpeg', quality: 0.98 }, html2canvas: { scale: 2 }, jsPDF: { unit: 'mm', format: 'a6', orientation: 'portrait' } };
            
            // Wait for PDF to generate, save it, THEN show the Thank You card
            html2pdf().set(opt).from(element).save().then(() => {
                triggerDone();
            });
        }
    </script>
</body>
</html>