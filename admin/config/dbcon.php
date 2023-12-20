<?php

$host = "localhost";
$username = "id21691733_zeppeliz"; //"root";
$password = "Haribentahan69!";
$database = "id21691733_haribentahan";

//Database connection
$con = mysqli_connect($host, $username, $password, $database);

//Check database connection
if (!$con){
    die("Connection failed: ". mysqli_connect_error());
}

?>