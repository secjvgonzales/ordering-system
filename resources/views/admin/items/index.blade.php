@extends('layouts.main')

@section('title', 'Manage Items')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Manage Items</h2>
            <p class="text-muted mb-0">
                Manage item prices, stock, and availability.
            </p>
        </div>

        <a href="{{ route('admin.items.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i>
            Add Item
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">

            <div class="table-responsive">

                <table id="itemsTable" class="table table-hover align-middle">

                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach ($items as $item)
                            <tr>

                                <td>{{ $item->id }}</td>

                                <td>{{ $item->name }}</td>

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
                text: 'This item will be permanently deleted.',
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
