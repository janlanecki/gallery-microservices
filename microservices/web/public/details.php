<?php

echo "<!DOCTYPE html>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
<title>Mimages Gallery</title>
</head>
<body>
<center>";


if (isset($_GET["id"])) {
    $img_json = file_get_contents('http://web_app/image.php?id='.$_GET["id"]);
    $img = json_decode($img_json, true);

    echo "<b>".$img["name"]."</b>";
    echo "<br>";
    echo "<img style='max-width:100%;' src='".$img["full"]."'>";



} else {
    echo "File not found.";
}

echo"</center>
</body>
</html>";