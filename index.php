<?php
session_start();
require_once 'config/database.php';

// Lấy danh sách Categories để đưa lên Menu điều hướng
$stmt_cats = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $stmt_cats->fetchAll();

// Kiểm tra xem người dùng đang xem trang chủ hay đang lọc theo danh mục
$category_filter = isset($_GET['category']) && is_numeric($_GET['category']) ? (int)$_GET['category'] : 0;

if ($category_filter > 0) {
    // NẾU ĐANG LỌC DANH MỤC: Chỉ lấy sản phẩm của danh mục đó
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.category_id = ? ORDER BY p.id DESC");
    $stmt->execute([$category_filter]);
    $category_products = $stmt->fetchAll();
    
    // Lấy tên danh mục đang xem
    $stmt_cat_name = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
    $stmt_cat_name->execute([$category_filter]);
    $current_category_name = $stmt_cat_name->fetchColumn();
} else {
    // NẾU Ở TRANG CHỦ: Lấy dữ liệu cho 2 mục Bán Chạy và Sale
    // 1. Sản phẩm bán chạy (Sắp xếp theo sold_count giảm dần)
    $stmt_best = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.sold_count DESC LIMIT 8");
    $bestseller_products = $stmt_best->fetchAll();

    // 2. Sản phẩm đang Sale (Có old_price và old_price > price)
    $stmt_sale = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.old_price IS NOT NULL AND p.old_price > p.price ORDER BY p.id DESC LIMIT 4");
    $sale_products = $stmt_sale->fetchAll();
}

