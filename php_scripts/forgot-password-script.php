<?php 

if(isset($_POST["forgot-password-submit"])){

    $email=$_POST["forgot-password-email"];

    require_once 'db-connection.php';
    require_once 'functions-scripts.php';
    
    if(emptyInputLogin($email,$email)!==false){
        header("location: ../login.php?error=emptyinput");
        exit();
    }

    forgotPassword($conn,$email);
    
}else{
    header("location: ../login.php");
    exit();
}