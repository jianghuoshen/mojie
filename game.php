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

// 获取当前地图信息
$current_map_id = $user['current_map_id'] ?? 1; // 如果字段不存在，默认地图 ID 为 1
$stmt = $pdo->prepare("SELECT * FROM maps WHERE map_id = ?");
$stmt->execute([$current_map_id]);
$current_map = $stmt->fetch(PDO::FETCH_ASSOC);

// 处理切换地图请求
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['switch_map'])) {
    $direction = $_GET['direction']; // 获取方向参数
    $adjacent_map_ids = json_decode($current_map['adjacent_map_ids'] ?? '[]', true);

    // 检查方向是否有效
    if (isset($adjacent_map_ids[$direction])) {
        $new_map_id = $adjacent_map_ids[$direction];

        // 更新用户当前地图
        $stmt = $pdo->prepare("UPDATE users SET current_map_id = ? WHERE user_id = ?");
        $stmt->execute([$new_map_id, $user_id]);

        // 重定向到当前页面，避免重复提交
        header("Location: game.php");
        exit();
    } else {
        echo "无法切换到该方向的地图。";
    }
}

// 获取当前地图的 NPC
$npc_ids = json_decode($current_map['npc_ids'] ?? '[]', true);
$npcs = [];
if (!empty($npc_ids)) {
    $placeholders = implode(',', array_fill(0, count($npc_ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM npcs WHERE npc_id IN ($placeholders)");
    $stmt->execute($npc_ids);
    $npcs = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 获取当前地图的怪物
$monster_ids = json_decode($current_map['monster_ids'] ?? '[]', true);
$monsters = [];
if (!empty($monster_ids)) {
    $placeholders = implode(',', array_fill(0, count($monster_ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM monsters WHERE monster_id IN ($placeholders)");
    $stmt->execute($monster_ids);
    $monsters = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 获取当前地图的其他玩家
$stmt = $pdo->prepare("SELECT nickname FROM users WHERE current_map_id = ? AND user_id != ?");
$stmt->execute([$current_map_id, $user_id]);
$other_players = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 获取当前地图的掉落道具
$drop_items = json_decode($current_map['drop_items'] ?? '[]', true);
?>

<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <link rel="icon" href="data:;base64,=">
    <title>魔界</title>
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

        }
    </style>
</head>
<body>
<div class="container">
    <!-- 地图信息 -->
    <div class="section">
        <h1>你来到「<?php echo $current_map['name']; ?>」</h1>
        <p><?php echo $current_map['description']; ?></p>
    </div>

    <!-- NPC 列表 -->
    <?php if (!empty($npcs)): ?>
    <div class="section">
        <h2>看到</h2>
        <?php foreach ($npcs as $npc): ?>
        <a href="/index.c?sid=<?php echo session_id(); ?>&cmd=5"><?php echo $npc['name']; ?></a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- 怪物列表 -->
    <?php if (!empty($monsters)): ?>
    <div class="section">
        <h2>看到</h2>
        <?php foreach ($monsters as $monster): ?>
        <a href="monster_list.php"=<?php echo session_id(); ?>&cmd=6"><?php echo $monster['name']; ?> × <?php echo $monster['quantity'] ?? 1; ?></a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- 其他玩家列表 -->
    <?php if (!empty($other_players)): ?>
    <div class="section">
        <h2>看到</h2>
        <?php foreach ($other_players as $player): ?>
        <a href="/index.c?sid=<?php echo session_id(); ?>&cmd=7"><?php echo $player['nickname']; ?></a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- 掉落道具列表 -->
    <?php if (!empty($drop_items)): ?>
    <div class="section">
        <h2>发现</h2>
        <?php foreach ($drop_items as $item): ?>
        <a href="/index.c?sid=<?php echo session_id(); ?>&cmd=a"><?php echo $item['name']; ?></a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- 操作菜单 -->
    <div class="section">
        <h2>操作</h2>
        <a href="/index.c?sid=<?php echo session_id(); ?>&cmd=b">察看</a>
        <a href="/index.c?sid=<?php echo session_id(); ?>&cmd=c">聊天</a>
        <a href="/index.c?sid=<?php echo session_id(); ?>&cmd=d">队伍</a>
        <a href="/index.c?sid=<?php echo session_id(); ?>&cmd=e">地图</a>
        <a href="/index.c?sid=<?php echo session_id(); ?>&cmd=f">商城</a>
        <a href="/index.c?sid=<?php echo session_id(); ?>&cmd=10">挂机</a><br>

        <!-- 切换地图链接 -->
        <?php
        $adjacent_map_ids = json_decode($current_map['adjacent_map_ids'] ?? '[]', true);
        if (!empty($adjacent_map_ids)) {
            foreach ($adjacent_map_ids as $direction => $map_id) {
                // 获取相邻地图的名称
                $stmt = $pdo->prepare("SELECT name FROM maps WHERE map_id = ?");
                $stmt->execute([$map_id]);
                $adjacent_map = $stmt->fetch(PDO::FETCH_ASSOC);

                // 如果相邻地图存在，则显示链接
                if ($adjacent_map) {
                    $adjacent_map_name = $adjacent_map['name'];
                    echo "<a href='game.php?switch_map&direction=$direction'>$direction $adjacent_map_name</a><br>";
                }
            }
        }
        ?>

        <br><a href="attributes.php">属性</a>
        <a href="backpack.php">物品</a>
        <a href="/index.c?sid=<?php echo session_id(); ?>&cmd=15">技能</a>
        <a href="/index.c?sid=<?php echo session_id(); ?>&cmd=16">宠物</a><br>

        <a href="/index.c?sid=<?php echo session_id(); ?>&cmd=17">任务</a>
        <a href="/index.c?sid=<?php echo session_id(); ?>&cmd=18">交流</a>
        <a href="/index.c?sid=<?php echo session_id(); ?>&cmd=19">帮助</a>
        <a href="/index.c?sid=<?php echo session_id(); ?>&cmd=1a">系统</a><br>

        <a href="/index.c?sid=<?php echo session_id(); ?>&cmd=1b">返回首页</a>
    </div>
</div>
</body>
</html>