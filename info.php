<?php include 'info_header.php'; ?>


<?php if ($_GET['id'] == 1) { ?>

<?php } elseif ($_GET['id'] == 2) { ?>

    <table id="search-results" class="table table-bordered">
        <thead>
            <tr>
                <!-- <th><input type="checkbox" name="checkall" /></th> -->
                <td width="2">#</td>
                <th width="20">Picture</th>
                <th width="8%">ParticipantID</th>
                <th width="8%">Enrollment Date</th>
                <!-- <th width="6%">AGREE USING NIMREGENIN ?</th> -->
                <th width="6%">USING NIMREGENIN ?</th>
                <th width="10%">Name</th>
                <th width="8%">Gender</th>
                <th width="8%">Age</th>
                <th width="3%">PATIENT TYPE</th>
                <!-- <th width="3%">TREATMENT TYPE</th> -->
                <!-- <th width="4%">CATEGORY</th>  -->
                <th width="8%">SITE</th>
                <th width="10%">STATUS</th>
                <?php if ($_GET['status'] == 4) { ?>

                    <th width="10%">REASON</th>
                <?php } else { ?>
                    <th width="40%">ACTION</th>

                <?php } ?>

            </tr>
        </thead>
        <tbody>
            <?php
            $x = 1;
            foreach ($clients as $client) {
                $visit_site_id = $override->get('visit', 'client_id', $client['id']);

                foreach ($visit_site_id as $visit_ids) {
                    $user->updateRecord('visit', array(
                        'site_id' => 1,
                    ), 1);
                }

                $screening1 = $override->getCount('screening', 'client_id', $client['id']);
                $screening2 = $override->getCount('lab', 'client_id', $client['id']);
                $visit = $override->getCount('visit', 'client_id', $client['id']);
                $visit_date = $override->firstRow('visit', 'visit_date', 'id', 'client_id', $client['id'])[0];
                $end_study = $override->getNews('crf6', 'status', 1, 'patient_id', $client['id'])[0];
                $screened = 0;
                $eligible = 0;
                $enrolled = 0;
                if ($client['screened'] == 1) {
                    $screened = 1;
                }

                if ($client['eligible'] == 1) {
                    $eligible = 1;
                }

                if ($client['enrolled'] == 1) {
                    $enrolled = 1;
                }



                // $category = 0;
        
                // if ($type['cardiac'] == 1) {
                //     $category =  $override->countData('cardiac', 'patient_id', $client['id'], 'status', 1);
                // } elseif ($type['diabetes'] == 1) {
                //     $category =  $override->countData('diabetic', 'patient_id', $client['id'], 'status', 1);
                // } elseif ($type['sickle_cell'] == 1) {
                //     $category =  $override->countData('sickle_cell', 'patient_id', $client['id'], 'status', 1);
                // } else {
                //     $category = 0;
                // }
        

                $crf1 = $override->countData('crf1', 'patient_id', $client['id'], 'status', 1);
                $crf2 = $override->countData('crf2', 'patient_id', $client['id'], 'status', 1);
                $crf3 = $override->countData('crf3', 'patient_id', $client['id'], 'status', 1);
                $crf4 = $override->countData('crf4', 'patient_id', $client['id'], 'status', 1);
                $crf5 = $override->countData('crf5', 'patient_id', $client['id'], 'status', 1);
                $crf6 = $override->countData('crf6', 'patient_id', $client['id'], 'status', 1);
                $crf7 = $override->countData('crf7', 'patient_id', $client['id'], 'status', 1);

                $Total_visit_available = 0;
                $Total_CRF_available = 0;
                $Total_CRF_required = 0;
                $progress = 0;

                $Total_visit_available = intval($override->getCountNot('visit', 'client_id', $client['id'], 'visit_code', 'AE', 'END'));
                if ($Total_visit_available < 1) {
                    $Total_visit_available = 0;
                    $Total_CRF_available = 0;
                    $Total_CRF_required = 0;
                } elseif ($Total_visit_available == 1) {
                    $Total_visit_available = intval($Total_visit_available);

                    $Total_CRF_available = intval(intval($crf1) + intval($crf2) + intval($crf3) + intval($crf4) + intval($crf5) + intval($crf6) + intval($crf7));

                    $Total_CRF_required = intval(intval($Total_visit_available) * 5);
                } elseif ($Total_visit_available > 1) {
                    $Total_visit_available = intval(intval($Total_visit_available) - 1);

                    $Total_CRF_available = intval(intval($crf2) + intval($crf3) + intval($crf4) + intval($crf7));


                    $Total_CRF_required = intval((intval($Total_visit_available) * 4) + 6);
                }

                $client_progress = intval(intval($Total_CRF_available) / intval($Total_CRF_required) * 100);
                ?>
                <tr>
                    <!-- <td><input type="checkbox" name="checkbox" /></td> -->
                    <td><?= $x ?></td>
                    <td width="100">
                        <?php if ($client['client_image'] != '' || is_null($client['client_image'])) {
                            $img = $client['client_image'];
                        } else {
                            $img = 'img/users/blank.png';
                        } ?>
                        <a href="#img<?= $client['id'] ?>" data-toggle="modal"><img src="<?= $img ?>" width="90" height="90"
                                class="" /></a>
                    </td>
                    <td><?= $client['study_id'] ?></td>
                    <td><?= $visit_date['visit_date'] ?></td>
                    <!-- <?php if ($client['consented_nimregenin'] == 1) { ?>
                                                    <td>Yes</td>
                                                <?php } else { ?>
                                                    <td>No</td>
                                                <?php } ?> -->
                    <?php if ($client['nimregenin'] == 1) { ?>
                        <td>
                            <a href="#" class="btn btn-info">YES</a>
                        </td>
                    <?php } elseif ($client['nimregenin'] == 2) { ?>
                        <td>
                            <a href="#" class="btn btn-warning">NO</a>
                        </td>
                    <?php } else { ?>
                        <td>
                            <a href="#" class="btn btn-danger">NOT DONE</a>
                        </td>
                    <?php } ?>
                    <td> <?= $client['firstname'] . ' ' . $client['lastname'] ?></td>
                    <td><?= $client['gender'] ?></td>
                    <td><?= $client['age'] ?></td>
                    <td><?= $cat ?></td>

                    <?php if ($category['patient_category'] == 1) { ?>
                        <td>Intervention</td>
                    <?php } elseif ($category['patient_category'] == 2) { ?>
                        <td>Control</td>
                    <?php } else { ?>
                        <td>Not Done</td>
                    <?php } ?>
                    <!-- <?php if ($category['patient_category'] == 1) { ?>
                                                    <td>NEW PATIENT</td>
                                                <?php } else { ?>
                                                    <td>OLD PATIENT</td>
                                                <?php } ?>

                                                <?php if ($client['treatment_type'] == 1) { ?>
                                                    <td>Radiotherapy</td>
                                                <?php } elseif ($client['treatment_type'] == 1) { ?>
                                                    <td>Chemotherapy</td>
                                                <?php } else { ?>
                                                    <td>Surgery</td>
                                                <?php } ?> -->
                    <?php if ($client['site_id'] == 1) { ?>
                        <td>MNH - UPANGA </td>
                    <?php } else { ?>
                        <td>ORCI </td>
                    <?php } ?>

                    <?php if ($_GET['status'] == '') { ?>

                        <?php if ($client['eligible'] == 1) { ?>
                            <td>
                                <a href="#" class="btn btn-success">Eligible</a>
                            </td>
                        <?php } else { ?>
                            <td>
                                <a href="#" class="btn btn-danger">Not Eligible</a>
                            </td>
                        <?php }
                    } ?>

                    <?php if ($_GET['status'] == 1) { ?>

                        <?php if ($client['eligible'] == 1) { ?>
                            <td>
                                <a href="#" class="btn btn-success">Eligible</a>
                            </td>
                        <?php } else { ?>
                            <td>
                                <a href="#" class="btn btn-danger">Not Eligible</a>
                            </td>
                        <?php }
                    } ?>

                    <?php if ($_GET['status'] == 2) { ?>

                        <?php if ($client['enrolled'] == 1) { ?>
                            <td>
                                <a href="#" class="btn btn-success">Enrolled</a>
                            </td>
                        <?php } else { ?>
                            <td>
                                <a href="#" class="btn btn-danger">Not Enrolled</a>
                            </td>
                        <?php }
                    } ?>

                    <?php if ($_GET['status'] == 3) { ?>

                        <?php if ($client['enrolled'] == 1) { ?>
                            <td>
                                <a href="#" class="btn btn-success">Enrolled</a>
                            </td>
                        <?php } else { ?>
                            <td>
                                <a href="#" class="btn btn-danger">Not Enrolled</a>
                            </td>
                        <?php }
                    } ?>

                    <?php if ($_GET['status'] == 4) { ?>

                        <?php if ($client['end_study'] == 1) { ?>
                            <td>
                                <a href="#" class="btn btn-danger">END</a>
                            </td>

                            <?php if ($end_study['completed120days'] == 1) { ?>
                                <td>
                                    <a href="#" class="btn btn-info">Completed 120 days</a>
                                </td>

                            <?php } elseif ($end_study['reported_dead'] == 1) { ?>
                                <td>
                                    <a href="#" class="btn btn-info">Reported Dead</a>
                                </td>
                                <?php
                            } elseif ($end_study['withdrew_consent'] == 1) { ?>
                                <td>
                                    <a href="#" class="btn btn-info">Withdrew Consent</a>
                                </td>
                                <?php
                            } else { ?>
                                <td>
                                    <a href="#" class="btn btn-info">Other</a>
                                </td>
                                <?php
                            } ?>

                            <!-- <td>
                                                            <a href="#" class="btn btn-danger"><?= $end_study['outcome'] ?></a>
                                                        </td> -->
                        <?php } else { ?>
                            <td>
                                <a href="#" class="btn btn-success">ACTIVE</a>
                            </td>
                        <?php }
                    } ?>

                    <?php if ($_GET['status'] == 5 || $_GET['status'] == 6 || $_GET['status'] == 7 || $_GET['status'] == 8) { ?>

                        <?php if ($client['screened'] == 1) { ?>
                            <td>
                                <a href="#" class="btn btn-success">SCREENED</a>
                            </td>
                        <?php } else { ?>
                            <td>
                                <a href="#" class="btn btn-danger">NOT SCREENED</a>
                            </td>
                        <?php }
                    } ?>

                    <td>
                        <?php if ($_GET['status'] == 1 || $_GET['status'] == 5 || $_GET['status'] == 6 || $_GET['status'] == 7 || $_GET['status'] == 8) { ?>
                            <?php if ($user->data()->accessLevel == 1 || $user->data()->power == 1) { ?>
                                <a href="#clientView<?= $client['id'] ?>" role="button" class="btn btn-default"
                                    data-toggle="modal">View</a>
                                <a href="id.php?cid=<?= $client['id'] ?>" class="btn btn-warning">Patient ID</a>
                                <a href="#delete<?= $client['id'] ?>" role="button" class="btn btn-danger"
                                    data-toggle="modal">Delete</a>
                                <a href="#deleteSchedule<?= $client['id'] ?>" role="button" class="btn btn-danger"
                                    data-toggle="modal">Delete Schedule</a>
                                <a href="#screened<?= $client['id'] ?>" role="button" class="btn btn-info"
                                    data-toggle="modal">screened</a>
                                <a href="#eligibility1<?= $client['id'] ?>" role="button" class="btn btn-info"
                                    data-toggle="modal">eligibility1</a><br>
                                <a href="#eligibility2<?= $client['id'] ?>" role="button" class="btn btn-info"
                                    data-toggle="modal">eligibility2</a>
                                <a href="#eligible<?= $client['id'] ?>" role="button" class="btn btn-info"
                                    data-toggle="modal">eligible</a>
                                <a href="#enrolled<?= $client['id'] ?>" role="button" class="btn btn-info"
                                    data-toggle="modal">enrolled</a>
                            <?php } ?>
                            <hr>
                            <a href="#asignID<?= $client['id'] ?>" role="button" class="btn btn-success" data-toggle="modal">asign
                                ID</a>
                            <hr>
                            <a href="add.php?id=2&cid=<?= $client['id'] ?>" class="btn btn-info">Edit</a>

                            <hr>
                            <?php
                            //  if ($screened == 1) {
                
                            ?>
                            <?php if ($screening1 >= 1) { ?>
                                <a href="#addInclusion<?= $client['id'] ?>" role="button" class="btn btn-info" data-toggle="modal">Edit
                                    Inclusion</a>

                            <?php } else { ?>
                                <a href="#addInclusion<?= $client['id'] ?>" role="button" class="btn btn-warning"
                                    data-toggle="modal">Add Inclusion</a>

                            <?php } ?>
                            <hr>
                            <?php if ($screening2 >= 1) { ?>
                                <a href="#addExclusion<?= $client['id'] ?>" role="button" class="btn btn-info" data-toggle="modal">Edit
                                    Exclusion</a>

                            <?php } else { ?>
                                <a href="#addExclusion<?= $client['id'] ?>" role="button" class="btn btn-warning"
                                    data-toggle="modal">Add Exclusion</a>

                                <?php
                            }
                        // }
                    }
                    ?>
                        <?php if ($_GET['status'] == 2) { ?>
                            <?php if ($eligible == 1) { ?>
                                <?php if ($visit >= 1) { ?>
                                    <a href="#editEnrollment<?= $client['id'] ?>" role="button" class="btn btn-info"
                                        data-toggle="modal">Edit Enrollment</a>
                                <?php } else { ?>
                                    <a href="#addEnrollment<?= $client['id'] ?>" role="button" class="btn btn-warning"
                                        data-toggle="modal">Add Enrollment</a>

                                <?php }
                            } ?>
                        <?php } ?>
                        <?php if ($visit >= 1) { ?>
                            <?php if ($_GET['status'] == 3) { ?>
                                <?php if ($enrolled == 1) { ?>
                                    <a href="info.php?id=7&cid=<?= $client['id'] ?>" role="button" class="btn btn-success">schedule</a>
                                    <hr>
                                    <?php if ($client_progress == 100) { ?>
                                        <span class="badge badge-primary right">
                                            <?= $Total_CRF_available ?> out of <?= $Total_CRF_required ?>
                                        </span>
                                        <hr>
                                        <span class="badge badge-primary right">
                                            <?= $client_progress ?>%
                                            <?php
                                            $user->updateRecord('clients', array(
                                                'progress' => $client_progress,
                                            ), $client['id']);
                                            ?>
                                        </span>
                                    <?php } elseif ($client_progress > 100) { ?>
                                        <span class="badge badge-warning right">
                                            <?= $Total_CRF_available ?> out of <?= $Total_CRF_required ?>
                                        </span>
                                        <hr>
                                        <span class="badge badge-warning right">
                                            <?= $client_progress ?>%
                                            <?php
                                            $user->updateRecord('clients', array(
                                                'progress' => $client_progress,
                                            ), $client['id']);
                                            ?> </span>
                                    <?php } elseif ($client_progress >= 80 && $client_progress < 100) { ?>
                                        <span class="badge badge-info right">
                                            <?= $Total_CRF_available ?> out of <?= $Total_CRF_required ?>
                                        </span>
                                        <hr>
                                        <span class="badge badge-info right">
                                            <?= $client_progress ?>%
                                            <?php
                                            $user->updateRecord('clients', array(
                                                'progress' => $client_progress,
                                            ), $client['id']);
                                            ?> </span>
                                    <?php } elseif ($client_progress >= 50 && $client_progress < 80) { ?>
                                        <span class="badge badge-secondary right">
                                            <?= $Total_CRF_available ?> out of <?= $Total_CRF_required ?>
                                        </span>
                                        <hr>
                                        <span class="badge badge-secondary right">
                                            <?= $client_progress ?>%
                                            <?php
                                            $user->updateRecord('clients', array(
                                                'progress' => $client_progress,
                                            ), $client['id']);
                                            ?> </span>
                                    <?php } elseif ($client_progress < 50) { ?>
                                        <span class="badge badge-danger right">
                                            <?= $Total_CRF_available ?> out of <?= $Total_CRF_required ?>
                                        </span>
                                        <hr>
                                        <span class="badge badge-danger right">
                                            <?= $client_progress ?>%
                                            <?php
                                            $user->updateRecord('clients', array(
                                                'progress' => $client_progress,
                                            ), $client['id']);
                                            ?>
                                        </span>
                                    <?php } ?>
                                <?php }
                            }
                        } ?>
                    </td>
                </tr>
                <div class="modal fade" id="addScreening<?= $client['id'] ?>">
                    <div class="modal-dialog">
                        <form method="post">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">SCREENING FORM</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Date of Screening</label>
                                                    <input class="form-control" type="date" max="<?= date('Y-m-d'); ?>"
                                                        type="screening_date" name="screening_date" id="screening_date"
                                                        style="width: 100%;"
                                                        value="<?php if ($screening['screening_date']) {
                                                            print_r($screening['screening_date']);
                                                        } ?>"
                                                        required />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <div class="form-group">
                                                    <label>Consenting individuals?</label>
                                                    <select class="form-control" name="consent" id="consent"
                                                        style="width: 100%;"
                                                        onchange="checkQuestionValue1('consent','conset_date')" required>
                                                        <option value="<?= $screening['consent'] ?>">
                                                            <?php if ($screening['consent']) {
                                                                if ($screening['consent'] == 1) {
                                                                    echo 'Yes';
                                                                } elseif ($screening['consent'] == 2) {
                                                                    echo 'No';
                                                                }
                                                            } else {
                                                                echo 'Select';
                                                            } ?></option>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Date of Conset</label>
                                                    <input class="form-control" type="date" max="<?= date('Y-m-d'); ?>"
                                                        type="conset_date" name="conset_date" id="conset_date"
                                                        style="width: 100%;"
                                                        value="<?php if ($screening['conset_date']) {
                                                            print_r($screening['conset_date']);
                                                        } ?>"
                                                        required />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <div class="form-group">
                                                    <label>Permanent resident?</label>
                                                    <select class="form-control" name="residence" style="width: 100%;" required>
                                                        <option value="<?= $screening['residence'] ?>">
                                                            <?php if ($screening['residence']) {
                                                                if ($screening['residence'] == 1) {
                                                                    echo 'Yes';
                                                                } elseif ($screening['residence'] == 2) {
                                                                    echo 'No';
                                                                }
                                                            } else {
                                                                echo 'Select';
                                                            } ?></option>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <div class="form-group">
                                                    <label>Known NCD?</label>
                                                    <select class="form-control" name="ncd" style="width: 100%;" required>
                                                        <option value="<?= $screening['ncd'] ?>">
                                                            <?php if ($screening['ncd']) {
                                                                if ($screening['ncd'] == 1) {
                                                                    echo 'Yes';
                                                                } elseif ($screening['ncd'] == 2) {
                                                                    echo 'No';
                                                                }
                                                            } else {
                                                                echo 'Select';
                                                            } ?></option>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <div class="form-group">
                                                    <label>Request Lab Test ?</label>
                                                    <select class="form-control" name="lab_request" style="width: 100%;"
                                                        required>
                                                        <option value="<?= $screening['lab_request'] ?>">
                                                            <?php if ($screening['lab_request']) {
                                                                if ($screening['lab_request'] == 1) {
                                                                    echo 'Yes';
                                                                } elseif ($screening['ncd'] == 2) {
                                                                    echo 'No';
                                                                }
                                                            } else {
                                                                echo 'Select';
                                                            } ?></option>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Date of Request</label>
                                                    <input class="form-control" type="date" max="<?= date('Y-m-d'); ?>"
                                                        type="lab_request_date" name="lab_request_date" id="lab_request_date"
                                                        style="width: 100%;"
                                                        value="<?php if ($screening['lab_request_date']) {
                                                            print_r($screening['lab_request_date']);
                                                        } ?>" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <div class="form-group">
                                                    <label>Type of Screening</label>
                                                    <select class="form-control" name="screening_type" style="width: 100%;"
                                                        required>
                                                        <option value="<?= $screening['screening_type'] ?>">
                                                            <?php if ($screening['screening_type']) {
                                                                if ($screening['screening_type'] == 1) {
                                                                    echo 'Facility';
                                                                } elseif ($screening['ncd'] == 2) {
                                                                    echo 'Community';
                                                                }
                                                            } else {
                                                                echo 'Select';
                                                            } ?></option>
                                                        <option value="1">Facility</option>
                                                        <option value="2">Community</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                </div>
                                <div class="modal-footer justify-content-between">
                                    <input type="hidden" name="id" value="<?= $screening['id'] ?>">
                                    <input type="hidden" name="cid" value="<?= $client['id'] ?>">
                                    <input type="hidden" name="gender" value="<?= $client['gender'] ?>">
                                    <input type="hidden" name="study_id" value="<?= $client['study_id'] ?>">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <?php if ($user->data()->accessLevel == 1 || $user->data()->accessLevel == 2) { ?>
                                        <input type="submit" name="add_screening" class="btn btn-primary" value="Submit">
                                    <?php } ?>
                                </div>
                            </div>
                            <!-- /.modal-content -->
                        </form>
                    </div>
                    <!-- /.modal-dialog -->
                </div>
                <!-- /.modal -->

                <div class="modal fade" id="addEnrollment<?= $client['id'] ?>">
                    <div class="modal-dialog">
                        <form method="post">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Visit</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <?php
                                $enrollment = $override->getNews('visit', 'client_id', $client['id'], 'seq_no', 1)[0];
                                ?>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Date of Enrollment</label>
                                                    <input class="form-control" type="date" max="<?= date('Y-m-d'); ?>"
                                                        type="visit_date" name="visit_date" id="visit_date" style="width: 100%;"
                                                        value="<?php if ($enrollment['visit_date']) {
                                                            print_r($enrollment['visit_date']);
                                                        } ?>"
                                                        required />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-8">
                                            <div class="row-form clearfix">
                                                <div class="form-group">
                                                    <label>Notes / Remarks / Comments</label>
                                                    <textarea class="form-control" name="reasons" rows="3">
                                                                                         <?php
                                                                                         if ($enrollment['reasons']) {
                                                                                             print_r($enrollment['reasons']);
                                                                                         } ?>
                                                                                        </textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="dr"><span></span></div>
                                    </div>
                                </div>
                                <div class="modal-footer justify-content-between">
                                    <input type="hidden" name="id" value="<?= $client['id'] ?>">
                                    <input type="hidden" name="visit_name" value="Enrollment Visit">
                                    <input type="hidden" name="study_id" value="<?= $client['study_id'] ?>">
                                    <input type="hidden" name="site_id" value="<?= $client['site_id'] ?>">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <?php if ($user->data()->accessLevel == 1 || $user->data()->accessLevel == 2) { ?>
                                        <input type="submit" name="add_Enrollment" class="btn btn-primary" value="Submit">
                                    <?php } ?>
                                </div>
                            </div>
                            <!-- /.modal-content -->
                        </form>
                    </div>
                    <!-- /.modal-dialog -->
                </div>
                <!-- /.modal -->

                <div class="modal fade" id="delete_client<?= $client['id'] ?>" tabindex="-1" role="dialog"
                    aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <form method="post">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal"><span
                                            aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                    <h4>Delete Client</h4>
                                </div>
                                <div class="modal-body">
                                    <strong style="font-weight: bold;color: red">
                                        <p>Are you sure you want to delete this Client ?</p>
                                    </strong>
                                </div>
                                <div class="modal-footer">
                                    <input type="hidden" name="id" value="<?= $client['id'] ?>">
                                    <?php if ($user->data()->accessLevel == 1) { ?>
                                        <input type="submit" name="delete_client" value="Delete" class="btn btn-danger">
                                    <?php } ?>
                                    <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <?php $x++;
            } ?>
        </tbody>
    </table>
<?php } ?>



<?php include 'info_header.php'; ?>