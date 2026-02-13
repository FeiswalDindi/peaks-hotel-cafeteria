@extends('layouts.admin')

@section('header')
<div class="d-flex align-items-center">
    @if($staff->department_id)
        <a href="{{ route('admin.staff.department', $staff->department_id) }}" class="btn btn-sm btn-light border rounded-circle me-3 shadow-sm">
            <i class="fas fa-arrow-left"></i>
        </a>
    @else
        <a href="{{ route('admin.staff.index') }}" class="btn btn-sm btn-light border rounded-circle me-3 shadow-sm">
            <i class="fas fa-arrow-left"></i>
        </a>
    @endif
    <span class="fw-bold">Staff Analytics Profile: {{ $staff->name }}</span>
</div>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 text-center h-100">
            <div class="bg-primary-subtle rounded-circle p-4 d-inline-block mx-auto mb-3 text-primary">
                <i class="fas fa-user-tie fa-3x"></i>
            </div>
            <h4 class="fw-bold" style="color: #192C57;">{{ $staff->name }}</h4>
            <p class="text-muted small mb-0">{{ $staff->department->name ?? 'No Department' }}</p>
            <div class="badge mt-2 px-3" style="background-color: #192C57;">Staff ID: {{ $staff->staff_number ?? 'N/A' }}</div>
            
            <hr class="my-4 opacity-50">
            
            <div class="row text-start">
                <div class="col-6 mb-3">
                    <small class="text-muted d-block">Current Wallet</small>
                    <span class="fw-bold">KES {{ number_format($staff->daily_allocation) }}</span>
                </div>
                <div class="col-6 mb-3">
                    <small class="text-muted d-block">Lifetime Orders</small>
                    <span class="fw-bold">{{ $staff->orders->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 text-muted fw-bold small text-uppercase">Top Preference</p>
                            <h3 class="fw-bold mb-0" style="color: #192C57;">{{ $favoriteItem->menu_name ?? 'N/A' }}</h3>
                            <small class="text-success">{{ $favoriteItem->total ?? 0 }} total orders</small>
                        </div>
                        <i class="fas fa-heart fa-3x text-danger opacity-25"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-0 shadow-sm p-4 rounded-4 text-white" style="background: #198754;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 opacity-75 small fw-bold">Lifetime Value</p>
                            <h3 class="fw-bold mb-0">KES {{ number_format($totalSpent) }}</h3>
                            <small>Staff Contribution</small>
                        </div>
                        <i class="fas fa-hand-holding-usd fa-3x opacity-25"></i>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 p-4 mt-3">
                    <h6 class="fw-bold mb-4" style="color: #192C57;">Payment Method Distribution</h6>
                    <div class="progress" style="height: 30px; border-radius: 15px;">
                        @php 
                            $walletPerc = $totalSpent > 0 ? ($walletTotal / $totalSpent) * 100 : 0;
                            $mpesaPerc = $totalSpent > 0 ? ($mpesaTotal / $totalSpent) * 100 : 100;
                        @endphp
                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $walletPerc }}%" title="Wallet Payments">
                            Wallet: {{ number_format($walletPerc) }}%
                        </div>
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $mpesaPerc }}%" title="M-Pesa Payments">
                            M-Pesa: {{ number_format($mpesaPerc) }}%
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-3 small text-muted">
                        <span><i class="fas fa-circle text-primary me-1"></i> KES {{ number_format($walletTotal) }}</span>
                        <span><i class="fas fa-circle text-success me-1"></i> KES {{ number_format($mpesaTotal) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden mt-4">
    <div class="card-header bg-white py-3 border-0">
        <h5 class="fw-bold mb-0" style="color: #192C57;">Order History for {{ $staff->name }}</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Order ID</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Wallet</th>
                        <th>M-Pesa</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($staff->orders as $order)
                    <tr>
                        <td class="ps-4 fw-bold">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $order->created_at->format('d M, Y') }}</td>
                        <td class="fw-bold">KES {{ number_format($order->total_amount) }}</td>
                        <td class="text-primary">KES {{ number_format($order->wallet_paid) }}</td>
                        <td class="text-success">KES {{ number_format($order->mpesa_paid) }}</td>
                        <td><span class="badge bg-success-subtle text-success px-3">{{ $order->status }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection