<div class="container">
    <!-- Breadcrumb -->
    <div class="breadcrumb-wrapper">
        <ul class="breadcrumb-list">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>"><i class="fa-solid fa-house"></i> Trang chủ</a></li>
            <li class="breadcrumb-separator"><i class="fa-solid fa-chevron-right"></i></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/cart">Giỏ hàng</a></li>
            <li class="breadcrumb-separator"><i class="fa-solid fa-chevron-right"></i></li>
            <li class="breadcrumb-item active">Thanh toán đơn hàng</li>
        </ul>
    </div>

    <div style="margin-bottom: 24px;">
        <h1 style="font-size: 26px; font-weight: 800; font-family: 'Outfit', sans-serif;">
            <i class="fa-solid fa-cash-register" style="color: var(--primary);"></i> Thanh Toán & Đặt Hàng
        </h1>
        <p style="color: var(--text-muted); font-size: 14px;">Hoàn tất thông tin giao hàng và chọn phương thức thanh toán</p>
    </div>

    <form action="<?= BASE_URL ?>/checkout/process" method="POST" id="checkoutForm">
        <div class="checkout-layout">
            
            <!-- CỘT TRÁI: THÔNG TIN GIAO HÀNG & PHƯƠNG THỨC THANH TOÁN -->
            <div class="checkout-main-col">
                
                <!-- Khối 1: Thông tin người nhận & Địa chỉ giao hàng (Bắt buộc không được để null) -->
                <div class="checkout-card">
                    <div class="checkout-card-title">
                        <i class="fa-solid fa-location-dot" style="color: var(--primary);"></i> 1. Thông Tin Nhận Hàng & Địa Chỉ
                    </div>

                    <?php if (!empty($addresses)): ?>
                        <!-- Danh sách địa chỉ đã lưu trong Sổ địa chỉ user_addresses -->
                        <div style="margin-bottom: 18px;">
                            <label style="font-size: 13.5px; font-weight: 700; color: var(--text-primary); display: block; margin-bottom: 10px;">
                                <i class="fa-solid fa-address-book" style="color: var(--primary);"></i> Chọn địa chỉ nhận hàng từ sổ địa chỉ của bạn:
                            </label>

                            <div style="display: flex; flex-direction: column; gap: 10px;" id="savedAddressesList">
                                <?php foreach ($addresses as $idx => $addr): ?>
                                    <label class="address-select-card <?= $addr['is_default'] ? 'active' : '' ?>" style="display: flex; align-items: flex-start; gap: 12px; padding: 14px 16px; background: var(--bg-surface-elevated); border: 2px solid <?= $addr['is_default'] ? 'var(--primary)' : 'var(--border-color)' ?>; border-radius: 10px; cursor: pointer; transition: var(--transition);">
                                        <input type="radio" name="address_id" value="<?= $addr['id'] ?>" <?= $addr['is_default'] ? 'checked' : '' ?> onchange="onAddressSelect(this, '<?= e($addr['recipient_name']) ?>', '<?= e($addr['recipient_phone']) ?>', '<?= e($addr['full_address']) ?>')" style="margin-top: 3px; accent-color: var(--primary);">
                                        <div style="flex: 1;">
                                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px; flex-wrap: wrap;">
                                                <strong style="color: var(--text-primary); font-size: 14.5px;"><?= e($addr['recipient_name']) ?></strong>
                                                <span style="color: var(--text-muted); font-size: 13px;">| <?= e($addr['recipient_phone']) ?></span>
                                                <?php if ($addr['is_default']): ?>
                                                    <span style="padding: 2px 8px; border-radius: 4px; background: rgba(255, 107, 0, 0.15); color: var(--primary); font-size: 11px; font-weight: 700; border: 1px solid rgba(255, 107, 0, 0.3);">Mặc Định</span>
                                                <?php endif; ?>
                                            </div>
                                            <div style="font-size: 13.5px; color: var(--text-secondary); line-height: 1.4;">
                                                <?= e($addr['full_address']) ?>
                                            </div>
                                        </div>
                                    </label>
                                <?php endforeach; ?>

                                <!-- Lựa chọn nhập địa chỉ mới khác -->
                                <label class="address-select-card" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; background: var(--bg-surface); border: 2px dashed var(--border-color); border-radius: 10px; cursor: pointer; transition: var(--transition);">
                                    <input type="radio" name="address_id" value="0" id="radioNewAddress" onchange="onAddressSelect(this, '', '', '')" style="accent-color: var(--primary);">
                                    <span style="font-size: 13.5px; font-weight: 600; color: var(--primary);">
                                        <i class="fa-solid fa-plus-circle"></i> Giao đến một địa chỉ mới khác
                                    </span>
                                </label>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Form nhập / xác nhận thông tin chi tiết -->
                    <div id="newAddressFormWrapper" style="<?= !empty($addresses) ? 'display: none;' : '' ?> margin-top: 16px; padding: 16px; background: rgba(255,255,255,0.02); border: 1px solid var(--border-color); border-radius: 10px;">
                        <div style="font-size: 13px; font-weight: 700; color: var(--text-primary); margin-bottom: 12px; display: flex; align-items: center; gap: 6px;">
                            <i class="fa-solid fa-pen-to-square" style="color: var(--primary);"></i> Nhập địa chỉ giao hàng mới:
                        </div>

                        <div class="form-row-2">
                            <div class="form-group" style="margin-bottom: 14px;">
                                <label style="font-size: 13px; font-weight: 600; color: var(--text-secondary); display: block; margin-bottom: 6px;">
                                    Họ và tên người nhận <span style="color: var(--danger);">*</span>
                                </label>
                                <input type="text" id="custNameInput" name="customer_name" class="form-control" placeholder="Ví dụ: Nguyễn Văn A" value="<?= e($defaultAddress['recipient_name'] ?? ($currentUser['fullname'] ?? '')) ?>" required style="width: 100%; padding: 10px 14px; background: var(--bg-surface-elevated); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-primary); font-size: 14px;">
                            </div>

                            <div class="form-group" style="margin-bottom: 14px;">
                                <label style="font-size: 13px; font-weight: 600; color: var(--text-secondary); display: block; margin-bottom: 6px;">
                                    Số điện thoại liên hệ <span style="color: var(--danger);">*</span>
                                </label>
                                <input type="tel" id="custPhoneInput" name="customer_phone" class="form-control" placeholder="Ví dụ: 0912345678" value="<?= e($defaultAddress['recipient_phone'] ?? ($currentUser['phone'] ?? '')) ?>" required style="width: 100%; padding: 10px 14px; background: var(--bg-surface-elevated); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-primary); font-size: 14px;">
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 14px;">
                            <label style="font-size: 13px; font-weight: 600; color: var(--text-secondary); display: block; margin-bottom: 6px;">
                                Địa chỉ nhận hàng chi tiết <span style="color: var(--danger);">* (Không được để trống)</span>
                            </label>
                            <textarea id="shippingAddressInput" name="shipping_address" rows="2" class="form-control" placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố..." style="width: 100%; padding: 10px 14px; background: var(--bg-surface-elevated); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-primary); font-size: 14px; resize: vertical;"><?= e($defaultAddress['full_address'] ?? '') ?></textarea>
                        </div>

                        <div style="display: flex; gap: 16px; margin-top: 10px; flex-wrap: wrap;">
                            <label style="font-size: 12.5px; color: var(--text-secondary); display: flex; align-items: center; gap: 6px; cursor: pointer;">
                                <input type="checkbox" name="save_as_new_address" value="1" checked style="accent-color: var(--primary);">
                                Lưu địa chỉ này vào sổ địa chỉ tài khoản của tôi
                            </label>
                            <label style="font-size: 12.5px; color: var(--text-secondary); display: flex; align-items: center; gap: 6px; cursor: pointer;">
                                <input type="checkbox" name="set_default_address" value="1" style="accent-color: var(--primary);">
                                Đặt làm địa chỉ giao hàng mặc định
                            </label>
                        </div>
                    </div>

                    <div class="form-group" style="margin-top: 14px; margin-bottom: 14px;">
                        <label style="font-size: 13px; font-weight: 600; color: var(--text-secondary); display: block; margin-bottom: 6px;">
                            Địa chỉ email (để nhận hóa đơn & thông báo giao hàng)
                        </label>
                        <input type="email" name="customer_email" class="form-control" placeholder="Ví dụ: email@gmail.com" value="<?= e($currentUser['email'] ?? '') ?>" style="width: 100%; padding: 10px 14px; background: var(--bg-surface-elevated); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-primary); font-size: 14px;">
                    </div>

                    <div class="form-group">
                        <label style="font-size: 13px; font-weight: 600; color: var(--text-secondary); display: block; margin-bottom: 6px;">
                            Ghi chú đơn hàng (nếu có)
                        </label>
                        <input type="text" name="notes" class="form-control" placeholder="Ví dụ: Giao giờ hành chính, gọi trước khi giao..." style="width: 100%; padding: 10px 14px; background: var(--bg-surface-elevated); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-primary); font-size: 14px;">
                    </div>
                </div>

                <!-- Khối 2: Phương thức thanh toán -->
                <div class="checkout-card">
                    <div class="checkout-card-title">
                        <i class="fa-solid fa-wallet" style="color: var(--primary);"></i> 2. Phương Thức Thanh Toán
                    </div>

                    <div class="payment-methods-grid">
                        <!-- Option 1: VietQR -->
                        <label class="payment-method-item active" id="labelVietQR">
                            <input type="radio" name="payment_method" value="vietqr" checked onchange="switchPaymentMethod('vietqr')">
                            <div class="payment-icon-box" style="color: #38bdf8; background: rgba(56, 189, 248, 0.1);">
                                <i class="fa-solid fa-qrcode"></i>
                            </div>
                            <div style="flex: 1;">
                                <div style="font-weight: 700; color: var(--text-primary); font-size: 15px; display: flex; align-items: center; gap: 8px;">
                                    <span>Chuyển khoản Ngân hàng (VietQR)</span>
                                    <span class="meta-pill" style="background: rgba(0, 230, 118, 0.15); color: var(--success); border-color: rgba(0, 230, 118, 0.3); font-size: 11px;">Khuyên Dùng</span>
                                </div>
                                <div style="font-size: 13px; color: var(--text-muted); margin-top: 3px;">
                                    Quét mã QR tự động qua ứng dụng MBBank / Vietcombank / Techcombank / MoMo. Xác nhận thanh toán ngay lập tức.
                                </div>
                            </div>
                        </label>

                        <!-- Option 2: COD -->
                        <label class="payment-method-item" id="labelCOD">
                            <input type="radio" name="payment_method" value="cod" onchange="switchPaymentMethod('cod')">
                            <div class="payment-icon-box" style="color: var(--warning); background: rgba(255, 179, 0, 0.1);">
                                <i class="fa-solid fa-hand-holding-dollar"></i>
                            </div>
                            <div style="flex: 1;">
                                <div style="font-weight: 700; color: var(--text-primary); font-size: 15px;">
                                    Thanh toán khi nhận hàng (COD)
                                </div>
                                <div style="font-size: 13px; color: var(--text-muted); margin-top: 3px;">
                                    Kiểm tra hàng thoải mái trước khi thanh toán tiền mặt cho shipper bưu tá.
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

            </div>

            <!-- CỘT PHẢI: TÓM TẮT ĐƠN HÀNG & NÚT ĐẶT HÀNG -->
            <div class="checkout-sidebar-col">
                <div class="checkout-card" style="position: sticky; top: 90px;">
                    <div class="checkout-card-title">
                        <i class="fa-solid fa-receipt" style="color: var(--primary);"></i> Đơn Hàng Của Bạn (<?= count($cart) ?>)
                    </div>

                    <!-- Danh sách sản phẩm thu nhỏ -->
                    <div style="max-height: 260px; overflow-y: auto; padding-right: 6px; margin-bottom: 18px; display: flex; flex-direction: column; gap: 12px;">
                        <?php foreach ($cart as $item): 
                            $lineTotal = (float)$item['price'] * (int)$item['quantity'];
                        ?>
                            <div style="display: flex; gap: 12px; align-items: center; font-size: 13.5px;">
                                <img src="<?= e($item['thumbnail']) ?>" alt="<?= e($item['name']) ?>" style="width: 48px; height: 48px; border-radius: 6px; object-fit: contain; background: var(--bg-surface-elevated); border: 1px solid var(--border-color); padding: 4px; flex-shrink: 0;">
                                <div style="flex: 1; min-width: 0;">
                                    <div style="font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: var(--text-primary);">
                                        <?= e($item['name']) ?>
                                    </div>
                                    <div style="color: var(--text-muted); font-size: 12px;">
                                        <?= number_format($item['price']) ?> đ × <?= (int)$item['quantity'] ?>
                                    </div>
                                </div>
                                <div style="font-weight: 700; color: var(--text-primary); flex-shrink: 0;">
                                    <?= number_format($lineTotal) ?> đ
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Chi phí chi tiết -->
                    <div style="padding-top: 14px; border-top: 1px solid var(--border-color);">
                        <div class="summary-row">
                            <span>Tạm tính tiền hàng:</span>
                            <strong><?= number_format($totalAmount) ?> đ</strong>
                        </div>

                        <div class="summary-row">
                            <span>Phí giao hàng:</span>
                            <strong style="<?= $shippingFee == 0 ? 'color: var(--success);' : '' ?>">
                                <?= $shippingFee == 0 ? 'Miễn phí' : number_format($shippingFee) . ' đ' ?>
                            </strong>
                        </div>

                        <?php if ($voucher): ?>
                            <div class="summary-row" style="color: var(--success);">
                                <span>Voucher (<?= e($voucher['code']) ?>):</span>
                                <strong>-<?= number_format($discountAmount) ?> đ</strong>
                            </div>
                        <?php endif; ?>

                        <div class="summary-row total-row">
                            <span>Tổng thanh toán:</span>
                            <span class="total-price"><?= number_format($finalAmount) ?> đ</span>
                        </div>
                    </div>

                    <!-- Nút xác nhận đặt hàng -->
                    <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; height: 50px; font-size: 16px; font-weight: 700; margin-top: 20px;">
                        <i class="fa-solid fa-shield-halved"></i> Xác Nhận Đặt Hàng
                    </button>

                    <div style="margin-top: 16px; font-size: 12px; color: var(--text-muted); text-align: center;">
                        Bằng việc bấm xác nhận, bạn đồng ý với Điều khoản dịch vụ và Chính sách bảo mật của <?= APP_NAME ?>.
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

