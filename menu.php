<?php
require_once 'php/core/init.php';
$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();
$users = $override->getData('user');
if ($user->isLoggedIn()) {
    $tables = $override->AllTablesCont();
    $AllDatabasesCount = $override->AllDatabasesCount();

    if ($user->data()->power == 1) {
        $registered = $override->getCount('clients', 'status', 1);
        $not_screened = $override->countData('clients', 'status', 1, 'screened', 0);
        $all = $override->getNo('clients');
        $deleted = $override->getCount('clients', 'status', 0);
        $visits = $override->getNo1('visit', 'expected_date', date('Y-m-d'), 'status', 0);
        $visits_DAY0 = $override->getNo2('visit', 'expected_date', date('Y-m-d'), 'status', 0, 'visit_code', 'D0');
        $visits_DAY7 = $override->getNo2('visit', 'expected_date', date('Y-m-d'), 'status', 0, 'visit_code', 'D7');
        $visits_DAY14 = $override->getNo2('visit', 'expected_date', date('Y-m-d'), 'status', 0, 'visit_code', 'D14');
        $visits_DAY30 = $override->getNo2('visit', 'expected_date', date('Y-m-d'), 'status', 0, 'visit_code', 'D30');
        $visits_DAY60 = $override->getNo2('visit', 'expected_date', date('Y-m-d'), 'status', 0, 'visit_code', 'D60');
        $visits_DAY90 = $override->getNo2('visit', 'expected_date', date('Y-m-d'), 'status', 0, 'visit_code', 'D90');
        $visits_DAY90 = $override->getNo2('visit', 'expected_date', date('Y-m-d'), 'status', 0, 'visit_code', 'D90');
        $visits_DAY120 = $override->getNo2('visit', 'expected_date', date('Y-m-d'), 'status', 0, 'visit_code', 'D120');


        $schedule = 1;
        $today = date('Y-m-d');
        $nxt_visit_date = date('Y-m-d', strtotime($today . ' + ' . $schedule . ' days'));
        $nxt_visit = $override->getCount1('visit', 'expected_date', $nxt_visit_date, 'status', 0);


        $DAY = $override->getNews0('visit', 'expected_date', date('Y-m-d'));
        $CRF = $override->get('crf1', 'patient_id', $DAY0['client_id']);
        $resultAll = array_diff($DAY, $CRF);
        $resultAll = count($resultAll);


        $DAY0 = $override->getNews1('visit', 'expected_date', date('Y-m-d'), 'visit_code', 'D0');
        $CRF1 = $override->getNews('crf1', 'patient_id', $DAY0['client_id'], 'vcode', 'D0');
        $resultCrfDay0 = array_diff($DAY0, $CRF1);
        $resultCrf1Day0 = count($resultCrf1Day0);

        $DAY0 = $override->getNews1('visit', 'expected_date', date('Y-m-d'), 'visit_code', 'D0');
        $CRF2 = $override->getNews('crf2', 'patient_id', $DAY0['client_id'], 'vcode', 'D0');
        $resultCrf2Day0 = array_diff($DAY0, $CRF2);
        $resultCrf2Day0 = count($resultCrf2Day0);


        $DAY0 = $override->getNews1('visit', 'expected_date', date('Y-m-d'), 'visit_code', 'D0');
        $CRF3 = $override->getNews('crf3', 'patient_id', $DAY0['client_id'], 'vcode', 'D0');
        $resultCrf3Day0 = array_diff($DAY0, $CRF3);
        $resultCrf3Day0 = count($resultCrf3Day0);


        $DAY0 = $override->getNews1('visit', 'expected_date', date('Y-m-d'), 'visit_code', 'D0');
        $CRF4 = $override->getNews('crf4', 'patient_id', $DAY0['client_id'], 'vcode', 'D0');
        $resultCrf4Day0 = array_diff($DAY0, $CRF4);
        $resultCrf4Day0 = count($resultCrf4Day0);


        $DAY0 = $override->getNews1('visit', 'expected_date', date('Y-m-d'), 'visit_code', 'D0');
        $CRF5 = $override->getNews('crf5', 'patient_id', $DAY0['client_id'], 'vcode', 'D0');
        $resultCrf5Day0 = array_diff($DAY0, $CRF5);
        $resultCrf5Day0 = count($resultCrf5Day0);


        $DAY0 = $override->getNews1('visit', 'expected_date', date('Y-m-d'), 'visit_code', 'D0');
        $CRF6 = $override->getNews('crf6', 'patient_id', $DAY0['client_id'], 'vcode', 'D0');
        $resultCrf6Day0 = array_diff($DAY0, $CRF6);
        $resultCrf6Day0 = count($resultCrf6Day0);

        $DAY0 = $override->getNews1('visit', 'expected_date', date('Y-m-d'), 'visit_code', 'D0');
        $CRF7 = $override->getNews('crf7', 'patient_id', $DAY0['client_id'], 'vcode', 'D0');
        $resultCrf7Day0 = array_diff($DAY0, $CRF7);
        $resultCrf7Day0 = count($resultCrf7Day0);
    } else {
        $registered = $override->countData('clients', 'status', 1, 'site_id', $user->data()->site_id);
        $not_screened = $override->countData2('clients', 'status', 1, 'screened', 0, 'site_id', $user->data()->site_id);
        $all = $override->getCount('clients', 'site_id', $user->data()->site_id);
        $deleted = $override->countData('clients', 'status', 0, 'site_id', $user->data()->site_id);
        $visits = $override->getNo1('visit', 'expected_date', date('Y-m-d'), 'status', 0);
        // NO SITE_ID COLUMN ON VISIT TABLE
        // $visits = $override->get3('visit', 'expected_date', date('Y-m-d'), 'status', 0, 'site_id', $user->data()->site_id);
        $visits_DAY0 = $override->getNo2('visit', 'expected_date', date('Y-m-d'), 'status', 0, 'visit_code', 'D0');
        $visits_DAY7 = $override->getNo2('visit', 'expected_date', date('Y-m-d'), 'status', 0, 'visit_code', 'D7');
        $visits_DAY14 = $override->getNo2('visit', 'expected_date', date('Y-m-d'), 'status', 0, 'visit_code', 'D14');
        $visits_DAY30 = $override->getNo2('visit', 'expected_date', date('Y-m-d'), 'status', 0, 'visit_code', 'D30');
        $visits_DAY60 = $override->getNo2('visit', 'expected_date', date('Y-m-d'), 'status', 0, 'visit_code', 'D60');
        $visits_DAY90 = $override->getNo2('visit', 'expected_date', date('Y-m-d'), 'status', 0, 'visit_code', 'D90');
        $visits_DAY90 = $override->getNo2('visit', 'expected_date', date('Y-m-d'), 'status', 0, 'visit_code', 'D90');
        $visits_DAY120 = $override->getNo2('visit', 'expected_date', date('Y-m-d'), 'status', 0, 'visit_code', 'D120');


        $schedule = 1;
        $today = date('Y-m-d');
        $nxt_visit_date = date('Y-m-d', strtotime($today . ' + ' . $schedule . ' days'));
        $nxt_visit = $override->countData('visit', 'expected_date', $nxt_visit_date, 'status', 0);
    }
} else {
    Redirect::to('index.php');
}
?>
<div class="menu">

    <div class="breadLine">
        <div class="arrow"></div>
        <div class="adminControl active">
            Hi, <?= $user->data()->firstname ?>
        </div>
    </div>

    <div class="admin">
        <div class="image">
            <img src="img/users/blank.png" class="img-thumbnail" />
        </div>
        <ul class="control">
            <li><span class="glyphicon glyphicon-comment"></span> <a href="#">Messages</a></li>
            <li><span class="glyphicon glyphicon-cog"></span> <a href="profile.php">Profile</a></li>
            <li><span class="glyphicon glyphicon-share-alt"></span> <a href="logout.php">Logout</a></li>
        </ul>
        <div class="info">
            <span>Welcome back! Your last visit: <?= $user->data()->last_login ?></span>
        </div>
    </div>

    <ul class="navigation">
        <li class="active">
            <a href="dashboard.php">
                <span class="isw-grid"></span><span class="text">Dashboard</span>
            </a>
        </li>
        <?php if ($user->data()->accessLevel == 1) { ?>
            <li class="openable">
                <a href="#"><span class="isw-user"></span><span class="text">Staff</span></a>
                <ul>
                    <li>
                        <a href="add.php?id=1">
                            <span class="glyphicon glyphicon-user"></span><span class="text">Add staff</span>
                        </a>
                    </li>
                    <li>
                        <a href="info.php?id=1">
                            <span class="glyphicon glyphicon-registration-mark"></span><span class="text">Manage staff</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="openable">
                <a href="#"><span class="isw-tag"></span><span class="text">Extra</span></a>
                <ul>
                    <li>
                        <a href="add.php?id=2">
                            <span class="glyphicon glyphicon-user"></span><span class="text">Add Position</span>
                        </a>
                    </li>
                    <li>
                        <a href="add.php?id=5">
                            <span class="glyphicon glyphicon-floppy-disk"></span><span class="text">Study</span>
                        </a>
                    </li>
                    <li>
                        <a href="add.php?id=6">
                            <span class="glyphicon glyphicon-floppy-disk"></span><span class="text">Site</span>
                        </a>
                    </li>
                    <li>
                        <a href="info.php?id=5">
                            <span class="glyphicon glyphicon-list"></span><span class="text">Study IDs</span>
                        </a>
                    </li>
                    <!-- <li>
                        <a href="info.php?id=23">
                            <span class="glyphicon glyphicon-list"></span><span class="text">Clear Data on Table</span>
                            <span class="badge badge-secondary badge-pill"><?= $tables ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="info.php?id=24">
                            <span class="glyphicon glyphicon-list"></span><span class="text">Export Data Tables</span>
                            <span class="badge badge-secondary badge-pill"><?= $tables ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="info.php?id=25">
                            <span class="glyphicon glyphicon-list"></span><span class="text">Export Database</span>
                            <span class="badge badge-secondary badge-pill"><?= $AllDatabasesCount ?></span>
                        </a>
                    </li> -->
                    <li>
                        <a href="info.php?id=2">
                            <span class="glyphicon glyphicon-share"></span><span class="text">Manage</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="openable">
                <a href="#"><span class="isw-tag"></span><span class="text">Reports</span></a>
                <ul>
                    <li class="active">
                        <a href="info.php?id=20">
                            <span class="isw-download"></span><span class="text">Download Data</span>
                        </a>
                    </li>
                    <li class="active">
                        <a href="report.php">
                            <span class="text">Report ( TABLE 0) </span>
                        </a>
                    </li>
                    <li class="active">
                        <a href="report1.php">
                            <span class="text">Report 1 ( TABLE 1)</span>
                        </a>
                    </li>
                    <li class="active">
                        <a href="report2.php">
                            <span class="text">Report 2 ( TABLE 2)</span>
                        </a>
                    </li>
                    <li class="active">
                        <a href="report3.php">
                            <span class="text">Report 3 (TABLE 3) </span>
                        </a>
                    </li>
                    <li class="active">
                        <a href="report4.php">
                            <span class="text">Report 4 (TABLE 4)</span>
                        </a>
                    </li>

                    <li class="active">
                        <a href="report4.php">
                            <span class="text">Report 4 (TABLE 4) ( DAY 0)</span>
                        </a>
                    </li>

                    <li class="active">
                        <!-- <a href="report5.php" target="_blank"> -->
                        <a href="report5.php">
                            <span class="text">Report 5 (TABLE 5) ( DAY 7)</span>
                        </a>
                    </li>

                    <li class="active">
                        <!-- <a href="report5.php" target="_blank"> -->
                        <a href="report6.php">
                            <span class="text">Report 6 (TABLE 6) ( Screening with Control)</span>
                        </a>
                    </li>

                    <li class="active">
                        <!-- <a href="report5.php" target="_blank"> -->
                        <a href="report6_1.php">
                            <span class="text">Report 6_1 (TABLE 6_1) ( Screening with Control)</span>
                        </a>
                    </li>

                    <li class="active">
                        <!-- <a href="report5.php" target="_blank"> -->
                        <a href="report6_2.php">
                            <span class="text">Report 6_2(TABLE 6_2) ( Screening with Control)</span>
                        </a>
                    </li>
                    <li class="active">
                        <!-- <a href="report5.php" target="_blank"> -->
                        <a href="report7.php">
                            <span class="text">Report 7(TABLE 7) ( Screening with Control)</span>
                        </a>
                    </li>
                    <li class="active">
                        <!-- <a href="report5.php" target="_blank"> -->
                        <a href="report8.php">
                            <span class="text">Report 8(TABLE 8) ( Screening with Control)</span>
                        </a>
                    </li>
                    <li class="active">
                        <!-- <a href="report5.php" target="_blank"> -->
                        <a href="reports9.php">
                            <span class="text">Report 9(TABLE 9) ( Screening with Control)</span>
                        </a>
                    </li>
                    <li class="active">
                        <!-- <a href="report5.php" target="_blank"> -->
                        <a href="reports10.php">
                            <span class="text">Report 10(TABLE 10) ( Screening with Control)</span>
                        </a>
                    </li>
                    <li class="active">
                        <!-- <a href="report5.php" target="_blank"> -->
                        <a href="reports11.php">
                            <span class="text">Report 11(TABLE 11) ( Screening with Control)</span>
                        </a>
                    </li>
                    <li class="active">
                        <!-- <a href="report5.php" target="_blank"> -->
                        <a href="reports12.php">
                            <span class="text">Report 12(TABLE 12) ( Screening with Control)</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="openable">
                <a href="#"><span class="isw-tag"></span><span class="text">Pending Visits</span></a>
                <ul>
                    <li class="active">
                        <!-- <a href="info.php?id=21" target="_blank"> -->
                        <a href="info.php?id=21">
                            <span class="text">All Visits </span>
                            <span class="badge badge-secondary badge-pill"><?= $visits ?></span>
                        </a>
                    </li>
                    <li class="active">
                        <a href="info.php?id=21&day=D0">
                            <span class="text">Day 0 Visits </span>
                            <span class="badge badge-secondary badge-pill"><?= $visits_DAY0 ?></span>
                        </a>
                    </li>
                    <li class="active">
                        <a href="info.php?id=21&day=D7">
                            <span class="text">Day 7 Visits </span>
                            <span class="badge badge-secondary badge-pill"><?= $visits_DAY7 ?></span>
                        </a>
                    </li>
                    <li class="active">
                        <a href="info.php?id=21&day=D14">
                            <span class="text">Day 14 Visits </span>
                            <span class="badge badge-secondary badge-pill"><?= $visits_DAY14 ?></span>
                        </a>
                    </li>
                    <li class="active">
                        <a href="info.php?id=21&day=D30">
                            <span class="text">Day 30 Visits </span>
                            <span class="badge badge-secondary badge-pill"><?= $visits_DAY30 ?></span>
                        </a>
                    </li>
                    <li class="active">
                        <a href="info.php?id=21&day=D60">
                            <span class="text">Day 60 Visits </span>
                            <span class="badge badge-secondary badge-pill"><?= $visits_DAY60 ?></span>
                        </a>
                    </li>
                    <li class="active">
                        <a href="info.php?id=21&day=D90">
                            <span class="text">Day 90 Visits </span>
                            <span class="badge badge-secondary badge-pill"><?= $visits_DAY90 ?></span>
                        </a>
                    </li>
                    <li class="active">
                        <a href="info.php?id=21&day=D120">
                            <span class="text">Day 120 Visits </span>
                            <span class="badge badge-secondary badge-pill"><?= $visits_DAY120 ?></span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="openable">
                <a href="#"><span class="isw-tag"></span><span class="text">Tomorrow Visits</span></a>
                <ul>
                    <li class="active">
                        <a href="info.php?id=21&day=Nxt">
                            <span class="text">All Visits </span>
                            <span class="badge badge-secondary badge-pill"><?= $nxt_visit ?></span>
                        </a>
                    </li>

                </ul>

                <ul>
                    <li class="active">
                        <a href="info.php?id=26">
                            <span class="text">Download Missing Crf </span>
                            <span class="badge badge-secondary badge-pill"><?= $MissingCrfNo ?></span>
                        </a>
                    </li>

                </ul>

                <ul>
                    <li class="active">
                        <a href="info.php?id=27">
                            <span class="text">Download Missing Crf Visits </span>
                            <span class="badge badge-secondary badge-pill"><?= $MissingCrfNo1 ?></span>
                        </a>
                    </li>

                </ul>

                <ul>
                    <li class="active">
                        <a href="info.php?id=28">
                            <span class="text">Download Missing Crf All</span>
                            <span class="badge badge-secondary badge-pill"><?= $MissingCrfNo2 ?></span>
                        </a>
                    </li>

                </ul>
            </li>

            <li class="openable">
                <a href="#"><span class="isw-tag"></span><span class="text">Missing Crfs</span></a>
                <ul>
                    <li class="active">
                        <a href="info.php?id=22">
                            <span class="text">All crfs </span>
                            <span class="badge badge-secondary badge-pill"><?= $resultAll ?></span>
                        </a>
                    </li>
                    <li class="active">
                        <a href="info.php?id=22">
                            <span class="text">crfs 1</span>
                            <span class="badge badge-secondary badge-pill"><?= $resultCrf1Day0 ?></span>
                        </a>
                    </li>
                    <li class="active">
                        <a href="info.php?id=22">
                            <span class="text">crfs 2</span>
                            <span class="badge badge-secondary badge-pill"><?= $resultCrf2Day0 ?></span>
                        </a>
                    </li>
                    <li class="active">
                        <a href="info.php?id=22">
                            <span class="text">crfs 3</span>
                            <span class="badge badge-secondary badge-pill"><?= $resultCrf3Day0 ?></span>
                        </a>
                    </li>
                    <li class="active">
                        <a href="info.php?id=22">
                            <span class="text">crfs 4</span>
                            <span class="badge badge-secondary badge-pill"><?= $resultCrf4Day0 ?></span>
                        </a>
                    </li>
                    <li class="active">
                        <a href="info.php?id=22">
                            <span class="text">crfs 5</span>
                            <span class="badge badge-secondary badge-pill"><?= $resultCrf5Day0 ?></span>
                        </a>
                    </li>
                    <li class="active">
                        <a href="info.php?id=22">
                            <span class="text">crfs 6</span>
                            <span class="badge badge-secondary badge-pill"><?= $resultCrf6Day0 ?></span>
                        </a>
                    </li>
                    <li class="active">
                        <a href="info.php?id=22">
                            <span class="text">crfs 7</span>
                            <span class="badge badge-secondary badge-pill"><?= $resultCrf7Day0 ?></span>
                        </a>
                    </li>


                </ul>
            </li>

        <?php } ?>

        <li class="openable">
            <a href="#"><span class="isw-users"></span><span class="text">Clients Registration</span></a>
            <ul>
                <li>
                    <a href="add.php?id=4">
                        <span class="glyphicon glyphicon-user"></span><span class="text">Register New Client</span>
                    </a>
                </li>

                <li>
                    <a href="info.php?id=3&status=5">
                        <span class="glyphicon glyphicon-registration-mark"></span><span class="text">Registred Clients</span>
                        <span class="badge badge-secondary badge-pill"><?= $registered ?></span>
                    </a>
                </li>

                <li>
                    <a href="info.php?id=3&status=6">
                        <span class="glyphicon glyphicon-registration-mark"></span><span class="text">Clients Not Screened</span>
                        <span class="badge badge-secondary badge-pill"><?= $not_screened ?></span>
                    </a>
                </li>

                <?php if ($user->data()->accessLevel == 1) { ?>

                    <li>
                        <a href="info.php?id=3&status=7">
                            <span class="glyphicon glyphicon-registration-mark"></span><span class="text">All Clients</span>
                            <span class="badge badge-secondary badge-pill"><?= $all ?></span>
                        </a>
                    </li>

                    <li>
                        <a href="info.php?id=3&status=8">
                            <span class="glyphicon glyphicon-registration-mark"></span><span class="text">Deleted Clients</span>
                            <span class="badge badge-secondary badge-pill"><?= $deleted ?></span>
                        </a>
                    </li>

                <?php } ?>
                <!-- 
               <li class="active">
                    <a href="info.php?id=3&status=7" target="_blank">
                        <span class="isw-download"></span><span class="text">Pending Clients Visits</span>
                    </a>
                </li>  -->

                <li class="active">
                    <a href="info.php?id=3&status=7" target="_blank">
                        <span class="isw-download"></span><span class="text">Pending Clients Visits</span>
                    </a>
                </li>
            </ul>
        </li>

        <?php if ($user->data()->accessLevel == 1  && $user->data()->power == 1) { ?>
            <li class="active">
                <a href="zebra.php" target="_blank">
                    <span class="isw-print"></span><span class="text">Zebra Print</span>
                </a>
            </li>
        <?php } ?>

    </ul>

    <div class="dr"><span></span></div>

    <div class="widget-fluid">
        <div id="menuDatepicker"></div>
    </div>

    <div class="dr"><span></span></div>

    <div class="widget">

        <div class="input-group">
            <input id="appendedInputButton" class="form-control" type="text">
            <div class="input-group-btn">
                <button class="btn btn-default" type="button">Search</button>
            </div>
        </div>

    </div>

    <div class="dr"><span></span></div>

    <div class="widget-fluid">

        <div class="wBlock clearfix">
            <div class="dSpace">
                <h3>Studies</h3>
                <span class="number"></span>
                <span><b>Ongoing</b></span>
                <span><b>Ended</b></span>
            </div>
        </div>

    </div>

    <div class="modal fade" id="fModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4>Search Report</h4>
                </div>
                <form method="post">
                    <div class="modal-body modal-body-np">
                        <div class="row">
                            <div class="block-fluid">
                                <div class="row-form clearfix">
                                    <div class="col-md-3">Start Date:</div>
                                    <div class="col-md-9">
                                        <input value="" class="validate[required,custom[date]]" type="text" name="start" id="date" />
                                        <span>Example: 2010-12-01</span>
                                    </div>
                                </div>
                                <div class="row-form clearfix">
                                    <div class="col-md-3">End Date:</div>
                                    <div class="col-md-9">
                                        <input value="" class="validate[required,custom[date]]" type="text" name="start" id="date" />
                                        <span>Example: 2010-12-01</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="submit" class="btn btn-info" value="Search" aria-hidden="true">
                        <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>