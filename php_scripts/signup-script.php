<?php 

if(isset($_POST["submit"])){
    $name=$_POST["name"];
    $email=$_POST["email"];
    $pwd=$_POST["pwd"];
    $pwdRepeat=$_POST["pwdRepeat"];

    require_once 'db-connection.php';
    require_once 'functions-scripts.php';

    if(emptyInputSignup($name,$email,$pwd,$pwdRepeat)!==false){
        header("location: ../signup.php?error=emptyinput");
        exit();
    }

    if(pwdMatch($pwd,$pwdRepeat)!==false){
        header("location: ../signup.php?error=pwdnomatch");
        exit();
    }

    if(emailExists($conn,$email)!==false){
        header("location: ../signup.php?error=emailexists");
        exit();
    }
    
    createUser($conn,$name,$email,$pwd);
        
}else{
    header("location: ../signup.php");
    exit();
}