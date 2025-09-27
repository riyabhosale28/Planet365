<?php
$conn=new mysqli("localhost","root","","planet365");
if($conn->connect_error){
    die("Connection failed:". $conn->connect_error);
}
?>