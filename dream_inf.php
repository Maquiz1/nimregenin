<?php
require_once 'php/core/init.php';

$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();
$validate = new validate();

$successMessage = null;
$pageError = null;
$errorMessage = null;
$numRec = 5;
if ($user->isLoggedIn()) {
    if (Input::exists('post')) {
        $validate = new validate();

        if (Input::get('reset_pass')) {
            $salt = $random->get_rand_alphanumeric(32);
            $password = '12345678';
            $user->updateRecord('user', array(
                'password' => Hash::make($password, $salt),
                'salt' => $salt,
            ), Input::get('id'));
            $successMessage = 'Password Reset Successful';
        } elseif (Input::get('change_pass')) {
            $salt = $random->get_rand_alphanumeric(32);
            $password = Input::get('password');
            $user->updateRecord('user', array(
                'password' => Hash::make($password, $salt),
                'salt' => $salt,
            ), Input::get('id'));
            $successMessage = 'Password Changed Successful';
        } elseif (Input::get('lock_account')) {
            $user->updateRecord('user', array(
                'count' => 4,
            ), Input::get('id'));
            $successMessage = 'Account locked Successful';
        } elseif (Input::get('unlock_account')) {
            $user->updateRecord('user', array(
                'count' => 0,
            ), Input::get('id'));
            $successMessage = 'Account Unlock Successful';
        } elseif (Input::get('delete_staff')) {
            $user->updateRecord('user', array(
                'status' => 0,
            ), Input::get('id'));
            $successMessage = 'User Restored Successful';
        } elseif (Input::get('delete_sites')) {
            $user->updateRecord('sites', array(
                'status' => 0,
            ), Input::get('id'));
            $successMessage = 'Site Deleted Successful';
        } elseif (Input::get('delete_positions')) {
            $user->updateRecord('position', array(
                'status' => 0,
            ), Input::get('id'));
            $successMessage = 'Position Deleted Successful';
        } elseif (Input::get('delete_facility')) {
            $user->updateRecord('sites', array(
                'status' => 0,
            ), Input::get('id'));
            $successMessage = 'Site Deleted Successful';
        } elseif (Input::get('restore_facility')) {
            $user->updateRecord('sites', array(
                'status' => 1,
            ), Input::get('id'));
            $successMessage = 'Facility Restored Successful';
        } elseif (Input::get('restore_staff')) {
            $user->updateRecord('user', array(
                'status' => 1,
            ), Input::get('id'));
            $successMessage = 'User Deleted Successful';
        } elseif (Input::get('add_visit')) {
            $validate = $validate->check($_POST, array(
                'visit_date' => array(
                    'required' => true,
                ),
                'visit_status' => array(
                    'required' => true,
                ),
            ));

            if ($validate->passed()) {
                $user->updateRecord('visit', array(
                    'visit_date' => Input::get('visit_date'),
                    'visit_status' => Input::get('visit_status'),
                    'comments' => Input::get('comments'),
                    'patient_id' => Input::get('cid'),
                    'update_on' => date('Y-m-d H:i:s'),
                    'update_id' => $user->data()->id,
                ), Input::get('id'));

                $successMessage = 'Visit Updates  Successful';
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('search_by_site')) {

            $validate = $validate->check($_POST, array(
                'facility_id' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                if (Input::get('facility_id')) {
                    $url = 'info.php?id=' . $_GET['id'] . '&status=' . $_GET['status'] . '&facility_id=' . Input::get('facility_id');
                } else {
                    $url = 'info.php?id=' . $_GET['id'] . '&status=' . $_GET['status'];
                }
                Redirect::to($url);
                $pageError = $validate->errors();
            }
        } elseif (Input::get('clear_data')) {

            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    if (Input::get('name')) {
                        if (Input::get('name') == 'user' || Input::get('name') == 'sites' || Input::get('name') == 'position' || Input::get('name') == 'district') {
                            $errorMessage = 'Table ' . '"' . Input::get('name') . '"' . '  can not be Cleared';
                        } else {
                            $clearData = $override->clearDataTable(Input::get('name'));
                            $successMessage = 'Table ' . '"' . Input::get('name') . '"' . ' Cleared Successfull';
                        }
                    } else {
                        $errorMessage = 'Table ' . '"' . Input::get('name') . '"' . '  can not be Found!';
                    }
                    // die;
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('setSiteId')) {

            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $setSiteId = $override->setSiteId('visit', 'site_id', Input::get('name'), 1);
                    $successMessage = 'Site ID Successfull';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('unset_study_id')) {
            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    if (Input::get('name') == 'study_id') {
                        $study_id = $override->getData('study_id');
                        foreach ($study_id as $row) {
                            $user->updateRecord('study_id', array(
                                'client_id' => 0,
                                'status' => 0,
                            ), $row['id']);
                        }
                    }
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('invite')) {
            $staff = $override->get('user', 'id', Input::get('id'))[0];
            $subject = 'Nanopore WhatsApp Invite';
            $link = 'https://chat.whatsapp.com/KbhgEsRCew40x5ZHbcac3y';
            try {
                $email->userInvite($staff['email_address'], $staff['lastname'], $subject, $link);
                $successMessage = 'Email Sent Successful';
            } catch (Exception $e) {
                $e->getMessage();
            }
        } else if (Input::get('delete_record')) {
            $user->updateRecord('screening', array(
                'status' => 0,
            ), Input::get('id'));
            $successMessage = 'Recored Deleted Successful';
        } else if (Input::get('restore_record')) {
            $user->updateRecord('screening', array(
                'status' => 1,
            ), Input::get('id'));
            $successMessage = 'Recored Restored Successful';
        }
    }
} else {
    Redirect::to('index.php');
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dream Fund Sub-Studies Database | Info</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <?php include 'navbar.php'; ?>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <?php include 'sidemenu.php'; ?>

        <?php if ($errorMessage) { ?>
            <div class="alert alert-danger text-center">
                <h4>Error!</h4>
                <?= $errorMessage ?>
            </div>
        <?php } elseif ($pageError) { ?>
            <div class="alert alert-danger text-center">
                <h4>Error!</h4>
                <?php foreach ($pageError as $error) {
                    echo $error . ' , ';
                } ?>
            </div>
        <?php } elseif ($successMessage) { ?>
            <div class="alert alert-success text-center">
                <h4>Success!</h4>
                <?= $successMessage ?>
            </div>
        <?php } ?>


        <?php if ($_GET['id'] == 1) { ?>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>
                                    <?php
                                    $pagNum = 0;
                                    if ($_GET['status'] == 1) {
                                        $pagNum = $override->getCount('user', 'status', 1);
                                    } else if ($_GET['status'] == 2) {
                                        $pagNum = $override->getCount('user', 'status', 0);
                                    } else if ($_GET['status'] == 3) {
                                        $pagNum = $override->getCount1('user', 'status', 1, 'count', 4);
                                    } else if ($_GET['status'] == 4) {
                                        $pagNum = $override->getCount1('user', 'status', 0, 'count', 4);
                                    } else {
                                        $pagNum = $override->getNo('user');
                                    }


                                    $pages = ceil($pagNum / $numRec);
                                    if (!$_GET['page'] || $_GET['page'] == 1) {
                                        $page = 0;
                                    } else {
                                        $page = ($_GET['page'] * $numRec) - $numRec;
                                    }


                                    if ($_GET['status'] == 1) {
                                        $data = $override->getWithLimit('user', 'status', 1, $page, $numRec);
                                    } else if ($_GET['status'] == 2) {
                                        $data = $override->getWithLimit('user', 'status', 0, $page, $numRec);
                                    } else if ($_GET['status'] == 3) {
                                        $data = $override->getWithLimit1('user', 'status', 1, 'count', 4, $page, $numRec);
                                    } else if ($_GET['status'] == 4) {
                                        $data = $override->getWithLimit1('user', 'status', 0, 'count', 4, $page, $numRec);
                                    } else {
                                        $data = $override->getWithLimit0('user', $page, $numRec);
                                    }

                                    $total_user = $override->getCount('user', 'status', 1);


                                    ?>
                                    List of Staff ( <?= $total_user; ?> )
                                </h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="index1.php">Home</a></li>
                                    <li class="breadcrumb-item active">List of Staff</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <section class="content-header">
                                        <div class="container-fluid">
                                            <div class="row mb-2">
                                                <div class="col-sm-6">
                                                    <div class="card-header">
                                                        List of Staff
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <ol class="breadcrumb float-sm-right">
                                                        <li class="breadcrumb-item">
                                                            <a href="index1.php">
                                                                < Back</a>
                                                        </li>
                                                        &nbsp;
                                                        <li class="breadcrumb-item">
                                                            <a href="index1.php">
                                                                Go Home > </a>
                                                        </li>
                                                    </ol>
                                                </div>
                                            </div>
                                            <hr>
                                        </div><!-- /.container-fluid -->
                                    </section>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>username</th>
                                                    <th>Position</th>
                                                    <th>Access Level</th>
                                                    <th>Sex</th>
                                                    <th>Site</th>
                                                    <th>Status</th>
                                                    <th class="text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $x = 1;
                                                foreach ($data as $staff) {
                                                    // $position = $override->getNews('position', 'status', 1, 'id', $staff['accessLevel'])[0];
                                                    $position = $override->getNews('position', 'status', 1, 'id', $staff['position'])[0];
                                                    $sites = $override->getNews('sites', 'status', 1, 'id', $staff['site_id'])[0];

                                                    ?>
                                                    <tr>
                                                        <td class="table-user">
                                                            <?= $staff['firstname'] . '  ' . $staff['middlename'] . ' ' . $staff['lastname']; ?>
                                                        </td>
                                                        <td class="table-user">
                                                            <?= $staff['username']; ?>
                                                        </td>
                                                        <td class="table-user">
                                                            <?= $position['name']; ?>
                                                        </td>
                                                        <td class="table-user">
                                                            <?= $staff['accessLevel']; ?>
                                                        </td>
                                                        <?php if ($staff['sex'] == 1) { ?>
                                                            <td class="table-user">
                                                                Male
                                                            </td>
                                                        <?php } elseif ($staff['sex'] == 2) { ?>
                                                            <td class="table-user">
                                                                Female
                                                            </td>
                                                        <?php } else { ?>
                                                            <td class="table-user">
                                                                Not Available
                                                            </td>
                                                        <?php } ?>

                                                        <td class="table-user">
                                                            <?= $sites['name']; ?>
                                                        </td>
                                                        <?php if ($staff['count'] < 4) { ?>
                                                            <?php if ($staff['status'] == 1) { ?>
                                                                <td class="text-center">
                                                                    <a href="#" class="btn btn-success">
                                                                        <i class="ri-edit-box-line">
                                                                        </i>Active
                                                                    </a>
                                                                </td>
                                                            <?php } else { ?>
                                                                <td class="text-center">
                                                                    <a href="#" class="btn btn-danger">
                                                                        <i class="ri-edit-box-line">
                                                                        </i>Not Active
                                                                    </a>
                                                                </td>
                                                            <?php } ?>

                                                        <?php } else { ?>
                                                            <td class="text-center">
                                                                <a href="#" class="btn btn-warning"> <i
                                                                        class="ri-edit-box-line"></i>Locked</a>
                                                            </td>
                                                        <?php } ?>
                                                        <td>
                                                            <a href="add.php?id=1&staff_id=<?= $staff['id'] ?>"
                                                                class="btn btn-info">Update</a>
                                                            <a href="#reset<?= $staff['id'] ?>" role="button"
                                                                class="btn btn-default" data-toggle="modal">Reset</a>
                                                            <a href="#change<?= $staff['id'] ?>" role="button"
                                                                class="btn btn-success" data-toggle="modal">Change</a>
                                                            <a href="#lock<?= $staff['id'] ?>" role="button"
                                                                class="btn btn-warning" data-toggle="modal">Lock</a>
                                                            <a href="#unlock<?= $staff['id'] ?>" role="button"
                                                                class="btn btn-primary" data-toggle="modal">Unlock</a>
                                                            <a href="#delete<?= $staff['id'] ?>" role="button"
                                                                class="btn btn-danger" data-toggle="modal">Delete</a>
                                                            <a href="#restore<?= $staff['id'] ?>" role="button"
                                                                class="btn btn-secondary"
                                                                data-toggle="modal">Restore</a>&nbsp;&nbsp;&nbsp;
                                                            <a href="profile.php?id=<?= $staff['id'] ?>&status=<?= $_GET['status'] ?>"
                                                                class="btn btn-success">Change Password</a>
                                                            <a href="#invite<?= $staff['id'] ?>" role="button"
                                                                class="btn btn-orrange"
                                                                data-toggle="modal">Invite</a>&nbsp;&nbsp;&nbsp;
                                                        </td>
                                                    </tr>
                                                    <div class="modal fade" id="reset<?= $staff['id'] ?>" tabindex="-1"
                                                        role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <form method="post">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <button type="button" class="close"
                                                                            data-dismiss="modal"><span
                                                                                aria-hidden="true">&times;</span><span
                                                                                class="sr-only">Close</span></button>
                                                                        <h4>Reset Password</h4>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <p>Are you sure you want to reset password to default
                                                                            (12345678)</p>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <input type="hidden" name="id"
                                                                            value="<?= $staff['id'] ?>">
                                                                        <input type="submit" name="reset_pass" value="Reset"
                                                                            class="btn btn-warning">
                                                                        <button class="btn btn-default" data-dismiss="modal"
                                                                            aria-hidden="true">Close</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <div class="modal fade" id="change<?= $staff['id'] ?>" tabindex="-1"
                                                        role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <form method="post">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <button type="button" class="close"
                                                                            data-dismiss="modal"><span
                                                                                aria-hidden="true">&times;</span><span
                                                                                class="sr-only">Close</span></button>
                                                                        <h4>Change Password</h4>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <input type="text" name="password" value="">
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <input type="hidden" name="id"
                                                                            value="<?= $staff['id'] ?>">
                                                                        <input type="submit" name="change_pass" value="Change"
                                                                            class="btn btn-warning">
                                                                        <button class="btn btn-default" data-dismiss="modal"
                                                                            aria-hidden="true">Close</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <div class="modal fade" id="lock<?= $staff['id'] ?>" tabindex="-1"
                                                        role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <form method="post">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <button type="button" class="close"
                                                                            data-dismiss="modal"><span
                                                                                aria-hidden="true">&times;</span><span
                                                                                class="sr-only">Close</span></button>
                                                                        <h4>Lock Account</h4>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <p>Are you sure you want to lock this account </p>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <input type="hidden" name="id"
                                                                            value="<?= $staff['id'] ?>">
                                                                        <input type="submit" name="lock_account" value="Lock"
                                                                            class="btn btn-warning">
                                                                        <button class="btn btn-default" data-dismiss="modal"
                                                                            aria-hidden="true">Close</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <div class="modal fade" id="unlock<?= $staff['id'] ?>" tabindex="-1"
                                                        role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <form method="post">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <button type="button" class="close"
                                                                            data-dismiss="modal"><span
                                                                                aria-hidden="true">&times;</span><span
                                                                                class="sr-only">Close</span></button>
                                                                        <h4>Unlock Account</h4>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <strong style="font-weight: bold;color: red">
                                                                            <p>Are you sure you want to unlock this account </p>
                                                                        </strong>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <input type="hidden" name="id"
                                                                            value="<?= $staff['id'] ?>">
                                                                        <input type="submit" name="unlock_account"
                                                                            value="Unlock" class="btn btn-success">
                                                                        <button class="btn btn-default" data-dismiss="modal"
                                                                            aria-hidden="true">Close</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <div class="modal fade" id="delete<?= $staff['id'] ?>" tabindex="-1"
                                                        role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <form method="post">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <button type="button" class="close"
                                                                            data-dismiss="modal"><span
                                                                                aria-hidden="true">&times;</span><span
                                                                                class="sr-only">Close</span></button>
                                                                        <h4>Delete User</h4>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <strong style="font-weight: bold;color: red">
                                                                            <p>Are you sure you want to delete this user</p>
                                                                        </strong>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <input type="hidden" name="id"
                                                                            value="<?= $staff['id'] ?>">
                                                                        <input type="submit" name="delete_staff" value="Delete"
                                                                            class="btn btn-danger">
                                                                        <button class="btn btn-default" data-dismiss="modal"
                                                                            aria-hidden="true">Close</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <div class="modal fade" id="restore<?= $staff['id'] ?>" tabindex="-1"
                                                        role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <form method="post">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <button type="button" class="close"
                                                                            data-dismiss="modal"><span
                                                                                aria-hidden="true">&times;</span><span
                                                                                class="sr-only">Close</span></button>
                                                                        <h4>Restore User</h4>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <strong style="font-weight: bold;color: green">
                                                                            <p>Are you sure you want to restore this user</p>
                                                                        </strong>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <input type="hidden" name="id"
                                                                            value="<?= $staff['id'] ?>">
                                                                        <input type="submit" name="restore_staff"
                                                                            value="Restore" class="btn btn-success">
                                                                        <button class="btn btn-default" data-dismiss="modal"
                                                                            aria-hidden="true">Close</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <div class="modal fade" id="invite<?= $staff['id'] ?>" tabindex="-1"
                                                        role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <form method="post">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <button type="button" class="close"
                                                                            data-dismiss="modal"><span
                                                                                aria-hidden="true">&times;</span><span
                                                                                class="sr-only">Close</span></button>
                                                                        <h4>Invite</h4>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <p>Are you sure you want to Invite this user?
                                                                        </p>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <input type="hidden" name="id"
                                                                            value="<?= $staff['id'] ?>">
                                                                        <input type="submit" name="invite" value="Invite"
                                                                            class="btn btn-warning">
                                                                        <button class="btn btn-default" data-dismiss="modal"
                                                                            aria-hidden="true">Close</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <?php $x++;
                                                } ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>username</th>
                                                    <th>Position</th>
                                                    <th>Access Level</th>
                                                    <th>Sex</th>
                                                    <th>Site</th>
                                                    <th>Status</th>
                                                    <th class="text-center">Action</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <!-- /.card-body -->
                                    <?php
                                    $currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
                                    $currentSite = $_GET['facility_id'];
                                    // $pages = 10; // Total number of pages (replace with your actual calculation)
                                    $range = 2; // Number of pages to show before and after the current page
                                
                                    // Calculate start and end for the visible range
                                    $start = max(1, $currentPage - $range);
                                    $end = min($pages, $currentPage + $range);
                                    ?>
                                    <div class="card-footer clearfix">
                                        <ul class="pagination pagination-sm m-0 float-right">
                                            <!-- Previous Page -->
                                            <li class="page-item <?php echo ($currentPage <= 1) ? 'disabled' : ''; ?>">
                                                <a class="page-link"
                                                    href="info.php?id=<?= $_GET['id']; ?>&status=<?= $_GET['status']; ?>&facility_id=<?= $currentSite; ?>&page=<?php echo max($currentPage - 1, 1); ?>">&laquo;</a>
                                            </li>

                                            <!-- First Page (if outside the range) -->
                                            <?php if ($start > 1): ?>
                                                <li class="page-item">
                                                    <a class="page-link"
                                                        href="info.php?id=<?= $_GET['id']; ?>&status=<?= $_GET['status']; ?>&facility_id=<?= $currentSite; ?>&page=1">1</a>
                                                </li>
                                                <?php if ($start > 2): ?>
                                                    <li class="page-item disabled">
                                                        <span class="page-link">...</span>
                                                    </li>
                                                <?php endif; ?>
                                            <?php endif; ?>

                                            <!-- Visible Page Links -->
                                            <?php for ($i = $start; $i <= $end; $i++): ?>
                                                <li class="page-item <?php echo ($i === $currentPage) ? 'active' : ''; ?>">
                                                    <a class="page-link"
                                                        href="info.php?id=<?= $_GET['id']; ?>&status=<?= $_GET['status']; ?>&facility_id=<?= $currentSite; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                                </li>
                                            <?php endfor; ?>

                                            <!-- Last Page (if outside the range) -->
                                            <?php if ($end < $pages): ?>
                                                <?php if ($end < $pages - 1): ?>
                                                    <li class="page-item disabled">
                                                        <span class="page-link">...</span>
                                                    </li>
                                                <?php endif; ?>
                                                <li class="page-item">
                                                    <a class="page-link"
                                                        href="info.php?id=<?= $_GET['id']; ?>&status=<?= $_GET['status']; ?>&facility_id=<?= $currentSite; ?>&page=<?php echo $pages; ?>"><?php echo $pages; ?></a>
                                                </li>
                                            <?php endif; ?>
                                            <!-- Next Page -->
                                            <li class="page-item <?php echo ($currentPage >= $pages) ? 'disabled' : ''; ?>">
                                                <a class="page-link"
                                                    href="info.php?id=<?= $_GET['id']; ?>&status=<?= $_GET['status']; ?>&facility_id=<?= $currentSite; ?>&page=<?php echo min($currentPage + 1, $pages); ?>">&raquo;</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <!-- /.card -->
                                </div>
                                <!--/.col (right) -->
                            </div>
                            <!-- /.row -->
                        </div>
                        <!-- /.container-fluid -->
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->
        <?php } elseif ($_GET['id'] == 2) { ?>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>
                                    <?php
                                    if ($user->data()->power == 1 || $user->data()->accessLevel == 1 || $user->data()->accessLevel == 2) {
                                        if ($_GET['site_id'] != null) {
                                            $clients = $override->getDataDesc2('sites', 'status', 1, 'id', $_GET['site_id'], 'id');
                                        } else {
                                            $clients = $override->getDataDesc1('sites', 'status', 1, 'id');
                                        }
                                    } else {
                                        $clients = $override->getDataDesc2('sites', 'status', 1, 'id', $_GET['site_id'], 'id');
                                    } ?>
                                    Sites
                                </h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="index1.php">Home</a></li>
                                    <li class="breadcrumb-item active">Sites</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <section class="content-header">
                                        <div class="container-fluid">
                                            <div class="row mb-2">
                                                <div class="col-sm-3">
                                                    <div class="card-header">
                                                        <h3 class="card-title">List of Sites</h3>&nbsp;&nbsp;
                                                        <span class="badge badge-info right"><?= $results; ?></span>
                                                    </div>
                                                </div>

                                                <div class="col-sm-3">
                                                    <ol class="breadcrumb float-sm-right">
                                                        <li class="breadcrumb-item">
                                                            <a href="index1.php">
                                                                < Back</a>
                                                        </li>
                                                        &nbsp;
                                                        <li class="breadcrumb-item">
                                                            <a href="index1.php">
                                                                Go Home > </a>
                                                        </li>
                                                    </ol>
                                                </div>
                                            </div>
                                            <hr>
                                        </div><!-- /.container-fluid -->
                                    </section>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <table id="example1" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Facility Id</th>
                                                    <th>Name</th>
                                                    <th>Status</th>
                                                    <th class="text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $x = 1;
                                                foreach ($clients as $value) {
                                                    $sites = $override->getNews('sites', 'status', 1, 'id', $value['site_id'])[0];
                                                    ?>
                                                    <tr>
                                                        <td class="table-user">
                                                            <?= $x; ?>
                                                        </td>
                                                        <td class="table-user">
                                                            <?= $value['id']; ?>
                                                        </td>
                                                        <td class="table-user">
                                                            <?= $value['name']; ?>
                                                        </td>
                                                        <td class="table-user">
                                                            <a href="#" class="btn btn-success">Active</a>
                                                        </td>
                                                        <td class="table-user">
                                                            <a href="add.php?id=2&site_id=<?= $value['id'] ?>"
                                                                class="btn btn-info">Update</a>
                                                            <a href="#delete<?= $value['id'] ?>" role="button"
                                                                class="btn btn-danger" data-toggle="modal">Delete</a>
                                                        </td>
                                                    </tr>
                                                    <div class="modal fade" id="delete<?= $value['id'] ?>" tabindex="-1"
                                                        role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <form method="post">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <button type="button" class="close"
                                                                            data-dismiss="modal"><span
                                                                                aria-hidden="true">&times;</span><span
                                                                                class="sr-only">Close</span></button>
                                                                        <h4>Delete Site</h4>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <strong style="font-weight: bold;color: red">
                                                                            <p>Are you sure you want to delete this site</p>
                                                                        </strong>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <input type="hidden" name="id"
                                                                            value="<?= $value['id'] ?>">
                                                                        <input type="submit" name="delete_sites" value="Delete"
                                                                            class="btn btn-danger">
                                                                        <button class="btn btn-default" data-dismiss="modal"
                                                                            aria-hidden="true">Close</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <?php $x++;
                                                } ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>Study Id</th>
                                                    <th>Site</th>
                                                    <th>Status</th>
                                                    <th class="text-center">Action</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <!-- /.card-body -->
                                </div>
                                <!-- /.card -->
                            </div>
                            <!--/.col (right) -->
                        </div>
                        <!-- /.row -->
                    </div><!-- /.container-fluid -->
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->

        <?php } elseif ($_GET['id'] == 3) { ?>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>
                                    <?php
                                    $pagNum = 0;
                                    if ($_GET['search_name']) {
                                        $searchTerm = $_GET['search_name'];
                                        if ($_GET['status'] == 1) {
                                            $pagNum = $override->getWithLimitSearchNewsCount1('screening', 'status', 1, $searchTerm, 'pid');
                                        } else if ($_GET['status'] == 2) {
                                            $pagNum = $override->getWithLimitSearchNewsCount2('screening', 'status', 1, 'eligible', 1, $searchTerm, 'pid');
                                        } else if ($_GET['status'] == 3) {
                                            // $pagNum = $override->countData('enrollment_form', 'status', 1, 'facility_id', $user->data()->site_id);
                                
                                            $pagNum = $override->getWithLimitSearchNewsCount1('enrollment_form', 'status', 1, 'pid', $searchTerm);
                                        } else if ($_GET['status'] == 4) {
                                            $pagNum = $override->getWithLimitSearchNewsCount1('termination', 'status', 1, $searchTerm, 'pid');
                                        } else {
                                            $pagNum = $override->getWithLimitSearchNewsCount('screening', $searchTerm, 'pid');
                                        }

                                        $pages = ceil($pagNum / $numRec);
                                        if (!$_GET['page'] || $_GET['page'] == 1) {
                                            $page = 0;
                                        } else {
                                            $page = ($_GET['page'] * $numRec) - $numRec;
                                        }

                                        if ($_GET['status'] == 1) {
                                            $data = $override->getWithLimitSearchNews1('screening', 'status', 1, $page, $numRec, $searchTerm, 'pid');
                                        } else if ($_GET['status'] == 2) {
                                            $data = $override->getWithLimitSearchNews2('screening', 'status', 1, 'eligible', 1, $page, $numRec, $searchTerm, 'pid');
                                        } else if ($_GET['status'] == 3) {
                                            // $data = $override->getWithLimit1Desc('enrollment_form', 'status', 1, 'facility_id', $user->data()->site_id, $page, $numRec);
                                
                                            $data = $override->getWithLimitSearchNews1('enrollment_form', 'status', 1, 'pid', $searchTerm);
                                        } else if ($_GET['status'] == 4) {
                                            $data = $override->getWithLimitSearchNews1('termination', 'status', 1, $page, $numRec, $searchTerm, 'pid');
                                        } else {
                                            $data = $override->getWithLimitSearchNews('screening', $page, $numRec, $searchTerm, 'pid');
                                        }

                                    } else {
                                        //     $clients = $override->getWithLimit3('clients', 'status', 1, 'eligible', 1, 'site_id', $_GET['site_id'], $page, $numRec);
                                        if ($user->data()->accessLevel == 1) {
                                            if ($_GET['facility_id'] != null) {
                                                if ($_GET['status'] == 1) {
                                                    $pagNum = $override->countData('screening', 'status', 1, 'facility_id', $_GET['facility_id']);
                                                } else if ($_GET['status'] == 2) {
                                                    $pagNum = $override->countData1('screening', 'status', 1, 'eligible', 1, 'facility_id', $_GET['facility_id']);
                                                } else if ($_GET['status'] == 3) {
                                                    $pagNum = $override->countData('enrollment_form', 'status', 1, 'facility_id', $_GET['facility_id']);
                                                } else if ($_GET['status'] == 4) {
                                                    $pagNum = $override->countData('termination', 'status', 1, 'facility_id', $_GET['facility_id']);
                                                } else {
                                                    $pagNum = $override->getCount('screening', 'facility_id', $_GET['facility_id']);
                                                }
                                            } else {
                                                if ($_GET['status'] == 1) {
                                                    $pagNum = $override->getCount('screening', 'status', 1);
                                                } else if ($_GET['status'] == 2) {
                                                    $pagNum = $override->countData('screening', 'status', 1, 'eligible', 1);
                                                } else if ($_GET['status'] == 3) {
                                                    $pagNum = $override->getCount('enrollment_form', 'status', 1);
                                                } else if ($_GET['status'] == 4) {
                                                    $pagNum = $override->getCount('termination', 'status', 1);
                                                } else {
                                                    $pagNum = $override->getNo('screening');
                                                }
                                            }
                                        } else {
                                            if ($_GET['status'] == 1) {
                                                $pagNum = $override->countData('screening', 'status', 1, 'facility_id', $user->data()->site_id);
                                            } else if ($_GET['status'] == 2) {
                                                $pagNum = $override->countData1('screening', 'status', 1, 'eligible', 1, 'facility_id', $user->data()->site_id);
                                            } else if ($_GET['status'] == 3) {
                                                $pagNum = $override->countData('enrollment_form', 'status', 1, 'facility_id', $user->data()->site_id);
                                            } else if ($_GET['status'] == 4) {
                                                $pagNum = $override->countData('termination', 'status', 1, 'facility_id', $user->data()->site_id);
                                            } else {
                                                $pagNum = $override->getData('screening');
                                            }
                                        }


                                        $pages = ceil($pagNum / $numRec);
                                        if (!$_GET['page'] || $_GET['page'] == 1) {
                                            $page = 0;
                                        } else {
                                            $page = ($_GET['page'] * $numRec) - $numRec;
                                        }


                                        if ($user->data()->accessLevel == 1) {
                                            if ($_GET['facility_id'] != null) {
                                                if ($_GET['status'] == 1) {
                                                    $data = $override->getWithLimit1Desc('screening', 'status', 1, 'facility_id', $_GET['facility_id'], $page, $numRec);
                                                } else if ($_GET['status'] == 2) {
                                                    $data = $override->getWithLimit2Desc('screening', 'status', 1, 'eligible', 1, 'facility_id', $_GET['facility_id'], $page, $numRec);
                                                } else if ($_GET['status'] == 3) {
                                                    $data = $override->getWithLimit1Desc('enrollment_form', 'status', 1, 'facility_id', $_GET['facility_id'], $page, $numRec);
                                                } else if ($_GET['status'] == 4) {
                                                    $data = $override->getWithLimit1Desc('termination', 'status', 1, 'facility_id', $_GET['facility_id'], $page, $numRec);
                                                } else {
                                                    $data = $override->getWithLimitDesc('screening', 'facility_id', $_GET['facility_id'], $page, $numRec);
                                                }
                                            } else {
                                                if ($_GET['status'] == 1) {
                                                    $data = $override->getWithLimitDesc('screening', 'status', 1, $page, $numRec);
                                                } else if ($_GET['status'] == 2) {
                                                    $data = $override->getWithLimit1Desc('screening', 'status', 1, 'eligible', 1, $page, $numRec);
                                                } else if ($_GET['status'] == 3) {
                                                    $data = $override->getWithLimitDesc('enrollment_form', 'status', 1, $page, $numRec);
                                                } else if ($_GET['status'] == 4) {
                                                    $data = $override->getWithLimitDesc('termination', 'status', 1, $page, $numRec);
                                                } else {
                                                    $data = $override->getWithLimit0Desc('screening', $page, $numRec);
                                                }
                                            }
                                        } else {
                                            if ($_GET['status'] == 1) {
                                                $data = $override->getWithLimit1Desc('screening', 'status', 1, 'facility_id', $user->data()->site_id, $page, $numRec);
                                            } else if ($_GET['status'] == 2) {
                                                $data = $override->getWithLimit2Desc('screening', 'status', 1, 'eligible', 1, 'facility_id', $user->data()->site_id, $page, $numRec);
                                            } else if ($_GET['status'] == 3) {
                                                $data = $override->getWithLimit1Desc('enrollment_form', 'status', 1, 'facility_id', $user->data()->site_id, $page, $numRec);
                                            } else if ($_GET['status'] == 4) {
                                                $data = $override->getWithLimit1Desc('termination', 'status', 1, 'facility_id', $user->data()->site_id, $page, $numRec);
                                            } else {
                                                $data = $override->getWithLimit0Desc('screening', $page, $numRec);
                                            }
                                        }
                                    }
                                    ?>
                                    <?php
                                    if ($_GET['status'] == 1) {
                                        echo $title = 'Screening';
                                        ?>
                                        <?php
                                    } elseif ($_GET['status'] == 2) {
                                        echo $title = 'Eligible';
                                        ?>
                                        <?php
                                    } elseif ($_GET['status'] == 3) {
                                        echo $title = 'Enrollment';
                                        ?>
                                        <?php
                                    } elseif ($_GET['status'] == 4) {
                                        echo $title = 'End Study';
                                        ?>
                                        <?php
                                    } ?>
                                </h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="index1.php">Home</a></li>
                                    <li class="breadcrumb-item active"><?= $title; ?></li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <section class="content-header">
                                        <div class="container-fluid">
                                            <div class="row mb-2">
                                                <div class="col-sm-3">
                                                    <div class="card-header">
                                                        <?php
                                                        if ($_GET['status'] == 1) { ?>
                                                            <h3 class="card-title">List of Screened Clients</h3> &nbsp;&nbsp;
                                                            <span class="badge badge-info right"><?= $pagNum; ?></span>
                                                            <?php
                                                        } elseif ($_GET['status'] == 2) { ?>
                                                            <h3 class="card-title">List of Eligible Clients</h3> &nbsp;&nbsp;
                                                            <span class="badge badge-info right"><?= $pagNum; ?></span>
                                                            <?php
                                                        } elseif ($_GET['status'] == 3) { ?>
                                                            <h3 class="card-title">List of Enrolled Clients</h3> &nbsp;&nbsp;
                                                            <span class="badge badge-info right"><?= $pagNum; ?></span>
                                                            <?php
                                                        } elseif ($_GET['status'] == 4) { ?>
                                                            <h3 class="card-title">List of Terminated Clients</h3> &nbsp;&nbsp;
                                                            <span class="badge badge-info right"><?= $pagNum; ?></span>
                                                            <?php
                                                        } ?>
                                                    </div>
                                                </div>
                                                <?php
                                                // if ($user->data()->accessLevel == 1) {
                                                ?>
                                                <div class="col-sm-3">
                                                    <form id="validation" enctype="multipart/form-data" method="post"
                                                        autocomplete="off">
                                                        <div class="row">
                                                            <div class="col-sm-4">
                                                                <div class="row-form clearfix">
                                                                    <div class="form-group">
                                                                        <select class="form-control" name="facility_id"
                                                                            style="width: 100%;" autocomplete="off">
                                                                            <option value="">Select Site</option>
                                                                            <?php foreach ($override->get('sites', 'status', 1) as $site) { ?>
                                                                                <option value="<?= $site['id'] ?>">
                                                                                    <?= $site['name'] ?>
                                                                                </option>
                                                                            <?php } ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-4">
                                                                <div class="row-form clearfix">
                                                                    <div class="form-group">
                                                                        <input type="submit" name="search_by_site"
                                                                            value="Search by Site" class="btn btn-primary">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                                <div class="card-tools">
                                                    <div class="input-group input-group-sm float-right"
                                                        style="width: 350px;">
                                                        <form method="get">
                                                            <div class="form-inline">
                                                                <input type="hidden" name="id" value="<?= $_GET['id'] ?>">
                                                                <input type="hidden" name="status"
                                                                    value="<?= $_GET['status'] ?>">
                                                                <input type="hidden" name="sid" value="<?= $_GET['sid'] ?>">
                                                                <input type="hidden" name="facility_id"
                                                                    value="<?= $_GET['facility_id'] ?>">
                                                                <input type="hidden" name="page"
                                                                    value="<?= $_GET['page'] ?>">
                                                                <input type="text" name="search_name" id="search_name"
                                                                    class="form-control float-right"
                                                                    placeholder="Search here PID">
                                                                <input type="submit" value="Search"
                                                                    class="btn btn-default"><i class="fas fa-search"></i>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                                <?php
                                                //  } 
                                                ?>
                                                <div class="col-sm-6">
                                                    <ol class="breadcrumb float-sm-right">
                                                        <li class="breadcrumb-item">
                                                            <a href="index1.php">
                                                                < Back</a>
                                                        </li>
                                                        &nbsp;
                                                        <li class="breadcrumb-item">
                                                            <a href="index1.php">
                                                                Go Home > </a>
                                                        </li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </div><!-- /.container-fluid -->
                                    </section>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <?php if ($_GET['status'] == 1 || $_GET['status'] == 2) { ?>
                                                        <th>Screening Date /
                                                            <hr> Time
                                                        </th>
                                                    <?php } ?>
                                                    <?php if ($_GET['status'] == 3) { ?>
                                                        <th>Enrollment Date /
                                                            <hr> Time
                                                        </th>
                                                    <?php } ?>
                                                    <?php if ($_GET['status'] == 4) { ?>
                                                        <th>Termination Date /
                                                            <hr> Time
                                                        </th>
                                                    <?php } ?>
                                                    <th>PID</th>
                                                    <th>Site / Staff</th>
                                                    <th class="text-center">Status/
                                                        <hr> Action
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $x = 1;
                                                foreach ($data as $value) {
                                                    $sites = $override->getNews('sites', 'status', 1, 'id', $value['facility_id'])[0];
                                                    $sex = $override->getNews('sex', 'status', 1, 'id', $value['sex'])[0];
                                                    $staff = $override->getNews('user', 'status', 1, 'id', $value['staff_id'])[0];
                                                    $sid = '';
                                                    if ($_GET['status'] == 1 || $_GET['status'] == 2) {
                                                        $sid = $value['id'];
                                                    } else {
                                                        $sid = $value['enrollment_id'];
                                                    }
                                                    ?>
                                                    <tr>
                                                        <?php if ($_GET['status'] == 1 || $_GET['status'] == 2) { ?>
                                                            <td class="table-user">
                                                                <?= $value['screening_date']; ?>
                                                                /
                                                                <hr>
                                                                <?php
                                                                // print_r (date("h:i:s A"));
                                                                print_r(date('H:i', strtotime($value['create_on'])));
                                                                ?>
                                                            </td>
                                                        <?php } ?>
                                                        <?php if ($_GET['status'] == 3) { ?>
                                                            <td class="table-user">
                                                                <?= $value['enrollment_date']; ?>
                                                                /
                                                                <hr>
                                                                <?php
                                                                // print_r (date("h:i:s A"));
                                                                print_r(date('H:i', strtotime($value['create_on'])));
                                                                ?>
                                                            </td>
                                                        <?php } ?>
                                                        <?php if ($_GET['status'] == 4) { ?>
                                                            <td class="table-user">
                                                                <?= $value['update_on']; ?> /
                                                                <hr>
                                                                <?php
                                                                // print_r (date("h:i:s A"));
                                                                print_r(date('H:i', strtotime($value['create_on'])));
                                                                ?>
                                                            </td>
                                                        <?php } ?>
                                                        <td class="table-user">
                                                            <?= $value['pid']; ?>
                                                        </td>
                                                        <td class="table-user">
                                                            <?= $sites['name']; ?>
                                                            /
                                                            <hr>
                                                            <?= $staff['firstname'] . '-' . $staff['lastname']; ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <?php if ($_GET['status'] == 1) { ?>
                                                                <?php if ($value['eligible'] == 1) { ?>
                                                                    <!-- <td class="text-center"> -->
                                                                    <a href="#" class="btn btn-success">
                                                                        <i class="ri-edit-box-line">
                                                                        </i> Eligible
                                                                    </a>
                                                                    <!-- </td> -->
                                                                <?php } else { ?>
                                                                    <!-- <td class="text-center"> -->
                                                                    <a href="#" class="btn btn-danger"> <i
                                                                            class="ri-edit-box-line"></i>Not
                                                                        Eligible</a>
                                                                    <!-- </td> -->
                                                                <?php } ?>
                                                                <hr>
                                                                <?php if ($_GET['status'] == 1 || $_GET['status'] == 2) { ?>
                                                                    <?php if ($override->get('screening', 'status', 1)) { ?>
                                                                        <a href="add.php?id=13&status=<?= $_GET['status'] ?>&sid=<?= $sid ?>&facility_id=<?= $_GET['facility_id'] ?>&page=<?= $_GET['page'] ?>"
                                                                            role=" button" class="btn btn-info"> Update Screening
                                                                            Data</a>&nbsp;&nbsp; <br><br>

                                                                    <?php } else { ?>
                                                                        <a href="add.php?id=13&status=<?= $_GET['status'] ?>&sid=<?= $sid ?>&facility_id=<?= $_GET['facility_id'] ?>&page=<?= $_GET['page'] ?>"
                                                                            role=" button" class="btn btn-warning"> Add Screening
                                                                            Data</a>&nbsp;&nbsp; <br><br>
                                                                    <?php } ?>
                                                                    <hr>
                                                                    <a href="#delete_record<?= $sid ?>" role="button"
                                                                        class="btn btn-primary" data-toggle="modal">Delete Record</a>
                                                                    <!-- <a href="#restore_record<?= $sid ?>" role="button" class="btn btn-warning" data-toggle="modal">Restore -->
                                                                    <!-- Record</a> -->
                                                                    <hr>
                                                                <?php } ?>
                                                                <?php
                                                                if ($override->get3('enrollment_form', 'status', 1, 'enrollment_id', $_GET['sid'], 'other_samples', 1)) {
                                                                    ?>
                                                                    <?php if (
                                                                        $override->getNews('enrollment_form', 'status', 1, 'enrollment_id', $sid) &&
                                                                        $override->getNews('diagnosis_test', 'status', 1, 'enrollment_id', $sid) &&
                                                                        $override->getNews('diagnosis', 'status', 1, 'enrollment_id', $sid) &&
                                                                        $override->getNews('respiratory', 'status', 1, 'enrollment_id', $sid) &&
                                                                        $override->getNews('non_respiratory', 'status', 1, 'enrollment_id', $sid)
                                                                    ) { ?>

                                                                        <?php if ($value['eligible'] || $_GET['status'] == 2 || $_GET['status'] == 3) { ?>
                                                                            <a href="info.php?id=6&status=<?= $_GET['status'] ?>&sid=<?= $sid ?>&facility_id=<?= $_GET['facility_id'] ?>&page=<?= $_GET['page'] ?>"
                                                                                role=" button" class="btn btn-info"> View Enrollment Forms
                                                                            </a>&nbsp;&nbsp; <br><br>
                                                                        <?php } ?>

                                                                    <?php } else { ?>
                                                                        <?php if ($value['eligible'] || $_GET['status'] == 2 || $_GET['status'] == 3) { ?>
                                                                            <a href="info.php?id=6&status=<?= $_GET['status'] ?>&sid=<?= $sid ?>&facility_id=<?= $_GET['facility_id'] ?>&page=<?= $_GET['page'] ?>"
                                                                                role=" button" class="btn btn-warning"> Add Enrollment Forms
                                                                            </a>&nbsp;&nbsp; <br><br>
                                                                        <?php } ?>
                                                                    <?php } ?>
                                                                <?php } else { ?>
                                                                    <?php if (
                                                                        $override->getNews('enrollment_form', 'status', 1, 'enrollment_id', $sid) &&
                                                                        $override->getNews('diagnosis_test', 'status', 1, 'enrollment_id', $sid) &&
                                                                        $override->getNews('diagnosis', 'status', 1, 'enrollment_id', $sid) &&
                                                                        $override->getNews('respiratory', 'status', 1, 'enrollment_id', $sid)
                                                                    ) { ?>

                                                                        <?php if ($value['eligible'] || $_GET['status'] == 2 || $_GET['status'] == 3) { ?>
                                                                            <a href="info.php?id=6&status=<?= $_GET['status'] ?>&sid=<?= $sid ?>&facility_id=<?= $_GET['facility_id'] ?>&page=<?= $_GET['page'] ?>"
                                                                                role=" button" class="btn btn-info"> View Enrollment Forms
                                                                            </a>&nbsp;&nbsp; <br><br>
                                                                        <?php } ?>

                                                                    <?php } else { ?>
                                                                        <?php if ($value['eligible'] || $_GET['status'] == 2 || $_GET['status'] == 3) { ?>
                                                                            <a href="info.php?id=6&status=<?= $_GET['status'] ?>&sid=<?= $sid ?>&facility_id=<?= $_GET['facility_id'] ?>&page=<?= $_GET['page'] ?>"
                                                                                role=" button" class="btn btn-warning"> Add Enrollment Forms
                                                                            </a>&nbsp;&nbsp; <br><br>
                                                                        <?php } ?>
                                                                    <?php } ?>
                                                                <?php } ?>

                                                            <?php } else { ?>
                                                                <?php if ($value['status'] == 1) { ?>
                                                                    <!-- <td class="text-center"> -->
                                                                    <a href="#" class="btn btn-success">
                                                                        <i class="ri-edit-box-line">
                                                                        </i> Enrolled
                                                                    </a>
                                                                    <!-- </td> -->
                                                                <?php } else { ?>
                                                                    <!-- <td class="text-center"> -->
                                                                    <a href="#" class="btn btn-danger"> <i
                                                                            class="ri-edit-box-line"></i>Not
                                                                        Enrolled</a>
                                                                </td>
                                                            <?php } ?>

                                                            <hr>
                                                            <?php if ($_GET['status'] == 1 || $_GET['status'] == 2) { ?>
                                                                <?php if ($override->get('screening', 'status', 1)) { ?>
                                                                    <a href="add.php?id=13&status=<?= $_GET['status'] ?>&sid=<?= $sid ?>&facility_id=<?= $_GET['facility_id'] ?>&page=<?= $_GET['page'] ?>"
                                                                        role=" button" class="btn btn-info"> Update Screening
                                                                        Data</a>&nbsp;&nbsp; <br><br>

                                                                <?php } else { ?>
                                                                    <a href="add.php?id=13&status=<?= $_GET['status'] ?>&sid=<?= $sid ?>&facility_id=<?= $_GET['facility_id'] ?>&page=<?= $_GET['page'] ?>"
                                                                        role=" button" class="btn btn-warning"> Add Screening
                                                                        Data</a>&nbsp;&nbsp; <br><br>
                                                                <?php } ?>
                                                                <hr>
                                                                <a href="#delete_record<?= $sid ?>" role="button"
                                                                    class="btn btn-primary" data-toggle="modal">Delete Record</a>
                                                                <!-- <a href="#restore_record<?= $sid ?>" role="button" class="btn btn-warning" data-toggle="modal">Restore -->
                                                                <!-- Record</a> -->
                                                                <hr>
                                                            <?php } ?>
                                                            <?php
                                                            if ($override->get3('enrollment_form', 'status', 1, 'enrollment_id', $_GET['sid'], 'other_samples', 1)) {
                                                                ?>
                                                                <?php if (
                                                                    $override->getNews('enrollment_form', 'status', 1, 'enrollment_id', $sid) &&
                                                                    $override->getNews('diagnosis_test', 'status', 1, 'enrollment_id', $sid) &&
                                                                    $override->getNews('diagnosis', 'status', 1, 'enrollment_id', $sid) &&
                                                                    $override->getNews('respiratory', 'status', 1, 'enrollment_id', $sid) &&
                                                                    $override->getNews('non_respiratory', 'status', 1, 'enrollment_id', $sid)
                                                                ) { ?>

                                                                    <?php if ($value['eligible'] || $_GET['status'] == 2 || $_GET['status'] == 3) { ?>
                                                                        <a href="info.php?id=6&status=<?= $_GET['status'] ?>&sid=<?= $sid ?>&facility_id=<?= $_GET['facility_id'] ?>&page=<?= $_GET['page'] ?>"
                                                                            role=" button" class="btn btn-info"> View Enrollment Forms
                                                                        </a>&nbsp;&nbsp; <br><br>
                                                                    <?php } ?>

                                                                <?php } else { ?>
                                                                    <?php if ($value['eligible'] || $_GET['status'] == 2 || $_GET['status'] == 3) { ?>
                                                                        <a href="info.php?id=6&status=<?= $_GET['status'] ?>&sid=<?= $sid ?>&facility_id=<?= $_GET['facility_id'] ?>&page=<?= $_GET['page'] ?>"
                                                                            role=" button" class="btn btn-warning"> Add Enrollment Forms
                                                                        </a>&nbsp;&nbsp; <br><br>
                                                                    <?php } ?>
                                                                <?php } ?>
                                                            <?php } else { ?>
                                                                <?php if (
                                                                    $override->getNews('enrollment_form', 'status', 1, 'enrollment_id', $sid) &&
                                                                    $override->getNews('diagnosis_test', 'status', 1, 'enrollment_id', $sid) &&
                                                                    $override->getNews('diagnosis', 'status', 1, 'enrollment_id', $sid) &&
                                                                    $override->getNews('respiratory', 'status', 1, 'enrollment_id', $sid)
                                                                ) { ?>

                                                                    <?php if ($value['eligible'] || $_GET['status'] == 2 || $_GET['status'] == 3) { ?>
                                                                        <a href="info.php?id=6&status=<?= $_GET['status'] ?>&sid=<?= $sid ?>&facility_id=<?= $_GET['facility_id'] ?>&page=<?= $_GET['page'] ?>"
                                                                            role=" button" class="btn btn-info"> View Enrollment Forms
                                                                        </a>&nbsp;&nbsp; <br><br>
                                                                    <?php } ?>

                                                                <?php } else { ?>
                                                                    <?php if ($value['eligible'] || $_GET['status'] == 2 || $_GET['status'] == 3) { ?>
                                                                        <a href="info.php?id=6&status=<?= $_GET['status'] ?>&sid=<?= $sid ?>&facility_id=<?= $_GET['facility_id'] ?>&page=<?= $_GET['page'] ?>"
                                                                            role=" button" class="btn btn-warning"> Add Enrollment Forms
                                                                        </a>&nbsp;&nbsp; <br><br>
                                                                    <?php } ?>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        <?php } ?>
                                                        </td>

                                                        <!-- <td class="text-center"> -->
                                                        <br>
                                                        <!-- </td> -->
                                                    </tr>
                                                    <div class="modal fade" id="delete_record<?= $sid ?>" tabindex="-1"
                                                        role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <form method="post">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <button type="button" class="close"
                                                                            data-dismiss="modal"><span
                                                                                aria-hidden="true">&times;</span><span
                                                                                class="sr-only">Close</span></button>
                                                                        <h4>Delete Record</h4>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <strong style="font-weight: bold;color: red">
                                                                            <p>Are you sure you want to delete this Record ?</p>
                                                                        </strong>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <input type="hidden" name="id" value="<?= $sid ?>">
                                                                        <?php
                                                                        //  if ($user->data()->accessLevel == 1) { 
                                                                        ?>
                                                                        <input type="submit" name="delete_record" value="Delete"
                                                                            class="btn btn-danger">
                                                                        <?php
                                                                        //  } 
                                                                        ?>
                                                                        <button class="btn btn-default" data-dismiss="modal"
                                                                            aria-hidden="true">Close</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <div class="modal fade" id="restore_record<?= $sid ?>" tabindex="-1"
                                                        role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <form method="post">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <button type="button" class="close"
                                                                            data-dismiss="modal"><span
                                                                                aria-hidden="true">&times;</span><span
                                                                                class="sr-only">Close</span></button>
                                                                        <h4>Restore Record</h4>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <strong style="font-weight: bold;color: green">
                                                                            <p>Are you sure you want to Restore this Record ?
                                                                            </p>
                                                                        </strong>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <input type="hidden" name="id"
                                                                            value="<?= $value['id'] ?>">
                                                                        <?php
                                                                        //  if ($user->data()->accessLevel == 1) { 
                                                                        ?>
                                                                        <input type="submit" name="restore_record"
                                                                            value="Restore" class="btn btn-warning">
                                                                        <?php
                                                                        //  } 
                                                                        ?>
                                                                        <button class="btn btn-default" data-dismiss="modal"
                                                                            aria-hidden="true">Close</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <?php $x++;
                                                } ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <?php if ($_GET['status'] == 1 || $_GET['status'] == 2) { ?>
                                                        <th>Screening Date /
                                                            <hr> Time
                                                        </th>
                                                    <?php } ?>
                                                    <?php if ($_GET['status'] == 3) { ?>
                                                        <th>Enrollment Date /
                                                            <hr> Time
                                                        </th>
                                                    <?php } ?>
                                                    <?php if ($_GET['status'] == 4) { ?>
                                                        <th>Termination Date /
                                                            <hr> Time
                                                        </th>
                                                    <?php } ?>
                                                    <th>PID</th>
                                                    <th>Site / Staff</th>
                                                    <th class="text-center">Status/
                                                        <hr> Action
                                                    </th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <!-- /.card-body -->
                                    <?php
                                    $currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
                                    $currentSite = $_GET['facility_id'];
                                    // $pages = 10; // Total number of pages (replace with your actual calculation)
                                    $range = 2; // Number of pages to show before and after the current page
                                
                                    // Calculate start and end for the visible range
                                    $start = max(1, $currentPage - $range);
                                    $end = min($pages, $currentPage + $range);
                                    ?>
                                    <div class="card-footer clearfix">
                                        <ul class="pagination pagination-sm m-0 float-right">
                                            <!-- Previous Page -->
                                            <li class="page-item <?php echo ($currentPage <= 1) ? 'disabled' : ''; ?>">
                                                <a class="page-link"
                                                    href="info.php?id=<?= $_GET['id']; ?>&status=<?= $_GET['status']; ?>&facility_id=<?= $currentSite; ?>&page=<?php echo max($currentPage - 1, 1); ?>">&laquo;</a>
                                            </li>

                                            <!-- First Page (if outside the range) -->
                                            <?php if ($start > 1): ?>
                                                <li class="page-item">
                                                    <a class="page-link"
                                                        href="info.php?id=<?= $_GET['id']; ?>&status=<?= $_GET['status']; ?>&facility_id=<?= $currentSite; ?>&page=1">1</a>
                                                </li>
                                                <?php if ($start > 2): ?>
                                                    <li class="page-item disabled">
                                                        <span class="page-link">...</span>
                                                    </li>
                                                <?php endif; ?>
                                            <?php endif; ?>

                                            <!-- Visible Page Links -->
                                            <?php for ($i = $start; $i <= $end; $i++): ?>
                                                <li class="page-item <?php echo ($i === $currentPage) ? 'active' : ''; ?>">
                                                    <a class="page-link"
                                                        href="info.php?id=<?= $_GET['id']; ?>&status=<?= $_GET['status']; ?>&facility_id=<?= $currentSite; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                                </li>
                                            <?php endfor; ?>

                                            <!-- Last Page (if outside the range) -->
                                            <?php if ($end < $pages): ?>
                                                <?php if ($end < $pages - 1): ?>
                                                    <li class="page-item disabled">
                                                        <span class="page-link">...</span>
                                                    </li>
                                                <?php endif; ?>
                                                <li class="page-item">
                                                    <a class="page-link"
                                                        href="info.php?id=<?= $_GET['id']; ?>&status=<?= $_GET['status']; ?>&facility_id=<?= $currentSite; ?>&page=<?php echo $pages; ?>"><?php echo $pages; ?></a>
                                                </li>
                                            <?php endif; ?>
                                            <!-- Next Page -->
                                            <li class="page-item <?php echo ($currentPage >= $pages) ? 'disabled' : ''; ?>">
                                                <a class="page-link"
                                                    href="info.php?id=<?= $_GET['id']; ?>&status=<?= $_GET['status']; ?>&facility_id=<?= $currentSite; ?>&page=<?php echo min($currentPage + 1, $pages); ?>">&raquo;</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card -->
                        </div>
                        <!--/.col (right) -->
                    </div>
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->
        <?php } elseif ($_GET['id'] == 4) { ?>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>Participant Forms</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="index1.php">Home</a></li>
                                    <li class="breadcrumb-item active">Participant Forms</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <?php
                                        $patient = $override->get('enrollment_form', 'id', $_GET['enrollment_id'])[0];
                                        $sex = $override->get('sex', 'id', $_GET['sex'])[0];
                                        $age = 'Age:  ' . $patient['age'];
                                        ?>


                                        <div class="row mb-2">
                                            <div class="col-sm-6">
                                                <h1>Study ID: <?= $patient['pid'] ?></h1>
                                                <h4><?= $age ?></h4>
                                                <h4><?= $sex ?></h4>
                                            </div>
                                            <div class="col-sm-6">
                                                <ol class="breadcrumb float-sm-right">
                                                    <li class="breadcrumb-item"><a
                                                            href="info.php?id=3&status=<?= $_GET['status'] ?>">
                                                            < Back</a>
                                                    </li>
                                                    <li class="breadcrumb-item">
                                                        <a href="index1.php">Go Home</a>
                                                        </a>
                                                    </li>
                                                </ol>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <table id="example1" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Screening Date</th>
                                                    <th>SITE</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $x = 1;
                                                foreach ($override->getNews('visit', 'status', 1, 'patient_id', $_GET['cid']) as $visit) {
                                                    $screening = $override->getNews('screening', 'status', 1, 'id', $_GET['cid'])[0];
                                                    $enrollment = $override->getNews('enrollment', 'status', 1, 'patient_id', $_GET['cid'])[0];
                                                    $site = $override->get('sites', 'id', $visit['facility_id'])[0];
                                                    ?>
                                                    <tr>
                                                        <td> <?= $visit['screening_date'] ?></td>
                                                        <td> <?= $site['name'] ?> </td>
                                                        <td>
                                                            <?php
                                                            if ($screening['eligible'] == 1) {
                                                                ?>
                                                                <?php if ($override->getNews('respiratory', 'status', 1, 'patient_id', $_GET['cid'])) { ?>
                                                                    <a href="add.php?id=11&cid=<?= $_GET['cid'] ?>&study_id=<?= $visit['pid'] ?>&status=<?= $_GET['status'] ?>"
                                                                        role=" button" class="btn btn-info"> Update Respiratory Sample
                                                                        Data </a>&nbsp;&nbsp; <br><br>

                                                                <?php } else { ?>
                                                                    <a href="add.php?id=11&cid=<?= $_GET['cid'] ?>&study_id=<?= $visit['pid'] ?>&status=<?= $_GET['status'] ?>"
                                                                        role=" button" class="btn btn-warning"> Add Respiratory Sample
                                                                        Data </a>&nbsp;&nbsp; <br><br>

                                                                <?php } ?>


                                                                <?php
                                                                //  if (!$override->get3('respiratory', 'status', 1, 'patient_id', $_GET['cid'], 'sample_type', 1)) { 
                                                                ?>

                                                                <?php if ($override->getNews('non_respiratory', 'status', 1, 'patient_id', $_GET['cid'])) { ?>
                                                                    <a href="add.php?id=12&cid=<?= $_GET['cid'] ?>&study_id=<?= $visit['pid'] ?>&status=<?= $_GET['status'] ?>"
                                                                        role=" button" class="btn btn-info"> Update Diagnostic Test
                                                                        Non-respiratory Samples Data </a>&nbsp;&nbsp; <br><br>

                                                                <?php } else { ?>
                                                                    <a href="add.php?id=12&cid=<?= $_GET['cid'] ?>&study_id=<?= $visit['pid'] ?>&status=<?= $_GET['status'] ?>"
                                                                        role=" button" class="btn btn-warning"> Add Diagnostic Test
                                                                        Non-respiratory Samples Data </a>&nbsp;&nbsp; <br><br>

                                                                <?php } ?>
                                                                <?php
                                                                //  } 
                                                                ?>

                                                                <?php if ($override->getNews('diagnosis_test', 'status', 1, 'patient_id', $_GET['cid'])) { ?>
                                                                    <a href="add.php?id=14&cid=<?= $_GET['cid'] ?>&study_id=<?= $visit['pid'] ?>&status=<?= $_GET['status'] ?>"
                                                                        role=" button" class="btn btn-info"> Update Diagnostic Test DST
                                                                        Data </a>&nbsp;&nbsp; <br><br>

                                                                <?php } else { ?>
                                                                    <a href="add.php?id=14&cid=<?= $_GET['cid'] ?>&study_id=<?= $visit['pid'] ?>&status=<?= $_GET['status'] ?>"
                                                                        role=" button" class="btn btn-warning"> Add Diagnostic Test DST
                                                                        Data </a>&nbsp;&nbsp; <br><br>

                                                                <?php } ?>

                                                                <?php if ($override->getNews('diagnosis', 'status', 1, 'patient_id', $_GET['cid'])) { ?>
                                                                    <a href="add.php?id=15&cid=<?= $_GET['cid'] ?>&study_id=<?= $visit['pid'] ?>&status=<?= $_GET['status'] ?>"
                                                                        role=" button" class="btn btn-info"> Update Diagnosis Data
                                                                    </a>&nbsp;&nbsp; <br><br>

                                                                <?php } else { ?>
                                                                    <a href="add.php?id=15&cid=<?= $_GET['cid'] ?>&study_id=<?= $visit['pid'] ?>&status=<?= $_GET['status'] ?>"
                                                                        role=" button" class="btn btn-warning"> Add Diagnosis Data
                                                                    </a>&nbsp;&nbsp; <br><br>

                                                                <?php } ?>

                                                                <?php
                                                            }
                                                            ?>
                                                        </td>
                                                    </tr>

                                                    <div class="modal fade" id="editEnrollment<?= $visit['id'] ?>">
                                                        <div class="modal-dialog">
                                                            <form method="post">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h4 class="modal-title">Enrollment Form</h4>
                                                                        <button type="button" class="close" data-dismiss="modal"
                                                                            aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="row">
                                                                            <div class="col-sm-12">
                                                                                <div class="row-form clearfix">
                                                                                    <!-- select -->
                                                                                    <div class="form-group">
                                                                                        <label>Enrollment Date</label>
                                                                                        <input value="<?php if ($enrollment['visit_date']) {
                                                                                            echo $enrollment['visit_date'];
                                                                                        } ?>" class="form-control"
                                                                                            max="<?= date('Y-m-d'); ?>"
                                                                                            type="date" name="enrollment_date"
                                                                                            id="enrollment_date" required />
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-sm-12">
                                                                                <div class="row-form clearfix">
                                                                                    <!-- select -->
                                                                                    <div class="form-group">
                                                                                        <label>Notes / Remarks /Comments</label>
                                                                                        <textarea class="form-control"
                                                                                            name="comments" rows="3">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <?php if ($enrollment['comments']) {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        echo $enrollment['comments'];
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    } ?>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </textarea>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="dr"><span></span></div>
                                                                    </div>
                                                                    <div class="modal-footer justify-content-between">
                                                                        <input type="hidden" name="id"
                                                                            value="<?= $enrollment['id'] ?>">
                                                                        <input type="hidden" name="cid"
                                                                            value="<?= $enrollment['patient_id'] ?>">
                                                                        <input type="hidden" name="sid"
                                                                            value="<?= $screening['id'] ?>">
                                                                        <button type="button" class="btn btn-default"
                                                                            data-dismiss="modal">Close</button>
                                                                        <input type="submit" name="add_enrollment"
                                                                            class="btn btn-primary" value="Submit">
                                                                    </div>
                                                                </div>
                                                                <!-- /.modal-content -->
                                                            </form>
                                                        </div>
                                                        <!-- /.modal-dialog -->
                                                    </div>
                                                    <!-- /.modal -->
                                                    <?php
                                                    $x++;
                                                } ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>Screening Date</th>
                                                    <th>SITE</th>
                                                    <th>Action</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <!-- /.card-body -->
                                </div>
                                <!-- /.card -->
                            </div>
                            <!--/.col (right) -->
                        </div>
                        <!-- /.row -->
                    </div><!-- /.container-fluid -->
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->
        <?php } elseif ($_GET['id'] == 5) { ?>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>
                                    <?php
                                    if ($user->data()->power == 1 || $user->data()->accessLevel == 1 || $user->data()->accessLevel == 2) {
                                        if ($_GET['site_id'] != null) {
                                            $clients = $override->getDataDesc2('position', 'status', 1, 'id', $_GET['position_id'], 'id');
                                        } else {
                                            $clients = $override->getDataDesc1('position', 'status', 1, 'id');
                                        }
                                    } else {
                                        $clients = $override->getDataDesc2('position', 'status', 1, 'id', $_GET['position_id'], 'id');
                                    } ?>
                                    position
                                </h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="index1.php">Home</a></li>
                                    <li class="breadcrumb-item active">position</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <section class="content-header">
                                        <div class="container-fluid">
                                            <div class="row mb-2">
                                                <div class="col-sm-3">
                                                    <div class="card-header">
                                                        <h3 class="card-title">List of Position</h3>&nbsp;&nbsp;
                                                        <span class="badge badge-info right"><?= $results; ?></span>
                                                    </div>
                                                </div>

                                                <div class="col-sm-3">
                                                    <ol class="breadcrumb float-sm-right">
                                                        <li class="breadcrumb-item">
                                                            <a href="index1.php">
                                                                < Back</a>
                                                        </li>
                                                        &nbsp;
                                                        <li class="breadcrumb-item">
                                                            <a href="index1.php">
                                                                Go Home > </a>
                                                        </li>
                                                    </ol>
                                                </div>
                                            </div>
                                            <hr>
                                        </div><!-- /.container-fluid -->
                                    </section>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <table id="example1" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>N0</th>
                                                    <th>Name</th>
                                                    <th>Status</th>
                                                    <th class="text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $x = 1;
                                                foreach ($clients as $value) {
                                                    $sites = $override->getNews('sites', 'status', 1, 'id', $value['site_id'])[0];
                                                    ?>
                                                    <tr>
                                                        <td class="table-user">
                                                            <?= $x; ?>
                                                        </td>
                                                        <td class="table-user">
                                                            <?= $value['name']; ?>
                                                        </td>
                                                        <td class="table-user">
                                                            <a href="#" class="btn btn-success">Active</a>
                                                        </td>
                                                        <td class="table-user">
                                                            <a href="add.php?id=2&sit_id=<?= $value['id'] ?>"
                                                                class="btn btn-info">Update</a>
                                                            <a href="#delete<?= $value['id'] ?>" role="button"
                                                                class="btn btn-danger" data-toggle="modal">Delete</a>
                                                        </td>
                                                    </tr>
                                                    <div class="modal fade" id="delete<?= $value['id'] ?>" tabindex="-1"
                                                        role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <form method="post">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <button type="button" class="close"
                                                                            data-dismiss="modal"><span
                                                                                aria-hidden="true">&times;</span><span
                                                                                class="sr-only">Close</span></button>
                                                                        <h4>Delete position</h4>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <strong style="font-weight: bold;color: red">
                                                                            <p>Are you sure you want to delete this position</p>
                                                                        </strong>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <input type="hidden" name="id"
                                                                            value="<?= $value['id'] ?>">
                                                                        <input type="submit" name="delete_positions"
                                                                            value="Delete" class="btn btn-danger">
                                                                        <button class="btn btn-default" data-dismiss="modal"
                                                                            aria-hidden="true">Close</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <?php $x++;
                                                } ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>Study Id</th>
                                                    <th>Site</th>
                                                    <th>Status</th>
                                                    <th class="text-center">Action</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <!-- /.card-body -->
                                </div>
                                <!-- /.card -->
                            </div>
                            <!--/.col (right) -->
                        </div>
                        <!-- /.row -->
                    </div><!-- /.container-fluid -->
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->

        <?php } elseif ($_GET['id'] == 6) { ?>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>Participant Forms</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="index1.php">Home</a></li>
                                    <li class="breadcrumb-item active">Participant Forms</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <?php
                                        $patient = $override->get('enrollment_form', 'enrollment_id', $_GET['sid'])[0];
                                        $sex = $override->get('sex', 'id', $patient['sex'])[0];
                                        $site = $override->get('sites', 'id', $patient['facility_id'])[0];
                                        ?>
                                        <div class="row mb-2">
                                            <div class="col-sm-6">
                                                <h1>Study ID: <?= $patient['pid'] ?></h1>
                                                <h4>Date of Birth :<?= $patient['dob'] ?></h4>
                                                <h4>Age :<?= $patient['age'] ?></h4>
                                                <h4>Sex: <?= $sex['name'] ?></h4>
                                            </div>
                                            <div class="col-sm-6">
                                                <ol class="breadcrumb float-sm-right">
                                                    <li class="breadcrumb-item"><a
                                                            href="info.php?id=3&status=<?= $_GET['status'] ?>&sid=<?= $value['id'] ?>&facility_id=<?= $_GET['facility_id'] ?>&page=<?= $_GET['page'] ?>">
                                                            < Back</a>
                                                    </li>
                                                    <li class="breadcrumb-item">
                                                        <a href="index1.php">Go Home</a>
                                                        </a>
                                                    </li>
                                                </ol>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <table id="example1" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Enrollment Date</th>
                                                    <th>Site</th>
                                                    <th>Status</th>
                                                    <th class="text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="table-user">
                                                        <?= $patient['enrollment_date']; ?>
                                                    </td>
                                                    <td class="table-user">
                                                        <?= $site['name']; ?>
                                                    </td>
                                                    <?php if ($patient['status'] == 1) { ?>
                                                        <td class="text-center">
                                                            <a href="#" class="btn btn-success">
                                                                <i class="ri-edit-box-line">
                                                                </i> Active
                                                            </a>
                                                        </td>
                                                    <?php } else { ?>
                                                        <td class="text-center">
                                                            <a href="#" class="btn btn-danger"> <i
                                                                    class="ri-edit-box-line"></i>Not
                                                                Active</a>
                                                        </td>
                                                    <?php } ?>
                                                    <td>
                                                        <?php if ($override->getNews('enrollment_form', 'status', 1, 'enrollment_id', $_GET['sid'])) { ?>
                                                            <a href="add.php?id=16&status=<?= $_GET['status'] ?>&sid=<?= $_GET['sid'] ?>&facility_id=<?= $_GET['facility_id'] ?>&page=<?= $_GET['page'] ?>"
                                                                role=" button" class="btn btn-info"> Update Enrolment <br>
                                                                (clinic)
                                                                Form</a>&nbsp;&nbsp; <br><br>

                                                        <?php } else { ?>
                                                            <a href="add.php?id=16&status=<?= $_GET['status'] ?>&sid=<?= $_GET['sid'] ?>&facility_id=<?= $_GET['facility_id'] ?>&page=<?= $_GET['page'] ?>"
                                                                role=" button" class="btn btn-warning"> Add Enrolment Form <br>
                                                                (clinic)
                                                            </a>&nbsp;&nbsp; <br><br>

                                                        <?php } ?>

                                                        <?php if ($override->getNews('enrollment_form', 'status', 1, 'enrollment_id', $_GET['sid'])) { ?>

                                                            <?php if ($override->getNews('respiratory', 'status', 1, 'enrollment_id', $_GET['sid'])) { ?>
                                                                <a href="add.php?id=11&status=<?= $_GET['status'] ?>&sid=<?= $_GET['sid'] ?>&facility_id=<?= $_GET['facility_id'] ?>&page=<?= $_GET['page'] ?>"
                                                                    role=" button" class="btn btn-info"> Update Laboratory form <br>
                                                                    (clinic)
                                                                </a>&nbsp;&nbsp; <br><br>

                                                            <?php } else { ?>
                                                                <a href="add.php?id=11&status=<?= $_GET['status'] ?>&sid=<?= $_GET['sid'] ?>&facility_id=<?= $_GET['facility_id'] ?>&page=<?= $_GET['page'] ?>"
                                                                    role=" button" class="btn btn-warning"> Add Laboratory form <br>
                                                                    (clinic)
                                                                </a>&nbsp;&nbsp; <br><br>

                                                            <?php } ?>

                                                            <?php
                                                            //  if ($user->data()->site_id == 6 || $user->data()->site_id == 13 || $user->data()->site_id == 20 || $user->data()->site_id == 22) {
                                                            ?>

                                                            <?php if ($override->getNews('diagnosis_test', 'status', 1, 'enrollment_id', $_GET['sid'])) { ?>
                                                                <a href="add.php?id=14&status=<?= $_GET['status'] ?>&sid=<?= $_GET['sid'] ?>&facility_id=<?= $_GET['facility_id'] ?>&page=<?= $_GET['page'] ?>"
                                                                    role=" button" class="btn btn-info"> Update Laboratory form <br>
                                                                    (zonal laboratory/CTRL)
                                                                </a>&nbsp;&nbsp; <br><br>

                                                            <?php } else { ?>
                                                                <a href="add.php?id=14&status=<?= $_GET['status'] ?>&sid=<?= $_GET['sid'] ?>&facility_id=<?= $_GET['facility_id'] ?>&page=<?= $_GET['page'] ?>"
                                                                    role=" button" class="btn btn-warning"> Add Laboratory form <br>
                                                                    (zonal laboratory/CTRL)
                                                                </a>&nbsp;&nbsp; <br><br>

                                                            <?php } ?>
                                                            <?php
                                                            //  } 
                                                            ?>


                                                            <?php if ($override->getNews('diagnosis', 'status', 1, 'enrollment_id', $_GET['sid'])) { ?>
                                                                <a href="add.php?id=15&status=<?= $_GET['status'] ?>&sid=<?= $_GET['sid'] ?>&facility_id=<?= $_GET['facility_id'] ?>&page=<?= $_GET['page'] ?>"
                                                                    role=" button" class="btn btn-info"> Update Diagnosis Form <br>
                                                                    (clinic)
                                                                </a>&nbsp;&nbsp; <br><br>

                                                            <?php } else { ?>
                                                                <a href="add.php?id=15&status=<?= $_GET['status'] ?>&sid=<?= $_GET['sid'] ?>&facility_id=<?= $_GET['facility_id'] ?>&page=<?= $_GET['page'] ?>"
                                                                    role=" button" class="btn btn-warning"> Add Diagnosis Form <br>
                                                                    (clinic)
                                                                </a>&nbsp;&nbsp; <br><br>

                                                            <?php } ?>
                                                            <?php
                                                        }
                                                        ?>

                                                    </td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>Enrollment Date</th>
                                                    <th>Site</th>
                                                    <th>Status</th>
                                                    <th class="text-center">Action</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <!-- /.card-body -->
                                </div>
                                <!-- /.card -->
                            </div>
                            <!--/.col (right) -->
                        </div>
                        <!-- /.row -->
                    </div><!-- /.container-fluid -->
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->
        <?php } elseif ($_GET['id'] == 7) { ?>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>
                                    <?php
                                    if ($user->data()->power == 1 || $user->data()->accessLevel == 1 || $user->data()->accessLevel == 2) {
                                        if ($_GET['site_id'] != null) {
                                            $clients = $override->getDataDesc2('history', 'status', 1, 'site_id', $_GET['site_id'], 'id');
                                        } else {
                                            $clients = $override->getDataDesc1('history', 'status', 1, 'id');
                                        }
                                    } else {
                                        $clients = $override->getDataDesc2('history', 'status', 1, 'site_id', $user->data()->site_id, 'id');
                                    } ?>
                                    history
                                </h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="index1.php">Home</a></li>
                                    <li class="breadcrumb-item active">history</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <section class="content-header">
                                        <div class="container-fluid">
                                            <div class="row mb-2">
                                                <div class="col-sm-3">
                                                    <div class="card-header">
                                                        <h3 class="card-title">List of history</h3>&nbsp;&nbsp;
                                                        <span class="badge badge-info right"><?= $history; ?></span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <?php
                                                    if ($user->data()->power == 1 || $user->data()->accessLevel == 1 || $user->data()->accessLevel == 2) {
                                                        ?>
                                                        <form id="validation" enctype="multipart/form-data" method="post"
                                                            autocomplete="off">
                                                            <div class="row">
                                                                <div class="col-sm-6">
                                                                    <div class="row-form clearfix">
                                                                        <div class="form-group">
                                                                            <select class="form-control" name="site_id"
                                                                                style="width: 100%;" autocomplete="off">
                                                                                <option value="">Select Site</option>
                                                                                <?php foreach ($override->get('sites', 'status', 1) as $site) { ?>
                                                                                    <option value="<?= $site['id'] ?>">
                                                                                        <?= $site['name'] ?>
                                                                                    </option>
                                                                                <?php } ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="row-form clearfix">
                                                                        <div class="form-group">
                                                                            <input type="submit" name="search_by_site"
                                                                                value="Search" class="btn btn-primary">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    <?php } ?>
                                                </div>
                                                <div class="col-sm-3">
                                                    <?php
                                                    if ($user->data()->power == 1 || $user->data()->accessLevel == 1 || $user->data()->accessLevel == 2) {
                                                        ?>
                                                        <form id="validation" enctype="multipart/form-data" method="post"
                                                            autocomplete="off">
                                                            <div class="row">
                                                                <div class="col-sm-6">
                                                                    <div class="row-form clearfix">
                                                                        <div class="form-group">
                                                                            <input type="submit" name="download_history"
                                                                                value="Download" class="btn btn-info">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    <?php } ?>
                                                </div>
                                                <div class="col-sm-3">
                                                    <ol class="breadcrumb float-sm-right">
                                                        <li class="breadcrumb-item">
                                                            <a href="index1.php">
                                                                < Back</a>
                                                        </li>
                                                        &nbsp;
                                                        <li class="breadcrumb-item">
                                                            <a href="index1.php">
                                                                Go Home > </a>
                                                        </li>
                                                    </ol>
                                                </div>
                                            </div>
                                            <hr>
                                        </div><!-- /.container-fluid -->
                                    </section>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <table id="example1" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Study Id</th>
                                                    <th>Site</th>
                                                    <th>Status</th>
                                                    <th class="text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $x = 1;
                                                foreach ($clients as $value) {
                                                    $sites = $override->getNews('sites', 'status', 1, 'id', $value['site_id'])[0];
                                                    ?>
                                                    <tr>
                                                        <td class="table-user">
                                                            <?= $value['study_id']; ?>
                                                        </td>
                                                        <td class="table-user">
                                                            <?= $sites['name']; ?>
                                                        </td>
                                                        <td class="table-user">
                                                            <a href="#" class="btn btn-success">Active</a>
                                                        </td>
                                                        <td class="table-user">
                                                            <a href="add.php?id=6&cid=<?= $value['id'] ?>"
                                                                class="btn btn-info">Update</a>
                                                        </td>
                                                    </tr>
                                                    <?php $x++;
                                                } ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>Study Id</th>
                                                    <th>Site</th>
                                                    <th>Status</th>
                                                    <th class="text-center">Action</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <!-- /.card-body -->
                                </div>
                                <!-- /.card -->
                            </div>
                            <!--/.col (right) -->
                        </div>
                        <!-- /.row -->
                    </div><!-- /.container-fluid -->
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->

        <?php } elseif ($_GET['id'] == 8) { ?>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>
                                    <?php
                                    if ($user->data()->power == 1 || $user->data()->accessLevel == 1 || $user->data()->accessLevel == 2) {
                                        if ($_GET['site_id'] != null) {
                                            $clients = $override->getDataDesc2('results', 'status', 1, 'site_id', $_GET['site_id'], 'id');
                                        } else {
                                            $clients = $override->getDataDesc1('results', 'status', 1, 'id');
                                        }
                                    } else {
                                        $clients = $override->getDataDesc2('results', 'status', 1, 'site_id', $user->data()->site_id, 'id');
                                    } ?>
                                    results
                                </h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="index1.php">Home</a></li>
                                    <li class="breadcrumb-item active">results</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <section class="content-header">
                                        <div class="container-fluid">
                                            <div class="row mb-2">
                                                <div class="col-sm-3">
                                                    <div class="card-header">
                                                        <h3 class="card-title">List of results</h3>&nbsp;&nbsp;
                                                        <span class="badge badge-info right"><?= $results; ?></span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <?php
                                                    if ($user->data()->power == 1 || $user->data()->accessLevel == 1 || $user->data()->accessLevel == 2) {
                                                        ?>
                                                        <form id="validation" enctype="multipart/form-data" method="post"
                                                            autocomplete="off">
                                                            <div class="row">
                                                                <div class="col-sm-6">
                                                                    <div class="row-form clearfix">
                                                                        <div class="form-group">
                                                                            <select class="form-control" name="site_id"
                                                                                style="width: 100%;" autocomplete="off">
                                                                                <option value="">Select Site</option>
                                                                                <?php foreach ($override->get('sites', 'status', 1) as $site) { ?>
                                                                                    <option value="<?= $site['id'] ?>">
                                                                                        <?= $site['name'] ?>
                                                                                    </option>
                                                                                <?php } ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="row-form clearfix">
                                                                        <div class="form-group">
                                                                            <input type="submit" name="search_by_site"
                                                                                value="Search" class="btn btn-primary">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    <?php } ?>
                                                </div>
                                                <div class="col-sm-3">
                                                    <?php
                                                    if ($user->data()->power == 1 || $user->data()->accessLevel == 1 || $user->data()->accessLevel == 2) {
                                                        ?>
                                                        <form id="validation" enctype="multipart/form-data" method="post"
                                                            autocomplete="off">
                                                            <div class="row">
                                                                <div class="col-sm-6">
                                                                    <div class="row-form clearfix">
                                                                        <div class="form-group">
                                                                            <input type="submit" name="download_results"
                                                                                value="Download" class="btn btn-info">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    <?php } ?>
                                                </div>
                                                <div class="col-sm-3">
                                                    <ol class="breadcrumb float-sm-right">
                                                        <li class="breadcrumb-item">
                                                            <a href="index1.php">
                                                                < Back</a>
                                                        </li>
                                                        &nbsp;
                                                        <li class="breadcrumb-item">
                                                            <a href="index1.php">
                                                                Go Home > </a>
                                                        </li>
                                                    </ol>
                                                </div>
                                            </div>
                                            <hr>
                                        </div><!-- /.container-fluid -->
                                    </section>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <table id="example1" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Study Id</th>
                                                    <th>Site</th>
                                                    <th>Status</th>
                                                    <th class="text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $x = 1;
                                                foreach ($clients as $value) {
                                                    $sites = $override->getNews('sites', 'status', 1, 'id', $value['site_id'])[0];
                                                    ?>
                                                    <tr>
                                                        <td class="table-user">
                                                            <?= $value['study_id']; ?>
                                                        </td>
                                                        <td class="table-user">
                                                            <?= $sites['name']; ?>
                                                        </td>
                                                        <td class="table-user">
                                                            <a href="#" class="btn btn-success">Active</a>
                                                        </td>
                                                        <td class="table-user">
                                                            <a href="add.php?id=7&cid=<?= $value['id'] ?>"
                                                                class="btn btn-info">Update</a>
                                                        </td>
                                                    </tr>
                                                    <?php $x++;
                                                } ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>Study Id</th>
                                                    <th>Site</th>
                                                    <th>Status</th>
                                                    <th class="text-center">Action</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <!-- /.card-body -->
                                </div>
                                <!-- /.card -->
                            </div>
                            <!--/.col (right) -->
                        </div>
                        <!-- /.row -->
                    </div><!-- /.container-fluid -->
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->

        <?php } elseif ($_GET['id'] == 9) { ?>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>
                                    <?php
                                    if ($user->data()->power == 1 || $user->data()->accessLevel == 1 || $user->data()->accessLevel == 2) {
                                        if ($_GET['site_id'] != null) {
                                            $clients = $override->getDataDesc2('classification', 'status', 1, 'site_id', $_GET['site_id'], 'id');
                                        } else {
                                            $clients = $override->getDataDesc1('classification', 'status', 1, 'id');
                                        }
                                    } else {
                                        $clients = $override->getDataDesc2('classification', 'status', 1, 'site_id', $user->data()->site_id, 'id');
                                    } ?>
                                    classification
                                </h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="index1.php">Home</a></li>
                                    <li class="breadcrumb-item active">classification</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <section class="content-header">
                                        <div class="container-fluid">
                                            <div class="row mb-2">
                                                <div class="col-sm-3">
                                                    <div class="card-header">
                                                        <h3 class="card-title">List of classification</h3>&nbsp;&nbsp;
                                                        <span class="badge badge-info right"><?= $classification; ?></span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <?php
                                                    if ($user->data()->power == 1 || $user->data()->accessLevel == 1 || $user->data()->accessLevel == 2) {
                                                        ?>
                                                        <form id="validation" enctype="multipart/form-data" method="post"
                                                            autocomplete="off">
                                                            <div class="row">
                                                                <div class="col-sm-6">
                                                                    <div class="row-form clearfix">
                                                                        <div class="form-group">
                                                                            <select class="form-control" name="site_id"
                                                                                style="width: 100%;" autocomplete="off">
                                                                                <option value="">Select Site</option>
                                                                                <?php foreach ($override->get('sites', 'status', 1) as $site) { ?>
                                                                                    <option value="<?= $site['id'] ?>">
                                                                                        <?= $site['name'] ?>
                                                                                    </option>
                                                                                <?php } ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="row-form clearfix">
                                                                        <div class="form-group">
                                                                            <input type="submit" name="search_by_site"
                                                                                value="Search" class="btn btn-primary">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    <?php } ?>
                                                </div>
                                                <div class="col-sm-3">
                                                    <?php
                                                    if ($user->data()->power == 1 || $user->data()->accessLevel == 1 || $user->data()->accessLevel == 2) {
                                                        ?>
                                                        <form id="validation" enctype="multipart/form-data" method="post"
                                                            autocomplete="off">
                                                            <div class="row">
                                                                <div class="col-sm-6">
                                                                    <div class="row-form clearfix">
                                                                        <div class="form-group">
                                                                            <input type="submit" name="download_classifiaction"
                                                                                value="Download" class="btn btn-info">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    <?php } ?>
                                                </div>
                                                <div class="col-sm-3">
                                                    <ol class="breadcrumb float-sm-right">
                                                        <li class="breadcrumb-item">
                                                            <a href="index1.php">
                                                                < Back</a>
                                                        </li>
                                                        &nbsp;
                                                        <li class="breadcrumb-item">
                                                            <a href="index1.php">
                                                                Go Home > </a>
                                                        </li>
                                                    </ol>
                                                </div>
                                            </div>
                                            <hr>
                                        </div><!-- /.container-fluid -->
                                    </section>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <table id="example1" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Study Id</th>
                                                    <th>Site</th>
                                                    <th>Status</th>
                                                    <th class="text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $x = 1;
                                                foreach ($clients as $value) {
                                                    $sites = $override->getNews('sites', 'status', 1, 'id', $value['site_id'])[0];
                                                    ?>
                                                    <tr>
                                                        <td class="table-user">
                                                            <?= $value['study_id']; ?>
                                                        </td>
                                                        <td class="table-user">
                                                            <?= $sites['name']; ?>
                                                        </td>
                                                        <td class="table-user">
                                                            <a href="#" class="btn btn-success">Active</a>
                                                        </td>
                                                        <td class="table-user">
                                                            <a href="add.php?id=8&cid=<?= $value['id'] ?>"
                                                                class="btn btn-info">Update</a>
                                                        </td>
                                                    </tr>
                                                    <?php $x++;
                                                } ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>Study Id</th>
                                                    <th>Site</th>
                                                    <th>Status</th>
                                                    <th class="text-center">Action</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <!-- /.card-body -->
                                </div>
                                <!-- /.card -->
                            </div>
                            <!--/.col (right) -->
                        </div>
                        <!-- /.row -->
                    </div><!-- /.container-fluid -->
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->

        <?php } elseif ($_GET['id'] == 10) { ?>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>
                                    <?php
                                    if ($user->data()->power == 1 || $user->data()->accessLevel == 1 || $user->data()->accessLevel == 2) {
                                        if ($_GET['site_id'] != null) {
                                            $clients = $override->getDataDesc2('outcome', 'status', 1, 'site_id', $_GET['site_id'], 'id');
                                        } else {
                                            $clients = $override->getDataDesc1('outcome', 'status', 1, 'id');
                                        }
                                    } else {
                                        $clients = $override->getDataDesc2('outcome', 'status', 1, 'site_id', $user->data()->site_id, 'id');
                                    } ?>
                                    outcome
                                </h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="index1.php">Home</a></li>
                                    <li class="breadcrumb-item active">outcome</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <section class="content-header">
                                        <div class="container-fluid">
                                            <div class="row mb-2">
                                                <div class="col-sm-3">
                                                    <div class="card-header">
                                                        <h3 class="card-title">List of Outcomes</h3>&nbsp;&nbsp;
                                                        <span class="badge badge-info right"><?= $outcome; ?></span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <?php
                                                    if ($user->data()->power == 1 || $user->data()->accessLevel == 1 || $user->data()->accessLevel == 2) {
                                                        ?>
                                                        <form id="validation" enctype="multipart/form-data" method="post"
                                                            autocomplete="off">
                                                            <div class="row">
                                                                <div class="col-sm-6">
                                                                    <div class="row-form clearfix">
                                                                        <div class="form-group">
                                                                            <select class="form-control" name="site_id"
                                                                                style="width: 100%;" autocomplete="off">
                                                                                <option value="">Select Site</option>
                                                                                <?php foreach ($override->get('sites', 'status', 1) as $site) { ?>
                                                                                    <option value="<?= $site['id'] ?>">
                                                                                        <?= $site['name'] ?>
                                                                                    </option>
                                                                                <?php } ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="row-form clearfix">
                                                                        <div class="form-group">
                                                                            <input type="submit" name="search_by_site"
                                                                                value="Search" class="btn btn-primary">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    <?php } ?>
                                                </div>
                                                <div class="col-sm-3">
                                                    <?php
                                                    if ($user->data()->power == 1 || $user->data()->accessLevel == 1 || $user->data()->accessLevel == 2) {
                                                        ?>
                                                        <form id="validation" enctype="multipart/form-data" method="post"
                                                            autocomplete="off">
                                                            <div class="row">
                                                                <div class="col-sm-6">
                                                                    <div class="row-form clearfix">
                                                                        <div class="form-group">
                                                                            <input type="submit" name="download_outcome"
                                                                                value="Download" class="btn btn-info">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    <?php } ?>
                                                </div>
                                                <div class="col-sm-6">
                                                    <ol class="breadcrumb float-sm-right">
                                                        <li class="breadcrumb-item">
                                                            <a href="index1.php">
                                                                < Back</a>
                                                        </li>
                                                        &nbsp;
                                                        <li class="breadcrumb-item">
                                                            <a href="index1.php">
                                                                Go Home > </a>
                                                        </li>
                                                    </ol>
                                                </div>
                                            </div>
                                            <hr>
                                        </div><!-- /.container-fluid -->
                                    </section>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <table id="example1" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Study Id</th>
                                                    <th>Site</th>
                                                    <th>Status</th>
                                                    <th class="text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $x = 1;
                                                foreach ($clients as $value) {
                                                    $sites = $override->getNews('sites', 'status', 1, 'id', $value['site_id'])[0];
                                                    ?>
                                                    <tr>
                                                        <td class="table-user">
                                                            <?= $value['study_id']; ?>
                                                        </td>
                                                        <td class="table-user">
                                                            <?= $sites['name']; ?>
                                                        </td>
                                                        <td class="table-user">
                                                            <a href="#" class="btn btn-success">Active</a>
                                                        </td>
                                                        <td class="table-user">
                                                            <a href="add.php?id=10&cid=<?= $value['id'] ?>"
                                                                class="btn btn-info">Update</a>
                                                        </td>
                                                    </tr>
                                                    <?php $x++;
                                                } ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>Study Id</th>
                                                    <th>Site</th>
                                                    <th>Status</th>
                                                    <th class="text-center">Action</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <!-- /.card-body -->
                                </div>
                                <!-- /.card -->
                            </div>
                            <!--/.col (right) -->
                        </div>
                        <!-- /.row -->
                    </div><!-- /.container-fluid -->
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->
        <?php } elseif ($_GET['id'] == 11) { ?>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>
                                    <?php
                                    if ($user->data()->power == 1) {
                                        $sites = $override->getDataAsc0('sites', 'id');
                                    } else {
                                        $sites = $override->getDataAsc('sites', 'status', 1, 'id');
                                    }
                                    ?>
                                    Sites
                                </h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="index1.php">Home</a></li>
                                    <li class="breadcrumb-item active">Sites</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <section class="content-header">
                                        <div class="container-fluid">
                                            <div class="row mb-2">
                                                <div class="col-sm-3">
                                                    <div class="card-header">
                                                        <h3 class="card-title">List of Sites</h3>&nbsp;&nbsp;
                                                        <span class="badge badge-info right"><?= $sites; ?></span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <?php
                                                    if ($user->data()->power == 1 || $user->data()->accessLevel == 1 || $user->data()->accessLevel == 2) {
                                                        ?>
                                                        <form id="validation" enctype="multipart/form-data" method="post"
                                                            autocomplete="off">
                                                            <div class="row">
                                                                <div class="col-sm-6">
                                                                    <div class="row-form clearfix">
                                                                        <div class="form-group">
                                                                            <select class="form-control" name="site_id"
                                                                                style="width: 100%;" autocomplete="off">
                                                                                <option value="">Select Site</option>
                                                                                <?php foreach ($override->get('sites', 'status', 1) as $site) { ?>
                                                                                    <option value="<?= $site['id'] ?>">
                                                                                        <?= $site['name'] ?>
                                                                                    </option>
                                                                                <?php } ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="row-form clearfix">
                                                                        <div class="form-group">
                                                                            <input type="submit" name="search_by_site"
                                                                                value="Search" class="btn btn-primary">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    <?php } ?>
                                                </div>
                                                <div class="col-sm-3">
                                                    <?php
                                                    if ($user->data()->power == 1 || $user->data()->accessLevel == 1 || $user->data()->accessLevel == 2) {
                                                        ?>
                                                        <form id="validation" enctype="multipart/form-data" method="post"
                                                            autocomplete="off">
                                                            <div class="row">
                                                                <div class="col-sm-6">
                                                                    <div class="row-form clearfix">
                                                                        <div class="form-group">
                                                                            <input type="submit" name="download_economic"
                                                                                value="Download" class="btn btn-info">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    <?php } ?>
                                                </div>
                                                <div class="col-sm-3">
                                                    <ol class="breadcrumb float-sm-right">
                                                        <li class="breadcrumb-item">
                                                            <a href="index1.php">
                                                                < Back</a>
                                                        </li>
                                                        &nbsp;
                                                        <li class="breadcrumb-item">
                                                            <a href="index1.php">
                                                                Go Home > </a>
                                                        </li>
                                                    </ol>
                                                </div>
                                            </div>
                                            <hr>
                                        </div><!-- /.container-fluid -->
                                    </section>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <table id="example1" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th class="text-center">Name</th>
                                                    <th class="text-center">District</th>
                                                    <th class="text-center">Level</th>
                                                    <th class="text-center">Arm</th>
                                                    <th class="text-center">Status</th>
                                                    <th class="text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $x = 1;
                                                foreach ($sites as $value) {
                                                    $district = $override->getNews('districts', 'status', 1, 'id', $value['district'])[0];
                                                    $arm = $override->getNews('facility_arm', 'status', 1, 'id', $value['arm'])[0];
                                                    $level = $override->getNews('facility_level', 'status', 1, 'id', $value['level'])[0];
                                                    $facility = $override->getNews('facility', 'sequence', 0, 'facility_id', $value['id'])[0];
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <?= $x; ?>
                                                        </td>
                                                        <td class="table-user">
                                                            <?= $value['name']; ?>
                                                        </td>
                                                        <td class="table-user">
                                                            <?= $district['name']; ?>
                                                        </td>
                                                        <td class="table-user text-center">
                                                            <?= $arm['name']; ?>
                                                        </td>
                                                        <td class="table-user text-center">
                                                            <?= $level['name']; ?>
                                                        </td>
                                                        <td class="table-user text-center">
                                                            <?php if ($value['status']) { ?>
                                                                <a href="#" class="btn btn-success">Active</a>
                                                            <?php } else { ?>
                                                                <a href="#" class="btn btn-warning">Not Active</a>
                                                            <?php } ?>

                                                        </td>
                                                        <td class="table-user text-center">
                                                            <?php if ($facility['status']) { ?>
                                                                <a href="info.php?id=12&site_id=<?= $value['id'] ?>&region_id=<?= $value['region'] ?>&district_id=<?= $value['district'] ?>&ward_id=<?= $value['ward'] ?>&respondent=<?= $value['respondent'] ?>"
                                                                    class="btn btn-info">View Schedule</a>

                                                            <?php } else { ?>
                                                                <a href="add.php?id=6&site_id=<?= $value['id'] ?>&region_id=<?= $value['region'] ?>&district_id=<?= $value['district'] ?>&ward_id=<?= $value['ward'] ?>&sequence=0&visit_code=M0&vid=3&respondent=<?= $value['respondent'] ?>"
                                                                    class="btn btn-warning">Add Schedule</a>


                                                            <?php } ?>
                                                            <?php if ($user->data()->power == 1) { ?>
                                                                <a href="add.php?id=3&site_id=<?= $value['id'] ?>&region_id=<?= $value['region'] ?>&district_id=<?= $value['district'] ?>&ward_id=<?= $value['ward'] ?>&respondent=<?= $value['respondent'] ?>"
                                                                    class="btn btn-primary">Edit Site ( Facility
                                                                    )</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                                                                <a href="#deleteSite<?= $value['id'] ?>" role="button"
                                                                    class="btn btn-danger" data-toggle="modal">
                                                                    Delete
                                                                </a>

                                                                <a href="#restoreSite<?= $value['id'] ?>" role="button"
                                                                    class="btn btn-warning" data-toggle="modal">
                                                                    Restore
                                                                </a>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                    <div class="modal fade" id="deleteSite<?= $value['id'] ?>" tabindex="-1"
                                                        role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <form method="post">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <button type="button" class="close"
                                                                            data-dismiss="modal"><span
                                                                                aria-hidden="true">&times;</span><span
                                                                                class="sr-only">Close</span></button>
                                                                        <h4>Delete Facility</h4>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <strong style="font-weight: bold;color: red">
                                                                            <p>Are you sure you want to delete this Facility ?
                                                                            </p>
                                                                        </strong>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <input type="hidden" name="id"
                                                                            value="<?= $value['id'] ?>">
                                                                        <input type="submit" name="delete_facility"
                                                                            value="Delete" class="btn btn-danger">
                                                                        <button class="btn btn-default" data-dismiss="modal"
                                                                            aria-hidden="true">Close</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <!-- /.modal -->
                                                    <div class="modal fade" id="restoreSite<?= $value['id'] ?>" tabindex="-1"
                                                        role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <form method="post">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <button type="button" class="close"
                                                                            data-dismiss="modal"><span
                                                                                aria-hidden="true">&times;</span><span
                                                                                class="sr-only">Close</span></button>
                                                                        <h4>Restore Facility</h4>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <strong style="font-weight: bold;color: yellow">
                                                                            <p>Are you sure you want to Restore this Facility ?
                                                                            </p>
                                                                        </strong>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <input type="hidden" name="id"
                                                                            value="<?= $value['id'] ?>">
                                                                        <input type="submit" name="restore_facility"
                                                                            value="Restore" class="btn btn-warning">
                                                                        <button class="btn btn-default" data-dismiss="modal"
                                                                            aria-hidden="true">Close</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <!-- /.modal -->
                                                    <?php $x++;
                                                } ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>#</th>
                                                    <th class="text-center">Name</th>
                                                    <th class="text-center">District</th>
                                                    <th class="text-center">Level</th>
                                                    <th class="text-center">Arm</th>
                                                    <th class="text-center">Status</th>
                                                    <th class="text-center">Action</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <!-- /.card-body -->
                                </div>
                                <!-- /.card -->
                            </div>
                            <!--/.col (right) -->
                        </div>
                        <!-- /.row -->
                    </div><!-- /.container-fluid -->
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->
        <?php } elseif ($_GET['id'] == 12) { ?>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>Facility Schedules</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="index1.php">Home</a></li>
                                    <li class="breadcrumb-item active">Facility Schedules</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <?php
                                        $site = $override->get('sites', 'id', $_GET['site_id'])[0];
                                        $cat = '';

                                        if ($site['interview_type'] == 1) {
                                            $cat = 'Intervention';
                                        } elseif ($site['interview_type'] == 2) {
                                            $cat = 'Control';
                                        } else {
                                            $cat = 'Not Screened';
                                        }


                                        // if ($patient['sex'] == 1) {
                                        //     $gender = 'Male';
                                        // } elseif ($patient['sex'] == 2) {
                                        //     $gender = 'Female';
                                        // }
                                    
                                        $name = 'Name: ' . $site['name'];
                                        // $age =  'Age:  ' . $patient['age'];
                                        // $gender =  'Gender: ' . $gender;
                                        $cat = 'Type: ' . $cat;
                                        ?>


                                        <div class="row mb-2">
                                            <div class="col-sm-6">
                                                <!-- <h1>Name: <?= $name ?></h1> -->
                                                <h4><?= $name ?></h4>
                                                <!-- <h4><?= $age ?></h4>
                                                <h4><?= $gender ?></h4> -->
                                                <h4><?= $cat ?></h4>
                                            </div>
                                            <div class="col-sm-6">
                                                <ol class="breadcrumb float-sm-right">
                                                    <li class="breadcrumb-item"><a href="info.php?id=11">
                                                            < Back</a>
                                                    </li>
                                                    <li class="breadcrumb-item">
                                                        <a href="index1.php">Go Home</a>
                                                        </a>
                                                    </li>
                                                </ol>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <table id="example1" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Visit Day</th>
                                                    <th>Expected Date</th>
                                                    <th>Visit Date</th>
                                                    <?php
                                                    if ($user->data()->power == 1 || $user->data()->accessLevel == 1 || $user->data()->accessLevel == 2) {
                                                        ?>
                                                        <th>SITE</th>
                                                    <?php } ?>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $x = 1;
                                                $i = 0;
                                                foreach ($override->get('facility', 'site_id', $_GET['site_id']) as $visit) {
                                                    $clients = $override->get('clients', 'id', $_GET['cid'])[0];
                                                    $site = $override->get('sites', 'id', $visit['site_id'])[0];
                                                    ?>
                                                    <tr>
                                                        <td> <?= $visit['visit_code'] ?></td>
                                                        <td> <?= $visit['expected_date'] ?></td>
                                                        <td> <?= $visit['visit_date'] ?> </td>

                                                        <?php
                                                        if ($user->data()->power == 1 || $user->data()->accessLevel == 1 || $user->data()->accessLevel == 2) {
                                                            ?>
                                                            <td> <?= $site['name'] ?> </td>
                                                        <?php } ?>
                                                        <td>
                                                            <?php if ($visit['visit_status'] == 1) { ?>
                                                                <a href="#addVisit<?= $visit['id'] ?>" role="button"
                                                                    class="btn btn-success" data-toggle="modal">
                                                                    Done
                                                                </a>
                                                            <?php } elseif ($visit['visit_status'] == 2) { ?>
                                                                <a href="#addVisit<?= $visit['id'] ?>" role="button"
                                                                    class="btn btn-warning" data-toggle="modal">
                                                                    Missed
                                                                </a>
                                                            <?php } elseif ($visit['visit_status'] == 0) { ?>
                                                                <a href="#addVisit<?= $visit['id'] ?>" role="button"
                                                                    class="btn btn-warning" data-toggle="modal">
                                                                    Pending
                                                                </a>
                                                            <?php } ?>
                                                        </td>

                                                        <td>
                                                            <?php if ($visit['visit_status'] == 1) { ?>
                                                                <?php if ($visit['sequence'] >= 0) { ?>
                                                                    <?php if ($override->get3('facility', 'site_id', $_GET['site_id'], 'extraction_date', '', 'sequence', $i)) { ?>
                                                                        <a href="add.php?id=6&site_id=<?= $_GET['site_id'] ?>&sequence=<?= $visit['sequence'] ?>&visit_code=<?= $visit['visit_code'] ?>&vid=<?= $visit['id'] ?>&status=<?= $_GET['status'] ?>&respondent=<?= $_GET['respondent'] ?>"
                                                                            role=" button" class="btn btn-warning"> Add Facility
                                                                            Records</a>&nbsp;&nbsp; <br><br>

                                                                    <?php } else { ?>
                                                                        <a href="add.php?id=6&site_id=<?= $_GET['site_id'] ?>&sequence=<?= $visit['sequence'] ?>&visit_code=<?= $visit['visit_code'] ?>&vid=<?= $visit['id'] ?>&status=<?= $_GET['status'] ?>&respondent=<?= $_GET['respondent'] ?>"
                                                                            role=" button" class="btn btn-info"> Update Facility Records
                                                                        </a>&nbsp;&nbsp; <br><br>

                                                                    <?php } ?>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>

                                                    <div class="modal fade" id="addVisit<?= $visit['id'] ?>">
                                                        <div class="modal-dialog">
                                                            <form method="post">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h4 class="modal-title">Update visit Status</h4>
                                                                        <button type="button" class="close" data-dismiss="modal"
                                                                            aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="row">
                                                                            <div class="col-sm-6">
                                                                                <div class="row-form clearfix">
                                                                                    <!-- select -->
                                                                                    <div class="form-group">
                                                                                        <label>Visit Date</label>
                                                                                        <input value="<?php if ($visit['visit_date']) {
                                                                                            echo $visit['visit_date'];
                                                                                        } ?>" class="form-control"
                                                                                            max="<?= date('Y-m-d'); ?>"
                                                                                            type="date" name="visit_date"
                                                                                            id="visit_date" required />
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-6">
                                                                                <div class="mb-2">
                                                                                    <label for="income_household"
                                                                                        class="form-label">Visit Status</label>
                                                                                    <select class="form-control"
                                                                                        id="visit_status" name="visit_status"
                                                                                        style="width: 100%;" required>
                                                                                        <option
                                                                                            value="<?= $visit['visit_status'] ?>">
                                                                                            <?php if ($visit['visit_status']) {
                                                                                                if ($visit['visit_status'] == 1) {
                                                                                                    echo 'Attended';
                                                                                                } elseif ($visit['visit_status'] == 2) {
                                                                                                    echo 'Missed';
                                                                                                }
                                                                                            } else {
                                                                                                echo 'Select';
                                                                                            } ?>
                                                                                        </option>
                                                                                        <option value="1">Attended</option>
                                                                                        <option value="2">Missed</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-sm-12">
                                                                                <div class="row-form clearfix">
                                                                                    <!-- select -->
                                                                                    <div class="form-group">
                                                                                        <label>Notes / Remarks /Comments</label>
                                                                                        <textarea class="form-control"
                                                                                            name="comments" rows="3">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <?php if ($visit['comments']) {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        echo $visit['comments'];
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    } ?>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </textarea>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="dr"><span></span></div>
                                                                    </div>
                                                                    <div class="modal-footer justify-content-between">
                                                                        <input type="hidden" name="id"
                                                                            value="<?= $visit['id'] ?>">
                                                                        <button type="button" class="btn btn-default"
                                                                            data-dismiss="modal">Close</button>
                                                                        <input type="submit" name="add_facility_visit"
                                                                            class="btn btn-primary" value="Save changes">
                                                                    </div>
                                                                </div>
                                                                <!-- /.modal-content -->
                                                            </form>
                                                        </div>
                                                        <!-- /.modal-dialog -->
                                                    </div>
                                                    <!-- /.modal -->
                                                    <?php
                                                    $x++;
                                                    $i++;
                                                } ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>Visit Day</th>
                                                    <th>Expected Date</th>
                                                    <th>Visit Date</th>
                                                    <?php
                                                    if ($user->data()->power == 1 || $user->data()->accessLevel == 1 || $user->data()->accessLevel == 2) {
                                                        ?>
                                                        <th>SITE</th>
                                                    <?php } ?>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <!-- /.card-body -->
                                </div>
                                <!-- /.card -->
                            </div>
                            <!--/.col (right) -->
                        </div>
                        <!-- /.row -->
                    </div><!-- /.container-fluid -->
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->
        <?php } elseif ($_GET['id'] == 13) { ?>
        <?php } elseif ($_GET['id'] == 14) { ?>
            <?php
            $AllTables = $override->AllTables();
            ?>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>Clear Data on Table</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="index1.php">
                                            < Back</a>
                                    </li>&nbsp;&nbsp;
                                    <li class="breadcrumb-item"><a href="index1.php">Home</a></li>&nbsp;&nbsp;
                                    <li class="breadcrumb-item active">Clear Data on Table</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <!-- right column -->
                            <div class="col-md-12">
                                <!-- general form elements disabled -->
                                <div class="card card-warning">
                                    <div class="card-header">
                                        <h3 class="card-title">Clear Data on Table</h3>
                                    </div>
                                    <!-- <dl>
                                        <dt>Clients</dt>
                                        <dd>- Clients</dd>
                                        <dt>Kap</dt>
                                        <dd>- Kap</dd>
                                        <dt>History</dt>
                                        <dd>- History</dd>
                                        <dt>Results</dt>
                                        <dd>- Results</dd>
                                        <dt>Classification</dt>
                                        <dd>- Classification</dd>
                                        <dt>Outcome</dt>
                                        <dd>- Outcome</dd>
                                        <dt>Economic</dt>
                                        <dd>- Economic</dd>
                                        <dt>Visit</dt>
                                        <dd>- Visit</dd>
                                    </dl> -->
                                    <!-- /.card-header -->
                                    <form id="validation" enctype="multipart/form-data" method="post" autocomplete="off">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="mb-3">
                                                        <label for="name" class="form-label">Clear Data on Table</label>
                                                        <select name="name" id="name" class="form-control" required>
                                                            <option value="">Select Table</option>
                                                            <?php foreach ($AllTables as $tables) {
                                                                if ($tables['Tables_in_dream'] == 'screening' || $tables['Tables_in_dream'] == 'screening_records' || $tables['Tables_in_dream'] == 'enrollment_form' || $tables['Tables_in_dream'] == 'enrollment_form_records' || $tables['Tables_in_dream'] == 'respiratory' || $tables['Tables_in_dream'] == 'respiratory_records' || $tables['Tables_in_dream'] == 'non_respiratory' || $tables['Tables_in_dream'] == 'non_respiratory_records' || $tables['Tables_in_dream'] == 'diagnosis' || $tables['Tables_in_dream'] == 'diagnosis_records' || $tables['Tables_in_dream'] == 'diagnosis_test' || $tables['Tables_in_dream'] == 'diagnosis_test_records' || $tables['Tables_in_dream'] == 'study_id') {
                                                                    ?>
                                                                    <option value="<?= $tables['Tables_in_dream'] ?>">
                                                                        <?= $tables['Tables_in_dream'] ?>
                                                                    </option>
                                                                <?php }
                                                            } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /.card-body -->
                                        <div class="card-footer">
                                            <input type="submit" name="clear_data" value="Clear Data"
                                                class="btn btn-primary">
                                        </div>
                                    </form>
                                </div>
                                <!-- /.card -->
                            </div>
                            <!--/.col (right) -->
                        </div>
                        <!-- /.row -->
                    </div><!-- /.container-fluid -->
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->
        <?php } elseif ($_GET['id'] == 15) { ?>
            <?php
            $AllTables = $override->AllTables();
            ?>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>Unset Study ID</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="index1.php">
                                            < Back</a>
                                    </li>&nbsp;&nbsp;
                                    <li class="breadcrumb-item"><a href="index1.php">Home</a></li>&nbsp;&nbsp;
                                    <li class="breadcrumb-item active">Unset Study ID</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <!-- right column -->
                            <div class="col-md-12">
                                <!-- general form elements disabled -->
                                <div class="card card-warning">
                                    <div class="card-header">
                                        <h3 class="card-title">Unset Study ID</h3>
                                    </div>
                                    <!-- /.card-header -->
                                    <form id="validation" enctype="multipart/form-data" method="post" autocomplete="off">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="mb-3">
                                                        <label for="name" class="form-label">Unset Study ID</label>
                                                        <select name="name" id="name" class="form-control" required>
                                                            <option value="">Select Table</option>
                                                            <?php foreach ($AllTables as $tables) {
                                                                if ($tables['Tables_in_dream'] == 'study_id') { ?>
                                                                    <option value="<?= $tables['Tables_in_dream'] ?>">
                                                                        <?= $tables['Tables_in_dream'] ?>
                                                                    </option>
                                                                <?php }
                                                            } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /.card-body -->
                                        <div class="card-footer">
                                            <input type="submit" name="unset_study_id" value="Unset Study ID Table"
                                                class="btn btn-primary">
                                        </div>
                                    </form>
                                </div>
                                <!-- /.card -->
                            </div>
                            <!--/.col (right) -->
                        </div>
                        <!-- /.row -->
                    </div><!-- /.container-fluid -->
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->
        <?php } elseif ($_GET['id'] == 16) { ?>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>
                                    <?php
                                    if ($user->data()->power == 1 || $user->data()->accessLevel == 1 || $user->data()->accessLevel == 2) {
                                        if ($_GET['site_id'] != null) {
                                            $clients = $override->get('validations', 'status', 1);
                                        } else {

                                            $clients = $override->get('validations', 'status', 1);
                                        }
                                    } else {
                                        $clients = $override->get('validations', 'status', 1);
                                    } ?>
                                    <?php
                                    if ($_GET['status'] == 1) {
                                        echo $title = 'Screening';
                                        ?>
                                        <?php
                                    } elseif ($_GET['status'] == 2) {
                                        echo $title = 'Eligibility';
                                        ?>
                                        <?php
                                    } elseif ($_GET['status'] == 3) {
                                        echo $title = 'Enrollment';
                                        ?>
                                        <?php
                                    } elseif ($_GET['status'] == 4) {
                                        echo $title = 'Termination';
                                        ?>
                                        <?php
                                    } elseif ($_GET['status'] == 5) {
                                        echo $title = 'Registration';
                                        ?>
                                        <?php
                                    } ?>
                                </h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="index1.php">Home</a></li>
                                    <li class="breadcrumb-item active"><?= $title; ?></li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <section class="content-header">
                                        <div class="container-fluid">
                                            <div class="row mb-2">
                                                <div class="col-sm-3">
                                                    <div class="card-header">
                                                        <?php
                                                        if ($_GET['status'] == 1) { ?>
                                                            <h3 class="card-title">List of Screened Clients</h3> &nbsp;&nbsp;
                                                            <span class="badge badge-info right"><?= $screened; ?></span>
                                                            <?php
                                                        } elseif ($_GET['status'] == 2) { ?>
                                                            <h3 class="card-title">List of Eligible Clients</h3> &nbsp;&nbsp;
                                                            <span class="badge badge-info right"><?= $eligible; ?></span>
                                                            <?php
                                                        } elseif ($_GET['status'] == 3) { ?>
                                                            <h3 class="card-title">List of Enrolled Clients</h3> &nbsp;&nbsp;
                                                            <span class="badge badge-info right"><?= $enrolled; ?></span>
                                                            <?php
                                                        } elseif ($_GET['status'] == 4) { ?>
                                                            <h3 class="card-title">List of Terminated Clients</h3> &nbsp;&nbsp;
                                                            <span class="badge badge-info right"><?= $end; ?></span>
                                                            <?php
                                                        } elseif ($_GET['status'] == 5) { ?>
                                                            <h3 class="card-title">List of Registered Clients</h3> &nbsp;&nbsp;
                                                            <span class="badge badge-info right"><?= $registered; ?></span>
                                                            <?php
                                                        } elseif ($_GET['status'] == 7) { ?>
                                                            <h3 class="card-title">List of Registered Clients</h3> &nbsp;&nbsp;
                                                            <span class="badge badge-info right"><?= $registered; ?></span>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <?php
                                                    if ($user->data()->power == 1 || $user->data()->accessLevel == 1 || $user->data()->accessLevel == 2) {
                                                        ?>
                                                        <form id="validation" enctype="multipart/form-data" method="post"
                                                            autocomplete="off">
                                                            <div class="row">
                                                                <div class="col-sm-6">
                                                                    <div class="row-form clearfix">
                                                                        <div class="form-group">
                                                                            <select class="form-control" name="site_id"
                                                                                style="width: 100%;" autocomplete="off">
                                                                                <option value="">Select Site</option>
                                                                                <?php foreach ($override->get('sites', 'status', 1) as $site) { ?>
                                                                                    <option value="<?= $site['id'] ?>">
                                                                                        <?= $site['name'] ?>
                                                                                    </option>
                                                                                <?php } ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="row-form clearfix">
                                                                        <div class="form-group">
                                                                            <input type="submit" name="search_by_site"
                                                                                value="Search by Site" class="btn btn-primary">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    <?php } ?>
                                                </div>
                                                <div class="col-sm-6">
                                                    <ol class="breadcrumb float-sm-right">
                                                        <li class="breadcrumb-item">
                                                            <a href="index1.php">
                                                                < Back</a>
                                                        </li>
                                                        &nbsp;
                                                        <li class="breadcrumb-item">
                                                            <a href="index1.php">
                                                                Go Home > </a>
                                                        </li>
                                                    </ol>
                                                </div>
                                            </div>
                                            <hr>
                                        </div><!-- /.container-fluid -->
                                    </section>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <table id="example1" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Study Id</th>
                                                    <?php
                                                    if ($user->data()->power == 1 || $user->data()->accessLevel == 1 || $user->data()->accessLevel == 2) {
                                                        ?>
                                                        <th>Site</th>
                                                    <?php } ?>
                                                    <th>Status</th>
                                                    <th class="text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php

                                                $kap = 0;
                                                $screening = 0;
                                                $health_care = 0;
                                                if ($_GET['interview'] == 1) {
                                                    $interview = 'kap';
                                                } elseif ($_GET['interview'] == 2) {
                                                    $interview = 'screening';
                                                } elseif ($_GET['interview'] == 3) {
                                                    $interview = 'health_care';
                                                }
                                                $x = 1;
                                                foreach ($clients as $value) {
                                                    $yes_no = $override->get('yes_no', 'status', 1)[0];
                                                    // $kap = $override->getNews('kap', 'status', 1, 'patient_id', $value['id']);
                                                    // $history = $override->getNews('history', 'status', 1, 'patient_id', $value['id']);
                                            
                                                    // $results1 = $override->get3('results', 'status', 1, 'patient_id', $value['id'], 'sequence', 1);
                                                    // $results2 = $override->get3('results', 'status', 1, 'patient_id', $value['id'], 'sequence', 2);
                                            
                                                    // $classification1 = $override->get3('classification', 'status', 1, 'patient_id', $value['id'], 'sequence', 1);
                                                    // $classification2 = $override->get3('classification', 'status', 1, 'patient_id', $value['id'], 'sequence', 2);
                                            
                                                    // $economic1 = $override->get3('economic', 'status', 1, 'patient_id', $value['id'], 'sequence', 1);
                                                    // $economic2 = $override->get3('economic', 'status', 1, 'patient_id', $value['id'], 'sequence', 2);
                                            
                                                    // $outcome1 = $override->get3('outcome', 'status', 1, 'patient_id', $value['id'], 'sequence', 1);
                                                    // $outcome2 = $override->get3('outcome', 'status', 1, 'patient_id', $value['id'], 'sequence', 2);
                                            
                                                    $sites = $override->getNews('sites', 'status', 1, 'id', $value['site_id'])[0];
                                                    ?>
                                                    <tr>
                                                        <td class="table-user">
                                                            <?= $value['study_id']; ?>
                                                        </td>
                                                        <?php
                                                        if ($user->data()->power == 1 || $user->data()->accessLevel == 1 || $user->data()->accessLevel == 2) {
                                                            ?>

                                                            <td class="table-user">
                                                                <?= $sites['name']; ?>
                                                            </td>
                                                        <?php } ?>
                                                        <?php if ($value['status'] == 1) { ?>
                                                            <td class="text-center">
                                                                <a href="#" class="btn btn-success"> <i
                                                                        class="ri-edit-box-line"></i>Active</a>
                                                            </td>
                                                        <?php } else { ?>
                                                            <td class="text-center">
                                                                <a href="#" class="btn btn-danger"> <i
                                                                        class="ri-edit-box-line"></i>Not
                                                                    Active</a>
                                                            </td>
                                                        <?php } ?>
                                                        <td class="text-center">
                                                            <a href="add.php?id=17&cid=<?= $value['id'] ?>&status=<?= $_GET['status'] ?>"
                                                                class="btn btn-info"> <i class="ri-edit-box-line"></i>Update
                                                                Validation Tool</a>&nbsp;&nbsp;<br>

                                                        </td>
                                                    </tr>

                                                    <?php $x++;
                                                } ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>Study Id</th>
                                                    <?php
                                                    if ($user->data()->power == 1 || $user->data()->accessLevel == 1 || $user->data()->accessLevel == 2) {
                                                        ?>
                                                        <th>Site</th>
                                                    <?php } ?>
                                                    <th>Status</th>
                                                    <th class="text-center">Action</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <!-- /.card-body -->
                                </div>
                                <!-- /.card -->
                            </div>
                            <!--/.col (right) -->
                        </div>
                        <!-- /.row -->
                    </div><!-- /.container-fluid -->
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->
        <?php } elseif ($_GET['id'] == 17) { ?>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>
                                    <?php
                                    $pagNum = $override->getNO('users');

                                    $data = $override->getData('users');

                                    ?>
                                    Total Users
                                </h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="index1.php">Home</a></li>
                                    <li class="breadcrumb-item active">Total Users</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <section class="content-header">
                                        <div class="container-fluid">
                                            <div class="row mb-2">
                                                <div class="col-sm-3">
                                                    <div class="card-header">
                                                        <h3 class="card-title">List of Users</h3> &nbsp;&nbsp;
                                                        <span class="badge badge-info right"><?= $pagNum; ?></span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <ol class="breadcrumb float-sm-right">
                                                        <li class="breadcrumb-item">
                                                            <a href="index1.php">
                                                                < Back</a>
                                                        </li>
                                                        &nbsp;
                                                        <li class="breadcrumb-item">
                                                            <a href="index1.php">
                                                                Go Home > </a>
                                                        </li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </div><!-- /.container-fluid -->
                                    </section>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <!-- <th class="text-center">Action</th> -->
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $x = 1;
                                                foreach ($data as $value) {
                                                    ?>
                                                    <tr>
                                                        <td class="table-user">
                                                            <?= $value['id']; ?>
                                                        </td>
                                                        <td class="table-user">
                                                            <?= $sites['name']; ?>
                                                        </td>
                                                        <td class="table-user">
                                                            <?= $sites['email']; ?>
                                                        </td>
                                                    </tr>
                                                    <?php $x++;
                                                } ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <!-- <th class="text-center">Action</th> -->
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <!-- /.card-body -->
                                </div>
                            </div>
                            <!-- /.card -->
                        </div>
                        <!--/.col (right) -->
                    </div>
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->
        <?php } ?>

        <?php include 'footer.php'; ?>


        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables  & Plugins -->
    <script src="plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="plugins/jszip/jszip.min.js"></script>
    <script src="plugins/pdfmake/pdfmake.min.js"></script>
    <script src="plugins/pdfmake/vfs_fonts.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <!-- <script src="dist/js/demo.js"></script> -->
    <!-- Page specific script -->
    <script>
        // $(function() {
        //     $("#example1").DataTable({
        //         "responsive": true,
        //         "lengthChange": false,
        //         "autoWidth": false,
        //         "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        //     }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        //     $('#example2').DataTable({
        //         "paging": true,
        //         "lengthChange": false,
        //         "searching": false,
        //         "ordering": true,
        //         "info": true,
        //         "autoWidth": false,
        //         "responsive": true,
        //     });
        // });
    </script>
</body>

</html>