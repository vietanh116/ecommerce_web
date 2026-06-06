<?php
session_start();
require_once 'config/database.php';

// 1. XỬ LÝ ĐĂNG NHẬP ADMIN (Tài khoản mặc định: admin / 123456)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login_admin'])) {
    if ($_POST['username'] === 'admin' && $_POST['password'] === '123456') {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin.php");
        exit;
    } else {
        $error = "Sai tài khoản hoặc mật khẩu!";
    }
}

// 2. XỬ LÝ ĐĂNG XUẤT
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    unset($_SESSION['admin_logged_in']);
    header("Location: admin.php");
    exit;
}

// Kiểm tra trạng thái đăng nhập
$is_admin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

// 3. XỬ LÝ CẬP NHẬT SẢN PHẨM (SỬA BẢN GHI VÀO CSDL)
if ($is_admin && $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_product'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $old_price = !empty($_POST['old_price']) ? $_POST['old_price'] : NULL;
    $short_desc = $_POST['short_description'];
    $stock = $_POST['stock_quantity'];

    $sql = "UPDATE products SET name = ?, price = ?, old_price = ?, short_description = ?, stock_quantity = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$name, $price, $old_price, $short_desc, $stock, $id]);
    
    $success_msg = "Cập nhật thông tin sản phẩm thành công!";
}

// Điều hướng hành động
$action = $_GET['action'] ?? 'list';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang Quản Trị - E-STORE</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; margin: 0; }
        .container { max-width: 1000px; margin: 50px auto; background: #fff; padding: 30px; box-shadow: 0 0 15px rgba(0,0,0,0.1); border-radius: 8px; }
        h1, h2 { color: #333; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 5px; color: #555; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .btn { padding: 10px 20px; background: #1a4388; color: #fff; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; font-weight: bold; }
        .btn:hover { background: #112c5b; }
        .btn-danger { background: #c90000; }
        .btn-danger:hover { background: #a00000; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th, .table td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        .table th { background-color: #f8f9fa; }
        .alert { padding: 15px; background: #d4edda; color: #155724; border-radius: 4px; margin-bottom: 20px; border-left: 5px solid #28a745; }
        .alert-danger { background: #f8d7da; color: #721c24; border-left: 5px solid #dc3545; }
    </style>
</head>
<body>

<div class="container">
    
    <?php if (!$is_admin): ?>
        <h2 style="text-align: center; margin-bottom: 30px;">Đăng nhập hệ thống quản trị</h2>
        <div style="max-width: 400px; margin: 0 auto;">
            <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
            <form method="POST">
                <div class="form-group">
                    <label>Tên đăng nhập</label>
                    <input type="text" name="username" class="form-control" required placeholder="Nhập: admin">
                </div>
                <div class="form-group">
                    <label>Mật khẩu</label>
                    <input type="password" name="password" class="form-control" required placeholder="Nhập: 123456">
                </div>
                <button type="submit" name="login_admin" class="btn" style="width: 100%;">Đăng nhập</button>
            </form>
        </div>

    <?php else: ?>
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #eee; padding-bottom: 15px; margin-bottom: 20px;">
            <h1 style="margin: 0;">Bảng Điều Khiển Admin</h1>
            <a href="admin.php?action=logout" class="btn btn-danger">Đăng xuất</a>
        </div>

        <?php if (isset($success_msg)) echo "<div class='alert'>$success_msg</div>"; ?>

        <?php if ($action == 'list'): ?>
            <h2>Danh sách bản ghi sản phẩm</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên sản phẩm</th>
                        <th>Giá bán</th>
                        <th>Kho</th>
                        <th style="width: 100px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
                    while ($row = $stmt->fetch()):
                    ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= number_format($row['price'], 0, ',', '.') ?> đ</td>
                        <td><?= $row['stock_quantity'] ?></td>
                        <td>
                            <a href="admin.php?action=edit&id=<?= $row['id'] ?>" class="btn" style="padding: 6px 15px; font-size: 13px;">Sửa</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <br>
            <a href="index.php" class="btn" style="background: #666;">Mở trang web khách hàng</a>

        <?php elseif ($action == 'edit' && isset($_GET['id'])): ?>
            <?php
            $id = (int)$_GET['id'];
            $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->execute([$id]);
            $product = $stmt->fetch();
            
            if (!$product) {
                echo "<div class='alert alert-danger'>Không tìm thấy sản phẩm!</div>";
            } else {
            ?>
            <h2>Sửa bản ghi: <?= htmlspecialchars($product['name']) ?></h2>
            <form method="POST" action="admin.php?action=edit&id=<?= $id ?>" style="background: #fdfdfd; padding: 20px; border: 1px solid #eee; border-radius: 4px;">
                <input type="hidden" name="id" value="<?= $product['id'] ?>">
                
                <div class="form-group">
                    <label>Tên sản phẩm</label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
                </div>
                
                <div style="display: flex; gap: 20px;">
                    <div class="form-group" style="flex: 1;">
                        <label>Giá bán hiện tại (VNĐ)</label>
                        <input type="number" name="price" class="form-control" value="<?= $product['price'] ?>" required>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Giá cũ (Khuyến mãi - VNĐ)</label>
                        <input type="number" name="old_price" class="form-control" value="<?= $product['old_price'] ?>">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Số lượng trong kho</label>
                        <input type="number" name="stock_quantity" class="form-control" value="<?= $product['stock_quantity'] ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Mô tả ngắn gọn</label>
                    <textarea name="short_description" class="form-control" rows="3"><?= htmlspecialchars($product['short_description']) ?></textarea>
                </div>

                <div style="margin-top: 20px;">
                    <button type="submit" name="update_product" class="btn">Lưu thay đổi</button>
                    <a href="admin.php" class="btn" style="background: #888; margin-left: 10px;">Hủy bỏ / Quay lại</a>
                </div>
            </form>
            <?php } ?>
        <?php endif; ?>

    <?php endif; ?>

</div>

</body>
</html>
