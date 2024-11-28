<?php
session_start();
if (isset($_SESSION['user_id']) and isset($_SESSION['user_name']) and isset($_SESSION['user_pass'])) {
    header('Location: index');
}
?>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="light" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable"><head>
    <head>
        <meta charset="utf-8">
        <title>Login To Dashboard !</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css">
        <link href="css/app.min.css" rel="stylesheet" type="text/css">
        <link href="css/custom.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    </head>
    <body class="auth-bg 100-vh">
        <div class="bg-overlay bg-light"></div>
        <div class="account-pages">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-11">
                        <div class="auth-full-page-content d-flex min-vh-100 py-sm-5 py-4">
                            <div class="w-100">
                                <div class="d-flex flex-column h-100 py-0 py-xl-4">
                                    <div class="card my-auto overflow-hidden">
                                        <div class="row g-0">
                                            <div class="col-12">
                                                <div class="p-lg-5 p-4">
                                                    <div class="text-center">
                                                        <h4 class="mb-0">Welcome Back !</h4>
                                                        <p class="text-muted mt-2">কি অবস্থা মনা !</p>
                                                    </div>
                                                    <div class="mt-4">
                                                        <form method="POST" class="auth-input">
                                                            <div class="alert alert-warning err" style="display:none;" role="alert"></div>
                                                            <div class="mb-3">
                                                                <label for="useremail" class="form-label">Email</label>
                                                                <input type="email" id="useremail" class="form-control" placeholder="Enter Email">
                                                            </div>
    
                                                            <div class="mb-2">
                                                                <label for="userpassword" class="form-label">Password</label>
                                                                <input type="password" id="userpassword" class="form-control pe-5" placeholder="Enter Password">
                                                            </div>
    
                                                            <div class="form-check form-check-primary fs-16 py-2">
                                                                <input class="form-check-input" type="checkbox" id="remember-check">
                                                                <label class="form-check-label fs-14" for="remember-check">Remember Me</label>
                                                            </div>
    
                                                            <div class="mt-2">
                                                                <button class="btn btn-primary text-white w-100" id="userlogin" type="button">Log In</button>
                                                            </div>
                                                        </form>
                                                        <div class="mt-4 text-center">
                                                            <p class="mb-0">Don't have an account ? <a href="register" class="fw-medium text-primary text-decoration-underline"> Register Now </a> </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
    
                                        </div>
                                    </div>
                                    <!-- end card -->
    
                                    <div class="mt-5 text-center">
                                        <p class="mb-0 text-muted">©2024 Stepup Technology Ltd</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
    </body>
</html>
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script>
    $(document).ready(function(){
        $("#userlogin").click(function(){
            var useremail    = $("#useremail").val();
            var userpassword = $("#userpassword").val();
            $.ajax({
                url: 'query/loginUser',
                method: 'POST',
                data: {"useremail": useremail, "userpassword":userpassword},
                success: function(data) {
                    if (data == "success") {
                        window.location.href = "index";
                    } else {
                        $(".err").fadeIn().delay(2000).fadeOut(2000).html(data);
                    }
                }
            });
        });
    });
</script>