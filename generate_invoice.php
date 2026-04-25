<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'db.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit();
}

/* SAFE FETCH */
$profileQ = $conn->query("SELECT * FROM profile LIMIT 1");
$profile = $profileQ ? $profileQ->fetch_assoc() : [];

$products = $conn->query("SELECT * FROM products");

/* SAVE */
if(isset($_POST['save'])){
    $invoice_no = "INV" . time();
    $total = array_sum($_POST['amount'] ?? [0]);

    $conn->query("INSERT INTO invoices (invoice_no,total) VALUES ('$invoice_no','$total')");
    $invoice_id = $conn->insert_id;

    for($i=0; $i<count($_POST['product']); $i++){
        $name = $_POST['product'][$i];
        $hsn = $_POST['hsn'][$i];
        $qty = $_POST['qty'][$i];
        $rate = $_POST['rate'][$i];
        $unit = $_POST['unit'][$i];
        $amt = $_POST['amount'][$i];

        $conn->query("INSERT INTO invoice_items 
        (invoice_id,product_name,hsn,qty,rate,unit,amount)
        VALUES ('$invoice_id','$name','$hsn','$qty','$rate','$unit','$amt')");
    }

    echo "<script>alert('Invoice Saved Successfully');</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Generate Invoice</title>

<style>
body { font-family: Arial; }
.invoice { width: 900px; margin:auto; border:1px solid #000; }
table { width:100%; border-collapse:collapse; }
td,th { border:1px solid #000; padding:5px; font-size:13px; }
.header { background:#5d88b5; color:#fff; text-align:center; }
.section { background:#e5e88c; font-weight:bold; }
.center { text-align:center; }
.right { text-align:right; }
input,select { width:100%; border:none; }
.logo { width:90px; }
</style>

<script>
function fillRow(sel){
    let row = sel.parentNode.parentNode;
    let opt = sel.options[sel.selectedIndex];

    row.querySelector(".hsn").value = opt.dataset.hsn || '';
    row.querySelector(".rate").value = opt.dataset.price || '';
    row.querySelector(".unit").value = opt.dataset.unit || 'Piece';

    calcAll();
}

function addRow(){
    let table = document.getElementById("rows");
    let row = table.rows[0].cloneNode(true);

    row.querySelectorAll("input").forEach(i => i.value="");
    row.querySelector("select").selectedIndex = 0;

    table.appendChild(row);
    updateSerial();
}

function removeRow(btn){
    let row = btn.parentNode.parentNode;
    if(document.getElementById("rows").rows.length>1){
        row.remove();
    }
    updateSerial();
    calcAll();
}

function updateSerial(){
    let rows = document.getElementById("rows").rows;
    for(let i=0;i<rows.length;i++){
        rows[i].cells[0].innerText = i+1;
    }
}

function calcAll(){
    let rows = document.getElementById("rows").rows;
    let total = 0;

    for(let r of rows){
        let qty = parseFloat(r.querySelector(".qty").value)||0;
        let rate = parseFloat(r.querySelector(".rate").value)||0;
        let amt = qty*rate;

        r.querySelector(".amount").value = amt.toFixed(2);
        total += amt;
    }

    document.getElementById("taxable").innerText = total.toFixed(2);

    let cg = parseFloat(document.getElementById("cgst_rate").value)||0;
    let sg = parseFloat(document.getElementById("sgst_rate").value)||0;

    let cgst = total*cg/100;
    let sgst = total*sg/100;

    document.getElementById("cgst_amt").innerText = cgst.toFixed(2);
    document.getElementById("sgst_amt").innerText = sgst.toFixed(2);

    let grand = total+cgst+sgst;
    document.getElementById("grand").innerText = grand.toFixed(2);

    document.getElementById("words").innerText = Math.floor(grand) + " Rupees Only";
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
<?php if(!empty($profile['logo'])){ ?>
<img src="uploads/<?php echo $profile['logo']; ?>" class="logo">
<?php } ?>
</td>

<td colspan="3">
<b><?php echo $profile['shop_name'] ?? ''; ?></b><br>
<?php echo $profile['address'] ?? ''; ?><br>
GSTIN: <?php echo $profile['gst'] ?? ''; ?>
</td>

<td colspan="3">
Invoice No <input><br>
Dispatch Doc <input><br>
Date <input type="date"><br>
</td>
</tr>
</table>

<!-- BUYER -->
<table>
<tr class="section"><td colspan="7">Buyer</td></tr>
<tr><td colspan="7">Name <input></td></tr>
</table>

<form method="POST">

<!-- PRODUCTS -->
<table>
<tr class="section">
<th>S.No</th><th>Product</th><th>HSN</th><th>Qty</th>
<th>Rate</th><th>Unit</th><th>Amt</th><th>X</th>
</tr>

<tbody id="rows">
<tr>
<td>1</td>
<td>
<select name="product[]" onchange="fillRow(this)">
<option>Select</option>
<?php while($p=$products->fetch_assoc()){ ?>
<option data-hsn="<?=$p['hsn']?>" data-price="<?=$p['price']?>" data-unit="<?=$p['unit']?>">
<?=$p['name']?>
</option>
<?php } ?>
</select>
</td>
<td><input name="hsn[]" class="hsn"></td>
<td><input name="qty[]" class="qty" oninput="calcAll()"></td>
<td><input name="rate[]" class="rate"></td>
<td><input name="unit[]" class="unit"></td>
<td><input name="amount[]" class="amount"></td>
<td><button type="button" onclick="removeRow(this)">X</button></td>
</tr>
</tbody>
</table>

<button type="button" onclick="addRow()">+ Add Row</button>

<br><br>

Taxable: ₹ <span id="taxable">0.00</span><br>

CGST 
<select id="cgst_rate" onchange="calcAll()">
<option value="2.5">2.5%</option>
<option value="6">6%</option>
<option value="9">9%</option>
</select>
= ₹ <span id="cgst_amt">0.00</span><br>

SGST 
<select id="sgst_rate" onchange="calcAll()">
<option value="2.5">2.5%</option>
<option value="6">6%</option>
<option value="9">9%</option>
</select>
= ₹ <span id="sgst_amt">0.00</span><br>

Total: ₹ <span id="grand">0.00</span>

<br><br>

Amount in words: <span id="words"></span>

<br><br>

<button type="submit" name="save">Save Invoice</button>

</form>

<!-- TERMS -->
<table>
<tr><td>
<b>Terms</b><br>
Goods not returnable
</td></tr>
</table>

<!-- SIGN -->
<table>
<tr>
<td>Customer</td>
<td>Authorised</td>
</tr>
</table>

<br>
<button onclick="window.print()">Print</button>

</div>

</body>
</html>
