<div class="row mb-4 align-items-center">
    <div class="col-md-5">
        <div class="card border-primary border-opacity-25 shadow-sm p-3 rounded-4 bg-white d-inline-block text-start" style="min-width: 250px;">
            <span class="text-muted small fw-bold text-uppercase">Total Wallet Flow on Date</span>
            <h3 class="kca-navy fw-bold mb-0 mt-1">KES {{ number_format($totalTransferred ?? 0) }}</h3>
        </div>
    </div>
    <div class="col-md-7 text-md-end mt-3 mt-md-0">
        <form action="{{ route('admin.orders.ledger') }}" method="GET" class="d-inline-block me-2 mb-2 mb-md-0">
            <div class="input-group shadow-sm rounded-pill overflow-hidden border">
                <span class="input-group-text bg-light border-0"><i class="fas fa-calendar-alt text-muted"></i></span>
                <input type="date" name="date" class="form-control border-0" value="{{ $date }}" onchange="this.form.submit()">
            </div>
        </form>
        
        <div class="btn-group shadow-sm rounded-pill overflow-hidden">
            <a href="{{ route('admin.orders.ledger.export', ['date' => $date, 'action' => 'print']) }}" target="_blank" class="btn btn-danger fw-bold px-3">
                <i class="fas fa-print me-1"></i> Print
            </a>
            <a href="{{ route('admin.orders.ledger.export', ['date' => $date, 'action' => 'download']) }}" target="_blank" class="btn btn-dark fw-bold px-3">
                <i class="fas fa-download me-1"></i> Download PDF
            </a>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 border-primary border-opacity-25">
    <div class="card-header bg-white py-3 border-0">
        <h5 class="fw-bold mb-0 kca-navy"><i class="fas fa-exchange-alt me-2 text-warning"></i>Master Wallet Audit Log</h5>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0 w-100">
            <thead class="bg-light text-secondary">
                <tr class="text-nowrap">
                    <th class="ps-3 ps-md-4">Time</th>
                    <th class="d-none d-md-table-cell">Type</th>
                    <th class="d-none d-md-table-cell">Sender (From)</th>
                    <th class="d-none d-md-table-cell">Receiver (To)</th>
                    <th class="text-end pe-3 pe-md-4">Amount</th>
                    <th class="d-md-none text-end pe-3">Info</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $tx)
                <tr class="mobile-clickable-row" data-bs-toggle="collapse" data-bs-target="#adminTx-{{ $tx->id }}">
                    <td class="ps-3 ps-md-4 fw-bold text-dark">{{ $tx->created_at->format('h:i A') }}</td>
                    
                    <td class="d-none d-md-table-cell">
                        @if($tx->type == 'admin_override')
                            <span class="badge bg-warning text-dark"><i class="fas fa-bolt"></i> Override</span>
                        @elseif($tx->type == 'food_purchase')
                            <span class="badge rounded-pill" style="background: #e7f1ff; color: #0d6efd;"><i class="fas fa-hamburger me-1"></i> Meal Purchase</span>
                        @else
                            <span class="badge bg-primary"><i class="fas fa-user-friends"></i> Transfer</span>
                        @endif
                    </td>
                    <td class="d-none d-md-table-cell fw-bold text-danger">{{ $tx->sender_name }}</td>
                    <td class="d-none d-md-table-cell fw-bold text-success">{{ $tx->receiver_name }}</td>
                    <td class="text-end pe-3 pe-md-4 fw-bold kca-navy">KES {{ number_format($tx->amount) }}</td>
                    <td class="d-md-none text-end pe-3"><i class="fas fa-chevron-down text-muted mobile-expand-icon"></i></td>
                </tr>

                <tr id="adminTx-{{ $tx->id }}" class="collapse d-md-none detail-row">
                    <td colspan="4">
                        <div class="detail-content border-start border-warning border-4">
                            <div class="mb-2">
                                @if($tx->type == 'admin_override')
                                    <span class="badge bg-warning text-dark"><i class="fas fa-bolt"></i> Override</span>
                                @elseif($tx->type == 'food_purchase')
                                    <span class="badge" style="background: #e7f1ff; color: #0d6efd;"><i class="fas fa-hamburger me-1"></i> Meal Purchase</span>
                                @else
                                    <span class="badge bg-primary"><i class="fas fa-user-friends"></i> Transfer</span>
                                @endif
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">From:</span>
                                <span class="fw-bold text-danger small">{{ $tx->sender_name }}</span>
                            </div>
                            <div class="d-flex justify-content-between border-top pt-2">
                                <span class="text-muted small">To:</span>
                                <span class="fw-bold text-success small">{{ $tx->receiver_name }}</span>
                            </div>
                            
                            @if($tx->is_order)
                            <div class="mt-3">
                                <a href="{{ route('receipt.show', $tx->order_id) }}" class="btn btn-sm btn-outline-dark w-100 rounded-pill fw-bold">View Meal Receipt</a>
                            </div>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-5 text-muted">No wallet activity on this date.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="d-flex justify-content-center pb-5">
    {{ $transactions->appends(request()->except('tx_page'))->links('pagination::bootstrap-5') }}
</div>