<style>
    /* Footer Style */
    .custom-footer { background-color: #f1f5f9; color: #333; padding: 40px 0; border-top: 1px solid #d1d5db; margin-top: 50px; font-size: 13px; clear: both; }
    .footer-container { max-width: 1200px; margin: 0 auto; padding: 0 20px; display: flex; justify-content: space-between; gap: 30px; }
    .footer-col { flex: 1; }
    .footer-col h4 { font-size: 15px; margin-bottom: 15px; color: #111; }
    .footer-col p { margin-bottom: 10px; line-height: 1.5; }
    .footer-col strong { color: #111; }
    .footer-badges { display: flex; flex-direction: column; gap: 15px; align-items: flex-start; }
    .footer-badges img { max-width: 150px; }
    
    /* 1. KHỐI TIỆN ÍCH NỔI BÊN PHẢI (Chat, Phone, Lên đầu trang) */
    .floating-right {
        position: fixed;
        bottom: 30px;
        right: 30px;
        display: flex;
        flex-direction: column;
        gap: 15px;
        z-index: 9999;
        align-items: flex-end;
    }
    .float-btn {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        text-decoration: none;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        transition: transform 0.3s;
        cursor: pointer;
        border: none;
        font-size: 20px;
    }
    .float-btn:hover { transform: scale(1.1); }
    
    /* Cấu hình màu sắc các nút mạng xã hội */
    .btn-top { background-color: #444; }
    .btn-zalo { background-color: #0088FF; }
    .btn-mes { background-color: #006AFF; }
    .btn-phone { 
        background-color: #fff; 
        color: #c90000; 
        width: auto; 
        border-radius: 25px; 
        padding: 0 20px; 
        font-weight: bold; 
        font-size: 18px;
        border: 2px solid #c90000;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* 2. BẢN ĐỒ NỔI BÊN TRÁI */
    .floating-left {
        position: fixed;
        bottom: 30px;
        left: 30px;
        z-index: 9999;
        width: 320px;
        height: 200px;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        transition: transform 0.3s;
        border: 3px solid #fff;
    }
    .floating-left:hover { transform: scale(1.02); }

    /* Responsive: Ẩn map trên điện thoại để không chắn màn hình */
    @media (max-width: 768px) {
        .footer-container { flex-direction: column; }
        .floating-left { display: none; } 
    }
</style>

<footer class="custom-footer">
    <div class="footer-container">
        <div class="footer-col">
            <h4>CÔNG TY CỔ PHẦN THƯƠNG MẠI - DỊCH VỤ E-STORE</h4>
            <p>© 2025 - 2026 Công Ty Cổ Phần Thương Mại - Dịch Vụ E-STORE</p>
            <p>Giấy chứng nhận đăng ký doanh nghiệp: 0304998358 do Sở KH-ĐT TP.HCM cấp lần đầu ngày 30 tháng 05 năm 2025</p>
            <p>Website ecommerce_web thuộc quyền sở hữu của Công ty Cổ phần Thương Mại - Dịch Vụ E-STORE và được phát triển bởi Teko.</p>
        </div>
        <div class="footer-col">
            <p><strong>Địa chỉ trụ sở chính:</strong><br>Tầng 5, 117-119-121 Nguyễn Du, Phường Bến Thành, Thành Phố Hồ Chí Minh, Việt Nam</p>
            <p><strong>Văn phòng điều hành miền Bắc:</strong><br>Tầng 2, Số 47 Phố Thái Hà, Phường Đống Đa, Thành phố Hà Nội, Việt Nam</p>
            <p><strong>Văn phòng điều hành miền Nam:</strong><br>677/2A Điện Biên Phủ, Phường Thạnh Mỹ Tây, Thành phố Hồ Chí Minh, Việt Nam</p>
        </div>
        <div class="footer-col footer-badges">
            <img src="assets/images/logo_bct.png" alt="Đã thông báo Bộ Công Thương">
            <img src="assets/images/logo_dmca.png" alt="DMCA Protected">
        </div>
    </div>
</footer>

<div class="floating-left">
    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d8858.60558111296!2d105.80717230463422!3d21.009272715491782!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ac81fe967f59%3A0x9bd0c3303b7f501a!2zUGhvbmcgVsWpIFRow6FpIEjDoA!5e0!3m2!1sen!2sus!4v1780696259790!5m2!1sen!2sus" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
</div>

<div class="floating-right">
    <button class="float-btn btn-top" onclick="window.scrollTo({top: 0, behavior: 'smooth'})" title="Lên đầu trang">
        ▲
    </button>
    <a href="tel:18006867" class="float-btn btn-phone">
        📞 1800 6867
    </a>
    <a href="https://zalo.me" target="_blank" class="float-btn btn-zalo" title="Chat Zalo">
        <img src="assets/images/icon_zalo.svg" width="25" height="25" alt="Zalo">
    </a>
    <a href="https://m.me" target="_blank" class="float-btn btn-mes" title="Chat Messenger">
        <img src="assets/images/icon_mess.svg" width="25" height="25" alt="Messenger">
    </a>
</div>