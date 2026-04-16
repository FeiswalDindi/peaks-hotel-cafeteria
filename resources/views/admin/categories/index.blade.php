@extends('layouts.admin')

@section('header', 'Category Management')

@section('content')
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
            <h5 class="card-title text-muted mb-0">All Categories</h5>
            <a href="{{ route('admin.categories.create') }}"
               class="btn btn-primary px-3 px-md-4 shadow-sm w-100 w-md-auto text-center"
               style="background-color: #192C57; border-color: #192C57; border-radius: 50px;">
                <i class="fas fa-plus me-2"></i> Add New Category
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 d-none d-sm-table-cell">Image</th>
                        <th>Name</th>
                        <th class="d-none d-md-table-cell">Items</th>
                        <th class="d-none d-lg-table-cell">Created</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr>
                        <td class="ps-3 d-none d-sm-table-cell">
                            @if($category->image)
                                <img src="{{ asset($category->image) }}"
                                     width="50" height="50"
                                     class="rounded object-fit-cover">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:50px; height:50px;">
                                    <i class="fas fa-image text-muted"></i>
                                </div>
                            @endif
                        </td>

                        <td class="fw-bold text-navy">
                            {{ $category->name }}
                            <small class="d-block d-md-none text-muted">{{ $category->menus->count() }} items</small>
                        </td>

                        <td class="d-none d-md-table-cell">
                            <span class="badge rounded-pill bg-info bg-opacity-10 text-info px-3">
                                {{ $category->menus->count() }} items
                            </span>
                        </td>

                        <td class="d-none d-lg-table-cell text-muted small">
                            {{ $category->created_at->format('M d, Y') }}
                        </td>

                        <td class="text-end pe-3">
                            <div class="d-flex justify-content-end gap-1 gap-md-2">
                                <a href="{{ route('admin.categories.edit', $category->id) }}"
                                   class="btn btn-sm btn-outline-primary border-0 shadow-none"
                                   title="Edit Category">
                                    <i class="fas fa-edit"></i>
                                    <span class="d-none d-md-inline ms-1">Edit</span>
                                </a>

                                <form action="{{ route('admin.categories.destroy', $category->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Are you sure you want to delete this category? This will affect {{ $category->menus->count() }} menu items.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0 shadow-none"
                                            title="Delete Category">
                                        <i class="fas fa-trash"></i>
                                        <span class="d-none d-md-inline ms-1">Delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 py-md-5 text-muted">
                            <i class="fas fa-tags fa-2x fa-3x mb-3 d-block opacity-25"></i>
                            <p class="h6 mb-3">No categories added yet.</p>
                            <a href="{{ route('admin.categories.create') }}" class="btn btn-sm btn-link text-decoration-none d-block d-md-inline">
                                <i class="fas fa-plus me-1"></i> Click here to add your first category
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection