@extends('layouts.main')

@section('title', 'Edit Product')

@section('content')

    <div class="card shadow-sm">

        <div class="card-header">
            <h3>Edit Product</h3>
        </div>

        <div class="card-body">

            @if ($errors->any())

                <div class="alert alert-danger">

                    <strong>Please fix the following errors:</strong>

                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>

                </div>

            @endif

            <form action="{{ route('admin.items.update', $item) }}" method="POST" enctype="multipart/form-data">

                @csrf
                @method('PUT')

                <div class="mb-3">

                    <label class="form-label">
                        Name
                    </label>

                    <input type="text" name="name" value="{{ old('name', $item->name) }}"
                        class="form-control @error('name') is-invalid @enderror">

                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                <div class="mb-3">
                    <label for="category" class="form-label">Category</label>
                    <select id="category" name="category" class="form-select @error('category') is-invalid @enderror">
                        <option value="">Select a category</option>
                        @foreach (\App\Models\Item::CATEGORIES as $category)
                            <option value="{{ $category }}" @selected(old('category', $item->category) === $category)>{{ $category }}</option>
                        @endforeach
                    </select>
                    @error('category')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">Product Image</label>
                    @if ($item->image_path && file_exists(public_path($item->image_path)))
                        <img src="{{ asset($item->image_path) }}" alt="{{ $item->name }}"
                            class="d-block rounded object-fit-cover mb-3" width="180" height="180">
                    @else
                        <div class="d-flex align-items-center justify-content-center bg-light rounded mb-3"
                            style="width: 180px; height: 180px;">
                            <i class="bi bi-image fs-1 text-muted"></i>
                        </div>
                    @endif
                    <input id="image" type="file" name="image" accept=".jpg,.jpeg,.png,.webp"
                        class="form-control @error('image') is-invalid @enderror">
                    <div class="form-text">Leave empty to keep the current image. Maximum 2 MB.</div>
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Description
                    </label>

                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4">{{ old('description', $item->description) }}</textarea>

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Price
                    </label>

                    <input type="number" name="price" step="0.01" min="0"
                        value="{{ old('price', $item->price) }}" class="form-control @error('price') is-invalid @enderror">

                    @error('price')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Stock Quantity
                    </label>

                    <input type="number" name="stock_quantity" min="0"
                        value="{{ old('stock_quantity', $item->stock_quantity) }}"
                        class="form-control @error('stock_quantity') is-invalid @enderror">

                    @error('stock_quantity')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Status
                    </label>

                    <select name="status" class="form-select @error('status') is-invalid @enderror">

                        <option value="active" @selected(old('status', $item->status) === 'active')>
                            Active
                        </option>

                        <option value="inactive" @selected(old('status', $item->status) === 'inactive')>
                            Inactive
                        </option>

                    </select>

                </div>

                <button type="submit" class="btn btn-warning">
                    Update Product
                </button>

                <a href="{{ route('admin.items.index') }}" class="btn btn-secondary">
                    Back
                </a>

            </form>

        </div>

    </div>

@endsection
