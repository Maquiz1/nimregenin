<?php
require_once 'php/core/init.php';
$user = new User();
$override = new OverideData();
$usr = null;
$email = new Email();
$st = null;
$random = new Random();
$pageError = null;
$successMessage = null;
$errorM = false;
$errorMessage = null;
if (!$user->isLoggedIn()) {
    if (Input::exists('post')) {
        if (Token::check(Input::get('token'))) {
            $validate = new validate();
            $validate = $validate->check($_POST, array(
                'username' => array('required' => true),
                'password' => array('required' => true)
            ));
            if ($validate->passed()) {
                $st = $override->get('user', 'username', Input::get('username'));
                if ($st) {
                    if ($st[0]['count'] > 3) {
                        $errorMessage = 'You Account have been deactivated,Someone was trying to access it with wrong credentials. Please contact your system administrator';
                    } else {
                        $login = $user->loginUser(Input::get('username'), Input::get('password'), 'user');
                        if ($login) {
                            $lastLogin = $override->get('user', 'id', $user->data()->id);
                            if ($lastLogin[0]['last_login'] == date('Y-m-d')) {
                            } else {
                                try {
                                    $user->updateRecord('user', array(
                                        'last_login' => date('Y-m-d H:i:s'),
                                        'count' => 0,
                                    ), $user->data()->id);
                                } catch (Exception $e) {
                                }
                            }
                            try {
                                $user->updateRecord('user', array(
                                    'count' => 0,
                                ), $user->data()->id);
                            } catch (Exception $e) {
                            }

                            Redirect::to('index1.php');
                        } else {
                            $usr = $override->get('user', 'username', Input::get('username'));
                            if ($usr && $usr[0]['count'] < 3) {
                                try {
                                    $user->updateRecord('user', array(
                                        'count' => $usr[0]['count'] + 1,
                                    ), $usr[0]['id']);
                                } catch (Exception $e) {
                                }
                                $errorMessage = 'Wrong username or password';
                            } else {
                                try {
                                    $user->updateRecord('user', array(
                                        'count' => $usr[0]['count'] + 1,
                                    ), $usr[0]['id']);
                                } catch (Exception $e) {
                                }
                                $email->deactivation($usr[0]['email_address'], $usr[0]['lastname'], 'Account Deactivated');
                                $errorMessage = 'You Account have been deactivated,Someone was trying to access it with wrong credentials. Please contact your system administrator';
                            }
                        }
                    }
                } else {
                    $errorMessage = 'Invalid username, Please check your credentials and try again';
                }
            } else {
                $pageError = $validate->errors();
            }
        }
    }
} else {
    Redirect::to('index1.php');
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nimregenin Database | Log in</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
</head>

<?php if ($errorMessage) { ?>
    <div class="alert alert-danger">
        <h4>Error!</h4>
        <?= $errorMessage ?>
    </div>
<?php } elseif ($pageError) { ?>
    <div class="alert alert-danger">
        <h4>Error!</h4>
        <?php foreach ($pageError as $error) {
            echo $error . ' , ';
        } ?>
    </div>
<?php } elseif ($successMessage) { ?>
    <div class="alert alert-success">
        <h4>Success!</h4>
        <?= $successMessage ?>
    </div>
<?php } ?>

<body class="hold-transition login-page">

    <div class="login-box">
        <div class="login-logo">
            <a href="#"><b>Nimregenin</b>Database</a>
        </div>
        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Sign in to start your session</p>

                <form method="post">
                    <div class="input-group mb-3">
                        <input type="text" name="username" id="username" placeholder="Username" class="form-control validate[required]" />
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" name="password" id="password" placeholder="Password" class="form-control validate[required]" />
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <input type="hidden" name="token" value="<?= Token::generate(); ?>">
                            <input type="submit" value="Sign in" class="btn btn-primary btn-block">
                        </div>
                        <!-- /.col -->
                    </div>
                </form>
            </div>
            <!-- /.login-card-body -->
        </div>
    </div>
    <!-- /.login-box -->

    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.min.js"></script>
</body>

</html>