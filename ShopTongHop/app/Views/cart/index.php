<div class="container">
    <!-- Breadcrumb -->
    <div class="breadcrumb-wrapper">
        <ul class="breadcrumb-list">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>"><i class="fa-solid fa-house"></i> Trang chủ</a></li>
            <li class="breadcrumb-separator"><i class="fa-solid fa-chevron-right"></i></li>
            <li class="breadcrumb-item active">Giỏ hàng mua sắm</li>
        </ul>
    </div>

    <div style="margin-bottom: 24px;">
        <h1 style="font-size: 26px; font-weight: 800; font-family: 'Outfit', sans-serif;">
            <i class="fa-solid fa-cart-shopping" style="color: var(--primary);"></i> Giỏ Hàng Của Bạn
        </h1>
        <p style="color: var(--text-muted); font-size: 14px;">Quản lý các mặt hàng đã chọn và áp dụng ưu đãi giảm giá</p>
    </div>

    <?php if (empty($cart)): ?>
        <!-- Trạng thái giỏ hàng trống -->
        <div style="text-align: center; padding: 70px 20px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); margin-bottom: 60px;">
            <div style="width: 80px; height: 80px; border-radius: 50%; background: var(--bg-surface-elevated); border: 1px solid var(--border-color); display: inline-flex; align-items: center; justify-content: center; font-size: 36px; color: var(--text-muted); margin-bottom: 20px;">
                <i class="fa-solid fa-cart-arrow-down"></i>
            </div>
            <h3 style="font-size: 22px; margin-bottom: 10px;">Giỏ hàng của bạn đang trống!</h3>
            <p style="color: var(--text-muted); font-size: 14.5px; max-width: 480px; margin: 0 auto 28px;">
                Bạn chưa thêm sản phẩm nào vào giỏ hàng. Hãy lướt qua hàng trăm sản phẩm công nghệ, gia dụng chất lượng cao đang giảm giá ngay nhé!
            </p>
            <a href="<?= BASE_URL ?>/products" class="btn btn-primary" style="padding: 12px 32px; font-weight: 600;">
                <i class="fa-solid fa-bag-shopping"></i> Khám Phá Sản Phẩm Ngay
            </a>
        </div>
    <?php else: ?>
        <!-- Bố cục giỏ hàng 2 cột -->
        <div class="cart-layout">
            <!-- Cột trái: Bảng danh sách mặt hàng -->
            <div class="cart-items-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid var(--border-color);">
                    <span style="font-size: 14px; font-weight: 600; color: var(--text-secondary);">
                        Hiện có <strong><?= count($cart) ?></strong> loại sản phẩm trong giỏ
                    </span>
                    <a href="<?= BASE_URL ?>/cart/clear" onclick="return confirm('Bạn có chắc chắn muốn xóa toàn bộ sản phẩm khỏi giỏ hàng?')" style="font-size: 13px; color: var(--danger); font-weight: 600;">
                        <i class="fa-solid fa-trash-can"></i> Xóa tất cả
                    </a>
                </div>

                <div style="overflow-x: auto;">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th style="width: 140px; text-align: center;">Đơn giá</th>
                                <th style="width: 150px; text-align: center;">Số lượng</th>
                                <th style="width: 140px; text-align: right;">Thành tiền</th>
                                <th style="width: 40px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart as $id => $item): 
                                $subtotal = (float)$item['price'] * (int)$item['quantity'];
                            ?>
                                <tr id="cartRow_<?= $id ?>">
                                    <td>
                                        <div class="cart-product-cell">
                                            <img src="<?= e($item['thumbnail']) ?>" alt="<?= e($item['name']) ?>" class="cart-thumb">
                                            <div class="cart-product-info">
                                                <h4><a href="<?= BASE_URL ?>/product/detail?slug=<?= urlencode($item['slug'] ?? '') ?>"><?= e($item['name']) ?></a></h4>
                                                <?php if (!empty($item['sku'])): ?>
                                                    <div class="cart-product-meta">SKU: <?= e($item['sku']) ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="text-align: center; font-weight: 600; color: var(--text-primary);">
                                        <?= number_format($item['price']) ?> đ
                                        <?php if (!empty($item['discount_percent'])): ?>
                                            <div style="font-size: 11.5px; color: var(--text-muted); text-decoration: line-through;">
                                                <?= number_format($item['raw_price']) ?> đ
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div style="display: flex; justify-content: center;">
                                            <div class="quantity-stepper" style="height: 38px;">
                                                <button type="button" class="stepper-btn" onclick="updateCartQty(<?= $id ?>, <?= (int)$item['quantity'] - 1 ?>)" style="width: 32px;">-</button>
                                                <input type="number" id="qtyInput_<?= $id ?>" class="stepper-input" value="<?= (int)$item['quantity'] ?>" min="1" max="<?= (int)$item['stock_quantity'] ?>" style="width: 42px; font-size: 14px;" onchange="updateCartQty(<?= $id ?>, this.value)">
                                                <button type="button" class="stepper-btn" onclick="updateCartQty(<?= $id ?>, <?= (int)$item['quantity'] + 1 ?>)" style="width: 32px;">+</button>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="text-align: right; font-weight: 700; color: var(--primary); font-size: 15px;" id="subtotal_<?= $id ?>">
                                        <?= number_format($subtotal) ?> đ
                                    </td>
                                    <td style="text-align: center;">
                                        <a href="<?= BASE_URL ?>/cart/remove?id=<?= $id ?>" onclick="return confirm('Bạn muốn bỏ sản phẩm này?')" style="color: var(--text-muted); font-size: 15px; transition: var(--transition);" title="Xóa khỏi giỏ" onmouseover="this.style.color='#FF3838'" onmouseout="this.style.color='var(--text-muted)'">
                                            <i class="fa-solid fa-xmark"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; padding-top: 18px; border-top: 1px solid var(--border-color);">
                    <a href="<?= BASE_URL ?>/products" class="btn btn-secondary btn-sm">
                        <i class="fa-solid fa-arrow-left"></i> Tiếp tục xem sản phẩm
                    </a>
                    <span style="font-size: 13px; color: var(--text-muted);">
                        <i class="fa-solid fa-truck-fast" style="color: var(--primary);"></i> Miễn phí vận chuyển cho đơn hàng từ <strong>300.000 đ</strong>
                    </span>
                </div>
            </div>

            <!-- Cột phải: Hộp Tóm Tắt & Thanh Toán -->
            <div class="cart-summary-card">
                <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid var(--border-color);">
                    Tóm Tắt Đơn Hàng
                </h3>

                <div class="summary-row">
                    <span>Tạm tính tiền hàng:</span>
                    <strong id="cartSubtotalText"><?= number_format($totalAmount) ?> đ</strong>
                </div>

                <div class="summary-row">
                    <span>Phí giao hàng:</span>
                    <strong id="cartShippingText" style="<?= $shippingFee == 0 ? 'color: var(--success);' : '' ?>">
                        <?= $shippingFee == 0 ? 'Miễn phí' : number_format($shippingFee) . ' đ' ?>
                    </strong>
                </div>

                <?php if ($totalAmount < 300000 && $totalAmount > 0): ?>
                    <div style="font-size: 12px; color: var(--primary); background: rgba(255, 107, 53, 0.08); padding: 6px 10px; border-radius: 6px; margin-bottom: 12px;">
                        Mua thêm <strong><?= number_format(300000 - $totalAmount) ?> đ</strong> để được FREESHIP!
                    </div>
                <?php endif; ?>

                <?php if ($voucher): ?>
                    <div class="voucher-applied-pill">
                        <div>
                            <i class="fa-solid fa-ticket"></i> Mã: <strong><?= e($voucher['code']) ?></strong>
                            <div style="font-size: 11.5px; opacity: 0.9;">
                                (Giảm <?= $voucher['discount_type'] === 'percent' ? "{$voucher['discount_value']}%" : (number_format($voucher['discount_value']) . " đ") ?>)
                            </div>
                        </div>
                        <a href="<?= BASE_URL ?>/cart/remove-voucher" title="Gỡ mã giảm giá" style="color: var(--danger); font-size: 16px;">
                            <i class="fa-solid fa-circle-xmark"></i>
                        </a>
                    </div>
                    <div class="summary-row" style="color: var(--success);">
                        <span>Giảm giá Voucher:</span>
                        <strong>-<?= number_format($discountAmount) ?> đ</strong>
                    </div>
                <?php endif; ?>

                <div class="summary-row total-row">
                    <span>Tổng thanh toán:</span>
                    <span class="total-price" id="cartFinalAmount"><?= number_format($finalAmount) ?> đ</span>
                </div>

                <!-- Form áp dụng Voucher -->
                <?php if (!$voucher): ?>
                    <form action="<?= BASE_URL ?>/cart/apply-voucher" method="POST" style="margin-top: 16px;">
                        <label style="font-size: 13px; font-weight: 600; color: var(--text-secondary); display: block; margin-bottom: 6px;">
                            <i class="fa-solid fa-tags" style="color: var(--primary);"></i> Mã Giảm Giá / Voucher:
                        </label>
                        <div class="voucher-box-input">
                            <input type="text" name="voucher_code" id="voucherCodeInput" placeholder="Nhập mã (vd: SALE10)" class="form-control" style="flex: 1; text-transform: uppercase; font-weight: 600; padding: 9px 12px; background: var(--bg-surface-elevated); border: 1px solid var(--border-color); border-radius: 6px; color: var(--text-primary); font-size: 13px;" required>
                            <button type="submit" class="btn btn-primary" style="padding: 0 16px; font-size: 13px; font-weight: 600;">Áp Dụng</button>
                        </div>
                    </form>

                    <!-- Gợi ý voucher có sẵn -->
                    <div style="margin-top: 10px; margin-bottom: 20px;">
                        <span style="font-size: 12px; color: var(--text-muted); display: block; margin-bottom: 6px;">Mã ưu đãi gợi ý:</span>
                        <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                            <span class="meta-pill" style="cursor: pointer; border-style: dashed; font-size: 11.5px; background: rgba(255, 107, 53, 0.08); color: var(--primary); border-color: var(--primary);" onclick="applyQuickVoucher('SALE10')">
                                SALE10 (-10%)
                            </span>
                            <span class="meta-pill" style="cursor: pointer; border-style: dashed; font-size: 11.5px; background: rgba(0, 245, 255, 0.08); color: var(--accent); border-color: var(--accent);" onclick="applyQuickVoucher('FREESHIP')">
                                FREESHIP
                            </span>
                            <span class="meta-pill" style="cursor: pointer; border-style: dashed; font-size: 11.5px; background: rgba(0, 230, 118, 0.08); color: var(--success); border-color: var(--success);" onclick="applyQuickVoucher('WELCOME')">
                                WELCOME (-50K)
                            </span>
                        </div>
                    </div>
                <?php endif; ?>

                <a href="<?= BASE_URL ?>/checkout" class="btn btn-primary" style="width: 100%; justify-content: center; height: 50px; font-size: 16px; font-weight: 700; margin-top: 8px;">
                    <i class="fa-solid fa-credit-card"></i> Tiến Hành Đặt Hàng
                </a>

                <div style="margin-top: 20px; font-size: 12.5px; color: var(--text-muted); text-align: center; display: flex; flex-direction: column; gap: 6px;">
                    <span><i class="fa-solid fa-lock" style="color: var(--success);"></i> Bảo mật thanh toán an toàn 100%</span>
                    <span><i class="fa-solid fa-shield-halved"></i> Đổi trả hàng miễn phí trong 7 ngày</span>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function applyQuickVoucher(code) {
    const input = document.getElementById('voucherCodeInput');
    if (input) {
        input.value = code;
        input.focus();
    }
}

