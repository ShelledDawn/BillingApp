<?php
session_start();
include 'db.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
}

$result = $conn->query("SELECT * FROM products");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Stock List</title>
    <link rel="stylesheet" href="style.css">

    <script>
        function printTable() {
            window.print();
        }
    </script>
</head>

<body>

<div class="main">

<h2>Stock List</h2>

<!-- BUTTONS -->
<div class="btn-row">
    <button onclick="printTable()">🖨️ Print</button>
    <button onclick="window.location.href='dashboard.php'">⬅ Back</button>
</div>

<table class="stock-table">

<tr>
    <th>ID</th>
    <th>Name</th>
    <th>HSN</th>
    <th>Price</th>
    <th>Quantity</th>
    <th>Purchase Date</th>
    <th>Expiry Date</th>
</tr>

<?php while($row = $result->fetch_assoc()) { ?>
<tr>
    <td><?php echo $row['id']; ?></td>
    <td><?php echo $row['name']; ?></td>
    <td><?php echo $row['hsn']; ?></td>
    <td><?php echo number_format($row['price'],2); ?></td>
    <td><?php echo $row['quantity']; ?></td>
    <td><?php echo $row['purchase_date']; ?></td>
    <td><?php echo $row['expiry_date']; ?></td>
</tr>
<?php } ?>

</table>

</div>

</body>
</html>