$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ - E-STORE</title>
    <style>
        :root { --primary-color: #111; --hover-color: #c90000; --bg-light: #f9f9f9; --border-color: #eaeaea; --text-color: #333; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background-color: #fff; color: var(--text-color); line-height: 1.6; }
        
        /* Header & Navigation ngang */
        header { background-color: var(--primary-color); color: #fff; padding: 15px 50px; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 100; }
        .logo { font-size: 24px; font-weight: bold; text-transform: uppercase; color: #fff; text-decoration: none; }
        nav ul { list-style: none; display: flex; gap: 20px; }
        nav ul li a { color: #fff; text-decoration: none; font-size: 14px; text-transform: uppercase; font-weight: 500; padding: 5px 10px; transition: 0.3s; }
        nav ul li a:hover, nav ul li a.active { color: var(--hover-color); }
        .header-actions a { color: #fff; text-decoration: none; margin-left: 20px; font-weight: bold; font-size: 14px; }
        
        .container { max-width: 1200px; margin: 40px auto; padding: 0 20px; min-height: 60vh; }
        
        /* Product Sections */
        .section-title { font-size: 24px; text-transform: uppercase; margin-bottom: 25px; display: flex; align-items: center; }
        .section-title span { background: var(--hover-color); color: #fff; padding: 5px 15px; border-radius: 4px; font-size: 14px; margin-left: 15px; }
        
        .product-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 25px; margin-bottom: 50px; }
        .product-card { background: #fff; border: 1px solid var(--border-color); border-radius: 4px; overflow: hidden; display: flex; flex-direction: column; transition: transform 0.3s; position: relative; }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.08); }
        
        .sale-badge { position: absolute; top: 10px; left: 10px; background: var(--hover-color); color: #fff; font-size: 12px; font-weight: bold; padding: 5px 10px; border-radius: 4px; z-index: 2; }
        
        .product-image { width: 100%; height: 220px; padding: 15px; background: var(--bg-light); display: flex; align-items: center; justify-content: center; position: relative; }
        .product-image img { max-width: 100%; max-height: 100%; object-fit: contain; }
        
        .product-info { padding: 20px; flex-grow: 1; display: flex; flex-direction: column; }
        .product-category { font-size: 12px; color: #888; text-transform: uppercase; margin-bottom: 8px; }
        .product-title { font-size: 15px; font-weight: 600; margin-bottom: 10px; color: var(--primary-color); text-decoration: none; flex-grow: 1; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .product-title:hover { color: var(--hover-color); }
        
        .price-box { margin-bottom: 15px; }
        .old-price { font-size: 14px; color: #999; text-decoration: line-through; margin-right: 10px; }
        .product-price { font-size: 18px; font-weight: bold; color: var(--hover-color); }
        .sold-count { font-size: 12px; color: #666; margin-top: 5px; }
        
        .btn-add-cart { display: block; width: 100%; padding: 10px; text-align: center; background-color: var(--primary-color); color: #fff; border: none; cursor: pointer; text-transform: uppercase; font-weight: bold; transition: 0.3s; font-size: 13px; }
        .btn-add-cart:hover { background-color: var(--hover-color); }
    </style>
</head>
<body>

    <header>
        <a href="index.php" class="logo">E-STORE</a>
        <nav>
            <ul>
                <li><a href="index.php" class="<?= $category_filter == 0 ? 'active' : '' ?>">Trang chủ</a></li>
                <?php foreach($categories as $cat): ?>
                    <li>
                        <a href="index.php?category=<?= $cat['id'] ?>" class="<?= $category_filter == $cat['id'] ? 'active' : '' ?>">
                            <?= htmlspecialchars($cat['name']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
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
        
        <?php if ($category_filter > 0): ?>
            <h2 class="section-title">Danh mục: <?= htmlspecialchars($current_category_name) ?></h2>
            <div class="product-grid">
                <?php if (count($category_products) > 0): ?>
                    <?php foreach ($category_products as $row): ?>
                        <div class="product-card">
                            <?php if(!empty($row['old_price']) && $row['old_price'] > $row['price']): 
                                $percent = round((($row['old_price'] - $row['price']) / $row['old_price']) * 100);
                            ?>
                                <div class="sale-badge">-<?= $percent ?>%</div>
                            <?php endif; ?>
                            
                            <a href="product_detail.php?id=<?= $row['id'] ?>" style="display: block; text-decoration: none;">
                                <div class="product-image">
                                    <img src="assets/images/<?= htmlspecialchars($row['image_url']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
                                </div>
                            </a>
                            <div class="product-info">
                                <a href="product_detail.php?id=<?= $row['id'] ?>" class="product-title"><?= htmlspecialchars($row['name']) ?></a>
                                <div class="price-box">
                                    <?php if(!empty($row['old_price']) && $row['old_price'] > $row['price']): ?>
                                        <span class="old-price"><?= number_format($row['old_price'], 0, ',', '.') ?> đ</span>
                                    <?php endif; ?>
                                    <span class="product-price"><?= number_format($row['price'], 0, ',', '.') ?> đ</span>
                                </div>
                                <form action="cart.php" method="POST">
                                    <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
                                    <button type="submit" class="btn-add-cart">Thêm Vào Giỏ</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="grid-column: 1/-1;">Chưa có sản phẩm nào thuộc danh mục này.</p>
                <?php endif; ?>
            </div>

        <?php else: ?>
            <?php if (count($sale_products) > 0): ?>
                <h2 class="section-title">Đang Flash Sale <span>Giờ vàng</span></h2>
                <div class="product-grid">
                    <?php foreach ($sale_products as $row): ?>
                        <div class="product-card">
                            <?php 
                                $percent = round((($row['old_price'] - $row['price']) / $row['old_price']) * 100);
                            ?>
                            <div class="sale-badge">-<?= $percent ?>%</div>
                            
                            <a href="product_detail.php?id=<?= $row['id'] ?>" style="display: block; text-decoration: none;">
                                <div class="product-image">
                                    <img src="assets/images/<?= htmlspecialchars($row['image_url']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
                                </div>
                            </a>
                            <div class="product-info">
                                <span class="product-category"><?= htmlspecialchars($row['category_name']) ?></span>
                                <a href="product_detail.php?id=<?= $row['id'] ?>" class="product-title"><?= htmlspecialchars($row['name']) ?></a>
                                <div class="price-box">
                                    <span class="old-price"><?= number_format($row['old_price'], 0, ',', '.') ?> đ</span>
                                    <span class="product-price"><?= number_format($row['price'], 0, ',', '.') ?> đ</span>
                                </div>
                                <form action="cart.php" method="POST">
                                    <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
                                    <button type="submit" class="btn-add-cart">Thêm Vào Giỏ</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <h2 class="section-title" style="margin-top: 60px;">Sản phẩm bán chạy nhất</h2>
            <div class="product-grid">
                <?php foreach ($bestseller_products as $row): ?>
                    <div class="product-card">
                        <?php if(!empty($row['old_price']) && $row['old_price'] > $row['price']): 
                            $percent = round((($row['old_price'] - $row['price']) / $row['old_price']) * 100);
                        ?>
                            <div class="sale-badge">-<?= $percent ?>%</div>
                        <?php endif; ?>

                        <a href="product_detail.php?id=<?= $row['id'] ?>" style="display: block; text-decoration: none;">
                            <div class="product-image">
                                <img src="assets/images/<?= htmlspecialchars($row['image_url']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
                            </div>
                        </a>
                        <div class="product-info">
                            <span class="product-category"><?= htmlspecialchars($row['category_name']) ?></span>
                            <a href="product_detail.php?id=<?= $row['id'] ?>" class="product-title"><?= htmlspecialchars($row['name']) ?></a>
                            <div class="price-box">
                                <?php if(!empty($row['old_price']) && $row['old_price'] > $row['price']): ?>
                                    <span class="old-price"><?= number_format($row['old_price'], 0, ',', '.') ?> đ</span>
                                <?php endif; ?>
                                <span class="product-price"><?= number_format($row['price'], 0, ',', '.') ?> đ</span>
                            </div>
                            <div class="sold-count">Đã bán: <?= number_format($row['sold_count']) ?></div>
                            <form action="cart.php" method="POST" style="margin-top: 15px;">
                                <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
                                <button type="submit" class="btn-add-cart">Thêm Vào Giỏ</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php endif; ?>
    </main>

    <?php include 'includes/footer.php'; ?>

</body>
</html>