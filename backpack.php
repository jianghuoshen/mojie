<?php
// items.php

session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 获取玩家金币和背包容量
try {
    $stmt = $pdo->prepare("SELECT gold, backpack_capacity FROM users WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("查询失败: " . $e->getMessage());
}

// 获取玩家背包物品
try {
    $stmt = $pdo->prepare("
        SELECT 
            b.uid,
            b.item_id,
            b.equipment_id,
            b.quantity,
            i.name AS item_name,
            i.type AS item_type,
            e.name AS equipment_name,
            e.part AS equipment_part
        FROM backpack_items b
        LEFT JOIN items i ON b.item_id = i.item_id
        LEFT JOIN equipment e ON b.equipment_id = e.equipment_id
        WHERE b.user_id = :user_id
    ");
    $stmt->execute(['user_id' => $user_id]);
    $backpack_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("查询失败: " . $e->getMessage());
}

// 分页逻辑
$items_per_page = 10; // 每页显示 10 个物品
$total_items = count($backpack_items);
$total_pages = ceil($total_items / $items_per_page);

$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$current_page = max(1, min($current_page, $total_pages)); // 确保当前页在有效范围内

$offset = ($current_page - 1) * $items_per_page;
$paged_items = array_slice($backpack_items, $offset, $items_per_page);
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>游戏物品</title>
    <style>
        /* 统一字体大小 */
        body {
            font-family: Arial, sans-serif;
            font-size: 16px; /* 所有字体大小统一为 16px */
            margin: 0;
            padding: 20px;
         
        }

        h1 {
            font-size: 16px; /* 标题字体大小 */
            margin-bottom: 10px;
        }

        .info {
            margin-bottom: 20px;
        }

        .filters {
            margin-bottom: 20px;
        }

        .filters a {
            margin-right: 10px;
            
            text-decoration: none;
        }

        .filters a:hover {
            text-decoration: underline;
        }

        .item-list {
            margin-bottom: 20px;
        }

       

        .item:last-child {
            border-bottom: none;
        }

        
       

        .pagination a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>「随身物品」</h1>

    <div class="info">
        魔金: 0 （商城代币券）<br>
        金币: <?= htmlspecialchars($user['gold']) ?> | 背包容量: <?= htmlspecialchars($user['backpack_capacity']) ?>格扩容
    </div>

    <div class="filters">
        <a href="?type=all">全部</a> |
        <a href="?type=potion">药水</a> |
        <a href="?type=equipment">装备</a> |
        <a href="?type=scroll">书卷</a></br>
        <a href="?type=quest">任务</a> |
        <a href="?type=gem">宝石</a> |
        <a href="?type=material">材料</a> |
        <a href="?type=item">道具</a>
    </div>

    <div class="item-list">
        <?php if (count($paged_items) > 0): ?>
            <?php foreach ($paged_items as $item): ?>
                <div class="item">
                    <?php if ($item['item_name']): ?>
                        <?= htmlspecialchars($item['item_name']) ?> (<?= htmlspecialchars($item['item_type']) ?>)
                        <?php if ($item['item_type'] === '装备'): ?>
                            <a href="equip.php?item_id=<?= $item['item_id'] ?>">装备</a>
                        <?php else: ?>
                            <a href="use.php?item_id=<?= $item['item_id'] ?>">使用</a>
                        <?php endif; ?>
                    <?php elseif ($item['equipment_name']): ?>
                        <?= htmlspecialchars($item['equipment_name']) ?> (装备)
                        <a href="equip.php?equipment_id=<?= $item['equipment_id'] ?>">装备</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>背包为空。</p>
        <?php endif; ?>
    </div>

    <div class="pagination">
        <?php if ($total_pages > 1): ?>
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <?php if ($i === $current_page): ?>
                    <strong><?= $i ?></strong>
                <?php else: ?>
                    <a href="?page=<?= $i ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>
        <?php endif; ?>
    </div>

    <p><a href="game.php">返回游戏</a></p>
</body>
</html>