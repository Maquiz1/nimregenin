<?php
require_once 'php/core/init.php';
$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();

$successMessage = null;
$pageError = null;
$errorMessage = null;

$numRec = 15;
if ($user->isLoggedIn()) {
    if (Input::exists('post')) {
        $validate = new validate();
        if (Input::get('edit_position')) {
            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('position', array(
                        'name' => Input::get('name'),
                    ), Input::get('id'));
                    $successMessage = 'Position Successful Updated';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('reset_pass')) {
            $salt = $random->get_rand_alphanumeric(32);
            $password = '12345678';
            $user->updateRecord('user', array(
                'password' => Hash::make($password, $salt),
                'salt' => $salt,
            ), Input::get('id'));
            $successMessage = 'Password Reset Successful';
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
            $successMessage = 'User Deleted Successful';
        } elseif (Input::get('restore_staff')) {
            $user->updateRecord('user', array(
                'status' => 1,
            ), Input::get('id'));
            $successMessage = 'User Deleted Successful';
        } elseif (Input::get('delete_client')) {
            $user->updateRecord('clients', array(
                'status' => 0,
            ), Input::get('id'));
            $successMessage = 'Client Deleted Successful';
        } elseif (Input::get('edit_study')) {
            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
                'code' => array(
                    'required' => true,
                ),
                'sample_size' => array(
                    'required' => true,
                ),
                'start_date' => array(
                    'required' => true,
                ),
                'end_date' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('study', array(
                        'name' => Input::get('name'),
                        'code' => Input::get('code'),
                        'sample_size' => Input::get('sample_size'),
                        'start_date' => Input::get('start_date'),
                        'end_date' => Input::get('end_date'),
                    ), Input::get('id'));
                    $successMessage = 'Study Successful Updated';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('edit_site')) {
            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('site', array(
                        'name' => Input::get('name'),
                    ), Input::get('id'));
                    $successMessage = 'Site Successful Updated';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_screening')) {
            $validate = $validate->check($_POST, array(
                'screening_date' => array(
                    'required' => true,
                ),
                'lab_request' => array(
                    'required' => true,
                ),
                'ncd' => array(
                    'required' => true,
                ),
                'consent' => array(
                    'required' => true,
                ),
                'residence' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $eligibility = 0;
                    if ((Input::get('consent') == 1 && Input::get('residence') == 1) && (Input::get('ncd') == 1)) {
                        $eligibility = 1;
                    }

                    $doctor_confirm = 0;
                    if ((Input::get('consent') == 1 && Input::get('residence') == 1)) {
                        if (Input::get('ncd') == 1) {
                            $doctor_confirm = 2;
                        }
                    }

                    if ($override->getNews('screening', 'status', 1, 'patient_id', Input::get('cid'))) {
                        $user->updateRecord('screening', array(
                            'study_id' => Input::get('study_id'),
                            'screening_date' => Input::get('screening_date'),
                            'conset_date' => Input::get('conset_date'),
                            'ncd' => Input::get('ncd'),
                            'lab_request' => Input::get('lab_request'),
                            'lab_request_date' => Input::get('lab_request_date'),
                            'screening_type' => Input::get('screening_type'),
                            'consent' => Input::get('consent'),
                            'residence' => Input::get('residence'),
                            'created_on' => date('Y-m-d'),
                            'patient_id' => Input::get('cid'),
                            'staff_id' => $user->data()->id,
                            'eligibility' => $eligibility,
                            'doctor_confirm' => $doctor_confirm,
                            'status' => 1,
                            'site_id' => $user->data()->site_id,
                        ), Input::get('id'));

                        $visit = $override->getNews('visit', 'client_id', Input::get('cid'), 'seq_no', 0, 'visit_name', 'Screening')[0];

                        $user->updateRecord('visit', array(
                            'expected_date' => Input::get('screening_date'),
                            'visit_date' => Input::get('screening_date'),
                        ), $visit['id']);

                        $successMessage = 'Screening Successful Updated';
                    } else {
                        $user->createRecord('screening', array(
                            'screening_date' => Input::get('screening_date'),
                            'conset_date' => Input::get('conset_date'),
                            'consent' => Input::get('consent'),
                            'ncd' => Input::get('ncd'),
                            'lab_request' => Input::get('lab_request'),
                            'lab_request_date' => Input::get('lab_request_date'),
                            'screening_type' => Input::get('screening_type'),
                            'study_id' => Input::get('study_id'),
                            'residence' => Input::get('residence'),
                            'created_on' => date('Y-m-d'),
                            'patient_id' => Input::get('cid'),
                            'staff_id' => $user->data()->id,
                            'eligibility' => $eligibility,
                            'status' => 1,
                            'doctor_confirm' => $doctor_confirm,
                            'site_id' => $user->data()->site_id,
                        ));

                        $user->createRecord('visit', array(
                            'study_id' => Input::get('study_id'),
                            'visit_name' => 'Screening',
                            'visit_code' => 'SV',
                            'visit_day' => 'Day 0',
                            'expected_date' => Input::get('screening_date'),
                            'visit_date' => Input::get('screening_date'),
                            'visit_window' => 0,
                            'status' => 1,
                            'seq_no' => 0,
                            'client_id' => Input::get('cid'),
                            'created_on' => date('Y-m-d'),
                            'reasons' => '',
                            'visit_status' => 1,
                            'site_id' => $user->data()->site_id,
                        ));
                    }

                    $user->updateRecord('clients', array(
                        'eligible' => $eligibility,
                        // 'enrolled' => $eligibility,
                        'screened' => 1,
                    ), Input::get('cid'));

                    $successMessage = 'Screening Successful Added';

                    if ($eligibility) {
                        Redirect::to('info.php?id=3&status=2');
                    } else {
                        Redirect::to('info.php?id=3&status=1');
                        // Redirect::to('info.php?id=3&status=' . $_GET['status']);
                        // Redirect::to('add_lab.php?cid=' . Input::get('id') . '&status=1&msg=' . $successMessage);
                    }
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_Enrollment')) {
            $validate = $validate->check($_POST, array(
                'visit_date' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                $visit = $override->getNews('visit', 'client_id', Input::get('id'), 'seq_no', 1);

                if ($visit) {
                    $user->updateRecord('visit', array('expected_date' => Input::get('visit_date'), 'reasons' => Input::get('reasons')), $visit[0]['id']);

                    foreach ($override->get('visit', 'client_id', Input::get('id')) as $visit_client) {
                        $user->updateRecord('visit', array('study_id' => Input::get('study_id'), 'site_id' => Input::get('site_id')), $visit_client['id']);
                    }

                    $successMessage = 'Enrollment  Updated Successful';
                } else {

                    $user->createRecord('visit', array(
                        'summary_id' => 0,
                        'study_id' => Input::get('study_id'),
                        'visit_name' => 'Enrollment Visit',
                        'visit_code' => 'EV',
                        'visit_day' => 'Day 1',
                        'expected_date' => Input::get('visit_date'),
                        'visit_date' => '',
                        'visit_window' => 0,
                        'status' => 1,
                        'client_id' => Input::get('id'),
                        'created_on' => date('Y-m-d'),
                        'seq_no' => 1,
                        'reasons' => Input::get('reasons'),
                        'visit_status' => 0,
                        'site_id' => Input::get('site_id'),
                    ));

                    foreach ($override->get('visit', 'client_id', Input::get('id')) as $visit_client) {
                        $user->updateRecord('visit', array('study_id' => Input::get('study_id'), 'site_id' => Input::get('site_id')), $visit_client['id']);
                    }

                    $user->updateRecord('clients', array('enrolled' => 1), Input::get('id'));


                    $successMessage = 'Enrollment  Added Successful';
                }
                Redirect::to('info.php?id=3&status=3&msg=' . $successMessage);
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_visit')) {
            $validate = $validate->check($_POST, array(
                'visit_date' => array(
                    'required' => true,
                ),
                // 'visit_status' => array(
                //     'required' => true,
                // ),
            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('visit', array(
                        'visit_date' => Input::get('visit_date'),
                        'visit_status' => Input::get('visit_status'),
                        'reasons' => Input::get('reasons'),
                    ), Input::get('id'));

                    $client_id = $override->getNews('clients', 'id', Input::get('cid'), 'status', 1)[0];


                    if (Input::get('visit_name') == 'Study Termination Visit') {
                        $user->updateRecord('clients', array(
                            'end_study' => 1,
                        ), Input::get('cid'));
                    } else {
                        $user->updateRecord('clients', array(
                            'end_study' => 0,
                        ), Input::get('cid'));
                    }

                    $successMessage = 'Visit  Added Successful';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('update_visit')) {
            $validate = $validate->check($_POST, array(
                'expected_date' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('visit', array(
                        'expected_date' => Input::get('expected_date'),
                    ), Input::get('id'));

                    $user->updateRecord('summary', array(
                        'next_appointment_date' => Input::get('expected_date'),
                    ), Input::get('summary_id'));

                    $successMessage = 'Visit  Updated Successful';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('search_by_site')) {

            $validate = $validate->check($_POST, array(
                'site' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                $url = 'info.php?id=3&sid=' . Input::get('site');
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
                        if (Input::get('name') == 'user' || Input::get('name') == 'sub_category' || Input::get('name') == 'test_list' || Input::get('name') == 'category' || Input::get('name') == 'medications' || Input::get('name') == 'site' || Input::get('name') == 'schedule' || Input::get('name') == 'study_id') {
                            $errorMessage = 'Table ' . '"' . Input::get('name') . '"' . '  can not be Cleared';
                        } else {
                            $clearData = $override->clearDataTable(Input::get('name'));
                        }
                        $successMessage = 'Table ' . '"' . Input::get('name') . '"' . ' Cleared Successfull';
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
        } elseif (Input::get('set_study_id')) {

            $validate = $validate->check($_POST, array(
                'client_id' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $std_id = $override->getNews('study_id', 'site_id', $user->data()->site_id, 'status', 0)[0];

                    $user->updateRecord('clients', array(
                        'study_id' => $std_id['study_id'],
                    ), Input::get('client_id'));

                    $user->updateRecord('study_id', array(
                        'status' => 1,
                        'client_id' => Input::get('client_id'),
                    ), $std_id['id']);

                    $successMessage = 'STUDY ID ADDED Successfull';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('update_study_id')) {

            $validate = $validate->check($_POST, array(
                'client_id' => array(
                    'required' => true,
                ),
                'table_name' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    if (Input::get('client_id')) {

                        $client_id = '';

                        if (Input::get('table_name') == 'visit') {
                            $client_id = 'client_id';
                        } else {
                            $client_id = 'patient_id';
                        }

                        $clients = $override->get('clients', 'id', Input::get('client_id'));
                        $tables = $override->get(Input::get('table_name'), $client_id, Input::get('client_id'));

                        foreach ($tables as $table) {
                            $user->updateRecord(Input::get('table_name'), array(
                                'study_id' => $clients[0]['study_id'],
                                'site_id' => $clients[0]['site_id'],
                            ), $table['id']);
                        }

                        $successMessage = 'STUDY ID Updated Successfull';
                    } else {
                        $errorMessage = 'Error on updating Table  ' . Input::get('table_name');
                    }
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('update_study_id_all_tables')) {
            $validate = $validate->check($_POST, array(
                'patient_id' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $patient_id = '';
                    if (Input::get('patient_id')) {
                        $patient_id = 'patient_id';
                        $clients = $override->get('clients', 'id', Input::get('patient_id'))[0];

                        foreach ($override->AllTables() as $tables) {

                            // print_r($tables);

                            if ($tables['Tables_in_penplus'] == 'screening') {
                                $table = $override->get('screening', $patient_id, Input::get('patient_id'));
                                foreach ($table as $value) {
                                    $user->updateRecord('screening', array(
                                        'study_id' => $clients['study_id'],
                                        'site_id' => $clients['site_id'],
                                    ), $value['id']);
                                }
                            }
                            if ($tables['Tables_in_penplus'] == 'demographic') {
                                $table = $override->get('demographic', $patient_id, Input::get('patient_id'));
                                foreach ($table as $value) {
                                    $user->updateRecord('demographic', array(
                                        'study_id' => $clients['study_id'],
                                        'site_id' => $clients['site_id'],
                                    ), $value['id']);
                                }
                            }
                            if ($tables['Tables_in_penplus'] == 'vitals') {
                                $table = $override->get('vitals', $patient_id, Input::get('patient_id'));
                                foreach ($table as $value) {
                                    $user->updateRecord('vitals', array(
                                        'study_id' => $clients['study_id'],
                                        'site_id' => $clients['site_id'],
                                    ), $value['id']);
                                }
                            }

                            if ($tables['Tables_in_penplus'] == 'main_diagnosis') {
                                $table = $override->get('main_diagnosis', $patient_id, Input::get('patient_id'));
                                foreach ($table as $value) {
                                    $user->updateRecord('main_diagnosis', array(
                                        'study_id' => $clients['study_id'],
                                        'site_id' => $clients['site_id'],
                                    ), $value['id']);
                                }
                            }

                            if ($tables['Tables_in_penplus'] == 'history') {
                                $table = $override->get('history', $patient_id, Input::get('patient_id'));
                                foreach ($table as $value) {
                                    $user->updateRecord('history', array(
                                        'study_id' => $clients['study_id'],
                                        'site_id' => $clients['site_id'],
                                    ), $value['id']);
                                }
                            }

                            if ($tables['Tables_in_penplus'] == 'symptoms') {
                                $table = $override->get('symptoms', $patient_id, Input::get('patient_id'));
                                foreach ($table as $value) {
                                    $user->updateRecord('symptoms', array(
                                        'study_id' => $clients['study_id'],
                                        'site_id' => $clients['site_id'],
                                    ), $value['id']);
                                }
                            }

                            if ($tables['Tables_in_penplus'] == 'cardiac') {
                                $table = $override->get('cardiac', $patient_id, Input::get('patient_id'));
                                foreach ($table as $value) {
                                    $user->updateRecord('cardiac', array(
                                        'study_id' => $clients['study_id'],
                                        'site_id' => $clients['site_id'],
                                    ), $value['id']);
                                }
                            }

                            if ($tables['Tables_in_penplus'] == 'diabetic') {
                                $table = $override->get('diabetic', $patient_id, Input::get('patient_id'));
                                foreach ($table as $value) {
                                    $user->updateRecord('diabetic', array(
                                        'study_id' => $clients['study_id'],
                                        'site_id' => $clients['site_id'],
                                    ), $value['id']);
                                }
                            }

                            if ($tables['Tables_in_penplus'] == 'sickle_cell') {
                                $table = $override->get('sickle_cell', $patient_id, Input::get('patient_id'));
                                foreach ($table as $value) {
                                    $user->updateRecord('sickle_cell', array(
                                        'study_id' => $clients['study_id'],
                                        'site_id' => $clients['site_id'],
                                    ), $value['id']);
                                }
                            }

                            if ($tables['Tables_in_penplus'] == 'results') {
                                $table = $override->get('results', $patient_id, Input::get('patient_id'));
                                foreach ($table as $value) {
                                    $user->updateRecord('results', array(
                                        'study_id' => $clients['study_id'],
                                        'site_id' => $clients['site_id'],
                                    ), $value['id']);
                                }
                            }

                            if ($tables['Tables_in_penplus'] == 'hospitalization') {
                                $table = $override->get('hospitalization', $patient_id, Input::get('patient_id'));
                                foreach ($table as $value) {
                                    $user->updateRecord('hospitalization', array(
                                        'study_id' => $clients['study_id'],
                                        'site_id' => $clients['site_id'],
                                    ), $value['id']);
                                }
                            }

                            if ($tables['Tables_in_penplus'] == 'hospitalization_details') {
                                $table = $override->get('hospitalization_details', $patient_id, Input::get('patient_id'));
                                foreach ($table as $value) {
                                    $user->updateRecord('hospitalization_details', array(
                                        'study_id' => $clients['study_id'],
                                        'site_id' => $clients['site_id'],
                                    ), $value['id']);
                                }
                            }

                            if ($tables['Tables_in_penplus'] == 'treatment_plan') {
                                $table = $override->get('treatment_plan', $patient_id, Input::get('patient_id'));
                                foreach ($table as $value) {
                                    $user->updateRecord('treatment_plan', array(
                                        'study_id' => $clients['study_id'],
                                        'site_id' => $clients['site_id'],
                                    ), $value['id']);
                                }
                            }

                            if ($tables['Tables_in_penplus'] == 'dgns_complctns_comorbdts') {
                                $table = $override->get('dgns_complctns_comorbdts', $patient_id, Input::get('patient_id'));
                                foreach ($table as $value) {
                                    $user->updateRecord('dgns_complctns_comorbdts', array(
                                        'study_id' => $clients['study_id'],
                                        'site_id' => $clients['site_id'],
                                    ), $value['id']);
                                }
                            }

                            if ($tables['Tables_in_penplus'] == 'risks') {
                                $table = $override->get('risks', $patient_id, Input::get('patient_id'));
                                foreach ($table as $value) {
                                    $user->updateRecord('risks', array(
                                        'study_id' => $clients['study_id'],
                                        'site_id' => $clients['site_id'],
                                    ), $value['id']);
                                }
                            }

                            if ($tables['Tables_in_penplus'] == 'lab_details') {
                                $table = $override->get('lab_details', $patient_id, Input::get('patient_id'));
                                foreach ($table as $value) {
                                    $user->updateRecord('lab_details', array(
                                        'study_id' => $clients['study_id'],
                                        'site_id' => $clients['site_id'],
                                    ), $value['id']);
                                }
                            }

                            if ($tables['Tables_in_penplus'] == 'social_economic') {
                                $table = $override->get('social_economic', $patient_id, Input::get('patient_id'));
                                foreach ($table as $value) {
                                    $user->updateRecord('social_economic', array(
                                        'study_id' => $clients['study_id'],
                                        'site_id' => $clients['site_id'],
                                    ), $value['id']);
                                }
                            }

                            if ($tables['Tables_in_penplus'] == 'summary') {
                                $table = $override->get('summary', $patient_id, Input::get('patient_id'));
                                foreach ($table as $value) {
                                    $user->updateRecord('summary', array(
                                        'study_id' => $clients['study_id'],
                                        'site_id' => $clients['site_id'],
                                    ), $value['id']);
                                }
                            }

                            if ($tables['Tables_in_penplus'] == 'medication_treatments') {
                                $table = $override->get('medication_treatments', $patient_id, Input::get('patient_id'));
                                foreach ($table as $value) {
                                    $user->updateRecord('medication_treatments', array(
                                        'study_id' => $clients['study_id'],
                                        'site_id' => $clients['site_id'],
                                    ), $value['id']);
                                }
                            }

                            if ($tables['Tables_in_penplus'] == 'hospitalization_detail_id') {
                                $table = $override->get('hospitalization_detail_id', $patient_id, Input::get('patient_id'));
                                foreach ($table as $value) {
                                    $user->updateRecord('hospitalization_detail_id', array(
                                        'study_id' => $clients['study_id'],
                                        'site_id' => $clients['site_id'],
                                    ), $value['id']);
                                }
                            }

                            if ($tables['Tables_in_penplus'] == 'sickle_cell_status_table') {
                                $table = $override->get('sickle_cell_status_table', $patient_id, Input::get('patient_id'));
                                foreach ($table as $value) {
                                    $user->updateRecord('sickle_cell_status_table', array(
                                        'study_id' => $clients['study_id'],
                                        'site_id' => $clients['site_id'],
                                    ), $value['id']);
                                }
                            }

                            if ($tables['Tables_in_penplus'] == 'visit') {
                                $table = $override->get('visit', 'client_id', Input::get('patient_id'));
                                foreach ($table as $value) {
                                    $user->updateRecord('visit', array(
                                        'study_id' => $clients['study_id'],
                                        'site_id' => $clients['site_id'],
                                    ), $value['id']);
                                }
                            }

                            $successMessage = 'STUDY ID Updated Successfull On All Tables';
                            Redirect::to('info.php?id=' . $_GET['id'] . '&msg=' . $successMessage);
                        }
                    } else {
                        $errorMessage = 'Please select Patient Study ID';
                    }
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('unset_study_id')) {

            $validate = $validate->check($_POST, array(
                'client_id' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('clients', array(
                        'study_id' => '',
                    ), Input::get('client_id'));

                    $successMessage = 'STUDY ID DELETED Successfull';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('delete_data')) {

            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
                'site' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    if (Input::get('name')) {
                        $site_id = '';
                        if (Input::get('site') == '1') {
                            $site_id = 'KNODOA';
                        } elseif (Input::get('site') == '2') {
                            $site_id = 'KARATU';
                        }

                        if (Input::get('name') == 'user' || Input::get('name') == 'sub_category' || Input::get('name') == 'test_list' || Input::get('name') == 'category' || Input::get('name') == 'medications' || Input::get('name') == 'site' || Input::get('name') == 'schedule' || Input::get('name') == 'study_id') {
                            $errorMessage = 'Table ' . '"' . Input::get('name') . '"' . '  can not be Deleted';
                        } else {
                            // $clearData = $override->deleteDataTable(Input::get('name'), Input::get('site'));
                            $deleteData = $user->deleteRecord(Input::get('name'), 'site_id', Input::get('site'));
                            $successMessage = 'Data on Table ' . '"' . Input::get('name') . 'On site "' . '"' . $site_id . '"' . ' Deleted Successfull';
                        }
                    } else {
                        $errorMessage = 'Data on Table ' . '"' . Input::get('name') . '"' . '  can not be Found!';
                    }
                    // die;
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('delete_data2')) {

            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
                'date2' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    if (Input::get('name')) {
                        $site_id = '';
                        if (Input::get('site') == '1') {
                            $site_id = 'KNODOA';
                        } elseif (Input::get('site') == '2') {
                            $site_id = 'KARATU';
                        }

                        if (Input::get('name') == 'user' || Input::get('name') == 'sub_category' || Input::get('name') == 'test_list' || Input::get('name') == 'category' || Input::get('name') == 'medications' || Input::get('name') == 'site' || Input::get('name') == 'schedule' || Input::get('name') == 'study_id') {
                            $errorMessage = 'Table ' . '"' . Input::get('name') . '"' . '  can not be Deleted';
                        } else {
                            // $clearData = $override->deleteDataTable(Input::get('name'), Input::get('site'));
                            $deleteData = $user->deleteRecord(Input::get('name'), 'created_on', Input::get('date2'));
                            $successMessage = 'Data on Table ' . '"' . Input::get('name') . ' On site "' . '"' . $site_id . '"' . ' On date "' . '"' . Input::get('date2') . '"' . ' Deleted Successfull';
                        }
                    } else {
                        $errorMessage = 'Data on Table ' . '"' . Input::get('name') . '"' . '  can not be Found!';
                    }
                    // die;
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_medications')) {
            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
                'cardiac' => array(
                    'required' => true,
                ),
                'diabetes' => array(
                    'required' => true,
                ),
                'sickle_cell' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    if (Input::get('action') == 'edit') {
                        $user->updateRecord('medications', array(
                            'name' => Input::get('name'),
                            'cardiac' => Input::get('cardiac'),
                            'diabetes' => Input::get('diabetes'),
                            'sickle_cell' => Input::get('sickle_cell'),
                            'status' => 1,
                        ), Input::get('id'));
                        $successMessage = 'Medications Successful Updated';
                    } elseif (Input::get('action') == 'add') {

                        $medications = $override->get('medications', 'name', Input::get('name'));
                        if ($medications) {
                            $errorMessage = 'Medications Already  Available Please Update instead!';
                        } else {
                            $user->createRecord('medications', array(
                                'name' => Input::get('name'),
                                'cardiac' => Input::get('cardiac'),
                                'diabetes' => Input::get('diabetes'),
                                'sickle_cell' => Input::get('sickle_cell'),
                                'status' => 1,
                            ));
                            $successMessage = 'Medications Successful Added';
                        }
                    }
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('DoctorConfirm')) {

            $validate = $validate->check($_POST, array(
                // 'name' => array(
                //     'required' => true,
                // ),
            ));
            if ($validate->passed()) {
                try {
                    $setSiteId = $override->DoctorConfirm('screening', 'doctor_confirm', Input::get('name'), 1);
                    $successMessage = 'Doctor Confirm Successfull Updated';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('delete_medication')) {
            $user->updateRecord('medications', array(
                'status' => 0,
            ), Input::get('id'));
            $successMessage = Input::get('name') . ' - ' . 'Medications Deleted Successful';
        } elseif (Input::get('delete_batch')) {
            $user->updateRecord('batch', array(
                'status' => 0,
            ), Input::get('id'));
            $successMessage = Input::get('name') . ' - ' . 'Medications Deleted Successful';
        } elseif (Input::get('search_by_site1')) {
            $validate = $validate->check($_POST, array(
                'site_id' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                if (Input::get('status')) {
                    $url = 'info.php?id=3&status=' . Input::get('status') . '&site_id=' . Input::get('site_id');
                } else {
                    $url = 'info.php?id=' . $_GET['id'] . '&site_id=' . Input::get('site_id');
                }
                Redirect::to($url);
                $pageError = $validate->errors();
            }
        } elseif (Input::get('increase_batch')) {
            $validate = $validate->check($_POST, array(
                'date' => array(
                    'required' => true,
                ),
                'cost' => array(
                    'required' => true,
                ),
                'price' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $batch = $override->getNews('batch', 'status', 1, 'id', Input::get('id'))[0];
                    $amount = $batch['amount'] + Input::get('received');
                    $price = $batch['price'] + Input::get('cost');

                    $user->updateRecord('batch', array(
                        'amount' => $amount,
                        'price' => $price,
                    ), Input::get('id'));

                    $user->createRecord('batch_records', array(
                        'date' => Input::get('date'),
                        'batch_id' => $batch['id'],
                        'medication_id' => $batch['medication_id'],
                        'serial_name' => $batch['serial_name'],
                        'received' => Input::get('received'),
                        'removed' => 0,
                        'amount' => $amount,
                        'expire_date' => $batch['expire_date'],
                        'remarks' => Input::get('remarks'),
                        'cost' => Input::get('cost'),
                        'price' => $price,
                        'status' => 1,
                        'create_on' => date('Y-m-d H:i:s'),
                        'site_id' => $user->data()->site_id,
                        'staff_id' => $user->data()->id,
                    ));

                    $successMessage = 'Medication name : ' . Input::get('name') . ' : Batch : ' . Input::get('serial_name') . ' - ' . Input::get('removed') . ' Increased Successful';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('decrease_batch')) {
            $validate = $validate->check($_POST, array(
                'date' => array(
                    'required' => true,
                ),
                'removed' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $batch = $override->getNews('batch', 'status', 1, 'id', Input::get('id'))[0];
                    if (Input::get('removed') <= $batch['amount']) {
                        $amount = $batch['amount'] - Input::get('removed');

                        $user->updateRecord('batch', array(
                            'amount' => $amount,
                        ), Input::get('id'));

                        $user->createRecord('batch_records', array(
                            'date' => Input::get('date'),
                            'batch_id' => $batch['id'],
                            'medication_id' => $batch['medication_id'],
                            'serial_name' => $batch['serial_name'],
                            'received' => 0,
                            'removed' => Input::get('removed'),
                            'amount' => $amount,
                            'expire_date' => $batch['expire_date'],
                            'remarks' => Input::get('remarks'),
                            'cost' => 0,
                            'price' => $batch['price'],
                            'status' => 1,
                            'create_on' => date('Y-m-d H:i:s'),
                            'site_id' => $user->data()->site_id,
                            'staff_id' => $user->data()->id,
                        ));

                        $successMessage = 'Medication name : ' . Input::get('name') . ' : Batch : ' . Input::get('serial_name') . ' - ' . Input::get('removed') . ' Decreased Successful';
                    } else {
                        $errorMessage = 'Batch to remove exceeds the available Amount';
                    }
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        }
    }


    if ($user->data()->power == 1 || $user->data()->accessLevel == 1 || $user->data()->accessLevel == 2) {
        if ($_GET['site_id'] != null) {
            $screened = $override->countData2('clients', 'status', 1, 'screened', 1, 'site_id', $_GET['site_id']);
            $eligible = $override->countData2('clients', 'status', 1, 'eligible', 1, 'site_id', $_GET['site_id']);
            $enrolled = $override->countData2('clients', 'status', 1, 'enrolled', 1, 'site_id', $_GET['site_id']);
            $end = $override->countData2('clients', 'status', 1, 'end_study', 1, 'site_id', $_GET['site_id']);
        } else {
            $screened = $override->countData('clients', 'status', 1, 'screened', 1);
            $eligible = $override->countData('clients', 'status', 1, 'eligible', 1);
            $enrolled = $override->countData('clients', 'status', 1, 'enrolled', 1);
            $end = $override->countData('clients', 'status', 1, 'end_study', 1);
        }
    } else {
        $screened = $override->countData2('clients', 'status', 1, 'screened', 1, 'site_id', $user->data()->site_id);
        $eligible = $override->countData2('clients', 'status', 1, 'eligible', 1, 'site_id', $user->data()->site_id);
        $enrolled = $override->countData2('clients', 'status', 1, 'enrolled', 1, 'site_id', $user->data()->site_id);
        $end = $override->countData2('clients', 'status', 1, 'end_study', 1, 'site_id', $user->data()->site_id);
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
    <title>Nimregenin Database | Info</title>

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
        <?php } elseif ($_GET['msg']) { ?>
            <div class="alert alert-success text-center">
                <h4>Success!</h4>
                <?= $_GET['msg'] ?>
            </div>
        <?php } ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <?php
                $Site = '';
                if ($user->data()->accessLevel == 1 || $user->data()->accessLevel == 3) {
                    $Site = 'ALL SITES';
                    if ($_GET['site_id']) {
                        $Site = $override->getNews('site', 'status', 1, 'id', $_GET['site_id'])[0]['name'];
                    }
                } else {
                    $Site = $override->getNews('site', 'status', 1, 'id', $user->data()->site_id)[0]['name'];
                }
                ?>
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>
                                <?php
                                if ($_GET['status'] == 1) {
                                    echo $title = 'Screening for ' . $Site;
                                    ?>
                                    <?php
                                } elseif ($_GET['status'] == 2) {
                                    echo $title = 'Eligibility  for ' . $Site;
                                    ?>
                                    <?php
                                } elseif ($_GET['status'] == 3) {
                                    echo $title = 'Enrollment for ' . $Site;
                                    ?>
                                    <?php
                                } elseif ($_GET['status'] == 4) {
                                    echo $title = 'Termination for ' . $Site;
                                    ?>
                                    <?php
                                } elseif ($_GET['status'] == 5) {
                                    echo $title = 'Registration for ' . $Site; ?>
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
                        <div class="col-md-12">
                            <?php
                            if ($user->data()->accessLevel == 1 || $user->data()->accessLevel == 3) {
                                if ($_GET['site_id'] != null) {
                                    $pagNum = 0;
                                    if ($_GET['status'] == 1) {
                                        $pagNum = $override->countData2('clients', 'status', 1, 'screened', 1, 'site_id', $_GET['site_id']);
                                    } elseif ($_GET['status'] == 2) {
                                        $pagNum = $override->countData2('clients', 'status', 1, 'eligible', 1, 'site_id', $_GET['site_id']);
                                    } elseif ($_GET['status'] == 3) {
                                        $pagNum = $override->countData2('clients', 'status', 1, 'enrolled', 1, 'site_id', $_GET['site_id']);
                                    } elseif ($_GET['status'] == 4) {
                                        $pagNum = $override->countData2('clients', 'status', 1, 'end_study', 1, 'site_id', $_GET['site_id']);
                                    } elseif ($_GET['status'] == 5) {
                                        $pagNum = $override->countData('clients', 'status', 1, 'site_id', $_GET['site_id']);
                                    } elseif ($_GET['status'] == 6) {
                                        $pagNum = $override->countData2('clients', 'status', 1, 'screened', 0, 'site_id', $_GET['site_id']);
                                    } elseif ($_GET['status'] == 7) {
                                        $pagNum = $override->getCount('clients', 'site_id', $_GET['site_id']);
                                    } elseif ($_GET['status'] == 8) {
                                        $pagNum = $override->countData('clients', 'status', 0, 'site_id', $_GET['sid']);
                                    }
                                    $pages = ceil($pagNum / $numRec);
                                    if (!$_GET['page'] || $_GET['page'] == 1) {
                                        $page = 0;
                                    } else {
                                        $page = ($_GET['page'] * $numRec) - $numRec;
                                    }

                                    if ($_GET['status'] == 1) {
                                        if ($_GET['search_name']) {
                                            $searchTerm = $_GET['search_name'];
                                            $clients = $override->getWithLimit3Search('clients', 'status', 1, 'screened', 1, 'site_id', $_GET['site_id'], $page, $numRec, $searchTerm, 'firstname', 'middlename', 'lastname', 'study_id');
                                        } else {
                                            $clients = $override->getWithLimit3('clients', 'status', 1, 'screened', 1, 'site_id', $_GET['site_id'], $page, $numRec);
                                        }
                                    } elseif ($_GET['status'] == 2) {
                                        if ($_GET['search_name']) {
                                            $searchTerm = $_GET['search_name'];
                                            $clients = $override->getWithLimit3Search('clients', 'status', 1, 'eligible', 1, 'site_id', $_GET['site_id'], $page, $numRec, $searchTerm, 'firstname', 'middlename', 'lastname', 'study_id');
                                        } else {
                                            $clients = $override->getWithLimit3('clients', 'status', 1, 'eligible', 1, 'site_id', $_GET['site_id'], $page, $numRec);
                                        }
                                    } elseif ($_GET['status'] == 3) {
                                        if ($_GET['search_name']) {
                                            $searchTerm = $_GET['search_name'];
                                            $clients = $override->getWithLimit3Search('clients', 'status', 1, 'enrolled', 1, 'site_id', $_GET['site_id'], $page, $numRec, $searchTerm, 'firstname', 'middlename', 'lastname', 'study_id');
                                        } else {
                                            $clients = $override->getWithLimit3('clients', 'status', 1, 'enrolled', 1, 'site_id', $_GET['site_id'], $page, $numRec);
                                        }
                                    } elseif ($_GET['status'] == 4) {
                                        if ($_GET['search_name']) {
                                            $searchTerm = $_GET['search_name'];
                                            $clients = $override->getWithLimit3Search('clients', 'status', 1, 'end_study', 1, 'site_id', $_GET['site_id'], $page, $numRec, $searchTerm, 'firstname', 'middlename', 'lastname', 'study_id');
                                        } else {
                                            $clients = $override->getWithLimit3('clients', 'status', 1, 'end_study', 1, 'site_id', $_GET['site_id'], $page, $numRec);
                                        }
                                    } elseif ($_GET['status'] == 5) {
                                        if ($_GET['search_name']) {
                                            $searchTerm = $_GET['search_name'];
                                            $clients = $override->getWithLimit1Search('clients', 'status', 1, 'site_id', $_GET['site_id'], $page, $numRec, $searchTerm, 'firstname', 'middlename', 'lastname', 'study_id');
                                        } else {
                                            $clients = $override->getWithLimit1('clients', 'status', 1, 'site_id', $_GET['site_id'], $page, $numRec);
                                        }
                                    } elseif ($_GET['status'] == 6) {
                                        if ($_GET['search_name']) {
                                            $searchTerm = $_GET['search_name'];
                                            $clients = $override->getWithLimit3Search('clients', 'status', 1, 'screened', 0, 'site_id', $_GET['site_id'], $page, $numRec, $searchTerm, 'firstname', 'middlename', 'lastname', 'study_id');
                                        } else {
                                            $clients = $override->getWithLimit3('clients', 'status', 1, 'screened', 0, 'site_id', $_GET['site_id'], $page, $numRec);
                                        }
                                    } elseif ($_GET['status'] == 7) {
                                        if ($_GET['search_name']) {
                                            $searchTerm = $_GET['search_name'];
                                            $clients = $override->getWithLimitSearch('clients', 'site_id', $_GET['site_id'], $page, $numRec, $searchTerm, 'firstname', 'middlename', 'lastname', 'study_id');
                                        } else {
                                            $clients = $override->getWithLimit('clients', 'site_id', $_GET['site_id'], $page, $numRec);
                                        }
                                    } elseif ($_GET['status'] == 8) {
                                        if ($_GET['search_name']) {
                                            $searchTerm = $_GET['search_name'];
                                            $clients = $override->getWithLimit1Search('clients', 'status', 0, 'site_id', $_GET['site_id'], $page, $numRec, $searchTerm, 'firstname', 'middlename', 'lastname', 'study_id');
                                        } else {
                                            $clients = $override->getWithLimit1('clients', 'status', 0, 'site_id', $_GET['site_id'], $page, $numRec);
                                        }
                                    }
                                } else {

                                    $pagNum = 0;
                                    if ($_GET['status'] == 1) {
                                        $pagNum = $override->getCount1('clients', 'status', 1, 'screened', 1);
                                    } elseif ($_GET['status'] == 2) {
                                        $pagNum = $override->getCount1('clients', 'status', 1, 'eligible', 1);
                                    } elseif ($_GET['status'] == 3) {
                                        $pagNum = $override->getCount1('clients', 'status', 1, 'enrolled', 1);
                                    } elseif ($_GET['status'] == 4) {
                                        $pagNum = $override->getCount1('clients', 'status', 1, 'end_study', 1);
                                    } elseif ($_GET['status'] == 5) {
                                        $pagNum = $override->getCount('clients', 'status', 1);
                                    } elseif ($_GET['status'] == 6) {
                                        $pagNum = $override->getCount1('clients', 'status', 1, 'screened', 0);
                                    } elseif ($_GET['status'] == 7) {
                                        $clients = $override->getNo('clients');
                                    } elseif ($_GET['status'] == 8) {
                                        $pagNum = $override->getCount('clients', 'status', 0);
                                    }
                                    $pages = ceil($pagNum / $numRec);
                                    if (!$_GET['page'] || $_GET['page'] == 1) {
                                        $page = 0;
                                    } else {
                                        $page = ($_GET['page'] * $numRec) - $numRec;
                                    }

                                    if ($_GET['status'] == 1) {
                                        if ($_GET['search_name']) {
                                            $searchTerm = $_GET['search_name'];
                                            $clients = $override->getWithLimit1Search('clients', 'status', 1, 'screened', 1, $page, $numRec, $searchTerm, 'firstname', 'middlename', 'lastname', 'study_id');
                                        } else {
                                            $clients = $override->getWithLimit1('clients', 'status', 1, 'screened', 1, $page, $numRec);
                                        }
                                    } elseif ($_GET['status'] == 2) {
                                        if ($_GET['search_name']) {
                                            $searchTerm = $_GET['search_name'];
                                            $clients = $override->getWithLimit1Search('clients', 'status', 1, 'eligible', 1, $page, $numRec, $searchTerm, 'firstname', 'middlename', 'lastname', 'study_id');
                                        } else {
                                            $clients = $override->getWithLimit1('clients', 'status', 1, 'eligible', 1, $page, $numRec);
                                        }
                                    } elseif ($_GET['status'] == 3) {
                                        if ($_GET['search_name']) {
                                            $searchTerm = $_GET['search_name'];
                                            $clients = $override->getWithLimit1Search('clients', 'status', 1, 'enrolled', 1, $page, $numRec, $searchTerm, 'firstname', 'middlename', 'lastname', 'study_id');
                                        } else {
                                            $clients = $override->getWithLimit1('clients', 'status', 1, 'enrolled', 1, $page, $numRec);
                                        }
                                    } elseif ($_GET['status'] == 4) {
                                        if ($_GET['search_name']) {
                                            $searchTerm = $_GET['search_name'];
                                            $clients = $override->getWithLimit1Search('clients', 'status', 1, 'end_study', 1, $page, $numRec, $searchTerm, 'firstname', 'middlename', 'lastname', 'study_id');
                                        } else {
                                            $clients = $override->getWithLimit1('clients', 'status', 1, 'end_study', 1, $page, $numRec);
                                        }
                                    } elseif ($_GET['status'] == 5) {
                                        if ($_GET['search_name']) {
                                            $searchTerm = $_GET['search_name'];
                                            $clients = $override->getWithLimitSearch('clients', 'status', 1, $page, $numRec, $searchTerm, 'firstname', 'middlename', 'lastname', 'study_id');
                                        } else {
                                            $clients = $override->getWithLimit('clients', 'status', 1, $page, $numRec);
                                        }
                                    } elseif ($_GET['status'] == 6) {
                                        if ($_GET['search_name']) {
                                            $searchTerm = $_GET['search_name'];
                                            $clients = $override->getWithLimit1Search('clients', 'status', 1, 'screened', 0, $page, $numRec, $searchTerm, 'firstname', 'middlename', 'lastname', 'study_id');
                                        } else {
                                            $clients = $override->getWithLimit1('clients', 'status', 1, 'screened', 0, $page, $numRec);
                                        }
                                    } elseif ($_GET['status'] == 7) {
                                        if ($_GET['search_name']) {
                                            $searchTerm = $_GET['search_name'];
                                            $clients = $override->getDataLimitSearch('clients', $page, $numRec, $searchTerm, 'firstname', 'middlename', 'lastname', 'study_id');
                                        } else {
                                            $clients = $override->getDataLimit('clients', $page, $numRec);
                                        }
                                    } elseif ($_GET['status'] == 8) {
                                        if ($_GET['search_name']) {
                                            $searchTerm = $_GET['search_name'];
                                            $clients = $override->getWithLimitSearch('clients', 'status', 0, $page, $numRec, $searchTerm, 'firstname', 'middlename', 'lastname', 'study_id');
                                        } else {
                                            $clients = $override->getWithLimit('clients', 'status', 0, $page, $numRec);
                                        }
                                    }
                                }
                            } else {
                                $pagNum = 0;
                                if ($_GET['status'] == 1) {
                                    $pagNum = $override->countData2('clients', 'status', 1, 'screened', 1, 'site_id', $user->data()->site_id);
                                } elseif ($_GET['status'] == 2) {
                                    $pagNum = $override->countData2('clients', 'status', 1, 'eligible', 1, 'site_id', $user->data()->site_id);
                                } elseif ($_GET['status'] == 3) {
                                    $pagNum = $override->countData2('clients', 'status', 1, 'enrolled', 1, 'site_id', $user->data()->site_id);
                                } elseif ($_GET['status'] == 4) {
                                    $pagNum = $override->countData2('clients', 'status', 1, 'end_study', 1, 'site_id', $user->data()->site_id);
                                } elseif ($_GET['status'] == 5) {
                                    $pagNum = $override->countData('clients', 'status', 1, 'site_id', $user->data()->site_id);
                                } elseif ($_GET['status'] == 6) {
                                    $pagNum = $override->countData2('clients', 'status', 1, 'screened', 0, 'site_id', $user->data()->site_id);
                                } elseif ($_GET['status'] == 7) {
                                    $pagNum = $override->getCount('clients', 'site_id', $user->data()->site_id);
                                } elseif ($_GET['status'] == 8) {
                                    $pagNum = $override->countData('clients', 'status', 0, 'site_id', $user->data()->site_id);
                                }
                                $pages = ceil($pagNum / $numRec);
                                if (!$_GET['page'] || $_GET['page'] == 1) {
                                    $page = 0;
                                } else {
                                    $page = ($_GET['page'] * $numRec) - $numRec;
                                }
                                if ($_GET['status'] == 1) {
                                    if ($_GET['search_name']) {
                                        $searchTerm = $_GET['search_name'];
                                        $clients = $override->getWithLimit3Search('clients', 'status', 1, 'screened', 1, 'site_id', $user->data()->site_id, $page, $numRec, $searchTerm, 'firstname', 'middlename', 'lastname', 'study_id');
                                    } else {
                                        $clients = $override->getWithLimit3('clients', 'status', 1, 'screened', 1, 'site_id', $user->data()->site_id, $page, $numRec);
                                    }
                                } elseif ($_GET['status'] == 2) {
                                    if ($_GET['search_name']) {
                                        $searchTerm = $_GET['search_name'];
                                        $clients = $override->getWithLimit3Search('clients', 'status', 1, 'eligible', 1, 'site_id', $user->data()->site_id, $page, $numRec, $searchTerm, 'firstname', 'middlename', 'lastname', 'study_id');
                                    } else {
                                        $clients = $override->getWithLimit3('clients', 'status', 1, 'eligible', 1, 'site_id', $user->data()->site_id, $page, $numRec);
                                    }
                                } elseif ($_GET['status'] == 3) {
                                    if ($_GET['search_name']) {
                                        $searchTerm = $_GET['search_name'];
                                        $clients = $override->getWithLimit3Search('clients', 'status', 1, 'enrolled', 1, 'site_id', $user->data()->site_id, $page, $numRec, $searchTerm, 'firstname', 'middlename', 'lastname', 'study_id');
                                    } else {
                                        $clients = $override->getWithLimit3('clients', 'status', 1, 'enrolled', 1, 'site_id', $user->data()->site_id, $page, $numRec);
                                    }
                                } elseif ($_GET['status'] == 4) {
                                    if ($_GET['search_name']) {
                                        $searchTerm = $_GET['search_name'];
                                        $clients = $override->getWithLimit3Search('clients', 'status', 1, 'end_study', 1, 'site_id', $user->data()->site_id, $page, $numRec, $searchTerm, 'firstname', 'middlename', 'lastname', 'study_id');
                                    } else {
                                        $clients = $override->getWithLimit3('clients', 'status', 1, 'end_study', 1, 'site_id', $user->data()->site_id, $page, $numRec);
                                    }
                                } elseif ($_GET['status'] == 5) {
                                    if ($_GET['search_name']) {
                                        $searchTerm = $_GET['search_name'];
                                        $clients = $override->getWithLimit1Search('clients', 'status', 1, 'site_id', $user->data()->site_id, $page, $numRec, $searchTerm, 'firstname', 'middlename', 'lastname', 'study_id');
                                    } else {
                                        $clients = $override->getWithLimit1('clients', 'status', 1, 'site_id', $user->data()->site_id, $page, $numRec);
                                    }
                                } elseif ($_GET['status'] == 6) {
                                    if ($_GET['search_name']) {
                                        $searchTerm = $_GET['search_name'];
                                        $clients = $override->getWithLimit3Search('clients', 'status', 1, 'screened', 1, 'site_id', $user->data()->site_id, $page, $numRec, $searchTerm, 'firstname', 'middlename', 'lastname', 'study_id');
                                    } else {
                                        $clients = $override->getWithLimit3('clients', 'status', 1, 'screened', 0, 'site_id', $user->data()->site_id, $page, $numRec);
                                    }
                                } elseif ($_GET['status'] == 7) {
                                    if ($_GET['search_name']) {
                                        $searchTerm = $_GET['search_name'];
                                        $clients = $override->getWithLimitSearch('clients', 'site_id', $user->data()->site_id, $page, $numRec, $searchTerm, 'firstname', 'middlename', 'lastname', 'study_id');
                                    } else {
                                        $clients = $override->getWithLimit('clients', 'site_id', $user->data()->site_id, $page, $numRec);
                                    }
                                } elseif ($_GET['status'] == 8) {
                                    if ($_GET['search_name']) {
                                        $searchTerm = $_GET['search_name'];
                                        $clients = $override->getWithLimit1Search('clients', 'status', 0, 'site_id', $user->data()->site_id, $page, $numRec, $searchTerm, 'firstname', 'middlename', 'lastname', 'study_id');
                                    } else {
                                        $clients = $override->getWithLimit1('clients', 'status', 0, 'site_id', $user->data()->site_id, $page, $numRec);
                                    }
                                }
                            }
                            ?>
                            <hr>

                            <div class="card">
                                <div class="row mb-2">
                                    <div class="col-sm-12">
                                        <div class="card-header">
                                            <?php
                                            $patient = $override->get('clients', 'id', $_GET['cid'])[0];

                                            // $visits_status = $override->firstRow1('visit', 'status', 'id', 'client_id', $_GET['cid'], 'visit_code', 'EV')[0]['status'];
                                            
                                            // $patient = $override->get('clients', 'id', $_GET['cid'])[0];
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

                                            $name = 'Name: ' . $patient['firstname'] . ' ' . $patient['lastname'] . ' Age: ' . $patient['age'] . ' Gender: ' . $gender . ' Type: ' . $cat;

                                            ?>
                                            <?php
                                            if ($_GET['status'] == 1) { ?>
                                                <h3 class="card-title">List of Screened Clients for <?= $Site; ?></h3>
                                                &nbsp;&nbsp;
                                                <span class="badge badge-info right"><?= $screened; ?></span>
                                                <?php
                                            } elseif ($_GET['status'] == 2) { ?>
                                                <h3 class="card-title">List of Eligible Clients for <?= $Site; ?></h3>
                                                &nbsp;&nbsp;
                                                <span class="badge badge-info right"><?= $eligible; ?></span>
                                                <?php
                                            } elseif ($_GET['status'] == 3) { ?>
                                                <h3 class="card-title">List of Enrolled Clients for <?= $Site; ?></h3>
                                                &nbsp;&nbsp;
                                                <span class="badge badge-info right"><?= $enrolled; ?></span>
                                                <?php
                                            } elseif ($_GET['status'] == 4) { ?>
                                                <h3 class="card-title">List of Terminated Clients for <?= $Site; ?></h3>
                                                &nbsp;&nbsp;
                                                <span class="badge badge-info right"><?= $end; ?></span>
                                                <?php
                                            } elseif ($_GET['status'] == 5) { ?>
                                                <h3 class="card-title">List of Registered Clients for <?= $Site; ?></h3>
                                                &nbsp;&nbsp;
                                                <span class="badge badge-info right"><?= $registered; ?></span>
                                                <?php
                                            } elseif ($_GET['status'] == 7) { ?>
                                                <h3 class="card-title">List of Registered Clients for <?= $Site; ?></h3>
                                                &nbsp;&nbsp;
                                                <span class="badge badge-info right"><?= $registered; ?></span>
                                            <?php } ?>
                                            <div class="card-tools">
                                                <ul class="pagination pagination-sm float-right">
                                                    <li class="page-item"><a class="page-link" href="index1.php">&laquo;
                                                            Back</a></li>
                                                    <li class="page-item"><a class="page-link" href="index1.php">&raquo;
                                                            Home</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <hr>

                                        <?php
                                        if ($user->data()->accessLevel == 1 || $user->data()->accessLevel == 3) {
                                            ?>
                                            <div class="card-tools">
                                                <div class="input-group input-group-sm float-left" style="width: 350px;">
                                                    <form method="post">
                                                        <div class="form-inline">
                                                            <div class="input-group-append">
                                                                <div class="col-sm-12">
                                                                    <select class="form-control float-right" name="site_id"
                                                                        style="width: 100%;" autocomplete="off">
                                                                        <option value="">Select Site</option>
                                                                        <?php foreach ($override->get('site', 'status', 1) as $site) { ?>
                                                                            <option value="<?= $site['id'] ?>">
                                                                                <?= $site['name'] ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                                <input type="submit" name="search_by_site1"
                                                                    value="Search by Site" class="btn btn-info"><i
                                                                    class="fas fa-search"></i>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        <?php } ?>

                                        <div class="card-tools">
                                            <div class="input-group input-group-sm float-right" style="width: 350px;">
                                                <form method="get">
                                                    <div class="form-inline">
                                                        <input type="hidden" name="id" value="<?= $_GET['id'] ?>">
                                                        <!-- <input type="hidden" name="site_id" value="<?= $_GET['site_id'] ?>"> -->
                                                        <input type="hidden" name="status"
                                                            value="<?= $_GET['status'] ?>">
                                                        <input type="text" name="search_name" id="search_name"
                                                            class="form-control float-right"
                                                            placeholder="Search here Names or Study ID">
                                                        <input type="submit" value="Search" class="btn btn-default"><i
                                                            class="fas fa-search"></i>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">