<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>Billing App</h2>

    <a href="dashboard.php">🏠 Home</a>
    <a href="profile.php">👤 Profile</a>
    <a href="add_product.php">➕ Add Stock</a>
    <a href="view_products.php">📦 View Stock</a>
    <a href="generate_invoice.php">🧾 Generate Invoice</a>
    <a href="#">📊 Sales Report</a>
    <a href="#">📥 Purchase Report</a>
    <a href="#">📅 Monthly Report</a>
    <a href="#">📄 All Invoices</a>
    <a href="gst_calculator.php">🧮 GST Calculator</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<!-- MAIN -->
<div class="main">
    <h1>Welcome Owner 👋</h1>

    <h3>Find Invoice</h3>

    <form>
        <input type="text" placeholder="Enter Invoice No">
        <button>Search</button>
    </form>
</div>

</body>
</html>