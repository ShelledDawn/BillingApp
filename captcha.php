<?php
session_start();

$chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
$captcha = '';

for ($i = 0; $i < 5; $i++) {
    $captcha .= $chars[rand(0, strlen($chars) - 1)];
}

$_SESSION['captcha'] = $captcha;

$image = imagecreate(120, 40);
$bg = imagecolorallocate($image, 255, 255, 255);
$textcolor = imagecolorallocate($image, 0, 0, 0);

// noise
for ($i = 0; $i < 5; $i++) {
    imageline($image, rand(0,120), rand(0,40), rand(0,120), rand(0,40), $textcolor);
}

imagestring($image, 5, 25, 10, $captcha, $textcolor);

header("Content-type: image/png");
imagepng($image);
imagedestroy($image);
?>