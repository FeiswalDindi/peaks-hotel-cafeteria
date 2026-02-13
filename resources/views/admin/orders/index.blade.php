@extends('layouts.admin')

@section('header', 'Order Management System')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm p-4 rounded-4 h-100" style="background: #192C57; color: #ffffff;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-1 opacity-75 fw-bold">Today's Total Revenue</p>
                    <h2 class="fw-bold mb-0">KES {{ number_format($totalToday) }}</h2>
                </div>
                <i class="fas fa-chart-line fa-3x opacity-25"></i>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 shadow-sm p-4 rounded-4 h-100" style="background: #CEAA0C; color: #192C57;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-1 fw-bold opacity-75">Most Popular Today</p>
                    <h2 class="fw-bold mb-0">{{ $hotItem->menu_name ?? 'No Orders Yet' }}</h2>
                </div>
                <i class="fas fa-fire fa-3x opacity-25"></i>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-header bg-white py-3 border-0">
        <h5 class="fw-bold mb-0 kca-navy">Master Order Log</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-secondary">
                    <tr>
                        <th class="ps-4">Order ID</th>
                        <th>Customer Type</th>
                        <th>Amount</th>
                        <th>Payment</th>
                        <th>Workflow Status</th> <th class="text-end pe-4">Receipt</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td class="ps-4 fw-bold kca-navy">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td>
                            @if($order->user_id)
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary-subtle p-2 rounded-circle me-2 text-primary" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-user-tie"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $order->user->name }}</div>
                                        <small class="text-muted">Staff</small>
                                    </div>
                                </div>
                            @else
                                <div class="d-flex align-items-center">
                                    <div class="bg-secondary-subtle p-2 rounded-circle me-2 text-secondary" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-walking"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">Guest</div>
                                        <small class="text-muted">{{ $order->phone_number ?? 'Walk-in' }}</small>
                                    </div>
                                </div>
                            @endif
                        </td>
                        <td class="fw-bold text-navy">KES {{ number_format($order->total_amount) }}</td>
                        <td>
                            @if($order->wallet_paid > 0)
                                <span class="badge rounded-pill" style="background: #e7f1ff; color: #0d6efd;"><i class="fas fa-wallet me-1"></i> Wallet</span>
                            @else
                                <span class="badge rounded-pill" style="background: #e6fcf5; color: #0ca678;"><i class="fas fa-mobile-alt me-1"></i> M-Pesa</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <select name="status" onchange="this.form.submit()" class="form-select form-select-sm border-0 bg-light fw-bold rounded-pill">
                                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>🕒 Pending</option>
                                    <option value="paid" {{ $order->status == 'paid' ? 'selected' : '' }}>💰 Paid</option>
                                    <option value="preparing" {{ $order->status == 'preparing' ? 'selected' : '' }}>🍳 Preparing</option>
                                    <option value="ready" {{ $order->status == 'ready' ? 'selected' : '' }}>🔔 Ready</option>
                                    <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>✅ Completed</option>
                                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>❌ Cancelled</option>
                                </select>
                            </form>
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('receipt.show', $order->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                                <i class="fas fa-file-invoice"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4 d-flex justify-content-center">
    {{ $orders->links() }}
</div>
@endsection