<?php
require_once "db.php";
session_start();

$product_id = $_GET['product_id'];
$product = $_SESSION['cart'][$product_id]; 

unset($_SESSION['cart'][$product_id]);

$stmt = $pdo->prepare('SELECT * FROM items where product_id = ?');
$stmt->execute([$product_id]);
$product = $stmt->fetch();

header('Location: pos.php');
exit;

