<?php
session_start();

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

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['product_id']) && !isset($_POST['action'])) {
        $product_id = (int)$_POST['product_id'];
        

        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity']++;
        } else {

            $stmt = $pdo->prepare("SELECT id, name, price, image_url FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch();
            
            if ($product) {
                $_SESSION['cart'][$product_id] = [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'image_url' => $product['image_url'],
                    'quantity' => 1
                ];
            }
        }

        header("Location: cart.php");
        exit;
    }
    

    if (isset($_POST['action'])) {
        $product_id = (int)$_POST['update_id'];
        
        if ($_POST['action'] === 'update') {
            $new_quantity = (int)$_POST['quantity'];
            if ($new_quantity > 0) {
                $_SESSION['cart'][$product_id]['quantity'] = $new_quantity;
            } else {
                unset($_SESSION['cart'][$product_id]);
            }
        } elseif ($_POST['action'] === 'remove') {
            unset($_SESSION['cart'][$product_id]);
        }
        header("Location: cart.php");
        exit;
    }
}


$total_cart_value = 0;
foreach ($_SESSION['cart'] as $item) {
    $total_cart_value += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng - E-STORE</title>
    <style>
        :root {
            --primary-color: #111;
            --hover-color: #c90000;
            --bg-light: #f9f9f9;
            --text-color: #333;
            --border-color: #eaeaea;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background-color: #fff; color: var(--text-color); line-height: 1.6; }

        header { background-color: var(--primary-color); color: #fff; padding: 15px 50px; display: flex; justify-content: space-between; align-items: center; }
        .logo { font-size: 24px; font-weight: bold; text-transform: uppercase; color: #fff; text-decoration: none; }
        nav ul { list-style: none; display: flex; gap: 30px; }
        nav ul li a { color: #fff; text-decoration: none; font-size: 15px; text-transform: uppercase; }
        nav ul li a:hover { color: var(--hover-color); }
        .header-actions a { color: #fff; text-decoration: none; margin-left: 20px; font-weight: bold; }

        .container { max-width: 1000px; margin: 50px auto; padding: 0 20px; min-height: 50vh; }
        .page-title { text-align: center; margin-bottom: 40px; font-size: 28px; text-transform: uppercase; }
        .page-title::after { content: ''; display: block; width: 60px; height: 3px; background-color: var(--primary-color); margin: 15px auto 0; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid var(--border-color); }
        th { background-color: var(--bg-light); text-transform: uppercase; font-size: 14px; }
        
        .cart-item-name { font-weight: bold; color: var(--primary-color); text-decoration: none; }
        .qty-input { width: 60px; padding: 5px; text-align: center; border: 1px solid var(--border-color); }
        
        .btn { padding: 8px 15px; border: none; cursor: pointer; font-size: 13px; text-transform: uppercase; font-weight: bold; transition: background 0.3s; text-decoration: none; display: inline-block; }
        .btn-update { background-color: #eee; color: #333; }
        .btn-update:hover { background-color: #ddd; }
        .btn-remove { background-color: transparent; color: var(--hover-color); padding: 0; text-transform: none; text-decoration: underline; }
        .btn-primary { background-color: var(--primary-color); color: #fff; }
        .btn-primary:hover { background-color: var(--hover-color); }

        .cart-summary { background-color: var(--bg-light); padding: 30px; border-radius: 4px; text-align: right; }
        .cart-summary h3 { font-size: 24px; margin-bottom: 20px; color: var(--hover-color); }
        .cart-actions { display: flex; justify-content: flex-end; gap: 15px; margin-top: 20px; }

        .empty-cart { text-align: center; padding: 50px; background: var(--bg-light); border-radius: 4px; }
        
        footer { background-color: var(--primary-color); color: #fff; text-align: center; padding: 20px; margin-top: 50px; font-size: 14px; }
    </style>
</head>
<body>

    <header>
        <a href="index.php" class="logo">E-STORE</a>
        <nav>
            <ul>
                <li><a href="index.php">Trang chủ</a></li>
                <li><a href="#">Sản phẩm</a></li>
            </ul>
        </nav>
        <div class="header-actions">
            <a href="cart.php">Giỏ hàng (<?= count($_SESSION['cart']) ?>)</a>
        </div>
    </header>

    <main class="container">
        <h1 class="page-title">Giỏ hàng của bạn</h1>

        <?php if (empty($_SESSION['cart'])): ?>
            <div class="empty-cart">
                <p style="margin-bottom: 20px; font-size: 18px;">Giỏ hàng hiện đang trống.</p>
                <a href="index.php" class="btn btn-primary">Tiếp tục mua sắm</a>
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Đơn giá</th>
                        <th>Số lượng</th>
                        <th>Thành tiền</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['cart'] as $id => $item): ?>
                        <tr>
                            <td>
                                <a href="#" class="cart-item-name"><?= htmlspecialchars($item['name']) ?></a>
                            </td>
                            <td><?= number_format($item['price'], 0, ',', '.') ?> đ</td>
                            <td>
                                <form action="cart.php" method="POST" style="display: flex; gap: 10px; align-items: center;">
                                    <input type="hidden" name="update_id" value="<?= $id ?>">
                                    <input type="hidden" name="action" value="update">
                                    <input type="number" name="quantity" class="qty-input" value="<?= $item['quantity'] ?>" min="1">
                                    <button type="submit" class="btn btn-update">Cập nhật</button>
                                </form>
                            </td>
                            <td style="font-weight: bold;"><?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?> đ</td>
                            <td>
                                <form action="cart.php" method="POST">
                                    <input type="hidden" name="update_id" value="<?= $id ?>">
                                    <input type="hidden" name="action" value="remove">
                                    <button type="submit" class="btn btn-remove">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="cart-summary">
                <p style="font-size: 16px; margin-bottom: 10px;">Tổng tiền thanh toán:</p>
                <h3><?= number_format($total_cart_value, 0, ',', '.') ?> VNĐ</h3>
                
                <div class="cart-actions">
                    <a href="index.php" class="btn btn-update">Tiếp tục mua sắm</a>
                    <a href="checkout.php" class="btn btn-primary">Tiến hành thanh toán</a>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <?php include 'includes/footer.php'; ?>

</body>
</html>