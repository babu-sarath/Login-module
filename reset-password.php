<?php
    include_once 'layout/header.php';
?>

<div class="container">
    <div class="row justify-content-center py-5">
        <div class="col-4">
            <?php 
                $selector=$_GET["selector"];
                $validator=$_GET["validator"];

                if(empty($selector)||empty($validator)){
                    header("location: reset-password.php?error=invalid");
                    exit();
                }
                
                //check for proper hexadecimal format
                if(ctype_xdigit($selector)!==false && ctype_xdigit($validator)!==false){
                    ?>

            <form action="./php_scripts/reset-password-script.php" method="POST" class="form-group">
                <h4>Reset Password</h4>
                <input type="hidden" name="selector" value="<?php echo htmlspecialchars($selector) ?>" />
                <input type="hidden" name="validator" value="<?php echo htmlspecialchars($validator) ?>" />
                <input type="password" name="pwd" class="form-control my-3" placeholder="Password" />
                <input type="password" name="pwdRepeat" class="form-control" placeholder="Confirm Password" />
                <input type="submit" name="submit" class="btn btn-block btn-info mt-3" value="Reset" />
                <?php
                    //getting the error from server
                    
                    if(isset($_GET["error"])){
                        $error;
                        switch($_GET["error"]){
                            case "invalid": $error="The link is invalid";
                            break;
                            case "pwdnomatch": $error= "The passwords you entered dont match";
                            break;
                            case "emptyinput": $error="You forgot to fill some field! Fill all the fields";
                            break;
                            default: $error= "Something has gone wrong! Try again";
                        }
                        echo '<div id="alert" class="alert alert-danger alert-dismissible fade show text-center" role="alert">'.$error.'</div>';
                    }
                ?>
            </form>

            <?php 
            } ?>

        </div>
    </div>
</div>
<?php
	include_once 'layout/footer.php';
?>