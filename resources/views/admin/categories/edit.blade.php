@extends('layouts.admin')

@section('header', 'Edit Category')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-3 p-md-4 p-lg-5">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4 gap-2">
                    <h4 class="fw-bold mb-0" style="color: #192C57;">Modify Category</h4>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                        <i class="fas fa-times me-1"></i> Cancel
                    </a>
                </div>

                <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase">Category Name</label>
                        <input type="text" name="name" class="form-control form-control-lg rounded-3"
                               value="{{ $category->name }}" required>
                        <small class="text-muted">Enter a unique category name.</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase">Update Image</label>
                        @if($category->image)
                            <div class="mb-2">
                                <img src="{{ asset($category->image) }}" class="rounded shadow-sm" width="100">
                                <small class="text-muted d-block mt-1">Current image shown above</small>
                            </div>
                        @endif
                        <input type="file" name="image" class="form-control">
                        <small class="text-muted">Leave empty to keep current image.</small>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm"
                                style="background-color: #192C57; border: none; border-radius: 50px;">
                            UPDATE CATEGORY <i class="fas fa-check-circle ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection