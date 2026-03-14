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
    <span class="fw-bold text-truncate" style="max-width: 250px;">Profile: {{ $staff->name }}</span>
</div>
@endsection

@section('content')
<style>
    @media (max-width: 768px) {
        .mobile-clickable-row { cursor: pointer; transition: background-color 0.2s; }
        .mobile-clickable-row:active { background-color: #f0f0f0; }
        .mobile-expand-icon { transition: transform 0.3s ease; }
        tr[aria-expanded="true"] .mobile-expand-icon { transform: rotate(180deg); color: #CEAA0C !important; }
        .detail-row td { padding: 0 !important; border: none; }
        .detail-content { padding: 1.25rem; border-left: 4px solid #192C57; background-color: #f8f9fc; box-shadow: inset 0 3px 6px rgba(0,0,0,0.02); }
    }
</style>

<div class="row mb-4 g-3">
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 text-center h-100">
            <div class="bg-primary-subtle rounded-circle p-4 d-inline-block mx-auto mb-3 text-primary">
                <i class="fas fa-user-tie fa-3x"></i>
            </div>
            <h4 class="fw-bold" style="color: #192C57;">{{ $staff->name }}</h4>
            <p class="text-muted small mb-0">{{ $staff->department->name ?? 'No Department' }}</p>
            <div class="badge mt-2 px-3 py-2 rounded-pill" style="background-color: #192C57;">ID: {{ $staff->staff_number ?? 'N/A' }}</div>
            
            <hr class="my-4 opacity-25">
            
            <div class="row text-start">
                <div class="col-6 mb-3">
                    <small class="text-muted d-block text-uppercase" style="font-size: 0.7rem;">Current Wallet</small>
                    <span class="fw-bold fs-5 text-primary">KES {{ number_format($staff->wallet_balance) }}</span>
                </div>
                <div class="col-6 mb-3">
                    <small class="text-muted d-block text-uppercase" style="font-size: 0.7rem;">Lifetime Orders</small>
                    <span class="fw-bold fs-5">{{ $staff->orders->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-8">
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <div class="card border-0 shadow-sm p-4 rounded-4 bg-white h-100">
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

            <div class="col-12 col-md-6">
                <div class="card border-0 shadow-sm p-4 rounded-4 text-white h-100" style="background: #198754;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 opacity-75 small fw-bold text-uppercase">Lifetime Value</p>
                            <h3 class="fw-bold mb-0">KES {{ number_format($totalSpent) }}</h3>
                            <small>Staff Contribution</small>
                        </div>
                        <i class="fas fa-hand-holding-usd fa-3x opacity-25"></i>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 p-4 mt-1">
                    <h6 class="fw-bold mb-4" style="color: #192C57;">Payment Method Distribution</h6>
                    <div class="progress" style="height: 30px; border-radius: 15px;">
                        @php 
                            $walletPerc = $totalSpent > 0 ? ($walletTotal / $totalSpent) * 100 : 0;
                            $mpesaPerc = $totalSpent > 0 ? ($mpesaTotal / $totalSpent) * 100 : 100;
                        @endphp
                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $walletPerc }}%" title="Wallet Payments">
                            <span class="d-none d-sm-inline">Wallet: </span>{{ number_format($walletPerc) }}%
                        </div>
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $mpesaPerc }}%" title="M-Pesa Payments">
                            <span class="d-none d-sm-inline">M-Pesa: </span>{{ number_format($mpesaPerc) }}%
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
        <h5 class="fw-bold mb-0" style="color: #192C57;">Personal Order History</h5>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0 w-100">
            <thead class="table-light text-secondary">
                <tr class="text-nowrap">
                    <th class="ps-3 ps-md-4">Order ID</th>
                    <th class="d-none d-md-table-cell">Date</th>
                    <th class="d-none d-md-table-cell">Total</th>
                    <th class="d-none d-md-table-cell">Wallet</th>
                    <th class="d-none d-md-table-cell">M-Pesa</th>
                    <th>Status</th>
                    <th class="d-md-none text-end pe-3">Info</th>
                </tr>
            </thead>
            <tbody>
                @forelse($staff->orders as $order)
                <tr class="mobile-clickable-row" data-bs-toggle="collapse" data-bs-target="#order-{{ $order->id }}">
                    <td class="ps-3 ps-md-4 fw-bold text-dark">
                        #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                        <div class="d-md-none small text-muted fw-normal mt-1">{{ $order->created_at->format('d M, Y') }}</div>
                    </td>
                    <td class="d-none d-md-table-cell">{{ $order->created_at->format('d M, Y') }}</td>
                    <td class="d-none d-md-table-cell fw-bold">KES {{ number_format($order->total_amount) }}</td>
                    <td class="d-none d-md-table-cell text-primary">KES {{ number_format($order->wallet_paid) }}</td>
                    <td class="d-none d-md-table-cell text-success">KES {{ number_format($order->mpesa_paid) }}</td>
                    
                    <td><span class="badge bg-success-subtle text-success px-3 rounded-pill">{{ ucfirst($order->status) }}</span></td>
                    <td class="d-md-none text-end pe-3"><i class="fas fa-chevron-down text-muted mobile-expand-icon"></i></td>
                </tr>

                <tr id="order-{{ $order->id }}" class="collapse d-md-none detail-row">
                    <td colspan="3">
                        <div class="detail-content">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Total Amount:</span>
                                <span class="fw-bold text-dark small">KES {{ number_format($order->total_amount) }}</span>
                            </div>
                            <div class="row g-2 mt-2">
                                <div class="col-6">
                                    <div class="bg-white p-2 rounded-3 border text-center">
                                        <span class="d-block text-muted" style="font-size: 0.6rem; text-transform: uppercase;">Wallet Paid</span>
                                        <span class="fw-bold text-primary small">KES {{ number_format($order->wallet_paid) }}</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-white p-2 rounded-3 border text-center">
                                        <span class="d-block text-muted" style="font-size: 0.6rem; text-transform: uppercase;">M-Pesa Paid</span>
                                        <span class="fw-bold text-success small">KES {{ number_format($order->mpesa_paid) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-5 text-muted">No orders found for this staff member.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection