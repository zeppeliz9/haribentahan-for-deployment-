<?php
//for the place order button in checkout.php
session_start();
include('../config/dbcon.php');

if(isset($_SESSION['auth'])) //If user is authenticated, then
{
    if(isset($_POST['placeOrderBtn']))
    {
        $name = mysqli_real_escape_string($con, $_POST['name']);
        $email = mysqli_real_escape_string($con, $_POST['email']);
        $phone = mysqli_real_escape_string($con, $_POST['phone']);
        $student_no = mysqli_real_escape_string($con, $_POST['pincode']);
        $notes = mysqli_real_escape_string($con, $_POST['address']);
        $payment_mode =  mysqli_real_escape_string($con, $_POST['payment_mode']);


        if($name == "" || $email == "" || $phone == "" || $student_no == "" || $notes == "") //should not be empty
        {
            $_SESSION['message'] = "All fields must be filled up!";
            header('Location: ../checkout.php');
            exit(0);
        }

        else
        {
            $userId = $_SESSION['auth_user']['user_id'];
            $vquery = "SELECT verify_status FROM users WHERE id = $userId LIMIT 1";
            $vquery_run = mysqli_query($con,$vquery);
            
            if ($vquery_run) 
            {
                // Fetch the result as an associative array
                $userVerification = mysqli_fetch_array($vquery_run);
                // Check the verify_status
                $verify_status = $userVerification['verify_status']; 

                if ($verify_status == 1) 
                {
                    // User is verified, proceed with the checkout process
                    $query = "SELECT c.id as cid, c.prod_id, c.prod_qty, p.id as pid, p.name, p.image, p.selling_price  
                    FROM carts c, products p WHERE c.prod_id=p.id AND c.user_id='$userId' ORDER BY c.id DESC ";
                    $query_run = mysqli_query($con,$query); 
                    $totalPrice = 0;

                    foreach($query_run as $citem)
                    {   
                        $totalPrice += $citem['selling_price'] * $citem['prod_qty'];
                    }

                    $tracking_no = "haribenta".rand(1111,9999).substr($phone,2);
                    $user_id = $_SESSION['auth_user']['user_id'];
                    $insert_query = "INSERT INTO orders (tracking_no,user_id,name,email,phone,notes,student_no,total_price,payment_mode) VALUES ('$tracking_no','$user_id','$name','$email','$phone','$notes','$student_no','$totalPrice','$payment_mode')";
                    $insert_query_run = mysqli_query($con, $insert_query);

                    if ($insert_query_run)
                    {
                        $order_id = mysqli_insert_id($con);

                        foreach($query_run as $citem)
                        {  
                            $prod_id = $citem['prod_id'];
                            $prod_qty = $citem['prod_qty'];
                            $price = $citem['selling_price'];

                            $insert_items_query = "INSERT INTO order_items (order_id, prod_id, qty, price) VALUES ('$order_id','$prod_id','$prod_qty','$price')";
                            $insert_items_query_run = mysqli_query($con, $insert_items_query);
                            $product_query = "SELECT * FROM products WHERE id='$prod_id' LIMIT 1";
                            $product_query_run = mysqli_query($con,$product_query);
                            $productData = mysqli_fetch_array($product_query_run);
                            $current_qty = $productData['qty'];
                            $new_qty = $current_qty - $prod_qty;
                            $updateQty_query = "UPDATE products SET qty='$new_qty' WHERE id='$prod_id' ";
                            $updateQty_query_run = mysqli_query($con,$updateQty_query);
                        } 

                        $deleteCartQuery = "DELETE FROM carts WHERE user_id= '$userId'";
                        $deleteCartQuery_run = mysqli_query($con, $deleteCartQuery);

                        $_SESSION['message'] = "Order Placed Successfully";
                        header('Location: ../my-orders.php');
                        die();
                    }

                }

                else 
                {
                    $_SESSION['message'] = 'Your account is not verified. Please verify your email first.';
                    header("Location: ../index.php");
                    exit();
                }
            }

            else
            {
                header('Location: ../index.php');
            }

        }
        
    }
    else 
    {
        echo "Error in the query: " . mysqli_error($con);
    } 
} 



?>