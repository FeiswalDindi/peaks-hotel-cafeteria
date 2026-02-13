<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Tray | KCA Cafeteria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Figtree', sans-serif; }
        .kca-navy { color: #192C57; }
        .btn-kca { background-color: #192C57; color: white; border: none; }
        .btn-kca:hover { background-color: #0d1a35; color: white; }
        .btn-gold { background-color: #CEAA0C; color: #192C57; font-weight: bold; border: none; transition: 0.3s; }
        .btn-gold:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(206,170,12,0.4); }
        .quantity-input { width: 70px; text-align: center; border: 2px solid #e0e0e0; border-radius: 8px; font-weight: bold; }
        .quantity-input:focus { border-color: #192C57; box-shadow: none; }
    </style>
</head>
<body>

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold kca-navy"><i class="fas fa-shopping-basket me-2"></i>My Food Tray</h2>
            <a href="{{ route('menu.all') }}" class="btn btn-outline-secondary rounded-pill fw-bold">
                <i class="fas fa-arrow-left me-2"></i>Back to Menu
            </a>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="table-responsive">
                        <table class="table mb-0 align-middle table-hover">
                            <thead class="bg-light text-muted">
                                <tr>
                                    <th class="ps-4 py-3">Item</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $total = 0 @endphp
                                @forelse(session('cart', []) as $id => $details)
                                    @php $total += $details['price'] * $details['quantity'] @endphp
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ !empty($details['image']) ? asset('storage/'.$details['image']) : 'https://via.placeholder.com/50' }}" 
                                                     width="55" height="55" class="rounded-3 shadow-sm me-3 object-fit-cover" alt="Item">
                                                <span class="fw-bold text-dark">{{ $details['name'] }}</span>
                                            </div>
                                        </td>
                                        <td class="text-muted">KES {{ number_format($details['price']) }}</td>
                                        <td>
                                            <input type="number" value="{{ $details['quantity'] }}" min="1" 
                                                   class="form-control quantity-input update-cart" data-id="{{ $id }}">
                                        </td>
                                        <td class="fw-bold kca-navy">KES {{ number_format($details['price'] * $details['quantity']) }}</td>
                                        <td class="text-end pe-4">
                                            <button class="btn btn-light text-danger rounded-circle remove-from-cart shadow-sm" data-id="{{ $id }}" style="width: 35px; height: 35px;">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <i class="fas fa-box-open fa-3x text-muted opacity-25 mb-3"></i>
                                            <h5 class="text-muted mb-0">Your tray is empty.</h5>
                                            <a href="{{ route('menu.all') }}" class="btn btn-kca rounded-pill px-4 mt-3">Start Ordering</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 100px;">
                    <h5 class="fw-bold kca-navy mb-4">Order Summary</h5>
                    
                    <div class="d-flex justify-content-between mb-3 text-muted">
                        <span>Total Items:</span>
                        <span class="fw-bold text-dark">{{ count(session('cart', [])) }}</span>
                    </div>
                    
                    @if(Auth::check() && Auth::user()->hasRole('staff'))
                        @php 
                            $wallet = Auth::user()->daily_allocation;
                            $walletUsed = min($total, $wallet);
                            $mpesaExcess = $total - $walletUsed;
                        @endphp
                        
                        <div class="bg-light p-3 rounded-3 border mb-3">
                            <h6 class="fw-bold text-muted small mb-3 text-uppercase">Staff Wallet Allocation</h6>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span class="small text-muted"><i class="fas fa-wallet text-primary me-1"></i> Balance:</span>
                                <span class="small fw-bold">KES {{ number_format($wallet) }}</span>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span class="small text-muted">Covered by Wallet:</span>
                                <span class="small fw-bold text-success">- KES {{ number_format($walletUsed) }}</span>
                            </div>
                            
                            @if($mpesaExcess > 0)
                            <div class="d-flex justify-content-between pt-2 mt-2 border-top border-danger border-opacity-25">
                                <span class="small fw-bold text-danger"><i class="fas fa-mobile-alt me-1"></i> M-Pesa Excess:</span>
                                <span class="small fw-bold text-danger">KES {{ number_format($mpesaExcess) }}</span>
                            </div>
                            @endif
                        </div>
                    @endif
                    <hr class="opacity-10">
                    
                    <div class="d-flex justify-content-between mb-4 mt-2">
                        <span class="h5 fw-bold kca-navy">Grand Total:</span>
                        <span class="h4 fw-bold text-success mb-0">KES {{ number_format($total) }}</span>
                    </div>

                    @if(count(session('cart', [])) > 0)
                        <a href="{{ route('checkout.index') }}" class="btn btn-gold btn-lg w-100 rounded-pill shadow">
                            Proceed to Checkout <i class="fas fa-chevron-right ms-2"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript">
        // Remove Item
        $(".remove-from-cart").click(function (e) {
            e.preventDefault();
            var ele = $(this);
            if(confirm("Remove this item from your tray?")) {
                $.ajax({
                    url: '{{ route('cart.remove') }}',
                    method: "DELETE",
                    data: { _token: '{{ csrf_token() }}', id: ele.attr("data-id") },
                    success: function (response) { window.location.reload(); }
                });
            }
        });

        // 🌟 NEW: Live Update Quantity
        $(".update-cart").change(function (e) {
            e.preventDefault();
            var ele = $(this);
            
            // Prevent entering zero or negative numbers
            if(ele.val() < 1) { ele.val(1); }

            $.ajax({
                url: '{{ route('cart.update') }}',
                method: "patch",
                data: {
                    _token: '{{ csrf_token() }}', 
                    id: ele.attr("data-id"), 
                    quantity: ele.val()
                },
                success: function (response) {
                    window.location.reload();
                }
            });
        });
    </script>
    
    @include('layouts.footer')
</body>
</html>