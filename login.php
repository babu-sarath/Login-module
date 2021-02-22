<?php
    include_once 'layout/header.php';
    
    session_start();
    if(isset($_SESSION["useremail"])){
        header("location: index.php");
        exit();
    }
?>

<div class="container">
    <div class="row justify-content-center py-5">
        <div class="col-4">
            <form action="./php_scripts/login-script.php" method="POST" class="form-group">
                <h4>Login</h4>
                <input type="email" name="email" class="form-control my-3" placeholder="Email" />
                <input type="password" name="pwd" class="form-control" placeholder="Password" />
                <input type="submit" name="submit" class="btn btn-block btn-info mt-3" value="Login" />
                <a href="./signup.php">Signup</a>
                <a data-toggle="modal" data-target="#forgot-password-modal">Forgot Password?</a> <?php
                    //getting the error from server
                    
                    if(isset($_GET["error"])){
                        $error;
                        switch($_GET["error"]){
                            case "emptyinput": $error="You forgot to fill some field! Fill all the fields";
                            break;
                            case "nouser": $error= "The user does not exist! Signup";
                            break;
                            case "wrongpassword": $error= "The password entered is wrong";
                            break;
                            case "passwordresetsent": $error= "The password reset has been sent";
                            break;
                            case "passwordresetsuccess": $error= "The password has been reset. Login with new password";
                            break;
                            default: $error= "Something has gone wrong! Try again";
                        }
                        echo '<div id="alert" class="alert alert-danger alert-dismissible fade show text-center" role="alert">'.$error.'</div>';
                    }
                ?>
            </form>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="forgot-password-modal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Forgot Password</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="php_scripts/forgot-password-script.php" method="POST">
                        Enter your email and we will send password reset instructions.
                        <input type="email" name="forgot-password-email" class="form-control my-3"
                            placeholder="Email" />
                        <input type="submit" name="forgot-password-submit" class="btn btn-block btn-info mt-3"
                            value="Send" />
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
<?php
	include_once 'layout/footer.php';
?>