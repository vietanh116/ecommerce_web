<?php
session_start();
require_once 'config/database.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$product_id = (int)$_GET['id'];

// 1. Lấy thông tin chi tiết của sản phẩm hiện tại
$stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    die("Sản phẩm không tồn tại!");
}

// 2. Lấy danh sách Categories để đưa lên Menu điều hướng ngang
$stmt_cats = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $stmt_cats->fetchAll();

// 3. Lấy các sản phẩm liên quan (5 sản phẩm)
$stmt_related = $pdo->prepare("SELECT * FROM products WHERE category_id = ? AND id != ? LIMIT 5");
$stmt_related->execute([$product['category_id'], $product_id]);
$related_products = $stmt_related->fetchAll();

$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?> - E-STORE</title>
    <style>
        :root { --primary-color: #111; --hover-color: #c90000; --bg-light: #f9f9f9; --border-color: #eaeaea; --text-color: #333; --blue-color: #1a4388; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background-color: #fff; color: var(--text-color); line-height: 1.6; }
        
        /* Header & Navigation ngang */
        header { background-color: var(--primary-color); color: #fff; padding: 15px 50px; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 100; }
        .logo { font-size: 24px; font-weight: bold; text-transform: uppercase; color: #fff; text-decoration: none; }
        nav ul { list-style: none; display: flex; gap: 20px; }
        nav ul li a { color: #fff; text-decoration: none; font-size: 14px; text-transform: uppercase; font-weight: 500; padding: 5px 10px; transition: 0.3s; }
        nav ul li a:hover { color: var(--hover-color); }
        .header-actions a { color: #fff; text-decoration: none; margin-left: 20px; font-weight: bold; font-size: 14px; }
        
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; min-height: 60vh; }
        
        /* Breadcrumb */
        .breadcrumb { margin-bottom: 20px; font-size: 14px; }
        .breadcrumb a { color: #666; text-decoration: none; }
        .breadcrumb a:hover { color: var(--hover-color); }
        
        /* Product Layout (Top Section) */
        .product-wrapper { display: flex; gap: 40px; margin-bottom: 40px; }
        .product-image-box { flex: 1; border: 1px solid var(--border-color); border-radius: 4px; padding: 20px; display: flex; align-items: center; justify-content: center; }
        .product-image-box img { max-width: 100%; max-height: 400px; object-fit: contain; }
        
        .product-info-box { flex: 1; }
        .product-title { font-size: 28px; margin-bottom: 10px; color: var(--primary-color); }
        .product-price { font-size: 24px; font-weight: bold; color: var(--hover-color); margin-bottom: 20px; }
        .product-short-desc { margin-bottom: 20px; color: #555; }
        
        .btn-add-cart { display: inline-block; padding: 12px 25px; background-color: var(--hover-color); color: #fff; border: none; cursor: pointer; text-transform: uppercase; font-weight: bold; font-size: 14px; border-radius: 4px; transition: 0.3s; }
        .btn-add-cart:hover { background-color: #a00000; }

        /* TAB SYSTEM (Thông số & Chi tiết) */
        .tabs-container { margin-top: 40px; border: 1px solid var(--border-color); border-radius: 4px; background: #fff; }
        .tab-buttons { display: flex; border-bottom: 1px solid var(--border-color); background: #fafafa; }
        .tab-btn { flex: 1; padding: 15px; background: none; border: none; font-size: 16px; font-weight: bold; color: #666; cursor: pointer; border-bottom: 2px solid transparent; transition: 0.3s; }
        .tab-btn:hover { color: var(--blue-color); }
        .tab-btn.active { color: var(--blue-color); border-bottom-color: var(--blue-color); background: #fff; }
        
        .tab-content { display: none; padding: 30px; }
        .tab-content.active { display: block; }
        
        /* Bảng thông số kỹ thuật */
        .specs-table { width: 100%; border-collapse: collapse; }
        .specs-table tr:nth-child(odd) { background-color: #f8f9fa; }
        .specs-table th, .specs-table td { padding: 15px; border: none; font-size: 14px; }
        .specs-table th { width: 30%; text-align: left; color: #555; font-weight: normal; }
        .specs-table td { color: #111; font-weight: 500; }

        /* Related Products (5 items per row) */
        .related-section { margin-top: 60px; padding-top: 30px; }
        .related-section h3 { margin-bottom: 20px; font-size: 22px; text-transform: uppercase; }
        .product-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 15px; margin-bottom: 50px; }
        .product-card { border: 1px solid var(--border-color); border-radius: 4px; overflow: hidden; transition: box-shadow 0.3s; display: flex; flex-direction: column; background: #fff; }
        .product-card:hover { box-shadow: 0 5px 15px rgba(0,0,0,0.08); }
        .product-card img { width: 100%; height: 180px; object-fit: contain; padding: 15px; }
        .product-card-info { padding: 15px; display: flex; flex-direction: column; flex-grow: 1; }
        .product-card-title { font-size: 14px; color: #333; text-decoration: none; margin-bottom: 10px; flex-grow: 1; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .product-card-title:hover { color: var(--blue-color); }
        .product-card-price { color: var(--blue-color); font-weight: bold; font-size: 16px; margin-bottom: 15px; }
        
        /* Nút thêm vào giỏ viền xanh */
        .btn-outline-blue { display: block; width: 100%; padding: 8px 0; text-align: center; border: 1px solid var(--blue-color); color: var(--blue-color); background: transparent; text-decoration: none; font-size: 13px; font-weight: 600; border-radius: 4px; transition: 0.3s; cursor: pointer; }
        .btn-outline-blue:hover { background: var(--blue-color); color: #fff; }
    </style>
</head>
<body>

    <header>
        <a href="index.php" class="logo">E-STORE</a>
        <nav>
            <ul>
                <li><a href="index.php">Trang chủ</a></li>
                <?php foreach($categories as $cat): ?>
                    <li>
                        <a href="index.php?category=<?= $cat['id'] ?>">
                            <?= htmlspecialchars($cat['name']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
                
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li><a href="profile.php">Tài khoản</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <div class="header-actions">
            <?php if(!isset($_SESSION['user_id'])): ?>
                <a href="login.php">Đăng nhập</a>
            <?php else: ?>
                <a href="profile.php">Chào, <?= htmlspecialchars($_SESSION['username']) ?></a>
            <?php endif; ?>
            <a href="cart.php">Giỏ hàng (<?= $cart_count ?>)</a>
        </div>
    </header>

    <main class="container">
        <div class="breadcrumb">
            <a href="index.php">Trang chủ</a> &gt; 
            <a href="index.php?category=<?= $product['category_id'] ?>"><?= htmlspecialchars($product['category_name']) ?></a> &gt; 
            <strong><?= htmlspecialchars($product['name']) ?></strong>
        </div>

        <div class="product-wrapper">
            <div class="product-image-box">
                <img src="assets/images/<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" onerror="this.src='https://via.placeholder.com/400x400?text=No+Image'">
            </div>
            <div class="product-info-box">
                <h1 class="product-title"><?= htmlspecialchars($product['name']) ?></h1>
                
                <?php if(!empty($product['old_price']) && $product['old_price'] > $product['price']): ?>
                    <div style="text-decoration: line-through; color: #999; font-size: 16px; margin-bottom: 5px;">
                        <?= number_format($product['old_price'], 0, ',', '.') ?> đ
                    </div>
                <?php endif; ?>
                
                <div class="product-price"><?= number_format($product['price'], 0, ',', '.') ?> đ</div>
                
                <p class="product-short-desc">
                    <?= htmlspecialchars($product['short_description'] ?: 'Đang cập nhật thông tin mô tả ngắn.') ?>
                </p>
                
                <p style="margin-bottom: 20px; font-size: 14px;">
                    Tình trạng: <strong style="color: <?= $product['stock_quantity'] > 0 ? 'green' : 'red' ?>;"><?= $product['stock_quantity'] > 0 ? 'Còn hàng' : 'Hết hàng' ?></strong>
                </p>
                
                <form action="cart.php" method="POST">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <button type="submit" class="btn-add-cart" <?= $product['stock_quantity'] == 0 ? 'disabled' : '' ?>>
                        Thêm Vào Giỏ Hàng
                    </button>
                </form>
            </div>
        </div>

        <div class="tabs-container">
            <div class="tab-buttons">
                <button class="tab-btn active" onclick="openTab(event, 'tab-specs')">Thông số kỹ thuật</button>
                <button class="tab-btn" onclick="openTab(event, 'tab-details')">Chi tiết sản phẩm</button>
            </div>
            
            <div id="tab-specs" class="tab-content active">
                <table class="specs-table">
                    <tr>
                        <th>Danh mục</th>
                        <td><?= htmlspecialchars($product['category_name']) ?></td>
                    </tr>
                    <tr>
                        <th>Thương hiệu</th>
                        <td>Đang cập nhật</td>
                    </tr>
                    <tr>
                        <th>Bảo hành</th>
                        <td>12 tháng</td>
                    </tr>
                    <tr>
                        <th>Tình trạng</th>
                        <td>Mới 100% - Chính hãng</td>
                    </tr>
                    <tr>
                        <th>Kho hàng</th>
                        <td><?= htmlspecialchars($product['stock_quantity']) ?> sản phẩm</td>
                    </tr>
                </table>
            </div>

            <div id="tab-details" class="tab-content">
                <h3 style="margin-bottom: 15px; font-size: 18px;">Mô tả sản phẩm</h3>
                <p style="color: #444; line-height: 1.8;">
                    <?= nl2br(htmlspecialchars($product['description'])) ?>
                </p>
            </div>
        </div>

        <?php if (count($related_products) > 0): ?>
        <div class="related-section">
            <h3>Sản phẩm liên quan</h3>
            <div class="product-grid">
                <?php foreach ($related_products as $rel): ?>
                    <div class="product-card">
                        <a href="product_detail.php?id=<?= $rel['id'] ?>">
                            <img src="assets/images/<?= htmlspecialchars($rel['image_url']) ?>" alt="<?= htmlspecialchars($rel['name']) ?>" onerror="this.src='https://via.placeholder.com/200x200?text=No+Image'">
                        </a>
                        <div class="product-card-info">
                            <a href="product_detail.php?id=<?= $rel['id'] ?>" class="product-card-title"><?= htmlspecialchars($rel['name']) ?></a>
                            <div class="product-card-price"><?= number_format($rel['price'], 0, ',', '.') ?> đ</div>
                            <form action="cart.php" method="POST" style="margin-top: auto;">
                                <input type="hidden" name="product_id" value="<?= $rel['id'] ?>">
                                <button type="submit" class="btn-outline-blue">Thêm vào giỏ</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script>
        function openTab(evt, tabName) {
            var tabcontent = document.getElementsByClassName("tab-content");
            for (var i = 0; i < tabcontent.length; i++) {
                tabcontent[i].classList.remove("active");
            }
            
            var tablinks = document.getElementsByClassName("tab-btn");
            for (var i = 0; i < tablinks.length; i++) {
                tablinks[i].classList.remove("active");
            }
            
            document.getElementById(tabName).classList.add("active");
            evt.currentTarget.classList.add("active");
        }
    </script>

</body>
</html>