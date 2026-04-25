<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'db.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit();
}

// Seller data
$profile = $conn->query("SELECT * FROM profile LIMIT 1")->fetch_assoc();

// Products
$products = $conn->query("SELECT * FROM products");
?>

<!DOCTYPE html>
<html>
<head>
<title>Generate Invoice</title>
<link rel="stylesheet" href="style.css">

<script>
function fillProduct(sel) {
    let opt = sel.options[sel.selectedIndex];

    document.getElementById("hsn").value = opt.getAttribute("data-hsn");
    document.getElementById("rate").value = opt.getAttribute("data-price");
    document.getElementById("unit").value = opt.getAttribute("data-unit");

    calculate();
}

function calculate() {
    let qty = parseFloat(document.getElementById("qty").value) || 0;
    let rate = parseFloat(document.getElementById("rate").value) || 0;

    let amount = qty * rate;
    document.getElementById("amount").value = amount.toFixed(2);

    calcGST();
}

function calcGST() {
    let amt = parseFloat(document.getElementById("amount").value) || 0;
    let gst = parseFloat(document.getElementById("gst").value) || 0;

    let cgst = amt * gst / 100;
    let sgst = amt * gst / 100;

    document.getElementById("cgst").value = cgst.toFixed(2);
    document.getElementById("sgst").value = sgst.toFixed(2);

    document.getElementById("total").value = (amt + cgst + sgst).toFixed(2);
}
</script>

</head>

<body>

<div class="main">

<h2 style="text-align:center;">Tax Invoice</h2>

<!-- SELLER -->
<div class="invoice-box">

<?php if (!empty($profile['logo']) && file_exists("uploads/".$profile['logo'])) { ?>
    <img src="uploads/<?php echo $profile['logo']; ?>" width="80">
<?php } ?>

<h3><?php echo $profile['shop_name']; ?></h3>
<p><?php echo $profile['address']; ?></p>
<p>GST: <?php echo $profile['gst']; ?></p>
<p>Email: <?php echo $profile['email']; ?></p>
<p>Phone: <?php echo $profile['mobile']; ?></p>

</div>

<hr>

<!-- BUYER -->
<h3>Buyer Details</h3>

<input type="text" placeholder="Buyer Name">
<textarea placeholder="Address"></textarea>
<input type="text" placeholder="GST Number">
<input type="text" placeholder="Phone Number">

<hr>

<!-- PRODUCT -->
<h3>Product Details</h3>

<select onchange="fillProduct(this)">
    <option value="">Select Product</option>

    <?php while($p = $products->fetch_assoc()) { ?>
    <option 
        data-hsn="<?php echo $p['hsn']; ?>"
        data-price="<?php echo $p['price']; ?>"
        data-unit="<?php echo $p['unit'] ?? 'Unit'; ?>">
        <?php echo $p['name']; ?>
    </option>
    <?php } ?>

</select>

<input type="text" id="hsn" placeholder="HSN/SAC">
<input type="number" id="qty" placeholder="Quantity" onkeyup="calculate()">
<input type="text" id="rate" placeholder="Rate">
<input type="text" id="unit" placeholder="Unit">
<input type="text" id="amount" placeholder="Amount" readonly>

<hr>

<!-- GST -->
<h3>GST Calculation</h3>

<select id="gst" onchange="calcGST()">
    <option value="2.5">2.5%</option>
    <option value="6">6%</option>
    <option value="9">9%</option>
    <option value="14">14%</option>
</select>

<input type="text" id="cgst" placeholder="CGST Amount" readonly>
<input type="text" id="sgst" placeholder="SGST Amount" readonly>

<h3>Total Amount</h3>
<input type="text" id="total" readonly>

<br><br>

<button onclick="window.print()">🖨️ Print Invoice</button>
<button onclick="window.location.href='dashboard.php'">⬅ Back</button>

</div>

</body>
</html>