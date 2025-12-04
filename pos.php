<?php 
    session_start();

    require_once "db.php";
        $cart = $_SESSION['cart'] ?? [];
    if(isset($_POST['clear']))
    {
        unset($_SESSION['cart']);
    }
    $total = 0;
    $error = '';

    $errors = [];

    if (isset($_GET['errors'])) {
        $errors = json_decode($_GET['errors'], true);
    }
        
?>

<h3>Product Scan Form</h3>

<?php
    if(!empty($errors)){
        foreach ($errors as $e) {
            echo "<div style='color:red;'>$e</div>";
        }
    }
?>

<form action="add_to_cart.php" method="get">
    <label for="">Product ID:</label>
    <input type="text" name="product_id" placeholder="e.g. AAA-12345"><br>
    <button type="submit">Add to cart</button>
</form>

<?php
    $stmt = $pdo->prepare('SELECT * FROM items WHERE product_id = ?');
    $stmt->execute([$_GET['prodID']]);
    $productScanned = $stmt->fetch();
?>

<table border="1" cellpadding="8">
    <thead>
        <th>Product Name</th>
        <th>Price</th>
        <th>Available Quantity</th>
    </thead>
    <tr>
        <td><?= $productScanned['name'] ?></td>
        <td><?= $productScanned['price'] ?></td>
        <td><?= $productScanned['quantity'] ?></td>
    </tr>
</table>

<table border="1" cellpadding="8">
    <th>Product</th>
    <th>Price</th>
    <th>Quantity</th>
    <th>Subtotal</th>
    <th>Remove</th>
    <tr>

    <?php
    $cart = $_SESSION['cart']; 
    foreach($cart as $itemID => $item):?>
        <td><?= $item['name']; ?></td>
        <td><?php echo '&#8369;';?><?= $item['price']; ?></td>
        <td><?= $item['quantity']; ?></td>
        <td><?= $item['price'] * $item['quantity'] ?> </td>>
        <td>
            <form action="remove_from_cart.php" method="get">
                <input type="hidden" name="product_id" placeholder="e.g. AAA-12345" value="<?= $itemID?>"><br>
                <button type="submit">Remove</button>
            </form>
        </td>
    </tr>
    <?php endforeach ?>
    <tr>
        <td>Grand Total</td>
        <td colspan="4" style="text-align: center">
            <?php foreach($cart as $itemId => $item)
                $total += $item['quantity'] * $item['price'];  
                echo '&#8369;'.$total.'<br>';   
            ?>
        </td>
    </tr>
</table>

<form action="pos.php" method="post">
    <button type="submit" name="clear">Clear</button>
</form>

<form action="checkout.php" method="post">
    <input type="hidden" name="cart" value="<?=$cart?>">

    <label for="">Payment: </label>
    <input type="number" name="payment">
    <button type="submit">Checkout</button>
</form>


<!-- CREATE TABLE sales (
 id INT AUTO_INCREMENT PRIMARY KEY,
 total_amount DECIMAL(10,2),
 payment DECIMAL(10,2),
 change_amount DECIMAL(10,2),
 sale_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
); -->