<script>
function switchPaymentMethod(method) {
    document.querySelectorAll('.payment-method-item').forEach(el => el.classList.remove('active'));
    if (method === 'vietqr') {
        document.getElementById('labelVietQR').classList.add('active');
    } else {
        document.getElementById('labelCOD').classList.add('active');
    }
}

function onAddressSelect(radioElem, name, phone, address) {
    document.querySelectorAll('.address-select-card').forEach(el => {
        el.style.borderColor = 'var(--border-color)';
    });
    if (radioElem.parentElement) {
        radioElem.parentElement.style.borderColor = 'var(--primary)';
    }

    const newForm = document.getElementById('newAddressFormWrapper');
    const nameInput = document.getElementById('custNameInput');
    const phoneInput = document.getElementById('custPhoneInput');
    const addrInput = document.getElementById('shippingAddressInput');

    if (radioElem.value === '0') {
        if (newForm) newForm.style.display = 'block';
        if (addrInput) {
            addrInput.value = '';
            addrInput.required = true;
            addrInput.focus();
        }
    } else {
        if (newForm) newForm.style.display = 'none';
        if (nameInput && name) nameInput.value = name;
        if (phoneInput && phone) phoneInput.value = phone;
        if (addrInput) {
            addrInput.value = address;
            addrInput.required = false;
        }
    }
}

// Kiểm tra form trước khi submit: địa chỉ không được để trống!
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    const selectedRadio = document.querySelector('input[name="address_id"]:checked');
    const addrInput = document.getElementById('shippingAddressInput');
    if (selectedRadio && selectedRadio.value === '0' && (!addrInput.value || !addrInput.value.trim())) {
        e.preventDefault();
        alert('Vui lòng nhập địa chỉ nhận hàng chi tiết và rõ ràng!');
        addrInput.focus();
    }
});
</script>
