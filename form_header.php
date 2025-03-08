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

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><?= $_GET['action'] ?> New <?= $_GET['table'] ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="info.php?id=1">
                                < Back </a>
                        </li>&nbsp;&nbsp;
                        <li class="breadcrumb-item"><a href="index1.php">Home</a></li>&nbsp;&nbsp;
                        <li class="breadcrumb-item">
                            <a href="info.php?id=1">
                                Go to staff list >
                            </a>
                        </li>&nbsp;&nbsp;
                        <li class="breadcrumb-item active"><?= $_GET['action'] ?> New <?= $_GET['table'] ?></li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <?php $patient = $override->get1('crf1', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode'])[0] ?>
                <?php $herbal_treatment = $override->get1('herbal_treatment', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode'])[0] ?>
                <?php $chemotherapy = $override->get1('chemotherapy', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode'])[0] ?>
                <?php $surgery = $override->get1('surgery', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode'])[0] ?>
                <?php

                $patients = $override->get('clients', 'id', $_GET['cid'])[0];
                $visits_status = $override->firstRow1('visit', 'status', 'id', 'client_id', $_GET['cid'], 'visit_code', 'EV')[0]['status'];

                $required_visit = $override->countData1('visit', 'status', 1, 'client_id', $_GET['cid'], 'seq_no', $_GET['seq']);

                $status = $override->get3('visit', 'client_id', $_GET['cid'], 'seq_no', $_GET['seq'], 'id', $_GET['vid'])[0];


                $category = $override->get('clients', 'id', $_GET['cid'])[0];
                $cat = '';

                if ($category['patient_category'] == 1) {
                    $cat = 'Intervention';
                } elseif ($category['patient_category'] == 2) {
                    $cat = 'Control';
                } elseif ($category['patient_category'] == 0) {
                    $cat = 'Not Filled';
                } else {
                    $cat = 'Not Filled';
                }


                if ($patient['gender'] == 'male') {
                    $gender = 'Male';
                } elseif ($patient['gender'] == 'female') {
                    $gender = 'Female';
                }

                $name = 'Name: ' . $patients['firstname'] . ' ' . $patients['lastname'] . ' Age: ' . $patients['age'] . ' Gender: ' . $gender . ' Type: ' . $cat;

                ?>
                <!-- right column -->
                <div class="col-md-12">
                    <!-- general form elements disabled -->
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title"><?= $_GET['table'] ?> Details</h3>
                        </div>