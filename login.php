<?php
session_start();
require 'db.php'; // 引入数据库连接文件

// 处理登录
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // 验证用户
    $stmt = $pdo->prepare("SELECT user_id, username, password_hash FROM user_auth WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['user_id'];
        header("Location: game.php"); // 登录成功后跳转到游戏页面
        exit();
    } else {
        echo "登录失败：账号或密码错误。";
    }
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>登录</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>登录</h1>
    <form method="post" action="">
        <input type="hidden" name="login">
        <label>账号: <input type="text" name="username" required></label><br>
        <label>密码: <input type="password" name="password" required></label><br>
        <button type="submit">登录</button>
    </form>

    <p>还没有账号？<a href="register.php">点击注册</a></p>
</body>
</html>