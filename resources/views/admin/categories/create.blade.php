@extends('layouts.admin')

@section('header', 'Add New Category')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-3 p-md-4">
                <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-bold">Category Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Breakfast, Lunch, Hot Beverages" required>
                        <small class="text-muted">Enter a unique category name for your menu items.</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Upload Image (Optional)</label>
                        <input type="file" name="image" class="form-control">
                        <small class="text-muted">Upload a category image (JPG, PNG, max 2MB).</small>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary btn-lg" style="background-color: #192C57; border-color: #192C57;">
                            <i class="fas fa-save me-2"></i> Create Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection