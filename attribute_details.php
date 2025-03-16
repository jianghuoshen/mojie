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
?>

<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <link rel="icon" href="data:;base64,=">
    <title>角色详情</title>
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
  <div class="section">
    <h1>角色详情</h1>
    <ul class="attribute-list">
        <li>等级: <?php echo $attributes['level']; ?></li>
        <li>经验值: <?php echo $attributes['exp']; ?>/<?php echo $next_level_exp; ?></li>
        <li>力量: <?php echo $attributes['strength']; ?></li>
        <li>体力: <?php echo $attributes['vitality']; ?></li>
        <li>智力: <?php echo $attributes['intelligence']; ?></li>
        <li>敏捷: <?php echo $attributes['agility']; ?></li>
        <li>自由属性点: <?php echo $attributes['free_points']; ?></li>
    </ul>
</div>
    <!-- 分配属性点表单 -->
    <div class="section">
        <h2>分配属性点</h2>
        <form method="post" action="allocate_points.php">
            <label>力量: <input type="number" name="strength" min="0" max="<?php echo $attributes['free_points']; ?>"></label><br>
            <label>体力: <input type="number" name="vitality" min="0" max="<?php echo $attributes['free_points']; ?>"></label><br>
            <label>智力: <input type="number" name="intelligence" min="0" max="<?php echo $attributes['free_points']; ?>"></label><br>
            <label>敏捷: <input type="number" name="agility" min="0" max="<?php echo $attributes['free_points']; ?>"></label><br>
            <button type="submit">分配</button>
        </form>
    </div>

    <!-- 返回属性页面 -->
    <div class="section">
        <a href="attributes.php">返回属性页面</a>
    </div>
</div>
</body>
</html>