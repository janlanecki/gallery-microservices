<?php

echo "<!DOCTYPE html>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
<title>Mimages Gallery</title>
</head>
<body>";
echo get_loaded_extensions();
echo"<form action='upload.php' method='post' enctype='multipart/form-data'>
Select image to upload (.jpg, max 10MB):<br>
<input type='file' name='upfile' id='upfile' label='Select' required><br>
<input type='text' name='name' id='name' placeholder='Image title' required>
<input type='submit' value='Upload Image' name='submit'>
</form><br>";


$ids_json = file_get_contents('http://web_app/image.php');

$ids = json_decode($ids_json, true)["ids"];
foreach ($ids as $id) {
    $img_json = file_get_contents('http://web_app/image.php?id='.$id);
    $img = json_decode($img_json, true);
    echo "<a href='http://localhost:8001/details.php?id=".$id."'><img style='max-width:200px;' src='".$img["thumb"]."'></a>";
}


echo"</body>
</html>";