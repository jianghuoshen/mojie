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

// 处理分配属性点请求
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $strength = intval($_POST['strength']);
    $vitality = intval($_POST['vitality']);
    $intelligence = intval($_POST['intelligence']);
    $agility = intval($_POST['agility']);

    // 获取当前自由属性点
    $stmt = $pdo->prepare("SELECT free_points FROM player_attributes WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $free_points = $stmt->fetchColumn();

    // 检查分配的点数是否合法
    $total_allocated = $strength + $vitality + $intelligence + $agility;
    if ($total_allocated <= $free_points) {
        // 更新属性点
        $stmt = $pdo->prepare("UPDATE player_attributes SET strength = strength + ?, vitality = vitality + ?, intelligence = intelligence + ?, agility = agility + ?, free_points = free_points - ? WHERE user_id = ?");
        $stmt->execute([$strength, $vitality, $intelligence, $agility, $total_allocated, $user_id]);

        // 重定向到详情页面
        header("Location: attribute_details.php");
        exit();
    } else {
        echo "分配的点数超过可用自由属性点。";
    }
}