<?php
require_once 'php/core/init.php';
$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();

$successMessage = null;
$pageError = null;
$errorMessage = null;
$noE = 0;
$noC = 0;
$noD = 0;
$users = $override->getData('user');
if ($user->isLoggedIn()) {
    if (Input::exists('post')) {
        $validate = new validate();
        $data = null;
        $filename = null;
        if (Input::get('today_schedule')) {
            $data = $override->getNews('visit', 'expected_date', date('Y-m-d'), 'status', 0);
            $filename = 'Today Schedule Visits';
        }
        $user->exportData($data, $filename);
    }
} else {
    Redirect::to('index.php');
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title> Dashboard - NIMREGENIN</title>
    <?php include "head.php"; ?>
</head>

<body>
    <div class="wrapper">
        <?php include 'topbar.php' ?>
        <?php include 'menu.php' ?>
        <div class="content">
            <div class="breadLine">
                <ul class="breadcrumb">
                    <li><a href="#">Dashboard</a> <span class="divider">></span></li>
                </ul>
                <?php include 'pageInfo.php' ?>
            </div>

            <div class="workplace">
                <?php include 'header.php' ?>

                <div class="dr"><span></span></div>
                <div class="row">
                    <div class="col-md-12">
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
                        <div class="col-md-12">
                            <div class="head clearfix">
                                <div class="isw-grid"></div>
                                <h1>Today Schedule</h1>
                                <ul class="buttons">
                                    <li><a href="add.php?id=4&status=5.php" class="isw-plus"></a>Register New Client
                                    <li><a href="info.php?id=3&status=5.php" class="isw-edit"></a>Registred Clients</li>
                                    </li>
                                    <li><a href="today.php" class="isw-download"></a></li>
                                    <li><a href="#" class="isw-attachment"></a></li>
                                    <li>
                                        <a href="#" class="isw-settings"></a>
                                        <ul class="dd-list">
                                            <li><a href="#"><span class="isw-plus"></span> New document</a></li>
                                            <li><a href="#"><span class="isw-edit"></span> Edit</a></li>
                                            <li><a href="#"><span class="isw-delete"></span> Delete</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                            <?php if ($user->data()->power == 1) {
                                $visits = $override->getNews('visit', 'expected_date', date('Y-m-d'), 'status', 0);
                            } else {
                                $visits = $override->getNews('visit', 'expected_date', date('Y-m-d'), 'status', 0);
                                // $visits = $override->get3('visit', 'expected_date', date('Y-m-d'), 'status', 0, 'site_id',$user->data()->site_id);
                            } ?>
                            <form method="post"><input type="submit" name="today_schedule" value="Download Excel">
                            </form>
                            <div class="block-fluid">
                                <table cellpadding="0" cellspacing="0" width="100%" class="table">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" name="checkall" /></th>
                                            <td width="20">#</td>
                                            <th width="40">Picture</th>
                                            <th width="20%">Study ID</th>
                                            <th width="8%">ON NIMREGENIN</th>
                                            <th width="10%">Visit Name</th>
                                            <th width="10%">Name</th>
                                            <th width="10%">Gender</th>
                                            <th width="10%">Age</th>
                                            <th width="10%">Site</th>
                                            <th width="30%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // $Report = $override->getReport2('clients')[0];
                                        // print_r($Report);
                                        $x = 1;
                                        foreach ($visits as $visit) {
                                            $client = $override->get3('clients', 'id', $visit['client_id'], 'enrolled', 1, 'end_study', 0)[0];
                                            $site = $override->getNews('clients', 'id', $visit['client_id'], 'status', 1)[0]['site_id'];
                                            $site_id = $override->get('site', 'id', $site)[0]['name'];
                                            // $client = $override->get4('clients', 'id', $visit['client_id'],'enrolled',1,'end_study',0,'site_id',$user->data()->site_id)[0] 
                                        
                                            ?>
                                            <tr>
                                                <td><input type="checkbox" name="checkbox" /></td>
                                                <td><?= $x ?></td>
                                                <td width="100">
                                                    <?php if ($client['client_image'] != '' || is_null($client['client_image'])) {
                                                        $img = $client['client_image'];
                                                    } else {
                                                        $img = 'img/users/blank.png';
                                                    } ?>
                                                    <a href="#img<?= $client['id'] ?>" data-toggle="modal"><img
                                                            src="<?= $img ?>" width="90" height="90" class="" /></a>
                                                </td>
                                                <td><?= $client['study_id'] ?></td>
                                                <?php if ($client['nimregenin'] == 1) { ?>
                                                    <td>
                                                        <a href="#" class="btn btn-info">YES</a>
                                                    </td>
                                                <?php } else { ?>
                                                    <td>
                                                        <a href="#" class="btn btn-warning">NO</a>
                                                    </td>
                                                <?php } ?>
                                                <td> <?= $visit['visit_name'] ?></td>
                                                <td> <?= $client['firstname'] . ' ' . $client['lastname'] ?></td>
                                                <td><?= $client['gender'] ?></td>
                                                <td><?= $client['age'] ?></td>
                                                <td><?= $site_id ?></td>
                                                <td>
                                                    <a href="info.php?id=7&cid=<?= $client['id'] ?>" role="button"
                                                        class="btn btn-warning">Schedule</a>
                                                </td>

                                            </tr>
                                            <div class="modal fade" id="img<?= $client['id'] ?>" tabindex="-1" role="dialog"
                                                aria-labelledby="myModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <form method="post">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal"><span
                                                                        aria-hidden="true">&times;</span><span
                                                                        class="sr-only">Close</span></button>
                                                                <h4>Client Image</h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <img src="<?= $img ?>" width="350">
                                                            </div>
                                                            <div class="modal-footer">
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
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="dr"><span></span></div>

                <div class="row">

                </div>
            </div>

        </div>
    </div>
    <script>
        <?php if ($user->data()->pswd == 0) { ?>
            $(window).on('load', function () {
                $("#change_password_n").modal({
                    backdrop: 'static',
                    keyboard: false
                }, 'show');
            });
        <?php } ?>
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>

</html>