@extends('layouts.main')

@section('title', 'Add Product')

@section('content')

    <div class="card shadow-sm">

        <div class="card-header">
            <h3>Add Product</h3>
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

            <form action="{{ route('admin.items.store') }}" method="POST" enctype="multipart/form-data">

                @csrf

                <div class="mb-3">

                    <label class="form-label">
                        Name
                    </label>

                    <input type="text" name="name" value="{{ old('name') }}"
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
                            <option value="{{ $category }}" @selected(old('category') === $category)>{{ $category }}</option>
                        @endforeach
                    </select>
                    @error('category')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">Product Image</label>
                    <input id="image" type="file" name="image" accept=".jpg,.jpeg,.png,.webp"
                        class="form-control @error('image') is-invalid @enderror">
                    <div class="form-text">JPG, PNG, or WebP. Maximum 2 MB.</div>
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Description
                    </label>

                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4">{{ old('description') }}</textarea>

                    @error('description')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Price
                    </label>

                    <input type="number" name="price" step="0.01" min="0" value="{{ old('price') }}"
                        class="form-control @error('price') is-invalid @enderror">

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

                    <input type="number" name="stock_quantity" min="0" value="{{ old('stock_quantity', 0) }}"
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

                        <option value="active" @selected(old('status') === 'active')>
                            Active
                        </option>

                        <option value="inactive" @selected(old('status') === 'inactive')>
                            Inactive
                        </option>

                    </select>

                    @error('status')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                <button type="submit" class="btn btn-primary">
                    Save Product
                </button>

                <a href="{{ route('admin.items.index') }}" class="btn btn-secondary">
                    Back
                </a>

            </form>

        </div>

    </div>

@endsection
