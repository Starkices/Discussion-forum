<?php
//connect to mysql server

define('Host', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'discussion_forum');
// Create connection
$conn = mysqli_connect(Host, DB_USERNAME, DB_PASSWORD, DB_NAME);

if (mysqli_connect_errno()) {
    die("Connection failed: " . mysqli_connect_error());
} else {
    
}

try{
    $pdo = new PDO('mysql:host=localhost;dbname=discussion_forum', 'root', '');
    if(!$pdo){
        throw new Exception("Connection failed");
    }

}catch(Exception $e){
    echo $e;
}
