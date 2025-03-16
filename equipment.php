<?php
session_start();
require 'db.php';

// 检查是否登录
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 获取当前用户信息
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// 获取角色装备信息
$stmt = $pdo->prepare("SELECT * FROM backpack_items WHERE user_id = ?");
$stmt->execute([$user_id]);
$equipment = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <link rel="icon" href="data:;base64,=">
    <title>装备信息</title>
    <style>
        /* 全局样式 */
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            font-size: 18px; /* 统一字体大小 */
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        h1, h2, p, a {
            margin: 0;
            padding: 0;
            font-size: 18px; /* 统一字体大小 */
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            width: 100%;
        }

        a {
            color: #007bff;
            text-decoration: none;
            display: block; /* 链接跨行显示 */
            margin-bottom: 5px; /* 链接之间的间距 */
        }

        a:hover {
            text-decoration: underline;
        }

        .section {
            margin-bottom: 20px;
        }

        .equipment-list {
            list-style-type: none;
            padding: 0;
        }

        .equipment-list li {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <!-- 装备信息 -->
    <div class="section">
        <h1>装备信息</h1>
        <?php if (!empty($equipment)): ?>
        <ul class="equipment-list">
            <?php foreach ($equipment as $item): ?>
            <li>
                <strong><?php echo $item['name']; ?></strong> (数量: <?php echo $item['quantity']; ?>)
                <p><?php echo $item['description'] ?? '暂无描述'; ?></p>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php else: ?>
        <p>暂无装备。</p>
        <?php endif; ?>
    </div>

    <!-- 返回属性页面 -->
    <div class="section">
        <a href="attributes.php">返回属性页面</a>
    </div>
</div>
</body>
</html>