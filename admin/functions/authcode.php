<?php

include('../config/dbcon.php');
include('myfunctions.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//require 'PHPMailer/src/PHPMailer.php';
//require 'PHPMailer/src/Exception.php';
//require 'PHPMailer/src/SMTP.php';

require '../vendor/autoload.php';

if(!isset($_SESSION)) 
{ 
    session_start(); 
} 

function sendemail_verify($name,$email,$verification_code)
{
    $mail = new PHPMailer(true);

        //Enable verbose debug output
        $mail->SMTPDebug = 0;//SMTP::DEBUG_SERVER;

        //Send using SMTP
        $mail->isSMTP();

        //Set the SMTP server to send through
        $mail->Host = "smtp.office365.com";

        //Enable SMTP authentication
        $mail->SMTPAuth = true;

        //SMTP username
        $mail->Username = "haribentahan.verification@outlook.com";

        //SMTP password
        $mail->Password = "Haribentahan.verify";// yahoo "Haribon.verify"; //;

        //Enable TLS encryption;
        $mail->SMTPSecure = "tls";// PHPMailer::ENCRYPTION_STARTTLS;

        //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
        $mail->Port = 587;

        //Recipients
        $mail->setFrom("haribentahan.verification@outlook.com","PLM Haribentahan");

        //Add a recipient
        $mail->addAddress($email);

        //Set email format to HTML
        $mail->isHTML(true);

        $mail->Subject = "Haribentahan: Email Verification";
        $email_template = "
            <div style='background-color: rgb(220,53,69); padding: 20px; text-align: center; color: #ffffff;'>
                <h2>Verify Your Email</h2>
            </div> 
            <div style='background-color: rgb(255,193,7); padding: 30px; text-align: center; color: #495057; font-size: 16px;'>
                <p>To enjoy Haribentahan at its finest, verify your Haribentahan account by clicking the link below:</p>
                <a href='https://haribentahan.000webhostapp.com/admin/email-verification.php?token=$verification_code'>
                    <button type='button' style='background-color: rgb(220,53,69); padding: 10px 20px; border: none; border-radius: 5px; color: #ffffff; font-size: 16px;'>
                        <b>Click here to verify</b>
                    </button>
                </a>
            </div>
            <div style='background-color: rgb(220,53,69); padding: 20px; text-align: center; color: #6c757d; font-size: 12px;'>
                <p>This is an automated message. Do not reply. Labyu</p>
            </div>";

        $mail->Body = $email_template;
        
        $mail->send();
    
}



if(isset($_POST['register_btn']))
{
    $name = mysqli_real_escape_string($con,$_POST['name']);
    $email = mysqli_real_escape_string($con,$_POST['email']);
    $phone = mysqli_real_escape_string($con,$_POST['phone']);
    $password = mysqli_real_escape_string($con,$_POST['password']);
    $cpassword = mysqli_real_escape_string($con,$_POST['checkPassword']);

    //check if email is already registered
    $check_email_query = "SELECT email FROM users WHERE email = '$email' ";
    $check_email_query_run = mysqli_query($con, $check_email_query);

    $check_phone_query = "SELECT phone FROM users WHERE phone = '$phone' ";
    $check_phone_query_run = mysqli_query($con, $check_phone_query);

        if (mysqli_num_rows($check_email_query_run) > 0)
            {
                $_SESSION['message'] = "Email already registered!";
                header('Location: ../register.php');
            }

        else
            {
                $allowed_candidate_domain = array("plm.edu.ph"); 

                if (checkIfPLMEmail(strtolower($email), $allowed_candidate_domain, 1))
                {

                    if (mysqli_num_rows($check_phone_query_run) > 0)
                    {

                        $_SESSION['message'] = "Phone number already registered!";
                        header('Location: ../register.php');

                    }
                    else
                    {
                        if($password == $cpassword)
                        {
                            $uppercase = preg_match('@[A-Z]@', $password);
                            $lowercase = preg_match('@[a-z]@', $password);
                            $number = preg_match('@[0-9]@', $password);
                            $specialChars = preg_match('@[^\w]@', $password);
        
                            if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) 
                            {
                                $_SESSION['message'] = "Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character.";
                                header('Location: ../register.php');
                            }
        
                            else
                            {
                                $encrypted_password = md5($password);
                                $verification_code = md5(rand());
                                sendemail_verify("$name","$email","$verification_code");
                                $insert_query = "INSERT INTO users (name, email, phone, verification_code, password) VALUES ('$name', '$email', '$phone', '$verification_code','$encrypted_password')";

                                $insert_query_run = mysqli_query($con,$insert_query);
        
                                if ($insert_query_run)
                                    {
                                        $_SESSION['message'] = "Account registered successfully!";
                                        header('Location: ../verify-landing.php');
                                    }
        
                                else
                                    {
                                        $_SESSION['message'] = "Something went wrong.";
                                        header('Location: ../register.php');
                                    }
                            }
                        }
                        else 
                        {
                            $_SESSION['message'] = "Passwords do not match!";
                            header('Location: ../register.php');
                        }
                    }
                } 

                else 
                {
                    $_SESSION['message'] = "Use your PLM email address to continue registration!";
                    header('Location: ../register.php');
                }

                
                   
            }   
}

