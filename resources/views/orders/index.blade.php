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
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm py-3">
    <div class="container">
        <a class="navbar-brand fw-bold kca-navy" href="{{ route('home') }}" style="font-size: 1.5rem;">
            <i class="fas fa-graduation-cap kca-gold"></i> KCA<span class="kca-gold">U</span>
        </a>
        <div class="d-flex align-items-center gap-3">
            <span class="text-muted small fw-bold d-none d-md-block">Staff Portal: {{ $user->name }}</span>
            <a href="{{ route('home') }}" class="btn btn-outline-dark rounded-pill px-4 fw-bold shadow-sm">
                <i class="fas fa-arrow-left me-2"></i> Back to Menu
            </a>
        </div>
    </div>
</nav>

<div class="container my-5">
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h2 class="fw-bold kca-navy mb-1">My Order History</h2>
            <p class="text-muted">Review your past cafeteria purchases and track your daily allowance.</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <div class="bg-white p-3 rounded-4 shadow-sm border border-primary d-inline-block text-start" style="min-width: 220px;">
                <span class="text-muted small fw-bold d-block mb-1">Today's Wallet Balance</span>
                <h4 class="kca-navy fw-bold mb-0">
                    <i class="fas fa-wallet text-primary me-2"></i> KES {{ number_format($user->daily_allocation, 2) }}
                </h4>
            </div>
        </div>
    </div>

    <div class="card p-0 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary">
                        <tr>
                            <th class="ps-4">Order #</th>
                            <th>Date & Time</th>
                            <th>Items Purchased</th>
                            <th>Wallet Used</th>
                            <th>M-Pesa Used</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Receipt</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td class="ps-4 fw-bold kca-navy">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $order->created_at->format('d M Y') }}</div>
                                <small class="text-muted">{{ $order->created_at->format('h:i A') }}</small>
                            </td>
                            <td>
                                <ul class="list-unstyled mb-0 small text-muted">
                                    @foreach($order->items as $item)
                                        <li>{{ $item->quantity }}x {{ $item->menu_name }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="text-primary fw-bold">KES {{ number_format($order->wallet_paid) }}</td>
                            <td class="text-success fw-bold">KES {{ number_format($order->mpesa_paid) }}</td>
                            <td>
                                @if($order->status == 'paid')
                                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill"><i class="fas fa-check-circle me-1"></i> Paid</span>
                                @elseif($order->status == 'pending')
                                    <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill"><i class="fas fa-clock me-1"></i> Pending</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill"><i class="fas fa-times-circle me-1"></i> cancelled</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('receipt.show', $order->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                                    View <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-box-open fa-3x mb-3 opacity-25"></i>
                                <h5>No orders found</h5>
                                <p>You haven't made any purchases yet.</p>
                                <a href="{{ route('home') }}" class="btn btn-primary bg-navy rounded-pill px-4 mt-2">Go to Menu</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="d-flex justify-content-center mt-4">
        {{ $orders->links('pagination::bootstrap-5') }}
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 mt-5">
    <div class="card-body p-4">
        <h5 class="fw-bold" style="color: #192C57;"><i class="fas fa-comment-dots me-2"></i>Anonymous Suggestion Box</h5>
        <p class="text-muted small">Help us improve! Share your thoughts on today's meals or service. Your identity is strictly anonymous.</p>
        
        <form id="feedbackForm">
            @csrf
            <div class="mb-3">
                <textarea class="form-control border-0 bg-light" id="feedbackMessage" rows="3" placeholder="e.g. The beef was excellent today!" required></textarea>
            </div>
            <button type="submit" class="btn rounded-pill px-4 shadow-sm" style="background: #CEAA0C; color: #192C57; font-weight: bold;">
                Send Anonymous Review <i class="fas fa-paper-plane ms-2"></i>
            </button>
        </form>
    </div>
</div>

<script>
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
        if (!response.ok) throw new Error('Server Error'); // Check for 403 or 500 errors
        return response.json();
    })
    .then(data => {
        alert(data.success);
        document.getElementById('feedbackMessage').value = '';
    })
    .catch(error => {
        alert("Error: Access Denied or Connection Lost."); // Helpful error message
        console.error(error);
    })
    .finally(() => {
        // 🌟 This ALWAYS runs, even if it fails, to reset the button
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
});
</script>

</body>
</html>