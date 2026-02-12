@extends('layouts.admin')

@section('header', 'Edit Menu Item')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold" style="color: #192C57;">Modify Dish</h4>
                    <a href="{{ route('admin.menus.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                        <i class="fas fa-times me-1"></i> Cancel
                    </a>
                </div>

                <form action="{{ route('admin.menus.update', $menu->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase">Item Name</label>
                        <input type="text" name="name" class="form-control form-control-lg rounded-3" 
                               value="{{ $menu->name }}" required>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold small text-uppercase">Price (KES)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">/=</span>
                                <input type="number" name="price" class="form-control" 
                                       value="{{ $menu->price }}" required>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold small text-uppercase">Quantity In Stock</label>
                            <input type="number" name="quantity" class="form-control" 
                                   value="{{ $menu->quantity }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold small text-uppercase">Category</label>
                            <select name="category_id" class="form-select" required>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ $menu->category_id == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase">Description</label>
                        <textarea name="description" class="form-control rounded-3" rows="3">{{ $menu->description }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase">Update Image</label>
                        @if($menu->image)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $menu->image) }}" class="rounded shadow-sm" width="100">
                                <small class="text-muted d-block mt-1">Current image shown above</small>
                            </div>
                        @endif
                        <input type="file" name="image" class="form-control">
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm" 
                                style="background-color: #192C57; border: none; border-radius: 50px;">
                            UPDATE DISH <i class="fas fa-check-circle ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection