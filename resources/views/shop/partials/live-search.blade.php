@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('product-search-form');
            const search = document.getElementById('product-search');
            const category = document.getElementById('product-category');
            const grid = document.getElementById('product-grid');
            const count = document.getElementById('product-count');
            let timer;
            let activeRequest;

            if (!form || !search || !grid) {
                return;
            }

            const loadProducts = function () {
                activeRequest?.abort();
                activeRequest = new AbortController();

                const params = new URLSearchParams({ q: search.value.trim() });

                if (category?.value) {
                    params.set('category', category.value);
                }

                fetch(@json(route('shop.search')) + '?' + params.toString(), {
                    headers: { 'Accept': 'application/json' },
                    signal: activeRequest.signal
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Search request failed.');
                        }

                        return response.json();
                    })
                    .then(data => {
                        grid.innerHTML = data.html;
                        count.textContent = data.count + (data.count === 1 ? ' product' : ' products');
                    })
                    .catch(error => {
                        if (error.name !== 'AbortError') {
                            form.submit();
                        }
                    });
            };

            search.addEventListener('input', function () {
                clearTimeout(timer);
                timer = setTimeout(loadProducts, 300);
            });

            category?.addEventListener('change', loadProducts);
        });
    </script>
@endpush
