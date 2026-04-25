<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'db.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit();
}

$profile = $conn->query("SELECT * FROM profile LIMIT 1")->fetch_assoc();
$products = $conn->query("SELECT * FROM products");
?>

<!DOCTYPE html>
<html>
<head>
<title>Generate Invoice</title>

<style>
body { font-family: Arial; }
.invoice { width: 900px; margin:auto; border:1px solid #000; background:#fff; }
table { width:100%; border-collapse: collapse; }
td,th { border:1px solid #000; padding:5px; font-size:13px; }
.header { background:#5d88b5; color:white; text-align:center; font-weight:bold; }
.section { background:#e5e88c; font-weight:bold; }
.center { text-align:center; }
.right { text-align:right; }
input,select { width:100%; border:none; outline:none; }
</style>

<script>

// Auto fill product details
function fillProduct(sel){
    let opt = sel.options[sel.selectedIndex];

    document.getElementById("hsn").value = opt.dataset.hsn;
    document.getElementById("rate").value = opt.dataset.price;

    // auto select unit
    let unit = opt.dataset.unit || "Piece";
    document.getElementById("unit").value = unit;

    calculate();
}

// Calculation
function calculate(){

    let qty = parseFloat(document.getElementById("qty").value) || 0;
    let rate = parseFloat(document.getElementById("rate").value) || 0;
    let gst = parseFloat(document.getElementById("gst").value) || 0;

    let amount = qty * rate;

    document.getElementById("amount_input").value = amount.toFixed(2);
    document.getElementById("amount_display").innerText = amount.toFixed(2);

    let cgst = amount * gst / 100;
    let sgst = amount * gst / 100;

    document.getElementById("cgst_amt").innerText = cgst.toFixed(2);
    document.getElementById("sgst_amt").innerText = sgst.toFixed(2);

    let total = amount + cgst + sgst;
    document.getElementById("total").innerText = total.toFixed(2);

    document.getElementById("words").innerText = numberToWords(total);
}

// Number to words
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
<tr class="header">
<td colspan="7">Tax Invoice</td>
</tr>

<tr>
<td rowspan="4" class="center" width="20%">
<?php if (!empty($profile['logo'])) { ?>
<img src="uploads/<?php echo $profile['logo']; ?>" width="100">
<?php } ?>
</td>

<td colspan="3">
<b><?php echo $profile['shop_name']; ?></b><br>
<?php echo $profile['address']; ?><br>
GSTIN:- <?php echo $profile['gst']; ?><br>
Phone:- <?php echo $profile['mobile']; ?><br>
Email:- <?php echo $profile['email']; ?>
</td>

<td colspan="3">
Invoice No:- <input><br>
Invoice Date:- <input type="date"><br>
E-Way Bill:- <input><br>
Dispatch Through:- <input>
</td>
</tr>

<tr>
<td colspan="3"></td>
<td colspan="3">
Dispatch Doc:- <input><br>
Destination:- <input><br>
Delivery Date:- <input type="date"><br>
Vehicle No:- <input>
</td>
</tr>
</table>

<!-- BUYER -->
<table>
<tr class="section"><td colspan="7">Buyer (Bill To)</td></tr>

<tr><td colspan="7">Name:- <input></td></tr>
<tr><td colspan="7">Address:- <input></td></tr>

<tr>
<td colspan="3">GSTIN:- <input></td>
<td colspan="4">State:- <input></td>
</tr>

<tr><td colspan="7">Phone:- <input></td></tr>
</table>

<!-- PRODUCT -->
<table>
<tr class="section center">
<th>S.No</th>
<th>Description</th>
<th>HSN</th>
<th>Qty</th>
<th>Rate</th>
<th>Unit</th>
<th>Amount</th>
</tr>

<tr>
<td>1</td>

<td>
<select onchange="fillProduct(this)">
<option value="">Select Product</option>
<?php while($p=$products->fetch_assoc()){ ?>
<option 
data-hsn="<?php echo $p['hsn']; ?>"
data-price="<?php echo $p['price']; ?>"
data-unit="<?php echo $p['unit'] ?? 'Piece'; ?>">
<?php echo $p['name']; ?>
</option>
<?php } ?>
</select>
</td>

<td><input id="hsn"></td>
<td><input id="qty" oninput="calculate()"></td>
<td><input id="rate"></td>

<td>
<select id="unit">
<option value="Piece">Piece</option>
<option value="Packet">Packet</option>
<option value="Kg">Kilogram</option>
<option value="Gram">Gram</option>
<option value="Liter">Liter</option>
<option value="Ton">Ton</option>
</select>
</td>

<td><input id="amount_input"></td>
</tr>

<tr>
<td colspan="6" class="right">Taxable Amount</td>
<td id="amount_display">0.00</td>
</tr>

<tr>
<td colspan="5"></td>
<td>CGST</td>
<td id="cgst_amt">0.00</td>
</tr>

<tr>
<td colspan="5"></td>
<td>SGST</td>
<td id="sgst_amt">0.00</td>
</tr>

<tr>
<td colspan="5"></td>
<td>Total</td>
<td id="total">0.00</td>
</tr>
</table>

<br>

<select id="gst" onchange="calculate()">
<option value="2.5">2.5%</option>
<option value="6">6%</option>
<option value="9">9%</option>
<option value="14">14%</option>
</select>

<!-- WORD -->
<table>
<tr>
<td>Amount in words:- <span id="words"></span></td>
</tr>
</table>

<!-- TERMS -->
<table>
<tr>
<td>
<b>Terms and conditions</b><br>
1. Goods once sold will not be returned.<br>
2. Warranty as per company.<br>
3. Subject to jurisdiction.
</td>
</tr>
</table>

<!-- SIGN -->
<table>
<tr>
<td class="center">Customer Signature</td>
<td class="center">Authorised Signature</td>
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