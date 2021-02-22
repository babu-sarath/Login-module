<?php
	include_once 'layout/header.php'
?>
<?php
    session_start();
    //check if any session exists
    if(isset($_SESSION["useremail"])){
        
    }else{
        header("location: login.php");
        exit();
    }
?>

<div class="container">
    <div class="row justify-content-center py-5">
        <div class="col-4">
            <h4>Hello Boi</h4>
            <a href="php_scripts/logout-script.php">Logout</a>
        </div>
    </div>
</div>

<?php
	include_once 'layout/footer.php'
?>