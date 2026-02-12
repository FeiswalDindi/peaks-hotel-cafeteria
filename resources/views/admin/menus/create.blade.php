@extends('layouts.admin')

@section('header', 'Add New Menu Item')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('admin.menus.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Item Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Chapati" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Price (KES)</label>
                            <input type="number" name="price" class="form-control" placeholder="e.g. 20" required>
                        </div>
                        <div class="col-md-2 mb-3"> <label class="form-label fw-bold">Quantity</label>
    <input type="number" name="quantity" class="form-control" placeholder="Qty" value="10" required>
</div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Category</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">Select Category...</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Need a new category? Run the seeder below.</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Description (Optional)</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Upload Image</label>
                        <input type="file" name="image" class="form-control">
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg" style="background-color: #192C57; border-color: #192C57;">
                            Save Menu Item
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection