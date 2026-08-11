<header class="phone-store-header fixed-top bg-white shadow-sm" id="mainHeader">
    <div class="container d-flex align-items-center justify-content-between py-3">
        <a class="navbar-brand fs-4 fw-bold text-dark" href="{{ route('home') }}">PhoneStore</a>

        <div class="search-box flex-fill mx-3 position-relative d-none d-lg-flex">
            <input id="searchInput" type="search" class="form-control rounded-pill border-0 shadow-sm" placeholder="Tìm điện thoại, mẫu mã, hãng..." autocomplete="off">
            <div id="searchSuggestions" class="suggestions-box bg-white border rounded-3 shadow-sm"></div>
        </div>

        <nav class="nav d-none d-lg-flex align-items-center gap-3">
            @foreach(['iphone'=>'iPhone','samsung'=>'Samsung','xiaomi'=>'Xiaomi','oppo'=>'OPPO','vivo'=>'vivo','realme'=>'realme','pixel'=>'Pixel','asus'=>'ASUS'] as $slug => $label)
                <a class="nav-link text-dark px-2" href="{{ route('brand.show', $slug) }}">{{ $label }}</a>
            @endforeach
        </nav>

        <div class="header-actions d-flex align-items-center gap-3">
            <a href="#" class="text-dark icon-btn"><i class="fa-regular fa-heart"></i></a>
            <a href="{{ route('cart.index') }}" class="text-dark icon-btn position-relative"><i class="fa-solid fa-cart-shopping"></i><span class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle" id="cartCount">{{ array_sum(session('cart', [])) }}</span></a>
            @guest
                <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm">Đăng nhập</a>
            @else
                <a href="#" class="btn btn-outline-secondary btn-sm">{{ auth()->user()->name }}</a>
            @endguest
        </div>
    </div>
</header>
