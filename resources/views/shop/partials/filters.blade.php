<form id="product-search-form" method="GET" action="{{ request()->url() }}" class="row g-2 mb-4">
    <div class="{{ $categories->isNotEmpty() ? 'col-md-8' : 'col-12' }}">
        <label for="product-search" class="visually-hidden">Search products</label>
        <div class="input-group">
            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
            <input id="product-search" type="search" name="q" value="{{ request('q') }}"
                class="form-control" placeholder="Search products..." autocomplete="off">
        </div>
    </div>

    @if ($categories->isNotEmpty())
        <div class="col-md-4">
            <label for="product-category" class="visually-hidden">Category</label>
            <select id="product-category" name="category" class="form-select">
                <option value="">All categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
                @endforeach
            </select>
        </div>
    @endif

    <noscript>
        <div class="col-12"><button type="submit" class="btn btn-dark">Search</button></div>
    </noscript>
</form>
