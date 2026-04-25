<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'db.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit();
}

/* SAFE FETCH (no error if empty) */
$profile = $conn->query("SELECT * FROM profile LIMIT 1");
$profile = $profile ? $profile->fetch_assoc() : [];

$products = $conn->query("SELECT * FROM products");
?>

<!DOCTYPE html>
<html>
<head>
<title>Generate Invoice</title>

<style>
body { font-family: Arial; }

.invoice {
    width: 900px;
    margin: auto;
    border: 1px solid #000;
    background: #fff;
}

table { width:100%; border-collapse: collapse; }

td, th {
    border:1px solid #000;
    padding:5px;
    font-size:13px;
    vertical-align: top;
}

.header {
    background:#5d88b5;
    color:white;
    text-align:center;
    font-weight:bold;
}

.section {
    background:#e5e88c;
    font-weight:bold;
}

.center { text-align:center; }
.right { text-align:right; }

input, select {
    width:100%;
    border:none;
    outline:none;
}

/* compact header */
.seller-box { font-size:14px; line-height:1.4; }
.invoice-box input { height:18px; }

.logo { width:100px; }
</style>

<script>
function fillProduct(sel){
    let opt = sel.options[sel.selectedIndex];

    document.getElementById("hsn").value = opt.dataset.hsn || '';
    document.getElementById("rate").value = opt.dataset.price || '';
    document.getElementById("unit").value = opt.dataset.unit || "Piece";

    calculate();
}

function calculate(){

    let qty = parseFloat(document.getElementById("qty").value) || 0;
    let rate = parseFloat(document.getElementById("rate").value) || 0;

    let cgstRate = parseFloat(document.getElementById("cgst_rate").value) || 0;
    let sgstRate = parseFloat(document.getElementById("sgst_rate").value) || 0;

    let amount = qty * rate;

    document.getElementById("amount_input").value = amount.toFixed(2);
    document.getElementById("amount_display").innerText = amount.toFixed(2);

    let cgst = amount * cgstRate / 100;
    let sgst = amount * sgstRate / 100;

    document.getElementById("cgst_amt").innerText = cgst.toFixed(2);
    document.getElementById("sgst_amt").innerText = sgst.toFixed(2);

    let total = amount + cgst + sgst;
    document.getElementById("total").innerText = total.toFixed(2);

    document.getElementById("words").innerText = numberToWords(total);

    // GST SUMMARY
    document.getElementById("hsn_summary").innerText = document.getElementById("hsn").value || '-';
    document.getElementById("taxable_summary").innerText = amount.toFixed(2);
    document.getElementById("taxable_total").innerText = amount.toFixed(2);

    document.getElementById("cgst_rate_display").innerText = cgstRate + "%";
    document.getElementById("sgst_rate_display").innerText = sgstRate + "%";

    document.getElementById("cgst_summary").innerText = cgst.toFixed(2);
    document.getElementById("sgst_summary").innerText = sgst.toFixed(2);

    document.getElementById("cgst_total").innerText = cgst.toFixed(2);
    document.getElementById("sgst_total").innerText = sgst.toFixed(2);

    let totalTax = cgst + sgst;
    document.getElementById("total_tax_summary").innerText = totalTax.toFixed(2);
    document.getElementById("final_tax_total").innerText = totalTax.toFixed(2);
}

function numberToWords(num){
    num = Math.floor(num);

    const a = ["","One","Two","Three","Four","Five","Six","Seven","Eight","Nine","Ten",
    "Eleven","Twelve","Thirteen","Fourteen","Fifteen","Sixteen","Seventeen","Eighteen","Nineteen"];

    const b = ["","","Twenty","Thirty","Forty","Fifty","Sixty","Seventy","Eighty","Ninety"];

    function convert(n){
        if(n < 20) return a[n];
        if(n < 100) return b[Math.floor(n/10)] + " " + a[n%10];
        if(n < 1000) return a[Math.floor(n/100)] + " Hundred " + convert(n%100);
        if(n < 100000) return convert(Math.floor(n/1000)) + " Thousand " + convert(n%1000);
        if(n < 10000000) return convert(Math.floor(n/100000)) + " Lakh " + convert(n%100000);
        return "";
    }

    return convert(num) + " Rupees Only";
}
</script>

</head>

<body>

<div class="invoice">

<!-- HEADER -->
<table>
<tr class="header"><td colspan="7">Tax Invoice</td></tr>

<tr>

