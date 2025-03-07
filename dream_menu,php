<?php
require_once 'php/core/init.php';
$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();



$users = $override->getData('user');
if ($user->isLoggedIn()) {
    if (Input::exists('post')) {

        if (Input::get('search_by_site')) {
            $validate = new validate();
            $validate = $validate->check($_POST, array(
                'site_id' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {

                $url = 'index1.php?&site_id=' . Input::get('site_id');
                Redirect::to($url);
                $pageError = $validate->errors();
            }
        }
    }
    $sites = $override->getNo('sites');
    $position = $override->getNo('position');

    $Offline_Users = $override->getNo('users');

    $staff_all = $override->getNo('user');
    $staff_active = $override->getCount('user', 'status', 1);
    $staff_inactive = $override->getCount('user', 'status', 0);
    $staff_lock_active = $override->getCount1('user', 'status', 1, 'count', 4);
    $staff_lock_inactive = $override->getCount1('user', 'status', 0, 'count', 4);


    if ($user->data()->accessLevel == 1) {
        if ($_GET['facility_id'] != null) {
            $screened = $override->countData('screening', 'status', 1, 'facility_id', $_GET['facility_id']);
            $eligible = $override->countData1('screening', 'status', 1, 'eligible', 1, 'facility_id', $_GET['facility_id']);
            $enrolled = $override->countData('enrollment_form', 'status', 1, 'facility_id', $_GET['facility_id']);
            // $end = $override->countData('termination', 'status', 1, 'facility_id', $_GET['facility_id']);

            // $Incompletes = $override->countData1('screening', 'status', 1, 'form_status', 1, 'facility_id', $_GET['facility_id']);
            // $Incompletes_Screened = $override->countData1('screening', 'status', 1, 'form_status', 1, 'facility_id', $_GET['facility_id']);
            // $Incompletes_Enrollment = $override->countData('enrollment_form', 'status', 1, 'facility_id', $_GET['facility_id']);
            // $Incompletes_Respiratory = $override->countData('respiratory', 'status', 1, 'facility_id', $_GET['facility_id']);
            // $Incompletes_Diagnosis = $override->countData('diagnosis_test', 'status', 1, 'facility_id', $_GET['facility_id']);
            // $Incompletes_Diagnosis = $override->countData('diagnosis', 'status', 1, 'facility_id', $_GET['facility_id']);

            // $Completes = $override->countData1('screening', 'status', 1, 'form_status', 1, 'facility_id', $_GET['facility_id']);
            // $Completes_Screened = $override->countData1('screening', 'status', 1, 'form_status', 1, 'facility_id', $_GET['facility_id']);
            // $Completes_Enrollment = $override->countData('enrollment_form', 'status', 1, 'facility_id', $_GET['facility_id']);
            // $Completes_Respiratory = $override->countData('respiratory', 'status', 1, 'facility_id', $_GET['facility_id']);
            // $Completes_Diagnosis = $override->countData('diagnosis_test', 'status', 1, 'facility_id', $_GET['facility_id']);
            // $Completes_Diagnosis = $override->countData('diagnosis', 'status', 1, 'facility_id', $_GET['facility_id']);

            // $Un_Verified = $override->countData1('screening', 'status', 1, 'form_status', 1, 'facility_id', $_GET['facility_id']);
            // $Un_Verified_Screened = $override->countData1('screening', 'status', 1, 'form_status', 1, 'facility_id', $_GET['facility_id']);
            // $Un_Verified_Enrollment = $override->countData('enrollment_form', 'status', 1, 'facility_id', $_GET['facility_id']);
            // $Un_Verified_Respiratory = $override->countData('respiratory', 'status', 1, 'facility_id', $_GET['facility_id']);
            // $Un_Verified_Diagnosis = $override->countData('diagnosis_test', 'status', 1, 'facility_id', $_GET['facility_id']);
            // $Un_Verified_Diagnosis = $override->countData('diagnosis', 'status', 1, 'facility_id', $_GET['facility_id']);

            // $Verified = $override->countData1('screening', 'status', 1, 'form_status', 1, 'facility_id', $_GET['facility_id']);
            // $Verified_Screened = $override->countData('screening', 'status', 1, 'facility_id', $_GET['facility_id']);
            // $Verified_Enrollment = $override->countData('enrollment_form', 'status', 1, 'facility_id', $_GET['facility_id']);
            // $Verified_Respiratory = $override->countData('respiratory', 'status', 1, 'facility_id', $_GET['facility_id']);
            // $Verified_Diagnosis = $override->countData('diagnosis_test', 'status', 1, 'facility_id', $_GET['facility_id']);
            // $Verified_Diagnosis = $override->countData('diagnosis', 'status', 1, 'facility_id', $_GET['facility_id']);
        } else {
            $screened = $override->getCount('screening', 'status', 1);
            $eligible = $override->getCount1('screening', 'status', 1, 'eligible', 1);
            $enrolled = $override->getCount('enrollment_form', 'status', 1);
            // $end = $override->getCount('termination', 'status', 1);

            // $Incompletes_Screened = $override->countData1('screening', 'status', 1, 'form_status', 1, 'facility_id', $_GET['facility_id']);
            // $Incompletes_Enrollment = $override->countData('enrollment_form', 'status', 1, 'facility_id', $_GET['facility_id']);
            // $Incompletes_Respiratory = $override->countData('respiratory', 'status', 1, 'facility_id', $_GET['facility_id']);
            // $Incompletes_Diagnosis = $override->countData('diagnosis_test', 'status', 1, 'facility_id', $_GET['facility_id']);
            // $Incompletes_Diagnosis = $override->countData('diagnosis', 'status', 1, 'facility_id', $_GET['facility_id']);

            // $Completes_Screened = $override->countData1('screening', 'status', 1, 'facility_id', 'form_status', 1, $_GET['facility_id']);
            // $Completes_Enrollment = $override->countData('enrollment_form', 'status', 1, 'facility_id', $_GET['facility_id']);
            // $Completes_Respiratory = $override->countData('respiratory', 'status', 1, 'facility_id', $_GET['facility_id']);
            // $Completes_Diagnosis = $override->countData('diagnosis_test', 'status', 1, 'facility_id', $_GET['facility_id']);
            // $Completes_Diagnosis = $override->countData('diagnosis', 'status', 1, 'facility_id', $_GET['facility_id']);

            // $Un_Verified_Screened = $override->countData1('screening', 'status', 1, 'form_status', 1, 'facility_id', $_GET['facility_id']);
            // $Un_Verified_Enrollment = $override->countData('enrollment_form', 'status', 1, 'facility_id', $_GET['facility_id']);
            // $Un_Verified_Respiratory = $override->countData('respiratory', 'status', 1, 'facility_id', $_GET['facility_id']);
            // $Un_Verified_Diagnosis = $override->countData('diagnosis_test', 'status', 1, 'facility_id', $_GET['facility_id']);
            // $Un_Verified_Diagnosis = $override->countData('diagnosis', 'status', 1, 'facility_id', $_GET['facility_id']);

            // $Verified_Screened = $override->countData1('screening', 'status', 1, 'form_status', 1, 'facility_id', $_GET['facility_id']);
            // $Verified_Enrollment = $override->countData('enrollment_form', 'status', 1, 'facility_id', $_GET['facility_id']);
            // $Verified_Respiratory = $override->countData('respiratory', 'status', 1, 'facility_id', $_GET['facility_id']);
            // $Verified_Diagnosis = $override->countData('diagnosis_test', 'status', 1, 'facility_id', $_GET['facility_id']);
            // $Verified_Diagnosis = $override->countData('diagnosis', 'status', 1, 'facility_id', $_GET['facility_id']);
        }
    } else {
        $screened = $override->countData('screening', 'status', 1, 'facility_id', $user->data()->site_id);
        $eligible = $override->countData1('screening', 'status', 1, 'eligible', 1, 'facility_id', $user->data()->site_id);
        $enrolled = $override->countData('enrollment_form', 'status', 1, 'facility_id', $user->data()->site_id);
        // $end = $override->countData('termination', 'status', 1, 'facility_id', $user->data()->site_id);

        // $Incompletes_Screened = $override->countData1('screening', 'status', 1, 'form_status', 1, 'facility_id', $_GET['facility_id']);
        // $Incompletes_Enrollment = $override->countData('enrollment_form', 'status', 1, 'facility_id', $_GET['facility_id']);
        // $Incompletes_Respiratory = $override->countData('respiratory', 'status', 1, 'facility_id', $_GET['facility_id']);
        // $Incompletes_Diagnosis = $override->countData('diagnosis_test', 'status', 1, 'facility_id', $_GET['facility_id']);
        // $Incompletes_Diagnosis = $override->countData('diagnosis', 'status', 1, 'facility_id', $_GET['facility_id']);

        // $Completes_Screened = $override->countData1('screening', 'status', 1, 'form_status', 1, 'facility_id', $_GET['facility_id']);
        // $Completes_Enrollment = $override->countData('enrollment_form', 'status', 1, 'facility_id', $_GET['facility_id']);
        // $Completes_Respiratory = $override->countData('respiratory', 'status', 1, 'facility_id', $_GET['facility_id']);
        // $Completes_Diagnosis = $override->countData('diagnosis_test', 'status', 1, 'facility_id', $_GET['facility_id']);
        // $Completes_Diagnosis = $override->countData('diagnosis', 'status', 1, 'facility_id', $_GET['facility_id']);

        // $Un_Verified_Screened = $override->countData1('screening', 'status', 1, 'form_status', 1, 'facility_id', $_GET['facility_id']);
        // $Un_Verified_Enrollment = $override->countData('enrollment_form', 'status', 1, 'facility_id', $_GET['facility_id']);
        // $Un_Verified_Respiratory = $override->countData('respiratory', 'status', 1, 'facility_id', $_GET['facility_id']);
        // $Un_Verified_Diagnosis = $override->countData('diagnosis_test', 'status', 1, 'facility_id', $_GET['facility_id']);
        // $Un_Verified_Diagnosis = $override->countData('diagnosis', 'status', 1, 'facility_id', $_GET['facility_id']);

        // $Verified_Screened = $override->countData1('screening', 'status', 1, 'form_status', 1, 'facility_id', $_GET['facility_id']);
        // $Verified_Enrollment = $override->countData('enrollment_form', 'status', 1, 'facility_id', $_GET['facility_id']);
        // $Verified_Respiratory = $override->countData('respiratory', 'status', 1, 'facility_id', $_GET['facility_id']);
        // $Verified_Diagnosis = $override->countData('diagnosis_test', 'status', 1, 'facility_id', $_GET['facility_id']);
        // $Verified_Diagnosis = $override->countData('diagnosis', 'status', 1, 'facility_id', $_GET['facility_id']);
    }
} else {
    Redirect::to('index.php');
}

?>

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index1.php" class="brand-link">
        <img src="dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
            style="opacity: .8">
        <span class="brand-text font-weight-light">Dream Database</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <?php if ($user->data()->sex == 1) { ?>
                    <img src="dist/img/avatar5.png" class="img-circle elevation-2" alt="User Image">

                <?php } elseif ($user->data()->sex == 2) { ?>
                    <img src="dist/img/avatar3.png" class="img-circle elevation-2" alt="User Image">

                <?php } else { ?>
                    <img src="dist/img/avatar5.png" class="img-circle elevation-2" alt="User Image">

                <?php } ?>
            </div>
            <div class="info">
                <a href="#" class="d-block"><?= $user->data()->firstname . ' - ' . $user->data()->lastname ?></a>
            </div>
        </div>

        <!-- SidebarSearch Form -->
        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                <li class="nav-item menu-open">
                    <a href="#" class="nav-link active">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Dashboard
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="./index1.php" class="nav-link active">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Home</p>
                            </a>
                        </li>
                        <!-- <li class="nav-item">
                            <a href="./index3.php" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Dashboard v3</p>
                            </a>
                        </li> -->
                    </ul>
                </li>
                <?php if ($user->data()->power == 1) {
                    ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-copy"></i>
                            <span class="badge badge-info right"><?= $staff_all; ?></span>
                            <p>
                                Staff <i class="fas fa-angle-left right"></i>

                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="add.php?id=1" class="nav-link">
                                    <i class="nav-icon fas fa-th"></i>
                                    <p>
                                        Register
                                        <span class="right badge badge-danger">New Staff</span>
                                    </p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="info.php?id=1" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <span class="badge badge-info right"><?= $staff_all; ?></span>
                                    <p>All Staff</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="info.php?id=1&status=1" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <span class="badge badge-info right"><?= $staff_active; ?></span>
                                    <p>Active</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="info.php?id=1&status=2" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <span class="badge badge-info right"><?= $staff_inactive; ?></span>
                                    <p>Inactive</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="info.php?id=1&status=3" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <span class="badge badge-info right"><?= $staff_lock_active; ?></span>
                                    <p>Locked And Active</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="info.php?id=1&status=4" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <span class="badge badge-info right"><?= $staff_lock_inactive; ?></span>
                                    <p>Locked And Inactive</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-copy"></i>
                            <span class="badge badge-info right"><?= $position; ?></span>
                            <p>
                                Positions <i class="fas fa-angle-left right"></i>

                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="add.php?id=5" class="nav-link">
                                    <i class="nav-icon fas fa-th"></i>
                                    <p>
                                        Add
                                        <span class="right badge badge-danger">New Position</span>
                                    </p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="info.php?id=5" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <span class="badge badge-info right"><?= $position; ?></span>
                                    <p>List of Positions</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-copy"></i>
                            <span class="badge badge-info right"><?= $sites; ?></span>
                            <p>
                                Site <i class="fas fa-angle-left right"></i>

                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="add.php?id=2" class="nav-link">
                                    <i class="nav-icon fas fa-th"></i>
                                    <p>
                                        Add
                                        <span class="right badge badge-danger">New Site</span>
                                    </p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="info.php?id=2" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <span class="badge badge-info right"><?= $sites; ?></span>
                                    <p>List of Sites</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php } ?>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-copy"></i>
                        <span class="badge badge-info right"><?= $screened; ?></span>
                        <p>
                            Patients <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <!-- <a href="add.php?id=13&status=1&sid=&facility_id=<?= $user->data()->site_id ?>&page=<?= $_GET['page'] ?>"
                                class="nav-link"> -->
                            <a href="add.php?id=13&status=1" class="nav-link">
                                <i class="nav-icon fas fa-th"></i>
                                <p>
                                    Add
                                    <span class="right badge badge-success">New Patient</span>
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="info.php?id=3&status=1&sid=<?= $_GET['sid'] ?>&facility_id=<?= $user->data()->site_id ?>&page=<?= $_GET['page'] ?>"
                                class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <span class="badge badge-info right"><?= $screened; ?></span>
                                <p>Screened Patients</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="info.php?id=3&status=2&sid=<?= $_GET['sid'] ?>&facility_id=<?= $user->data()->site_id ?>&page=<?= $_GET['page'] ?>"
                                class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <span class="badge badge-info right"><?= $eligible; ?></span>
                                <p>Eligible Patients</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="info.php?id=3&status=3&sid=<?= $_GET['sid'] ?>&facility_id=<?= $user->data()->site_id ?>&page=<?= $_GET['page'] ?>"
                                class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <span class="badge badge-info right"><?= $enrolled; ?></span>
                                <p>Enrolled Patients</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="info.php?id=3&status=4&sid=<?= $_GET['sid'] ?>&facility_id=<?= $user->data()->site_id ?>&page=<?= $_GET['page'] ?>"
                                class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <span class="badge badge-info right"><?= $end; ?></span>
                                <p>Terminated Patients</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-header">Records</li>
                <?php foreach ($override->get('form_completness', 'status', 1) as $form_status) { ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-circle"></i>
                            <!-- <span class="badge badge-info right"><?= $override->countRowsWithStatus($form_status['id']); ?></span> -->
                            <p>
                                <?= $form_status['name']; ?>
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php foreach ($override->AllTables() as $table_name) {
                                if (
                                    $table_name['Tables_in_dream'] == 'screening' || $table_name['Tables_in_dream'] == 'enrollment_form' || $table_name['Tables_in_dream'] == 'respiratory' ||
                                    $table_name['Tables_in_dream'] == 'diagnosis' ||
                                    $table_name['Tables_in_dream'] == 'diagnosis_test'
                                ) {
                                    if (
                                        $table_name['Tables_in_dream'] == 'screening'
                                    ) {
                                        $table = 'Screening';
                                    } else if ($table_name['Tables_in_dream'] == 'enrollment_form') {
                                        $table = 'Enrollment';
                                    } else if ($table_name['Tables_in_dream'] == 'respiratory') {
                                        $table = 'Laboratory (Clinic)';
                                    } else if ($table_name['Tables_in_dream'] == 'diagnosis_test') {
                                        $table = 'Laboratory (Zonal/CTRL)';
                                    } else if ($table_name['Tables_in_dream'] == 'diagnosis') {
                                        $table = 'Diagnosis';
                                    }
                                    ?>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <!-- <span class="badge badge-info right"> -->
                                                <!-- <?= $override->countData2($table_name['Tables_in_dream'], 'status', 1, 'form_status', $form_status['id'], 'facility_id', $user->data()->site_id); ?> -->
                                            <!-- </span> -->
                                            <p><?= $table; ?></p>
                                        </a>
                                    </li>
                                <?php }
                            } ?>
                        </ul>
                    </li>
                <?php } ?>
                <?php if ($user->data()->power == 1) { ?>

                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-copy"></i>
                            <span class="badge badge-info right"><?= $validations; ?></span>
                            <p>
                                Validation Tool <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="add.php?id=17&status=1" class="nav-link">
                                    <i class="nav-icon fas fa-th"></i>
                                    <p>
                                        Add
                                        <span class="right badge badge-danger">New Validation</span>
                                    </p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="info.php?id=16&status=1" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <span class="badge badge-info right"><?= $validations; ?></span>
                                    <p>Total Validations</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-copy"></i>
                            <span class="badge badge-info right"><?= $validations; ?></span>
                            <p>
                                Test Offline Mode <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="add.php?id=18" class="nav-link">
                                    <i class="nav-icon fas fa-th"></i>
                                    <p>
                                        Add
                                        <span class="right badge badge-danger">New User</span>
                                    </p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="info.php?id=17" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <span class="badge badge-info right"><?= $Offline_Users; ?></span>
                                    <p>Total Users</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php } ?>
                <li class="nav-item">
                    <!-- <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-copy"></i>
                        <span class="badge badge-info right"><?= $override->getCount('sites', 'status', 1); ?></span>
                        <p>
                            Facilities <i class="fas fa-angle-left right"></i>
                        </p>
                    </a> -->
                    <ul class="nav nav-treeview">
                        <?php if ($user->data()->power == 1) {
                            ?>
                            <li class="nav-item">
                                <a href="add.php?id=3" class="nav-link">
                                    <i class="nav-icon fas fa-th"></i>
                                    <p>
                                        Add
                                        <span class="right badge badge-danger">New Client</span>
                                    </p>
                                </a>
                            </li>
                        <?php } ?>

                        <li class="nav-item">
                            <a href="info.php?id=11" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <span class="badge badge-info right"></span>
                                <p>List of Facilities</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php
                if ($user->data()->power == 1) {
                    ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-copy"></i>
                            <p>
                                Data <i class="fas fa-angle-left right"></i>

                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="data.php?id=1&status=1" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <span class="badge badge-info right"><?= $registered1; ?></span>
                                    <p>Download</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-copy"></i>
                            <p>
                                Clear Data <i class="fas fa-angle-left right"></i>

                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="info.php?id=14" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>List of Tables</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="info.php?id=15" class="nav-link">
                            <i class="nav-icon fas fa-copy"></i>
                            <p>
                                Unset Study ID <i class="fas fa-angle-left right"></i>

                            </p>
                        </a>
                        <!-- <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="info.php?id=15" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>List of Tables</p>
                                </a>
                            </li>
                        </ul> -->
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-copy"></i>
                            <p>
                                Reports <i class="fas fa-angle-left right"></i>

                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="summary.php" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <span class="badge badge-info right"></span>
                                    <p>Summary</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="pids.php" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <span class="badge badge-info right"><?= $registered1; ?></span>
                                    <p>PID's</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php } ?>
                <?php if ($user->data()->power == 1) {
                    ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-copy"></i>
                            <p>
                                Extra <i class="fas fa-angle-left right"></i>

                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="add.php?id=8" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Regions</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="add.php?id=9" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Disricts</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="add.php?id=10" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Wards</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php } ?>

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>