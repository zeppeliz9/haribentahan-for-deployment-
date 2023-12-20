<?php
session_start();
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
                                <h5><?=$_SESSION['status'];?></h5>
                            </div>
                            <?php 
                            unset($_SESSION['status']);
                        }
                    
                    ?>
                    <div class="card" style="color:black; border: solid 3px;">
                        <div class="card-header"; style="background-color: rgb(220,53,69);color:black; border: solid 3px;">
                            <h4 style="text-align:center; font-weight:bold;  color:white; ">Change Password</h4>
                        </div>
                        <div class="card-body" >
                            <form action="password-reset-code.php" method="POST">
                                <div class="mb-3">
                                    <label for="exampleInputEmail1">Enter your Outlook email to change your password. </label>
                                    <br>
                                    <input type="email" name="email" class="form-control"  placeholder="Enter a valid Outlook email address">
                                </div>
                                <button type="submit" name="password_reset_link" style=" background-color:rgb(220,53,69);display: block; width: 100%; margin: 0 auto; color:black;text-align:center;"
                                class="btn">Send Link</button>
                            </form>
                        </div>
                    </div>
                <div>       
            </div>
        </div>
    </div>