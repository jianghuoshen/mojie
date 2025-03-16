<?php
session_start();
require 'db.php';

// 处理登录
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // 验证用户
    $stmt = $pdo->prepare("SELECT user_id, username, password_hash FROM users WHERE username = ?");
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

// 处理注册
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // 密码哈希
    $nickname = $_POST['nickname'];
    $gender = $_POST['gender'];
    $class = $_POST['class'];

    // 获取默认装备和技能
    $stmt = $pdo->prepare("SELECT equipment_id FROM default_equipment WHERE class = ?");
    $stmt->execute([$class]);
    $default_equipment = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT skill_id FROM default_skills WHERE class = ?");
    $stmt->execute([$class]);
    $default_skill = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$default_equipment || !$default_skill) {
        die("默认装备或技能未设置。");
    }

    // 插入用户数据
    try {
        $pdo->beginTransaction();

        // 插入到 users 表
        $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, nickname, gender, class) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$username, $password, $nickname, $gender, $class]);
        $user_id = $pdo->lastInsertId();

        // 插入到 player_attributes 表
        $stmt = $pdo->prepare("INSERT INTO player_attributes (user_id) VALUES (?)");
        $stmt->execute([$user_id]);

        // 分配默认装备
        $stmt = $pdo->prepare("INSERT INTO backpack_items (user_id, item_id, quantity) VALUES (?, ?, 1)");
        $stmt->execute([$user_id, $default_equipment['equipment_id']]);

        // 分配默认技能
        $stmt = $pdo->prepare("INSERT INTO skills (user_id, skill_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $default_skill['skill_id']]);

        $pdo->commit();
        echo "注册成功！";
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "注册失败: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>登录与注册</title>
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

    <h1>注册</h1>
    <form method="post" action="">
        <input type="hidden" name="register">
        <label>账号: <input type="text" name="username" required></label><br>
        <label>密码: <input type="password" name="password" required></label><br>
        <label>昵称: <input type="text" name="nickname" required></label><br>
        <label>性别: 
            <select name="gender" required>
                <option value="male">男</option>
                <option value="female">女</option>
            </select>
        </label><br>
        <label>职业: 
            <select name="class" required>
                <option value="法师">法师</option>
                <option value="射手">射手</option>
                <option value="战士">战士</option>
            </select>
        </label><br>
        <button type="submit">注册</button>
    </form>
</body>
</html>