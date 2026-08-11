const searchInput = document.getElementById('searchInput');
const suggestions = document.getElementById('searchSuggestions');
let searchTimer = null;

if (searchInput) {
    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimer);
        const query = this.value.trim();
        if (query.length < 2) {
            suggestions.classList.remove('active');
            suggestions.innerHTML = '';
            return;
        }

        searchTimer = setTimeout(() => {
            fetch(`/search/suggest?q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    if (!data.length) {
                        suggestions.classList.remove('active');
                        suggestions.innerHTML = '';
                        return;
                    }
                    suggestions.innerHTML = data.map(item => {
                        const highlighted = item.name.replace(new RegExp(query, 'ig'), match => `<strong>${match}</strong>`);
                        return `
                            <a href="/${item.brandSlug || 'phone'}/${item.slug}" class="suggestion-item d-flex gap-3 align-items-center p-3 text-decoration-none text-dark">
                                <img src="${item.image ? '/storage/'+item.image : 'https://via.placeholder.com/60x60?text=Phone'}" alt="${item.name}">
                                <div>
                                    <div class="fw-semibold">${highlighted}</div>
                                    <div class="text-muted small">${item.brand ? item.brand + ' · ' : ''}${new Intl.NumberFormat('vi-VN').format(item.price)} ₫</div>
                                </div>
                            </a>
                        `;
                    }).join('');
                    suggestions.classList.add('active');
                });
        }, 300);
    });
}

window.addEventListener('scroll', function () {
    const header = document.getElementById('mainHeader');
    if (!header) return;
    if (window.scrollY > 40) {
        header.classList.add('scrolled');
    } else {
        header.classList.remove('scrolled');
    }
});

const cartButtons = document.querySelectorAll('.add-to-cart-btn');
cartButtons.forEach(button => {
    button.addEventListener('click', function (event) {
        event.preventDefault();
        const productId = this.dataset.id;
        fetch('/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({ product_id: productId, quantity: 1 }),
        })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    const cartCount = document.getElementById('cartCount');
                    if (cartCount) {
                        cartCount.innerText = res.count;
                    }
                    alert(res.message);
                }
            });
    });
});

const cartUpdateButtons = document.querySelectorAll('.cart-action-btn');
cartUpdateButtons.forEach(button => {
    button.addEventListener('click', function () {
        const action = this.dataset.action;
        const productId = this.dataset.id;
        const quantityInput = document.querySelector(`#quantity-${productId}`);
        let quantity = parseInt(quantityInput.value, 10);

        if (action === 'decrease') {
            quantity = Math.max(1, quantity - 1);
        }
        if (action === 'increase') {
            quantity += 1;
        }

        fetch('/cart/update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({ product_id: productId, quantity }),
        })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    quantityInput.value = quantity;
                    const totalRow = document.querySelector(`#item-total-${productId}`);
                    const price = parseInt(totalRow.dataset.price, 10);
                    totalRow.innerText = new Intl.NumberFormat('vi-VN').format(price * quantity) + ' ₫';
                    const cartCount = document.getElementById('cartCount');
                    if (cartCount) {
                        cartCount.innerText = res.count;
                    }
                    window.location.reload();
                }
            });
    });
});

const cartRemoveButtons = document.querySelectorAll('.cart-remove-btn');
cartRemoveButtons.forEach(button => {
    button.addEventListener('click', function () {
        const productId = this.dataset.id;
        fetch('/cart/remove', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({ product_id: productId }),
        })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    const cartCount = document.getElementById('cartCount');
                    if (cartCount) {
                        cartCount.innerText = res.count;
                    }
                    window.location.reload();
                }
            });
    });
});
