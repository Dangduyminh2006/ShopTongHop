<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Hóa Đơn Bán Hàng') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        body {
            background-color: #f1f2f6;
            color: #2f3542;
            padding: 30px 15px;
            font-size: 14px;
            line-height: 1.6;
        }
        .invoice-actions-bar {
            max-width: 800px;
            margin: 0 auto 20px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
            border: none;
        }
        .btn-print {
            background: #ff5722;
            color: #fff;
        }
        .btn-print:hover {
            background: #e64a19;
        }
        .btn-back {
            background: #dfe4ea;
            color: #2f3542;
        }
        .btn-back:hover {
            background: #ced6e0;
        }
        .invoice-paper {
            max-width: 800px;
            margin: 0 auto;
            background: #ffffff;
            padding: 45px 50px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #ff5722;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }
        .company-logo {
            font-size: 24px;
            font-weight: 800;
            color: #1e272e;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .company-logo span {
            color: #ff5722;
        }
        .company-info {
            font-size: 13px;
            color: #57606f;
            margin-top: 5px;
        }
        .invoice-title-block {
            text-align: right;
        }
        .invoice-title {
            font-size: 20px;
            font-weight: 800;
            color: #ff5722;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .invoice-number {
            font-size: 14px;
            font-weight: 700;
            color: #2f3542;
            margin-top: 4px;
        }
        .invoice-date {
            font-size: 12px;
            color: #747d8c;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-bottom: 25px;
            background: #f8f9fa;
            padding: 18px 20px;
            border-radius: 6px;
        }
        .info-section h4 {
            font-size: 13px;
            text-transform: uppercase;
            color: #ff5722;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }
        .info-section p {
            margin-bottom: 4px;
            font-size: 13px;
        }
        .table-invoice {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .table-invoice th {
            background: #2f3542;
            color: #ffffff;
            padding: 10px 12px;
            text-align: left;
            font-size: 13px;
            font-weight: 600;
        }
        .table-invoice td {
            padding: 12px;
            border-bottom: 1px solid #e1e2e6;
            font-size: 13px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: 700; }
        
        .totals-section {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 30px;
        }
        .totals-table {
            width: 320px;
        }
        .totals-table tr td {
            padding: 6px 12px;
            font-size: 13px;
        }
        .totals-table .grand-total td {
            border-top: 2px solid #ff5722;
            font-size: 16px;
            font-weight: 800;
            color: #ff5722;
            padding-top: 10px;
        }
        .signatures-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
        }
        .signature-title {
            font-weight: 700;
            font-size: 13px;
            color: #2f3542;
            margin-bottom: 60px;
        }
        .signature-sub {
            font-size: 11px;
            color: #747d8c;
            font-style: italic;
        }
        .invoice-footer {
            margin-top: 40px;
            border-top: 1px dashed #ced6e0;
            padding-top: 15px;
            text-align: center;
            font-size: 12px;
            color: #747d8c;
        }

        /* PRINT MEDIA STYLES */
        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .invoice-actions-bar {
                display: none !important;
            }
            .invoice-paper {
                box-shadow: none;
                padding: 20px 0;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

    <div class="invoice-actions-bar">
        <a href="<?= BASE_URL ?>/order/detail/<?= $order['order_code'] ?>" class="btn btn-back">
            <i class="fa-solid fa-arrow-left"></i> Quay Lại Đơn Hàng
        </a>
        <button onclick="window.print()" class="btn btn-print">
            <i class="fa-solid fa-print"></i> In Hóa Đơn / Lưu PDF
        </button>
    </div>

    <div class="invoice-paper">
        <!-- HEADER -->
        <div class="invoice-header">
            <div>
                <div class="company-logo">SHOP<span>XPRESS</span></div>
                <div class="company-info">
                    <p><strong>CÔNG TY CỔ PHẦN THƯƠNG MẠI SHOPXPRESS VIỆT NAM</strong></p>
                    <p>Địa chỉ: Tòa nhà ShopXpress, Cầu Giấy, Hà Nội</p>
                    <p>Hotline: 1900 8899 | Email: support@shoptonghop.vn</p>
                    <p>Mã số thuế: 0109888888</p>
                </div>
            </div>
            <div class="invoice-title-block">
                <h1 class="invoice-title">HÓA ĐƠN BÁN HÀNG</h1>
                <div class="invoice-number">MÃ ĐƠN: #<?= e($order['order_code']) ?></div>
                <div class="invoice-date">Ngày đặt: <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></div>
                <div class="invoice-date">Ngày in: <?= date('d/m/Y H:i') ?></div>
            </div>
        </div>

        <!-- CUSTOMER & PAYMENT DETAILS -->
        <div class="info-grid">
            <div class="info-section">
                <h4>Thông Tin Khách Hàng</h4>
                <p><strong>Khách hàng:</strong> <?= e($order['customer_name']) ?></p>
                <p><strong>Điện thoại:</strong> <?= e($order['customer_phone']) ?></p>
                <p><strong>Email:</strong> <?= e($order['customer_email']) ?></p>
                <p><strong>Địa chỉ giao:</strong> <?= e($order['shipping_address']) ?></p>
            </div>
            <div class="info-section">
                <h4>Phương Thức Thanh Toán</h4>
                <p><strong>Hình thức:</strong> <?= ($order['payment_method'] === 'vietqr') ? 'Chuyển khoản VietQR NAPAS247' : 'Thanh toán khi nhận hàng (COD)' ?></p>
                <p><strong>Trạng thái:</strong> 
                    <strong style="color: <?= ($order['payment_status'] === 'paid') ? '#2ed573' : '#ffa502' ?>;">
                        <?= ($order['payment_status'] === 'paid') ? 'ĐÃ THANH TOÁN THÀNH CÔNG' : 'CHƯA THANH TOÁN (COD)' ?>
                    </strong>
                </p>
                <p><strong>Trạng thái đơn:</strong> <?= strtoupper($order['order_status']) ?></p>
                <?php if (!empty($order['notes'])): ?>
                    <p><strong>Ghi chú:</strong> <?= e($order['notes']) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- ITEMS TABLE -->
        <table class="table-invoice">
            <thead>
                <tr>
                    <th class="text-center" style="width: 40px;">STT</th>
                    <th>Tên Sản Phẩm / Dịch Vụ</th>
                    <th class="text-center" style="width: 70px;">Số Lượng</th>
                    <th class="text-right" style="width: 120px;">Đơn Giá</th>
                    <th class="text-right" style="width: 140px;">Thành Tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    $subtotal = 0;
                    foreach ($items as $index => $item): 
                        $subtotal += (float)$item['subtotal'];
                ?>
                    <tr>
                        <td class="text-center"><?= $index + 1 ?></td>
                        <td>
                            <strong><?= e($item['product_name']) ?></strong>
                            <?php if (!empty($item['sku'])): ?>
                                <br><small style="color:#747d8c;">Mã SKU: <?= e($item['sku']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td class="text-center"><?= (int)$item['quantity'] ?></td>
                        <td class="text-right"><?= format_currency($item['product_price']) ?></td>
                        <td class="text-right font-bold"><?= format_currency($item['subtotal']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- TOTALS BREAKDOWN -->
        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td>Cộng tiền hàng:</td>
                    <td class="text-right font-bold"><?= format_currency($subtotal) ?></td>
                </tr>
                <?php if ((float)$order['discount_amount'] > 0): ?>
                    <tr>
                        <td>Giảm giá Voucher (<?= e($order['voucher_code']) ?>):</td>
                        <td class="text-right font-bold" style="color: #2ed573;">-<?= format_currency($order['discount_amount']) ?></td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td>Phí vận chuyển:</td>
                    <td class="text-right font-bold" style="color: #2ed573;">0 ₫ (Miễn phí)</td>
                </tr>
                <tr class="grand-total">
                    <td>TỔNG THANH TOÁN:</td>
                    <td class="text-right"><?= format_currency($order['total_amount']) ?></td>
                </tr>
            </table>
        </div>

        <!-- SIGNATURES -->
        <div class="signatures-grid">
            <div>
                <div class="signature-title">Người Mua Hàng</div>
                <div class="signature-sub">(Ký & ghi rõ họ tên)</div>
            </div>
            <div>
                <div class="signature-title">Người Giao Hàng</div>
                <div class="signature-sub">(Ký & ghi rõ họ tên)</div>
            </div>
            <div>
                <div class="signature-title">Thủ Trưởng Đơn Vị</div>
                <div class="signature-sub">(Ký, đóng dấu & ghi rõ họ tên)</div>
            </div>
        </div>

        <!-- FOOTER -->
        <div class="invoice-footer">
            <p>Cảm ơn quý khách đã tin tưởng mua sắm tại <strong>ShopXpress</strong>!</p>
            <p>Hóa đơn điện tử được tạo tự động bởi hệ thống Quản lý Thương mại điện tử ShopTongHop.</p>
        </div>
    </div>

</body>
</html>