else if (isset($_POST['login_btn'])) {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $encrypted_password = md5($password);

    $login_query = "SELECT * FROM users WHERE email = '$email' AND password = '$encrypted_password'";
    $login_query_run = mysqli_query($con, $login_query);

    if (mysqli_num_rows($login_query_run) > 0) {
        // Successful login

        // Check if the user is banned
        $check_ban_query = "SELECT * FROM users WHERE email = '$email' AND login_attempts >= 3 AND UNIX_TIMESTAMP() - last_login_attempt_timestamp < 86400";
        $check_ban_query_run = mysqli_query($con, $check_ban_query);

        if (mysqli_num_rows($check_ban_query_run) > 0) {
            // User is banned
            redirect("../login.php", "Account locked. Please try again after one day.");
        }

        $_SESSION['auth'] = true;

        // Reset login attempts in the database
        $reset_attempts_query = "UPDATE users SET login_attempts = 0, last_login_attempt_timestamp = 0 WHERE email = '$email'";
        mysqli_query($con, $reset_attempts_query);


        $userdata = mysqli_fetch_array($login_query_run) or die(mysqli_error($con));
        $userid = $userdata['id'];
        $username = $userdata['name'];
        $useremail = $userdata['email'];
        $role_as= $userdata['role_as'];

        $_SESSION['auth_user'] = [
            'user_id' => $userid,
            'name' => $username,
            'email' => $useremail,
        ];  

        $_SESSION['role_as'] = $role_as;
        
        if ($role_as == 1) {
            redirect("../admin/approved-orders.php", "Welcome, admin!");
        } else {
            redirect("../index.php", "Logged in successfully!");
        }

    } else {
        // Failed login attempt
        handleFailedLogin($con, $email);
        redirect("../login.php", "Invalid credentials!");
    }
}


// Function to handle failed login attempts

function handleFailedLogin($con, $email) {
    // Check if the user exists
    $check_user_query = "SELECT * FROM users WHERE email = '$email'";
    $check_user_query_run = mysqli_query($con, $check_user_query);

    if (mysqli_num_rows($check_user_query_run) > 0) {
        $user_data = mysqli_fetch_array($check_user_query_run);
        $login_attempts = $user_data['login_attempts'];
        $last_login_attempt_timestamp = $user_data['last_login_attempt_timestamp'];

        // Check if the user is banned
        if ($login_attempts >= 3 && time() - $last_login_attempt_timestamp < 86400) {
            // User is banned for a day
            redirect("../login.php", "Account locked. Please try again after one day.");
        }

        // Update login attempts and timestamp
        $update_attempts_query = "UPDATE users SET login_attempts = login_attempts + 1, last_login_attempt_timestamp = UNIX_TIMESTAMP() WHERE email = '$email'";
        mysqli_query($con, $update_attempts_query);
    }
}

?>