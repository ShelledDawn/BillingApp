<?php
session_start();
include 'db.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit();
}

// SAVE DATA
if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $shop = $_POST['shop'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $gst = $_POST['gst'];

    // Fetch old data
    $data = $conn->query("SELECT * FROM profile LIMIT 1")->fetch_assoc();
    $logo_name = $data['logo'];

    // Upload logo
    if (!empty($_FILES['logo']['name'])) {
        $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));

        if ($ext == "jpg" || $ext == "jpeg") {
            $logo_name = time() . "." . $ext;
            move_uploaded_file($_FILES['logo']['tmp_name'], "uploads/" . $logo_name);
        }
    }

    $conn->query("UPDATE profile SET 
        shop_name='$shop',
        address='$address',
        email='$email',
        mobile='$mobile',
        gst='$gst',
        logo='$logo_name'
    WHERE id=1");

    // Redirect with success
    header("Location: profile.php?success=1");
    exit();
}

// FETCH DATA
$data = $conn->query("SELECT * FROM profile LIMIT 1")->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profile</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<!-- SUCCESS POPUP -->
<?php if (isset($_GET['success'])) { ?>
<script>
    alert("Details Saved Successfully!");
</script>
<?php } ?>

<div class="main">

<h2>Profile Settings</h2>

<form method="POST" enctype="multipart/form-data" class="form-box">

    <input type="text" name="shop" placeholder="Shop / Company Name"
        value="<?php echo $data['shop_name']; ?>" required>

    <textarea name="address" placeholder="Address" required><?php echo $data['address']; ?></textarea>

    <input type="email" name="email" placeholder="Email"
        value="<?php echo $data['email']; ?>">

    <input type="text" name="mobile" placeholder="Mobile Number"
        value="<?php echo $data['mobile']; ?>">

    <input type="text" name="gst" placeholder="GST Number"
        value="<?php echo $data['gst']; ?>">

    <label>Upload Logo (JPEG only)</label>
    <input type="file" name="logo" accept="image/jpeg">

    <br><br>

    <!-- SHOW LOGO -->
    <?php if (!empty($data['logo']) && file_exists("uploads/" . $data['logo'])) { ?>
        <img src="uploads/<?php echo $data['logo']; ?>" width="100">
    <?php } ?>

    <br><br>

    <div class="btn-row">
        <button type="submit" class="save-btn">Save</button>

        <button type="button" class="seller-btn" onclick="updateSeller()">Update Details</button>

        <button type="button" class="back-btn" onclick="window.location.href='dashboard.php'">Back</button>
    </div>

</form>

</div>

<script>
function updateSeller() {
    alert("Seller details update feature coming next!");
}
</script>

</body>
</html>