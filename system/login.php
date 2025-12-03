<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Updated to match your table structure: user_id, username, password_hash, full_name
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // Use password_verify for hashed passwords
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['fullname'] = $user['full_name'];
        $_SESSION['role'] = 'admin'; // Default role since column might not exist
        header("Location: dashboard.php");
        exit();
    } else {
        // Fallback for plain text password (just in case you manually inserted '1234')
        if ($user && $password == $user['password_hash']) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['fullname'] = $user['full_name'];
            $_SESSION['role'] = 'admin';
            header("Location: dashboard.php");
            exit();
        }
        $error = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
    }
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Clothery System</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <div class="login-page">
        <div class="login-card">
            <div class="login-header">
                <span class="login-logo">👕</span>
                <h2>Clothery System</h2>
                <p style="color: var(--text-light);">ระบบจัดการร้านเสื้อผ้า</p>
            </div>
            <?php if (isset($error)): ?>
                <div style="color: red; text-align: center; margin-bottom: 1rem;"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="form-group">
                    <label class="form-label">ชื่อผู้ใช้งาน</label>
                    <input type="text" name="username" class="form-control" placeholder="Username" required>
                </div>
                <div class="form-group">
                    <label class="form-label">รหัสผ่าน</label>
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem;">เข้าสู่ระบบ</button>
            </form>
            <div style="margin-top: 1.5rem; text-align: center; font-size: 0.875rem; color: var(--text-light);">
                <p>AIE313 Database System Project</p>
            </div>
        </div>
    </div>
</body>

</html>