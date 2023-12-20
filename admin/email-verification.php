<?php
session_start(); // remove if needed
include('config/dbcon.php');

if(isset($_GET['token']))
{
    $token = $_GET['token'];
    $verify_query = "SELECT verification_code, verify_status FROM users WHERE verification_code='$token' LIMIT 1";
    $verify_query_run = mysqli_query($con, $verify_query);

    if(mysqli_num_rows($verify_query_run)>0)
    {
        $row = mysqli_fetch_array($verify_query_run);
        
        if($row['verify_status'] == "0")
        {
            $clicked_token = $row['verification_code'];
            $update_query = "UPDATE users SET verify_status='1' WHERE verification_code='$clicked_token' LIMIT 1";
            $update_query_run = mysqli_query($con, $update_query);

            if($update_query_run)
            {
                $_SESSION['message'] = "Verification successful!";
                header("Location: login.php");
                exit(0);

            }
            else
            {
                $_SESSION['message'] = "Verification has failed.";
                header("Location: login.php");
                exit(0);
            }

        }

        else
        {
            $_SESSION['message'] = "Email has been verified already. Log in.";
            header("Location: login.php");
            exit(0);
        }

    }
    else
    {
        $_SESSION['message'] = "Something went wrong.";
        header("Location: login.php");
    }

}
else
{
    $_SESSION['message'] = "Not allowed";
    header("Location: login.php");
}
?>