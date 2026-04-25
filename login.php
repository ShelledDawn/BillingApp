<?php
session_start();

if ($_POST) {
    $user = $_POST['user'];
    $pass = $_POST['pass'];
    $captcha = $_POST['captcha'];

    if ($user == "Owner2025" && $pass == "Owner@9142") {

        if (strtoupper($captcha) == $_SESSION['captcha']) {
            $_SESSION['login'] = true;
            header("Location: dashboard.php");
        } else {
            $error = "Invalid CAPTCHA!";
        }

    } else {
        $error = "Invalid Username or Password!";
    }
}
?>

<link rel="stylesheet" href="style.css">

<div class="login-box">
<h2>Login</h2>

<form method="POST">
    <input type="text" name="user" placeholder="User ID"><br>
    <input type="password" name="pass" placeholder="Password"><br>

    <img src="captcha.php" id="captcha_img">
    <button type="button" onclick="refreshCaptcha()">↻</button><br>

    <input type="text" name="captcha" placeholder="Enter CAPTCHA"><br>

    <button type="submit">Login</button>
</form>

<?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
</div>

<script>
function refreshCaptcha() {
    document.getElementById("captcha_img").src = "captcha.php?" + Date.now();
}
</script>