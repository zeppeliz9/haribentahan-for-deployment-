<?php // This code is used for button configurations under password reset page
session_start();
include('config/dbcon.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//require 'PHPMailer/src/PHPMailer.php';
//require 'PHPMailer/src/Exception.php';
//require 'PHPMailer/src/SMTP.php';

require 'vendor/autoload.php';
function send_password_reset($get_name,$get_email,$token)
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
        $mail->Username = "";

        //SMTP password
        $mail->Password = "";//

        //Enable TLS encryption;
        $mail->SMTPSecure = "tls";// PHPMailer::ENCRYPTION_STARTTLS;

        //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
        $mail->Port = 587;

        //Recipients
        $mail->setFrom("haribentahan.verification@outlook.com","PLM Haribentahan");

        //Add a recipient
        $mail->addAddress($get_email);

        //Set email format to HTML
        $mail->isHTML(true);

        $mail->Subject = "Haribentahan: Reset Password";
        $email_template = "
            <div style='background-color: #dc3545; padding: 20px; text-align: center;'>
                <h2 style='color: white;'>Change Your Password</h2>
            </div> 
            <div style='background-color: #ffffff; padding: 30px; text-align: center;'>
                <p style='color: #495057; font-size: 16px;'>
                    To successfully change your account password, click the link below:
                </p>
                <a href='https://haribentahan.000webhostapp.com/admin/password-change.php?token=$token&email=$get_email'>
                    <button type='button' style='background-color: #EDAD24; padding: 10px 20px; border: none; border-radius: 5px; color: #ffffff; font-size: 16px;'>
                        <b>Click here to change password</b>
                    </button>
                </a>
            </div>
            <div style='background-color: #f8f9fa; padding: 20px; text-align: center;'>
                <p style='color: #6c757d; font-size: 12px;'>
                    This is an automated message. Do not reply. Labyu
                </p>
            </div>";
        $mail->Body = $email_template;
        
        $mail->send();
}

if(isset($_POST['password_reset_link']))
{
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $token = md5(rand());

    $check_email = "SELECT email, name FROM users WHERE email='$email' LIMIT 1";
    $check_email_run = mysqli_query($con, $check_email);

    if(mysqli_num_rows($check_email_run) > 0)
    {
        $row = mysqli_fetch_array($check_email_run);
        $get_name = $row['name'];
        $get_email = $row['email'];

        $update_token = "UPDATE users SET verification_code='$token' WHERE email='$email'";
        $update_token_run = mysqli_query($con, $update_token);

        if($update_token_run)
        {
            send_password_reset($get_name, $get_email, $token);
            $_SESSION['message'] = "An email has been sent with a link to change your password. Check your email
            for instructions to modify your account password.";
            header("Location: login.php");
            exit(0);  
        }
        else
        {
            $_SESSION['message'] = "Something went wrong.";
            header("Location: password-reset.php");
            exit(0);  
        }
    }
    else
    {
        $_SESSION['message'] = "Email not found.";
        header("Location: password-reset.php");
        exit(0);  
    }
}


if(isset($_POST['password_update']))
{
    $email = mysqli_real_escape_string($con,$_POST['email']);
    $new_password = mysqli_real_escape_string($con,$_POST['new_password']);
    $confirm_password = mysqli_real_escape_string($con,$_POST['confirm_password']);

    $token = mysqli_real_escape_string($con,$_POST['password_token']);

    if(!empty($token))
    {
        if(!empty($email) && !empty($new_password) && !empty($confirm_password))
        {
            // Check if token is valid or not, just in case people want to mess with links

            $check_token = "SELECT verification_code FROM users WHERE verification_code='$token' LIMIT 1";
            $check_token_run = mysqli_query($con, $check_token);

            if (mysqli_num_rows($check_token_run) > 0)
            {
                            $uppercase = preg_match('@[A-Z]@', $new_password);
                            $lowercase = preg_match('@[a-z]@', $new_password);
                            $number = preg_match('@[0-9]@', $new_password);
                            $specialChars = preg_match('@[^\w]@', $new_password);
        
                            if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($new_password) < 8) 
                            {
                                $_SESSION['message'] = "Password should be at least 8 characters in length and 
                                should include at least one upper case letter, one lower case letter, one number,
                                 and one special character.";
                                header("Location: password-change.php?token=$token&email=$email");
                            }
                            else
                            {               
                                if($new_password == $confirm_password)
                                {
                                    // Hash the password before updating
                                    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
                                    $update_password = "UPDATE users SET password='$hashed_password' WHERE verification_code='$token' LIMIT 1";
                                    $update_password_run = mysqli_query($con, $update_password);
                                    
                                    if($update_password_run)
                                    {
                                        //New token every change pass
                                        $new_token = md5(rand());
                                        $update_new_token = "UPDATE users SET verification_code='$new_token' WHERE verification_code='$token' LIMIT 1";
                                        $update_new_token_run = mysqli_query($con, $update_new_token);
                                        
                                        $_SESSION['message'] = "Password successfully updated!";
                                        header("Location: login.php");
                                        exit(); 
                                    }

                                    else
                                    {
                                        $_SESSION['message'] = "Password was not updated. Something went wrong.";
                                        header("Location: password-change.php?token=$token&email=$email");
                                        exit(); 
                                    }
                                }

                                else
                                {
                                    $_SESSION['message'] = "Password and Confirm password don't match!";
                                    header("Location: password-change.php?token=$token&email=$email");
                                    exit(); 
                                }

                            }
                        
            }
            else
            {
                $_SESSION['message'] = "Invalid token";
                header("Location: password-change.php?token=$token&email=$email");
                exit(); 
            }

        }
        else
        {
            $_SESSION['message'] = "All fields are mandatory.";
            header("Location: password-change.php?token=$token&email=$email");
            exit();  
        }
    }
    else
    {
        $_SESSION['message'] = "No token available.";
        header("Location: password-change.php");
        exit();  
    }
}
?>
