<?php 
session_start();
require_once "db.php";

$cart = $_SESSION['cart'];
$errors = [];

if(empty($cart) && empty($errors)){
    $errors[] = 'Fill the cart first';
}

foreach($cart as $cartItem => $item){
    $total += $item['quantity'] * $item['price'];
}

if($_POST['payment'] < $total){
    $errors[] = 'Insufficient Amount';
}

if (!empty($errors)) {
    header("Location: pos.php?errors=" . urlencode(json_encode($errors)));
    exit;
};

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=\, initial-scale=1.0">
    <title>POS | Checkout</title>
</head>
<body>
    <h3>Receipt</h3>
    <table border="1" cellpadding="8">
        <thead>
            <th>Product</th>
            <th>Qty</th>
            <th>Total</th>
        </thead>
        <?php foreach($cart as $cartItem => $item):?>
        <tr>
            <td><?= $item['name']?></td>
            <td><?= $item['quantity']?></td>
            <td><?php echo '&#8369;';?><?= $item['quantity'] * $item['price']?></td>
           
        </tr>

        <?php
            $stmt = $pdo->prepare("SELECT quantity FROM items WHERE product_id = ?");
            $stmt->execute([$cartItem]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            $newQty = $product['quantity'] - $item['quantity'];
            $update = $pdo->prepare("UPDATE items SET quantity = ? WHERE product_id = ?");
            $update->execute([$newQty, $cartItem]);
        ?>

        <?php endforeach; ?>
        <!-- Grand Total -->
        <tr>
            <td>Grand Total: </td><td colspan="2"><?php echo '&#8369;';?><?= $total ?></td>
        </tr>
        <!-- Grand Total w vat-->
        <tr>
            <td>Grand Total w/vat: </td><td colspan="2"><?php echo '&#8369;';?><?= $total*.12 ?></td>
        </tr>
        <!-- Payment -->
        <tr>
            <td>Payment: </td><td colspan="2"><?php echo '&#8369;';?><?= $_POST['payment']?></td>
        </tr>
        <!-- Change -->
        <tr>
            <td>Change: </td><td colspan="2"><?php echo '&#8369;';?><?= $_POST['payment'] - $total ?></td>
        </tr>
    </table>
</body>
</html>
