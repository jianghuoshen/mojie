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

// 获取角色属性信息
// 获取角色属性信息
$stmt = $pdo->prepare("SELECT * FROM player_attributes WHERE user_id = ?");
$stmt->execute([$user_id]);
$attributes = $stmt->fetch(PDO::FETCH_ASSOC);

// 获取当前等级和下一级所需的经验值
$stmt = $pdo->prepare("SELECT required_exp FROM level_experience WHERE level = ?");
$stmt->execute([$attributes['level']]);
$current_level_exp = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT required_exp FROM level_experience WHERE level = ?");
$stmt->execute([$attributes['level'] + 1]);
$next_level_exp = $stmt->fetchColumn();

// 如果属性信息不存在，初始化默认值
if (!$attributes) {
    $attributes = [
        'level' => 1,
        'strength' => 0,
        'vitality' => 0,
        'intelligence' => 0,
        'agility' => 0,
        'free_points' => 0,
        'exp' => 0,
        'next_level_exp' => 100, // 假设升级需要 100 经验值
    ];
}

// 获取角色装备信息
$stmt = $pdo->prepare("SELECT * FROM backpack_items WHERE user_id = ?");
$stmt->execute([$user_id]);
$equipment = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 获取角色技能信息
$stmt = $pdo->prepare("SELECT * FROM skills WHERE user_id = ?");
$stmt->execute([$user_id]);
$skills = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <link rel="icon" href="data:;base64,=">
    <title>角色属性</title>
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

        .attribute-list {
            list-style-type: none;
            padding: 0;
        }

        .attribute-list li {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <!-- 角色基本信息 -->
    <div class="section">
        <h1>「<?php echo $user['nickname']; ?>」</h1>
        <p>刚来到这个世界的流浪者。</p>
        <p><?php echo $attributes['level']; ?>级<?php echo $user['gender'] === 'male' ? '男' : '女'; ?>【<?php echo $user['class']; ?>】</p>
        <p>生命: <?php echo $attributes['vitality'] * 10 + 50; ?>/<?php echo $attributes['vitality'] * 10 + 50; ?>+</p>
        <p>魔力: <?php echo $attributes['intelligence'] * 10 + 100; ?>/<?php echo $attributes['intelligence'] * 10 + 100; ?>+</p>
        <p>怒气: 100/100</p>
        <p>技能: <?php echo !empty($skills) ? $skills[0]['skill_name'] : '无'; ?></p>
        <p>状态: 正常，看起来精力充沛。</p>
        <p>一个纯洁善良的人。</p>
        <p>『双手』<?php echo !empty($equipment) ? $equipment[0]['name'] : '无装备'; ?></p>
    </div>

    <!-- 详情链接 -->
    <div class="section">
        <a href="attribute_details.php">详情</a>
    </div>

    <!-- 装备信息链接 -->
    <div class="section">
        <a href="equipment.php">装备信息</a>
    </div>

    <!-- 返回场景 -->
    <div class="section">
        <a href="game.php">返回场景</a>
    </div>
</div>
</body>
</html>