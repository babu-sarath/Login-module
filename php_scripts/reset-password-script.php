<?php 

if(isset($_POST["submit"])){

    $pwd=$_POST["pwd"];
    $pwdRepeat=$_POST["pwdRepeat"];
    $selector=$_POST["selector"];
    $validator=$_POST["validator"];

    require_once 'db-connection.php';
    require_once 'functions-scripts.php';

    if(emptyInputLogin($pwd,$pwdRepeat)!==false){
        header("location: ../login.php?error=emptyinput");
        exit();
    }

    if(pwdMatch($pwd,$pwdRepeat)!==false){
        header("location: ../reset-password.php?error=pwdnomatch");
        exit();
    }

    resetPassword($conn,$email,$pwd,$selector,$validator);
    
    
}else{
    header("location: ../login.php");
    exit();
}