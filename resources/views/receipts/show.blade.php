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
        body { background-color: #f4f6f9; font-family: 'Courier New', Courier, monospace; }
        
        .floating-back-btn {
            position: fixed; top: 20px; left: 20px; width: 45px; height: 45px;
            background: #fff; border-radius: 50%; display: flex; align-items: center;
            justify-content: center; color: #192C57; text-decoration: none;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1); transition: all 0.3s ease; z-index: 1000; font-size: 1.2rem;
        }
        .floating-back-btn:hover { background: #192C57; color: #fff; transform: translateX(-3px); }

        .receipt-container { max-width: 380px; margin: 60px auto 50px; background: #fff; padding: 25px 20px; position: relative; box-shadow: 0 10px 25px rgba(0,0,0,0.08); border-radius: 8px; }
        .status-badge { text-align: center; padding: 12px; margin-bottom: 25px; font-weight: bold; color: white; border-radius: 6px; letter-spacing: 1px;}
        .bg-pending { background-color: #dc3545; } 
        .bg-paid { background-color: #198754; } 
        .blur-content { filter: blur(5px); pointer-events: none; user-select: none; }
        
        /* Premium UX Styles */
        .locked-overlay { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; background: rgba(255,255,255,0.95); padding: 30px 20px; border: 2px solid #dc3545; width: 85%; z-index: 10; box-shadow: 0 10px 25px rgba(220,53,69,0.2); border-radius: 12px;}
        
        .pulse-icon { animation: pulse 1.5s infinite; }
        @keyframes pulse { 0% { transform: scale(1); opacity: 1; } 50% { transform: scale(1.1); opacity: 0.7; } 100% { transform: scale(1); opacity: 1; } }
        
        .scanning-bar { width: 100%; height: 4px; background: #ffe5e5; border-radius: 2px; overflow: hidden; position: relative; margin: 15px 0 20px 0; }
        .scanning-bar::after { content: ''; position: absolute; top: 0; left: -50%; width: 50%; height: 100%; background: #dc3545; animation: scan 1.5s infinite ease-in-out; border-radius: 2px; }
        @keyframes scan { 0% { left: -50%; } 100% { left: 100%; } }

        .thank-you-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(25, 44, 87, 0.95); z-index: 9999; align-items: center; justify-content: center; animation: fadeIn 0.3s; }
        .thank-you-card { background: white; padding: 40px 30px; border-radius: 20px; text-align: center; box-shadow: 0 15px 30px rgba(0,0,0,0.3); max-width: 400px; width: 90%; transform: scale(0.9); animation: popIn 0.3s forwards; }
        
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes popIn { to { transform: scale(1); } }
    </style>
</head>
<body>

    <a href="{{ Auth::check() ? route('dashboard') : route('home') }}" class="floating-back-btn" data-html2canvas-ignore="true" title="Go Back">
        <i class="fas fa-chevron-left"></i>
    </a>

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
            
            <div class="locked-overlay rounded" data-html2canvas-ignore="true">
                <i class="fas fa-mobile-alt fa-3x text-danger mb-3 pulse-icon"></i>
                <h5 class="fw-bold text-danger">CHECK YOUR PHONE</h5>
                
                <div class="scanning-bar"></div>
                
                <p class="small text-muted mb-4 fw-bold" id="dynamic-waiting-text">Awaiting M-Pesa PIN...</p>
                
                <form action="{{ route('order.cancel', $order->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger w-100 fw-bold shadow-sm rounded-pill" onclick="return confirm('Are you sure you want to cancel this order?');"><i class="fas fa-times me-1"></i> Cancel Request</button>
                </form>
            </div>
            
        @elseif($order->status == 'cancelled')
            <div class="status-badge bg-pending" style="background-color: #343a40;" data-html2canvas-ignore="true">
                <i class="fas fa-times-circle me-2"></i> ORDER CANCELLED
            </div>
            @if(session('timeout_message'))
                <div class="alert alert-warning text-center small fw-bold mx-2 shadow-sm" data-html2canvas-ignore="true" style="border-radius: 8px; color: #856404; background-color: #fff3cd; border-color: #ffeeba;">
                    <i class="fas fa-exclamation-triangle me-1 text-danger"></i> {{ session('timeout_message') }}
                </div>
            @endif
            
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
            <div class="text-center mt-4 mb-2"><i class="fas fa-barcode fa-3x text-dark opacity-75"></i></div>
        </div>
        
        @if($order->status == 'paid')
        <div class="mt-4 pt-3 border-top" data-html2canvas-ignore="true">
            <button onclick="downloadPDF()" class="btn btn-dark w-100 fw-bold py-2 shadow-sm mb-3 rounded-3">
                <i class="fas fa-file-download me-2"></i> Download Digital Receipt
            </button>
            
            <div class="d-flex gap-2">
                <a href="{{ route('home') }}" class="btn btn-outline-secondary w-50 fw-bold py-2 shadow-sm rounded-3" style="font-size: 0.9rem;">
                    <i class="fas fa-utensils me-1"></i> New Order
                </a>
                @auth
                <a href="{{ route('dashboard') }}" class="btn btn-outline-primary w-50 fw-bold py-2 shadow-sm rounded-3" style="font-size: 0.9rem;">
                    <i class="fas fa-receipt me-1"></i> History
                </a>
                @endauth
            </div>
        </div>
        @endif

        @if($order->status == 'cancelled')
        <div class="mt-4 pt-3 border-top text-center" data-html2canvas-ignore="true">
            <a href="{{ route('cart.index') }}" class="btn btn-warning w-100 fw-bold py-2 mb-2 shadow-sm rounded-3" style="color: #856404;">
                <i class="fas fa-redo-alt me-1"></i> Retry Payment
            </a>
            <a href="{{ route('home') }}" class="btn btn-outline-dark w-100 fw-bold py-2 shadow-sm rounded-3">
                Browse Menu
            </a>
        </div>
        @endif
    </div>

    @if($order->status == 'pending')
    <script>
        // 1. Dynamic Text Cycler to keep user engaged
        const messages = [
            "Awaiting M-Pesa PIN...", 
            "Connecting to Safaricom...", 
            "Verifying transaction...", 
            "Still checking network..."
        ];
        let msgIndex = 0;
        setInterval(() => {
            msgIndex = (msgIndex + 1) % messages.length;
            document.getElementById('dynamic-waiting-text').innerText = messages[msgIndex];
        }, 3500);

        // 2. The Original 3-Second Status Knocker
        setInterval(function() {
            fetch("{{ route('order.status', $order->id) }}")
                .then(response => response.json())
                .then(data => {
                    if(data.status === 'paid' || data.status === 'cancelled') { 
                        window.location.reload(); 
                    }
                }).catch(error => console.error('Poller Error:', error));
        }, 3000);
    </script>
    @endif

    <script>
        function triggerDone() {
            document.getElementById('thankYouOverlay').style.display = 'flex';
            setTimeout(() => { window.location.href = "{{ route('home') }}"; }, 2500);
        }

        function downloadPDF() {
            const element = document.getElementById('receipt-box');
            
            element.style.margin = '0';
            element.style.boxShadow = 'none';
            element.style.borderRadius = '0';
            window.scrollTo(0,0); 
            
            const opt = { 
                margin: 2, 
                filename: 'KCA_Receipt_{{ $order->id }}.pdf', 
                image: { type: 'jpeg', quality: 1 }, 
                html2canvas: { scale: 2, scrollY: 0, useCORS: true }, 
                jsPDF: { unit: 'mm', format: [80, 250], orientation: 'portrait' } 
            };
            
            html2pdf().set(opt).from(element).save().then(() => {
                element.style.margin = '';
                element.style.boxShadow = '';
                element.style.borderRadius = '';
                triggerDone();
            });
        }
    </script>
</body>
</html>