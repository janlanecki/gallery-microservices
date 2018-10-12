<?php

include 'clear.php';


//require_once __DIR__ . '/vendor/autoload.php';
require_once '/var/www/html/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;


$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->queue_declare('rabbitmq', false, true, false, false);


$upload_dir = "upload/";


echo "<html>
<head>
<title>Mimages Upload</title>
</head>
<body>
<center>";

try {
    if (
        !isset($_FILES['upfile']['error']) ||
        is_array($_FILES['upfile']['error'])
    ) {
        throw new RuntimeException('Invalid parameters.');
    }

    switch ($_FILES['upfile']['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            throw new RuntimeException('No file sent.');
        case UPLOAD_ERR_INI_SIZE:
            throw new RuntimeException('Ini size.');
        case UPLOAD_ERR_FORM_SIZE:
            throw new RuntimeException('Exceeded filesize limit.');
        default:
            throw new RuntimeException('Unknown errors.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    if (false === $ext = array_search(
        $finfo->file($_FILES['upfile']['tmp_name']),
        array(
            'jpg' => 'image/jpeg',
        ),
        true
    )) {
        throw new RuntimeException('Invalid file format.');
    }

    $custom_name = $_POST["name"];

    $final_name = sprintf('./%s%s.%s',
        $upload_dir,
        sha1($custom_name.sha1_file($_FILES['upfile']['tmp_name'])),
        $ext
    );

    if (!move_uploaded_file(
        $_FILES['upfile']['tmp_name'],
        $final_name
    )) {
        throw new RuntimeException('Failed to move uploaded file.');
    }

    chmod($final_name, 0777);

    echo 'File uploaded successfully.';

    $url = "http://localhost:8001".substr($final_name, 1);
    $array = array('name' => $custom_name, 'url' => $url);
    $data = json_encode($array);

    // queue part 
    if (!empty($data)) {
        $msg = new AMQPMessage(
            $data,
            array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT)
        );
        $channel->basic_publish($msg, '', 'rabbitmq');
    }
    

    clear($upload_dir);


} catch (RuntimeException $e) {

    echo $e->getMessage();

}

echo "<br><a href='index.php'>Go back</a>";
echo "</center>
</body>
</html>";

$channel->close();
$connection->close();
