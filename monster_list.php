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

// 获取当前地图的怪物信息
$current_map_id = $user['current_map_id'] ?? 1; // 如果字段不存在，默认地图 ID 为 1
$stmt = $pdo->prepare("SELECT monster_ids FROM maps WHERE map_id = ?");
$stmt->execute([$current_map_id]);
$monster_ids = json_decode($stmt->fetchColumn() ?? '[]', true);

// 分页逻辑
$page = isset($_GET['page']) ? intval($_GET['page']) : 1; // 当前页码
$per_page = 10; // 每页显示的怪物数量
$total_monsters = count($monster_ids); // 怪物总数
$total_pages = ceil($total_monsters / $per_page); // 总页数

// 获取当前页的怪物 ID
$offset = ($page - 1) * $per_page;
$current_monster_ids = array_slice($monster_ids, $offset, $per_page);

// 获取怪物详细信息
$monsters = [];
if (!empty($current_monster_ids)) {
    $placeholders = implode(',', array_fill(0, count($current_monster_ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM monsters WHERE monster_id IN ($placeholders)");
    $stmt->execute($current_monster_ids);
    $monsters = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <link rel="icon" href="data:;base64,=">
    <title>怪物列表</title>
    <style>
        /* 全局样式 */
        body {
         
            display: flex;
            justify-content: center;
            font-size: 18px; /* 统一字体大小 */
            margin: 0;
            padding: 0;
         
        }

        h1, h2, p, a {
            margin: 0;
            padding: 0;
            font-size: 18px; /* 统一字体大小 */
        }

       

    </style>
</head>
<body>
<div class="container">
    <!-- 怪物列表 -->
    <div class="section">
        <h1>[角色列表] 群攻</h1>
        <ul class="monster-list">
            <?php foreach ($monsters as $monster): ?>
            <li>
                <?php if ($monster['rarity'] === '稀有'): ?>
                [精]  <a href="monster_details.php?monster_id=<?php echo $monster['monster_id']; ?>"<?php echo $monster['name']; ?></a><a href="/inde"> 攻击</a><br>
                <?php elseif ($monster['rarity'] === '史诗'): ?>
                [金]  <a href="monster_details.php?monster_id=<?php echo $monster['monster_id']; ?>"><?php echo $monster['name']; ?></a><a href="/inde"> 攻击</a><br>
                <?php else: ?>
                 <a href="monster_details.php?monster_id=<?php echo $monster['monster_id']; ?>"><?php echo $monster['name']; ?></a><a href="/inde"> 攻击</a><br>
                <?php endif; ?>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- 分页 -->
    <?php if ($total_pages > 1): ?>
    <div class="section pagination">
        <?php if ($page > 1): ?>
        <a href="monster_list.php?page=<?php echo $page - 1; ?>">上一页</a>
        <?php endif; ?>
        <?php if ($page < $total_pages): ?>
        <a href="monster_list.php?page=<?php echo $page + 1; ?>">下一页</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- 返回场景 -->
    <div class="section">
        <a href="game.php">返回场景</a>
    </div>
</div>
</body>
</html>