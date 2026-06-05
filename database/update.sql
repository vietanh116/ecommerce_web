ALTER TABLE products ADD COLUMN old_price DECIMAL(10, 2) NULL AFTER price;
ALTER TABLE products ADD COLUMN sold_count INT DEFAULT 0 AFTER stock_quantity;

-- Giả lập dữ liệu số lượng bán ra
UPDATE products SET sold_count = 150 WHERE id = 1;
UPDATE products SET sold_count = 320, old_price = 1500000 WHERE id = 2; -- Bàn phím Aula Sale từ 1.5tr xuống 1.25tr
UPDATE products SET sold_count = 85 WHERE id = 3;
UPDATE products SET sold_count = 410, old_price = 800000 WHERE id = 4; -- Chuột Shark Sale từ 800k xuống 600k

ALTER TABLE products ADD COLUMN short_description VARCHAR(255) NULL AFTER old_price;

-- Cập nhật mô tả ngắn phù hợp cho từng loại sản phẩm
UPDATE products SET short_description = 'Chuột không dây siêu nhẹ, cảm biến độ nhạy cao, form cầm thoải mái.' WHERE id = 4;
UPDATE products SET short_description = 'Bàn phím cơ TKL, có núm xoay đa năng, led RGB sặc sỡ.' WHERE id = 2;
UPDATE products SET short_description = 'Bàn phím cơ mạch xuôi, gõ êm, kết nối 3 chế độ tiện lợi.' WHERE id = 3;
UPDATE products SET short_description = 'Laptop gaming hiệu năng cao, tản nhiệt mát, màn hình 144Hz mượt mà.' WHERE id = 1;