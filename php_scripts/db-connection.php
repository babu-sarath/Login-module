<?php 
$server="localhost";
$db_usr_name="root";
$db_pwd="";
$db_name="login_system";

$conn= mysqli_connect($server,$db_usr_name,$db_pwd,$db_name);

if(!$conn){
    die("Connection failed. Error: ".mysqli_connect_error());
}