<?php 

function emptyInputSignup($name,$email,$pwd,$pwdRepeat){
    $result;
    if(empty($name) || empty($email) || empty($pwd) || empty($pwdRepeat)){
        //there is error
        $result=true;
    }else{
        //no erro
        $result=false;
    }
    return $result;
}

function pwdMatch($pwd,$pwdRepeat){
    $result;
    if($pwd !== $pwdRepeat){
        //there is error
        $result=true;
    }else{
        //no erro
        $result=false;
    }
    return $result;
}

function emailExists($conn,$email){
    $sql="SELECT * FROM users WHERE usersEmail=?;";
    $stmt=mysqli_stmt_init($conn);
    if(!mysqli_stmt_prepare($stmt,$sql)){
        header("location: ../signup.php?error=stmtfailed");
        exit();
    }

    //creating prepared stmt and bind the values
    mysqli_stmt_bind_param($stmt,"s",$email);
    mysqli_stmt_execute($stmt);

    //getting the result from db
    $resultData=mysqli_stmt_get_result($stmt);

    //check if the email already exists 
    if($row=mysqli_fetch_assoc($resultData)){
        return $row;
    }else{
        $result=false;
        return $result;
    }

    mysqli_stmt_close($stmt);
    
}

function emptyInputLogin($email,$pwd){
    $result;
    if(empty($email) || empty($pwd)){
        //there is error
        $result=true;
    }else{
        //no erro
        $result=false;
    }
    return $result;
}

function createUser($conn,$name,$email,$pwd){
    $sql="INSERT INTO users(usersName,usersEmail,usersPassword) VALUES(?,?,?);";
    $stmt=mysqli_stmt_init($conn);
    if(!mysqli_stmt_prepare($stmt,$sql)){
        header("location: ../signup.php?error=stmtfailed");
        exit();
    }

    $hashedPwd=password_hash($pwd, PASSWORD_DEFAULT);
    
    //creating prepared stmt and bind the values
    mysqli_stmt_bind_param($stmt,"sss",$name,$email,$hashedPwd);
    mysqli_stmt_execute($stmt);
    
    mysqli_stmt_close($stmt);
    
    header("location: ../signup.php?error=none");
    exit();
    
}

function loginUser($conn,$email,$pwd){
    $userExists=emailExists($conn,$email);

    if($userExists==false){
        header("location: ../login.php?error=nouser");
        exit();
    }

    $pwdHashed=$userExists["usersPassword"];
    $checkPassword=password_verify($pwd,$pwdHashed);

    if($checkPassword==false){
        header("location: ../login.php?error=wrongpassword");
        exit();
    }else if($checkPassword==true){
        session_start();
        $_SESSION["useremail"]=  $email;
        $_SESSION["username"]=  $userExists["usersName"];
        header("location: ../index.php");
        exit();
    }
    
}

