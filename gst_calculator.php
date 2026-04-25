<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
}
?>

<link rel="stylesheet" href="style.css">

<div class="main">
<h2>GST Calculator</h2>

<form method="POST">
    <input type="number" name="amount" placeholder="Enter Amount" step="0.01" required>

    <select name="gst">
        <option value="5">5%</option>
        <option value="12">12%</option>
        <option value="18">18%</option>
        <option value="28">28%</option>
    </select>

    <button>Calculate</button>
</form>

<?php
if ($_POST) {
    $amount = $_POST['amount'];
    $gst = $_POST['gst'];

    $gst_amount = ($amount * $gst) / 100;
    $total = $amount + $gst_amount;

    echo "<p>GST: ₹$gst_amount</p>";
    echo "<p>Total: ₹$total</p>";
}
?>

</div>