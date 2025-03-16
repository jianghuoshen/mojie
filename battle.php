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

// 获取当前用户信息
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// 获取角色属性信息
$stmt = $pdo->prepare("SELECT * FROM player_attributes WHERE user_id = ?");
$stmt->execute([$user_id]);
$player_attributes = $stmt->fetch(PDO::FETCH_ASSOC);

// 获取怪物信息
$stmt = $pdo->prepare("SELECT * FROM monsters WHERE monster_id = ?");
$stmt->execute([$monster_id]);
$monster = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$monster) {
    die("怪物不存在。");
}

// 战斗逻辑
$player_hp = $player_attributes['vitality'] * 10 + 50; // 玩家生命值
$monster_hp = $monster['hp']; // 怪物生命值

$battle_log = []; // 战斗日志

while ($player_hp > 0 && $monster_hp > 0) {
    // 玩家攻击怪物
    $player_damage = max(1, $player_attributes['strength'] - $monster['defense']);
    $monster_hp -= $player_damage;
    $battle_log[] = "你对 {$monster['name']} 造成了 $player_damage 点伤害。";

    if ($monster_hp <= 0) {
        $battle_log[] = "你击败了 {$monster['name']}！";
        break;
    }

    // 怪物攻击玩家
    $monster_damage = max(1, $monster['attack'] - $player_attributes['defense']);
    $player_hp -= $monster_damage;
    $battle_log[] = "{$monster['name']} 对你造成了 $monster_damage 点伤害。";

    if ($player_hp <= 0) {
        $battle_log[] = "你被 {$monster['name']} 击败了。";
        break;
    }
}

// 更新玩家生命值
$stmt = $pdo->prepare("UPDATE player_attributes SET vitality = ? WHERE user_id = ?");
$stmt->execute([max(0, $player_hp), $user_id]);

// 如果玩家胜利，处理掉落物品
if ($monster_hp <= 0) {
    // 获取怪物掉落物品
    $stmt = $pdo->prepare("SELECT * FROM monster_drops WHERE monster_id = ?");
    $stmt->execute([$monster_id]);
    $drops = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 将掉落物品添加到玩家背包
    foreach ($drops as $drop) {
        $stmt = $pdo->prepare("INSERT INTO backpack_items (user_id, item_id, quantity) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE quantity = quantity + ?");
        $stmt->execute([$user_id, $drop['item_id'], $drop['quantity'], $drop['quantity']]);
    }

    // 记录战斗结果
    $stmt = $pdo->prepare("INSERT INTO battles (user_id, monster_name, damage, result) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $monster['name'], $player_damage, '胜利']);
} else {
    // 记录战斗结果
    $stmt = $pdo->prepare("INSERT INTO battles (user_id, monster_name, damage, result) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $monster['name'], $monster_damage, '失败']);
}
?>

<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <link rel="icon" href="data:;base64,=">
    <title>战斗结果</title>
    <style>
        /* 全局样式 */
        body {
            font-family: Arial, sans-serif;
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

        .battle-log {
            list-style-type: none;
            padding: 0;
        }

        .battle-log li {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <!-- 战斗结果 -->
    <div class="section">
        <h1>战斗结果</h1>
        <ul class="battle-log">
            <?php foreach ($battle_log as $log): ?>
            <li><?php echo $log; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- 掉落物品 -->
    <?php if ($monster_hp <= 0 && !empty($drops)): ?>
    <div class="section">
        <h2>掉落物品</h2>
        <ul>
            <?php foreach ($drops as $drop): ?>
            <li><?php echo $drop['name']; ?> × <?php echo $drop['quantity']; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <!-- 操作菜单 -->
    <div class="section">
        <a href="monster_list.php">返回怪物列表</a>
        <a href="game.php">返回场景</a>
    </div>
</div>
</body>
</html>