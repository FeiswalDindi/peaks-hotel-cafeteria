@extends('layouts.admin')

@section('header', 'Food Menu Management')

@section('content')
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
            <h5 class="card-title text-muted mb-0">All Menu Items</h5>
            <a href="{{ route('admin.menus.create') }}"
               class="btn btn-primary px-3 px-md-4 shadow-sm w-100 w-md-auto text-center"
               style="background-color: #192C57; border-color: #192C57; border-radius: 50px;">
                <i class="fas fa-plus me-2"></i> Add New Item
            </a>
        </div>

        <!-- Search Form -->
        <div class="mb-4">
            <form method="GET" action="{{ route('admin.menus.index') }}" class="d-flex gap-2" id="searchForm">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0"
                           placeholder="Search by item name or category (e.g., 'chapati', 'drinks', 'lunch')..."
                           value="{{ request('search') }}" id="searchInput">
                    <button type="submit" class="btn btn-outline-primary">
                        Search
                    </button>
                    @if(request('search'))
                        <a href="{{ route('admin.menus.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    @endif
                </div>
            </form>
            @if(request('search'))
                <div class="mt-2">
                    <small class="text-muted">
                        Found {{ $menus->count() }} menu item{{ $menus->count() !== 1 ? 's' : '' }} matching "{{ request('search') }}"
                    </small>
                </div>
            @endif
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 d-none d-sm-table-cell">Image</th>
                        <th>Name</th>
                        <th class="d-none d-md-table-cell">Category</th>
                        <th class="d-none d-lg-table-cell">Price</th>
                        <th class="d-none d-xl-table-cell">Stock</th>
                        <th class="d-none d-lg-table-cell">Status</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($menus as $menu)
                    <tr>
                        <td class="ps-3 d-none d-sm-table-cell">
                            @if($menu->image)
                                <img src="{{ asset($menu->image) }}" 
                                     width="50" height="50"
                                     class="rounded object-fit-cover">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:50px; height:50px;">
                                    <i class="fas fa-image text-muted"></i>
                                </div>
                            @endif
                        </td>

                        <td class="fw-bold text-navy">
                            {{ $menu->name }}
                            <small class="d-block d-md-none text-muted">
                                @if($menu->categories->count() > 0)
                                    {{ $menu->categories->pluck('name')->join(', ') }}
                                @else
                                    Uncategorized
                                @endif
                                • KES {{ number_format($menu->price, 2) }}
                                @if($menu->quantity > 0)
                                    • {{ $menu->quantity }} left
                                @else
                                    • Out of Stock
                                @endif
                            </small>
                        </td>

                        <td class="d-none d-md-table-cell">
                            @if($menu->categories->count() > 0)
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($menu->categories as $category)
                                        <span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary px-2 py-1 small">
                                            {{ $category->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-muted small">Uncategorized</span>
                            @endif
                        </td>

                        <td class="d-none d-lg-table-cell fw-bold" style="color: #192C57;">
                            KES {{ number_format($menu->price, 2) }}
                        </td>

                        <td class="d-none d-xl-table-cell">
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

                        <td class="d-none d-lg-table-cell">
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
                            <div class="d-flex justify-content-end gap-1 gap-md-2">
                                <a href="{{ route('admin.menus.edit', $menu->id) }}"
                                   class="btn btn-sm btn-outline-primary border-0 shadow-none"
                                   title="Edit Menu Item">
                                    <i class="fas fa-edit"></i>
                                    <span class="d-none d-md-inline ms-1">Edit</span>
                                </a>

                                <form action="{{ route('admin.menus.destroy', $menu->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Are you sure you want to delete this item?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0 shadow-none"
                                            title="Delete Menu Item">
                                        <i class="fas fa-trash"></i>
                                        <span class="d-none d-md-inline ms-1">Delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 py-md-5 text-muted">
                            <i class="fas fa-utensils fa-2x fa-3x mb-3 d-block opacity-25"></i>
                            <p class="h6 mb-3">No food items added yet.</p>
                            <a href="{{ route('admin.menus.create') }}" class="btn btn-sm btn-link text-decoration-none d-block d-md-inline">
                                <i class="fas fa-plus me-1"></i> Click here to add your first dish
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Auto-submit search form on Enter key press
document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        document.getElementById('searchForm').submit();
    }
});
</script>
@endsection