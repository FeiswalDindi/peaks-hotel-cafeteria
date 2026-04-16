@extends('layouts.admin')

@section('header', 'Add New Menu Item')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-10 col-lg-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-3 p-md-4 p-lg-5">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4 gap-2">
                    <div>
                        <h4 class="fw-bold mb-0" style="color: #192C57;">Add New Menu Item</h4>
                        <small class="text-muted">Create a new dish for your cafeteria menu</small>
                    </div>
                    <a href="{{ route('admin.menus.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                        <i class="fas fa-arrow-left me-1"></i> Back to Menu
                    </a>
                </div>

                <form action="{{ route('admin.menus.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase">Item Name</label>
                        <input type="text" name="name" class="form-control form-control-lg rounded-3" placeholder="e.g. Chapati" required>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold small text-uppercase">Price (KES)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">/=</span>
                                <input type="number" name="price" class="form-control" placeholder="e.g. 20" required>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold small text-uppercase">Quantity</label>
                            <input type="number" name="quantity" class="form-control" placeholder="Qty" value="10" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold small text-uppercase">Categories</label>
                            <div class="border rounded p-3 bg-light" style="max-height: 200px; overflow-y: auto;">
                                @foreach($categories as $category)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="categories[]" value="{{ $category->id }}" id="category-{{ $category->id }}">
                                        <label class="form-check-label" for="category-{{ $category->id }}">
                                            {{ $category->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <small class="text-muted">Select one or more categories for this menu item</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase">Description</label>
                        <textarea name="description" class="form-control rounded-3" rows="3"></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase">Upload Image</label>
                        <input type="file" name="image" class="form-control" id="imageInput" accept="image/*">

                        <!-- Image Preview -->
                        <div id="imagePreview" class="d-none mt-2">
                            <div class="p-3 border rounded bg-light">
                                <div class="d-flex align-items-center gap-3">
                                    <img id="previewImg" class="rounded shadow-sm" width="80" height="80" style="object-fit: cover;">
                                    <div>
                                        <small class="text-success fw-bold">Image selected</small>
                                        <small class="text-muted d-block">This image will be uploaded when you save</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <small class="text-muted">Supported formats: JPG, PNG, GIF. Max size: 2MB</small>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm"
                                style="background-color: #192C57; border: none; border-radius: 50px;">
                            <i class="fas fa-plus-circle me-2"></i> SAVE MENU ITEM
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Image preview functionality
document.getElementById('imageInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');

    if (file) {
        // Validate file type
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!validTypes.includes(file.type)) {
            alert('Please select a valid image file (JPG, PNG, or GIF).');
            e.target.value = '';
            preview.classList.add('d-none');
            return;
        }

        // Validate file size (2MB max)
        if (file.size > 2 * 1024 * 1024) {
            alert('Image size must be less than 2MB.');
            e.target.value = '';
            preview.classList.add('d-none');
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.classList.remove('d-none');
        };
        reader.readAsDataURL(file);
    } else {
        preview.classList.add('d-none');
    }
});

// Form validation enhancement
document.querySelector('form').addEventListener('submit', function(e) {
    const checkboxes = document.querySelectorAll('input[name="categories[]"]:checked');
    if (checkboxes.length === 0) {
        e.preventDefault();
        alert('Please select at least one category for this menu item.');
        return false;
    }
});
</script>
@endsection