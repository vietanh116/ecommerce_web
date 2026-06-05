<?php
$host = 'localhost';
$dbname = 'ecommerce_db';
$username = 'root';
$password = ''; // Cập nhật mật khẩu nếu MySQL của bạn có set pass

try {

    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
    $pdo->exec("TRUNCATE TABLE order_details; TRUNCATE TABLE orders; TRUNCATE TABLE products; TRUNCATE TABLE categories; TRUNCATE TABLE users;");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");


    $passwordHash = password_hash('123456', PASSWORD_DEFAULT);
    $pdo->exec("INSERT INTO users (id, username, email, password_hash, role) VALUES 
        (1, 'admin', 'admin@gmail.com', '$passwordHash', 'admin'),
        (2, 'khachhang_01', 'khachhang@gmail.com', '$passwordHash', 'customer')
    ");


    $pdo->exec("INSERT INTO categories (id, name, description) VALUES 
        (1, 'Laptop & Máy trạm', 'Các dòng máy tính xách tay hiệu năng cao'),
        (2, 'Bàn phím cơ', 'Bàn phím cơ custom, phím gaming các loại'),
        (3, 'Phụ kiện máy tính', 'Chuột, tai nghe, lót chuột')
    ");


    $pdo->exec("INSERT INTO products (id, category_id, name, price, image_url, description, stock_quantity) VALUES 
        (1, 1, 'Laptop HP Victus 16', 21500000, 'hp_victus.jpg', 'Cấu hình mạnh mẽ, tản nhiệt tốt, tối ưu cho môi trường lập trình và chạy model AI.', 15),
        (2, 2, 'Bàn phím cơ Aula F75', 1250000, 'aula_f75.jpg', 'Bàn phím layout 75%, gõ cực êm, kết nối không dây độ trễ thấp.', 30),
        (3, 2, 'Bàn phím cơ E-Dra EK375 Pro', 850000, 'edra_ek375.jpg', 'Phím cơ quốc dân trong tầm giá, switch có độ nảy tốt, LED RGB.', 50),
        (4, 3, 'Chuột Attack Shark X85', 600000, 'attack_shark.jpg', 'Chuột không dây siêu nhẹ, form cầm thoải mái cho thời gian dài.', 40)
    ");


    $pdo->exec("INSERT INTO orders (id, user_id, total_amount, status) VALUES 
        (1, 2, 22750000, 'completed')
    ");

    // 5. Sinh dữ liệu Order Details (Chi tiết của đơn hàng trên)
    $pdo->exec("INSERT INTO order_details (order_id, product_id, quantity, price) VALUES 
        (1, 1, 1, 21500000), -- Khách mua 1 Laptop HP Victus
        (1, 2, 1, 1250000)   -- Khách mua 1 Bàn phím Aula F75
    ");

    echo "<h3>🎉 Sinh dữ liệu (Seeding) thành công!</h3>";
    echo "<p>Đã tạo sẵn tài khoản Admin và Khách hàng với mật khẩu: <strong>123456</strong></p>";
    echo "<p>Bạn có thể mở phpMyAdmin để kiểm tra các bảng dữ liệu.</p>";

} catch (PDOException $e) {
    die("<h3>❌ Lỗi kết nối hoặc thực thi CSDL: </h3>" . $e->getMessage());
}
?>