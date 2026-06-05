<?php
session_start();

$host = 'localhost';
$dbname = 'ecommerce_db';
$username = 'root';
$password = '';

$error = '';


$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username']);
    $pass = $_POST['password'];
    $redirect = $_POST['redirect'];

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$user]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);


        if ($userData && password_verify($pass, $userData['password_hash'])) {

            $_SESSION['user_id'] = $userData['id'];
            $_SESSION['username'] = $userData['username'];
            $_SESSION['role'] = $userData['role'];


            header("Location: " . $redirect);
            exit;
        } else {
            $error = "Sai tên đăng nhập hoặc mật khẩu!";
        }
    } catch (PDOException $e) {
        $error = "Lỗi hệ thống: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - E-STORE</title>
    <style>
        :root { --primary-color: #111; --hover-color: #c90000; --border-color: #eaeaea; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background-color: #f9f9f9; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .auth-box { background: #fff; border: 1px solid var(--border-color); padding: 40px; width: 100%; max-width: 400px; border-radius: 4px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .auth-box h2 { text-align: center; margin-bottom: 25px; text-transform: uppercase; letter-spacing: 1px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 5px; font-size: 14px; font-weight: 600; }
        .form-control { width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 4px; font-size: 14px; }
        .btn-auth { width: 100%; padding: 12px; background: var(--primary-color); color: #fff; border: none; font-weight: bold; text-transform: uppercase; cursor: pointer; transition: background 0.3s; margin-top: 10px; }
        .btn-auth:hover { background: var(--hover-color); }
        .alert { padding: 10px; margin-bottom: 15px; font-size: 14px; border-radius: 4px; text-align: center; background: #fde8e8; color: #e53e3e; }
        .link-text { text-align: center; margin-top: 20px; font-size: 14px; }
        .link-text a { color: var(--hover-color); text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

    <div class="auth-box">
        <h2>Đăng Nhập</h2>

        <?php if ($error): ?> <div class="alert"><?= $error ?></div> <?php endif; ?>

        <form action="login.php" method="POST">
            <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">

            <div class="form-group">
                <label>Tên tài khoản</label>
                <input type="text" name="username" class="form-control" required placeholder="Nhập tên tài khoản">
            </div>
            <div class="form-group">
                <label>Mật khẩu</label>
                <input type="password" name="password" class="form-control" required placeholder="Nhập mật khẩu">
            </div>
            <button type="submit" class="btn-auth">Đăng nhập</button>
        </form>

        <div class="link-text">
            Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a>
        </div>
    </div>

</body>
</html>