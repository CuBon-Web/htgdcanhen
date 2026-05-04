@extends('layouts.main.master')
@section('title')
    Giỏ hàng của bạn
@endsection
@section('description')
    Giỏ hàng khóa học của bạn
@endsection
@section('image')
    {{ url('' . $setting->logo) }}
@endsection
@section('css')
    <style>
        /* Modern Cart Page Styles */
        .cart-page-modern {
            background: #f8f9fa;
            padding: 40px 0 80px;
            min-height: 70vh;
        }

        .cart-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        .cart-header {
            background: linear-gradient(135deg, #c0121c 0%, #e74c3c 100%);
            color: white;
            padding: 20px 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(192, 18, 28, 0.3);
        }

        .cart-header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
            margin: 0;
            text-align: center;
        }

        .cart-header p {
            text-align: center;
            margin: 10px 0 0;
            opacity: 0.9;
            font-size: 1.1rem;
        }

        .cart-content {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 30px;
            margin-bottom: 30px;
        }

        .cart-items {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        }

        .cart-item {
            display: flex;
            align-items: center;
            padding: 20px 0;
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.3s ease;
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .cart-item:hover {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 0 -20px;
        }

        .cart-item-image {
            width: 100px;
            height: 100px;
            border-radius: 10px;
            overflow: hidden;
            margin-right: 20px;
            flex-shrink: 0;
        }

        .cart-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .cart-item-details {
            flex: 1;
            margin-right: 20px;
        }

        .cart-item-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2c3e50;
            margin: 0 0 8px 0;
            line-height: 1.4;
        }

        .cart-item-price {
            font-size: 1.1rem;
            color: #c0121c;
            font-weight: 600;
            margin: 0 0 8px 0;
        }

        .cart-item-original-price {
            font-size: 0.9rem;
            color: #95a5a6;
            text-decoration: line-through;
            margin-left: 10px;
        }

        .cart-item-discount {
            background: #e74c3c;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
            margin-left: 10px;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            margin: 10px 0;
        }

        .quantity-btn {
            width: 35px;
            height: 35px;
            border: 1px solid #ddd;
            background: white;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1.2rem;
            font-weight: 600;
            color: #666;
        }

        .quantity-btn:hover {
            background: #c0121c;
            color: white;
            border-color: #c0121c;
        }

        .quantity-input {
            width: 60px;
            height: 35px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin: 0 10px;
            font-size: 1rem;
            font-weight: 600;
        }

        .cart-item-total {
            font-size: 1.3rem;
            font-weight: 700;
            color: #2c3e50;
            text-align: right;
            min-width: 120px;
        }

        .remove-item {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            margin-left: 20px;
        }

        .remove-item:hover {
            background: #c0392b;
            transform: translateY(-2px);
        }

        .cart-summary {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            height: fit-content;
            position: sticky;
            top: 20px;
        }

        .summary-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2c3e50;
            margin: 0 0 20px 0;
            text-align: center;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .summary-row:last-child {
            border-bottom: none;
            font-weight: 700;
            font-size: 1.2rem;
            color: #2c3e50;
            margin-top: 10px;
            padding-top: 20px;
            border-top: 2px solid #c0121c;
        }

        .summary-label {
            color: #666;
            font-size: 1rem;
        }

        .summary-value {
            color: #2c3e50;
            font-weight: 600;
        }

        .checkout-btn {
            width: 100%;
            background: linear-gradient(135deg, #c0121c 0%, #e74c3c 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .checkout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(192, 18, 28, 0.4);
        }

        .continue-shopping {
            display: inline-flex;
            align-items: center;
            color: #c0121c;
            text-decoration: none;
            font-weight: 600;
            margin-top: 20px;
            transition: all 0.3s ease;
        }

        .continue-shopping:hover {
            color: #e74c3c;
            transform: translateX(-5px);
        }

        .empty-cart {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        }

        .empty-cart-icon {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 20px;
        }

        .empty-cart h3 {
            color: #2c3e50;
            margin: 0 0 10px 0;
            font-size: 1.5rem;
        }

        .empty-cart p {
            color: #666;
            margin: 0 0 30px 0;
            font-size: 1.1rem;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .cart-content {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .cart-header h1 {
                font-size: 2rem;
            }

            .cart-item {
                flex-direction: column;
                align-items: flex-start;
                padding: 15px 0;
            }

            .cart-item-image {
                width: 80px;
                height: 80px;
                margin-right: 15px;
                margin-bottom: 15px;
            }

            .cart-item-details {
                margin-right: 0;
                width: 100%;
            }

            .cart-item-total {
                text-align: left;
                margin-top: 10px;
            }

            .quantity-controls {
                justify-content: flex-start;
            }
        }

        @media (max-width: 480px) {
            .cart-container {
                padding: 0 10px;
            }

            .cart-header {
                padding: 20px;
            }

            .cart-items,
            .cart-summary {
                padding: 20px;
            }

            .cart-item-image {
                width: 60px;
                height: 60px;
            }
        }
    </style>
@endsection
@section('content')
    <div class="cart-page-modern">
        <div class="cart-container">
            <!-- Cart Header -->
            <div class="cart-header">
                <h1><i class="fas fa-shopping-cart"></i> Giỏ hàng của bạn</h1>
                <p>Xem lại và thanh toán các khóa học đã chọn</p>
            </div>

            @if (count($cart) > 0)
                <div class="cart-content">
                    <!-- Cart Items -->
                    <div class="cart-items">
                        @php
                            $total = 0;
                            $qtyCourse = 0;
                            $qtyDocument = 0;
                            $qtyDethi = 0;
                        @endphp
                        @foreach ($cart as $id => $details)
                            @php
                                $itemTotal = $details['price'] * $details['quantity'];
                                $total += $itemTotal;
                                if($details['type'] == 'product'){
                                    $qtyCourse += $details['quantity'];
                                }
                                if($details['type'] == 'document'){
                                    $qtyDocument += $details['quantity'];
                                }
                                if($details['type'] == 'dethi'){
                                    $qtyDethi += $details['quantity'];
                                }
                            @endphp

                            <div class="cart-item" data-product-id="{{ $details['idpro'] }}">
                                <div class="cart-item-image">
                                    <img src="{{ url('' . $details['image']) }}" alt="{{ $details['name'] }}">
                                </div>

                                <div class="cart-item-details">
                                    <h3 class="cart-item-title">{{ $details['name'] }}</h3>
                                    <div class="cart-item-price">
                                        {{ number_format($details['price']) }}₫
                                        @if ($details['discount'] > 0)
                                            <span
                                                class="cart-item-original-price">{{ number_format($details['discount']) }}₫</span>
                                            <span class="cart-item-discount">-{{ round(($details['discount'] - $details['price']) / $details['discount'] * 100) }}%</span>
                                        @endif
                                    </div>

                                    <div class="quantity-controls">
                                        <button type="button" class="quantity-btn"
                                            onclick="qtyminus({{ $details['idpro'] }})">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="text" class="quantity-input" value="{{ $details['quantity'] }}"
                                            id="quantity-{{ $details['idpro'] }}" min="1" max="99">
                                        <button type="button" class="quantity-btn"
                                            onclick="qtyplus({{ $details['idpro'] }})">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="cart-item-total" id="cartprice-{{ $id }}">
                                    {{ number_format($itemTotal) }}₫
                                </div>
                                <div class="cart-item-action">
                                    <button type="button" class="remove-item"
                                        onclick="removeCart({{ $details['idpro'] }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Cart Summary -->
                    <div class="cart-summary">
                        <h3 class="summary-title">Tóm tắt đơn hàng</h3>

                        <div class="summary-row">
                            <span class="summary-label">Số tài liệu:</span>
                            <span class="summary-value">{{ $qtyDocument }}</span>
                        </div>

                        <div class="summary-row">
                            <span class="summary-label">Số khóa học:</span>
                            <span class="summary-value">{{ $qtyCourse }}</span>
                        </div>

                        <div class="summary-row">
                            <span class="summary-label">Số để thi:</span>
                            <span class="summary-value">{{ $qtyDethi }}</span>
                        </div>

                        <div class="summary-row">
                            <span class="summary-label">Tổng số lượng:</span>
                            <span class="summary-value">{{ $qtyCourse + $qtyDocument }}</span>
                        </div>

                        <div class="summary-row">
                            <span class="summary-label">Tạm tính:</span>
                            <span class="summary-value">{{ number_format($total) }}₫</span>
                        </div>

                        <div class="summary-row">
                            <span class="summary-label">Phí vận chuyển:</span>
                            <span class="summary-value">Liên Hệ</span>
                        </div>

                        <div class="summary-row">
                            <span class="summary-label">Tổng cộng:</span>
                            <span class="summary-value total-price">{{ number_format($total) }}₫</span>
                        </div>

                        <button type="button" class="checkout-btn" onclick="location.href='{{ route('postBill') }}'">
                            <i class="fas fa-credit-card"></i> Thanh toán ngay
                        </button>

                        <a href="{{ route('couseList') }}" class="continue-shopping">
                            <i class="fas fa-arrow-left"></i> Tiếp tục mua sắm
                        </a>
                    </div>
                </div>
            @else
                <!-- Empty Cart -->
                <div class="empty-cart">
                    <div class="empty-cart-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h3>Giỏ hàng trống</h3>
                    <p>Bạn chưa có khóa học nào trong giỏ hàng. Hãy khám phá các khóa học thú vị!</p>
                    <a href="{{ route('couseList') }}" class="checkout-btn">
                        <i class="fas fa-book"></i> Khám phá khóa học
                    </a>
                </div>
            @endif
        </div>
    </div>

    <script>
        // Cart functions
        function qtyplus(id) {
            var currentQty = parseInt(document.getElementById('quantity-' + id).value);
            if (currentQty < 99) {
                updateQuantity(id, currentQty + 1);
            }
        }

        function qtyminus(id) {
            var currentQty = parseInt(document.getElementById('quantity-' + id).value);
            if (currentQty > 1) {
                updateQuantity(id, currentQty - 1);
            }
        }

        function updateQuantity(id, quantity) {
            $.ajax({
                url: '/update-cart',
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    id: id,
                    quantity: quantity
                },
                success: function(response) {
                    location.reload();
                },
                error: function() {
                    alert('Có lỗi xảy ra khi cập nhật giỏ hàng');
                }
            });
        }

        function removeCart(id) {
            if (confirm('Bạn có chắc chắn muốn xóa khóa học này khỏi giỏ hàng?')) {
                $.ajax({
                    url: '/remove-from-cart',
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        id: id
                    },
                    success: function(response) {
                        location.reload();
                    },
                    error: function() {
                        alert('Có lỗi xảy ra khi xóa sản phẩm');
                    }
                });
            }
        }

        // Update cart count in header
        function updateCartCount() {
            $.ajax({
                url: '/cart-count',
                method: 'GET',
                success: function(response) {
                    $('.cart-count').text(response.count);
                }
            });
        }
    </script>
@endsection
