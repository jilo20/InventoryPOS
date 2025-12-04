<?php
require_once "db.php";

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $product_id = strtoupper(trim($_POST['product_id']));
    $name       = trim($_POST['name']);
    $qty        = trim($_POST['quantity']);
    $price      = trim($_POST['price']);

    // Validate Product ID format
    if (!preg_match("/^[A-Z]{3}-[0-9]{5}$/", $product_id)) {
        $errors[] = "Product ID must follow the format AAA-12345.";
    }

    // Check if Product ID already exists
    $stmt = $pdo->prepare("SELECT * FROM items WHERE product_id = ?");
    $stmt->execute([$product_id]);
    if ($stmt->rowCount() > 0) {
        $errors[] = "Product ID already exists. Use another.";
    }

    // Validate item name
    if (empty($name)) {
        $errors[] = "Item name is required.";
    }

    // Validate quantity
    if (!ctype_digit($qty) || (int)$qty < 0) {
        $errors[] = "Quantity must be a non-negative integer.";
    }

    // Validate price
    if (!is_numeric($price) || (float)$price < 0) {
        $errors[] = "Price must be a non-negative number.";
    }

    // If valid → Insert into DB
    if (empty($errors)) {
        $stmt = $pdo->prepare("
            INSERT INTO items (product_id, name, quantity, price)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$product_id, $name, $qty, $price]);

        header("Location: index.php");
        exit;
    }
}
?>

<h2>Add New Item</h2>

<?php if (!empty($errors)): ?>
    <ul style="color:red;">
        <?php foreach ($errors as $err): ?>
            <li><?= $err ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">

    <label>Product ID (AAA-12345):</label><br>
    <input type="text" name="product_id" required><br><br>

    <label>Item Name:</label><br>
    <input type="text" name="name" required><br><br>

    <label>Quantity:</label><br>
    <input type="number" name="quantity" required><br><br>

    <label>Price:</label><br>
    <input type="number" step="0.01" name="price" required><br><br>

    <button type="submit">Save</button>

</form>
