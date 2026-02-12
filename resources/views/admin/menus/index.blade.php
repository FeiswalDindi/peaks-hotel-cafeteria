@extends('layouts.admin')

@section('header', 'Food Menu Management')

@section('content')
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="card-title text-muted mb-0">All Menu Items</h5>
            <a href="{{ route('admin.menus.create') }}" 
               class="btn btn-primary px-4 shadow-sm" 
               style="background-color: #192C57; border-color: #192C57; border-radius: 50px;">
                <i class="fas fa-plus me-2"></i> Add New Item
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th> 
                        <th>Status</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($menus as $menu)
                    <tr>
                        <td class="ps-3">
                            @if($menu->image)
                                <img src="{{ asset('storage/' . $menu->image) }}" 
                                     width="50" height="50"
                                     class="rounded object-fit-cover">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:50px; height:50px;">
                                    <i class="fas fa-image text-muted"></i>
                                </div>
                            @endif
                        </td>

                        <td class="fw-bold text-navy">{{ $menu->name }}</td>

                        <td>
                            <span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary px-3">
                                {{ $menu->category->name ?? 'Uncategorized' }}
                            </span>
                        </td>

                        <td class="fw-bold" style="color: #192C57;">
                            KES {{ number_format($menu->price, 2) }}
                        </td>

                        <td>
                            @if($menu->quantity > 5)
                                <span class="badge bg-success bg-opacity-10 text-success px-3">
                                    {{ $menu->quantity }} left
                                </span>
                            @elseif($menu->quantity > 0)
                                <span class="badge bg-warning bg-opacity-10 text-dark px-3">
                                    {{ $menu->quantity }} left
                                </span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger px-3">
                                    Out of Stock
                                </span>
                            @endif
                        </td>

                        <td>
                            @if($menu->is_available && $menu->quantity > 0)
                                <span class="text-success small fw-bold">
                                    <i class="fas fa-check-circle me-1"></i> Available
                                </span>
                            @else
                                <span class="text-danger small fw-bold">
                                    <i class="fas fa-times-circle me-1"></i> Unavailable
                                </span>
                            @endif
                        </td>

                        <td class="text-end pe-3">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.menus.edit', $menu->id) }}" 
                                   class="btn btn-sm btn-outline-primary border-0 shadow-none">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <form action="{{ route('admin.menus.destroy', $menu->id) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Are you sure you want to delete this item?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0 shadow-none">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-utensils fa-3x mb-3 d-block opacity-25"></i>
                            <p class="h6">No food items added yet.</p>
                            <a href="{{ route('admin.menus.create') }}" class="btn btn-sm btn-link text-decoration-none">Click here to add your first dish</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection