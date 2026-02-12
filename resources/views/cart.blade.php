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
        .btn-gold { background-color: #CEAA0C; color: #192C57; font-weight: bold; border: none; }
    </style>
</head>
<body>

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold kca-navy"><i class="fas fa-shopping-basket me-2"></i>My Food Tray</h2>
            <a href="{{ route('home') }}" class="btn btn-outline-secondary rounded-pill">
                <i class="fas fa-arrow-left me-2"></i>Back to Menu
            </a>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="table-responsive">.
                    <table class="table mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Item</th>
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
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                        <img src="{{ !empty($details['image']) ? asset('storage/'.$details['image']) : 'https://via.placeholder.com/50' }}" 
     width="50" height="50" class="rounded me-3" alt="Item">
                                            <span class="fw-bold">{{ $details['name'] }}</span>
                                        </div>
                                    </td>
                                    <td>KES {{ $details['price'] }}</td>
                                    <td>{{ $details['quantity'] }}</td>
                                    <td class="fw-bold">KES {{ $details['price'] * $details['quantity'] }}</td>
                                    <td class="text-end pe-4">
                                        <button class="btn btn-sm btn-outline-danger border-0 remove-from-cart" data-id="{{ $id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <p class="text-muted mb-0">Your tray is empty.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
</div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h5 class="fw-bold kca-navy mb-4">Order Summary</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Items:</span>
                        <span>{{ count(session('cart', [])) }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="h5 fw-bold">Grand Total:</span>
                        <span class="h5 fw-bold text-success">KES {{ number_format($total, 2) }}</span>
                    </div>

                    @if(count(session('cart', [])) > 0)
                        <a href="{{ route('checkout.index') }}" class="btn btn-gold btn-lg w-100 rounded-pill shadow-sm">
                            Proceed to Checkout <i class="fas fa-chevron-right ms-2"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript">
        $(".remove-from-cart").click(function (e) {
            e.preventDefault();
            var ele = $(this);
            if(confirm("Are you sure?")) {
                $.ajax({
                    url: '{{ route('cart.remove') }}',
                    method: "DELETE",
                    data: {
                        _token: '{{ csrf_token() }}', 
                        id: ele.attr("data-id")
                    },
                    success: function (response) {
                        window.location.reload();
                    }
                });
            }
        });
    </script>
    @include('layouts.footer')
</body>
</html>