<?php
session_start();
require 'db.php';

// 检查是否登录
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 获取怪物 ID
if (!isset($_GET['monster_id'])) {
    die("怪物 ID 未提供。");
}
$monster_id = intval($_GET['monster_id']);

// 获取怪物信息
$stmt = $pdo->prepare("SELECT * FROM monsters WHERE monster_id = ?");
$stmt->execute([$monster_id]);
$monster = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$monster) {
    die("怪物不存在。");
}

// 获取怪物技能信息
$stmt = $pdo->prepare("SELECT * FROM monster_skills WHERE monster_id = ?");
$stmt->execute([$monster_id]);
$skills = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 获取怪物装备信息
$stmt = $pdo->prepare("SELECT * FROM monster_equipment WHERE monster_id = ?");
$stmt->execute([$monster_id]);
$equipment = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <link rel="icon" href="data:;base64,=">
    <title>怪物属性</title>
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
    <!-- 怪物属性信息 -->
    <div class="section">
        <h1>「<?php echo $monster['name']; ?>」</h1>
        <p><?php echo $monster['level']; ?>级 【<?php echo $monster['class']; ?>】</p>
        <p><?php echo $monster['description']; ?></p>
        <p>正常，看起来精力充沛。</p>
        <p>技能: <?php echo !empty($skills) ? $skills[0]['skill_name'] : '无'; ?></p>
        <p>『<?php echo !empty($equipment) ? $equipment[0]['name'] : '无装备'; ?>』</p>
        <p>『<?php echo !empty($equipment) ? $equipment[1]['name'] : '无装备'; ?>』</p>
    </div>

    <!-- 操作菜单 -->
    <div class="section">
        <a href="battle.php?monster_id=<?php echo $monster['monster_id']; ?>">攻击</a>
        <a href="monster_list.php">返回上级</a>
        <a href="game.php">返回场景</a>
    </div>
</div>
</body>
</html>