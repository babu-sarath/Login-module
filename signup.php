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
            <form action="php_scripts/signup-script.php" method="POST" class="form-group">
                <h4>Sign Up</h4>
                <input type="email" name="email" class="form-control my-3" placeholder="Email" required />
                <input type="text" name="name" class="form-control my-3" placeholder="Name" required />
                <input type="password" name="pwd" class="form-control my-3" placeholder="Password" required />
                <input type="password" name="pwdRepeat" class="form-control" placeholder="Confirm Password" required />
                <input type="submit" name="submit" class="btn btn-block btn-info mt-3" value="Signup" required />
                <a href="./login.php">Login</a>

                <?php
                    //getting the error from server
                    if(isset($_GET["error"])){
                        if($_GET["error"]=="none"){
                            echo '<div id="alert" class="alert alert-success alert-dismissible fade show text-center" role="alert">You have successfully registered</div>';
                        }else{
                            $error;
                            switch($_GET["error"]){
                                case "emptyinput": $error= "You forgot to fill some field! Fill all the fields";
                                break;
                                case "pwdnomatch": $error= "The passwords you entered dont match";
                                break;
                                case "emailexists": $error= "This email is already registered! Try logging in";
                                break;
                                default: $error= "Something has gone wrong! Try again";
                            }
                            echo '<div id="alert" class="alert alert-danger alert-dismissible fade show text-center" role="alert">'.$error.'</div>';
                        
                        }
                        
                    }
                ?>

            </form>
        </div>
    </div>
</div>


<?php
	include_once 'layout/footer.php'
?>