<?php
require_once 'php/core/init.php';
$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();
$users = $override->getData('user');
if ($user->isLoggedIn()) {
    if ($user->data()->power == 1) {
        $registered = $override->getCount('clients', 'status', 1);
        $not_screened = $override->countData('clients', 'status', 1, 'screened', 0);
        $all = $override->getNo('clients');
        $deleted = $override->getCount('clients', 'status', 0);
    } else {
        $registered = $override->countData('clients', 'status', 1, 'site_id', $user->data()->site_id);
        $not_screened = $override->countData2('clients', 'status', 1,'screened', 0, 'site_id', $user->data()->site_id);
        $all = $override->getCount('clients','site_id', $user->data()->site_id);
        $deleted = $override->countData('clients', 'status', 0, 'site_id', $user->data()->site_id);
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
                    <li>
                        <a href="info.php?id=2">
                            <span class="glyphicon glyphicon-share"></span><span class="text">Manage</span>
                        </a>
                    </li>
                    <li class="active">
                        <a href="info.php?id=20" target="_blank">
                            <span class="isw-download"></span><span class="text">Download Data</span>
                        </a>
                    </li>
                    <li class="active">
                        <a href="report.php" target="_blank">
                            <span class="text">Report  </span> 
                        </a>
                    </li>
                    <li class="active">
                        <a href="report1.php" target="_blank">
                            <span class="text">Report 1 </span> 
                        </a>
                    </li>
                    <li class="active">
                        <a href="report2.php" target="_blank">
                            <span class="text">Report 2 </span> 
                        </a>
                    </li>
                    <li class="active">
                        <a href="report3.php" target="_blank">
                            <span class="text">Report 3 </span> 
                        </a>
                    </li>
                    <li class="active">
                        <a href="report4.php" target="_blank">
                            <span class="text">Report 4 </span> 
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
                        <span class="glyphicon glyphicon-user"></span><span class="text">Register New  Client</span>
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

                <!-- <li class="active">
                    <a href="info.php?id=3&status=7" target="_blank">
                        <span class="isw-download"></span><span class="text">Pending Clients Visits</span>
                    </a>
                </li> -->
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