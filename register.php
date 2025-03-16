<?php
session_start();
require 'db.php'; // 引入数据库连接文件

// 处理注册
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // 密码哈希
    $super_password = password_hash($_POST['super_password'], PASSWORD_BCRYPT); // 超级密码哈希
    $nickname = $_POST['nickname'];
    $gender = $_POST['gender'];
    $class = $_POST['class'];

    // 检查昵称是否已存在
    $stmt = $pdo->prepare("SELECT user_id FROM users WHERE nickname = ?");
    $stmt->execute([$nickname]);
    if ($stmt->fetch()) {
        echo "昵称已存在，请选择其他昵称。<br>";
        echo "<a href='register.php'><button>返回注册页面</button></a>"; // 返回按钮
        exit(); // 终止脚本执行
    }

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
        $stmt = $pdo->prepare("INSERT INTO users (nickname, gender, class) VALUES (?, ?, ?)");
        $stmt->execute([$nickname, $gender, $class]);
        $user_id = $pdo->lastInsertId();

        // 插入到 user_auth 表
        $stmt = $pdo->prepare("INSERT INTO user_auth (user_id, username, password_hash, super_password_hash) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $username, $password, $super_password]);

        // 插入到 player_attributes 表
        $stmt = $pdo->prepare("INSERT INTO player_attributes (user_id) VALUES (?)");
        $stmt->execute([$user_id]);

        // 插入到 skill_points 表
        $stmt = $pdo->prepare("INSERT INTO skill_points (user_id) VALUES (?)");
        $stmt->execute([$user_id]);

        // 分配默认装备
        $stmt = $pdo->prepare("INSERT INTO backpack_items (user_id, equipment_id, quantity) VALUES (?, ?, 1)");
        $stmt->execute([$user_id, $default_equipment['equipment_id']]);

        // 分配默认技能
        $stmt = $pdo->prepare("INSERT INTO skills (user_id, skill_id, skill_name) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $default_skill['skill_id'], '默认技能名称']); // 替换 '默认技能名称' 为实际值

        $pdo->commit();
        echo "注册成功！<a href='login.php'>点击登录</a>";
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
    <title>注册</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>注册</h1>
    <form method="post" action="">
        <input type="hidden" name="register">
        <label>账号: <input type="text" name="username" required></label><br>
        <label>密码: <input type="password" name="password" required></label><br>
        <label>超级密码: <input type="password" name="super_password" required></label><br>
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

    <p>已有账号？<a href="login.php">点击登录</a></p>
</body>
</html>