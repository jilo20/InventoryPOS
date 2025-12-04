<?php require_once "db.php"; ?>

<h2>Inventory Items</h2>
<a href="add.php">Add New Item</a>
<br><br>

<table border="1" cellpadding="8">
    <tr>
        <th>Product ID</th>
        <th>Name</th>
        <th>Quantity</th>
        <th>Price</th>
        <th>Action</th>
    </tr>

    <?php
    $stmt = $pdo->prepare("SELECT * FROM items ORDER BY product_id");
    $stmt->execute();
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($items as $row):
    ?>
        <tr>
            <td><?= htmlspecialchars($row['product_id']) ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= $row['quantity'] ?></td>
            <td><?= $row['price'] ?></td>
            <td>
                <a href="edit.php?id=<?= $row['product_id'] ?>">Edit</a> |
                <a href="delete.php?id=<?= $row['product_id'] ?>" 
                   onclick="return confirm('Delete this item?');">
                   Delete
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
