<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$host = 'localhost';
$dbname = 'ecommerce_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Lỗi kết nối CSDL: " . $e->getMessage());
}

$user_id = $_SESSION['user_id'];

if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header("Location: index.php");
    exit;
}

$stmt_user = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt_user->execute([$user_id]);
$user = $stmt_user->fetch();

$stmt_orders = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt_orders->execute([$user_id]);
$orders = $stmt_orders->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hồ sơ của tôi - E-STORE</title>
    <style>
        :root { --primary-color: #111; --hover-color: #c90000; --bg-light: #f9f9f9; --border-color: #eaeaea; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background-color: #f4f4f4; color: #333; line-height: 1.6; }
        header { background-color: var(--primary-color); color: #fff; padding: 15px 50px; display: flex; justify-content: space-between; align-items: center; }
        .logo { font-size: 24px; font-weight: bold; text-decoration: none; color: #fff; }
        
        .container { max-width: 1000px; margin: 40px auto; display: grid; grid-template-columns: 1fr 3fr; gap: 30px; padding: 0 20px; }
        .sidebar { background: #fff; padding: 20px; border-radius: 4px; border: 1px solid var(--border-color); height: fit-content; }
        .sidebar h3 { margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px; }
        .sidebar a { display: block; padding: 10px 0; color: #333; text-decoration: none; border-bottom: 1px solid var(--bg-light); }
        .sidebar a:hover { color: var(--hover-color); }
        .sidebar a.text-danger { color: red; font-weight: bold; border: none; margin-top: 10px; }

        .content { background: #fff; padding: 30px; border-radius: 4px; border: 1px solid var(--border-color); }
        .content h2 { margin-bottom: 20px; color: var(--primary-color); }
        
        .order-card { border: 1px solid var(--border-color); margin-bottom: 20px; border-radius: 4px; overflow: hidden; }
        .order-header { background: var(--bg-light); padding: 15px; display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-color); font-size: 14px; }
        .status { font-weight: bold; text-transform: uppercase; }
        .status.pending { color: orange; }
        .status.completed { color: green; }
        .status.cancelled { color: red; }
        
        .order-items { padding: 15px; }
        .item-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dashed var(--border-color); font-size: 14px; }
        .item-row:last-child { border-bottom: none; }
        .order-footer { padding: 15px; text-align: right; border-top: 1px solid var(--border-color); font-size: 18px; font-weight: bold; color: var(--hover-color); }
    </style>
</head>
<body>

    <header>
        <a href="index.php" class="logo">E-STORE</a>
        <a href="index.php" style="color: #fff; text-decoration: none;">Về trang chủ</a>
    </header>

    <div class="container">
        <aside class="sidebar">
            <h3>Tài khoản</h3>
            <p><strong><?= htmlspecialchars($user['username']) ?></strong></p>
            <p style="font-size: 13px; color: #666; margin-bottom: 15px;"><?= htmlspecialchars($user['email']) ?></p>
            
            <a href="#">Lịch sử mua hàng</a>
            <a href="#">Đổi mật khẩu</a>
            <a href="profile.php?action=logout" class="text-danger">Đăng xuất</a>
        </aside>

        <main class="content">
            <h2>Lịch sử đơn hàng</h2>
            
            <?php if(count($orders) == 0): ?>
                <p>Bạn chưa có đơn hàng nào. <a href="index.php">Mua sắm ngay</a>.</p>
            <?php else: ?>
                <?php foreach($orders as $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <span>Mã đơn: <strong>#<?= $order['id'] ?></strong> | Ngày: <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></span>
                            <span class="status <?= $order['status'] ?>">
                                <?= $order['status'] == 'pending' ? 'Chờ xử lý' : ($order['status'] == 'completed' ? 'Hoàn thành' : $order['status']) ?>
                            </span>
                        </div>
                        <div class="order-items">
                            <?php
                            // Lấy chi tiết các sản phẩm trong đơn hàng này
                            $stmt_details = $pdo->prepare("
                                SELECT od.*, p.name 
                                FROM order_details od 
                                JOIN products p ON od.product_id = p.id 
                                WHERE od.order_id = ?
                            ");
                            $stmt_details->execute([$order['id']]);
                            $details = $stmt_details->fetchAll();
                            
                            foreach($details as $item):
                            ?>
                                <div class="item-row">
                                    <span><?= htmlspecialchars($item['name']) ?> <strong>x<?= $item['quantity'] ?></strong></span>
                                    <span><?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?> đ</span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="order-footer">
                            Tổng tiền: <?= number_format($order['total_amount'], 0, ',', '.') ?> VNĐ
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </main>
    </div>

    <?php include 'includes/footer.php'; ?>

</body>
</html>