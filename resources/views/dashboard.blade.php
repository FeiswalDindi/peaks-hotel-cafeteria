@extends('layouts.admin')

@section('header', 'Admin Dashboard')

@section('content')
<style>
    /* 🌟 Mobile Accordion Standard */
    @media (max-width: 768px) {
        .mobile-clickable-row { cursor: pointer; transition: background-color 0.2s; }
        .mobile-clickable-row:active { background-color: #f0f0f0; }
        .mobile-expand-icon { transition: transform 0.3s ease; }
        tr[aria-expanded="true"] .mobile-expand-icon { transform: rotate(180deg); color: #CEAA0C !important; }
        .detail-row td { padding: 0 !important; border: none; }
        .detail-content { 
            padding: 1.25rem; 
            border-left: 4px solid #192C57; 
            background-color: #f8f9fc; 
            box-shadow: inset 0 3px 6px rgba(0,0,0,0.02);
        }
    }
</style>

<div class="row g-3">
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm p-4 h-100 rounded-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1 fw-bold text-uppercase small">Total Orders</p>
                    <h2 class="fw-bold mb-0" style="color: #192C57;" id="stat-orders">{{ $totalOrders ?? 0 }}</h2>
                </div>
                <div class="icon-box rounded-circle p-3" style="background: rgba(206, 170, 12, 0.1);">
                    <i class="fas fa-shopping-basket fa-2x" style="color: #CEAA0C;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm p-4 h-100 rounded-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1 fw-bold text-uppercase small">Total Revenue</p>
                    <h2 class="fw-bold mb-0 text-success" id="stat-revenue">KES {{ number_format($totalRevenue ?? 0) }}</h2>
                </div>
                <div class="icon-box rounded-circle p-3" style="background: rgba(25, 135, 84, 0.1);">
                    <i class="fas fa-coins fa-2x text-success"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-4">
        <a href="{{ route('admin.feedback.index') }}" class="text-decoration-none h-100 d-block">
            <div class="card border-0 shadow-sm p-4 h-100 rounded-4 transition-hover">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 fw-bold text-uppercase small">New Feedback</p>
                       <h2 class="fw-bold mb-0" style="color: #192C57;" id="stat-feedback">{{ $feedbackCount ?? 0 }}</h2>
                    </div>
                    <div class="icon-box rounded-circle p-3" style="background: rgba(25, 44, 87, 0.1);">
                        <i class="fas fa-comment-dots fa-2x" style="color: #192C57;"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="mt-5 mb-4">
    <div class="d-flex justify-content-between align-items-end mb-3">
        <div>
            <h4 class="fw-bold text-dark mb-0"><i class="fas fa-bolt text-warning me-2"></i>Live Transactions</h4>
            <p class="text-muted small mb-0">Updates automatically in real-time.</p>
        </div>
        <div class="spinner-grow spinner-grow-sm text-success" role="status" id="live-indicator" style="opacity: 0.5;">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0 w-100">
                <thead class="bg-light text-secondary">
                    <tr class="text-nowrap">
                        <th class="ps-3 ps-md-4 py-3">Order #</th>
                        <th class="d-none d-md-table-cell">User</th>
                        <th class="d-none d-md-table-cell">Amount</th>
                        <th class="d-none d-md-table-cell">Payment</th>
                        <th class="d-none d-md-table-cell">M-Pesa Code</th>
                        <th class="d-none d-md-table-cell">Time</th>
                        <th class="d-md-none text-end pe-3">Info</th>
                    </tr>
                </thead>
                <tbody id="recent-orders-tbody">
                    @foreach($recentOrders as $order)
                    <tr class="mobile-clickable-row" data-bs-toggle="collapse" data-bs-target="#order-{{ $order->id }}">
                        <td class="ps-3 ps-md-4 fw-bold text-dark">
                            #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                            <div class="d-md-none small text-muted fw-normal mt-1">{{ $order->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="d-none d-md-table-cell">{{ $order->user->name ?? 'Guest' }}</td>
                        <td class="d-none d-md-table-cell fw-bold text-success">KES {{ number_format($order->total_amount) }}</td>
                        <td class="d-none d-md-table-cell">
                            @if($order->mpesa_paid > 0)
                                <span class="badge bg-success">M-Pesa</span>
                            @else
                                <span class="badge bg-primary">Wallet</span>
                            @endif
                        </td>
                        <td class="d-none d-md-table-cell text-monospace fw-bold">{{ $order->mpesa_code ?? '-' }}</td>
                        <td class="d-none d-md-table-cell text-muted small">{{ $order->created_at->diffForHumans() }}</td>
                        <td class="d-md-none text-end pe-3"><i class="fas fa-chevron-down text-muted mobile-expand-icon"></i></td>
                    </tr>

                    <tr id="order-{{ $order->id }}" class="collapse d-md-none detail-row">
                        <td colspan="2">
                            <div class="detail-content border-start border-warning border-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">User:</span>
                                    <span class="fw-bold small">{{ $order->user->name ?? 'Guest' }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">Amount:</span>
                                    <span class="fw-bold text-success small">KES {{ number_format($order->total_amount) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">Payment:</span>
                                    @if($order->mpesa_paid > 0)
                                        <span class="badge bg-success">M-Pesa</span>
                                    @else
                                        <span class="badge bg-primary">Wallet</span>
                                    @endif
                                </div>
                                @if($order->mpesa_paid > 0)
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">M-Pesa Code:</span>
                                    <span class="fw-bold small">{{ $order->mpesa_code ?? '-' }}</span>
                                </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // 🌟 Live AJAX Polling Engine
    function fetchDashboardData() {
        const liveIndicator = document.getElementById('live-indicator');
        liveIndicator.style.opacity = '1'; // Flash the green dot

        fetch("{{ route('admin.dashboard.live') }}", {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            // Update Top Cards
            document.getElementById('stat-orders').innerText = data.totalOrders;
            document.getElementById('stat-revenue').innerText = 'KES ' + data.totalRevenue;
            document.getElementById('stat-feedback').innerText = data.feedbackCount;

            // Rebuild the Orders Table
            let tbody = document.getElementById('recent-orders-tbody');
            let newHtml = '';

            data.recentOrders.forEach(order => {
                let badge = order.mpesa_paid > 0 
                    ? '<span class="badge bg-success">M-Pesa</span>' 
                    : '<span class="badge bg-primary">Wallet</span>';
                
                let codeRow = order.mpesa_paid > 0 
                    ? `<div class="d-flex justify-content-between mb-2"><span class="text-muted small">M-Pesa Code:</span><span class="fw-bold small">${order.mpesa_code}</span></div>` 
                    : '';

                newHtml += `
                    <tr class="mobile-clickable-row" data-bs-toggle="collapse" data-bs-target="#order-${order.id_raw}">
                        <td class="ps-3 ps-md-4 fw-bold text-dark">
                            #${order.id}
                            <div class="d-md-none small text-muted fw-normal mt-1">${order.time}</div>
                        </td>
                        <td class="d-none d-md-table-cell">${order.user}</td>
                        <td class="d-none d-md-table-cell fw-bold text-success">KES ${order.amount}</td>
                        <td class="d-none d-md-table-cell">${badge}</td>
                        <td class="d-none d-md-table-cell text-monospace fw-bold">${order.mpesa_code}</td>
                        <td class="d-none d-md-table-cell text-muted small">${order.time}</td>
                        <td class="d-md-none text-end pe-3"><i class="fas fa-chevron-down text-muted mobile-expand-icon"></i></td>
                    </tr>
                    <tr id="order-${order.id_raw}" class="collapse d-md-none detail-row">
                        <td colspan="2">
                            <div class="detail-content border-start border-warning border-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">User:</span>
                                    <span class="fw-bold small">${order.user}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">Amount:</span>
                                    <span class="fw-bold text-success small">KES ${order.amount}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">Payment:</span>
                                    ${badge}
                                </div>
                                ${codeRow}
                            </div>
                        </td>
                    </tr>
                `;
            });

            tbody.innerHTML = newHtml;

            // Fade out the green dot
            setTimeout(() => { liveIndicator.style.opacity = '0.5'; }, 500);
        })
        .catch(error => console.error("Live Update Failed", error));
    }

    // Fetch immediately and then refresh every 10 seconds
    fetchDashboardData();
    setInterval(fetchDashboardData, 10000);
</script>
@endsection