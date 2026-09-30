@extends('layouts.main')

@section('title', 'Manage Products')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Manage Products</h2>
            <p class="text-muted mb-0">
                Manage product prices, stock, images, and availability.
            </p>
        </div>

        <a href="{{ route('admin.items.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i>
            Add Product
        </a>
    </div>

    <form method="GET" class="card card-body shadow-sm mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-5">
                <label for="search" class="form-label">Search</label>
                <input id="search" name="search" value="{{ request('search') }}" class="form-control"
                    placeholder="Name or description">
            </div>
            <div class="col-md-3">
                <label for="category" class="form-label">Category</label>
                <select id="category" name="category" class="form-select">
                    <option value="">All categories</option>
                    @foreach (\App\Models\Item::CATEGORIES as $category)
                        <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="status" class="form-label">Status</label>
                <select id="status" name="status" class="form-select">
                    <option value="">All statuses</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-primary flex-grow-1">Filter</button>
                <a href="{{ route('admin.items.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="card-body">

            <div class="table-responsive">

                <table id="itemsTable" class="table table-hover align-middle">

                    <thead class="table-light">
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach ($items as $item)
                            <tr>

                                <td>
                                    @if ($item->image_path && file_exists(public_path($item->image_path)))
                                        <img src="{{ asset($item->image_path) }}" alt="{{ $item->name }}"
                                            class="rounded object-fit-cover" width="54" height="54">
                                    @else
                                        <div class="rounded bg-light d-flex align-items-center justify-content-center"
                                            style="width: 54px; height: 54px;">
                                            <i class="bi bi-image text-muted"></i>
                                        </div>
                                    @endif
                                </td>

                                <td>{{ $item->name }}</td>

                                <td>{{ $item->category ?? 'Uncategorized' }}</td>

                                <td>
                                    ₱{{ number_format($item->price, 2) }}
                                </td>

                                <td>
                                    {{ $item->stock_quantity }}
                                </td>

                                <td>
                                    @if ($item->status === 'active')
                                        <span class="badge bg-success">
                                            Active
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            Inactive
                                        </span>
                                    @endif
                                </td>

                                <td>

                                    <a href="{{ route('admin.items.show', $item) }}" class="btn btn-primary btn-sm">
                                        View
                                    </a>

                                    <a href="{{ route('admin.items.edit', $item) }}" class="btn btn-warning btn-sm">
                                        Edit
                                    </a>

                                    <form action="{{ route('admin.items.status', $item) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')

                                        <button type="submit" class="btn btn-secondary btn-sm">
                                            {{ $item->status === 'active' ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>

                                    <form id="delete-form-{{ $item->id }}"
                                        action="{{ route('admin.items.destroy', $item) }}" method="POST" class="d-inline">

                                        @csrf
                                        @method('DELETE')

                                        <button type="button" class="btn btn-danger btn-sm"
                                            onclick="confirmDelete({{ $item->id }})">
                                            Delete
                                        </button>

                                    </form>

                                </td>

                            </tr>
                        @endforeach

                        @if ($items->isEmpty())
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No products match these filters.</td>
                            </tr>
                        @endif

                    </tbody>

                </table>

            </div>

        </div>
    </div>

@endsection

@push('scripts')
    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'This product will be permanently deleted.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {

                if (result.isConfirmed) {
                    document
                        .getElementById('delete-form-' + id)
                        .submit();
                }

            });
        }
    </script>
@endpush
