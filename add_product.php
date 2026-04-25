<?php
session_start();
include 'db.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
}

if ($_POST) {
    $name = $_POST['name'];
    $hsn = $_POST['hsn'];
    $price = $_POST['price'];
    $qty = $_POST['quantity'];
    $unit = $_POST['unit'];
    $purchase = $_POST['purchase_date'];
    $expiry = $_POST['expiry_date'];

    $conn->query("INSERT INTO products (name, hsn, price, quantity, purchase_date, expiry_date)
    VALUES ('$name','$hsn','$price','$qty','$purchase','$expiry')");

    $msg = "Product Added Successfully!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Stock</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="main">

<h2>Add Stock</h2>

<form method="POST" class="form-box" id="productForm">

    <input type="text" name="name" placeholder="Product Name" required>

    <input type="text" name="hsn" placeholder="HSN Code">

    <input type="number" name="price" placeholder="Price" step="0.01" required>

    <div class="qty-row">
        <input type="number" name="quantity" placeholder="Quantity" step="0.01" required>

        <select name="unit" required>
            <option value="" disabled selected hidden>Unit</option>
            <option value="Piece">Piece</option>
            <option value="Packet">Packet</option>
            <option value="Kilogram">Kilogram (kg)</option>
            <option value="Gram">Gram (g)</option>
            <option value="Liter">Liter (L)</option>
            <option value="Ton">Ton</option>
        </select>
    </div>

    <label>Purchase Date</label>
    <input type="date" name="purchase_date" required>

    <label>Expiry Date</label>
    <input type="date" name="expiry_date">

    <div class="btn-row">
        <button type="submit">Add Product</button>
        <button type="button" onclick="clearForm()">Clear All</button>
        <button type="button" onclick="window.location.href='dashboard.php'">← Back</button>
    </div>

</form>

<?php if (isset($msg)) echo "<p class='success'>$msg</p>"; ?>

</div>

<script>
function clearForm() {
    document.getElementById("productForm").reset();
}
</script>

</body>
</html>