function forgotPassword($conn,$email){
    $userExists=emailExists($conn,$email);
    if($userExists==false){
        header("location: ../login.php?error=nouser");
        exit();
    }

    //generating random bytes as tokens
    $selector= bin2hex(random_bytes(8));
    $token=random_bytes(64);
    
    $url="http://localhost/PHP/login/reset-password.php?selector=".$selector."&validator=".bin2hex($token);
    
    //10 minutes expiry
    $expiry=date("U")+600;
    
    $sql="DELETE FROM passwordReset WHERE passwordResetEmail=?;";
    $stmt=mysqli_stmt_init($conn);
    if(!mysqli_stmt_prepare($stmt,$sql)){
        header("location: ../login.php?error=stmtfailed");
        exit();
    }

    //creating prepared stmt and bind the values
    mysqli_stmt_bind_param($stmt,"s",$email);
    mysqli_stmt_execute($stmt);

    $sql="INSERT INTO passwordReset(passwordResetEmail,passwordResetSelector,passwordResetToken,passwordResetExpiry) VALUES(?,?,?,?);";
    $stmt=mysqli_stmt_init($conn);
    if(!mysqli_stmt_prepare($stmt,$sql)){
        header("location: ../login.php?error=stmtfailed");
        exit();
    }

    $hashedToken=password_hash($token,PASSWORD_DEFAULT);

    //creating prepared stmt and bind the values
    mysqli_stmt_bind_param($stmt,"ssss",$email,$selector,$hashedToken,$expiry);
    mysqli_stmt_execute($stmt);
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);


    //sending the email

    //refer this link https://www.youtube.com/watch?v=EM630O5W-_I&ab_channel=DomainRacer

    require_once '../PHPMailer/PHPMailerAutoload.php';

    $mail = new PHPMailer;

    //$mail->SMTPDebug = 3;                               // Enable verbose debug output

    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'smtp1.example.com;smtp2.example.com';  // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = 'user@example.com';                 // SMTP username
    $mail->Password = 'secret';                           // SMTP password
    $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;                                    // TCP port to connect to

    $mail->setFrom('from@example.com', 'Mailer');
    $mail->addAddress($email);     // Add a recipient
    $mail->addReplyTo('info@example.com', 'Information');
    $mail->addCC('cc@example.com');
    $mail->addBCC('bcc@example.com');

    $mail->isHTML(true);                                  // Set email format to HTML

    $mail->Subject = 'Password Reset';
    $mail->Body    = 'Password reset link. '.$url;
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    if(!$mail->send()) {
        header("location: ../login.php?error=nouser");
        exit();
        // echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        header("location: ../login.php?error=passwordresetsent");
        exit();
    }
    
}

function resetPassword($conn,$email,$pwd,$selector,$validator){
    $currentTime=date("U");
    $sql="SELECT * FROM passwordReset WHERE passwordResetSelector=? AND passwordResetExpiry>=?;";
    $stmt=mysqli_stmt_init($conn);
    if(!mysqli_stmt_prepare($stmt,$sql)){
        header("location: ../reset-password.php?error=stmtfailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt,"ss",$selector,$expiry);
    mysqli_stmt_execute($stmt);

    $result=mysqli_stmt_get_result($stmt);
    $row;
    if(!$row=mysqli_fetch_assoc($result)){
        header("location: ../reset-password.php?error=stmtfailed");
        exit();
    }
    
    $tokenBin=hex2bin($validator);
    $tokenCheck= password_verify($tokenBin,$row["passwordResetToken"]);

    if($tokenCheck==false){
        header("location: ../reset-password.php?error=stmtfailed");
        exit();
    }elseif($tokenCheck==true){
        //do the password reset
        $tokenEmail=$row["passwordResetEmail"];
        
        $sql="SELECT * FROM users WHERE usersEmail=?;";
        $stmt=mysqli_stmt_init($conn);
        if(!mysqli_stmt_prepare($stmt,$sql)){
            header("location: ../reset-password.php?error=stmtfailed");
            exit();
        }
        mysqli_stmt_bind_param($stmt,"s",$tokenEmail);
        mysqli_stmt_execute($stmt);

        $result=mysqli_stmt_get_result($stmt);
        $row;
        if(!$row=mysqli_fetch_assoc($result)){
            header("location: ../reset-password.php?error=stmtfailed");
            exit();
        }else {
            # code...
            $sql="UPDATE users SET usersPassword=? WHERE usersEmail=?;";
            $stmt=mysqli_stmt_init($conn);
            if(!mysqli_stmt_prepare($stmt,$sql)){
                header("location: ../reset-password.php?error=stmtfailed");
                exit();
            }
            $hashedPwd=password_hash($pwd,PASSWORD_DEFAULT);
            mysqli_stmt_bind_param($stmt,"ss",$hashedPwd,$tokenEmail);
            mysqli_stmt_execute($stmt);
            
            //delete the token from the reset table
            $sql="DELETE FROM passwordReset WHERE passwordResetEmail?=;";
            $stmt=mysqli_stmt_init($conn);
            if(!mysqli_stmt_prepare($stmt,$sql)){
                header("location: ../reset-password.php?error=stmtfailed");
                exit();
            }
            $hashedPwd=password_hash($pwd,PASSWORD_DEFAULT);
            mysqli_stmt_bind_param($stmt,"s",$tokenEmail);
            mysqli_stmt_execute($stmt);
            header("location: ../login.php?error=passwordresetsuccess");
            exit();
        }
            
    }
    
}