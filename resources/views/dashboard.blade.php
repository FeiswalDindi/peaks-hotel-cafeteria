@extends('layouts.admin')

@section('header', 'Admin Dashboard')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1">Total Orders</p>
                    <h3 class="fw-bold" style="color: #192C57;">{{ $totalOrders ?? 0 }}</h3>
                </div>
                <div class="icon-box bg-light rounded-circle p-3">
                    <i class="fas fa-shopping-basket fa-2x" style="color: #CEAA0C;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1">Total Revenue</p>
                    <h3 class="fw-bold" style="color: #192C57;">KES {{ number_format($totalRevenue ?? 0) }}</h3>
                </div>
                <div class="icon-box bg-light rounded-circle p-3">
                    <i class="fas fa-coins fa-2x" style="color: #CEAA0C;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <a href="{{ route('admin.feedback.index') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm p-3 mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">New Feedback</p>
                       <h3 class="fw-bold" style="color: #192C57;">{{ \App\Models\Feedback::where('is_read', false)->count() }}</h3>
                    </div>
                    <div class="icon-box bg-light rounded-circle p-3">
                        <i class="fas fa-comment-alt fa-2x" style="color: #198754;"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="mt-4">
    <h5 class="fw-bold text-muted mb-3">Recent Transactions</h5>
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 py-3">Order #</th>
                            <th>User</th>
                            <th>Amount</th>
                            <th>Payment</th>
                            <th>M-Pesa Code</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentOrders as $order)
                        <tr>
                            <td class="ps-4 fw-bold">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $order->user->name ?? 'Guest' }}</td>
                            <td class="fw-bold text-success">KES {{ number_format($order->total_amount) }}</td>
                            <td>
                                @if($order->mpesa_paid > 0)
                                    <span class="badge bg-success">M-Pesa</span>
                                @else
                                    <span class="badge bg-primary">Wallet</span>
                                @endif
                            </td>
                            <td class="text-monospace fw-bold">{{ $order->mpesa_code ?? '-' }}</td>
                            <td class="text-muted small">{{ $order->created_at->diffForHumans() }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Auto-sync Dashboard Data every 30 seconds
    setTimeout(function() {
        window.location.reload();
    }, 30000); 
</script>
@endsection