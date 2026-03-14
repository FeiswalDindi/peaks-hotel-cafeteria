<div class="row mb-4 g-3">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm p-4 rounded-4 h-100" style="background: #192C57; color: #ffffff;">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <p class="mb-0 opacity-75 fw-bold text-uppercase" style="font-size: 0.8rem;">Today's Real M-Pesa Revenue</p>
                <i class="fas fa-money-bill-wave fa-2x opacity-50 text-success"></i>
            </div>
            <h2 class="fw-bold mb-0">KES {{ number_format($totalMpesaToday ?? 0) }}</h2>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 shadow-sm p-4 rounded-4 h-100" style="background: #CEAA0C; color: #192C57;">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <p class="mb-0 fw-bold opacity-75 text-uppercase" style="font-size: 0.8rem;">Most Popular Today</p>
                <i class="fas fa-fire fa-2x opacity-50"></i>
            </div>
            <h3 class="fw-bold mb-0">{{ $hotItem->menu_name ?? 'No Orders Yet' }}</h3>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
    <div class="card-header bg-white py-3 border-0">
        <h5 class="fw-bold mb-0 kca-navy"><i class="fas fa-utensils me-2 text-warning"></i>Master Food Orders</h5>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0 w-100">
            <thead class="table-light text-secondary">
                <tr class="text-nowrap">
                    <th class="ps-3 ps-md-4">Order ID</th>
                    <th class="d-none d-md-table-cell">Customer</th>
                    <th class="d-none d-md-table-cell">Amount</th>
                    <th>Status</th>
                    <th class="d-none d-md-table-cell text-end pe-4">Receipt</th>
                    <th class="d-md-none text-end pe-3">Info</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr class="mobile-clickable-row" data-bs-toggle="collapse" data-bs-target="#adminOrder-{{ $order->id }}">
                    <td class="ps-3 ps-md-4 fw-bold kca-navy">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</td>
                    
                    <td class="d-none d-md-table-cell">
                        @if($order->user_id)
                            <div class="fw-bold text-dark">{{ $order->user->name }}</div>
                            <small class="text-muted">Staff</small>
                        @else
                            <div class="fw-bold text-dark">Guest</div>
                            <small class="text-muted">{{ $order->phone_number ?? 'Walk-in' }}</small>
                        @endif
                    </td>
                    <td class="d-none d-md-table-cell fw-bold" style="color: #192C57;">KES {{ number_format($order->total_amount) }}</td>
                    
                    <td onclick="event.stopPropagation();">
                        <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <select name="status" onchange="this.form.submit()" class="form-select form-select-sm border-0 bg-light fw-bold rounded-pill" style="min-width: 120px;">
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>🕒 Pending</option>
                                <option value="paid" {{ $order->status == 'paid' ? 'selected' : '' }}>💰 Paid</option>
                                <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>✅ Completed</option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>❌ Cancelled</option>
                            </select>
                        </form>
                    </td>

                    <td class="d-none d-md-table-cell text-end pe-4">
                        <a href="{{ route('receipt.show', $order->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill"><i class="fas fa-file-invoice"></i></a>
                    </td>
                    <td class="d-md-none text-end pe-3"><i class="fas fa-chevron-down text-muted mobile-expand-icon"></i></td>
                </tr>

                <tr id="adminOrder-{{ $order->id }}" class="collapse d-md-none detail-row">
                    <td colspan="4">
                        <div class="detail-content">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Customer:</span>
                                <span class="fw-bold text-dark small">{{ $order->user_id ? $order->user->name : 'Guest' }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                                <span class="text-muted small">Amount:</span>
                                <span class="fw-bold text-primary small">KES {{ number_format($order->total_amount) }}</span>
                            </div>
                            <a href="{{ route('receipt.show', $order->id) }}" class="btn btn-sm btn-outline-dark w-100 rounded-pill fw-bold">View Receipt</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-5 text-muted">No orders placed yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="d-flex justify-content-center mb-5">
    {{ $orders->links('pagination::bootstrap-5') }}
</div>