function updateCartQty(productId, newQty) {
    newQty = parseInt(newQty) || 1;
    if (newQty < 1) {
        if (!confirm('Bạn có muốn xóa sản phẩm này khỏi giỏ hàng?')) {
            const input = document.getElementById('qtyInput_' + productId);
            if (input) input.value = 1;
            return;
        }
        newQty = 0;
    }

    const formData = new URLSearchParams();
    formData.append('product_id', productId);
    formData.append('quantity', newQty);

    fetch('<?= BASE_URL ?>/cart/update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData.toString()
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            if (newQty <= 0) {
                const row = document.getElementById('cartRow_' + productId);
                if (row) row.remove();
                window.location.reload();
                return;
            }

            const input = document.getElementById('qtyInput_' + productId);
            if (input) input.value = data.quantity;

            const subtotal = document.getElementById('subtotal_' + productId);
            if (subtotal) subtotal.textContent = data.itemSubtotal;

            const badge = document.getElementById('cartBadge');
            if (badge) {
                badge.textContent = data.cartCount;
                badge.classList.remove('bounce');
                void badge.offsetWidth;
                badge.classList.add('bounce');
            }

            // Tải lại để cập nhật toàn bộ phí ship & voucher chính xác
            window.location.reload();
        } else {
            alert(data.message || 'Không thể cập nhật số lượng.');
            window.location.reload();
        }
    })
    .catch(() => window.location.reload());
}
</script>
