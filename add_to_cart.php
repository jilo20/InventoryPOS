<?php
session_start();
require_once "db.php";
$errors = [];  

$product_id = $_GET['product_id'];

$stmt = $pdo->prepare("SELECT * FROM items WHERE product_id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);
$cart = $_SESSION['cart'];


if(!isset($_SESSION['cart'])){
    $_SESSION['cart'] = [];
}

$currentQty = $_SESSION['cart'][$product_id]['quantity'] ?? NULL;

if ($product['quantity'] <= $currentQty && !empty($product)) {
    $errors[] = 'Insufficient Stock';
}

if(empty($product)){
    $errors[] = 'Product does not exists';
}


echo $product['quantity'].' || '. $cart[$product_id]['quantity'];
if(empty($errors)){
    $_SESSION['cart'][$product_id] = [
        'name' => $product['name'],
        'price' => $product['price'],
        'quantity' => $currentQty + 1
    ];
    // $product['quantity'] -=  $cart[$product_id]['quantity'];
    header("Location: pos.php?prodID=".$product_id);
    exit;
}else{
     header("Location: pos.php?errors=" . urlencode(json_encode($errors)));
    exit;

}

// if($product['quantity'] > $cart[$product_id]['quantity']){
    
// }else{
    
    

// }
