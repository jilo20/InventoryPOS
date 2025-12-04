<?php
require_once "db.php";

$id = $_GET['id'];
echo $_GET['id'];
$errors = [];

// Fetch existing item
$stmt = $pdo->prepare("SELECT * FROM items WHERE product_id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    die("Item not found.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name  = trim($_POST['name']);
    $qty   = trim($_POST['quantity']);
    $price = trim($_POST['price']);

    // Validate fields
    if (empty($name)) {
        $errors[] = "Item name is required.";
    }

    if (!ctype_digit($qty) || (int)$qty < 0) {
        $errors[] = "Quantity must be a non-negative integer.";
    }

    if (!is_numeric($price) || (float)$price < 0) {
        $errors[] = "Price must be a non-negative number.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("
            UPDATE items 
            SET name = ?, quantity = ?, price = ?
            WHERE product_id = ?
        ");

        $stmt->execute([$name, $qty, $price, $id]);

        header("Location: index.php");
        exit;
    }
}
?>

<h2>Edit Item</h2>

<?php if (!empty($errors)): ?>
    <ul style="color:red;">
        <?php foreach ($errors as $err): ?>
            <li><?= $err ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form action="<?= $_SERVER['PHP_SELF'].'?id='.$item['product_id'] ?>" method="post">

    <label>Product ID:</label><br>
    <input type="text" value="<?= $item['product_id'] ?>" disabled><br><br>

    <label>Item Name:</label><br>
    <input type="text" 
           name="name" 
           value="<?= htmlspecialchars($item['name']) ?>" 
           required><br><br>

    <label>Quantity:</label><br>
    <input type="number" 
           name="quantity" 
           value="<?= $item['quantity'] ?>" 
           required><br><br>

    <label>Price:</label><br>
    <input type="number" 
           step="0.01" 
           name="price" 
           value="<?= $item['price'] ?>" 
           required><br><br>
    <button type="submit">Update</button>
            
</form>
