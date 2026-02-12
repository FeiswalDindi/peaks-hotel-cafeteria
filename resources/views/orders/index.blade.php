@extends('layouts.app') {{-- Assuming you have a layout, or use standard HTML structure --}}

@section('content')
<div class="container py-5">
    <h3 class="fw-bold mb-4" style="color: #192C57;">My Order History</h3>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3">Date</th>
                            <th>Order #</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Receipt</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td class="ps-4 text-muted">{{ $order->created_at->format('d M Y') }}</td>
                            <td class="fw-bold">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</td>
                            <td>
                                <span class="d-inline-block text-truncate" style="max-width: 150px;">
                                    @foreach($order->items as $item)
                                        {{ $item->quantity }}x {{ $item->menu_name }}, 
                                    @endforeach
                                </span>
                            </td>
                            <td class="fw-bold">KES {{ number_format($order->total_amount) }}</td>
                            <td>
                                @if($order->mpesa_paid > 0)
                                    <span class="badge bg-success bg-opacity-10 text-success">M-Pesa: {{ $order->mpesa_code }}</span>
                                @else
                                    <span class="badge bg-primary bg-opacity-10 text-primary">Wallet Only</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-success">Paid</span>
                            </td>
                            <td>
                                <a href="{{ route('receipt.show', $order->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                                    <i class="fas fa-receipt"></i> View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">You haven't ordered anything yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="mt-4">
        {{ $orders->links() }}
    </div>
</div>


@include('layouts.footer')
@endsection