git clone https://github.com/vietanh116/ecommerce_web.git
Bước 1: Đưa mã nguồn vào thư mục của XAMPP
Thay vì thư mục ⁠www⁠ của Laragon, XAMPP quản lý các dự án web trong thư mục ⁠htdocs⁠.
1 Sao chép (hoặc sao lưu từ kho mã nguồn về) toàn bộ thư mục dự án ⁠ecommerce_web⁠.
2 Truy cập vào đường dẫn cài đặt của XAMPP trên máy tính (thường là ⁠C:\xampp\htdocs\⁠).
3 Dán thư mục dự án vào đây. Đường dẫn chính xác sẽ là: ⁠C:\xampp\htdocs\ecommerce_web\⁠.
Bước 2: Kiểm tra cấu hình kết nối CSDL
Mở file ⁠config/database.php⁠ để đảm bảo thông số kết nối khớp với cấu hình mặc định của XAMPP:
 Mặc định cả Laragon và XAMPP đều dùng chung cấu hình hệ thống: tài khoản là ⁠root⁠ và mật khẩu để trống (⁠''⁠). Do đó, thông thường bạn sẽ không cần phải chỉnh sửa gì ở file cấu hình này.
Bước 3: Bật các dịch vụ trên XAMPP Control Panel
1 Tìm và mở ứng dụng XAMPP Control Panel trên máy tính.
2 Tại dòng Apache, bấm nút Start.
3 Tại dòng MySQL, bấm nút Start.
 Khi cả hai chữ Apache và MySQL chuyển sang màu nền xanh lá cây và hiển thị số cổng (Port) hoạt động, máy chủ cục bộ đã sẵn sàng.
Bước 4: Tạo Cơ sở dữ liệu qua phpMyAdmin
Thay vì dùng phần mềm HeidiSQL độc lập, XAMPP tích hợp sẵn công cụ quản lý dữ liệu trên trình duyệt là phpMyAdmin.
1 Mở trình duyệt web và truy cập đường dẫn: ⁠http://localhost/phpmyadmin/⁠
2 Chọn mục New (Mới) ở cột bên trái -> Nhập tên cơ sở dữ liệu là ⁠ecommerce_db⁠ -> Bấm Create (Tạo).
3 Bấm chọn vào database ⁠ecommerce_db⁠ vừa tạo, nhìn lên thanh menu chức năng trên cùng và chọn tab SQL.
4 Mở file ⁠database/init_db.sql⁠ trong dự án của bạn, sao chép toàn bộ nội dung câu lệnh bên trong, dán vào khung nhập liệu SQL của phpMyAdmin rồi bấm nút Go (Thực hiện) ở góc dưới bên phải để khởi tạo cấu trúc bảng.
Bước 5: Bơm dữ liệu mẫu và Chạy thử website
1 Mở một tab mới trên trình duyệt, truy cập đường dẫn: ⁠http://localhost/ecommerce_web/seed.php⁠ để hệ thống tự động đổ dữ liệu sản phẩm, tài khoản vào các bảng trống.
2 Sau khi màn hình báo thành công, truy cập vào trang chủ qua đường dẫn: ⁠http://localhost/ecommerce_web/index.php⁠.