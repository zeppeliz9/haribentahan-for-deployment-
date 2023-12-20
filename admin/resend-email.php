<?php
include('includes/header.php');?>
    <div class="py-5">
    <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <?php
                        if(isset($_SESSION['status']))
                        {
                            ?>
                            <div class="alert alert-success">
                                <h5><?=$_SESSION['message'];?></h5>
                            </div>
                            <?php 
                            unset($_SESSION['message']);
                        }
                    
                    ?>
                    <div class="card" style="color:black; border: solid 3px;">
                        <div class="card-header"; style="background-color: rgb(220,53,69);color:black; border: solid 3px;">
                            <h4 style="text-align:center; font-weight:bold;  color:white; ">Resend Verification Email</h4>
                        </div>
                        <div class="card-body" >
                            <form action="resend-code.php" method="POST">
                                <div class="mb-3">
                                    <label for="exampleInputEmail1">Enter your Outlook email to resend the
                                        activation link. </label>
                                    <br>
                                    <input type="email" name="email" class="form-control"  placeholder="Enter a valid Outlook email address">
                                </div>
                                <button type="submit" name="resend_email_btn" class="btn" style="float:right; background-color:rgb(255,193,7);">Resend</button>
                            </form>
                        </div>
                    </div>
                <div>       
            </div>
        </div>
    </div>