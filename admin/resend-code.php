<?php
include('config/dbcon.php');
include('functions/myfunctions.php');
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//require 'PHPMailer/src/PHPMailer.php';
//require 'PHPMailer/src/Exception.php';
//require 'PHPMailer/src/SMTP.php';

require 'vendor/autoload.php';

function resend_email_verify($name,$email,$verification_code)
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

    $mail->Subject = "Haribentahan: Resend Email Verification";
    $email_template = "<div style='text-align: center; color:yellow'><h3>Verify your email</h3><br></div> 
    <div style='color:black; font-size: 12px;text-align: center;'>To enjoy Haribentahan at its finest, 
    verify your Haribentahan account by clicking the link below: <br><br>
    <a href='https://haribentahan.000webhostapp.com/admin/email-verification.php?token=$verification_code'>
    <b>Click here to verify</b></a></div><div style='font-size:10px;'><br><br><i>This is an automated message. 
    Do not reply. Labyu</i></div>";
    $mail->Body = $email_template;
    
    $mail->send();

}

if(!isset($POST['resend_email_btn'])) 
{ 
    if(!empty(trim($_POST['email'])))
    {
        $email = mysqli_real_escape_string($con, $_POST['email']);
        $allowed_candidate_domain = array("plm.edu.ph"); 
        if (checkIfPLMEmail(strtolower($email), $allowed_candidate_domain, 1))
        {
        $checkemail_query = "SELECT * FROM users WHERE email='$email' LIMIT 1";
        $checkemail_query_run = mysqli_query($con,$checkemail_query);

            if(mysqli_num_rows($checkemail_query_run) > 0)
            {
                $row = mysqli_fetch_array($checkemail_query_run);

                if($row['verify_status'] == "0")
                {
                    $name = $row['name'];
                    $email = $row['email'];
                    $verification_code = $row['verification_code'];
                    resend_email_verify($name,$email,$verification_code);
                    $_SESSION['message'] = "Activation link has been sent to your email.";
                    header("Location: login.php");
                    exit(0);
                }
                else
                {
                    $_SESSION['message'] = "Account is already activated. Please log in.";
                    header("Location: login.php");
                    exit(0);   
                }
        
            }
            else
            {
                $_SESSION['message'] = "Account is not yet registered Please register first.";
                header("Location: register.php");
                exit(0);
            }
        }
        
        else 
        {
            $_SESSION['message'] = "Use your PLM email address to continue registration!";
            header('Location: ../register.php');
            exit(0);
        }

    }
    else
    {
        $_SESSION['message'] = "Please enter a valid email.";
        header("Location: resend-email.php");
        exit(0);
    }
} 

?>