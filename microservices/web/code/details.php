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
    $img_json = file_get_contents('http://images_app:80/images/'.$_GET["id"]);
    $img = json_decode($img_json, true);

    $like_json = file_get_contents('http://like_app:80/likes/'.$_GET["id"]);
    $like = json_decode($like_json, true);

    
    echo "<b>".$img["name"]."</b> | Polubienia: ".$like["likes"]." ";
    
    echo "<form action='http://localhost:8001/details.php' method='get'>
    <input type='hidden' value=".$_GET["id"]." name='id'>
    <input type='hidden' value='1' name='like'>
    <input type='submit' value='Like it!'>
    </form>";
    echo "<br><a href='index.php'>Go back!</a>";
    echo "<br><br>";
    if ($_GET["like"]) {
        $url = "http://like_app:80/like/".$_GET["id"]; 
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
        ));
    
        $response = curl_exec($ch);
        curl_close($ch); 
    }
    echo "<img style='max-width:100%;' src='".$img["full"]."'>";


} else {
    echo "File not found.";
}

echo"</center>
</body>
</html>";