<td width="20%" class="center">
<?php if (!empty($profile['logo'])) { ?>
<img src="uploads/<?php echo $profile['logo']; ?>" class="logo">
<?php } ?>
</td>

<td colspan="3" class="seller-box">
<b><?php echo $profile['shop_name'] ?? ''; ?></b><br>
Address: <?php echo $profile['address'] ?? ''; ?><br>
GSTIN: <?php echo $profile['gst'] ?? ''; ?><br>
Phone: <?php echo $profile['mobile'] ?? ''; ?><br>
Email: <?php echo $profile['email'] ?? ''; ?>
</td>

<td colspan="3">
Invoice No: <input><br>
Dispatch Doc: <input><br>
Date: <input type="date"><br>
Destination: <input><br>
E-Way Bill: <input><br>
Delivery Date: <input type="date"><br>
Dispatch: <input><br>
Vehicle No: <input>
</td>

</tr>
</table>

<!-- BUYER -->
<table>
<tr class="section"><td colspan="7">Buyer (Bill To)</td></tr>
<tr><td colspan="7">Name: <input></td></tr>
<tr><td colspan="7">Address: <input></td></tr>
<tr>
<td colspan="3">GSTIN: <input></td>
<td colspan="4">State: <input></td>
</tr>
<tr><td colspan="7">Phone: <input></td></tr>
</table>

<!-- PRODUCT -->
<table>
<tr class="section center">
<th>S.No</th><th>Description</th><th>HSN</th><th>Qty</th>
<th>Rate</th><th>Unit</th><th>Amount</th>
</tr>

<tr>
<td>1</td>

<td>
<select onchange="fillProduct(this)">
<option>Select</option>
<?php if($products){ while($p=$products->fetch_assoc()){ ?>
<option 
data-hsn="<?= $p['hsn'] ?>"
data-price="<?= $p['price'] ?>"
data-unit="<?= $p['unit'] ?? 'Piece' ?>">
<?= $p['name'] ?>
</option>
<?php }} ?>
</select>
</td>

<td><input id="hsn"></td>
<td><input id="qty" oninput="calculate()"></td>
<td><input id="rate"></td>

<td>
<select id="unit">
<option>Piece</option>
<option>Packet</option>
<option>Kg</option>
<option>Gram</option>
<option>Liter</option>
</select>
</td>

<td><input id="amount_input"></td>
</tr>

<tr><td colspan="6" class="right"><b>Taxable Amount</b></td>
<td id="amount_display">0.00</td></tr>

<tr>
<td colspan="5"></td>
<td>CGST<br>
<select id="cgst_rate" onchange="calculate()">
<option value="2.5">2.5%</option>
<option value="6">6%</option>
<option value="9">9%</option>
</select></td>
<td id="cgst_amt">0.00</td>
</tr>

<tr>
<td colspan="5"></td>
<td>SGST<br>
<select id="sgst_rate" onchange="calculate()">
<option value="2.5">2.5%</option>
<option value="6">6%</option>
<option value="9">9%</option>
</select></td>
<td id="sgst_amt">0.00</td>
</tr>

<tr><td colspan="5"></td>
<td><b>Total</b></td>
<td id="total">0.00</td></tr>
</table>

<!-- WORD -->
<table>
<tr><td><b>Amount in words:</b> <span id="words"></span></td></tr>
</table>

<!-- GST SUMMARY -->
<table>
<tr class="section center">
<th>HSN/SAC</th><th>Taxable</th><th>CGST</th><th>SGST</th><th>Total Tax</th>
</tr>
<tr class="center">
<td id="hsn_summary">-</td>
<td id="taxable_summary">0.00</td>
<td id="cgst_summary">0.00</td>
<td id="sgst_summary">0.00</td>
<td id="total_tax_summary">0.00</td>
</tr>
</table>

<!-- TERMS -->
<table>
<tr>
<td>
<b>Terms and conditions</b><br>
1. Goods once sold will not be returned.<br>
2. Warranty as per company policy.<br>
3. Subject to jurisdiction.
</td>
</tr>
</table>

<!-- SIGN -->
<table>
<tr>
<td class="center" style="height:80px;">Customer Signature</td>
<td class="center">Authorised Signature<br>For: <?= $profile['shop_name'] ?? '' ?></td>
</tr>
</table>

<table>
<tr class="header">
<td>This is computer generated invoice</td>
</tr>
</table>

<br>
<button onclick="window.print()">Print</button>

</div>

</body>
</html>
