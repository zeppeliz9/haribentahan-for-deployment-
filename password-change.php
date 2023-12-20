<?php

session_start();

include('includes/header.php');?>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <?php
                        if(isset($_SESSION['message']))
                        {
                            ?>
                            <div class="alert alert-danger" style="padding:5px;">
                                <b><p style="font-size:14px; margin-top:0px; margin-bottom:0px;"><?=$_SESSION['message'];?></b></p>
                            </div>
                            <?php 
                            unset($_SESSION['message']);
                        }
                    ?>

                    <div class="card mt-0" style="border: solid 2px">
                        <div class="card-header" style="color: black; border: solid 3px;background-color: rgb(220,53,69);">
                            <h4 class="text-center mb-0" style="font-weight:bold; color:white;">Change Password</h4>
                        </div>

                        <div class="card-body">
                            <form action="password-reset-code.php" method="POST">
                                <input type="hidden" name="password_token" value="<?php if (isset($_GET['token'])) {
                                    echo $_GET['token'];
                                } ?>">
                                <div class="mb-3">
                                    <label for="exampleInputEmail1">PLM Email address</label>
                                    <input type="email" name="email" value="<?php if (isset($_GET['email'])) {
                                        echo $_GET['email'];
                                    } ?>" class="form-control" placeholder="Enter your PLM email address" readonly>
                                </div>

                                <div class="mb-3">
                                    <label for="password" style="margin-top:-15px;">Password</label>
                                    <input type="password" name="new_password" class="form-control" id="new_password" placeholder="Choose a strong password">
                                    <i class="bi bi-eye-slash" id="togglePassword"></i>
                                    <span style="font-size:12px;"> Show password </span>
                                </div>

                                <div class="mb-3">
                                    <label for="">Confirm Password</label>
                                    <input type="password" name="confirm_password" class="form-control" id="confirm_password" placeholder="Confirm your password">
                                    <i class="bi bi-eye-slash" id="toggleCheckPassword"></i>
                                    <span style="font-size:12px;"> Show password </span>
                                </div>

                                <button type="submit" name="password_update" class="btn" style="display: block; width: 100%; margin: 0 auto; background-color: rgb(220,53,69); color:white">Change Password</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    

<script>
    const passwordInput = document.querySelector("#new_password");
    const checkPasswordInput = document.querySelector("#confirm_password");
    const togglePassword = document.querySelector("#togglePassword");
    const toggleCheckPassword = document.querySelector("#toggleCheckPassword");

    togglePassword.addEventListener("click", togglePasswordVisibility);
    toggleCheckPassword.addEventListener("click", toggleCheckPasswordVisibility);

    function togglePasswordVisibility() {
        const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
        passwordInput.setAttribute("type", type);

        togglePassword.classList.toggle("bi-eye");
        togglePassword.classList.toggle("bi-eye-slash");
    }

    function toggleCheckPasswordVisibility() {
        const type = checkPasswordInput.getAttribute("type") === "password" ? "text" : "password";
        checkPasswordInput.setAttribute("type", type);

        toggleCheckPassword.classList.toggle("bi-eye");
        toggleCheckPassword.classList.toggle("bi-eye-slash");
    }
</script>


<?php include('includes/footer.php');?>