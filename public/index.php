<?php
if (isset($_SESSION['user'])){
    header("location:dashboard.php");
}
else{
    header("location:login.php");
}
?>