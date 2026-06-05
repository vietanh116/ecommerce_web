<?php
session_start();

if (empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit;
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=checkout.php");
    exit;
}

$host = 'localhost';
$dbname = 'ecommerce_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Lỗi kết nối CSDL: " . $e->getMessage());
}

$total_cart_value = 0;
foreach ($_SESSION['cart'] as $item) {
    $total_cart_value += $item['price'] * $item['quantity'];
}

$order_success = false;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'pending')");
        $stmt->execute([$_SESSION['user_id'], $total_cart_value]);
        $order_id = $pdo->lastInsertId();

        $stmt_detail = $pdo->prepare("INSERT INTO order_details (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach ($_SESSION['cart'] as $item) {
            $stmt_detail->execute([
                $order_id,
                $item['id'],
                $item['quantity'],
                $item['price']
            ]);
        }

        $pdo->commit();

        $_SESSION['cart'] = [];
        $order_success = true;

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Lỗi xử lý đơn hàng: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán - E-STORE</title>
    <style>
        :root { --primary-color: #111; --hover-color: #c90000; --bg-light: #f9f9f9; --text-color: #333; --border-color: #eaeaea; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background-color: #fff; color: var(--text-color); line-height: 1.6; }
        header { background-color: var(--primary-color); color: #fff; padding: 15px 50px; display: flex; justify-content: space-between; align-items: center; }
        .logo { font-size: 24px; font-weight: bold; text-transform: uppercase; color: #fff; text-decoration: none; }
        .container { max-width: 600px; margin: 50px auto; padding: 0 20px; }
        .checkout-box { border: 1px solid var(--border-color); padding: 30px; border-radius: 4px; background: #fff; }
        .title { font-size: 24px; text-transform: uppercase; margin-bottom: 20px; text-align: center; }
        .item-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--border-color); font-size: 14px; }
        .total-row { display: flex; justify-content: space-between; padding: 20px 0; font-size: 18px; font-weight: bold; color: var(--hover-color); }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; font-size: 14px; }
        .form-control { width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 4px; }
        .btn-order { display: block; width: 100%; padding: 12px; background: var(--primary-color); color: #fff; border: none; font-weight: bold; text-transform: uppercase; cursor: pointer; transition: background 0.3s; margin-top: 20px; text-align: center; text-decoration: none; }
        .btn-order:hover { background: var(--hover-color); }
        .success-box { text-align: center; padding: 40px; border: 1px solid var(--border-color); background: var(--bg-light); }
    </style>
</head>
<body>

    <header>
        <a href="index.php" class="logo">E-STORE</a>
        <div style="font-size: 14px;">Xin chào, <?= htmlspecialchars($_SESSION['username']) ?>!</div>
    </header>

    <main class="container">
        <?php if ($order_success): ?>
            <div class="success-box">
                <h2 style="color: green; margin-bottom: 15px;">🎉 Đặt hàng thành công!</h2>
                <p>Cảm ơn bạn đã mua sắm tại cửa hàng. Đơn hàng của bạn đã được ghi nhận vào hệ thống.</p>
                <a href="index.php" class="btn-order">Quay lại trang chủ</a>
            </div>
        <?php else: ?>
            <div class="checkout-box">
                <h1 class="title">Thông tin đơn hàng</h1>
                
                <?php foreach ($_SESSION['cart'] as $item): ?>
                    <div class="item-row">
                        <span><?= htmlspecialchars($item['name']) ?> (x<?= $item['quantity'] ?>)</span>
                        <span><?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?> đ</span>
                    </div>
                <?php endforeach; ?>
                
                <div class="total-row">
                    <span>Tổng thanh toán:</span>
                    <span><?= number_format($total_cart_value, 0, ',', '.') ?> VNĐ</span>
                </div>

                <form action="checkout.php" method="POST">
                    <div class="form-group">
                        <label>Họ và tên người nhận</label>
                        <input type="text" class="form-control" placeholder="Nguyễn Văn A" required>
                    </div>
                    <div class="form-group">
                        <label>Số điện thoại</label>
                        <input type="text" class="form-control" placeholder="0901234567" required>
                    </div>
                    <div class="form-group">
                        <label>Địa chỉ giao hàng</label>
                        <input type="text" class="form-control" placeholder="Số 123, Đường ABC, Quận 1, TP.HCM" required>
                    </div>
                    <button type="submit" class="btn-order">Xác nhận đặt hàng</button>
                </form>
            </div>
        <?php endif; ?>
    </main>

    <?php include 'includes/footer.php'; ?>

</body>
</html>