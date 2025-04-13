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
    <title>NIMREGENIN | Summary</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
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

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Summary</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Summary</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Summary Total For Nimregenin Study on <?= date('Y-m-d') ?></h3>
                                    <form method="GET" action="" class="form-inline float-right">
                                        <label for="month" class="mr-2">Filter by Month:</label>
                                        <select name="month" id="month" class="form-control mr-2">
                                            <option value="">All Months</option>
                                            <?php
                                            for ($m = 1; $m <= 12; $m++) {
                                                $monthName = date('F', mktime(0, 0, 0, $m, 1));
                                                $selected = (isset($_GET['month']) && $_GET['month'] == $m) ? 'selected' : '';
                                                echo "<option value='$m' $selected>$monthName</option>";
                                            }
                                            ?>
                                        </select>
                                        <button type="submit" class="btn btn-primary">Filter</button>
                                    </form>
                                    <form class="form-inline float-right">
                                        <div> <a href="report.php" class="btn btn-primary">Download</a></div>
                                    </form>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body p-0">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th style="width: 10px">#</th>
                                                <th>Site</th>
                                                <th>RECRUITMENT</th>
                                                <th>SCREENED</th>
                                                <th>ELIGIBLE</th>
                                                <th>ENROLLED</th>
                                                <th>Breast</th>
                                                <th>Brain</th>
                                                <th>Cervical</th>
                                                <th>Prostate</th>
                                                <th>COMPLETED</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $x = 1;
                                            $site_data = $override->getData('site');
                                            $Total = $override->getCount('clients', 'status', 1);
                                            $data_enrolled = $override->getCount1('clients', 'status', 1, 'enrolled', 1);
                                            foreach ($site_data as $row) {
                                                $registered = $override->countData('clients', 'status', 1, 'site_id', $row['id']);
                                                $registered_Total = $override->getCount('clients', 'status', 1);
                                                $screened = $override->countData2('clients', 'status', 1, 'screened', 1, 'site_id', $row['id']);
                                                $screened_Total = $override->countData('clients', 'status', 1, 'screened', 1);
                                                $eligible = $override->countData2('clients', 'status', 1, 'eligible', 1, 'site_id', $row['id']);
                                                $eligible_Total = $override->countData('clients', 'status', 1, 'eligible', 1);
                                                $enrolled = $override->countData2('clients', 'status', 1, 'enrolled', 1, 'site_id', $row['id']);
                                                $enrolled_Total = $override->countData('clients', 'status', 1, 'enrolled', 1);
                                                $end_study = $override->countData2('clients', 'status', 1, 'end_study', 1, 'site_id', $row['id']);
                                                $end_study_Total = $override->countData('clients', 'status', 1, 'end_study', 1);
                                                $breast_cancer = $override->countData2('screening', 'status', 1, 'breast_cancer', 1, 'site_id', $row['id']);
                                                $breast_cancer_Total = $override->countData('screening', 'status', 1, 'breast_cancer', 1);
                                                $brain_cancer = $override->countData2('screening', 'status', 1, 'brain_cancer', 1, 'site_id', $row['id']);
                                                $brain_cancer_Total = $override->countData('screening', 'status', 1, 'brain_cancer', 1,);
                                                $cervical_cancer = $override->countData2('screening', 'status', 1, 'cervical_cancer', 1, 'site_id', $row['id']);
                                                $cervical_cancer_Total = $override->countData('screening', 'status', 1, 'cervical_cancer', 1);
                                                $prostate_cancer = $override->countData2('screening', 'status', 1, 'prostate_cancer', 1, 'site_id', $row['id']);
                                                $prostate_cancer_Total = $override->countData('screening', 'status', 1, 'prostate_cancer', 1);

                                                $site_total = ($screened + $eligible + $enrolled + $end_study);
                                                $Total = ($screened_Total + $eligible_Total + $enrolled_Total + $end_study_Total);
                                            ?>
                                                <tr>
                                                    <td><?= $x ?></td>
                                                    <td><?= $row['name'] ?></td>
                                                    <td><span class="badge bg-default"><?= $registered ?></span></td>
                                                    <td><span class="badge bg-default"><?= $screened ?></span></td>
                                                    <td><span class="badge bg-default"><?= $eligible ?></span></td>
                                                    <td><span class="badge bg-default"><?= $enrolled ?></span></td>
                                                    <td><span class="badge bg-default"><?= $breast_cancer ?></span></td>
                                                    <td><span class="badge bg-default"><?= $brain_cancer ?></span></td>
                                                    <td><span class="badge bg-default"><?= $cervical_cancer ?></span></td>
                                                    <td><span class="badge bg-default"><?= $prostate_cancer ?></span></td>
                                                    <td><span class="badge bg-default"><?= $end_study ?></span></td>
                                                </tr>
                                            <?php $x++;
                                            } ?>
                                            <tr>
                                                <td>Total </td>
                                                <td></td>
                                                <td><span class="badge bg-success"><?= $registered_Total ?></span></td>
                                                <td><span class="badge bg-success"><?= $screened_Total ?></span></td>
                                                <td><span class="badge bg-success"><?= $eligible_Total ?></span></td>
                                                <td><span class="badge bg-success"><?= $enrolled_Total ?></span></td>
                                                <td><span class="badge bg-success"><?= $breast_cancer_Total ?></span></td>
                                                <td><span class="badge bg-success"><?= $brain_cancer_Total ?></span></td>
                                                <td><span class="badge bg-success"><?= $cervical_cancer_Total ?></span></td>
                                                <td><span class="badge bg-success"><?= $prostate_cancer_Total ?></span></td>
                                                <td><span class="badge bg-success"><?= $end_study_Total ?></span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                        <!-- /.col -->
                    </div>
                </div>
                <!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
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
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <!-- <script src="dist/js/demo.js"></script> -->
</body>

</html>