<?php
//session_start();
include('../functions/myfunctions.php');

if(isset($_SESSION['auth']))
{
    if($_SESSION['role_as'] != 1)
    {
        header('Location: ../index.php'); // redirect("../index.php", "You are not authorized to access this page!");
    }

}

else
{
    header('Location: ../login.php'); // redirect("../login.php", "Please log in to continue.");
}

?>