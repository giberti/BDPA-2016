<?php

    require __DIR__ . '/setup.php';

    // Create a boolean variable to tell us if the user is logged in or not
    $loggedIn = (isset($_SESSION['Username']) && strlen($_SESSION['Username']) > 0);

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Message Board</title>
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <style>
            .author { font-weight: bold; }
            .timestamp { color: #999999; font-size: 9pt }
        </style>
    </head>
    <body>

        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <div class="navbar-brand">Message Board</div>
                </div>

                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">

                    <!-- authenticated user -->
                    <?php if ($loggedIn) { ?>
                    <ul class="nav navbar-nav navbar-right">
                        <li><a class="logout-button">Logout</a></li>
                    </ul>
                    <?php } ?>

                    <!-- unauthenticated user -->
                    <?php if (!$loggedIn) { ?>
                    <form class="navbar-form navbar-right" onSubmit="return false;">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" class="form-control" placeholder="Username">
                        </div>

                        <div id="loginPasswordGroup" class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" class="form-control" placeholder="Password">
                            <span id="loginPasswordGlyph"  class="glyphicon form-control-feedback" aria-hidden="true"></span>
                        </div>
                        <button type="submit" id="loginUser" class="btn btn-default">Login</button>
                    </form>

                    <ul class="nav navbar-nav navbar-right">
                        <li><a data-toggle="modal" data-target="#registerModal">Register</a></li>
                    </ul>
                    <?php } ?>
                </div>
            </div>
        </nav>

        <div class="container theme-showcase" role="main">

            <?php if (!$loggedIn) { ?>
                <!-- unauthenticated view -->
                <div class="jumbotron">
                    <h1>My Message Board</h1>
                    <p>This message board is only available to registered users, please create an account to get started.</p>
                    <p><a class="btn btn-primary btn-lg" data-toggle="modal" data-target="#registerModal" role="button">Register</a></p>
                </div>
            <?php } ?>

            <?php if ($loggedIn) { ?>
                <!-- authenticated view -->
                <div class="page-header">
                    <h1>Message Board</h1>
                </div>

                <div class="row">
                    <div class="col-sm-8">
                        <h2>Recent Messages</h2>
                    </div>

                    <div class="col-sm-4">
                        <h2>Recently Active Users</h2>
                    </div>
                </div>
            <?php } ?>
        </div>


        <?php
        if (!$loggedIn) {
            include __DIR__ . '/includes/register.php';
        }
        ?>

        <script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script>


            /**
             * Log in a user and refresh the page
             */
            function login(username, password, errorHandler) {
                if (errorHandler === undefined) {
                    errorHandler = function() { alert('Unable to log user in'); }
                }
                $.ajax({
                    accepts:'application/json',
                    cache:false,
                    data: {method: 'login', username: username, password: password},
                    dataType: "json",
                    error: errorHandler,
                    method: "post",
                    success: function() {
                        location.reload(true);
                    },
                    url:'api/user.php'
                });

            }

            /**
             * Attempt to log in the current user
             */
            function loginUser(el) {
                var form = el.target.form;
                var username = $(form.username).val();
                var password = $(form.password).val();
                $(form.password).val('');
                login(username, password, function(){
                    $("#loginPasswordGroup").addClass('has-error has-feedback');
                    $('#loginPasswordGlyph').addClass('glyphicon-remove').removeClass('glyphicon-ok');
                    $(form.password).focus();
                });
            }

            /**
             * Log out the current user and refresh the page
             */
            function logoutUser() {
                $.ajax({
                    accepts:'application/json',
                    cache:false,
                    data: {method: 'logout'},
                    dataType: "json",
                    error: function() {
                        alert('Unable to logout');
                    },
                    method: "post",
                    success: function() {
                        location.reload(true);
                    },
                    url:'api/user.php'
                });
            }

            // Setup any event handlers etc
            $(document).ready(function(){
                // Handle submitting the login form
                $('#loginUser').on("click", loginUser);

                // Handle logging out the user
                $(".logout-button").on('click', logoutUser);

            });

        </script>
    </body>
</html>
