<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Order History | Peaks Hotel Cafeteria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Figtree', sans-serif; background-color: #f8f9fa; }
        .kca-navy { color: #192C57; }
        .kca-gold { color: #CEAA0C; }
        .bg-navy { background-color: #192C57; }
        .card { border-radius: 15px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .table > :not(caption) > * > * { padding: 1rem 0.5rem; }
        
        /* 🌟 NEW: Mobile Accordion Styles */
        @media (max-width: 768px) {
            .wallet-box { width: 100% !important; max-width: 100% !important; margin-top: 1rem; }
            .mobile-clickable-row { cursor: pointer; transition: background-color 0.2s; }
            .mobile-clickable-row:active { background-color: #f0f0f0; }
            .mobile-expand-icon { transition: transform 0.3s ease; }
            tr[aria-expanded="true"] .mobile-expand-icon { transform: rotate(180deg); color: #CEAA0C !important; }
            
            /* The sliding dropdown box styling */
            .detail-row td { padding: 0 !important; border: none; }
            .detail-content { 
                padding: 1.25rem; 
                border-left: 4px solid #192C57; 
                background-color: #f8f9fc; 
                box-shadow: inset 0 3px 6px rgba(0,0,0,0.02);
            }
            .detail-content-tx { border-left-color: #CEAA0C; }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm py-3">
    <div class="container">
        <div class="d-flex align-items-center gap-3">
            <a class="navbar-brand fw-bold kca-navy m-0" href="{{ route('home') }}" style="font-size: 1.5rem;">
                <i class="fas fa-graduation-cap kca-gold"></i> KCA<span class="kca-gold">U</span>
            </a>
            <span class="text-muted small fw-bold d-none d-md-block border-start ps-3 ms-2">Staff Portal: {{ $user->name }}</span>
        </div>
        <div class="d-flex align-items-center">
            <a href="{{ route('menu.all') }}" class="btn btn-outline-dark btn-sm rounded-pill px-3 px-md-4 fw-bold shadow-sm">
                <i class="fas fa-arrow-left me-1 me-md-2"></i> <span class="d-none d-sm-inline">Back to Menu</span><span class="d-inline d-sm-none">Menu</span>
            </a>
        </div>
    </div>
</nav>

<div class="container my-4 my-md-5">
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm border-0" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm border-0" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row mb-4 align-items-center">
        <div class="col-md-7 col-lg-8">
            <h2 class="fw-bold kca-navy mb-1 fs-3 fs-md-2">My Order History</h2>
            <p class="text-muted small md-text-base">Review your past cafeteria purchases.</p>
        </div>
        <div class="col-md-5 col-lg-4 text-md-end mt-2 mt-md-0">
            <div class="wallet-box bg-white p-3 rounded-4 shadow-sm border border-primary d-inline-block text-start" style="min-width: 250px;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted small fw-bold">Today's Wallet Balance</span>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill py-0 px-2 fw-bold" data-bs-toggle="modal" data-bs-target="#transferModal" style="font-size: 0.75rem;">
                        <i class="fas fa-exchange-alt"></i> Send
                    </button>
                </div>
                <h4 class="kca-navy fw-bold mb-0 mt-2">
                    <i class="fas fa-wallet text-primary me-2"></i> KES {{ number_format($user->wallet_balance, 2) }}
                </h4>
            </div>
        </div>
    </div>

    <div class="card p-0 overflow-hidden mb-4 border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0 w-100">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th class="ps-3 ps-md-4">Order #</th>
                        <th class="d-none d-md-table-cell">Date & Time</th>
                        <th class="d-none d-md-table-cell">Items Purchased</th>
                        <th class="d-none d-md-table-cell">Wallet Used</th>
                        <th class="d-none d-md-table-cell">M-Pesa Used</th>
                        <th>Status</th>
                        <th class="d-none d-md-table-cell text-end pe-4">Receipt</th>
                        <th class="d-md-none text-end pe-3">Info</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr class="mobile-clickable-row" data-bs-toggle="collapse" data-bs-target="#orderDetails-{{ $order->id }}" aria-expanded="false">
                        <td class="ps-3 ps-md-4 fw-bold kca-navy">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</td>
                        
                        <td class="d-none d-md-table-cell">
                            <div class="fw-bold text-dark">{{ $order->created_at->format('d M Y') }}</div>
                            <small class="text-muted">{{ $order->created_at->format('h:i A') }}</small>
                        </td>
                        <td class="d-none d-md-table-cell">
                            <ul class="list-unstyled mb-0 small text-muted">
                                @foreach($order->items as $item)
                                    <li>{{ $item->quantity }}x {{ $item->menu_name }}</li>
                                @endforeach
                            </ul>
                        </td>
                        <td class="d-none d-md-table-cell text-primary fw-bold">KES {{ number_format($order->wallet_paid) }}</td>
                        <td class="d-none d-md-table-cell text-success fw-bold">KES {{ number_format($order->mpesa_paid) }}</td>
                        
                        <td>
                            @if($order->status == 'paid')
                                <span class="badge bg-success bg-opacity-10 text-success px-2 px-md-3 py-1 py-md-2 rounded-pill"><i class="fas fa-check-circle me-1"></i> Paid</span>
                            @else
                                <span class="badge bg-warning bg-opacity-10 text-warning px-2 px-md-3 py-1 py-md-2 rounded-pill"><i class="fas fa-clock me-1"></i> Pending</span>
                            @endif
                        </td>
                        
                        <td class="d-none d-md-table-cell text-end pe-4">
                            <a href="{{ route('receipt.show', $order->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill">View <i class="fas fa-arrow-right ms-1"></i></a>
                        </td>
                        <td class="d-md-none text-end pe-3">
                            <i class="fas fa-chevron-down text-muted mobile-expand-icon"></i>
                        </td>
                    </tr>
                    
                    <tr id="orderDetails-{{ $order->id }}" class="collapse d-md-none detail-row">
                        <td colspan="3">
                            <div class="detail-content">
                                <div class="d-flex justify-content-between mb-2 pb-2 border-bottom border-light">
                                    <span class="text-muted small">Date & Time:</span>
                                    <span class="fw-bold text-dark small">{{ $order->created_at->format('d M Y, h:i A') }}</span>
                                </div>
                                <div class="mb-3">
                                    <span class="text-muted small d-block mb-1">Items Purchased:</span>
                                    <ul class="list-unstyled mb-0 fw-bold small text-dark">
                                        @foreach($order->items as $item)
                                            <li><i class="fas fa-utensils text-muted me-2" style="font-size: 0.7rem;"></i>{{ $item->quantity }}x {{ $item->menu_name }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <div class="bg-white p-2 rounded-3 border shadow-sm text-center">
                                            <span class="d-block text-muted" style="font-size: 0.65rem; text-transform: uppercase;">Wallet</span>
                                            <span class="fw-bold text-primary small">KES {{ number_format($order->wallet_paid) }}</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="bg-white p-2 rounded-3 border shadow-sm text-center">
                                            <span class="d-block text-muted" style="font-size: 0.65rem; text-transform: uppercase;">M-Pesa</span>
                                            <span class="fw-bold text-success small">KES {{ number_format($order->mpesa_paid) }}</span>
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ route('receipt.show', $order->id) }}" class="btn btn-sm btn-outline-dark w-100 rounded-pill fw-bold">View Full Receipt <i class="fas fa-receipt ms-1"></i></a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No food orders found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="d-flex justify-content-center mb-5">
        {{ $orders->appends(request()->except('orders_page'))->links('pagination::bootstrap-5') }}
    </div>

    <div class="row mb-3 mt-4 mt-md-5 align-items-center">
        <div class="col-12">
            <h3 class="fw-bold kca-navy mb-1 fs-4 fs-md-3"><i class="fas fa-exchange-alt me-2 text-warning"></i>Wallet Transfer History</h3>
            <p class="text-muted small md-text-base">Track funds you have sent to colleagues or received.</p>
        </div>
    </div>

    <div class="card p-0 overflow-hidden mb-4 border-primary border-opacity-25 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0 w-100">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th class="ps-3 ps-md-4">Date</th>
                        <th class="d-none d-md-table-cell">Transaction Type</th>
                        <th class="d-none d-md-table-cell">Details</th>
                        <th class="text-end pe-3 pe-md-4">Amount</th>
                        <th class="d-md-none text-end pe-3">Info</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $tx)
                    <tr class="mobile-clickable-row" data-bs-toggle="collapse" data-bs-target="#txDetails-{{ $tx->id }}" aria-expanded="false">
                        <td class="ps-3 ps-md-4">
                            <div class="fw-bold text-dark">{{ $tx->created_at->format('d M y') }}</div>
                            <small class="text-muted d-none d-md-block">{{ $tx->created_at->format('h:i A') }}</small>
                        </td>
                        
                        <td class="d-none d-md-table-cell">
                            @if($tx->type == 'admin_override')
                                <span class="badge bg-warning text-dark"><i class="fas fa-bolt"></i> Admin Override</span>
                            @else
                                <span class="badge bg-primary"><i class="fas fa-user-friends"></i> Staff Transfer</span>
                            @endif
                        </td>
                        <td class="d-none d-md-table-cell">
                            @if($tx->sender_id == Auth::id())
                                <span class="text-danger fw-bold"><i class="fas fa-arrow-up me-1"></i> Sent to:</span> {{ $tx->receiver->name ?? 'Unknown' }}
                            @elseif($tx->receiver_id == Auth::id())
                                <span class="text-success fw-bold"><i class="fas fa-arrow-down me-1"></i> Received from:</span> {{ $tx->sender->name ?? 'Admin/System' }}
                            @endif
                            <br><small class="text-muted">{{ $tx->description }}</small>
                        </td>
                        
                        <td class="text-end pe-3 pe-md-4">
                            @if($tx->sender_id == Auth::id())
                                <span class="fw-bold text-danger fs-6 fs-md-5">- KES {{ number_format($tx->amount) }}</span>
                            @elseif($tx->receiver_id == Auth::id())
                                <span class="fw-bold text-success fs-6 fs-md-5">+ KES {{ number_format($tx->amount) }}</span>
                            @endif
                        </td>
                        <td class="d-md-none text-end pe-3">
                            <i class="fas fa-chevron-down text-muted mobile-expand-icon"></i>
                        </td>
                    </tr>

                    <tr id="txDetails-{{ $tx->id }}" class="collapse d-md-none detail-row">
                        <td colspan="4">
                            <div class="detail-content detail-content-tx">
                                <div class="mb-3">
                                    <span class="text-muted small d-block mb-1">Time & Type:</span>
                                    <span class="fw-bold small me-2">{{ $tx->created_at->format('h:i A') }}</span>
                                    @if($tx->type == 'admin_override')
                                        <span class="badge bg-warning text-dark px-2"><i class="fas fa-bolt"></i> Admin Override</span>
                                    @else
                                        <span class="badge bg-primary px-2"><i class="fas fa-user-friends"></i> Staff Transfer</span>
                                    @endif
                                </div>
                                <div class="mb-1 p-2 bg-white rounded border shadow-sm">
                                    <span class="text-muted small d-block" style="font-size: 0.65rem; text-transform: uppercase;">Transaction Details</span>
                                    <div class="small mt-1">
                                        @if($tx->sender_id == Auth::id())
                                            <span class="text-danger fw-bold"><i class="fas fa-arrow-up me-1"></i> Sent to:</span> <span class="fw-bold">{{ $tx->receiver->name ?? 'Unknown' }}</span>
                                        @elseif($tx->receiver_id == Auth::id())
                                            <span class="text-success fw-bold"><i class="fas fa-arrow-down me-1"></i> Received from:</span> <span class="fw-bold">{{ $tx->sender->name ?? 'Admin/System' }}</span>
                                        @endif
                                    </div>
                                    @if($tx->description)
                                    <div class="small text-muted border-top mt-2 pt-1 font-monospace" style="font-size: 0.75rem;">
                                        "{{ $tx->description }}"
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="fas fa-receipt fa-2x mb-3 opacity-25"></i>
                            <h6>No transfers yet.</h6>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="d-flex justify-content-center mb-5">
        {{ $transactions->appends(request()->except('tx_page'))->links('pagination::bootstrap-5') }}
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-5">
        <div class="card-body p-3 p-md-4">
            <h5 class="fw-bold" style="color: #192C57;"><i class="fas fa-comment-dots me-2"></i>Anonymous Suggestion Box</h5>
            <p class="text-muted small">Help us improve! Share your thoughts on today's meals or service. Your identity is strictly anonymous.</p>
            
            <form id="feedbackForm">
                @csrf
                <div class="mb-3">
                    <textarea class="form-control border-0 bg-light" id="feedbackMessage" rows="3" placeholder="e.g. The beef was excellent today!" required></textarea>
                </div>
                <button type="submit" class="btn rounded-pill px-4 shadow-sm w-100 w-md-auto" style="background: #CEAA0C; color: #192C57; font-weight: bold;">
                    Send Anonymous Review <i class="fas fa-paper-plane ms-2"></i>
                </button>
            </form>
        </div>
    </div>

</div>

<div class="modal fade" id="transferModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header text-white" style="background-color: #192C57;">
                <h5 class="modal-title fw-bold fs-5"><i class="fas fa-exchange-alt me-2 text-warning"></i> Send Funds</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('wallet.transfer') }}" method="POST">
                @csrf
                <div class="modal-body p-3 p-md-4">
                    <p class="text-muted small mb-4">You can transfer a portion of your KES {{ number_format(Auth::user()->wallet_balance) }} allocation to another registered staff member.</p>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold kca-navy small">Select Colleague</label>
                        <select name="receiver_id" class="form-select border-primary" required>
                            <option value="" disabled selected>-- Choose Staff Member --</option>
                            @foreach($colleagues as $colleague)
                                <option value="{{ $colleague->id }}">{{ $colleague->name }} ({{ $colleague->department ?? 'Staff' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold kca-navy small">Amount to Send (KES)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 fw-bold">KES</span>
                            <input type="number" name="amount" class="form-control border-start-0 border-primary" min="1" max="{{ Auth::user()->wallet_balance }}" placeholder="e.g. 50" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-outline-secondary rounded-pill w-100 mb-2 w-sm-auto mb-sm-0" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning rounded-pill fw-bold w-100 w-sm-auto" style="color: #192C57;">Send Funds <i class="fas fa-paper-plane ms-1"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
if(document.getElementById('feedbackForm')) {
    document.getElementById('feedbackForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = e.target.querySelector('button');
        const originalText = btn.innerHTML;
        
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

        fetch("{{ route('feedback.submit') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ message: document.getElementById('feedbackMessage').value })
        })
        .then(response => {
            if (!response.ok) throw new Error('Server Error'); 
            return response.json();
        })
        .then(data => {
            alert(data.success);
            document.getElementById('feedbackMessage').value = '';
        })
        .catch(error => {
            alert("Error: Access Denied or Connection Lost."); 
            console.error(error);
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    });
}
</script>

</body>
</html>