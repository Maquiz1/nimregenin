<?php
require_once 'php/core/init.php';
$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();

$successMessage = null;
$pageError = null;
$errorMessage = null;
$numRec = 10;
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
        } elseif (Input::get('edit_staff')) {
            $validate = new validate();
            $validate = $validate->check($_POST, array(
                'firstname' => array(
                    'required' => true,
                ),
                'lastname' => array(
                    'required' => true,
                ),
                'position' => array(
                    'required' => true,
                ),
                'phone_number' => array(
                    'required' => true,
                ),
                'email_address' => array(),
            ));
            if ($validate->passed()) {
                $salt = $random->get_rand_alphanumeric(32);
                $password = '12345678';
                switch (Input::get('position')) {
                    case 1:
                        $accessLevel = 1;
                        break;
                    case 2:
                        $accessLevel = 2;
                        break;
                    case 3:
                        $accessLevel = 3;
                        break;
                }
                try {
                    $user->updateRecord('user', array(
                        'firstname' => Input::get('firstname'),
                        'lastname' => Input::get('lastname'),
                        'position' => Input::get('position'),
                        'phone_number' => Input::get('phone_number'),
                        'email_address' => Input::get('email_address'),
                        'accessLevel' => $accessLevel,
                        'power' => Input::get('power'),
                        'site_id' => Input::get('site'),
                        'user_id' => $user->data()->id,
                    ), Input::get('id'));

                    $successMessage = 'Account Updated Successful';
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
        } elseif (Input::get('unlock_account')) {
            $user->updateRecord('user', array(
                'count' => 0,
            ), Input::get('id'));
            $successMessage = 'Account Unlock Successful';
        } elseif (Input::get('change_screening_status')) {
            $user->updateRecord('clients', array(
                'screened' => Input::get('screened'),
            ), Input::get('id'));
            $successMessage = 'Screening status Updated Successful';
        } elseif (Input::get('change_eligibility1_status')) {
            $user->updateRecord('clients', array(
                'eligibility1' => Input::get('eligibility1'),
            ), Input::get('id'));
            $successMessage = 'Eligibility1 status Updated Successful';
        } elseif (Input::get('change_eligibility2_status')) {
            $user->updateRecord('clients', array(
                'eligibility2' => Input::get('eligibility2'),
            ), Input::get('id'));
            $successMessage = 'Eligibility2 status Updated Successful';
        } elseif (Input::get('change_eligible_status')) {
            $user->updateRecord('clients', array(
                'eligible' => Input::get('eligible'),
            ), Input::get('id'));
            $successMessage = 'Eligible status Updated Successful';
        } elseif (Input::get('change_enrolled_status')) {
            $user->updateRecord('clients', array(
                'enrolled' => Input::get('enrolled'),
            ), Input::get('id'));
            $successMessage = 'Enrolled status Updated Successful';
        } elseif (Input::get('delete_staff')) {
            $user->updateRecord('user', array(
                'status' => 0,
            ), Input::get('id'));
            $successMessage = 'User Deleted Successful';
        } elseif (Input::get('delete_client')) {
            $user->updateRecord('clients', array(
                'status' => 0,
            ), Input::get('id'));
            $successMessage = 'Client Deleted Successful';
        } elseif (Input::get('delete_schedule')) {
            $user->visit_delete(Input::get('id'));
            // $this->deleteRecord('visit', 'client_id', Input::get('id'));
            $successMessage = 'Client Schedule Deleted Successful';
        } elseif (Input::get('asign_id')) {
            $client_study = $override->getNews('clients', 'id', Input::get('id'), 'status', 1)[0];
            $std_id = $override->getNews('study_id', 'site_id', $user->data()->site_id, 'status', 0)[0];
            $screening_id = $override->get('screening', 'client_id', Input::get('id'))[0];
            $lab_id = $override->get('lab', 'client_id', Input::get('id'))[0];
            $visit_id = $override->get('visit', 'client_id', Input::get('id'))[0];

            if (!$client_study['study_id']) {
                $study_id = $std_id['study_id'];
            } else {
                $study_id = $client_study['study_id'];
            }


            $user->updateRecord('study_id', array('status' => 1, 'client_id' => Input::get('id')), $std_id['id']);
            $user->updateRecord('screening', array('study_id' => $std_id['study_id']), $screening_id['id']);
            $user->updateRecord('lab', array('study_id' => $std_id['study_id']), $lab_id['id']);
            $user->updateRecord('clients', array('study_id' => $std_id['study_id'], 'enrolled' => 1), Input::get('id'));

            // print_r($visit_id);

            // $user->visit_delete(Input::get('id'));
            // $this->deleteRecord('visit', 'client_id', Input::get('id'));
            $successMessage = 'Client Schedule Deleted Successful';
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
        } elseif (Input::get('edit_client')) {
            $validate = $validate->check($_POST, array(
                'clinic_date' => array(
                    'required' => true,
                ),
                'firstname' => array(
                    'required' => true,
                ),
                'lastname' => array(
                    'required' => true,
                ),
                'dob' => array(
                    'required' => true,
                ),
                'street' => array(
                    'required' => true,
                ),
                'phone_number' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $attachment_file = Input::get('image');
                    if (!empty($_FILES['image']["tmp_name"])) {
                        $attach_file = $_FILES['image']['type'];
                        if ($attach_file == "image/jpeg" || $attach_file == "image/jpg" || $attach_file == "image/png" || $attach_file == "image/gif") {
                            $folderName = 'clients/';
                            $attachment_file = $folderName . basename($_FILES['image']['name']);
                            if (@move_uploaded_file($_FILES['image']["tmp_name"], $attachment_file)) {
                                $file = true;
                            } else { {
                                    $errorM = true;
                                    $errorMessage = 'Your profile Picture Not Uploaded ,';
                                }
                            }
                        } else {
                            $errorM = true;
                            $errorMessage = 'None supported file format';
                        } //not supported format
                    } else {
                        $attachment_file = '';
                    }
                    if (!empty($_FILES['image']["tmp_name"])) {
                        $image = $attachment_file;
                    } else {
                        $image = Input::get('client_image');
                    }
                    if ($errorM == false) {
                        $age = $user->dateDiffYears(date('Y-m-d'), Input::get('dob'));
                        $user->updateRecord('clients', array(
                            'clinic_date' => Input::get('clinic_date'),
                            'firstname' => Input::get('firstname'),
                            'middlename' => Input::get('middlename'),
                            'lastname' => Input::get('lastname'),
                            'dob' => Input::get('dob'),
                            'age' => $age,
                            'id_number' => Input::get('id_number'),
                            'gender' => Input::get('gender'),
                            'marital_status' => Input::get('marital_status'),
                            'education_level' => Input::get('education_level'),
                            'workplace' => Input::get('workplace'),
                            'occupation' => Input::get('occupation'),
                            'national_id' => Input::get('national_id'),
                            'phone_number' => Input::get('phone_number'),
                            'other_phone' => Input::get('other_phone'),
                            'region' => Input::get('region'),
                            'district' => Input::get('district'),
                            'street' => Input::get('street'),
                            'ward' => Input::get('ward'),
                            'block_no' => Input::get('block_no'),
                            'site_id' => $user->data()->site_id,
                            'staff_id' => $user->data()->id,
                            'client_image' => $image,
                            'comments' => Input::get('comments'),
                            'initials' => Input::get('initials'),
                            'status' => 1,
                        ), Input::get('id'));

                        $successMessage = 'Client Updated Successful';
                    }
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('edit_visit2')) {
            $validate = $validate->check($_POST, array(
                'visit_date' => array(
                    'required' => true,
                ),
                'visit_status' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('visit', array(
                        'visit_date' => Input::get('visit_date'),
                        'created_on' => date('Y-m-d'),
                        'status' => 1,
                        'visit_status' => Input::get('visit_status'),
                    ), Input::get('id'));

                    if (Input::get('seq') == 2) {
                        $user->createRecord('visit', array(
                            'study_id' => $_GET['sid'],
                            'visit_name' => 'Visit 3',
                            'visit_code' => 'V3',
                            'visit_window' => 14,
                            'status' => 0,
                            'seq_no' => 3,
                            'client_id' => Input::get('cid'),
                        ));
                    }
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_screening')) {
            $validate = $validate->check($_POST, array());
            if ($validate->passed()) {
                $eligibility = 0;
                if (
                    Input::get('age_18') == 1 && Input::get('tr_pcr') == 1 && Input::get('hospitalized') == 1 &&
                    Input::get('moderate_severe') == 1 && Input::get('peptic_ulcers') == 2 && Input::get('consented') == 1 && (Input::get('pregnant') == 2 || Input::get('pregnant') == 3)
                ) {
                    $eligibility = 1;
                }
                try {
                    if ($override->get('screening', 'client_id', Input::get('cid'))) {
                        $cl_id = $override->get('screening', 'client_id', Input::get('cid'))[0]['id'];
                        $user->updateRecord('screening', array(
                            'sample_date' => Input::get('sample_date'),
                            'results_date' => Input::get('results_date'),
                            'covid_result' => Input::get('covid_result'),
                            'age_18' => Input::get('age_18'),
                            'tr_pcr' => Input::get('tr_pcr'),
                            'hospitalized' => Input::get('hospitalized'),
                            'moderate_severe' => Input::get('moderate_severe'),
                            'peptic_ulcers' => Input::get('peptic_ulcers'),
                            'pregnant' => Input::get('pregnant'),
                            'eligibility' => $eligibility,
                            'consented' => Input::get('consented'),
                            'created_on' => date('Y-m-d'),
                            'staff_id' => $user->data()->id,
                            'site_id' => $user->data()->site_id,
                            'status' => 1,
                            'client_id' => Input::get('cid'),
                        ), $cl_id);
                    } else {
                        $user->createRecord('screening', array(
                            'sample_date' => Input::get('sample_date'),
                            'results_date' => Input::get('results_date'),
                            'covid_result' => Input::get('covid_result'),
                            'age_18' => Input::get('age_18'),
                            'tr_pcr' => Input::get('tr_pcr'),
                            'hospitalized' => Input::get('hospitalized'),
                            'moderate_severe' => Input::get('moderate_severe'),
                            'peptic_ulcers' => Input::get('peptic_ulcers'),
                            'pregnant' => Input::get('pregnant'),
                            'eligibility' => $eligibility,
                            'consented' => Input::get('consented'),
                            'created_on' => date('Y-m-d'),
                            'staff_id' => $user->data()->id,
                            'site_id' => $user->data()->site_id,
                            'status' => 1,
                            'client_id' => Input::get('cid'),
                        ));
                    }
                    $user->updateRecord('clients', array('consented' => Input::get('consented')), Input::get('cid'));
                    $successMessage = 'Screening Successful Added';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_lab')) {
            $validate = $validate->check($_POST, array());
            if ($validate->passed()) {
                $eligibility = 0;
                $clnt = $override->get('clients', 'id', Input::get('cid'))[0];
                $sc_e = $override->get('screening', 'client_id', Input::get('cid'))[0];
                $std_id = $override->getNews('study_id', 'site_id', $user->data()->site_id, 'status', 0)[0];
                if (
                    (Input::get('wbc') >= 1.5 && Input::get('wbc') <= 11.0) && (Input::get('hb') >= 8.5 && Input::get('hb') <= 16.5)
                    && (Input::get('plt') >= 50 && Input::get('plt') <= 1000) && (Input::get('alt') >= 2.0 && Input::get('alt') <= 195.0)
                    && (Input::get('ast') >= 2.0 && Input::get('ast') <= 195.0)
                ) {
                    if ($clnt['gender'] == 'male' && (Input::get('sc') >= 44.0 && Input::get('sc') <= 158.4) && $sc_e['eligibility'] == 1) {
                        $eligibility = 1;
                        if ($override->getCount('visit', 'client_id', Input::get('cid')) == 1) {
                            $user->visit(Input::get('cid'), 0);
                            $user->updateRecord('study_id', array('status' => 1, 'client_id' => Input::get('cid')), $std_id['id']);
                            $user->updateRecord('clients', array('study_id' => $std_id['study_id'], 'enrolled' => 1), Input::get('cid'));
                        }
                    } elseif ($clnt['gender'] == 'female' && (Input::get('sc') >= 62.0 && Input::get('sc') <= 190.8) && $sc_e['eligibility'] == 1) {
                        $eligibility = 1;
                        if ($override->getCount('visit', 'client_id', Input::get('cid')) == 1) {
                            $user->visit(Input::get('cid'), 0);
                            $user->updateRecord('study_id', array('status' => 1, 'client_id' => Input::get('cid')), $std_id['id']);
                            $user->updateRecord('clients', array('study_id' => $std_id['study_id'], 'enrolled' => 1), Input::get('cid'));
                        }
                    }
                }
                try {
                    if ($override->get('lab', 'client_id', Input::get('cid'))) {
                        $l_id = $override->get('lab', 'client_id', Input::get('cid'))[0]['id'];
                        $user->updateRecord('lab', array(
                            'wbc' => Input::get('wbc'),
                            'hb' => Input::get('hb'),
                            'plt' => Input::get('plt'),
                            'alt' => Input::get('alt'),
                            'ast' => Input::get('ast'),
                            'sc' => Input::get('sc'),
                            'eligibility' => $eligibility,
                            'created_on' => date('Y-m-d'),
                            'staff_id' => $user->data()->id,
                            'site_id' => $user->data()->site_id,
                            'status' => 1,
                            'client_id' => Input::get('cid'),
                        ), $l_id);
                    } else {
                        $user->createRecord('lab', array(
                            'wbc' => Input::get('wbc'),
                            'hb' => Input::get('hb'),
                            'plt' => Input::get('plt'),
                            'alt' => Input::get('alt'),
                            'ast' => Input::get('ast'),
                            'sc' => Input::get('sc'),
                            'eligibility' => $eligibility,
                            'created_on' => date('Y-m-d'),
                            'staff_id' => $user->data()->id,
                            'site_id' => $user->data()->site_id,
                            'status' => 1,
                            'client_id' => Input::get('cid'),
                        ));
                    }

                    $successMessage = 'Screening Successful Added';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('edit_visit')) {
            $validate = $validate->check($_POST, array(
                'visit_date' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('visit', array(
                        'study_id' => Input::get('study_id'),
                        'visit_date' => Input::get('visit_date'),
                        'created_on' => date('Y-m-d'),
                        'status' => Input::get('visit_status'),
                        'visit_status' => Input::get('visit_status'),
                        'reasons' => Input::get('reasons'),
                    ), Input::get('id'));
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
                $url = 'info.php?id=3&status=' . $_GET['status'] . '&sid=' . Input::get('site');
                Redirect::to($url);
                $pageError = $validate->errors();
            }
        } elseif (Input::get('search_by_pid')) {
            $validate = $validate->check($_POST, array(
                'names' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                $url = 'info.php?id=3&status=' . $_GET['status'] . '&pid=' . Input::get('names');
                Redirect::to($url);
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_Visit')) {

            $validate = $validate->check($_POST, array());
            if ($validate->passed()) {
                $eligibility = 0;
                $clnt = $override->get('clients', 'id', Input::get('cid'))[0];
                $sc_e = $override->get('screening', 'client_id', Input::get('cid'))[0];
                $std_id = $override->getNews('study_id', 'site_id', $user->data()->site_id, 'status', 0)[0];
                if (
                    Input::get('age_18') == 1 && Input::get('biopsy') == 1 && Input::get('consented') == 1
                ) {
                    // $eligibility = 1;
                    if ($clnt['gender'] == 'male' && (Input::get('breast_cancer') == 1 || Input::get('brain_cancer') == 1 || Input::get('prostate_cancer') == 1) && $sc_e['eligibility'] == 1) {
                        $eligibility = 1;
                        // if ($override->getCount('visit', 'client_id', Input::get('cid')) == 1) {
                        //     $user->visit(Input::get('cid'), 0);
                        //     $user->updateRecord('study_id', array('status' => 1, 'client_id' => Input::get('cid')), $std_id['id']);
                        //     $user->updateRecord('clients', array('study_id' => $std_id['study_id'], 'enrolled' => 1), Input::get('cid'));
                        // }
                    } elseif ($clnt['gender'] == 'female' && (Input::get('breast_cancer') == 1 || Input::get('brain_cancer') == 1 || Input::get('cervical_cancer') == 1) && $sc_e['eligibility'] == 1) {
                        $eligibility = 1;
                        // if ($override->getCount('visit', 'client_id', Input::get('cid')) == 1) {
                        //     $user->visit(Input::get('cid'), 0);
                        //     $user->updateRecord('study_id', array('status' => 1, 'client_id' => Input::get('cid')), $std_id['id']);
                        //     $user->updateRecord('clients', array('study_id' => $std_id['study_id'], 'enrolled' => 1), Input::get('cid'));
                        // }
                    }
                }
                try {
                    if ($override->get('screening', 'client_id', Input::get('cid'))) {
                        $cl_id = $override->get('screening', 'client_id', Input::get('cid'))[0]['id'];
                        $client = $override->lastRow('clients', 'id')[0];

                        $user->createRecord('visit', array(
                            'visit_name' => 'Day 0',
                            'visit_code' => 'D0',
                            'visit_date' => date('Y-m-d'),
                            'visit_window' => 2,
                            'status' => 1,
                            'seq_no' => 0,
                            'client_id' => $client['id'],
                        ));
                    } else {
                        $client = $override->lastRow('clients', 'id')[0];

                        $user->createRecord('visit', array(
                            'visit_name' => 'Day 0',
                            'visit_code' => 'D0',
                            'visit_date' => date('Y-m-d'),
                            'visit_window' => 2,
                            'status' => 1,
                            'seq_no' => 0,
                            'client_id' => $client['id'],
                        ));
                    }
                    $user->updateRecord('clients', array('consented' => Input::get('consented')), Input::get('cid'));
                    $successMessage = 'Inclusion Successful Added';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_Inclusion')) {
            print_r($_POST);

            $validate = $validate->check($_POST, array());
            if ($validate->passed()) {
                $screening = 0;
                $eligibility = 0;
                $eligible = 0;
                if (
                    Input::get('age_18') == 1 && Input::get('biopsy') == 1 && Input::get('consented') == 1
                ) {
                    if (Input::get('gender') == 'male' && (Input::get('breast_cancer') == 1 || Input::get('brain_cancer') == 1 || Input::get('prostate_cancer') == 1)) {
                        $eligibility = 1;
                    } elseif (Input::get('gender') == 'female' && (Input::get('breast_cancer') == 1 || Input::get('brain_cancer') == 1 || Input::get('cervical_cancer') == 1)) {
                        $eligibility = 1;
                    }
                }
                try {
                    if ($override->get('screening', 'client_id', Input::get('id'))) {
                        $user->updateRecord('screening', array(
                            'screening_date' => Input::get('screening_date'),
                            'age_18' => Input::get('age_18'),
                            'biopsy' => Input::get('biopsy'),
                            'patient_category' => Input::get('patient_category'),
                            'breast_cancer' => Input::get('breast_cancer'),
                            'brain_cancer' => Input::get('brain_cancer'),
                            'cervical_cancer' => Input::get('cervical_cancer'),
                            'prostate_cancer' => Input::get('prostate_cancer'),
                            'eligibility' => $eligibility,
                            'consented' => Input::get('consented'),
                            'consented_nimregenin' => Input::get('consented_nimregenin'),
                            'reasons' => Input::get('reasons'),
                            'created_on' => date('Y-m-d'),
                            'staff_id' => $user->data()->id,
                            'site_id' => $user->data()->site_id,
                            'status' => 1,
                            'client_id' => Input::get('id'),
                        ), Input::get('screening_id'));
                    } else {
                        $user->createRecord('screening', array(
                            'screening_date' => Input::get('screening_date'),
                            'age_18' => Input::get('age_18'),
                            'biopsy' => Input::get('biopsy'),
                            'patient_category' => Input::get('patient_category'),
                            'study_id' => '',
                            'breast_cancer' => Input::get('breast_cancer'),
                            'brain_cancer' => Input::get('brain_cancer'),
                            'cervical_cancer' => Input::get('cervical_cancer'),
                            'prostate_cancer' => Input::get('prostate_cancer'),
                            'eligibility' => $eligibility,
                            'consented' => Input::get('consented'),
                            'consented_nimregenin' => Input::get('consented_nimregenin'),
                            'reasons' => Input::get('reasons'),
                            'created_on' => date('Y-m-d'),
                            'staff_id' => $user->data()->id,
                            'site_id' => $user->data()->site_id,
                            'status' => 1,
                            'client_id' => Input::get('id'),
                        ));
                    }
                    $eligible1 = $override->getNews('screening', 'client_id', Input::get('id'), 'status', 1)[0]['eligibility'];
                    $eligible2 = $override->getNews('lab', 'client_id', Input::get('id'), 'status', 1)[0]['eligibility'];

                    $screening1 = $override->getNews('screening', 'client_id', Input::get('id'), 'status', 1)[0]['status'];
                    $screening2 = $override->getNews('lab', 'client_id', Input::get('id'), 'status', 1)[0]['status'];

                    if ($screening1 == 1) {
                        $screening = 1;
                    }

                    if ($eligible1 == 1 && $eligible2 == 1) {
                        $eligible = 1;
                    }
                    $user->updateRecord('clients', array(
                        'consented' => Input::get('consented'),
                        'consented_nimregenin' => Input::get('consented_nimregenin'),
                        'screened' => $screening,
                        'eligible' => $eligible,
                        'eligibility1' => $eligibility,
                        'patient_category' => Input::get('patient_category'),
                    ), Input::get('id'));
                    $successMessage = 'Inclusion Successful Added';
                    if ($eligible == 1) {
                        Redirect::to('info.php?id=3&status=1');
                    } else {
                        // Redirect::to('info.php?id=3');
                        Redirect::to('info.php?id=3&status=1');
                    }
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_Exclusion')) {
            $validate = $validate->check($_POST, array());
            if ($validate->passed()) {
                $screening = 0;
                $eligibility = 0;
                $eligible = 0;

                $gender = $override->get('clients', 'id', Input::get('id'))[0]['gender'];

                if (Input::get('cdk') == 2 && Input::get('liver_disease') == 2) {
                    if ($gender == 'male') {
                        $eligibility = 1;
                    } elseif ($gender == 'female' && (Input::get('pregnant') == 2 && Input::get('breast_feeding') == 2)) {
                        $eligibility = 1;
                    }
                }
                try {
                    if ($override->get('lab', 'id', Input::get('lab_id'))) {
                        print_r('HI');
                        $user->updateRecord('lab', array(
                            'screening_date' => Input::get('screening_date'),
                            // 'study_id' => '',
                            'pregnant' => Input::get('pregnant'),
                            'breast_feeding' => Input::get('breast_feeding'),
                            'cdk' => Input::get('cdk'),
                            'liver_disease' => Input::get('liver_disease'),
                            'eligibility' => $eligibility,
                            'created_on' => date('Y-m-d'),
                            'staff_id' => $user->data()->id,
                            'site_id' => $user->data()->site_id,
                            'status' => 1,
                            'client_id' => Input::get('id'),
                        ), Input::get('lab_id'));
                    } else {
                        $user->createRecord('lab', array(
                            'screening_date' => Input::get('screening_date'),
                            'study_id' => '',
                            'pregnant' => Input::get('pregnant'),
                            'breast_feeding' => Input::get('breast_feeding'),
                            'cdk' => Input::get('cdk'),
                            'liver_disease' => Input::get('liver_disease'),
                            'eligibility' => $eligibility,
                            'created_on' => date('Y-m-d'),
                            'staff_id' => $user->data()->id,
                            'site_id' => $user->data()->site_id,
                            'status' => 1,
                            'client_id' => Input::get('id'),
                        ));
                    }
                    $eligible1 = $override->getNews('screening', 'client_id', Input::get('id'), 'status', 1)[0]['eligibility'];
                    $eligible2 = $override->getNews('lab', 'client_id', Input::get('id'), 'status', 1)[0]['eligibility'];

                    $screening1 = $override->getNews('screening', 'client_id', Input::get('id'), 'status', 1)[0]['status'];
                    $screening2 = $override->getNews('lab', 'client_id', Input::get('id'), 'status', 1)[0]['status'];

                    if ($screening2 == 1) {
                        $screening = 1;
                    }

                    if ($eligible1 == 1 && $eligible2 == 1) {
                        $eligible = 1;
                    }

                    $user->updateRecord('clients', array('eligible' => $eligible, 'screened' => $screening, 'eligibility2' => $eligibility), Input::get('id'));
                    $successMessage = 'Exclusion Successful Added';
                    if ($eligible == 1) {
                        Redirect::to('info.php?id=3&status=1&msg=' . $successMessage . '&msg1' . $errorMessage);
                    } else {
                        // Redirect::to('info.php?id=3');
                        Redirect::to('info.php?id=3&status=1&msg=' . $successMessage . '&msg1' . $errorMessage);
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
                $client_study = $override->getNews('clients', 'id', Input::get('id'), 'status', 1)[0];
                $std_id = $override->getNews('study_id', 'site_id', $user->data()->site_id, 'status', 0)[0];
                $screening_id = $override->getNews('screening', 'client_id', Input::get('id'), 'status', 1)[0];
                $lab_id = $override->getNews('lab', 'client_id', Input::get('id'), 'status', 1)[0];
                if (!$client_study['study_id']) {
                    $study_id = $std_id['study_id'];
                } else {
                    $study_id = $client_study['study_id'];
                }
                if (!$override->get('visit', 'client_id', Input::get('id'))) {
                    $user->createRecord('visit', array(
                        'visit_name' => 'Day 0',
                        'visit_code' => 'D0',
                        'study_id' => $study_id,
                        'expected_date' => '',
                        'visit_date' => Input::get('visit_date'),
                        'visit_window' => 2,
                        'status' => 1,
                        'client_id' => Input::get('id'),
                        'created_on' => date('Y-m-d'),
                        'seq_no' => 0,
                        'redcap' => 0,
                        'reasons' => Input::get('reasons'),
                        'visit_status' => 1
                    ));

                    if ($override->getCount('visit', 'client_id', Input::get('id')) == 1) {
                        try {
                            if (!$client_study['study_id']) {
                                $user->visit2(Input::get('id'), 0, $std_id['study_id']);
                                $user->updateRecord('study_id', array('status' => 1, 'client_id' => Input::get('id')), $std_id['id']);
                                $user->updateRecord('clients', array('study_id' => $std_id['study_id'], 'enrolled' => 1), Input::get('id'));
                            } else {
                                $user->visit2(Input::get('id'), 0, $client_study['study_id']);
                            }
                        } catch (Exception $e) {
                            die($e->getMessage());
                        }
                    }
                }

                $user->updateRecord(
                    'clients',
                    array(
                        'pt_type' => Input::get('pt_type'),
                        'treatment_type' => Input::get('treatment_type'),
                        'previous_date' => Input::get('previous_date'),
                        'treatment_type2' => Input::get('treatment_type2'),
                        'previous_date2' => Input::get('previous_date2'),
                        'total_cycle' => Input::get('total_cycle'),
                        'cycle_number' => Input::get('cycle_number')
                    ),
                    Input::get('id')
                );

                if (!$client_study['study_id']) {
                    $user->updateRecord('screening', array('study_id' => $std_id['study_id']), $screening_id['id']);
                    $user->updateRecord('lab', array('study_id' => $std_id['study_id']), $lab_id['id']);
                } else {
                    $user->updateRecord('screening', array('study_id' => $client_study['study_id']), $screening_id['id']);
                    $user->updateRecord('lab', array('study_id' => $client_study['study_id']), $lab_id['id']);
                }
                $user->updateRecord('clients', array('enrolled' => 1), Input::get('id'));
                $successMessage = 'Enrollment  Added Successful';
                Redirect::to('info.php?id=3&status=2');
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('edit_Enrollment')) {
            $validate = $validate->check($_POST, array(
                'visit_date' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                $client_study = $override->getNews('clients', 'id', Input::get('id'), 'status', 1)[0];
                $std_id = $override->getNews('study_id', 'site_id', $user->data()->site_id, 'status', 0)[0];
                // $enrollment_date = $override->get('clients', 'id', Input::get('id'))[0];
                $visit_date = $override->firstRow('visit', 'visit_date', 'id', 'client_id', Input::get('id'))[0];
                if (!$client_study['study_id']) {
                    $study_id = $std_id['study_id'];
                } else {
                    $study_id = $client_study['study_id'];
                }
                if ($override->get('visit', 'client_id', Input::get('id'))) {
                    if (Input::get('visit_date') != $visit_date['visit_date']) {
                        $user->deleteRecord('visit', 'client_id', Input::get('id'));
                        $user->createRecord('visit', array(
                            'visit_name' => 'Day 0',
                            'visit_code' => 'D0',
                            'study_id' => $study_id,
                            'expected_date' => '',
                            'visit_date' => Input::get('visit_date'),
                            'visit_window' => 2,
                            'status' => 1,
                            'client_id' => Input::get('id'),
                            'created_on' => date('Y-m-d'),
                            'seq_no' => 0,
                            'redcap' => 0,
                            'reasons' => Input::get('reasons'),
                            'visit_status' => 1,
                        ));

                        if ($override->getCount('visit', 'client_id', Input::get('id')) == 1) {
                            try {
                                if (!$client_study['study_id']) {
                                    $user->visit2(Input::get('id'), 0, $std_id['study_id']);
                                } else {
                                    $user->visit2(Input::get('id'), 0, $client_study['study_id']);
                                }
                            } catch (Exception $e) {
                                die($e->getMessage());
                            }
                        }
                    }
                }

                $user->updateRecord(
                    'clients',
                    array(
                        'pt_type' => Input::get('pt_type'),
                        'treatment_type' => Input::get('treatment_type'),
                        'previous_date' => Input::get('previous_date'),
                        'treatment_type2' => Input::get('treatment_type2'),
                        'previous_date2' => Input::get('previous_date2'),
                        'total_cycle' => Input::get('total_cycle'),
                        'cycle_number' => Input::get('cycle_number')
                    ),
                    Input::get('id')
                );

                $user->updateRecord('clients', array('enrolled' => 1), Input::get('id'));
                $successMessage = 'Enrollment  Updated Successful';
                Redirect::to('info.php?id=3&status=2');
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('update_crf1')) {
            $validate = $validate->check($_POST, array(
                //     'diagnosis_date' => array(
                //         'required' => true,
                //     ),
            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('crf1', array(
                        'vid' => $_GET["vid"],
                        'vcode' => $_GET["vcode"],
                        'study_id' => $_GET['sid'],
                        'diagnosis_date' => Input::get('diagnosis_date'),
                        'diabetic' => Input::get('diabetic'),
                        'diabetic_medicatn' => Input::get('diabetic_medicatn'),
                        'diabetic_medicatn_name' => Input::get('diabetic_medicatn_name'),
                        'hypertension' => Input::get('hypertension'),
                        'hypertension_medicatn' => Input::get('hypertension_medicatn'),
                        'hypertension_medicatn_name' => Input::get('hypertension_medicatn_name'),
                        'heart' => Input::get('heart'),
                        'heart_medicatn' => Input::get('heart_medicatn'),
                        'heart_medicatn_name' => Input::get('heart_medicatn_name'),
                        'asthma' => Input::get('asthma'),
                        'asthma_medicatn' => Input::get('asthma_medicatn'),
                        'asthma_medicatn_name' => Input::get('asthma_medicatn_name'),
                        'hiv_aids' => Input::get('hiv_aids'),
                        'hiv_aids_medicatn' => Input::get('hiv_aids_medicatn'),
                        'hiv_aids_medicatn_name' => Input::get('hiv_aids_medicatn_name'),
                        'other_medical' => Input::get('other_medical'),
                        'nimregenin_herbal' => Input::get('nimregenin_herbal'),
                        'other_herbal' => Input::get('other_herbal'),
                        'radiotherapy_performed' => Input::get('radiotherapy_performed'),
                        'chemotherapy_performed' => Input::get('chemotherapy_performed'),
                        'surgery_performed' => Input::get('surgery_performed'),
                        'crf1_cmpltd_date' => Input::get('crf1_cmpltd_date'),
                        'patient_id' => $_GET['cid'],
                        'staff_id' => $user->data()->id,
                        'status' => 1,
                        'created_on' => date('Y-m-d'),
                        'site_id' => $user->data()->site_id,
                    ), Input::get('id'));

                    if (Input::get('other_medical') == 1) {
                        for ($i = 0; $i < count(Input::get('other_specify')); $i++) {
                            $user->updateRecord('other_medication', array(
                                'vid' => $_GET["vid"],
                                'vcode' => $_GET["vcode"],
                                'study_id' => $_GET['sid'],
                                'other_medical' => Input::get('other_medical'),
                                'other_specify' => Input::get('other_specify')[$i],
                                'other_medical_medicatn' => Input::get('other_medical_medicatn')[$i],
                                'other_medicatn_name' => Input::get('other_medicatn_name')[$i],
                                'crf1_cmpltd_date' => Input::get('crf1_cmpltd_date'),
                                'patient_id' => $_GET['cid'],
                                'staff_id' => $user->data()->id,
                                'status' => 1,
                                'created_on' => date('Y-m-d'),
                                'site_id' => $user->data()->site_id,
                            ), Input::get('medication_id')[$i]);
                        }
                    }

                    if (Input::get('nimregenin_herbal') == 1) {
                        for ($i = 0; $i < count(Input::get('nimregenin_preparation')); $i++) {
                            $user->updateRecord('nimregenin', array(
                                'vid' => $_GET["vid"],
                                'vcode' => $_GET["vcode"],
                                'study_id' => $_GET['sid'],
                                'nimregenin_herbal' => Input::get('nimregenin_herbal'),
                                'nimregenin_preparation' => Input::get('nimregenin_preparation')[$i],
                                'nimregenin_start' => Input::get('nimregenin_start')[$i],
                                'nimregenin_ongoing' => Input::get('nimregenin_ongoing')[$i],
                                'nimregenin_end' => Input::get('nimregenin_end')[$i],
                                'nimregenin_dose' => Input::get('nimregenin_dose')[$i],
                                'nimregenin_frequency' => Input::get('nimregenin_frequency')[$i],
                                'nimregenin_remarks' => Input::get('nimregenin_remarks')[$i],
                                'crf1_cmpltd_date' => Input::get('crf1_cmpltd_date'),
                                'patient_id' => $_GET['cid'],
                                'staff_id' => $user->data()->id,
                                'status' => 1,
                                'created_on' => date('Y-m-d'),
                                'site_id' => $user->data()->site_id,
                            ), Input::get('nimregenin_id')[$i]);
                        }
                    }


                    if (Input::get('radiotherapy_performed') == 1) {
                        for ($i = 0; $i < count(Input::get('radiotherapy')); $i++) {
                            $user->updateRecord('radiotherapy', array(
                                'vid' => $_GET["vid"],
                                'vcode' => $_GET["vcode"],
                                'study_id' => $_GET['sid'],
                                'other_herbal' => Input::get('other_herbal'),
                                'radiotherapy_performed' => Input::get('radiotherapy_performed'),
                                'radiotherapy' => Input::get('radiotherapy')[$i],
                                'radiotherapy_start' => Input::get('radiotherapy_start')[$i],
                                'radiotherapy_ongoing' => Input::get('radiotherapy_ongoing')[$i],
                                'radiotherapy_end' => Input::get('radiotherapy_end')[$i],
                                'radiotherapy_dose' => Input::get('radiotherapy_dose')[$i],
                                'radiotherapy_frequecy' => Input::get('radiotherapy_frequecy')[$i],
                                'radiotherapy_remarks' => Input::get('radiotherapy_remarks')[$i],
                                'crf1_cmpltd_date' => Input::get('crf1_cmpltd_date'),
                                'patient_id' => $_GET['cid'],
                                'staff_id' => $user->data()->id,
                                'status' => 1,
                                'created_on' => date('Y-m-d'),
                                'site_id' => $user->data()->site_id,
                            ), Input::get('radiotherapy_id')[$i]);
                        }
                    }

                    if (Input::get('other_herbal') == 1) {
                        for ($i = 0; $i < count(Input::get('herbal_preparation')); $i++) {
                            $user->updateRecord('herbal_treatment', array(
                                'vid' => $_GET["vid"],
                                'vcode' => $_GET["vcode"],
                                'other_herbal' => Input::get('other_herbal'),
                                'herbal_preparation' => Input::get('herbal_preparation')[$i],
                                'herbal_start' => Input::get('herbal_start')[$i],
                                'herbal_ongoing' => Input::get('herbal_ongoing')[$i],
                                'herbal_end' => Input::get('herbal_end')[$i],
                                'herbal_dose' => Input::get('herbal_dose')[$i],
                                'herbal_frequency' => Input::get('herbal_frequency')[$i],
                                'herbal_remarks' => Input::get('herbal_remarks')[$i],
                                'crf1_cmpltd_date' => Input::get('crf1_cmpltd_date'),
                                'patient_id' => $_GET['cid'],
                                'staff_id' => $user->data()->id,
                                'status' => 1,
                                'created_on' => date('Y-m-d'),
                                'site_id' => $user->data()->site_id,
                            ), Input::get('herbal_id')[$i]);
                        }
                    }


                    if (Input::get('chemotherapy_performed') == 1) {
                        for ($i = 0; $i < count(Input::get('chemotherapy')); $i++) {
                            $user->updateRecord('chemotherapy', array(
                                'vid' => $_GET["vid"],
                                'vcode' => $_GET["vcode"],
                                'other_herbal' => Input::get('other_herbal'),
                                'chemotherapy_performed' => Input::get('chemotherapy_performed'),
                                'chemotherapy' => Input::get('chemotherapy')[$i],
                                'chemotherapy_start' => Input::get('chemotherapy_start')[$i],
                                'chemotherapy_ongoing' => Input::get('chemotherapy_ongoing')[$i],
                                'chemotherapy_end' => Input::get('chemotherapy_end')[$i],
                                'chemotherapy_dose' => Input::get('chemotherapy_dose')[$i],
                                'chemotherapy_frequecy' => Input::get('chemotherapy_frequecy')[$i],
                                'chemotherapy_remarks' => Input::get('chemotherapy_remarks')[$i],
                                'crf1_cmpltd_date' => Input::get('crf1_cmpltd_date'),
                                'patient_id' => $_GET['cid'],
                                'staff_id' => $user->data()->id,
                                'status' => 1,
                                'created_on' => date('Y-m-d'),
                                'site_id' => $user->data()->site_id,
                            ), Input::get('chemotherapy_id')[$i]);
                        }
                    }

                    if (Input::get('surgery_performed') == 1) {
                        if (count(Input::get('surgery_id')) == count(Input::get('surgery'))) {
                            for ($i = 0; $i < count(Input::get('surgery')); $i++) {
                                $user->updateRecord('surgery', array(
                                    'vid' => $_GET["vid"],
                                    'vcode' => $_GET["vcode"],
                                    'study_id' => $_GET['sid'],
                                    'other_herbal' => Input::get('other_herbal'),
                                    'surgery_performed' => Input::get('surgery_performed'),
                                    'surgery' => Input::get('surgery')[$i],
                                    'surgery_start' => Input::get('surgery_start')[$i],
                                    'surgery_number' => Input::get('surgery_number')[$i],
                                    'surgery_remarks' => Input::get('surgery_remarks')[$i],
                                    'crf1_cmpltd_date' => Input::get('crf1_cmpltd_date'),
                                    'patient_id' => $_GET['cid'],
                                    'staff_id' => $user->data()->id,
                                    'status' => 1,
                                    'created_on' => date('Y-m-d'),
                                    'site_id' => $user->data()->site_id,
                                ), Input::get('surgery_id')[$i]);
                            }
                        }
                    }
                    // else{
                    //     for ($i = count(Input::get('surgery')) + 1; $i < count(Input::get('surgery_id')); $i++) {
                    //         $user->createRecord('surgery', array(
                    //             'vid' => $_GET["vid"],
                    //             'vcode' => $_GET["vcode"],
                    //             'study_id' => $_GET['sid'],
                    //             'other_herbal' => Input::get('other_herbal'),
                    //             'surgery_performed' => Input::get('surgery_performed'),
                    //             'surgery' => Input::get('surgery')[$i],
                    //             'surgery_start' => Input::get('surgery_start')[$i],
                    //             'surgery_number' => Input::get('surgery_number')[$i],
                    //             'surgery_remarks' => Input::get('surgery_remarks')[$i],
                    //             'crf1_cmpltd_date' => Input::get('crf1_cmpltd_date'),
                    //             'patient_id' => $_GET['cid'],
                    //             'staff_id' => $user->data()->id,
                    //             'status' => 1,
                    //             'created_on' => date('Y-m-d'),
                    //             'site_id' => $user->data()->site_id,
                    //         ));
                    //     }
                    // }


                    $user->updateRecord('clients', array(
                        'nimregenin' => Input::get('nimregenin_herbal'),
                    ), $_GET['cid']);

                    $successMessage = 'CRF1 Updated Successful';
                    Redirect::to('info.php?id=6&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&sid=' . $_GET['sid']);
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('update_crf2')) {
            $validate = $validate->check($_POST, array(
                'crf2_date' => array(
                    'required' => true,
                ),

            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('crf2', array(
                        'study_id' => $_GET['sid'],
                        'vid' => $_GET['vid'],
                        'vcode' => $_GET['vcode'],
                        'crf2_date' => Input::get('crf2_date'),
                        'height' => Input::get('height'),
                        'weight' => Input::get('weight'),
                        'bmi' => Input::get('bmi'),
                        'time' => Input::get('time'),
                        'temperature' => Input::get('temperature'),
                        'method' => Input::get('method'),
                        'respiratory_rate' => Input::get('respiratory_rate'),
                        'heart_rate' => Input::get('heart_rate'),
                        'systolic' => Input::get('systolic'),
                        'diastolic' => Input::get('diastolic'),
                        'time2' => Input::get('time2'),
                        'appearance' => Input::get('appearance'),
                        'appearance_comments' => Input::get('appearance_comments'),
                        'appearance_signifcnt' => Input::get('appearance_signifcnt'),
                        'heent' => Input::get('heent'),
                        'heent_comments' => Input::get('heent_comments'),
                        'heent_signifcnt' => Input::get('heent_signifcnt'),
                        'respiratory' => Input::get('respiratory'),
                        'respiratory_comments' => Input::get('respiratory_comments'),
                        'respiratory_signifcnt' => Input::get('respiratory_signifcnt'),
                        'cardiovascular' => Input::get('cardiovascular'),
                        'cardiovascular_comments' => Input::get('cardiovascular_comments'),
                        'cardiovascular_signifcnt' => Input::get('cardiovascular_signifcnt'),
                        'abdnominal' => Input::get('abdnominal'),
                        'abdnominal_comments' => Input::get('abdnominal_comments'),
                        'abdnominal_signifcnt' => Input::get('abdnominal_signifcnt'),
                        'urogenital' => Input::get('urogenital'),
                        'urogenital_comments' => Input::get('urogenital_comments'),
                        'urogenital_signifcnt' => Input::get('urogenital_signifcnt'),
                        'musculoskeletal' => Input::get('musculoskeletal'),
                        'musculoskeletal_comments' => Input::get('musculoskeletal_comments'),
                        'musculoskeletal_signifcnt' => Input::get('musculoskeletal_signifcnt'),
                        'neurological' => Input::get('neurological'),
                        'neurological_comments' => Input::get('neurological_comments'),
                        'neurological_signifcnt' => Input::get('neurological_signifcnt'),
                        'psychological' => Input::get('psychological'),
                        'psychological_comments' => Input::get('psychological_comments'),
                        'psychological_signifcnt' => Input::get('psychological_signifcnt'),
                        'endocrime' => Input::get('endocrime'),
                        'endocrime_comments' => Input::get('endocrime_comments'),
                        'endocrime_signifcnt' => Input::get('endocrime_signifcnt'),
                        'lymphatic' => Input::get('lymphatic'),
                        'lymphatic_comments' => Input::get('lymphatic_comments'),
                        'lymphatic_signifcnt' => Input::get('lymphatic_signifcnt'),
                        'skin' => Input::get('skin'),
                        'skin_comments' => Input::get('skin_comments'),
                        'skin_signifcnt' => Input::get('skin_signifcnt'),
                        'local_examination' => Input::get('local_examination'),
                        'local_examination_comments' => Input::get('local_examination_comments'),
                        'local_examination_signifcnt' => Input::get('local_examination_signifcnt'),
                        'physical_exams_other' => Input::get('physical_exams_other'),
                        'physical_other_specify' => Input::get('physical_other_specify'),
                        'physical_other_system' => Input::get('physical_other_system'),
                        'physical_other_comments' => Input::get('physical_other_comments'),
                        'physical_other_signifcnt' => Input::get('physical_other_signifcnt'),
                        'additional_notes' => Input::get('additional_notes'),
                        'physical_performed' => Input::get('physical_performed'),
                        'crf2_cmpltd_date' => Input::get('crf2_cmpltd_date'),
                        'patient_id' => $_GET['cid'],
                        'staff_id' => $user->data()->id,
                        'status' => 1,
                        'created_on' => date('Y-m-d'),
                        'site_id' => $user->data()->site_id,
                    ), Input::get('id'));
                    $successMessage = 'CRF2 Updated Successful';
                    Redirect::to('info.php?id=6&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&sid=' . $_GET['sid']);
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('update_crf3')) {
            $validate = $validate->check($_POST, array(
                'crf3_date' => array(
                    'required' => true,
                ),

            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('crf3', array(
                        'study_id' => $_GET['sid'],
                        'vid' => $_GET["vid"],
                        'vcode' => $_GET["vcode"],
                        'crf3_date' => Input::get('crf3_date'),
                        'fever' => Input::get('fever'),
                        'vomiting' => Input::get('vomiting'),
                        'diarrhoea' => Input::get('diarrhoea'),
                        'headaches' => Input::get('headaches'),
                        'loss_appetite' => Input::get('loss_appetite'),
                        'nausea' => Input::get('nausea'),
                        'difficult_breathing' => Input::get('difficult_breathing'),
                        'sore_throat' => Input::get('sore_throat'),
                        'fatigue' => Input::get('fatigue'),
                        'muscle_pain' => Input::get('muscle_pain'),
                        'loss_consciousness' => Input::get('loss_consciousness'),
                        'backpain' => Input::get('backpain'),
                        'weight_loss' => Input::get('weight_loss'),
                        'heartburn_indigestion' => Input::get('heartburn_indigestion'),
                        'swelling' => Input::get('swelling'),
                        'pv_bleeding' => Input::get('pv_bleeding'),
                        'pv_discharge' => Input::get('pv_discharge'),
                        'micitrition' => Input::get('micitrition'),
                        'convulsions' => Input::get('convulsions'),
                        'blood_urine' => Input::get('blood_urine'),
                        'symptoms_other' => Input::get('symptoms_other'),
                        'symptoms_other_specify' => Input::get('symptoms_other_specify'),
                        'other_comments' => Input::get('other_comments'),
                        'adherence' => Input::get('adherence'),
                        'adherence_specify' => Input::get('adherence_specify'),
                        'herbal_medication' => Input::get('herbal_medication'),
                        'herbal_ingredients' => Input::get('herbal_ingredients'),
                        'crf3_cmpltd_date' => Input::get('crf3_cmpltd_date'),
                        'patient_id' => $_GET['cid'],
                        'staff_id' => $user->data()->id,
                        'status' => 1,
                        'created_on' => date('Y-m-d'),
                        'site_id' => $user->data()->site_id,
                    ), Input::get('id'));
                    $successMessage = 'CRF3 updated Successful';
                    Redirect::to('info.php?id=6&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&sid=' . $_GET['sid']);
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('update_crf4')) {
            $validate = $validate->check($_POST, array(
                'sample_date' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('crf4', array(
                        'study_id' => $_GET['sid'],
                        'vid' => $_GET["vid"],
                        'vcode' => $_GET["vcode"],
                        'sample_date' => Input::get('sample_date'),
                        'renal_urea' => Input::get('renal_urea'),
                        'renal_urea_units' => Input::get('renal_urea_units'),
                        'renal_creatinine' => Input::get('renal_creatinine'),
                        'renal_creatinine_units' => Input::get('renal_creatinine_units'),
                        'renal_creatinine_grade' => Input::get('renal_creatinine_grade'),
                        'renal_egfr' => Input::get('renal_egfr'),
                        'renal_egfr_units' => Input::get('renal_egfr_units'),
                        'renal_egfr_grade' => Input::get('renal_egfr_grade'),
                        'liver_ast' => Input::get('liver_ast'),
                        'liver_ast_grade' => Input::get('liver_ast_grade'),
                        'liver_alt' => Input::get('liver_alt'),
                        'liver_alt_grade' => Input::get('liver_alt_grade'),
                        'liver_alp' => Input::get('liver_alp'),
                        'liver_alp_grade' => Input::get('liver_alp_grade'),
                        'liver_pt' => Input::get('liver_pt'),
                        'liver_pt_grade' => Input::get('liver_pt_grade'),
                        'liver_ptt' => Input::get('liver_ptt'),
                        'liver_ptt_grade' => Input::get('liver_ptt_grade'),
                        'liver_inr' => Input::get('liver_inr'),
                        'liver_inr_grade' => Input::get('liver_inr_grade'),
                        'liver_ggt' => Input::get('liver_ggt'),
                        'liver_albumin' => Input::get('liver_albumin'),
                        'liver_albumin_grade' => Input::get('liver_albumin_grade'),
                        'liver_bilirubin_total' => Input::get('liver_bilirubin_total'),
                        'liver_bilirubin_total_units' => Input::get('liver_bilirubin_total_units'),
                        'bilirubin_total_grade' => Input::get('bilirubin_total_grade'),
                        'liver_bilirubin_direct' => Input::get('liver_bilirubin_direct'),
                        'liver_bilirubin_direct_units' => Input::get('liver_bilirubin_direct_units'),
                        'bilirubin_direct_grade' => Input::get('bilirubin_direct_grade'),
                        'rbg' => Input::get('rbg'),
                        'rbg_units' => Input::get('rbg_units'),
                        'rbg_grade' => Input::get('rbg_grade'),
                        'ldh' => Input::get('ldh'),
                        'crp' => Input::get('crp'),
                        'd_dimer' => Input::get('d_dimer'),
                        'ferritin' => Input::get('ferritin'),
                        'wbc' => Input::get('wbc'),
                        'wbc_grade' => Input::get('wbc_grade'),
                        'abs_neutrophil' => Input::get('abs_neutrophil'),
                        'abs_neutrophil_grade' => Input::get('abs_neutrophil_grade'),
                        'abs_lymphocytes' => Input::get('abs_lymphocytes'),
                        'abs_lymphocytes_grade' => Input::get('abs_lymphocytes_grade'),
                        'abs_eosinophils' => Input::get('abs_eosinophils'),
                        'abs_monocytes' => Input::get('abs_monocytes'),
                        'abs_basophils' => Input::get('abs_basophils'),
                        'hb' => Input::get('hb'),
                        'hb_grade' => Input::get('hb_grade'),
                        'mcv' => Input::get('mcv'),
                        'mch' => Input::get('mch'),
                        'hct' => Input::get('hct'),
                        'rbc' => Input::get('rbc'),
                        'plt' => Input::get('plt'),
                        'plt_grade' => Input::get('plt_grade'),
                        'cancer' => Input::get('cancer'),
                        'prostate' => Input::get('prostate'),
                        'chest_xray' => Input::get('chest_xray'),
                        'chest_specify' => Input::get('chest_specify'),
                        'ct_chest' => Input::get('ct_chest'),
                        'ct_chest_specify' => Input::get('ct_chest_specify'),
                        'ultrasound' => Input::get('ultrasound'),
                        'ultrasound_specify' => Input::get('ultrasound_specify'),
                        'crf4_cmpltd_date' => Input::get('crf4_cmpltd_date'),
                        'patient_id' => $_GET['cid'],
                        'staff_id' => $user->data()->id,
                        'status' => 1,
                        'created_on' => date('Y-m-d'),
                        'site_id' => $user->data()->site_id,
                    ), Input::get('id'));
                    $successMessage = 'CRF4 updated Successful';
                    Redirect::to('info.php?id=6&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&sid=' . $_GET['sid']);
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('update_crf5')) {
            $validate = $validate->check($_POST, array(
                'date_reported' => array(
                    'required' => true,
                ),

            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('crf5', array(
                        'study_id' => $_GET['sid'],
                        'vid' => $_GET["vid"],
                        'vcode' => $_GET["vcode"],
                        'date_reported' => Input::get('date_reported'),
                        'ae_description' => Input::get('ae_description'),
                        'ae_category' => Input::get('ae_category'),
                        'ae_start_date' => Input::get('ae_start_date'),
                        'ae_ongoing' => Input::get('ae_ongoing'),
                        'ae_end_date' => Input::get('ae_end_date'),
                        'ae_outcome' => Input::get('ae_outcome'),
                        'ae_severity' => Input::get('ae_severity'),
                        'ae_serious' => Input::get('ae_serious'),
                        'ae_expected' => Input::get('ae_expected'),
                        'ae_treatment' => Input::get('ae_treatment'),
                        'ae_taken' => Input::get('ae_taken'),
                        'ae_relationship' => Input::get('ae_relationship'),
                        'ae_staff_initial' => Input::get('ae_staff_initial'),
                        'ae_date' => Input::get('ae_date'),
                        'crf5_cmpltd_date' => Input::get('crf5_cmpltd_date'),
                        'patient_id' => $_GET['cid'],
                        'staff_id' => $user->data()->id,
                        'status' => 1,
                        'created_on' => date('Y-m-d'),
                        'site_id' => $user->data()->site_id,
                    ), Input::get('id'));
                    $successMessage = 'CRF5 updated Successful';
                    Redirect::to('info.php?id=6&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&sid=' . $_GET['sid']);
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('update_crf6')) {
            $validate = $validate->check($_POST, array(
                'today_date' => array(
                    'required' => true,
                ),

            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('crf6', array(
                        'study_id' => $_GET['sid'],
                        'vid' => $_GET["vid"],
                        'vcode' => $_GET["vcode"],
                        'today_date' => Input::get('today_date'),
                        'terminate_date' => Input::get('terminate_date'),
                        'completed120days' => Input::get('completed120days'),
                        'reported_dead' => Input::get('reported_dead'),
                        'withdrew_consent' => Input::get('withdrew_consent'),
                        'start_date' => Input::get('start_date'),
                        'end_date' => Input::get('end_date'),
                        'date_death' => Input::get('date_death'),
                        'primary_cause' => Input::get('primary_cause'),
                        'secondary_cause' => Input::get('secondary_cause'),
                        'withdrew_reason' => Input::get('withdrew_reason'),
                        'withdrew_other' => Input::get('withdrew_other'),
                        'terminated_reason' => Input::get('terminated_reason'),
                        'outcome' => Input::get('outcome'),
                        'outcome_date' => Input::get('outcome_date'),
                        'summary' => Input::get('summary'),
                        'clinician_name' => Input::get('clinician_name'),
                        'crf6_cmpltd_date' => Input::get('crf6_cmpltd_date'),
                        'patient_id' => $_GET['cid'],
                        'staff_id' => $user->data()->id,
                        'status' => 1,
                        'created_on' => date('Y-m-d'),
                        'site_id' => $user->data()->site_id,
                    ), Input::get('id'));
                    $successMessage = 'CRF6 updated Successful';
                    Redirect::to('info.php?id=6&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&sid=' . $_GET['sid']);
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('update_crf7')) {
            $validate = $validate->check($_POST, array(
                'tdate' => array(
                    'required' => true,
                ),

            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('crf7', array(
                        'study_id' => $_GET['sid'],
                        'vid' => $_GET['vid'],
                        'vcode' => $_GET['vcode'],
                        'tdate' => Input::get('tdate'),
                        'mobility' => Input::get('mobility'),
                        'self_care' => Input::get('self_care'),
                        'usual_active' => Input::get('usual_active'),
                        'pain' => Input::get('pain'),
                        'anxiety' => Input::get('anxiety'),
                        'FDATE' => Input::get('FDATE'),
                        'cpersid' => Input::get('cpersid'),
                        'cDATE' => Input::get('cDATE'),
                        'patient_id' => $_GET['cid'],
                        'staff_id' => $user->data()->id,
                        'status' => 1,
                        'created_on' => date('Y-m-d'),
                        'site_id' => $user->data()->site_id,
                    ), Input::get('id'));
                    $successMessage = 'CRF7 Updated Successful';
                    Redirect::to('info.php?id=6&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&sid=' . $_GET['sid']);
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
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
                        if (Input::get('name') == 'user' || Input::get('name') == 'schedule' || Input::get('name') == 'study_id') {
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
        }

        if ($_GET['id'] == 20) {
            $data = null;
            $filename = null;
            if (Input::get('clients')) {
                $data = $override->get('clients', 'status', 1);
                $filename = 'Clients';
            } elseif (Input::get('visits')) {
                $data = $override->get('visit', 'status', 1);
                $filename = 'Visits';
            } elseif (Input::get('lab')) {
                $data = $override->getData('lab');
                $filename = 'Exclusion Criteria';
            } elseif (Input::get('study_id')) {
                $data = $override->getData('study_id');
                $filename = 'Study IDs';
            } elseif (Input::get('sites')) {
                $data = $override->getData('site');
                $filename = 'Sites';
            } elseif (Input::get('screening')) {
                $data = $override->getData('screening');
                $filename = 'Inclusion criteria';
            } elseif (Input::get('crf1')) {
                $data = $override->getData('crf1');
                $filename = 'CRF 1';
            } elseif (Input::get('crf2')) {
                $data = $override->getData('crf2');
                $filename = 'CRF 2';
            } elseif (Input::get('crf3')) {
                $data = $override->getData('crf3');
                $filename = 'CRF 3';
            } elseif (Input::get('crf4')) {
                $data = $override->getData('crf4');
                $filename = 'CRF 4';
            } elseif (Input::get('crf5')) {
                $data = $override->getData('crf5');
                $filename = 'CRF 5';
            } elseif (Input::get('crf5')) {
                $data = $override->getData('crf5');
                $filename = 'CRF 5';
            } elseif (Input::get('crf6')) {
                $data = $override->getData('crf6');
                $filename = 'CRF 6';
            } elseif (Input::get('crf7')) {
                $data = $override->getData('crf7');
                $filename = 'CRF 7';
            } elseif (Input::get('herbal')) {
                $data = $override->getData('herbal_treatment');
                $filename = 'Other Herbal Treatment';
            } elseif (Input::get('medication')) {
                $data = $override->getData('other_medication');
                $filename = 'other_medication';
            } elseif (Input::get('nimregenin')) {
                $data = $override->getData('nimregenin');
                $filename = 'nimregenin';
            } elseif (Input::get('radiotherapy')) {
                $data = $override->getData('radiotherapy');
                $filename = 'radiotherapy';
            } elseif (Input::get('chemotherapy')) {
                $data = $override->getData('chemotherapy');
                $filename = 'chemotherapy';
            } elseif (Input::get('surgery')) {
                $data = $override->getData('surgery');
                $filename = 'surgery';
            }
            $user->exportData($data, $filename);
        }


        if ($_GET['id'] == 21) {
            $data = null;
            $filename = null;
            if (Input::get('visits_pending')) {
                if ($_GET['day']) {
                    if ($_GET['day'] == 'Nxt') {
                        $schedule = 0;
                        $today = date('Y-m-d');
                        $nxt_visit_date = date('Y-m-d', strtotime($today . ' + ' . $schedule . ' days'));
                        $data = $override->getNews3('visit', 'expected_date', $nxt_visit_date, 'status', 0);
                    } else {
                        $data = $override->getNews2('visit', 'expected_date', date('Y-m-d'), 'status', 0, 'visit_code', $_GET['day']);
                    }
                } else {
                    $data = $override->getNews1('visit', 'expected_date', date('Y-m-d'), 'status', 0);
                }
                $filename = 'Pending Visits';
            }
            $user->exportData($data, $filename);
        }

        if ($_GET['id'] == 22) {
            $data = null;
            $filename = null;
            if (Input::get('crfs_pending')) {
                // if ($_GET['day']) {
                //     if ($_GET['day'] == 'Nxt') {
                //         $schedule = 0;
                //         $today = date('Y-m-d');
                //         $nxt_visit_date = date('Y-m-d', strtotime($today . ' + ' . $schedule . ' days'));
                //         $data = $override->getNews3('visit', 'expected_date', $nxt_visit_date, 'status', 0);
                //     } else {
                //         $data = $override->getNews2('visit', 'expected_date', date('Y-m-d'), 'status', 0, 'visit_code', $_GET['day']);
                //     }
                // } else {
                $data = $override->getNews('crf1', 'vcode', 'D0', 'status', 1);
                // $data = $override->getNews1('crf1', 'visit_code', $_GET['day'], 'status', 1);
                // $data = $override->getNews1('visit', 'expected_date', date('Y-m-d'), 'status', 0);
                // }
                $filename = 'Missing Crfs';
            }
            $user->exportData($data, $filename);
        }

        if ($_GET['id'] == 24) {
            if (Input::get('export_data_table')) {
                // $data = $override->DBbackups(Input::get('name'), 'Backup');
                $data = $override->DBbackups();
                $output = '';
                foreach ($data as $table) {
                    $data = $user->createTable($table);
                    // foreach ($data as $show_table_row) {
                    //     $output .= "\n\n" . $show_table_row["Create Table"] . ";\n\n";
                    // }
                    //     $select_query = "SELECT * FROM " . $table . "";
                    //     $statement = $connect->prepare($select_query);
                    //     $statement->execute();
                    //     $total_row = $statement->rowCount();

                    //     for ($count = 0; $count < $total_row; $count++) {
                    //         $single_result = $statement->fetch(PDO::FETCH_ASSOC);
                    //         $table_column_array = array_keys($single_result);
                    //         $table_value_array = array_values($single_result);
                    //         $output .= "\nINSERT INTO $table (";
                    //         $output .= "" . implode(", ", $table_column_array) . ") VALUES (";
                    //         $output .= "'" . implode("','", $table_value_array) . "');\n";
                    //     }

                    print_r($data);
                }
                // $file_name = 'database_backup_on_' . date('y-m-d') . '.sql';
                // $file_handle = fopen($file_name, 'w+');
                // fwrite($file_handle, $output);
                // fclose($file_handle);
                // header('Content-Description: File Transfer');
                // header('Content-Type: application/octet-stream');
                // header('Content-Disposition: attachment; filename=' . basename($file_name));
                // header('Content-Transfer-Encoding: binary');
                // header('Expires: 0');
                // header('Cache-Control: must-revalidate');
                // header('Pragma: public');
                // header('Content-Length: ' . filesize($file_name));
                // ob_clean();
                // flush();
                // readfile($file_name);
                // unlink($file_name);


            }
        }

        if ($_GET['id'] == 25) {
            if (Input::get('export_data_table')) {
                $data = $override->Export_Database(Input::get('name'), 'Backup');
                if ($data === false) {
                    $successMessage = 'Failed to export database.';
                } else {
                    $successMessage = 'Database exported successfully.';
                    // print_r($data);
                }
            }
        }

        if ($_GET['id'] == 26) {
            $data = null;
            $filename = null;
            if (Input::get('dowmload_missing_datae')) {
                if ($data === false) {
                    $successMessage = 'Failed to export database.';
                    $filename = 'Mssing Crfs';
                } else {
                    $filename = 'Mssing Crfs';
                    $successMessage = 'Database exported successfully.';
                }
                $data = $override->MissingData();
                // $data = $override->exportData($MissingCrf, $filename);
                $user->exportData($data, $filename);
            }
        }

        if ($_GET['id'] == 27) {

            $data = null;
            $filename = null;
            if (Input::get('dowmload_missing_crfs_visits')) {
                if ($data === false) {
                    $successMessage = 'Failed to export database.';
                    $filename = 'Mssing Crfs';
                } else {
                    $filename = 'Mssing Crfs';
                    $successMessage = 'Database exported successfully.';
                }
                $data = $override->MissingData1();
                // $data = $override->exportData($MissingCrf, $filename);
                $user->exportData($data, $filename);
            }
        }

        if ($_GET['id'] == 28) {
            $data = null;
            $filename = null;
            if (Input::get('dowmload_missing_crfs_all')) {
                if ($data === false) {
                    $successMessage = 'Failed to export database.';
                    $filename = 'Mssing Crfs';
                } else {
                    $filename = 'Mssing Crfs';
                    $successMessage = 'Database exported successfully.';
                }
                $data = $override->MissingData2();
                // $data = $override->exportData($MissingCrf, $filename);
                $user->exportData($data, $filename);
            }
        }

        if ($_GET['id'] == 30) {

            $data = null;
            $filename = null;
            if (Input::get('dowmload_all_crfs_visits_with')) {
                if ($data === false) {
                    $successMessage = 'Failed to export database.';
                    $filename = 'All Clints Visits with Crfs';
                } else {
                    $filename = 'All Clints Visits with Crfs';
                    $successMessage = 'Database exported successfully.';
                }
                $data = $override->getDataStatus();
                // $data = $override->exportData($MissingCrf, $filename);
                $user->exportData($data, $filename);
                // $user->exportDataCsv($data, $filename);
            }
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

        <?php if ($errorMessage || $_GET['msg1']) { ?>
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-ban"></i> Error!</h4>
                <?= $errorMessage || $_GET['msg1'] ?>
            </div>
        <?php } elseif ($pageError) { ?>
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-ban"></i> Error!</h4>
                <?php foreach ($pageError as $error) {
                    echo $error . ' , ';
                } ?>
            </div>
        <?php } elseif ($_GET['msg']) { ?>
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-check"></i> Success!</h4>
                <?= $_GET['msg'] ?>
            </div>
        <?php } ?>


        <?php if ($_GET['id'] == 1 && ($user->data()->accessLevel == 1 || $user->data()->accessLevel == 2)) { ?>
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
                                        $pagNum = $override->countData2Active('user', 'status', 1, 'power', 0, 'count', 4);
                                    } elseif ($_GET['status'] == 2) {
                                        $pagNum = $override->countData2Active('user', 'status', 0, 'power', 0, 'count', 4);
                                    } elseif ($_GET['status'] == 3) {
                                        $pagNum = $override->countData2Locked('user', 'status', 1, 'power', 0, 'count', 4);
                                    } elseif ($_GET['status'] == 4) {
                                        $pagNum = $override->countData2Locked('user', 'status', 0, 'power', 0, 'count', 4);
                                    }

                                    $pages = ceil($pagNum / $numRec);
                                    if (!$_GET['page'] || $_GET['page'] == 1) {
                                        $page = 0;
                                    } else {
                                        $page = ($_GET['page'] * $numRec) - $numRec;
                                    }

                                    if ($_GET['status'] == 1) {
                                        $data = $override->getWithLimit3Active('user', 'status', 1, 'power', 0, 'count', 4, $page, $numRec);
                                    } elseif ($_GET['status'] == 2) {
                                        $data = $override->getWithLimit3Active('user', 'status', 0, 'power', 0, 'count', 4, $page, $numRec);
                                    } elseif ($_GET['status'] == 3) {
                                        $data = $override->getWithLimit3Locked('user', 'status', 1, 'power', 0, 'count', 4, $page, $numRec);
                                    } elseif ($_GET['status'] == 4) {
                                        $data = $override->getWithLimit3Locked('user', 'status', 0, 'power', 0, 'count', 4, $page, $numRec);
                                    }
                                    ?>
                                    List of Staff
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
                                        <table id="search-results" class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
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
                                                    $position = $override->getNews('position', 'status', 1, 'id', $staff['position'])[0];
                                                    $sites = $override->getNews('site', 'status', 1, 'id', $staff['site_id'])[0];

                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <?= $x; ?>
                                                        </td>
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
                                                        <td class="text-center">
                                                            <a href="add.php?id=1&staff_id=<?= $staff['id'] ?>"
                                                                class="btn btn-info">Update</a>
                                                            <a href="#reset<?= $staff['id'] ?>" role="button"
                                                                class="btn btn-default" data-toggle="modal">Reset</a>
                                                            <a href="#lock<?= $staff['id'] ?>" role="button"
                                                                class="btn btn-warning" data-toggle="modal">Lock</a>
                                                            <a href="#unlock<?= $staff['id'] ?>" role="button"
                                                                class="btn btn-primary" data-toggle="modal">Unlock</a>
                                                            <a href="#delete<?= $staff['id'] ?>" role="button"
                                                                class="btn btn-danger" data-toggle="modal">Delete</a>
                                                            <a href="#restore<?= $staff['id'] ?>" role="button"
                                                                class="btn btn-secondary" data-toggle="modal">Restore</a>
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

                                                    <?php $x++;
                                                } ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Name</th>
                                                    <th>username</th>
                                                    <th>Position</th>
                                                    <th>Sex</th>
                                                    <th>Site</th>
                                                    <th>Status</th>
                                                    <th class="text-center">Action</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <!-- /.card-body -->
                                    <div class="card-footer clearfix">
                                        <ul class="pagination pagination-sm m-0 float-right">
                                            <li class="page-item">
                                                <a class="page-link" href="info.php?id=1&status=<?= $_GET['status'] ?>site_id=<?= $_GET['site_id'] ?>&page=<?php if (($_GET['page'] - 1) > 0) {
                                                        echo $_GET['page'] - 1;
                                                    } else {
                                                        echo 1;
                                                    } ?>">&laquo;
                                                </a>
                                            </li>
                                            <?php for ($i = 1; $i <= $pages; $i++) { ?>
                                                <li class="page-item">
                                                    <a class="page-link <?php if ($i == $_GET['page']) {
                                                        echo 'active';
                                                    } ?>"
                                                        href="info.php?id=1&status=<?= $_GET['status'] ?>&site_id=<?= $_GET['site_id'] ?>&page=<?= $i ?>"><?= $i ?>
                                                    </a>
                                                </li>
                                            <?php } ?>
                                            <li class="page-item">
                                                <a class="page-link" href="info.php?id=1&status=<?= $_GET['status'] ?>&site_id=<?= $_GET['site_id'] ?>&page=<?php if (($_GET['page'] + 1) <= $pages) {
                                                        echo $_GET['page'] + 1;
                                                    } else {
                                                        echo $i - 1;
                                                    } ?>">&raquo;
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
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
        <?php } elseif ($_GET['id'] == 2 && ($user->data()->accessLevel == 1 || $user->data()->accessLevel == 2)) { ?>
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
                                    $pagNum = $override->getCount('position', 'status', 1);

                                    $pages = ceil($pagNum / $numRec);
                                    if (!$_GET['page'] || $_GET['page'] == 1) {
                                        $page = 0;
                                    } else {
                                        $page = ($_GET['page'] * $numRec) - $numRec;
                                    }

                                    $data = $override->getWithLimit('position', 'status', 1, $page, $numRec);

                                    ?>
                                    List of Posiitions
                                </h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="index1.php">Home</a></li>
                                    <li class="breadcrumb-item active">List of Posiitions</li>
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
                                                        List of Posiitions
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
                                        <table id="search-results" class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Name</th>
                                                    <th>Access Level</th>
                                                    <!-- <th>Status</th> -->
                                                    <th class="text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $x = 1;
                                                foreach ($data as $value) {
                                                    $position = $override->getNews('position', 'status', 1, 'id', $staff['position'])[0];
                                                    $access_level = $override->getNews('site', 'status', 1, 'id', $staff['site_id'])[0];

                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <?= $x; ?>
                                                        </td>
                                                        <td class="table-user">
                                                            <?= $value['name']; ?>
                                                        </td>
                                                        <td class="table-user">
                                                            <?= $value['access_level']; ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <a href="add.php?id=2&position_id=<?= $value['id'] ?>"
                                                                class="btn btn-info">Update</a>
                                                            <a href="#delete<?= $staff['id'] ?>" role="button"
                                                                class="btn btn-danger" data-toggle="modal">Delete</a>
                                                        </td>
                                                    </tr>
                                                    <?php $x++;
                                                } ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Name</th>
                                                    <th>Access Level</th>
                                                    <!-- <th>Status</th> -->
                                                    <th class="text-center">Action</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <!-- /.card-body -->
                                    <div class="card-footer clearfix">
                                        <ul class="pagination pagination-sm m-0 float-right">
                                            <li class="page-item">
                                                <a class="page-link" href="info.php?id=2&status=<?= $_GET['status'] ?>site_id=<?= $_GET['site_id'] ?>&page=<?php if (($_GET['page'] - 1) > 0) {
                                                        echo $_GET['page'] - 1;
                                                    } else {
                                                        echo 1;
                                                    } ?>">&laquo;
                                                </a>
                                            </li>
                                            <?php for ($i = 1; $i <= $pages; $i++) { ?>
                                                <li class="page-item">
                                                    <a class="page-link <?php if ($i == $_GET['page']) {
                                                        echo 'active';
                                                    } ?>"
                                                        href="info.php?id=2&status=<?= $_GET['status'] ?>&site_id=<?= $_GET['site_id'] ?>&page=<?= $i ?>"><?= $i ?>
                                                    </a>
                                                </li>
                                            <?php } ?>
                                            <li class="page-item">
                                                <a class="page-link" href="info.php?id=2&status=<?= $_GET['status'] ?>&site_id=<?= $_GET['site_id'] ?>&page=<?php if (($_GET['page'] + 1) <= $pages) {
                                                        echo $_GET['page'] + 1;
                                                    } else {
                                                        echo $i - 1;
                                                    } ?>">&raquo;
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
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
                                                                                    <?= $site['name'] ?>
                                                                                </option>
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
                                    <table id="search-results" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <!-- <th><input type="checkbox" name="checkall" /></th> -->
                                                <td width="2">#</td>
                                                <th width="8%">ParticipantID
                                                    <hr> Enrollment Date
                                                </th>
                                                <!-- <th width="6%">AGREE USING NIMREGENIN ?</th> -->
                                                <th width="6%">USING NIMREGENIN ?</th>
                                                <th width="10%">Name
                                                    <hr>Gender
                                                    <hr>Age
                                                </th>
                                                <th width="3%">PATIENT TYPE
                                                    <hr>SITE
                                                </th>
                                                <!-- <th width="3%">TREATMENT TYPE</th> -->
                                                <!-- <th width="4%">CATEGORY</th>  -->
                                                <th width="40%">STATUS</th>
                                                 <th width="40%">
                                                <?php if ($_GET['status'] == 4) { ?>
                                                    REASON 
                                                    <?php } else { ?>
                                                    ACTION
                                                <?php } ?>
                                                </th>
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

                                                $Total_visit_available = 1;
                                                $Total_CRF_available = 1;
                                                $Total_CRF_required = 1;
                                                $progress = 1;

                                                $Total_visit_available = intval($override->getCountNot('visit', 'client_id', $client['id'], 'visit_code', 'AE', 'END'));
                                                if ($Total_visit_available < 1) {
                                                    $Total_visit_available = 1;
                                                    $Total_CRF_available = 1;
                                                    $Total_CRF_required = 1;
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
                                                    <td><?= $client['study_id'] ?>
                                                        <hr> <?= $visit_date['visit_date'] ?>
                                                    </td>
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
                                                    <td> <?= $client['firstname'] . ' ' . $client['lastname'] ?>
                                                        <hr><?= $client['gender'] ?>
                                                        <hr><?= $client['age'] ?>
                                                    </td>
                                                    <td><?= $cat ?>
                                                        <hr>
                                                        <?php if ($client['site_id'] == 1) { ?>
                                                            MNH - UPANGA
                                                        <?php } else { ?>

                                                            ORCI
                                                        <?php } ?>
                                                    </td>
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
                                                                <a href="#clientView<?= $client['id'] ?>" role="button"
                                                                    class="btn btn-default" data-toggle="modal">View</a>
                                                                <a href="id.php?cid=<?= $client['id'] ?>"
                                                                    class="btn btn-warning">Patient ID</a>
                                                                <a href="#delete<?= $client['id'] ?>" role="button"
                                                                    class="btn btn-danger" data-toggle="modal">Delete</a>
                                                                <a href="#deleteSchedule<?= $client['id'] ?>" role="button"
                                                                    class="btn btn-danger" data-toggle="modal">Delete Schedule</a>
                                                                <a href="#screened<?= $client['id'] ?>" role="button"
                                                                    class="btn btn-info" data-toggle="modal">screened</a>
                                                                <a href="#eligibility1<?= $client['id'] ?>" role="button"
                                                                    class="btn btn-info" data-toggle="modal">eligibility1</a><br>
                                                                <a href="#eligibility2<?= $client['id'] ?>" role="button"
                                                                    class="btn btn-info" data-toggle="modal">eligibility2</a>
                                                                <a href="#eligible<?= $client['id'] ?>" role="button"
                                                                    class="btn btn-info" data-toggle="modal">eligible</a>
                                                                <a href="#enrolled<?= $client['id'] ?>" role="button"
                                                                    class="btn btn-info" data-toggle="modal">enrolled</a>
                                                            <?php } ?>
                                                            <hr>
                                                            <a href="#asignID<?= $client['id'] ?>" role="button"
                                                                class="btn btn-success" data-toggle="modal">asign ID</a>
                                                            <hr>
                                                            <a href="add.php?id=4&cid=<?= $client['id'] ?>&status=<?= $_GET['status'] ?>"
                                                                class="btn btn-info">Edit</a>

                                                            <hr>
                                                            <?php
                                                            //  if ($screened == 1) {
                                                
                                                            ?>
                                                            <?php if ($screening1 >= 1) { ?>
                                                                <a href="#addInclusion<?= $client['id'] ?>" role="button"
                                                                    class="btn btn-info" data-toggle="modal">Edit Inclusion</a>

                                                            <?php } else { ?>
                                                                <a href="#addInclusion<?= $client['id'] ?>" role="button"
                                                                    class="btn btn-warning" data-toggle="modal">Add Inclusion</a>

                                                            <?php } ?>
                                                            <hr>
                                                            <?php if ($screening2 >= 1) { ?>
                                                                <a href="#addExclusion<?= $client['id'] ?>" role="button"
                                                                    class="btn btn-info" data-toggle="modal">Edit Exclusion</a>

                                                            <?php } else { ?>
                                                                <a href="#addExclusion<?= $client['id'] ?>" role="button"
                                                                    class="btn btn-warning" data-toggle="modal">Add Exclusion</a>

                                                                <?php
                                                            }
                                                        // }
                                                    }
                                                    ?>
                                                        <?php if ($_GET['status'] == 2) { ?>
                                                            <?php if ($eligible == 1) { ?>
                                                                <?php if ($visit >= 1) { ?>
                                                                    <a href="#editEnrollment<?= $client['id'] ?>" role="button"
                                                                        class="btn btn-info" data-toggle="modal">Edit Enrollment</a>
                                                                <?php } else { ?>
                                                                    <a href="#addEnrollment<?= $client['id'] ?>" role="button"
                                                                        class="btn btn-warning" data-toggle="modal">Add Enrollment</a>

                                                                <?php }
                                                            } ?>
                                                        <?php } ?>
                                                        <?php if ($visit >= 1) { ?>
                                                            <?php if ($_GET['status'] == 3) { ?>
                                                                <?php if ($enrolled == 1) { ?>
                                                                    <a href="info.php?id=7&cid=<?= $client['id'] ?>" role="button"
                                                                        class="btn btn-success">schedule</a>
                                                                    <hr>
                                                                    <?php if ($client_progress == 100) { ?>
                                                                        <span class="badge badge-primary right">
                                                                            <?= $Total_CRF_available ?> out of <?= $Total_CRF_required ?>
                                                                        </span>
                                                                        <hr>
                                                                        <span class="badge badge-primary right">
                                                                            <?= $client_progress ?>%
                                                                            <?php
                                                                            // $user->updateRecord('clients', array(
                                                                            //     'progress' => $client_progress,
                                                                            // ), $client['id']);
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
                                                                            // $user->updateRecord('clients', array(
                                                                            //     'progress' => $client_progress,
                                                                            // ), $client['id']);
                                                                            ?> </span>
                                                                    <?php } elseif ($client_progress >= 80 && $client_progress < 100) { ?>
                                                                        <span class="badge badge-info right">
                                                                            <?= $Total_CRF_available ?> out of <?= $Total_CRF_required ?>
                                                                        </span>
                                                                        <hr>
                                                                        <span class="badge badge-info right">
                                                                            <?= $client_progress ?>%
                                                                            <?php
                                                                            // $user->updateRecord('clients', array(
                                                                            //     'progress' => $client_progress,
                                                                            // ), $client['id']);
                                                                            ?> </span>
                                                                    <?php } elseif ($client_progress >= 50 && $client_progress < 80) { ?>
                                                                        <span class="badge badge-secondary right">
                                                                            <?= $Total_CRF_available ?> out of <?= $Total_CRF_required ?>
                                                                        </span>
                                                                        <hr>
                                                                        <span class="badge badge-secondary right">
                                                                            <?= $client_progress ?>%
                                                                            <?php
                                                                            // $user->updateRecord('clients', array(
                                                                            //     'progress' => $client_progress,
                                                                            // ), $client['id']);
                                                                            ?> </span>
                                                                    <?php } elseif ($client_progress < 50) { ?>
                                                                        <span class="badge badge-danger right">
                                                                            <?= $Total_CRF_available ?> out of <?= $Total_CRF_required ?>
                                                                        </span>
                                                                        <hr>
                                                                        <span class="badge badge-danger right">
                                                                            <?= $client_progress ?>%
                                                                            <?php
                                                                            // $user->updateRecord('clients', array(
                                                                            //     'progress' => $client_progress,
                                                                            // ), $client['id']);
                                                                            ?>
                                                                        </span>
                                                                    <?php } ?>
                                                                <?php }
                                                            }
                                                        } ?>
                                                    </td>
                                                </tr>
                                                <div class="modal fade" id="clientView<?= $client['id'] ?>" tabindex="-1"
                                                    role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <form method="post">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close"
                                                                        data-dismiss="modal"><span
                                                                            aria-hidden="true">&times;</span><span
                                                                            class="sr-only">Close</span></button>
                                                                    <h4>Edit Client View</h4>
                                                                </div>
                                                                <div class="modal-body modal-body-np">
                                                                    <div class="row">
                                                                        <div class="block-fluid">
                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-2">Study</div>
                                                                                <div class="col-md-6">
                                                                                    <select name="position" style="width: 100%;"
                                                                                        disabled>
                                                                                        <?php foreach ($override->getData('study') as $study) { ?>
                                                                                            <option value="<?= $study['id'] ?>">
                                                                                                <?= $study['name'] ?>
                                                                                            </option>
                                                                                        <?php } ?>
                                                                                    </select>
                                                                                </div>
                                                                                <div class="col-md-4 pull-right">
                                                                                    <img src="<?= $img ?>" class="img-thumbnail"
                                                                                        width="50%" height="50%" />
                                                                                </div>
                                                                            </div>
                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">ParticipantID:</div>
                                                                                <div class="col-md-9">
                                                                                    <input
                                                                                        value="<?= $client['participant_id'] ?>"
                                                                                        class="validate[required]" type="text"
                                                                                        name="participant_id"
                                                                                        id="participant_id" disabled />
                                                                                </div>
                                                                            </div>
                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Date:</div>
                                                                                <div class="col-md-9">
                                                                                    <input value="<?= $client['clinic_date'] ?>"
                                                                                        class="validate[required,custom[date]]"
                                                                                        type="text" name="clinic_date"
                                                                                        id="clinic_date" disabled />
                                                                                    <span>Example:
                                                                                        2010-12-01</span>
                                                                                </div>
                                                                            </div>
                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">First Name:</div>
                                                                                <div class="col-md-9">
                                                                                    <input value="<?= $client['firstname'] ?>"
                                                                                        class="validate[required]" type="text"
                                                                                        name="firstname" id="firstname"
                                                                                        disabled />
                                                                                </div>
                                                                            </div>
                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Middle Name:</div>
                                                                                <div class="col-md-9">
                                                                                    <input value="<?= $client['middlename'] ?>"
                                                                                        class="validate[required]" type="text"
                                                                                        name="middlename" id="middlename"
                                                                                        disabled />
                                                                                </div>
                                                                            </div>
                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Last Name:</div>
                                                                                <div class="col-md-9">
                                                                                    <input value="<?= $client['lastname'] ?>"
                                                                                        class="validate[required]" type="text"
                                                                                        name="lastname" id="lastname"
                                                                                        disabled />
                                                                                </div>
                                                                            </div>

                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Date of Birth:</div>
                                                                                <div class="col-md-9">
                                                                                    <input value="<?= $client['dob'] ?>"
                                                                                        class="validate[required,custom[date]]"
                                                                                        type="text" name="dob" id="dob"
                                                                                        disabled />
                                                                                    <span>Example: 2010-12-01</span>
                                                                                </div>
                                                                            </div>

                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Age:</div>
                                                                                <div class="col-md-9">
                                                                                    <input value="<?= $client['age'] ?>"
                                                                                        class="validate[required]" type="text"
                                                                                        name="age" id="age" disabled />
                                                                                </div>
                                                                            </div>

                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Initials:</div>
                                                                                <div class="col-md-9">
                                                                                    <input value="<?= $client['initials'] ?>"
                                                                                        class="validate[required]" type="text"
                                                                                        name="initials" id="initials"
                                                                                        disabled />
                                                                                </div>
                                                                            </div>

                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Gender</div>
                                                                                <div class="col-md-9">
                                                                                    <select name="gender" style="width: 100%;"
                                                                                        disabled>
                                                                                        <option
                                                                                            value="<?= $client['gender'] ?>">
                                                                                            <?= $client['gender'] ?>
                                                                                        </option>
                                                                                        <option value="male">Male</option>
                                                                                        <option value="female">Female</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>

                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Hospital ID:</div>
                                                                                <div class="col-md-9">
                                                                                    <input value="<?= $client['id_number'] ?>"
                                                                                        class="validate[required]" type="text"
                                                                                        name="id_number" id="id_number"
                                                                                        disabled />
                                                                                </div>
                                                                            </div>

                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Marital Status</div>
                                                                                <div class="col-md-9">
                                                                                    <select name="marital_status"
                                                                                        style="width: 100%;" disabled>
                                                                                        <option
                                                                                            value="<?= $client['marital_status'] ?>">
                                                                                            <?= $client['marital_status'] ?>
                                                                                        </option>
                                                                                        <option value="Single">Single</option>
                                                                                        <option value="Married">Married</option>
                                                                                        <option value="Divorced">Divorced
                                                                                        </option>
                                                                                        <option value="Separated">Separated
                                                                                        </option>
                                                                                        <option value="Widower">Widower/Widow
                                                                                        </option>
                                                                                        <option value="Cohabit">Cohabit</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>

                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Education Level</div>
                                                                                <div class="col-md-9">
                                                                                    <select name="education_level"
                                                                                        style="width: 100%;" disabled>
                                                                                        <option
                                                                                            value="<?= $client['education_level'] ?>">
                                                                                            <?= $client['education_level'] ?>
                                                                                        </option>
                                                                                        <option value="Not attended school">Not
                                                                                            attended school</option>
                                                                                        <option value="Primary">Primary</option>
                                                                                        <option value="Secondary">Secondary
                                                                                        </option>
                                                                                        <option value="Certificate">Certificate
                                                                                        </option>
                                                                                        <option value="Diploma">Diploma</option>
                                                                                        <option value="Undergraduate degree">
                                                                                            Undergraduate degree</option>
                                                                                        <option value="Postgraduate degree">
                                                                                            Postgraduate degree</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Workplace/station site:
                                                                                </div>
                                                                                <div class="col-md-9"><input
                                                                                        value="<?= $client['workplace'] ?>"
                                                                                        class="" type="text" name="workplace"
                                                                                        id="workplace" disabled /></div>
                                                                            </div>
                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Occupation:</div>
                                                                                <div class="col-md-9"><input
                                                                                        value="<?= $client['occupation'] ?>"
                                                                                        class="" type="text" name="occupation"
                                                                                        id="occupation" disabled /></div>
                                                                            </div>
                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Phone Number:</div>
                                                                                <div class="col-md-9"><input
                                                                                        value="<?= $client['phone_number'] ?>"
                                                                                        class="" type="text" name="phone_number"
                                                                                        id="phone" disabled /> <span>Example:
                                                                                        0700
                                                                                        000 111</span></div>
                                                                            </div>
                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Relative's Phone Number:
                                                                                </div>
                                                                                <div class="col-md-9"><input
                                                                                        value="<?= $client['other_phone'] ?>"
                                                                                        class="" type="text" name="other_phone"
                                                                                        id="phone" disabled /> <span>Example:
                                                                                        0700
                                                                                        000 111</span></div>
                                                                            </div>
                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Residence Street:</div>
                                                                                <div class="col-md-9"><input
                                                                                        value="<?= $client['street'] ?>"
                                                                                        class="" type="text" name="street"
                                                                                        id="street" disabled /></div>
                                                                            </div>
                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Ward:</div>
                                                                                <div class="col-md-9"><input
                                                                                        value="<?= $client['ward'] ?>" class=""
                                                                                        type="text" name="ward" id="ward"
                                                                                        disabled /></div>
                                                                            </div>

                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">House Number:</div>
                                                                                <div class="col-md-9"><input
                                                                                        value="<?= $client['block_no'] ?>"
                                                                                        class="" type="text" name="block_no"
                                                                                        id="block_no" disabled /></div>
                                                                            </div>
                                                                            <div class="row-form clearfix">
                                                                                <div class="col-md-3">Comments:</div>
                                                                                <div class="col-md-9"><textarea name="comments"
                                                                                        rows="4"
                                                                                        disabled><?= $client['comments'] ?></textarea>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="dr"><span></span></div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <input type="hidden" name="id" value="<?= $client['id'] ?>">
                                                                    <button class="btn btn-default" data-dismiss="modal"
                                                                        aria-hidden="true">Close</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                                <div class="modal fade" id="client<?= $client['id'] ?>" tabindex="-1"
                                                    role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <form id="validation" enctype="multipart/form-data" method="post">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close"
                                                                        data-dismiss="modal"><span
                                                                            aria-hidden="true">&times;</span><span
                                                                            class="sr-only">Close</span></button>
                                                                    <h4>Edit Client Info</h4>
                                                                </div>
                                                                <div class="modal-body modal-body-np">

                                                                    <div class="row">
                                                                        <div class="col-sm-3">
                                                                            <div class="row-form clearfix">
                                                                                <!-- select -->
                                                                                <div class="form-group">
                                                                                    <label>Registration Date</label>
                                                                                    <input
                                                                                        class="validate[required,custom[date]]"
                                                                                        type="text" name="clinic_date"
                                                                                        id="clinic_date" value="<?php if ($client['clinic_date']) {
                                                                                            print_r($client['clinic_date']);
                                                                                        } ?>" />
                                                                                    <span>Example: 2010-12-01</span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-3">
                                                                            <div class="row-form clearfix">
                                                                                <!-- select -->
                                                                                <div class="form-group">
                                                                                    <label>First Name</label>
                                                                                    <input type="text" name="firstname"
                                                                                        id="firstname" value="<?php if ($client['firstname']) {
                                                                                            print_r($client['firstname']);
                                                                                        } ?>" />
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-3">
                                                                            <div class="row-form clearfix">
                                                                                <!-- select -->
                                                                                <div class="form-group">
                                                                                    <label>Middle Name</label>
                                                                                    <input type="text" name="middlename"
                                                                                        id="middlename" value="<?php if ($client['middlename']) {
                                                                                            print_r($client['middlename']);
                                                                                        } ?>" />
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-3">
                                                                            <div class="row-form clearfix">
                                                                                <!-- select -->
                                                                                <div class="form-group">
                                                                                    <label>Last Name</label>
                                                                                    <input type="text" name="lastname"
                                                                                        id="lastname" value="<?php if ($client['lastname']) {
                                                                                            print_r($client['lastname']);
                                                                                        } ?>" />
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="row">
                                                                        <div class="col-sm-3">
                                                                            <div class="row-form clearfix">
                                                                                <!-- select -->
                                                                                <div class="form-group">
                                                                                    <label>Date of Birth</label>
                                                                                    <input
                                                                                        class="validate[required,custom[date]]"
                                                                                        type="text" name="dob" id="dob" value="<?php if ($client['dob']) {
                                                                                            print_r($client['dob']);
                                                                                        } ?>" />
                                                                                    <span>Example: 2010-12-01</span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-3">
                                                                            <div class="row-form clearfix">
                                                                                <!-- select -->
                                                                                <div class="form-group">
                                                                                    <label>Age</label>
                                                                                    <input type="text" name="Age" id="Age"
                                                                                        value="<?php if ($client['age']) {
                                                                                            print_r($client['age']);
                                                                                        } ?>" disabled />
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-3">
                                                                            <div class="row-form clearfix">
                                                                                <!-- select -->
                                                                                <div class="form-group">
                                                                                    <label>Initials</label>
                                                                                    <input type="text" name="initials"
                                                                                        id="initials" value="<?php if ($client['initials']) {
                                                                                            print_r($client['initials']);
                                                                                        } ?>" />
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-3">
                                                                            <div class="row-form clearfix">
                                                                                <!-- select -->
                                                                                <div class="form-group">
                                                                                    <label>Gender</label>
                                                                                    <select name="gender" id="gender"
                                                                                        style="width: 100%;">
                                                                                        <option
                                                                                            value="<?= $client['gender'] ?>">
                                                                                            <?php if ($client) {
                                                                                                if ($client['gender'] == 'male') {
                                                                                                    echo 'Male';
                                                                                                } elseif ($client['gender'] == 'female') {
                                                                                                    echo 'Female';
                                                                                                }
                                                                                            } else {
                                                                                                echo 'Select';
                                                                                            } ?>
                                                                                        </option>
                                                                                        <option value="male">Male</option>
                                                                                        <option value="female">Female</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="row">

                                                                        <div class="col-sm-3">
                                                                            <div class="row-form clearfix">
                                                                                <!-- select -->
                                                                                <div class="form-group">
                                                                                    <label>Hospital ID Number</label>
                                                                                    <input type="text" name="id_number"
                                                                                        id="id_number" value="<?php if ($client['id_number']) {
                                                                                            print_r($client['id_number']);
                                                                                        } ?>" />
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-sm-3">
                                                                            <div class="row-form clearfix">
                                                                                <!-- select -->
                                                                                <div class="form-group">
                                                                                    <label>Marital Status</label>
                                                                                    <select name="marital_status"
                                                                                        id="marital_status"
                                                                                        style="width: 100%;">
                                                                                        <option
                                                                                            value="<?= $client['marital_status'] ?>">
                                                                                            <?php if ($client) {
                                                                                                if ($client['marital_status'] == 'Single') {
                                                                                                    echo 'Single';
                                                                                                } elseif ($client['marital_status'] == 'Married') {
                                                                                                    echo 'Married';
                                                                                                } elseif ($client['marital_status'] == 'Divorced') {
                                                                                                    echo 'Divorced';
                                                                                                } elseif ($client['marital_status'] == 'Separated') {
                                                                                                    echo 'Separated';
                                                                                                } elseif ($client['marital_status'] == 'Widower') {
                                                                                                    echo 'Widower';
                                                                                                } elseif ($client['marital_status'] == 'Cohabit') {
                                                                                                    echo 'Cohabit';
                                                                                                }
                                                                                            } else {
                                                                                                echo 'Select';
                                                                                            } ?>
                                                                                        </option>
                                                                                        <option value="Single">Single</option>
                                                                                        <option value="Married">Married</option>
                                                                                        <option value="Divorced">Divorced
                                                                                        </option>
                                                                                        <option value="Separated">Separated
                                                                                        </option>
                                                                                        <option value="Widower">Widower/Widow
                                                                                        </option>
                                                                                        <option value="Cohabit">Cohabit</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-sm-3">
                                                                            <div class="row-form clearfix">
                                                                                <!-- select -->
                                                                                <div class="form-group">
                                                                                    <label>Education Level</label>
                                                                                    <select name="education_level"
                                                                                        id="education_level"
                                                                                        style="width: 100%;">
                                                                                        <option
                                                                                            value="<?= $client['education_level'] ?>">
                                                                                            <?php if ($client) {
                                                                                                if ($client['education_level'] == 'Not attended school') {
                                                                                                    echo 'Not attended school';
                                                                                                } elseif ($client['education_level'] == 'Primary') {
                                                                                                    echo 'Primary';
                                                                                                } elseif ($client['education_level'] == 'Secondary') {
                                                                                                    echo 'Secondary';
                                                                                                } elseif ($client['education_level'] == 'Certificate') {
                                                                                                    echo 'Certificate';
                                                                                                } elseif ($client['education_level'] == 'Diploma') {
                                                                                                    echo 'Diploma';
                                                                                                } elseif ($client['education_level'] == 'Undergraduate degree') {
                                                                                                    echo 'Undergraduate degree';
                                                                                                } elseif ($client['education_level'] == 'Postgraduate degree') {
                                                                                                    echo 'Postgraduate degree';
                                                                                                }
                                                                                            } else {
                                                                                                echo 'Select';
                                                                                            } ?>
                                                                                        </option>
                                                                                        <option value="Not attended school">Not
                                                                                            attended school</option>
                                                                                        <option value="Primary">Primary</option>
                                                                                        <option value="Secondary">Secondary
                                                                                        </option>
                                                                                        <option value="Certificate">Certificate
                                                                                        </option>
                                                                                        <option value="Diploma">Diploma</option>
                                                                                        <option value="Undergraduate degree">
                                                                                            Undergraduate degree</option>
                                                                                        <option value="Postgraduate degree">
                                                                                            Postgraduate degree</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-sm-3">
                                                                            <div class="row-form clearfix">
                                                                                <!-- select -->
                                                                                <div class="form-group">
                                                                                    <label>Occupation</label>
                                                                                    <input type="text" name="occupation"
                                                                                        id="occupation" value="<?php if ($client['occupation']) {
                                                                                            print_r($client['occupation']);
                                                                                        } ?>" />
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="row">

                                                                        <div class="col-sm-3">
                                                                            <div class="row-form clearfix">
                                                                                <!-- select -->
                                                                                <div class="form-group">
                                                                                    <label>Occupation</label>
                                                                                    <input type="text" name="occupation"
                                                                                        id="occupation" value="<?php if ($client['occupation']) {
                                                                                            print_r($client['occupation']);
                                                                                        } ?>" />
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-sm-3">
                                                                            <div class="row-form clearfix">
                                                                                <!-- select -->
                                                                                <div class="form-group">
                                                                                    <label>National ID</label>
                                                                                    <input type="text" name="national_id"
                                                                                        id="national_id" value="<?php if ($client['national_id']) {
                                                                                            print_r($client['national_id']);
                                                                                        } ?>" />
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-sm-3">
                                                                            <div class="row-form clearfix">
                                                                                <!-- select -->
                                                                                <div class="form-group">
                                                                                    <label>Phone Number</label>
                                                                                    <input type="text" name="phone_number"
                                                                                        id="phone_number" value="<?php if ($client['phone_number']) {
                                                                                            print_r($client['phone_number']);
                                                                                        } ?>" />
                                                                                </div>
                                                                                <span>Example: 0700 000 111</span>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-sm-3">
                                                                            <div class="row-form clearfix">
                                                                                <!-- select -->
                                                                                <div class="form-group">
                                                                                    <label>Relative's Phone Number</label>
                                                                                    <input type="text" name="other_phone"
                                                                                        id="other_phone" value="<?php if ($client['other_phone']) {
                                                                                            print_r($client['other_phone']);
                                                                                        } ?>" />
                                                                                </div>
                                                                                <span>Example: 0700 000 111</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="row">

                                                                        <div class="col-sm-3">
                                                                            <div class="row-form clearfix">
                                                                                <!-- select -->
                                                                                <div class="form-group">
                                                                                    <label>Residence Street:</label>
                                                                                    <input type="text" name="street" id="street"
                                                                                        value="<?php if ($client['street']) {
                                                                                            print_r($client['street']);
                                                                                        } ?>" />
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-sm-3">
                                                                            <div class="row-form clearfix">
                                                                                <!-- select -->
                                                                                <div class="form-group">
                                                                                    <label>Region</label>
                                                                                    <input type="text" name="region" id="region"
                                                                                        value="<?php if ($client['region']) {
                                                                                            print_r($client['region']);
                                                                                        } ?>" />
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-sm-3">
                                                                            <div class="row-form clearfix">
                                                                                <!-- select -->
                                                                                <div class="form-group">
                                                                                    <label>District</label>
                                                                                    <input type="text" name="district"
                                                                                        id="district" value="<?php if ($client['district']) {
                                                                                            print_r($client['district']);
                                                                                        } ?>" />
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-sm-3">
                                                                            <div class="row-form clearfix">
                                                                                <!-- select -->
                                                                                <div class="form-group">
                                                                                    <label>Ward</label>
                                                                                    <input type="text" name="ward" id="ward"
                                                                                        value="<?php if ($client['ward']) {
                                                                                            print_r($client['ward']);
                                                                                        } ?>" />
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row-form clearfix">
                                                                        <div class="col-md-3">Comments:</div>
                                                                        <div class="col-md-9">
                                                                            <textarea name="comments" rows="4">
                                                                                                                                                                                    <?php if ($client['comments']) {
                                                                                                                                                                                        print_r($client['comments']);
                                                                                                                                                                                    } ?>
                                                                                                                                                                                    </textarea>
                                                                        </div>
                                                                    </div>


                                                                    <div class="dr"><span></span></div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <input type="hidden" name="client_image"
                                                                        value="<?= $client['client_image'] ?>" />
                                                                    <input type="hidden" name="id" value="<?= $client['id'] ?>">
                                                                    <input type="submit" name="edit_client" value="Save updates"
                                                                        class="btn btn-warning">
                                                                    <button class="btn btn-default" data-dismiss="modal"
                                                                        aria-hidden="true">Close</button>
                                                                </div>
                                                            </div>

                                                    </div>
                                                    </form>
                                                </div>
                                                <div class="modal fade" id="delete<?= $client['id'] ?>" tabindex="-1"
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
                                                                    <input type="hidden" name="id" value="<?= $client['id'] ?>">
                                                                    <input type="submit" name="delete_client" value="Delete"
                                                                        class="btn btn-danger">
                                                                    <button class="btn btn-default" data-dismiss="modal"
                                                                        aria-hidden="true">Close</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                                <div class="modal fade" id="deleteSchedule<?= $client['id'] ?>" tabindex="-1"
                                                    role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <form method="post">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close"
                                                                        data-dismiss="modal"><span
                                                                            aria-hidden="true">&times;</span><span
                                                                            class="sr-only">Close</span></button>
                                                                    <h4>Delete User Schedule</h4>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <strong style="font-weight: bold;color: red">
                                                                        <p>Are you sure you want to delete this user Schedule
                                                                        </p>
                                                                    </strong>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <input type="hidden" name="id" value="<?= $client['id'] ?>">
                                                                    <input type="submit" name="delete_schedule" value="Delete"
                                                                        class="btn btn-danger">
                                                                    <button class="btn btn-default" data-dismiss="modal"
                                                                        aria-hidden="true">Close</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
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
                                                <div class="modal fade" id="addInclusion<?= $client['id'] ?>" tabindex="-1"
                                                    role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-warning">
                                                                <h4 class="modal-title">Add Inclusion Criteria</h4>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <form method="post">
                                                                <?php $screening = $override->get('screening', 'client_id', $client['id'])[0]; ?>
                                                                <div class="modal-body">
                                                                    <div class="row">
                                                                        <div class="col-sm-4">
                                                                            <div class="form-group">
                                                                                <label>Date of Screening</label>
                                                                                <input
                                                                                    class="form-control validate[required,custom[date]]"
                                                                                    type="date" name="screening_date"
                                                                                    id="screening_date" value="<?php if ($screening['screening_date']) {
                                                                                        print_r($screening['screening_date']);
                                                                                    } ?>" required />
                                                                                <span>Example: 2010-12-01</span>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-sm-4">
                                                                            <div class="form-group">
                                                                                <label>Aged eighteen years and above</label>
                                                                                <select class="form-control" name="age_18"
                                                                                    style="width: 100%;" required>
                                                                                    <option value="<?= $screening['age_18'] ?>">
                                                                                        <?php if ($screening) {
                                                                                            if ($screening['age_18'] == 1) {
                                                                                                echo 'Yes';
                                                                                            } elseif ($screening['age_18'] == 2) {
                                                                                                echo 'No';
                                                                                            }
                                                                                        } else {
                                                                                            echo 'Select';
                                                                                        } ?>
                                                                                    </option>
                                                                                    <option value="1">Yes</option>
                                                                                    <option value="2">No</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-sm-4">
                                                                            <div class="form-group">
                                                                                <label>Confirmed cancer with biopsy?</label>
                                                                                <select class="form-control" name="biopsy"
                                                                                    style="width: 100%;" required>
                                                                                    <option value="<?= $screening['biopsy'] ?>">
                                                                                        <?php if ($screening) {
                                                                                            if ($screening['biopsy'] == 1) {
                                                                                                echo 'Yes';
                                                                                            } elseif ($screening['biopsy'] == 2) {
                                                                                                echo 'No';
                                                                                            } elseif ($screening['biopsy'] == 3) {
                                                                                                echo 'Not Applicable';
                                                                                            }
                                                                                        } else {
                                                                                            echo 'Select';
                                                                                        } ?>
                                                                                    </option>
                                                                                    <option value="1">Yes</option>
                                                                                    <option value="2">No</option>
                                                                                    <option value="3">Not Applicable</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>


                                                                    <div class="row">

                                                                        <div class="col-sm-4">
                                                                            <div class="form-group">
                                                                                <label>Brain cancer</label>
                                                                                <select class="form-control" name="brain_cancer"
                                                                                    style="width: 100%;" required>
                                                                                    <option
                                                                                        value="<?= $screening['brain_cancer'] ?>">
                                                                                        <?php if ($screening) {
                                                                                            if ($screening['brain_cancer'] == 1) {
                                                                                                echo 'Yes';
                                                                                            } elseif ($screening['brain_cancer'] == 2) {
                                                                                                echo 'No';
                                                                                            }
                                                                                        } else {
                                                                                            echo 'Select';
                                                                                        } ?>
                                                                                    </option>
                                                                                    <option value="1">Yes</option>
                                                                                    <option value="2">No</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-sm-4">
                                                                            <div class="form-group">
                                                                                <label>Breast cancer</label>
                                                                                <select class="form-control"
                                                                                    name="breast_cancer" style="width: 100%;"
                                                                                    required>
                                                                                    <option
                                                                                        value="<?= $screening['breast_cancer'] ?>">
                                                                                        <?php if ($screening) {
                                                                                            if ($screening['breast_cancer'] == 1) {
                                                                                                echo 'Yes';
                                                                                            } elseif ($screening['breast_cancer'] == 2) {
                                                                                                echo 'No';
                                                                                            }
                                                                                        } else {
                                                                                            echo 'Select';
                                                                                        } ?>
                                                                                    </option>
                                                                                    <option value="1">Yes</option>
                                                                                    <option value="2">No</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>

                                                                        <?php if ($client['gender'] == "female") { ?>

                                                                            <div class="col-sm-4">
                                                                                <div class="form-group">
                                                                                    <label>Cervical cancer</label>
                                                                                    <select class="form-control"
                                                                                        name="cervical_cancer" style="width: 100%;"
                                                                                        required>
                                                                                        <option
                                                                                            value="<?= $screening['cervical_cancer'] ?>">
                                                                                            <?php if ($screening) {
                                                                                                if ($screening['cervical_cancer'] == 1) {
                                                                                                    echo 'Yes';
                                                                                                } elseif ($screening['cervical_cancer'] == 2) {
                                                                                                    echo 'No';
                                                                                                }
                                                                                            } else {
                                                                                                echo 'Select';
                                                                                            } ?>
                                                                                        </option>
                                                                                        <option value="1">Yes</option>
                                                                                        <option value="2">No</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>

                                                                        <?php } elseif ($client['gender'] == "male") { ?>

                                                                            <div class="col-sm-4">
                                                                                <div class="form-group">
                                                                                    <label>Prostate cancer</label>
                                                                                    <select class="form-control"
                                                                                        name="prostate_cancer" style="width: 100%;"
                                                                                        required>
                                                                                        <option
                                                                                            value="<?= $screening['prostate_cancer'] ?>">
                                                                                            <?php if ($screening) {
                                                                                                if ($screening['prostate_cancer'] == 1) {
                                                                                                    echo 'Yes';
                                                                                                } elseif ($screening['prostate_cancer'] == 2) {
                                                                                                    echo 'No';
                                                                                                }
                                                                                            } else {
                                                                                                echo 'Select';
                                                                                            } ?>
                                                                                        </option>
                                                                                        <option value="1">Yes</option>
                                                                                        <option value="2">No</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>

                                                                        <?php } ?>

                                                                    </div>

                                                                    <div class="row">

                                                                        <div class="col-sm-4">
                                                                            <div class="form-group">
                                                                                <label>Participant Category</label>
                                                                                <select class="form-control"
                                                                                    name="patient_category" style="width: 100%;"
                                                                                    required>
                                                                                    <option
                                                                                        value="<?= $screening['patient_category'] ?>">
                                                                                        <?php if ($screening) {
                                                                                            if ($screening['patient_category'] == 1) {
                                                                                                echo 'Intervention';
                                                                                            } elseif ($screening['patient_category'] == 2) {
                                                                                                echo 'Control';
                                                                                            }
                                                                                        } else {
                                                                                            echo 'Select';
                                                                                        } ?>
                                                                                    </option>
                                                                                    <option value="1">Intervention</option>
                                                                                    <option value="2">Control</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-4">
                                                                            <div class="form-group">
                                                                                <label>Did the participant consent to be part of
                                                                                    the study?</label>
                                                                                <select class="form-control" name="consented"
                                                                                    style="width: 100%;" required>
                                                                                    <option
                                                                                        value="<?= $screening['consented'] ?>">
                                                                                        <?php if ($screening) {
                                                                                            if ($screening['consented'] == 1) {
                                                                                                echo 'Yes';
                                                                                            } elseif ($screening['consented'] == 2) {
                                                                                                echo 'No';
                                                                                            }
                                                                                        } else {
                                                                                            echo 'Select';
                                                                                        } ?>
                                                                                    </option>
                                                                                    <option value="1">Yes</option>
                                                                                    <option value="2">No</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-sm-4">
                                                                            <div class="form-group">
                                                                                <label>Did the participant consent to use
                                                                                    NIMREGENIN preparation?</label>
                                                                                <select class="form-control"
                                                                                    name="consented_nimregenin"
                                                                                    style="width: 100%;" required>
                                                                                    <option
                                                                                        value="<?= $screening['consented_nimregenin'] ?>">
                                                                                        <?php if ($screening) {
                                                                                            if ($screening['consented_nimregenin'] == 1) {
                                                                                                echo 'Yes';
                                                                                            } elseif ($screening['consented_nimregenin'] == 2) {
                                                                                                echo 'No';
                                                                                            }
                                                                                        } else {
                                                                                            echo 'Select';
                                                                                        } ?>
                                                                                    </option>
                                                                                    <option value="1">Yes</option>
                                                                                    <option value="2">No</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>


                                                                        <div class="col-sm-12">
                                                                            <div class="form-group">
                                                                                <label>Notes / Remark / Reason (Option)</label>
                                                                                <textarea class="form-control" name="reasons"
                                                                                    rows="4"><?= $screening['reasons'] ?></textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer justify-content-between">
                                                                    <input type="hidden" name="id" value="<?= $client['id'] ?>">
                                                                    <input type="hidden" name="screening_id"
                                                                        value="<?= $screening['id'] ?>">
                                                                    <input type="hidden" name="gender"
                                                                        value="<?= $client['gender'] ?>">
                                                                    <button type="button" class="btn btn-default"
                                                                        data-dismiss="modal">Close</button>
                                                                    <input type="submit" name="add_Inclusion"
                                                                        class="btn btn-warning" value="Save">
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal fade" id="addExclusion<?= $client['id'] ?>" tabindex="-1"
                                                    role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-warning">
                                                                <h4 class="modal-title">Add Exclusion Criteria</h4>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <form method="post">
                                                                <?php $lab = $override->get('lab', 'client_id', $client['id'])[0];
                                                                print_r($_POST);
                                                                ?>
                                                                <div class="modal-body">
                                                                    <div class="form-group">
                                                                        <label>Date of Screening:</label>
                                                                        <input type="date" class="form-control"
                                                                            name="screening_date" id="screening_date" value="<?php if ($lab['screening_date']) {
                                                                                print_r($lab['screening_date']);
                                                                            } ?>" required>
                                                                        <small class="form-text text-muted">Example:
                                                                            2010-12-01</small>
                                                                    </div>
                                                                    <?php if ($client['gender'] == "female") { ?>
                                                                        <div class="form-group">
                                                                            <label>Is the participant pregnant?</label>
                                                                            <select name="pregnant" class="form-control" required>
                                                                                <option value="<?= $lab['pregnant'] ?>">
                                                                                    <?php echo ($lab['pregnant'] == 1) ? 'Yes' : (($lab['pregnant'] == 2) ? 'No' : 'Not Applicable'); ?>
                                                                                </option>
                                                                                <option value="1">Yes</option>
                                                                                <option value="2">No</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label>Is the participant breastfeeding?</label>
                                                                            <select name="breast_feeding" class="form-control"
                                                                                required>
                                                                                <option value="<?= $lab['breast_feeding'] ?>">
                                                                                    <?php echo ($lab['breast_feeding'] == 1) ? 'Yes' : (($lab['breast_feeding'] == 2) ? 'No' : 'Not Applicable'); ?>
                                                                                </option>
                                                                                <option value="1">Yes</option>
                                                                                <option value="2">No</option>
                                                                            </select>
                                                                        </div>
                                                                    <?php } ?>
                                                                    <div class="form-group">
                                                                        <label>CKD?</label>
                                                                        <select name="cdk" class="form-control" required>
                                                                            <option value="<?= $lab['cdk'] ?>">
                                                                                <?php echo ($lab['cdk'] == 1) ? 'Yes' : (($lab['cdk'] == 2) ? 'No' : 'Not Applicable'); ?>
                                                                            </option>
                                                                            <option value="1">Yes</option>
                                                                            <option value="2">No</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Liver Disease?</label>
                                                                        <select name="liver_disease" class="form-control"
                                                                            required>
                                                                            <option value="<?= $lab['liver_disease'] ?>">
                                                                                <?php echo ($lab['liver_disease'] == 1) ? 'Yes' : (($lab['liver_disease'] == 2) ? 'No' : 'Not Applicable'); ?>
                                                                            </option>
                                                                            <option value="1">Yes</option>
                                                                            <option value="2">No</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer justify-content-between">
                                                                    <input type="hidden" name="id" value="<?= $client['id'] ?>">
                                                                    <input type="hidden" name="lab_id"
                                                                        value="<?= $lab['id'] ?>">
                                                                    <input type="hidden" name="gender"
                                                                        value="<?= $client['gender'] ?>">
                                                                    <input type="submit" name="add_Exclusion"
                                                                        class="btn btn-warning" value="Save">
                                                                    <!-- <button type="submit" name="add_Exclusion"
                                                                        class="btn btn-warning">Save</button> -->
                                                                    <button type="button" class="btn btn-default"
                                                                        data-dismiss="modal">Close</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="modal fade" id="addEnrollment<?= $client['id'] ?>" tabindex="-1"
                                                    aria-labelledby="modalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-primary">
                                                                <h4 class="modal-title">Add Visit</h4>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <form id="validation" method="post">
                                                                <div class="modal-body">
                                                                    <div class="row">
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label>Patient Type</label>
                                                                                <select name="pt_type" id="pt_type"
                                                                                    class="form-control" required>
                                                                                    <option value="">Select</option>
                                                                                    <option value="1">New Patient</option>
                                                                                    <option value="2">Already Enrolled</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label>Treatment Type</label>
                                                                                <select name="treatment_type"
                                                                                    id="treatment_type" class="form-control"
                                                                                    required>
                                                                                    <option value="">Select</option>
                                                                                    <option value="1">Radiotherapy Treatment
                                                                                    </option>
                                                                                    <option value="2">Chemotherapy Treatment
                                                                                    </option>
                                                                                    <option value="3">Surgery Treatment</option>
                                                                                    <option value="4">Active surveillance
                                                                                    </option>
                                                                                    <option value="5">Hormonal therapy ie ADT
                                                                                    </option>
                                                                                    <option value="96">Other (If Other write in
                                                                                        Notes / Remarks )</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label>Date Started Previous Treatment</label>
                                                                                <input type="date" name="previous_date"
                                                                                    id="previous_date" class="form-control"
                                                                                    required />
                                                                                <small>Example: 2010-12-01</small>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>Treatment Type 2</label>
                                                                                <select name="treatment_type2"
                                                                                    id="treatment_type2" class="form-control"
                                                                                    required>
                                                                                    <option value="">Select</option>
                                                                                    <option value="1">Radiotherapy Treatment
                                                                                    </option>
                                                                                    <option value="2">Chemotherapy Treatment
                                                                                    </option>
                                                                                    <option value="3">Surgery Treatment</option>
                                                                                    <option value="4">Active surveillance
                                                                                    </option>
                                                                                    <option value="5">Hormonal therapy ie ADT
                                                                                    </option>
                                                                                    <option value="96">Other (If Other write in
                                                                                        Notes / Remarks )</option>
                                                                                    <option value="99">NA</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>Date Started Previous Treatment 2</label>
                                                                                <input type="date" name="previous_date2"
                                                                                    id="previous_date2" class="form-control" />
                                                                                <small>Example: 2010-12-01</small>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>Total number of Cycles for That
                                                                                    Treatment</label>
                                                                                <input type="text" name="total_cycle"
                                                                                    id="total_cycle" class="form-control"
                                                                                    required />
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>Current number of Cycle for Patient
                                                                                    Treatment</label>
                                                                                <input type="text" name="cycle_number"
                                                                                    id="cycle_number" class="form-control"
                                                                                    required />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>Notes / Remark</label>
                                                                                <textarea name="reasons" class="form-control"
                                                                                    rows="4"></textarea>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>Date of Enrollment for This Study (Day
                                                                                    Started Treatment)</label>
                                                                                <input type="date" name="visit_date"
                                                                                    id="visit_date" class="form-control"
                                                                                    required />
                                                                                <small>Example: 2010-12-01</small>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <input type="hidden" name="id" value="<?= $client['id'] ?>">
                                                                    <input type="submit" name="add_Enrollment"
                                                                        class="btn btn-warning" value="Save">
                                                                    <!-- <button type="submit" name="add_Enrollment"
                                                                        class="btn btn-warning">Save</button> -->
                                                                    <button type="button" class="btn btn-default"
                                                                        data-dismiss="modal">Close</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="modal fade" id="editEnrollment<?= $client['id'] ?>" tabindex="-1"
                                                    role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <form id="validation" method="post">
                                                                <?php
                                                                $visit = $override->firstRow('visit', 'visit_date', 'id', 'client_id', $client['id'])[0];
                                                                $reasons = $override->firstRow('visit', 'reasons', 'id', 'client_id', $client['id'])[0];
                                                                $client = $override->getNews('clients', 'status', 1, 'id', $client['id'])[0];
                                                                ?>
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal"
                                                                        aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                    <h4 class="modal-title">Edit Visit</h4>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="row">
                                                                        <div class="col-sm-4">
                                                                            <div class="form-group">
                                                                                <label>Patient Type</label>
                                                                                <select name="pt_type" id="pt_type"
                                                                                    class="form-control" required>
                                                                                    <?php if ($client['pt_type'] == 1) { ?>
                                                                                        <option value="<?= $client['pt_type'] ?>">
                                                                                            New Patient</option>
                                                                                    <?php } else if ($client['pt_type'] == 2) { ?>
                                                                                            <option value="<?= $client['pt_type'] ?>">
                                                                                                Already Enrolled</option>
                                                                                    <?php } else { ?>
                                                                                            <option value="">Select</option>
                                                                                    <?php } ?>
                                                                                    <option value="1">New Patient</option>
                                                                                    <option value="2">Already Enrolled</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-4">
                                                                            <div class="form-group">
                                                                                <label>Treatment Type</label>
                                                                                <select name="treatment_type"
                                                                                    id="treatment_type" class="form-control"
                                                                                    required>
                                                                                    <?php if ($client['treatment_type'] == 1) { ?>
                                                                                        <option
                                                                                            value="<?= $client['treatment_type'] ?>">
                                                                                            Radiotherapy Treatment</option>
                                                                                    <?php } else if ($client['treatment_type'] == 2) { ?>
                                                                                            <option
                                                                                                value="<?= $client['treatment_type'] ?>">
                                                                                                Chemotherapy Treatment</option>
                                                                                    <?php } else if ($client['treatment_type'] == 3) { ?>
                                                                                                <option
                                                                                                    value="<?= $client['treatment_type'] ?>">
                                                                                                    Surgery Treatment</option>
                                                                                    <?php } else if ($client['treatment_type'] == 4) { ?>
                                                                                                    <option
                                                                                                        value="<?= $client['treatment_type'] ?>">
                                                                                                        Active surveillance</option>
                                                                                    <?php } else if ($client['treatment_type'] == 5) { ?>
                                                                                                        <option
                                                                                                            value="<?= $client['treatment_type'] ?>">
                                                                                                            Hormonal therapy ie ADT</option>
                                                                                    <?php } else if ($client['treatment_type'] == 6) { ?>
                                                                                                            <option
                                                                                                                value="<?= $client['treatment_type'] ?>">
                                                                                                                Other (If Other write in Notes /
                                                                                                                Remarks)</option>
                                                                                    <?php } else { ?>
                                                                                                            <option value="">Select</option>
                                                                                    <?php } ?>
                                                                                    <option value="1">Radiotherapy Treatment
                                                                                    </option>
                                                                                    <option value="2">Chemotherapy Treatment
                                                                                    </option>
                                                                                    <option value="3">Surgery Treatment</option>
                                                                                    <option value="4">Active surveillance
                                                                                    </option>
                                                                                    <option value="5">Hormonal therapy ie ADT
                                                                                    </option>
                                                                                    <option value="96">Other (If Other write in
                                                                                        Notes / Remarks)</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-4">
                                                                            <div class="form-group">
                                                                                <label>Date Started Previous(Past)
                                                                                    Treatment:</label>
                                                                                <input value="<?= $client['previous_date'] ?>"
                                                                                    class="form-control" type="date"
                                                                                    name="previous_date" id="previous_date"
                                                                                    required />
                                                                                <small>Example: 2010-12-01</small>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-sm-6">
                                                                            <div class="form-group">
                                                                                <label>Treatment Type 2</label>
                                                                                <select name="treatment_type2"
                                                                                    id="treatment_type2" class="form-control"
                                                                                    required>
                                                                                    <?php if ($client['treatment_type2'] == 1) { ?>
                                                                                        <option
                                                                                            value="<?= $client['treatment_type2'] ?>">
                                                                                            Radiotherapy Treatment</option>
                                                                                    <?php } else if ($client['treatment_type2'] == 2) { ?>
                                                                                            <option
                                                                                                value="<?= $client['treatment_type2'] ?>">
                                                                                                Chemotherapy Treatment</option>
                                                                                    <?php } else if ($client['treatment_type2'] == 3) { ?>
                                                                                                <option
                                                                                                    value="<?= $client['treatment_type2'] ?>">
                                                                                                    Surgery Treatment</option>
                                                                                    <?php } else if ($client['treatment_type2'] == 4) { ?>
                                                                                                    <option
                                                                                                        value="<?= $client['treatment_type2'] ?>">
                                                                                                        Active surveillance</option>
                                                                                    <?php } else if ($client['treatment_type2'] == 5) { ?>
                                                                                                        <option
                                                                                                            value="<?= $client['treatment_type2'] ?>">
                                                                                                            Hormonal therapy ie ADT</option>
                                                                                    <?php } else if ($client['treatment_type2'] == 96) { ?>
                                                                                                            <option
                                                                                                                value="<?= $client['treatment_type2'] ?>">
                                                                                                                Other (If Other write in Notes /
                                                                                                                Remarks)</option>
                                                                                    <?php } else if ($client['treatment_type2'] == 99) { ?>
                                                                                                                <option
                                                                                                                    value="<?= $client['treatment_type2'] ?>">
                                                                                                                    NA</option>
                                                                                    <?php } else { ?>
                                                                                                                <option value="">Select</option>
                                                                                    <?php } ?>
                                                                                    <option value="1">Radiotherapy Treatment
                                                                                    </option>
                                                                                    <option value="2">Chemotherapy Treatment
                                                                                    </option>
                                                                                    <option value="3">Surgery Treatment</option>
                                                                                    <option value="4">Active surveillance
                                                                                    </option>
                                                                                    <option value="5">Hormonal therapy ie ADT
                                                                                    </option>
                                                                                    <option value="96">Other (If Other write in
                                                                                        Notes / Remarks)</option>
                                                                                    <option value="99">NA</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-6">
                                                                            <div class="form-group">
                                                                                <label>Date Started Previous Treatment
                                                                                    2:</label>
                                                                                <input value="<?= $client['previous_date2'] ?>"
                                                                                    class="form-control" type="date"
                                                                                    name="previous_date2" id="previous_date2" />
                                                                                <small>Example: 2010-12-01</small>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-sm-6">
                                                                            <div class="form-group">
                                                                                <label>Total number of Cycles for That
                                                                                    Treatment:</label>
                                                                                <input value="<?= $client['total_cycle'] ?>"
                                                                                    class="form-control" type="text"
                                                                                    name="total_cycle" id="total_cycle"
                                                                                    required />
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-6">
                                                                            <div class="form-group">
                                                                                <label>Current number of Cycle for Patient
                                                                                    Treatment</label>
                                                                                <input value="<?= $client['cycle_number'] ?>"
                                                                                    class="form-control" type="text"
                                                                                    name="cycle_number" id="cycle_number"
                                                                                    required />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-sm-6">
                                                                            <div class="form-group">
                                                                                <label>Notes / Remark</label>
                                                                                <textarea name="reasons" class="form-control"
                                                                                    rows="4"><?= $reasons['reasons'] ?></textarea>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-6">
                                                                            <div class="form-group">
                                                                                <label>Date of Enrollment for This Study(Day
                                                                                    Started Treatment):</label>
                                                                                <input value="<?= $visit['visit_date'] ?>"
                                                                                    class="form-control" type="date"
                                                                                    name="visit_date" id="visit_date"
                                                                                    required />
                                                                                <small>Example: 2010-12-01</small>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <input type="hidden" name="id" value="<?= $client['id'] ?>">
                                                                    <input type="submit" name="edit_Enrollment"
                                                                        class="btn btn-warning" value="Save">
                                                                    <!-- <input type="submit" name="edit_Enrollment"
                                                                        class="btn btn-warning" value="Save"> -->
                                                                    <button class="btn btn-default" data-dismiss="modal"
                                                                        aria-hidden="true">Close</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal fade" id="screened<?= $client['id'] ?>" tabindex="-1"
                                                    role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <form method="post">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close"
                                                                        data-dismiss="modal"><span
                                                                            aria-hidden="true">&times;</span><span
                                                                            class="sr-only">Close</span></button>
                                                                    <h4>Change screened Status</h4>
                                                                </div>
                                                                <div class="modal-body modal-body-np">
                                                                    <div class="row">
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-8">Screening?</div>
                                                                            <div class="col-md-4">
                                                                                <select name="screened" style="width: 100%;"
                                                                                    required>
                                                                                    <option value="<?= $client['screened'] ?>">
                                                                                        <?php if ($client) {
                                                                                            if ($client['screened'] == 1) {
                                                                                                echo 'Yes';
                                                                                            } else {
                                                                                                echo 'No';
                                                                                            }
                                                                                        } else {
                                                                                            echo 'Select';
                                                                                        } ?>
                                                                                    </option>
                                                                                    <option value="1">Yes</option>
                                                                                    <option value="0">No</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>

                                                                        <div class="dr"><span></span></div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <input type="hidden" name="id" value="<?= $client['id'] ?>">
                                                                    <input type="submit" name="change_screening_status"
                                                                        class="btn btn-warning" value="Save">
                                                                    <button class="btn btn-default" data-dismiss="modal"
                                                                        aria-hidden="true">Close</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal fade" id="eligibility1<?= $client['id'] ?>" tabindex="-1"
                                                    role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <form method="post">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close"
                                                                        data-dismiss="modal"><span
                                                                            aria-hidden="true">&times;</span><span
                                                                            class="sr-only">Close</span></button>
                                                                    <h4>Change Eligibility1 Status</h4>
                                                                </div>
                                                                <div class="modal-body modal-body-np">
                                                                    <div class="row">
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-8">Eligibility1?</div>
                                                                            <div class="col-md-4">
                                                                                <select name="eligibility1" style="width: 100%;"
                                                                                    required>
                                                                                    <option
                                                                                        value="<?= $client['eligibility1'] ?>">
                                                                                        <?php if ($client) {
                                                                                            if ($client['eligibility1'] == 1) {
                                                                                                echo 'Yes';
                                                                                            } else {
                                                                                                echo 'No';
                                                                                            }
                                                                                        } else {
                                                                                            echo 'Select';
                                                                                        } ?>
                                                                                    </option>
                                                                                    <option value="1">Yes</option>
                                                                                    <option value="0">No</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>

                                                                        <div class="dr"><span></span></div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <input type="hidden" name="id" value="<?= $client['id'] ?>">
                                                                    <input type="submit" name="change_eligibility1_status"
                                                                        class="btn btn-warning" value="Save">
                                                                    <button class="btn btn-default" data-dismiss="modal"
                                                                        aria-hidden="true">Close</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal fade" id="eligibility2<?= $client['id'] ?>" tabindex="-1"
                                                    role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <form method="post">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close"
                                                                        data-dismiss="modal"><span
                                                                            aria-hidden="true">&times;</span><span
                                                                            class="sr-only">Close</span></button>
                                                                    <h4>Change Eligibility2 Status</h4>
                                                                </div>
                                                                <div class="modal-body modal-body-np">
                                                                    <div class="row">
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-8">Eligibility2?</div>
                                                                            <div class="col-md-4">
                                                                                <select name="eligibility2" style="width: 100%;"
                                                                                    required>
                                                                                    <option
                                                                                        value="<?= $client['eligibility2'] ?>">
                                                                                        <?php if ($client) {
                                                                                            if ($client['eligibility2'] == 1) {
                                                                                                echo 'Yes';
                                                                                            } else {
                                                                                                echo 'No';
                                                                                            }
                                                                                        } else {
                                                                                            echo 'Select';
                                                                                        } ?>
                                                                                    </option>
                                                                                    <option value="1">Yes</option>
                                                                                    <option value="0">No</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>

                                                                        <div class="dr"><span></span></div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <input type="hidden" name="id" value="<?= $client['id'] ?>">
                                                                    <input type="submit" name="change_eligibility2_status"
                                                                        class="btn btn-warning" value="Save">
                                                                    <button class="btn btn-default" data-dismiss="modal"
                                                                        aria-hidden="true">Close</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal fade" id="eligible<?= $client['id'] ?>" tabindex="-1"
                                                    role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <form method="post">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close"
                                                                        data-dismiss="modal"><span
                                                                            aria-hidden="true">&times;</span><span
                                                                            class="sr-only">Close</span></button>
                                                                    <h4>Change Eligible Status</h4>
                                                                </div>
                                                                <div class="modal-body modal-body-np">
                                                                    <div class="row">
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-8">Eligible?</div>
                                                                            <div class="col-md-4">
                                                                                <select name="eligible" style="width: 100%;"
                                                                                    required>
                                                                                    <option value="<?= $client['eligible'] ?>">
                                                                                        <?php if ($client) {
                                                                                            if ($client['eligible'] == 1) {
                                                                                                echo 'Yes';
                                                                                            } else {
                                                                                                echo 'No';
                                                                                            }
                                                                                        } else {
                                                                                            echo 'Select';
                                                                                        } ?>
                                                                                    </option>
                                                                                    <option value="1">Yes</option>
                                                                                    <option value="0">No</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>

                                                                        <div class="dr"><span></span></div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <input type="hidden" name="id" value="<?= $client['id'] ?>">
                                                                    <input type="submit" name="change_eligible_status"
                                                                        class="btn btn-warning" value="Save">
                                                                    <button class="btn btn-default" data-dismiss="modal"
                                                                        aria-hidden="true">Close</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal fade" id="enrolled<?= $client['id'] ?>" tabindex="-1"
                                                    role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <form method="post">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close"
                                                                        data-dismiss="modal"><span
                                                                            aria-hidden="true">&times;</span><span
                                                                            class="sr-only">Close</span></button>
                                                                    <h4>Change enrolled Status</h4>
                                                                </div>
                                                                <div class="modal-body modal-body-np">
                                                                    <div class="row">
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-8">Enrolled?</div>
                                                                            <div class="col-md-4">
                                                                                <select name="enrolled" style="width: 100%;"
                                                                                    required>
                                                                                    <option value="<?= $client['enrolled'] ?>">
                                                                                        <?php if ($client) {
                                                                                            if ($client['enrolled'] == 1) {
                                                                                                echo 'Yes';
                                                                                            } else {
                                                                                                echo 'No';
                                                                                            }
                                                                                        } else {
                                                                                            echo 'Select';
                                                                                        } ?>
                                                                                    </option>
                                                                                    <option value="1">Yes</option>
                                                                                    <option value="0">No</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>

                                                                        <div class="dr"><span></span></div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <input type="hidden" name="id" value="<?= $client['id'] ?>">
                                                                    <input type="submit" name="change_enrolled_status"
                                                                        class="btn btn-warning" value="Save">
                                                                    <button class="btn btn-default" data-dismiss="modal"
                                                                        aria-hidden="true">Close</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal fade" id="asignID<?= $client['id'] ?>" tabindex="-1"
                                                    role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <form method="post">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close"
                                                                        data-dismiss="modal"><span
                                                                            aria-hidden="true">&times;</span><span
                                                                            class="sr-only">Close</span></button>
                                                                    <h4>Assign enrolled ID</h4>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <input type="hidden" name="id" value="<?= $client['id'] ?>">
                                                                    <input type="submit" name="asign_id" class="btn btn-warning"
                                                                        value="Update Stud ID">
                                                                    <button class="btn btn-default" data-dismiss="modal"
                                                                        aria-hidden="true">Close</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php $x++;
                                            } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer clearfix">
                                    <ul class="pagination pagination-sm m-0 float-right">
                                        <li class="page-item">
                                            <a class="page-link" href="info.php?id=3&status=<?= $_GET['status'] ?>site_id=<?= $_GET['site_id'] ?>&page=<?php if (($_GET['page'] - 1) > 0) {
                                                    echo $_GET['page'] - 1;
                                                } else {
                                                    echo 1;
                                                } ?>">&laquo;
                                            </a>
                                        </li>
                                        <?php for ($i = 1; $i <= $pages; $i++) { ?>
                                            <li class="page-item">
                                                <a class="page-link <?php if ($i == $_GET['page']) {
                                                    echo 'active';
                                                } ?>"
                                                    href="info.php?id=3&status=<?= $_GET['status'] ?>&site_id=<?= $_GET['site_id'] ?>&page=<?= $i ?>"><?= $i ?>
                                                </a>
                                            </li>
                                        <?php } ?>
                                        <li class="page-item">
                                            <a class="page-link" href="info.php?id=3&status=<?= $_GET['status'] ?>&site_id=<?= $_GET['site_id'] ?>&page=<?php if (($_GET['page'] + 1) <= $pages) {
                                                    echo $_GET['page'] + 1;
                                                } else {
                                                    echo $i - 1;
                                                } ?>">&raquo;
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- /.row -->
                    </div>
                    <!-- /.container-fluid -->
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
                                <h1>Participant Schedules</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="index1.php">Home</a></li>
                                    <li class="breadcrumb-item active">Participant Schedules</li>
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
                                        $patient = $override->get('clients', 'id', $_GET['cid'])[0];
                                        $visits_status = $override->firstRow1('visit', 'status', 'id', 'client_id', $_GET['cid'], 'visit_code', 'EV')[0]['status'];

                                        $required_visit = $override->countData1('visit', 'status', 1, 'client_id', $_GET['cid'], 'seq_no', $_GET['seq']);


                                        $category = $override->get('main_diagnosis', 'patient_id', $_GET['cid'])[0];
                                        $cat = '';

                                        if ($category['cardiac'] == 1) {
                                            $cat = 'Cardiac';
                                        } elseif ($category['diabetes'] == 1) {
                                            $cat = 'Diabetes';
                                        } elseif ($category['sickle_cell'] == 1) {
                                            $cat = 'Sickle cell';
                                        } else {
                                            $cat = 'Not Diagnosed';
                                        }


                                        if ($patient['gender'] == 1) {
                                            $gender = 'Male';
                                        } elseif ($patient['gender'] == 2) {
                                            $gender = 'Female';
                                        }

                                        $name = 'Name: ' . $patient['firstname'] . ' ' . $patient['middlename'] . ' ' . $patient['lastname'];

                                        ?>


                                        <div class="row mb-2">
                                            <div class="col-sm-6">
                                                <h1>Study ID: <?= $patient['study_id'] ?></h1>
                                                <?php if ($user->data()->accessLevel = 1 || $user->data()->accessLevel = 2) { ?>
                                                    <h4>Name: <?= $name ?></h4>
                                                <?php } ?>
                                                <h4>Age: <?= $patient['age'] ?></h4>
                                                <h4>Gender: <?= $gender ?></h4>
                                                <h4>Category: <?= $cat ?></h4>
                                            </div>
                                            <div class="col-sm-6">
                                                <ol class="breadcrumb float-sm-right">
                                                    <li class="breadcrumb-item"><a
                                                            href="info.php?id=3&status=<?= $_GET['status'] ?>">
                                                            < Back</a>
                                                    </li>
                                                    <li class="breadcrumb-item"><a href="#">
                                                            <?php if ($visit['seq_no'] >= 1) {
                                                                $summary = '';
                                                                ?>
                                                                <?php
                                                                //  if ($visit['visit_status']) {
                                                                ?>
                                                                <a href="#addSchedule<?= $visit['id'] ?>" role="button"
                                                                    class="btn btn-info" data-toggle="modal">Update</a>
                                                            <?php } else {
                                                                $summary = 1;
                                                                ?>
                                                                <a href="index1.php">Go Home</a>
                                                                <?php
                                                                //  }
                                                            } ?>
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
                                                    <th>Status</th>
                                                    <th>Action ( Completion )</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $x = 1;
                                                foreach ($override->getDataAsc('visit', 'client_id', $_GET['cid'], 'id') as $visit) {
                                                    $clnt = $override->get('clients', 'id', $_GET['cid'])[0];
                                                    $cntV = $override->getCount('visit', 'client_id', $visit['client_id']);

                                                    $demographic = $override->get3('demographic', 'patient_id', $_GET['cid'], 'seq_no', $visit['seq_no'], 'visit_code', $visit['visit_code']);
                                                    $vital = $override->get3('vital', 'patient_id', $_GET['cid'], 'seq_no', $visit['seq_no'], 'visit_code', $visit['visit_code']);
                                                    $history = $override->get3('history', 'patient_id', $_GET['cid'], 'seq_no', $visit['seq_no'], 'visit_code', $visit['visit_code']);
                                                    $symptoms = $override->get3('symptoms', 'patient_id', $_GET['cid'], 'seq_no', $visit['seq_no'], 'visit_code', $visit['visit_code']);
                                                    $diagnosis = $override->get3('main_diagnosis', 'patient_id', $_GET['cid'], 'seq_no', $visit['seq_no'], 'visit_code', $visit['visit_code']);
                                                    $results = $override->get3('results', 'patient_id', $_GET['cid'], 'seq_no', $visit['seq_no'], 'visit_code', $visit['visit_code']);
                                                    $hospitalization = $override->get3('hospitalization', 'patient_id', $_GET['cid'], 'seq_no', $visit['seq_no'], 'visit_code', $visit['visit_code']);
                                                    $treatment_plan = $override->get3('treatment_plan', 'patient_id', $_GET['cid'], 'seq_no', $visit['seq_no'], 'visit_code', $visit['visit_code']);
                                                    $dgns_complctns_comorbdts = $override->get3('dgns_complctns_comorbdts', 'patient_id', $_GET['cid'], 'seq_no', $visit['seq_no'], 'visit_code', $visit['visit_code']);
                                                    $risks = $override->get3('risks', 'patient_id', $_GET['cid'], 'seq_no', $visit['seq_no'], 'visit_code', $visit['visit_code']);
                                                    $hospitalization_details = $override->get3('hospitalization_details', 'patient_id', $_GET['cid'], 'seq_no', $visit['seq_no'], 'visit_code', $visit['visit_code']);
                                                    $lab_details = $override->get3('lab_details', 'patient_id', $_GET['cid'], 'seq_no', $visit['seq_no'], 'visit_code', $visit['visit_code']);
                                                    $summary = $override->get3('summary', 'patient_id', $_GET['cid'], 'seq_no', $visit['seq_no'], 'visit_code', $visit['visit_code']);
                                                    $social_economic = $override->get3('social_economic', 'patient_id', $_GET['cid'], 'seq_no', $visit['seq_no'], 'visit_code', $visit['visit_code']);



                                                    $category = 0;

                                                    if ($diagnosis[0]['cardiac'] == 1) {
                                                        $category = $override->countData1('cardiac', 'patient_id', $visit['client_id'], 'status', 1, 'seq_no', $visit['seq_no']);
                                                    } elseif ($diagnosis[0]['diabetes'] == 1) {
                                                        $category = $override->countData('diabetic', 'patient_id', $visit['client_id'], 'status', 1, 'seq_no', $visit['seq_no']);
                                                    } elseif ($diagnosis[0]['sickle_cell'] == 1) {
                                                        $category = $override->countData('sickle_cell', 'patient_id', $visit['client_id'], 'status', 1, 'seq_no', $visit['seq_no']);
                                                    } else {
                                                        $category = 0;
                                                    }


                                                    $demographic1 = $override->countData1('demographic', 'patient_id', $visit['client_id'], 'status', 1, 'seq_no', $visit['seq_no']);
                                                    $vital1 = $override->countData1('vital', 'patient_id', $visit['client_id'], 'status', 1, 'seq_no', $visit['seq_no']);
                                                    $history1 = $override->countData1('history', 'patient_id', $visit['client_id'], 'status', 1, 'seq_no', $visit['seq_no']);
                                                    $symptoms1 = $override->countData1('symptoms', 'patient_id', $visit['client_id'], 'status', 1, 'seq_no', $visit['seq_no']);
                                                    $diagnosis1 = $override->countData1('main_diagnosis', 'patient_id', $visit['client_id'], 'status', 1, 'seq_no', $visit['seq_no']);
                                                    $results1 = $override->countData1('results', 'patient_id', $visit['client_id'], 'status', 1, 'seq_no', $visit['seq_no']);
                                                    $hospitalization1 = $override->countData1('hospitalization', 'patient_id', $visit['client_id'], 'status', 1, 'seq_no', $visit['seq_no']);
                                                    $treatment_plan1 = $override->countData1('treatment_plan', 'patient_id', $visit['client_id'], 'status', 1, 'seq_no', $visit['seq_no']);
                                                    $dgns_complctns_comorbdts1 = $override->countData1('dgns_complctns_comorbdts', 'patient_id', $visit['client_id'], 'status', 1, 'seq_no', $visit['seq_no']);
                                                    $risks1 = $override->countData1('risks', 'patient_id', $visit['client_id'], 'status', 1, 'seq_no', $visit['seq_no']);
                                                    $hospitalization_details1 = $override->countData1('hospitalization_details', 'patient_id', $visit['client_id'], 'status', 1, 'seq_no', $visit['seq_no']);
                                                    $lab_details1 = $override->countData1('lab_details', 'patient_id', $visit['client_id'], 'status', 1, 'seq_no', $visit['seq_no']);
                                                    $summary1 = $override->countData1('summary', 'patient_id', $visit['client_id'], 'status', 1, 'seq_no', $visit['seq_no']);
                                                    $social_economic1 = $override->countData1('social_economic', 'patient_id', $visit['client_id'], 'status', 1, 'seq_no', $visit['seq_no']);


                                                    $total_required = 0;

                                                    if ($visit['seq_no'] == 1) {
                                                        $total_required = 15;
                                                        if ($visit['visit_status'] == 1 && $visit['expected_date'] <= date('Y-m-d')) {
                                                            $total_available = intval($category) + intval($demographic1) + intval($vital1) + intval($history1) + intval($symptoms1) + intval($diagnosis1) + intval($results1) + intval($hospitalization1)
                                                                + intval($treatment_plan1) + intval($dgns_complctns_comorbdts1) + intval($risks1) + intval($hospitalization_details1) + intval($lab_details1)
                                                                + intval($summary1) + intval($social_economic1);
                                                        } elseif ($visit['visit_status'] == 0 && $visit['expected_date'] <= date('Y-m-d')) {
                                                            $total_available = intval($category) + intval($demographic1) + intval($vital1) + intval($history1) + intval($symptoms1) + intval($diagnosis1) + intval($results1) + intval($hospitalization1)
                                                                + intval($treatment_plan1) + intval($dgns_complctns_comorbdts1) + intval($risks1) + intval($hospitalization_details1) + intval($lab_details1)
                                                                + intval($summary1) + intval($social_economic1);
                                                        } elseif ($visit['visit_status'] == 1 && $visit['expected_date'] > date('Y-m-d')) {
                                                            $total_available = intval($category) + intval($demographic1) + intval($vital1) + intval($history1) + intval($symptoms1) + intval($diagnosis1) + intval($results1) + intval($hospitalization1)
                                                                + intval($treatment_plan1) + intval($dgns_complctns_comorbdts1) + intval($risks1) + intval($hospitalization_details1) + intval($lab_details1)
                                                                + intval($summary1) + intval($social_economic1);
                                                        } elseif ($visit['visit_status'] == 2) {
                                                            $total_available = intval($summary1);
                                                            $total_required = 1;
                                                        } elseif ($visit['visit_status'] == 0 && $visit['expected_date'] > date('Y-m-d')) {
                                                            $total_available = 0;
                                                            $total_required = 0;
                                                        }

                                                        $progress = intval((intval($total_available) / $total_required) * 100);
                                                    } elseif ($visit['seq_no'] > 1) {
                                                        $total_required = 10;
                                                        if ($visit['visit_status'] == 1 && $visit['expected_date'] <= date('Y-m-d')) {
                                                            $total_available = intval($vital1) + intval($symptoms1) + intval($results1) + intval($hospitalization1)
                                                                + intval($treatment_plan1) + intval($dgns_complctns_comorbdts1) + intval($risks1) + intval($hospitalization_details1) + intval($lab_details1)
                                                                + intval($summary1);
                                                        } elseif ($visit['visit_status'] == 0 && $visit['expected_date'] <= date('Y-m-d')) {
                                                            $total_available = intval($vital1) + intval($symptoms1) + intval($results1) + intval($hospitalization1)
                                                                + intval($treatment_plan1) + intval($dgns_complctns_comorbdts1) + intval($risks1) + intval($hospitalization_details1) + intval($lab_details1)
                                                                + intval($summary1);
                                                        } elseif ($visit['visit_status'] == 1 && $visit['expected_date'] > date('Y-m-d')) {
                                                            $total_available = intval($vital1) + intval($symptoms1) + intval($results1) + intval($hospitalization1)
                                                                + intval($treatment_plan1) + intval($dgns_complctns_comorbdts1) + intval($risks1) + intval($hospitalization_details1) + intval($lab_details1)
                                                                + intval($summary1);
                                                        } elseif ($visit['visit_status'] == 2) {
                                                            $total_available = intval($summary1);
                                                            $total_required = 1;
                                                        } elseif ($visit['visit_status'] == 0 && $visit['expected_date'] > date('Y-m-d')) {
                                                            $total_available = 0;
                                                            $total_required = 0;
                                                        }
                                                        $progress = intval((intval($total_available) / $total_required) * 100);
                                                    }


                                                    if ($visit['status'] == 0) {
                                                        $btnV = 'Add';
                                                    } elseif ($visit['status'] == 1) {
                                                        $btnV = 'Edit';
                                                    }

                                                    $visit_name = $visit['visit_name'];

                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <?= $visit['visit_day'] ?><br>
                                                            <?php if ($visit['seq_no'] == -1) {
                                                                echo '( Registration )';
                                                            } elseif ($visit['seq_no'] == 0) {
                                                                echo '( Screening )';
                                                            } elseif ($visit['seq_no'] == 1) {
                                                                echo '( Enrollment )';
                                                            } elseif ($visit['seq_no'] > 1) {
                                                                echo '( Follow Up )';
                                                            } ?>
                                                        </td>
                                                        <td> <?= $visit['expected_date'] ?></td>
                                                        <td> <?= $visit['visit_date'] ?> </td>
                                                        <td>
                                                            <?php if ($visit['visit_status'] == 1) { ?>
                                                                <a href="#AddVisit<?= $visit['id'] ?>" role="button"
                                                                    class="btn btn-success" data-toggle="modal">Done</a>
                                                            <?php } elseif ($visit['visit_status'] == 0) { ?>
                                                                <a href="#AddVisit<?= $visit['id'] ?>" role="button"
                                                                    class="btn btn-warning" data-toggle="modal">Pending</a>
                                                            <?php } elseif ($visit['visit_status'] == 2) { ?>
                                                                <a href="#AddVisit<?= $visit['id'] ?>" role="button"
                                                                    class="btn btn-danger" data-toggle="modal">Missed</a>
                                                            <?php } else { ?>
                                                                <a href="#AddVisit<?= $visit['id'] ?>" role="button"
                                                                    class="btn btn-warning" data-toggle="modal">Pending</a>
                                                            <?php } ?>
                                                        </td>

                                                        <td>
                                                            <?php if ($visit['visit_code'] == 'EV') { ?>

                                                                <?php if (($visit['visit_status'] == 1 || $visit['visit_status'] == 2) && ($visit['visit_code'] == 'EV' || $visit['visit_code'] == 'FV' || $visit['visit_code'] == 'TV' || $visit['visit_code'] == 'UV')) { ?>

                                                                    <?php if ($demographic && $vital && $history && $symptoms && $diagnosis && $results && $hospitalization && $treatment_plan && $dgns_complctns_comorbdts && $risks && $hospitalization_details && $lab_details && $social_economic && $summary) { ?>
                                                                        <?php if ($user->data()->accessLevel == 1 || $user->data()->accessLevel == 2) { ?>
                                                                            <a href="info.php?id=7&cid=<?= $_GET['cid'] ?>&vid=<?= $visit['id'] ?>&vcode=<?= $visit['visit_code'] ?>&seq=<?= $visit['seq_no'] ?>&sid=<?= $visit['study_id'] ?>&vday=<?= $visit['visit_day'] ?>&status=<?= $_GET['status'] ?>"
                                                                                role=" button" class="btn btn-info"> Edit Study Forms </a>
                                                                        <?php } else { ?>
                                                                            <a href="info.php?id=7&cid=<?= $_GET['cid'] ?>&vid=<?= $visit['id'] ?>&vcode=<?= $visit['visit_code'] ?>&seq=<?= $visit['seq_no'] ?>&sid=<?= $visit['study_id'] ?>&vday=<?= $visit['visit_day'] ?>&status=<?= $_GET['status'] ?>"
                                                                                role=" button" class="btn btn-info"> View Study Forms </a>
                                                                        <?php } ?>
                                                                        <?php if ($user->data()->power == 1 || $user->data()->accessLevel == 1) { ?>
                                                                            <hr>
                                                                            <?php if ($progress == 100) { ?>
                                                                                <span class="badge badge-primary right">
                                                                                    <?= $total_available ?> out of <?= $total_required ?>
                                                                                </span>
                                                                                <span class="badge badge-primary right">
                                                                                    <?= $progress ?>%
                                                                                </span>
                                                                            <?php } elseif ($progress > 100) { ?>
                                                                                <span class="badge badge-warning right">
                                                                                    <?= $total_available ?> out of <?= $total_required ?>
                                                                                </span>
                                                                                <span class="badge badge-warning right">
                                                                                    <?= $progress ?>%
                                                                                </span>
                                                                            <?php } elseif ($progress >= 80 && $progress < 100) { ?>
                                                                                <span class="badge badge-info right">
                                                                                    <?= $total_available ?> out of <?= $total_required ?>
                                                                                </span>
                                                                                <span class="badge badge-info right">
                                                                                    <?= $progress ?>%
                                                                                </span>
                                                                            <?php } elseif ($progress >= 50 && $progress < 80) { ?>
                                                                                <span class="badge badge-secondary right">
                                                                                    <?= $total_available ?> out of <?= $total_required ?>
                                                                                </span>
                                                                                <span class="badge badge-secondary right">
                                                                                    <?= $progress ?>%
                                                                                </span>
                                                                            <?php } elseif ($progress < 50) { ?>
                                                                                <span class="badge badge-danger right">
                                                                                    <?= $total_available ?> out of <?= $total_required ?>
                                                                                </span>
                                                                                <span class="badge badge-danger right">
                                                                                    <?= $progress ?>%
                                                                                </span>
                                                                            <?php } ?>
                                                                        <?php }
                                                                        ?>

                                                                    <?php } else { ?>
                                                                        <?php if ($user->data()->accessLevel == 1 || $user->data()->accessLevel == 2) { ?>
                                                                            <a href="info.php?id=7&cid=<?= $_GET['cid'] ?>&vid=<?= $visit['id'] ?>&vcode=<?= $visit['visit_code'] ?>&seq=<?= $visit['seq_no'] ?>&sid=<?= $visit['study_id'] ?>&vday=<?= $visit['visit_day'] ?>&status=<?= $_GET['status'] ?>"
                                                                                role=" button" class="btn btn-warning"> Fill Study Forms </a>
                                                                        <?php } else { ?>
                                                                            <a href="info.php?id=7&cid=<?= $_GET['cid'] ?>&vid=<?= $visit['id'] ?>&vcode=<?= $visit['visit_code'] ?>&seq=<?= $visit['seq_no'] ?>&sid=<?= $visit['study_id'] ?>&vday=<?= $visit['visit_day'] ?>&status=<?= $_GET['status'] ?>"
                                                                                role=" button" class="btn btn-warning"> View Study Forms </a>
                                                                        <?php } ?>
                                                                        <?php if ($user->data()->power == 1 || $user->data()->accessLevel == 1) { ?>
                                                                            <hr>
                                                                            <?php if ($progress == 100) { ?>
                                                                                <span class="badge badge-primary right">
                                                                                    <?= $total_available ?> out of <?= $total_required ?>
                                                                                </span>
                                                                                <span class="badge badge-primary right">
                                                                                    <?= $progress ?>%
                                                                                </span>
                                                                            <?php } elseif ($progress > 100) { ?>
                                                                                <span class="badge badge-warning right">
                                                                                    <?= $total_available ?> out of <?= $total_required ?>
                                                                                </span>
                                                                                <span class="badge badge-warning right">
                                                                                    <?= $progress ?>%
                                                                                </span>
                                                                            <?php } elseif ($progress >= 80 && $progress < 100) { ?>
                                                                                <span class="badge badge-info right">
                                                                                    <?= $total_available ?> out of <?= $total_required ?>
                                                                                </span>
                                                                                <span class="badge badge-info right">
                                                                                    <?= $progress ?>%
                                                                                </span>
                                                                            <?php } elseif ($progress >= 50 && $progress < 80) { ?>
                                                                                <span class="badge badge-secondary right">
                                                                                    <?= $total_available ?> out of <?= $total_required ?>
                                                                                </span>
                                                                                <span class="badge badge-secondary right">
                                                                                    <?= $progress ?>%
                                                                                </span>
                                                                            <?php } elseif ($progress < 50) { ?>
                                                                                <span class="badge badge-danger right">
                                                                                    <?= $total_available ?> out of <?= $total_required ?>
                                                                                </span>
                                                                                <span class="badge badge-danger right">
                                                                                    <?= $progress ?>%
                                                                                </span>
                                                                            <?php } ?>
                                                                        <?php }
                                                                        ?>
                                                                    <?php }
                                                                }


                                                                if ($user->data()->power == 1 || $user->data()->accessLevel == 1) { ?>
                                                                    <hr>
                                                                    <a href="#updateVisit<?= $visit['id'] ?>" role="button"
                                                                        class="btn btn-info" data-toggle="modal">Update Expected
                                                                        Date</a>
                                                                    <?php if ($user->data()->power == 1) { ?>
                                                                        <hr>
                                                                        <a href="#deleteVisit<?= $visit['id'] ?>" role="button"
                                                                            class="btn btn-danger" data-toggle="modal">Delete Visit</a>
                                                                        <hr>
                                                                    <?php } ?>
                                                                <?php }
                                                            } ?>


                                                            <?php if (($visit['visit_code'] == 'FV' || $visit['visit_code'] == 'TV' || $visit['visit_code'] == 'UV')) { ?>

                                                                <?php if (($visit['visit_status'] == 1 || $visit['visit_status'] == 2) && ($visit['visit_code'] == 'EV' || $visit['visit_code'] == 'FV' || $visit['visit_code'] == 'TV' || $visit['visit_code'] == 'UV')) { ?>

                                                                    <?php if ($vital && $symptoms && $results && $hospitalization && $treatment_plan && $dgns_complctns_comorbdts && $risks && $hospitalization_details && $lab_details && $summary) { ?>
                                                                        <?php if ($user->data()->accessLevel == 1 || $user->data()->accessLevel == 2) { ?>
                                                                            <a href="info.php?id=7&cid=<?= $_GET['cid'] ?>&vid=<?= $visit['id'] ?>&vcode=<?= $visit['visit_code'] ?>&seq=<?= $visit['seq_no'] ?>&sid=<?= $visit['study_id'] ?>&vday=<?= $visit['visit_day'] ?>&status=<?= $_GET['status'] ?>"
                                                                                role="button" class="btn btn-info"> Edit Study Forms </a>
                                                                        <?php } else { ?>
                                                                            <a href="info.php?id=7&cid=<?= $_GET['cid'] ?>&vid=<?= $visit['id'] ?>&vcode=<?= $visit['visit_code'] ?>&seq=<?= $visit['seq_no'] ?>&sid=<?= $visit['study_id'] ?>&vday=<?= $visit['visit_day'] ?>&status=<?= $_GET['status'] ?>"
                                                                                role="button" class="btn btn-info"> View Study Forms </a>
                                                                        <?php } ?>
                                                                        <?php if ($user->data()->power == 1 || $user->data()->accessLevel == 1) { ?>
                                                                            <hr>
                                                                            <?php if ($progress == 100) { ?>
                                                                                <span class="badge badge-primary right">
                                                                                    <?= $total_available ?> out of <?= $total_required ?>
                                                                                </span>
                                                                                <span class="badge badge-primary right">
                                                                                    <?= $progress ?>%
                                                                                </span>
                                                                            <?php } elseif ($progress > 100) { ?>
                                                                                <span class="badge badge-warning right">
                                                                                    <?= $total_available ?> out of <?= $total_required ?>
                                                                                </span>
                                                                                <span class="badge badge-warning right">
                                                                                    <?= $progress ?>%
                                                                                </span>
                                                                            <?php } elseif ($progress >= 80 && $progress < 100) { ?>
                                                                                <span class="badge badge-info right">
                                                                                    <?= $total_available ?> out of <?= $total_required ?>
                                                                                </span>
                                                                                <span class="badge badge-info right">
                                                                                    <?= $progress ?>%
                                                                                </span>
                                                                            <?php } elseif ($progress >= 50 && $progress < 80) { ?>
                                                                                <span class="badge badge-secondary right">
                                                                                    <?= $total_available ?> out of <?= $total_required ?>
                                                                                </span>
                                                                                <span class="badge badge-secondary right">
                                                                                    <?= $progress ?>%
                                                                                </span>
                                                                            <?php } elseif ($progress < 50) { ?>
                                                                                <span class="badge badge-danger right">
                                                                                    <?= $total_available ?> out of <?= $total_required ?>
                                                                                </span>
                                                                                <span class="badge badge-danger right">
                                                                                    <?= $progress ?>%
                                                                                </span>
                                                                            <?php } ?>
                                                                        <?php }
                                                                        ?>

                                                                    <?php } else { ?>
                                                                        <?php if ($user->data()->accessLevel == 1 || $user->data()->accessLevel == 2) { ?>
                                                                            <a href="info.php?id=7&cid=<?= $_GET['cid'] ?>&vid=<?= $visit['id'] ?>&vcode=<?= $visit['visit_code'] ?>&seq=<?= $visit['seq_no'] ?>&sid=<?= $visit['study_id'] ?>&vday=<?= $visit['visit_day'] ?>&status=<?= $_GET['status'] ?>"
                                                                                role="button" class="btn btn-warning"> Fill Study Forms </a>
                                                                        <?php } else { ?>
                                                                            <a href="info.php?id=7&cid=<?= $_GET['cid'] ?>&vid=<?= $visit['id'] ?>&vcode=<?= $visit['visit_code'] ?>&seq=<?= $visit['seq_no'] ?>&sid=<?= $visit['study_id'] ?>&vday=<?= $visit['visit_day'] ?>&status=<?= $_GET['status'] ?>"
                                                                                role="button" class="btn btn-warning"> View Study Forms </a>
                                                                        <?php } ?>
                                                                        <?php if ($user->data()->power == 1 || $user->data()->accessLevel == 1) { ?>
                                                                            <hr>
                                                                            <?php if ($progress == 100) { ?>
                                                                                <span class="badge badge-primary right">
                                                                                    <?= $total_available ?> out of <?= $total_required ?>
                                                                                </span>
                                                                                <span class="badge badge-primary right">
                                                                                    <?= $progress ?>%
                                                                                </span>
                                                                            <?php } elseif ($progress > 100) { ?>
                                                                                <span class="badge badge-warning right">
                                                                                    <?= $total_available ?> out of <?= $total_required ?>
                                                                                </span>
                                                                                <span class="badge badge-warning right">
                                                                                    <?= $progress ?>%
                                                                                </span>
                                                                            <?php } elseif ($progress >= 80 && $progress < 100) { ?>
                                                                                <span class="badge badge-info right">
                                                                                    <?= $total_available ?> out of <?= $total_required ?>
                                                                                </span>
                                                                                <span class="badge badge-info right">
                                                                                    <?= $progress ?>%
                                                                                </span>
                                                                            <?php } elseif ($progress >= 50 && $progress < 80) { ?>
                                                                                <span class="badge badge-secondary right">
                                                                                    <?= $total_available ?> out of <?= $total_required ?>
                                                                                </span>
                                                                                <span class="badge badge-secondary right">
                                                                                    <?= $progress ?>%
                                                                                </span>
                                                                            <?php } elseif ($progress < 50) { ?>
                                                                                <span class="badge badge-danger right">
                                                                                    <?= $total_available ?> out of <?= $total_required ?>
                                                                                </span>
                                                                                <span class="badge badge-danger right">
                                                                                    <?= $progress ?>%
                                                                                </span>
                                                                            <?php } ?>
                                                                        <?php }
                                                                        ?>
                                                                    <?php }
                                                                }
                                                                if ($user->data()->power == 1 || $user->data()->accessLevel == 1) { ?>
                                                                    <hr>
                                                                    <a href="#updateVisit<?= $visit['id'] ?>" role="button"
                                                                        class="btn btn-info" data-toggle="modal">Update Expected
                                                                        Date</a>
                                                                    <?php if ($user->data()->power == 1) { ?>
                                                                        <hr>

                                                                        <a href="#deleteVisit<?= $visit['id'] ?>" role="button"
                                                                            class="btn btn-danger" data-toggle="modal">Delete Visit</a>
                                                                        <?php
                                                                    }
                                                                }
                                                            } ?>
                                                        </td>
                                                    </tr>

                                                    <div class="modal fade" id="AddVisit<?= $visit['id'] ?>">
                                                        <div class="modal-dialog">
                                                            <form method="post">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h4 class="modal-title">Add Visit Details</h4>
                                                                        <button type="button" class="close" data-dismiss="modal"
                                                                            aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                        <?php $screening = $override->get('screening', 'patient_id', $client['id'])[0]; ?>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="row">
                                                                            <div class="col-sm-6">
                                                                                <div class="form-group">
                                                                                    <label>Visit Date</label>
                                                                                    <input class="form-control"
                                                                                        max="<?= date('Y-m-d'); ?>" type="date"
                                                                                        name="visit_date" id="visit_date"
                                                                                        style="width: 100%;" value="<?php if ($visit['visit_date']) {
                                                                                            print_r($visit['visit_date']);
                                                                                        } ?>" required />
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-6">
                                                                                <div class="form-group">
                                                                                    <label>Status</label>
                                                                                    <select id="visit_status"
                                                                                        name="visit_status" class="form-control"
                                                                                        required>
                                                                                        <option value="<?= $visit['id'] ?>">
                                                                                            <?php if ($visit['visit_status']) {
                                                                                                if ($visit['visit_status'] == 1) {
                                                                                                    echo 'Attended';
                                                                                                } else if ($visit['visit_status'] == 2) {
                                                                                                    echo 'Missed';
                                                                                                } else if ($visit['visit_status'] == 0) {
                                                                                                    echo 'Pending';
                                                                                                }
                                                                                            } else {
                                                                                                echo 'Select';
                                                                                            } ?>
                                                                                        </option>
                                                                                        <option value="1">Attended</option>
                                                                                        <option value="2">Missed</option>
                                                                                        <option value="0">Pending</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <hr>
                                                                        <div class="row">
                                                                            <div class="col-sm-12">
                                                                                <div class="form-group">
                                                                                    <label>Comments / Remarks:</label>
                                                                                    <textarea class="form-control"
                                                                                        name="reasons" rows="3"
                                                                                        placeholder="Type reason / comments here..."
                                                                                        required><?php if ($visit['reasons']) {
                                                                                            print_r($visit['reasons']);
                                                                                        } ?></textarea>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer justify-content-between">
                                                                        <input type="hidden" name="id"
                                                                            value="<?= $visit['id'] ?>">
                                                                        <button type="button" class="btn btn-default"
                                                                            data-dismiss="modal">Close</button>
                                                                        <?php if ($user->data()->accessLevel == 1 || $user->data()->accessLevel == 2) { ?>
                                                                            <input type="submit" name="add_visit"
                                                                                class="btn btn-primary" value="Submit">
                                                                        <?php } ?>
                                                                    </div>
                                                                </div>
                                                                <!-- /.modal-content -->
                                                            </form>
                                                        </div>
                                                        <!-- /.modal-dialog -->
                                                    </div>
                                                    <!-- /.modal -->

                                                    <div class="modal fade" id="updateVisit<?= $visit['id'] ?>">
                                                        <div class="modal-dialog">
                                                            <form method="post">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h4 class="modal-title">Visit Details</h4>
                                                                        <button type="button" class="close" data-dismiss="modal"
                                                                            aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <?php $screening = $override->get('screening', 'patient_id', $client['id'])[0];
                                                                    ?>
                                                                    <div class="modal-body">
                                                                        <div class="row">
                                                                            <div class="col-sm-12">
                                                                                <div class="row-form clearfix">
                                                                                    <!-- select -->
                                                                                    <div class="form-group">
                                                                                        <label>Expected Date</label>
                                                                                        <input class="form-control" type="date"
                                                                                            name="expected_date"
                                                                                            id="expected_date"
                                                                                            style="width: 100%;" value="<?php if ($visit['expected_date']) {
                                                                                                print_r($visit['expected_date']);
                                                                                            } ?>" required />
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="dr"><span></span></div>
                                                                    </div>
                                                                    <div class="modal-footer justify-content-between">
                                                                        <input type="hidden" name="id"
                                                                            value="<?= $visit['id'] ?>">
                                                                        <input type="hidden" name="summary_id"
                                                                            value="<?= $visit['summary_id'] ?>">
                                                                        <button type="button" class="btn btn-default"
                                                                            data-dismiss="modal">Close</button>
                                                                        <input type="submit" name="update_visit"
                                                                            class="btn btn-primary" value="Save changes">
                                                                    </div>
                                                                </div>
                                                                <!-- /.modal-content -->
                                                            </form>
                                                        </div>
                                                        <!-- /.modal-dialog -->
                                                    </div>
                                                    <!-- /.modal -->

                                                    <div class="modal fade" id="deleteVisit<?= $visit['id'] ?>" tabindex="-1"
                                                        role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <form method="post">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <button type="button" class="close"
                                                                            data-dismiss="modal"><span
                                                                                aria-hidden="true">&times;</span><span
                                                                                class="sr-only">Close</span></button>
                                                                        <h4>Delete Visit</h4>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <strong style="font-weight: bold;color: red">
                                                                            <p>Are you sure you want to delete this Visit ?</p>
                                                                        </strong>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <input type="hidden" name="id"
                                                                            value="<?= $visit['id'] ?>">
                                                                        <input type="submit" name="delete_visit" value="Delete"
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
                                                    <th>Visit Day</th>
                                                    <th>Expected Date</th>
                                                    <th>Visit Date</th>
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
        <?php } elseif ($_GET['id'] == 5) { ?>
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
                                <h1>STUDY ID FORM ( SET STUDY ID )</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="index1.php">Home</a></li>
                                    <li class="breadcrumb-item active">STUDY ID FORM ( SET STUDY ID )</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <?php if ($user->data()->power == 1) { ?>
                                <!-- left column -->
                                <div class="col-md-4">
                                    <!-- general form elements disabled -->
                                    <div class="card card-warning">
                                        <div class="card-header">
                                            <h3 class="card-title">Add STUDY ID </h3>
                                        </div>
                                        <!-- /.card-header -->
                                        <form id="validation" enctype="multipart/form-data" method="post" autocomplete="off">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label for="forms">FULL NAME ( STUDY ID )</label>
                                                                <select name="client_id" class="form-control"
                                                                    style="width: 100%;" required>
                                                                    <option value="">Select Name</option>
                                                                    <?php foreach ($override->get('clients', 'status', 1) as $client) { ?>
                                                                        <option value="<?= $client['id'] ?>">
                                                                            <?= $client['id'] . ' - ( ' . $client['study_id'] . ' - ' . $client['firstname'] . ' - ' . $client['middelname'] . ' - ' . $client['lastname'] . ' ) ' ?>
                                                                        </option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /.card-body -->
                                            <div class="card-footer">
                                                <a href='index1.php' class="btn btn-default">Back</a>
                                                <input type="hidden" name="study_id" value="<?= $client['id']; ?>">
                                                <input type="submit" name="set_study_id" value="Submit" class="btn btn-primary">
                                            </div>
                                        </form>
                                    </div>
                                    <!-- /.card -->
                                </div>
                                <!--/.col (left) -->
                            <?php } ?>

                            <!-- Center column -->
                            <div class="col-md-4">
                                <!-- general form elements disabled -->
                                <div class="card card-warning">
                                    <div class="card-header">
                                        <h3 class="card-title">Update STUDY ID </h3>
                                    </div>
                                    <!-- /.card-header -->
                                    <form id="validation" enctype="multipart/form-data" method="post" autocomplete="off">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="mb-3">
                                                        <label for="table_name" class="form-label">TABLE NAME</label>
                                                        <select name="table_name" id="table_name" class="form-control"
                                                            required>
                                                            <option value="">Select Table</option>
                                                            <?php $x = 1;
                                                            foreach ($AllTables as $tables) {
                                                                if (
                                                                    $tables['Tables_in_penplus'] == 'clients' || $tables['Tables_in_penplus'] == 'screening' ||
                                                                    $tables['Tables_in_penplus'] == 'demographic' || $tables['Tables_in_penplus'] == 'vitals' ||
                                                                    $tables['Tables_in_penplus'] == 'main_diagnosis' || $tables['Tables_in_penplus'] == 'history' ||
                                                                    $tables['Tables_in_penplus'] == 'symptoms' || $tables['Tables_in_penplus'] == 'cardiac' ||
                                                                    $tables['Tables_in_penplus'] == 'diabetic' || $tables['Tables_in_penplus'] == 'sickle_cell' ||
                                                                    $tables['Tables_in_penplus'] == 'results' || $tables['Tables_in_penplus'] == 'cardiac' ||
                                                                    $tables['Tables_in_penplus'] == 'hospitalization' || $tables['Tables_in_penplus'] == 'hospitalization_details' ||
                                                                    $tables['Tables_in_penplus'] == 'treatment_plan' || $tables['Tables_in_penplus'] == 'dgns_complctns_comorbdts' ||
                                                                    $tables['Tables_in_penplus'] == 'risks' || $tables['Tables_in_penplus'] == 'lab_details' ||
                                                                    $tables['Tables_in_penplus'] == 'social_economic' || $tables['Tables_in_penplus'] == 'summary' ||
                                                                    $tables['Tables_in_penplus'] == 'medication_treatments' || $tables['Tables_in_penplus'] == 'hospitalization_detail_id' ||
                                                                    $tables['Tables_in_penplus'] == 'sickle_cell_status_table' || $tables['Tables_in_penplus'] == 'visit' ||
                                                                    $tables['Tables_in_penplus'] == 'lab_requests'
                                                                ) { ?>
                                                                    <option value="<?= $tables['Tables_in_penplus'] ?>">
                                                                        <?= $x . ' - ' . $tables['Tables_in_penplus'] ?>
                                                                    </option>
                                                                <?php }
                                                                $x++;
                                                            } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="row-form clearfix">
                                                        <div class="form-group">
                                                            <label for="forms">FULL NAME ( STUDY ID )</label>
                                                            <select name="client_id" class="form-control"
                                                                style="width: 100%;" required>
                                                                <option value="">Select Name</option>
                                                                <?php foreach ($override->get('clients', 'status', 1) as $client) { ?>
                                                                    <option value="<?= $client['id'] ?>">
                                                                        <?= $client['id'] . ' - ( ' . $client['study_id'] . ' - ' . $client['firstname'] . ' - ' . $client['middelname'] . ' - ' . $client['lastname'] . ' ) ' ?>
                                                                    </option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /.card-body -->
                                        <div class="card-footer">
                                            <a href='index1.php' class="btn btn-default">Back</a>
                                            <input type="submit" name="update_study_id" value="Submit"
                                                class="btn btn-primary">
                                        </div>
                                    </form>
                                </div>
                                <!-- /.card -->
                            </div>
                            <!--/.col (Center) -->

                            <!-- right column -->
                            <div class="col-md-4">
                                <!-- general form elements disabled -->
                                <div class="card card-warning">
                                    <div class="card-header">
                                        <h3 class="card-title">Update STUDY ID (ALL TABLES) </h3>
                                    </div>
                                    <!-- /.card-header -->
                                    <form id="validation" enctype="multipart/form-data" method="post" autocomplete="off">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="row-form clearfix">
                                                        <div class="form-group">
                                                            <label for="forms">( PATIENT ID ) FULL NAME ( STUDY ID )</label>
                                                            <select name="patient_id" class="form-control"
                                                                style="width: 100%;" required>
                                                                <option value="">Select Name</option>
                                                                <?php foreach ($override->get('clients', 'status', 1) as $client) { ?>
                                                                    <option value="<?= $client['id'] ?>">
                                                                        <?= $client['id'] . ' - ( ' . $client['study_id'] . ' - ' . $client['firstname'] . ' - ' . $client['middelname'] . ' - ' . $client['lastname'] . ' ) ' ?>
                                                                    </option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /.card-body -->
                                        <div class="card-footer">
                                            <a href='index1.php' class="btn btn-default">Back</a>
                                            <input type="submit" name="update_study_id_all_tables" value="Submit"
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
        <?php } elseif ($_GET['id'] == 6) { ?>
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
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>Participants Study CRF's</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="index1.php">Home</a></li>
                                    <li class="breadcrumb-item active">Participants Study CRF's</li>
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
                                <!-- <?php if ($errorMessage) { ?>
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
                                <?php } ?> -->
                                <div class="card">
                                    <div class="card-header">
                                        <?php

                                        $patient = $override->get('clients', 'id', $_GET['cid'])[0];
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

                                        $name = 'Name: ' . $patient['firstname'] . ' ' . $patient['lastname'] . ' Age: ' . $patient['age'] . ' Gender: ' . $gender . ' Type: ' . $cat;


                                        ?>

                                        <div class="row mb-2">
                                            <div class="col-sm-6">
                                                <h1>Study ID: <?= $patient['study_id'] ?></h1>
                                                <h4>Name: <?= $name ?></h4>
                                                <h4>Age: <?= $patient['age'] ?></h4>
                                                <h4>Gender: <?= $gender ?></h4>
                                                <h4>Category: <?= $cat ?></h4>
                                            </div>
                                            <div class="col-sm-6">
                                                <ol class="breadcrumb float-sm-right">
                                                    <li class="breadcrumb-item"><a
                                                            href="info.php?id=4&cid=<?= $_GET['cid'] ?>&status=<?= $_GET['status'] ?>">
                                                            < Back</a>
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
                                                <tr>
                                                    <th width="5%">#</th>
                                                    <th width="25%">Name</th>
                                                    <th width="65%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if ($_GET['vcode'] == 'D0') { ?>
                                                    <tr>
                                                        <td>1</td>
                                                        <td>CRF 1</td>
                                                        <?php if ($override->get1('crf1', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode'])) { ?>
                                                            <td><a href="add.php?id=8&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&sid=<?= $_GET['sid'] ?>"
                                                                    class="btn btn-success"> Change </a> </td>
                                                        <?php } else { ?>
                                                            <td><a href="add.php?id=8&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&sid=<?= $_GET['sid'] ?>"
                                                                    class="btn btn-warning">Add </a> </td>
                                                        <?php } ?>
                                                    </tr>
                                                <?php } ?>

                                                <?php if ($_GET['vcode'] == 'D0' || $_GET['vcode'] == 'D7' || $_GET['vcode'] == 'D14' || $_GET['vcode'] == 'D30' || $_GET['vcode'] == 'D60' || $_GET['vcode'] == 'D90' || $_GET['vcode'] == 'D120') { ?>

                                                    <tr>
                                                        <td>2</td>
                                                        <td>CRF 2</td>
                                                        <?php if ($override->get1('crf2', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode'])) { ?>
                                                            <td><a href="add.php?id=9&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&sid=<?= $_GET['sid'] ?>"
                                                                    class="btn btn-success"> Change </a> </td>
                                                        <?php } else { ?>
                                                            <td><a href="add.php?id=9&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&sid=<?= $_GET['sid'] ?>"
                                                                    class="btn btn-warning">Add </a> </td>
                                                        <?php } ?>
                                                    </tr>

                                                    <tr>
                                                        <td>3</td>
                                                        <td>CRF 3</td>
                                                        <?php if ($override->get1('crf3', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode'])) { ?>
                                                            <td><a href="add.php?id=10&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&sid=<?= $_GET['sid'] ?>"
                                                                    class="btn btn-success"> Change </a> </td>
                                                        <?php } else { ?>
                                                            <td><a href="add.php?id=10&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&sid=<?= $_GET['sid'] ?>"
                                                                    class="btn btn-warning">Add </a> </td>
                                                        <?php } ?>
                                                    </tr>

                                                    <tr>
                                                        <td>4</td>
                                                        <td>CRF 4</td>
                                                        <?php if ($override->get1('crf4', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode'])) { ?>
                                                            <td><a href="add.php?id=11&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&sid=<?= $_GET['sid'] ?>"
                                                                    class="btn btn-success"> Change </a> </td>
                                                        <?php } else { ?>
                                                            <td><a href="add.php?id=11&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&sid=<?= $_GET['sid'] ?>"
                                                                    class="btn btn-warning">Add </a> </td>
                                                        <?php } ?>
                                                    </tr>

                                                    <tr>
                                                        <td>7</td>
                                                        <td>CRF 7</td>
                                                        <?php if ($override->get1('crf7', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode'])) { ?>
                                                            <td><a href="add.php?id=15&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&sid=<?= $_GET['sid'] ?>"
                                                                    class="btn btn-success"> Change </a> </td>
                                                        <?php } else { ?>
                                                            <td><a href="add.php?id=15&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&sid=<?= $_GET['sid'] ?>"
                                                                    class="btn btn-warning">Add </a> </td>
                                                        <?php } ?>
                                                    </tr>

                                                <?php } ?>


                                                <?php if ($_GET['vcode'] == 'AE') { ?>
                                                    <tr>
                                                        <td>5</td>
                                                        <td>CRF 5</td>
                                                        <?php if ($override->get1('crf5', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode'])) { ?>
                                                            <td><a href="add.php?id=12&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&sid=<?= $_GET['sid'] ?>"
                                                                    class="btn btn-success"> Change </a> </td>
                                                        <?php } else { ?>
                                                            <td><a href="add.php?id=12&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&sid=<?= $_GET['sid'] ?>"
                                                                    class="btn btn-warning">Add </a> </td>
                                                        <?php } ?>
                                                    </tr>

                                                <?php } ?>


                                                <?php if ($_GET['vcode'] == 'END') { ?>
                                                    <tr>
                                                        <td>6</td>
                                                        <td>CRF 6</td>
                                                        <?php if ($override->get1('crf6', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode'])) { ?>
                                                            <td><a href="add.php?id=13&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&sid=<?= $_GET['sid'] ?>"
                                                                    class="btn btn-success"> Change </a> </td>
                                                        <?php } else { ?>
                                                            <td><a href="add.php?id=13&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&sid=<?= $_GET['sid'] ?>"
                                                                    class="btn btn-warning">Add </a> </td>
                                                        <?php } ?>
                                                    </tr>

                                                <?php } ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th width="5%">#</th>
                                                    <th width="25%">Name</th>
                                                    <th width="65%">Action</th>
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
        <?php } elseif ($_GET['id'] == 7) { ?>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>Participants Study CRF's</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="index1.php">Home</a></li>
                                    <li class="breadcrumb-item active">Participants Study CRF's</li>
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
                                <!-- <?php if ($errorMessage) { ?>
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
                                <?php } ?> -->
                                <div class="card">
                                    <div class="card-header">
                                        <?php

                                        $patient = $override->get('clients', 'id', $_GET['cid'])[0];
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

                                        $name = 'Name: ' . $patient['firstname'] . ' ' . $patient['lastname'] . ' Age: ' . $patient['age'] . ' Gender: ' . $gender . ' Type: ' . $cat;


                                        ?>

                                        <div class="row mb-2">
                                            <div class="col-sm-6">
                                                <h1>Study ID: <?= $patient['study_id'] ?></h1>
                                                <h4>Name: <?= $name ?></h4>
                                                <h4>Age: <?= $patient['age'] ?></h4>
                                                <h4>Gender: <?= $gender ?></h4>
                                                <h4>Category: <?= $cat ?></h4>
                                            </div>
                                            <div class="col-sm-6">
                                                <ol class="breadcrumb float-sm-right">
                                                    <li class="breadcrumb-item"><a
                                                            href="info.php?id=4&cid=<?= $_GET['cid'] ?>&status=<?= $_GET['status'] ?>">
                                                            < Back</a>
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
                                                    <th width="2%">#</th>
                                                    <th width="8%">Visit Name</th>
                                                    <th width="3%">Visit Code</th>
                                                    <th width="10%">Visit Type</th>
                                                    <th width="10%">Expected Date</th>
                                                    <th width="10%">Visit Date</th>
                                                    <th width="5%">Status</th>
                                                    <th width="15%">Action</th>
                                                    <th width="15%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $x = 1;
                                                foreach ($override->get('visit', 'client_id', $_GET['cid']) as $visit) {
                                                    $crf1 = $override->get1('crf1', 'patient_id', $_GET['cid'], 'vcode', $visit['visit_code'])[0];
                                                    $crf2 = $override->get1('crf2', 'patient_id', $_GET['cid'], 'vcode', $visit['visit_code'])[0];
                                                    $crf3 = $override->get1('crf3', 'patient_id', $_GET['cid'], 'vcode', $visit['visit_code'])[0];
                                                    $crf4 = $override->get1('crf4', 'patient_id', $_GET['cid'], 'vcode', $visit['visit_code'])[0];
                                                    $crf5 = $override->get1('crf5', 'patient_id', $_GET['cid'], 'vcode', $visit['visit_code'])[0];
                                                    $crf6 = $override->get1('crf6', 'patient_id', $_GET['cid'], 'vcode', $visit['visit_code'])[0];
                                                    $crf7 = $override->get1('crf7', 'patient_id', $_GET['cid'], 'vcode', $visit['visit_code'])[0];
                                                    $sc = $override->get('screening', 'client_id', $_GET['cid'])[0];
                                                    $lb = $override->get('lab', 'client_id', $_GET['cid'])[0];
                                                    $cntV = $override->getCount('visit', 'client_id', $visit['client_id']);
                                                    $client = $override->get('clients', 'id', $_GET['cid'])[0];
                                                    $study_id = $override->get1('clients', 'id', $_GET['cid'], 'status', 1)[0];

                                                    $visit_status = 0;
                                                    if ($visit['visit_status']) {
                                                    }


                                                    // print_r($visit['visit_status']);
                                                    if ($visit['status'] == 0) {
                                                        $btnV = 'Add';
                                                    } elseif ($visit['status'] == 1) {
                                                        $btnV = 'Edit';
                                                        // if ($x == 1) {
                                                        //     $btnV = 'Add';
                                                        // }
                                                    }
                                                    if ($sc) {
                                                        $btnS = 'Edit';
                                                    } else {
                                                        $btnS = 'Add';
                                                    }
                                                    if ($lb) {
                                                        $btnL = 'Edit';
                                                    } else {
                                                        $btnL = 'Add';
                                                    }
                                                    if ($visit['visit_code'] == 'D0') {
                                                        $v_typ = 'Enrollment';
                                                    } elseif ($visit['visit_code'] == 'END') {
                                                        $v_typ = 'END STUDY';
                                                    } elseif ($visit['visit_code'] == 'AE') {
                                                        $v_typ = 'AE';
                                                    } else {
                                                        $v_typ = 'Follow Up';
                                                    }

                                                    if ($x == 1 || ($x > 1 && $sc['eligibility'] == 1 && $lb['eligibility'] == 1)) {

                                                        ?>
                                                        <tr>
                                                            <td><?= $x ?></td>
                                                            <td> <?= $visit['visit_name'] ?></td>
                                                            <td> <?= $visit['visit_code'] ?></td>
                                                            <td> <?= $v_typ ?></td>
                                                            <td> <?= $visit['expected_date'] ?></td>
                                                            <td> <?= $visit['visit_date'] ?></td>
                                                            <td>
                                                                <?php if ($visit['status'] == 1) { ?>
                                                                    <a href="#" role="button" class="btn btn-success">Done</a>
                                                                <?php } elseif ($visit['status'] == 0) { ?>
                                                                    <a href="#" role="button" class="btn btn-warning">Pending</a>
                                                                <?php } elseif ($visit['status'] == 2) { ?>
                                                                    <a href="#" role="button" class="btn btn-default">Missed</a>
                                                                <?php } elseif ($visit['status'] == 3) { ?>
                                                                    <a href="#" role="button" class="btn btn-danger">Terminated</a>
                                                                <?php } ?>
                                                            </td>
                                                            <td>
                                                                <?php if ($visit['seq_no'] >= 0) { ?>
                                                                    <?php if ($btnV == 'Add') { ?>
                                                                        <a href="#addVisit<?= $visit['id'] ?>" role="button"
                                                                            class="btn btn-warning"
                                                                            data-toggle="modal"><?= $btnV ?>Visit</a>
                                                                    <?php } else { ?>
                                                                        <a href="#addVisit<?= $visit['id'] ?>" role="button"
                                                                            class="btn btn-info" data-toggle="modal"><?= $btnV ?>Visit</a>
                                                                    <?php } ?>
                                                                </td>
                                                                <td>

                                                                    <?php if ($visit['status'] == 1 && $visit['visit_code'] == 'D0') { ?>

                                                                        <?php if ($crf1 && $crf2 && $crf3 && $crf4 && $crf7) { ?>
                                                                            <a href="info.php?id=6&cid=<?= $_GET['cid'] ?>&vid=<?= $visit['id'] ?>&vcode=<?= $visit['visit_code'] ?>&sid=<?= $client['study_id'] ?>"
                                                                                role="button" class="btn btn-info">Edit Study CRF</a>
                                                                        <?php } else { ?>
                                                                            <a href="info.php?id=6&cid=<?= $_GET['cid'] ?>&vid=<?= $visit['id'] ?>&vcode=<?= $visit['visit_code'] ?>&sid=<?= $client['study_id'] ?>"
                                                                                role="button" class="btn btn-warning">Add Study CRF</a>
                                                                        <?php }
                                                                        ?>



                                                                    <?php } elseif ($visit['status'] == 1 && ($visit['visit_code'] == 'D7' || $visit['visit_code'] == 'D14' || $visit['visit_code'] == 'D30' || $visit['visit_code'] == 'D60' || $visit['visit_code'] == 'D90' || $visit['visit_code'] == 'D120')) { ?>

                                                                        <?php if ($crf2 && $crf3 && $crf4 && $crf7) { ?>
                                                                            <a href="info.php?id=6&cid=<?= $_GET['cid'] ?>&vid=<?= $visit['id'] ?>&vcode=<?= $visit['visit_code'] ?>&sid=<?= $client['study_id'] ?>"
                                                                                role="button" class="btn btn-info">Edit Study CRF</a>
                                                                        <?php } else { ?>
                                                                            <a href="info.php?id=6&cid=<?= $_GET['cid'] ?>&vid=<?= $visit['id'] ?>&vcode=<?= $visit['visit_code'] ?>&sid=<?= $client['study_id'] ?>"
                                                                                role="button" class="btn btn-warning">Add Study CRF</a>
                                                                        <?php }
                                                                        ?>


                                                                    <?php } elseif ($visit['visit_code'] == 'END') { ?>

                                                                        <?php if ($crf6) { ?>
                                                                            <a href="info.php?id=6&cid=<?= $_GET['cid'] ?>&vid=<?= $visit['id'] ?>&vcode=<?= $visit['visit_code'] ?>&sid=<?= $client['study_id'] ?>"
                                                                                role="button" class="btn btn-info">Edit Study CRF</a>
                                                                        <?php } else { ?>
                                                                            <a href="info.php?id=6&cid=<?= $_GET['cid'] ?>&vid=<?= $visit['id'] ?>&vcode=<?= $visit['visit_code'] ?>&sid=<?= $client['study_id'] ?>"
                                                                                role="button" class="btn btn-warning">Add Study CRF</a>
                                                                        <?php }
                                                                        ?>


                                                                    <?php } elseif ($visit['status'] == 1 && $visit['visit_code'] == 'AE') { ?>

                                                                        <?php if ($crf5) { ?>
                                                                            <a href="info.php?id=6&cid=<?= $_GET['cid'] ?>&vid=<?= $visit['id'] ?>&vcode=<?= $visit['visit_code'] ?>&sid=<?= $client['study_id'] ?>"
                                                                                role="button" class="btn btn-info">Edit Study CRF</a>
                                                                        <?php } else { ?>
                                                                            <a href="info.php?id=6&cid=<?= $_GET['cid'] ?>&vid=<?= $visit['id'] ?>&vcode=<?= $visit['visit_code'] ?>&sid=<?= $client['study_id'] ?>"
                                                                                role="button" class="btn btn-warning">Add Study CRF</a>
                                                                        <?php } ?>

                                                                    <?php }
                                                                    ?>


                                                                </td>
                                                            <?php } ?>
                                                        </tr>
                                                        <div class="modal fade" id="addVisit<?= $visit['id'] ?>" tabindex="-1"
                                                            role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <form id="validation" method="post">
                                                                        <div class="modal-header">
                                                                            <h4 class="modal-title">Add Visit</h4>
                                                                            <button type="button" class="close" data-dismiss="modal"
                                                                                aria-label="Close">
                                                                                <span aria-hidden="true">&times;</span>
                                                                            </button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <div class="row">
                                                                                <div class="col-sm-6">
                                                                                    <div class="form-group">
                                                                                        <label>Visit Name:</label>
                                                                                        <input type="text" class="form-control"
                                                                                            name="name"
                                                                                            value="<?= $visit['visit_name'] . ' (' . $visit['visit_code'] . ')' ?>"
                                                                                            disabled />
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm-6">
                                                                                    <div class="form-group">
                                                                                        <label>Visit Type:</label>
                                                                                        <input type="text" class="form-control"
                                                                                            name="name" value="<?= $v_typ ?>"
                                                                                            disabled />
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="row">
                                                                                <div class="col-sm-4">
                                                                                    <div class="form-group">
                                                                                        <label>Visit Status</label>
                                                                                        <select name="visit_status"
                                                                                            id="visit_status" class="form-control">
                                                                                            <?php if ($visit['status'] == "1") { ?>
                                                                                                <option value="<?= $visit['status'] ?>">
                                                                                                    Attended</option>
                                                                                            <?php } elseif ($visit['status'] == "2") { ?>
                                                                                                <option value="<?= $visit['status'] ?>">
                                                                                                    Missed</option>
                                                                                            <?php } elseif ($visit['status'] == "3") { ?>
                                                                                                <option value="<?= $visit['status'] ?>">
                                                                                                    Terminated</option>
                                                                                            <?php } else { ?>
                                                                                                <option value="">Select</option>
                                                                                            <?php } ?>
                                                                                            <option value="1">Attended</option>
                                                                                            <option value="2">Missed</option>
                                                                                            <option value="3">Terminated</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm-4">
                                                                                    <div class="form-group">
                                                                                        <label>Notes / Remarks / Reason:</label>
                                                                                        <textarea name="reasons"
                                                                                            class="form-control" rows="4"><?php if ($visit['status'] != 0) {
                                                                                                echo $visit['reasons'];
                                                                                            } ?></textarea>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm-4">
                                                                                    <div class="form-group">
                                                                                        <label>Date of Follow Up Visit:</label>
                                                                                        <input value="<?php if ($visit['status'] != 0) {
                                                                                            echo $visit['visit_date'];
                                                                                        } ?>"
                                                                                            class="form-control" type="date"
                                                                                            name="visit_date" id="visit_date" />
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer justify-content-between">
                                                                            <input type="hidden" name="id"
                                                                                value="<?= $visit['id'] ?>">
                                                                            <input type="hidden" name="vc"
                                                                                value="<?= $visit['visit_code'] ?>">
                                                                            <input type="hidden" name="study_id"
                                                                                value="<?= $study_id['study_id'] ?>">
                                                                            <button type="button" class="btn btn-default"
                                                                                data-dismiss="modal">Close</button>
                                                                            <input type="submit" name="edit_visit"
                                                                                class="btn btn-warning" value="Save">
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php }
                                                    $x++;
                                                } ?>
                                            </tbody>
                                            <tfoot>
                                                <th>#</th>
                                                <th>CRF</th>
                                                <th>Records</th>
                                                <th>Action</th>
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
                                <h1>Patient Hitory & Complication</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a
                                            href="info.php?id=7&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&seq=<?= $_GET['seq'] ?>&sid=<?= $_GET['sid'] ?>&vday=<?= $_GET['vday'] ?>&status=<?= $_GET['status'] ?>">
                                            < Back</a>
                                    <li class="breadcrumb-item"><a href="index1.php">Home</a></li>
                                    <li class="breadcrumb-item active">Patient Hitory & Complication</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <?php
                            $patient = $override->get('clients', 'id', $_GET['cid'])[0];
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

                            $name = 'Name: ' . $patient['firstname'] . ' ' . $patient['lastname'] . ' Age: ' . $patient['age'] . ' Gender: ' . $gender . ' Type: ' . $cat;


                            ?>

                            <?php $patient = $override->get1('crf1', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode'])[0] ?>
                            <?php $herbal_treatment = $override->get1('herbal_treatment', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode'])[0] ?>
                            <?php $chemotherapy = $override->get1('chemotherapy', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode'])[0] ?>
                            <?php $surgery = $override->get1('surgery', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode'])[0] ?>
                            <!-- right column -->
                            <div class="col-md-12">
                                <!-- general form elements disabled -->
                                <div class="card card-warning">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <strong style="font-size: larger">
                                                <?= 'Name :- ' . $patient['firstname'] . ' - ' . $patient['middlename'] . ' - ' . $patient['lastname'] ?>
                                                ( Diseases History ) ( <?= $_GET['vday']; ?>)
                                            </strong>
                                        </h3>
                                    </div>
                                    <!-- Content Header (Page header) -->
                                    <section class="content-header">
                                        <div class="container-fluid">
                                            <div class="row mb-2">
                                                <div class="col-sm-3">
                                                    <ol class="breadcrumb">
                                                        <li class="breadcrumb-item"><a href="#">Patient ID:</a></li>
                                                        <li class="breadcrumb-item active"><?= $patient['study_id']; ?></li>
                                                    </ol>
                                                </div>
                                                <div class="col-sm-3">
                                                    <ol class="breadcrumb">
                                                        <li class="breadcrumb-item"><a href="#">Age:</a></li>
                                                        <li class="breadcrumb-item active"><?= $patient['age']; ?></li>
                                                    </ol>
                                                </div>
                                                <div class="col-sm-3">
                                                    <ol class="breadcrumb float-sm-right">
                                                        <li class="breadcrumb-item"><a href="#">Gender</a></li>
                                                        <li class="breadcrumb-item active"><?= $gender; ?></li>
                                                    </ol>
                                                </div>
                                                <div class="col-sm-3">
                                                    <ol class="breadcrumb float-sm-right">
                                                        <li class="breadcrumb-item"><a href="#">Type</a></li>
                                                        <li class="breadcrumb-item active"><?= $cat; ?></li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </div><!-- /.container-fluid -->
                                    </section>
                                    <!-- /.card-header -->
                                    <form id="validation" enctype="multipart/form-data" method="post" autocomplete="off">
                                        <div class="card-body">
                                            <hr>
                                            <!-- <form id="validation" enctype="multipart/form-data" method="post" autocomplete="off"> -->

                                            <div class="head clearfix">
                                                <div class="isw-ok"></div>
                                                <h1>Medical History</h1>
                                            </div>

                                            <div class="row-form clearfix">
                                                <div class="col-md-3">Date of diagnosis:</div>
                                                <div class="col-md-9">
                                                    <input value="<?= $patient['diagnosis_date'] ?>" type="text"
                                                        name="diagnosis_date" id="diagnosis_date" />
                                                    <span>Example : 2000-12-26 </span>
                                                </div>
                                            </div>


                                            <div class="head clearfix">
                                                <div class="isw-ok"></div>
                                                <h1>Medical History</h1>
                                                <h1>Do the patients have any of the following medical conditions</h1>
                                            </div>

                                            <div class="row-form clearfix">
                                                <div class="col-md-3">1. Diabetic Mellitus:</div>
                                                <div class="col-md-9">
                                                    <select name="diabetic" id="diabetic" style="width: 100%;">
                                                        <?php if ($patient['diabetic'] == "1") { ?>
                                                            <option value="<?= $patient['diabetic'] ?>">Yes</option>
                                                        <?php } elseif ($patient['diabetic'] == "2") { ?>
                                                            <option value="<?= $patient['diabetic'] ?>">No</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row-form clearfix" id="diabetic_medicatn1">
                                                <div class="col-md-3">1. Is the patient on Medication?</div>
                                                <div class="col-md-9">
                                                    <select name="diabetic_medicatn" id="diabetic_medicatn"
                                                        style="width: 100%;">
                                                        <?php if ($patient['diabetic_medicatn'] == "1") { ?>
                                                            <option value="<?= $patient['diabetic_medicatn'] ?>">Yes</option>
                                                        <?php } elseif ($patient['diabetic_medicatn'] == "2") { ?>
                                                            <option value="<?= $patient['diabetic_medicatn'] ?>">No</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row-form clearfix" id="diabetic_medicatn_name">
                                                <div class="col-md-3">1. Mention the medications:</div>
                                                <div class="col-md-9"><textarea
                                                        value="<?= $patient['diabetic_medicatn_name'] ?>"
                                                        name="diabetic_medicatn_name" rows="4"></textarea> </div>
                                            </div>

                                            <div class="row-form clearfix">
                                                <div class="col-md-3">2. Hypertension:</div>
                                                <div class="col-md-9">
                                                    <select name="hypertension" id="hypertension" style="width: 100%;">
                                                        <?php if ($patient['hypertension'] == "1") { ?>
                                                            <option value="<?= $patient['hypertension'] ?>">Yes</option>
                                                        <?php } elseif ($patient['hypertension'] == "2") { ?>
                                                            <option value="<?= $patient['hypertension'] ?>">No</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row-form clearfix" id="hypertension_medicatn1">
                                                <div class="col-md-3">2. Is the patient on Medication?</div>
                                                <div class="col-md-9">
                                                    <select name="hypertension_medicatn" id="hypertension_medicatn"
                                                        style="width: 100%;">
                                                        <?php if ($patient['hypertension_medicatn1'] == "1") { ?>
                                                            <option value="<?= $patient['hypertension_medicatn1'] ?>">Yes
                                                            </option>
                                                        <?php } elseif ($patient['hypertension_medicatn1'] == "2") { ?>
                                                            <option value="<?= $patient['hypertension_medicatn1'] ?>">No
                                                            </option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row-form clearfix" id="hypertension_medicatn_name">
                                                <div class="col-md-3">2. Mention the medications:</div>
                                                <div class="col-md-9"><textarea
                                                        value="<?= $patient['hypertension_medicatn_name'] ?>"
                                                        name="hypertension_medicatn_name" rows="4"></textarea> </div>
                                            </div>


                                            <div class="row-form clearfix">
                                                <div class="col-md-3">3. Any other heart problem apart from hypertension?:
                                                </div>
                                                <div class="col-md-9">
                                                    <select name="heart" id="heart" style="width: 100%;">
                                                        <?php if ($patient['heart'] == "1") { ?>
                                                            <option value="<?= $patient['heart'] ?>">Yes</option>
                                                        <?php } elseif ($patient['heart'] == "2") { ?>
                                                            <option value="<?= $patient['heart'] ?>">No</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row-form clearfix" id="heart_medicatn1">
                                                <div class="col-md-3">3. Is the patient on Medication?</div>
                                                <div class="col-md-9">
                                                    <select name="heart_medicatn" id="heart_medicatn" style="width: 100%;">
                                                        <?php if ($patient['heart_medicatn'] == "1") { ?>
                                                            <option value="<?= $patient['heart_medicatn'] ?>">Yes</option>
                                                        <?php } elseif ($patient['heart_medicatn'] == "2") { ?>
                                                            <option value="<?= $patient['heart_medicatn'] ?>">No</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row-form clearfix" id="heart_medicatn_name">
                                                <div class="col-md-3">3. Mention the medications:</div>
                                                <div class="col-md-9"><textarea
                                                        value="<?= $patient['heart_medicatn_name'] ?>"
                                                        name="heart_medicatn_name" rows="4"></textarea> </div>
                                            </div>


                                            <div class="row-form clearfix">
                                                <div class="col-md-3">4. Asthma:</div>
                                                <div class="col-md-9">
                                                    <select name="asthma" id="asthma" style="width: 100%;">
                                                        <?php if ($patient['asthma'] == "1") { ?>
                                                            <option value="<?= $patient['asthma'] ?>">Yes</option>
                                                        <?php } elseif ($patient['asthma'] == "2") { ?>
                                                            <option value="<?= $patient['asthma'] ?>">No</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row-form clearfix" id="asthma_medicatn1">
                                                <div class="col-md-3">4. Is the patient on Medication?</div>
                                                <div class="col-md-9">
                                                    <select name="asthma_medicatn" id="asthma_medicatn"
                                                        style="width: 100%;">
                                                        <?php if ($patient['asthma_medicatn'] == "1") { ?>
                                                            <option value="<?= $patient['asthma_medicatn'] ?>">Yes</option>
                                                        <?php } elseif ($patient['asthma_medicatn'] == "2") { ?>
                                                            <option value="<?= $patient['asthma_medicatn'] ?>">No</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row-form clearfix" id="asthma_medicatn_name">
                                                <div class="col-md-3">4. Mention the medications:</div>
                                                <div class="col-md-9"><textarea
                                                        value="<?= $patient['asthma_medicatn_name'] ?>"
                                                        name="asthma_medicatn_name" rows="4"></textarea> </div>
                                            </div>


                                            <div class="row-form clearfix">
                                                <div class="col-md-3">5. HIV/AIDS:</div>
                                                <div class="col-md-9">
                                                    <select name="hiv_aids" id="hiv_aids" style="width: 100%;">
                                                        <?php if ($patient['hiv_aids'] == "1") { ?>
                                                            <option value="<?= $patient['hiv_aids'] ?>">Yes</option>
                                                        <?php } elseif ($patient['hiv_aids'] == "2") { ?>
                                                            <option value="<?= $patient['hiv_aids'] ?>">No</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row-form clearfix" id="hiv_aids_medicatn1">
                                                <div class="col-md-3">5. Is the patient on Medication?</div>
                                                <div class="col-md-9">
                                                    <select name="hiv_aids_medicatn" id="hiv_aids_medicatn"
                                                        style="width: 100%;">
                                                        <?php if ($patient['hiv_aids_medicatn'] == "1") { ?>
                                                            <option value="<?= $patient['hiv_aids_medicatn'] ?>">Yes</option>
                                                        <?php } elseif ($patient['hiv_aids_medicatn'] == "2") { ?>
                                                            <option value="<?= $patient['hiv_aids_medicatn'] ?>">No</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row-form clearfix" id="hiv_aids_medicatn_name">
                                                <div class="col-md-3">5. Mention the medications:</div>
                                                <div class="col-md-9"><textarea
                                                        value="<?= $patient['hiv_aids_medicatn_name'] ?>"
                                                        name="hiv_aids_medicatn_name" rows="4"></textarea> </div>
                                            </div>

                                            <div class="row-form clearfix">
                                                <div class="col-md-3">6. Any other medical condition:</div>
                                                <div class="col-md-9">
                                                    <select name="other_medical" id="other_medical" style="width: 100%;">
                                                        <?php if ($patient['other_medical'] == "1") { ?>
                                                            <option value="<?= $patient['other_medical'] ?>">Yes</option>
                                                        <?php } elseif ($patient['other_medical'] == "2") { ?>
                                                            <option value="<?= $patient['other_medical'] ?>">No</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row-form clearfix" id="other_medication">

                                                <table id="medication_table">
                                                    <thead>
                                                        <tr>
                                                            <th>6. Specify the medical conditions?</th>
                                                            <th>6. Is the patient on Medication?</th>
                                                            <th>6. Mention the medications ?</th>
                                                            <!-- <th>Action</th> -->
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $x = 1;
                                                        foreach ($override->get1('other_medication', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']) as $medication) {
                                                            ?>
                                                            <tr>
                                                                <td><input value='<?= $medication['other_specify'] ?>'
                                                                        type="text" name="other_specify[]"></td>
                                                                <td>
                                                                    <select name="other_medical_medicatn[]"
                                                                        id="other_medical_medicatn[]" style="width: 100%;">
                                                                        <?php if ($medication['other_medical_medicatn'] == "1") { ?>
                                                                            <option
                                                                                value="<?= $medication['other_medical_medicatn'] ?>">
                                                                                Yes</option>
                                                                        <?php } elseif ($medication['other_medical_medicatn'] == "2") { ?>
                                                                            <option
                                                                                value="<?= $medication['other_medical_medicatn'] ?>">
                                                                                No</option>
                                                                        <?php } else { ?>
                                                                            <option value="">Select</option>
                                                                        <?php } ?>
                                                                        <option value="1">Yes</option>
                                                                        <option value="2">No</option>
                                                                    </select>
                                                                </td>
                                                                <td><input value='<?= $medication['other_medicatn_name'] ?>'
                                                                        type="text" name="other_medicatn_name[]"></td>
                                                                <!-- <td><button type="button" class="remove-row">Remove</button></td> -->
                                                                <td><input value='<?= $medication['id'] ?>' type="hidden"
                                                                        name="medication_id[]"></td>

                                                            </tr>
                                                            <?php
                                                            $x++;
                                                        }
                                                        ?>
                                                    </tbody>
                                                </table>
                                                <!-- <button type="button" id="add-row1">Add Row</button> -->
                                            </div>


                                            <div class="head clearfix">
                                                <div class="isw-ok"></div>
                                                <h1>USE OF HERBAL MEDICINES</h1>
                                            </div>


                                            <div class="row-form clearfix">
                                                <div class="col-md-3">8. Are you using NIMREGENIN herbal preparation?:</div>
                                                <div class="col-md-9">
                                                    <select name="nimregenin_herbal" id="nimregenin_herbal"
                                                        style="width: 100%;" required>
                                                        <?php if ($patient['nimregenin_herbal'] == "1") { ?>
                                                            <option value="<?= $patient['nimregenin_herbal'] ?>">Yes</option>
                                                        <?php } elseif ($patient['nimregenin_herbal'] == "2") { ?>
                                                            <option value="<?= $patient['nimregenin_herbal'] ?>">No</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>


                                            <div class="head clearfix" id="nimregenin_header">
                                                <div class="isw-ok"></div>
                                                <h1>NIMREGENIN Herbal preparation</h1>
                                            </div>


                                            <div class="row-form clearfix" id="nimregenin_preparation">

                                                <table id="nimregenin_table">
                                                    <thead>
                                                        <tr>
                                                            <th>Type of NIMREGENIN</th>
                                                            <th>Start Date</th>
                                                            <th>Ongoing ?</th>
                                                            <th>End Date</th>
                                                            <th>Dose</th>
                                                            <th>Frequecy</th>
                                                            <th>Remarks</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $x = 1;
                                                        foreach ($override->get1('nimregenin', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']) as $nimregenin) {
                                                            ?>
                                                            <tr>
                                                                <td><input value='<?= $nimregenin['nimregenin_preparation'] ?>'
                                                                        type="text" name="nimregenin_preparation[]"></td>
                                                                <td><input value='<?= $nimregenin['nimregenin_start'] ?>'
                                                                        type="text" name="nimregenin_start[]"><br><span>Example:
                                                                        2010-12-01</span></td>
                                                                <td>
                                                                    <select name="nimregenin_ongoing[]"
                                                                        id="nimregenin_ongoing[]" style="width: 100%;">
                                                                        <?php if ($nimregenin['nimregenin_ongoing'] == "1") { ?>
                                                                            <option
                                                                                value="<?= $nimregenin['nimregenin_ongoing'] ?>">Yes
                                                                            </option>
                                                                        <?php } elseif ($nimregenin['nimregenin_ongoing'] == "2") { ?>
                                                                            <option
                                                                                value="<?= $nimregenin['nimregenin_ongoing'] ?>">No
                                                                            </option>
                                                                        <?php } else { ?>
                                                                            <option value="">Select</option>
                                                                        <?php } ?>
                                                                        <option value="1">Yes</option>
                                                                        <option value="2">No</option>
                                                                    </select>
                                                                </td>
                                                                <td><input value='<?= $nimregenin['nimregenin_end'] ?>'
                                                                        type="text" name="nimregenin_end[]"><br><span>Example:
                                                                        2010-12-01</span></td>
                                                                <td><input value='<?= $nimregenin['nimregenin_dose'] ?>'
                                                                        type="text"
                                                                        name="nimregenin_dose[]"><br><span>(mls)</span></td>
                                                                <td><input value='<?= $nimregenin['nimregenin_frequency'] ?>'
                                                                        type="text" name="nimregenin_frequency[]"><br><span>(per
                                                                        day)</span></td>
                                                                <td><input value='<?= $nimregenin['nimregenin_remarks'] ?>'
                                                                        type="text" name="nimregenin_remarks[]"><br></td>
                                                                <td><input value='<?= $nimregenin['id'] ?>' type="hidden"
                                                                        name="nimregenin_id[]"></td>
                                                            </tr>
                                                            <?php
                                                            $x++;
                                                        }
                                                        ?>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div class="head clearfix">
                                                <div class="isw-ok"></div>
                                                <h1>Other Herbal preparation</h1>
                                            </div>

                                            <div class="row-form clearfix">
                                                <div class="col-md-3">8. Are you using any other herbal preparation?:</div>
                                                <div class="col-md-9">
                                                    <select name="other_herbal" id="other_herbal" style="width: 100%;"
                                                        required>
                                                        <?php if ($patient['other_herbal'] == "1") { ?>
                                                            <option value="<?= $patient['other_herbal'] ?>">Yes</option>
                                                        <?php } elseif ($patient['other_herbal'] == "2") { ?>
                                                            <option value="<?= $patient['other_herbal'] ?>">No</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>


                                            <div class="head clearfix" id="herbal_header">
                                                <div class="isw-ok"></div>
                                                <h1>Other Herbal preparation</h1>
                                            </div>


                                            <div class="row-form clearfix" id="herbal">

                                                <table id="herbal_preparation_table">
                                                    <thead>
                                                        <tr>
                                                            <th>Type of Herbal</th>
                                                            <th>Start Date</th>
                                                            <th>Ongoing ?</th>
                                                            <th>End Date</th>
                                                            <th>Dose</th>
                                                            <th>Frequecy</th>
                                                            <th>Remarks</th>
                                                            <!-- <th>Action</th> -->
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $x = 1;
                                                        foreach ($override->get1('herbal_treatment', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']) as $herbal_treatment) {
                                                            ?>
                                                            <tr>
                                                                <td><input
                                                                        value='<?= $herbal_treatment['herbal_preparation'] ?>'
                                                                        type="text" name="herbal_preparation[]"></td>
                                                                <td><input value='<?= $herbal_treatment['herbal_start'] ?>'
                                                                        type="text" name="herbal_start[]"><br><span>Example:
                                                                        2010-12-01</span></td>
                                                                <td>
                                                                    <select name="herbal_ongoing[]" id="herbal_ongoing[]"
                                                                        style="width: 100%;">
                                                                        <?php if ($herbal_treatment['herbal_ongoing'] == "1") { ?>
                                                                            <option
                                                                                value="<?= $herbal_treatment['herbal_ongoing'] ?>">
                                                                                Yes</option>
                                                                        <?php } elseif ($herbal_treatment['herbal_ongoing'] == "2") { ?>
                                                                            <option
                                                                                value="<?= $herbal_treatment['herbal_ongoing'] ?>">
                                                                                No</option>
                                                                        <?php } else { ?>
                                                                            <option value="">Select</option>
                                                                        <?php } ?>
                                                                        <option value="1">Yes</option>
                                                                        <option value="2">No</option>
                                                                    </select>
                                                                </td>
                                                                <td><input value='<?= $herbal_treatment['herbal_end'] ?>'
                                                                        type="text" name="herbal_end[]"><br><span>Example:
                                                                        2010-12-01</span></td>
                                                                <td><input value='<?= $herbal_treatment['herbal_dose'] ?>'
                                                                        type="text" name="herbal_dose[]"><br><span>(per
                                                                        day)</span></td>
                                                                <td><input value='<?= $herbal_treatment['herbal_frequency'] ?>'
                                                                        type="text" name="herbal_frequency[]"><br><span>(per
                                                                        day)</span></td>
                                                                <td><input value='<?= $herbal_treatment['herbal_remarks'] ?>'
                                                                        type="text" name="herbal_remarks[]"><br></td>
                                                                <!-- <td><button type="button" class="remove-row">Remove</button></td> -->
                                                                <td><input value='<?= $herbal_treatment['id'] ?>' type="hidden"
                                                                        name="herbal_id[]"></td>
                                                            </tr>
                                                            <?php
                                                            $x++;
                                                        }
                                                        ?>
                                                    </tbody>
                                                </table>
                                                <!-- <button type="button" id="add-row2">Add Row</button> -->
                                            </div>



                                            <div class="head clearfix">
                                                <div class="isw-ok"></div>
                                                <h2>STANDARD OF CARE TREATMENT
                                                </h2>
                                                <h2>Provide lists of treatments and supportive care given to the cancer
                                                    patient</h2>
                                            </div>

                                            <div class="head clearfix">
                                                <div class="isw-ok"></div>
                                                <h3>(To be retrieved from patient file/medical personnel)</h3>
                                                <h1>(all medication should be in generic names)</h1>

                                            </div>

                                            <div class="head clearfix">
                                                <div class="isw-ok"></div>
                                                <h1>1. Radiotherapy :</h1>
                                            </div>

                                            <div class="row-form clearfix">
                                                <div class="col-md-3">1. Is there any Radiotherapy performed?:</div>
                                                <div class="col-md-9">
                                                    <select name="radiotherapy_performed" id="radiotherapy_performed"
                                                        style="width: 100%;" required>
                                                        <?php if ($patient['radiotherapy_performed'] == "1") { ?>
                                                            <option value="<?= $patient['radiotherapy_performed'] ?>">Yes
                                                            </option>
                                                        <?php } elseif ($patient['radiotherapy_performed'] == "2") { ?>
                                                            <option value="<?= $patient['radiotherapy_performed'] ?>">No
                                                            </option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row-form clearfix" id="radiotherapy">

                                                <table id="radiotherapy_table">
                                                    <thead>
                                                        <tr>
                                                            <th>Type of Radiotherapy</th>
                                                            <th>Start Date</th>
                                                            <th>Ongoing ?</th>
                                                            <th>End Date</th>
                                                            <th>Dose</th>
                                                            <th>Frequecy</th>
                                                            <th>Remarks</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $x = 1;
                                                        foreach ($override->get1('radiotherapy', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']) as $radiotherapy) {
                                                            ?>
                                                            <tr>
                                                                <td><input value="<?= $radiotherapy['radiotherapy'] ?>"
                                                                        type="text" name="radiotherapy[]"></td>
                                                                <td><input value="<?= $radiotherapy['radiotherapy_start'] ?>"
                                                                        type="text"
                                                                        name="radiotherapy_start[]"><br><span>Example:
                                                                        2010-12-01</span></td>
                                                                <td>
                                                                    <select name="radiotherapy_ongoing[]"
                                                                        id="radiotherapy_ongoing[]" style="width: 100%;">
                                                                        <?php if ($radiotherapy['radiotherapy_ongoing'] == "1") { ?>
                                                                            <option
                                                                                value="<?= $radiotherapy['radiotherapy_ongoing'] ?>">
                                                                                Yes</option>
                                                                        <?php } elseif ($radiotherapy['radiotherapy_ongoing'] == "2") { ?>
                                                                            <option
                                                                                value="<?= $radiotherapy['radiotherapy_ongoing'] ?>">
                                                                                No</option>
                                                                        <?php } else { ?>
                                                                            <option value="">Select</option>
                                                                        <?php } ?>
                                                                        <option value="1">Yes</option>
                                                                        <option value="2">No</option>
                                                                    </select>
                                                                </td>
                                                                <td><input value="<?= $radiotherapy['radiotherapy_end'] ?>"
                                                                        type="text" name="radiotherapy_end[]"><br><span>Example:
                                                                        2010-12-01</span></td>
                                                                <td><input value="<?= $radiotherapy['radiotherapy_dose'] ?>"
                                                                        type="text"
                                                                        name="radiotherapy_dose[]"><br><span>(Grays)</span></td>
                                                                <td><input value="<?= $radiotherapy['radiotherapy_frequecy'] ?>"
                                                                        type="text"
                                                                        name="radiotherapy_frequecy[]"><br><span>(numbers)</span>
                                                                </td>
                                                                <td><input value="<?= $radiotherapy['radiotherapy_remarks'] ?>"
                                                                        type="text" name="radiotherapy_remarks[]"></td>
                                                                <td><input value="<?= $radiotherapy['id'] ?>" type="hidden"
                                                                        name="radiotherapy_id[]"></td>
                                                            </tr>
                                                            <?php
                                                            $x++;
                                                        }
                                                        ?>
                                                    </tbody>
                                                </table>
                                            </div>




                                            <div class="head clearfix">
                                                <div class="isw-ok"></div>
                                                <h1>2. Chemotherapy :</h1>
                                            </div>

                                            <div class="row-form clearfix">
                                                <div class="col-md-3">2. Is there any Chemotherapy performed?:</div>
                                                <div class="col-md-9">
                                                    <select name="chemotherapy_performed" id="chemotherapy_performed"
                                                        style="width: 100%;" required>
                                                        <?php if ($patient['chemotherapy_performed'] == "1") { ?>
                                                            <option value="<?= $patient['chemotherapy_performed'] ?>">Yes
                                                            </option>
                                                        <?php } elseif ($patient['chemotherapy_performed'] == "2") { ?>
                                                            <option value="<?= $patient['chemotherapy_performed'] ?>">No
                                                            </option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row-form clearfix" id="chemotherapy">

                                                <table id="chemotherapy_table">
                                                    <thead>
                                                        <tr>
                                                            <th>Type of Chemotherapy</th>
                                                            <th>Start Date</th>
                                                            <th>Ongoing ?</th>
                                                            <th>End Date</th>
                                                            <th>Dose</th>
                                                            <th>Frequecy</th>
                                                            <th>Remarks</th>
                                                            <!-- <th>Action</th> -->
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $x = 1;
                                                        foreach ($override->get1('chemotherapy', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']) as $chemotherapy) {
                                                            ?>
                                                            <tr>
                                                                <td><input value="<?= $chemotherapy['chemotherapy'] ?>"
                                                                        type="text" name="chemotherapy[]"></td>
                                                                <td><input value="<?= $chemotherapy['chemotherapy_start'] ?>"
                                                                        type="text"
                                                                        name="chemotherapy_start[]"><br><span>Example:
                                                                        2010-12-01</span></td>
                                                                <td>
                                                                    <select name="chemotherapy_ongoing[]"
                                                                        id="chemotherapy_ongoing[]" style="width: 100%;">
                                                                        <?php if ($chemotherapy['chemotherapy_ongoing'] == "1") { ?>
                                                                            <option
                                                                                value="<?= $chemotherapy['chemotherapy_ongoing'] ?>">
                                                                                Yes</option>
                                                                        <?php } elseif ($chemotherapy['chemotherapy_ongoing'] == "2") { ?>
                                                                            <option
                                                                                value="<?= $chemotherapy['chemotherapy_ongoing'] ?>">
                                                                                No</option>
                                                                        <?php } else { ?>
                                                                            <option value="">Select</option>
                                                                        <?php } ?>
                                                                        <option value="1">Yes</option>
                                                                        <option value="2">No</option>
                                                                    </select>
                                                                </td>
                                                                <td><input value="<?= $chemotherapy['chemotherapy_end'] ?>"
                                                                        type="text" name="chemotherapy_end[]"><br><span>Example:
                                                                        2010-12-01</span></td>
                                                                <td><input value="<?= $chemotherapy['chemotherapy_dose'] ?>"
                                                                        type="text"
                                                                        name="chemotherapy_dose[]"><br><span>(mg)</span></td>
                                                                <td><input value="<?= $chemotherapy['chemotherapy_frequecy'] ?>"
                                                                        type="text"
                                                                        name="chemotherapy_frequecy[]"><br><span>(numbers)</span>
                                                                </td>
                                                                <td><input value="<?= $chemotherapy['chemotherapy_remarks'] ?>"
                                                                        type="text" name="chemotherapy_remarks[]"></td>
                                                                <!-- <td><button type="button" class="remove-row3">Remove</button></td> -->
                                                                <td><input value="<?= $chemotherapy['id'] ?>" type="hidden"
                                                                        name="chemotherapy_id[]"></td>
                                                            </tr>
                                                            <?php
                                                            $x++;
                                                        }
                                                        ?>
                                                    </tbody>
                                                </table>
                                                <button type="button" id="add-row3">Add Row</button>
                                            </div>

                                            <div class="head clearfix">
                                                <div class="isw-ok"></div>
                                                <h1>3. Surgery :</h1>
                                            </div>

                                            <div class="row-form clearfix">
                                                <div class="col-md-3">2. Is there any Surgery performed?:</div>
                                                <div class="col-md-9">
                                                    <select name="surgery_performed" id="surgery_performed"
                                                        style="width: 100%;" required>
                                                        <?php if ($patient['surgery_performed'] == "1") { ?>
                                                            <option value="<?= $patient['surgery_performed'] ?>">Yes</option>
                                                        <?php } elseif ($patient['surgery_performed'] == "2") { ?>
                                                            <option value="<?= $patient['surgery_performed'] ?>">No</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row" id="surgery">
                                                <div class="row-form clearfix">
                                                    <table id="surgery_table">
                                                        <thead>
                                                            <tr>
                                                                <th>Type of Surgery</th>
                                                                <th>Start Date</th>
                                                                <th>Frequecy</th>
                                                                <th>Remarks</th>
                                                                <!-- <th>Action</th> -->
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $x = 1;
                                                            foreach ($override->get1('surgery', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']) as $surgery) {
                                                                ?>
                                                                <tr>
                                                                    <td><input value="<?= $surgery['surgery'] ?>" type="text"
                                                                            name="surgery[]"></td>
                                                                    <td><input value="<?= $surgery['surgery_start'] ?>"
                                                                            type="text"
                                                                            name="surgery_start[]"><br><span>Example:
                                                                            2010-12-01</span></td>
                                                                    <td><input value="<?= $surgery['surgery_number'] ?>"
                                                                            type="text"
                                                                            name="surgery_number[]"><br><span>(numbers)</span>
                                                                    </td>
                                                                    <td><input value="<?= $surgery['surgery_remarks'] ?>"
                                                                            type="text" name="surgery_remarks[]"></td>
                                                                    <!-- <td><button type="button" class="remove-row">Remove</button></td> -->
                                                                    <td><input value="<?= $surgery['id'] ?>" type="hidden"
                                                                            name="surgery_id[]"></td>
                                                                </tr>
                                                                <?php
                                                                $x++;
                                                            }
                                                            ?>
                                                        </tbody>
                                                    </table>
                                                    <button type="button" id="add-row4">Add Row</button>
                                                </div>
                                            </div>

                                            <div class="col-sm-12">
                                                <div class="row-form clearfix">
                                                    <!-- select -->
                                                    <div class="form-group">
                                                        <label>Date of Completion:</label>
                                                        <input value="<?= $patient['crf1_cmpltd_date'] ?>" type="text"
                                                            name="crf1_cmpltd_date" id="crf1_cmpltd_date" />
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- <div class="footer tar">

                                                </div> -->
                                            <!-- </form> -->
                                        </div>
                                        <!-- /.card-body -->
                                        <div class="card-footer">
                                            <!-- id=6&cid=1&vid=480&vcode=D0&sid=2-001 -->
                                            <a href='info.php?id=6&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>&seq=<?= $_GET['seq'] ?>&sid=<?= $_GET['sid'] ?>&vday=<?= $_GET['vday'] ?>&status=<?= $_GET['status'] ?>'
                                                class="btn btn-default">Back</a>
                                            <?php if ($user->data()->accessLevel == 1 || $user->data()->accessLevel == 2) { ?>
                                                <input type="hidden" name="id" value="<?= $patient['id'] ?>">
                                                <input type="submit" name="update_crf1" value="Submit" class="btn btn-default">
                                            <?php } ?>
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
        <?php } elseif ($_GET['id'] == 9) { ?>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>Medications</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="index1.php">Home</a></li>
                                    <li class="breadcrumb-item active">Medications</li>
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
                                        $patient = $override->get('clients', 'id', $_GET['cid'])[0];
                                        $visits_status = $override->firstRow1('visit', 'status', 'id', 'client_id', $_GET['cid'], 'visit_code', 'EV')[0]['status'];

                                        // $patient = $override->get('clients', 'id', $_GET['cid'])[0];
                                        $category = $override->get('main_diagnosis', 'patient_id', $_GET['cid'])[0];
                                        $cat = '';

                                        if ($category['cardiac'] == 1) {
                                            $cat = 'Cardiac';
                                        } elseif ($category['diabetes'] == 1) {
                                            $cat = 'Diabetes';
                                        } elseif ($category['sickle_cell'] == 1) {
                                            $cat = 'Sickle cell';
                                        } else {
                                            $cat = 'Not Diagnosed';
                                        }


                                        if ($patient['gender'] == 1) {
                                            $gender = 'Male';
                                        } elseif ($patient['gender'] == 2) {
                                            $gender = 'Female';
                                        }

                                        $name = 'Name: ' . $patient['firstname'] . ' ' . $patient['lastname'] . ' Age: ' . $patient['age'] . ' Gender: ' . $gender . ' Type: ' . $cat;

                                        ?>

                                        <div class="col-sm-6">
                                            <ol class="breadcrumb float-sm-right">
                                                <li class="breadcrumb-item"><a href="index1.php">
                                                        <a href='info.php?id=8' class="btn btn-default">Back</a>
                                                    </a>
                                                </li>
                                            </ol>
                                        </div>
                                    </div>

                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <table id="example1" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Batch / Serial No.</th>
                                                    <th>Amount</th>
                                                    <th>Expire Date</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $x = 1;
                                                foreach ($override->getNews('batch', 'status', 1, 'medication_id', $_GET['medication_id']) as $value) {
                                                    $name = $override->get('medications', 'status', 1, 'id', $value['medication_id'])[0];
                                                    $batch_sum = $override->getSumD2('batch', 'amount', 'status', 1, 'medication_id', $value['id'])[0]['SUM(amount)'];
                                                    $forms = $override->get('forms', 'status', 1, 'id', $value['forms'])[0];
                                                    if ($batch_sum) {
                                                        $batch_sum = $batch_sum;
                                                    } elseif ($visit['status'] == 1) {
                                                        $batch_sum = 0;
                                                    }

                                                    ?>
                                                    <tr>
                                                        <td><?= $name['name'] ?></td>
                                                        <td><?= $value['serial_name'] ?></td>
                                                        <td><?= $value['amount'] ?></td>
                                                        <td><?= $value['expire_date'] ?></td>
                                                        <td>
                                                            <?php if ($value['expire_date'] > date('Y-m-d')) { ?>
                                                                <a href="#editVisit<?= $visit['id'] ?>" role="button"
                                                                    class="btn btn-success" data-toggle="modal">Valid</a>
                                                            <?php } elseif ($visit['status'] == 0) { ?>
                                                                <a href="#editVisit<?= $visit['id'] ?>" role="button"
                                                                    class="btn btn-warning" data-toggle="modal">Expired</a>
                                                            <?php } ?>
                                                        </td>
                                                        <td>
                                                            <!-- <a href="#editVisit<?= $value['id'] ?>" role="button" class="btn btn-success" data-toggle="modal">Update</a> -->
                                                            <a href="#increase<?= $value['id'] ?>" role="button"
                                                                class="btn btn-info" data-toggle="modal">Increase Batch</a>
                                                            <a href="#decrease<?= $value['id'] ?>" role="button"
                                                                class="btn btn-warning" data-toggle="modal">Decrease Batch</a>
                                                            <a href="info.php?id=9&generic_id=<?= $value['id'] ?>" role="button"
                                                                class="btn btn-deafult">View</a>
                                                        </td>

                                                    </tr>

                                                    <div class="modal fade" id="increase<?= $value['id'] ?>">
                                                        <div class="modal-dialog">
                                                            <form method="post">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h4 class="modal-title">Increase (
                                                                            <?= $name['name']; ?>) :- Batch / Serial (
                                                                            <?= $value['serial_name']; ?>)
                                                                        </h4>
                                                                        <button type="button" class="close" data-dismiss="modal"
                                                                            aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="row">
                                                                            <div class="col-sm-4">
                                                                                <div class="row-form clearfix">
                                                                                    <!-- select -->
                                                                                    <div class="form-group">
                                                                                        <label>Date Received</label>
                                                                                        <input class="form-control" value=""
                                                                                            type="date" name="date" id="date"
                                                                                            required />
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-sm-4">
                                                                                <div class="row-form clearfix">
                                                                                    <!-- select -->
                                                                                    <div class="form-group">
                                                                                        <label>Available Amount</label>
                                                                                        <input class="form-control" value="<?php if ($value['amount']) {
                                                                                            echo $value['amount'];
                                                                                        } ?>" type="number"
                                                                                            min="0" name="amount" id="amount"
                                                                                            readonly />
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-4">
                                                                                <div class="row-form clearfix">
                                                                                    <!-- select -->
                                                                                    <div class="form-group">
                                                                                        <label>Received Amount</label>
                                                                                        <input class="form-control" value=""
                                                                                            type="number" min="0"
                                                                                            name="received" id="received"
                                                                                            required />
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="row">
                                                                            <div class="col-sm-6">
                                                                                <div class="row-form clearfix">
                                                                                    <!-- select -->
                                                                                    <div class="form-group">
                                                                                        <label>Total Price ( TSHS )</label>
                                                                                        <input class="form-control" value="<?php if ($value['price']) {
                                                                                            echo $value['price'];
                                                                                        } ?>" type="number"
                                                                                            min="0" name="price" id="price"
                                                                                            readonly />
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-6">
                                                                                <div class="row-form clearfix">
                                                                                    <!-- select -->
                                                                                    <div class="form-group">
                                                                                        <label>New Price ( TSHS )</label>
                                                                                        <input class="form-control" value=""
                                                                                            type="number" min="0" name="cost"
                                                                                            id="cost" required />
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="dr"><span></span></div>
                                                                    </div>
                                                                    <div class="modal-footer justify-content-between">
                                                                        <input type="hidden" name="id"
                                                                            value="<?= $value['id'] ?>">
                                                                        <input type="hidden" name="name"
                                                                            value="<?= $name['name']; ?>">
                                                                        <input type="hidden" name="serial_name"
                                                                            value="<?= $name['serial_name']; ?>">
                                                                        <button type="button" class="btn btn-default"
                                                                            data-dismiss="modal">Close</button>
                                                                        <input type="submit" name="increase_batch"
                                                                            class="btn btn-primary" value="Save changes">
                                                                    </div>
                                                                </div>
                                                                <!-- /.modal-content -->
                                                            </form>
                                                        </div>
                                                        <!-- /.modal-dialog -->
                                                    </div>
                                                    <!-- /.modal -->

                                                    <div class="modal fade" id="decrease<?= $value['id'] ?>">
                                                        <div class="modal-dialog">
                                                            <form method="post">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h4 class="modal-title">Decrease (
                                                                            <?= $name['name']; ?>) :- Batch / Serial (
                                                                            <?= $value['serial_name']; ?>)
                                                                        </h4>
                                                                        <button type="button" class="close" data-dismiss="modal"
                                                                            aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="row">
                                                                            <div class="col-sm-4">
                                                                                <div class="row-form clearfix">
                                                                                    <!-- select -->
                                                                                    <div class="form-group">
                                                                                        <label>Date Removed</label>
                                                                                        <input class="form-control" value=""
                                                                                            type="date" name="date" id="date"
                                                                                            required />
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-sm-4">
                                                                                <div class="row-form clearfix">
                                                                                    <!-- select -->
                                                                                    <div class="form-group">
                                                                                        <label>Available Amount</label>
                                                                                        <input class="form-control" value="<?php if ($value['amount']) {
                                                                                            echo $value['amount'];
                                                                                        } ?>" type="number"
                                                                                            min="0" name="amount" id="amount"
                                                                                            readonly />
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-4">
                                                                                <div class="row-form clearfix">
                                                                                    <!-- select -->
                                                                                    <div class="form-group">
                                                                                        <label>Remove Amount</label>
                                                                                        <input class="form-control" value=""
                                                                                            type="number" min="0" name="removed"
                                                                                            id="removed" required />
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="dr"><span></span></div>
                                                                    </div>
                                                                    <div class="modal-footer justify-content-between">
                                                                        <input type="hidden" name="id"
                                                                            value="<?= $value['id'] ?>">
                                                                        <input type="hidden" name="name"
                                                                            value="<?= $name['name']; ?>">
                                                                        <input type="hidden" name="serial_name"
                                                                            value="<?= $name['serial_name']; ?>">
                                                                        <button type="button" class="btn btn-default"
                                                                            data-dismiss="modal">Close</button>
                                                                        <input type="submit" name="decrease_batch"
                                                                            class="btn btn-primary" value="Save changes">
                                                                    </div>
                                                                </div>
                                                                <!-- /.modal-content -->
                                                            </form>
                                                        </div>
                                                        <!-- /.modal-dialog -->
                                                    </div>
                                                    <!-- /.modal -->
                                                    <?php $x++;
                                                } ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Batch / Serial No.</th>
                                                    <th>Amount</th>
                                                    <th>Expire Date</th>
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
        <?php } elseif ($_GET['id'] == 10) { ?>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>Medications Batch</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="add.php?id=6&btn=Add">
                                            < Go Back</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="index1.php">Home</a></li>
                                    <li class="breadcrumb-item active">Medications Batch</li>
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
                                        $patient = $override->get('clients', 'id', $_GET['cid'])[0];
                                        $visits_status = $override->firstRow1('visit', 'status', 'id', 'client_id', $_GET['cid'], 'visit_code', 'EV')[0]['status'];

                                        // $patient = $override->get('clients', 'id', $_GET['cid'])[0];
                                        $category = $override->get('main_diagnosis', 'patient_id', $_GET['cid'])[0];
                                        $cat = '';

                                        if ($category['cardiac'] == 1) {
                                            $cat = 'Cardiac';
                                        } elseif ($category['diabetes'] == 1) {
                                            $cat = 'Diabetes';
                                        } elseif ($category['sickle_cell'] == 1) {
                                            $cat = 'Sickle cell';
                                        } else {
                                            $cat = 'Not Diagnosed';
                                        }


                                        if ($patient['gender'] == 1) {
                                            $gender = 'Male';
                                        } elseif ($patient['gender'] == 2) {
                                            $gender = 'Female';
                                        }

                                        $name = 'Name: ' . $patient['firstname'] . ' ' . $patient['lastname'] . ' Age: ' . $patient['age'] . ' Gender: ' . $gender . ' Type: ' . $cat;

                                        ?>
                                    </div>

                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <table id="example1" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Batch / Serial No.</th>
                                                    <th>Amount</th>
                                                    <th>Forms</th>
                                                    <th>Expire Date</th>
                                                    <th>Status</th>
                                                    <th>Remarks</th>
                                                    <th>Price</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $x = 1;
                                                foreach ($override->getAsc('batch', 'status', 1) as $value) {
                                                    $medication = $override->getNews('medications', 'status', 1, 'id', $value['medication_id'])['0'];
                                                    $batch_sum = $override->getSumD2('batch', 'amount', 'status', 1, 'medication_id', $value['id'])[0]['SUM(amount)'];
                                                    $forms = $override->getNews('forms', 'status', 1, 'id', $medication['forms'])[0];
                                                    if ($batch_sum) {
                                                        $batch_sum = $batch_sum;
                                                    } elseif ($visit['status'] == 1) {
                                                        $batch_sum = 0;
                                                    }

                                                    ?>
                                                    <tr>
                                                        <td><?= $medication['name'] ?></td>
                                                        <td><?= $value['serial_name'] ?></td>
                                                        <td><?= $value['amount'] ?></td>
                                                        <td><?= $forms['name'] ?></td>
                                                        <td><?= $value['expire_date'] ?></td>
                                                        <td>
                                                            <?php if ($value['expire_date'] > date('Y-m-d')) { ?>
                                                                <a href="#editVisit<?= $visit['id'] ?>" role="button"
                                                                    class="btn btn-success" data-toggle="modal">Valid</a>
                                                            <?php } elseif ($visit['status'] == 0) { ?>
                                                                <a href="#editVisit<?= $visit['id'] ?>" role="button"
                                                                    class="btn btn-warning" data-toggle="modal">Expired</a>
                                                            <?php } ?>
                                                        </td>
                                                        <td><?= $value['remarks'] ?></td>
                                                        <td><?= $value['price'] ?></td>
                                                        <td>
                                                            <a href="add.php?id=6&batch_id=<?= $value['id'] ?>&medication_id=<?= $medication['id'] ?>&btn=Update"
                                                                role="button" class="btn btn-info">Update</a>
                                                            <a href="#delete_batch<?= $value['id'] ?>" role="button"
                                                                class="btn btn-danger" data-toggle="modal">Delete</a>
                                                        </td>
                                                    </tr>
                                                    <div class="modal fade" id="delete_batch<?= $value['id'] ?>">
                                                        <div class="modal-dialog">
                                                            <form method="post">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h4 class="modal-title">Delete Medication Batch</h4>
                                                                        <button type="button" class="close" data-dismiss="modal"
                                                                            aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <p class="text-muted text-center">Are you sure yoy want
                                                                            to delete this medication batch ?</p>
                                                                    </div>
                                                                    <div class="modal-footer justify-content-between">
                                                                        <input type="hidden" name="id"
                                                                            value="<?= $value['id'] ?>">
                                                                        <input type="hidden" name="name"
                                                                            value="<?= $value['serial_name'] ?>">
                                                                        <button type="button" class="btn btn-default"
                                                                            data-dismiss="modal">Cancel</button>
                                                                        <input type="submit" name="delete_batch"
                                                                            class="btn btn-danger" value="Yes, Delete">
                                                                    </div>
                                                                </div>
                                                                <!-- /.modal-content -->
                                                            </form>
                                                        </div>
                                                        <!-- /.modal-dialog -->
                                                    </div>
                                                    <!-- /.modal -->
                                                    <?php $x++;
                                                } ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Batch / Serial No.</th>
                                                    <th>Amount</th>
                                                    <th>Forms</th>
                                                    <th>Expire Date</th>
                                                    <th>Status</th>
                                                    <th>Remarks</th>
                                                    <th>Price</th>
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
        <?php } elseif ($_GET['id'] == 11) { ?>
        <?php } elseif ($_GET['id'] == 12) { ?>
        <?php } elseif ($_GET['id'] == 13) { ?>
        <?php } elseif ($_GET['id'] == 14) { ?>
        <?php } elseif ($_GET['id'] == 15) { ?>
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
        $(function () {
            $("#example1").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });
        });
    </script>

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

        $(document).ready(function () {
            $('#search').keyup(function () {
                var searchTerm = $(this).val();
                $.ajax({
                    url: 'fetch_details.php?content=fetchDetails',
                    type: 'GET',
                    data: {
                        search: searchTerm
                    },
                    // dataType: "json",
                    success: function (response) {
                        console.log(response)
                        $('#search-results').html(response);
                    }
                });
            });
        });

        $(document).ready(function () {
            $("#myInput11").on("keyup", function () {
                var value = $(this).val().toLowerCase();
                $("#inventory_report1 tr").filter(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });

        $(document).ready(function () {
            $("#myInput").on("keyup", function () {
                var value = $(this).val().toLowerCase();
                $("#medication_list tr").filter(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });

        function checkQuestionValue1(currentQuestion, elementToHide) {
            var currentQuestionInput = document.getElementById(currentQuestion);
            var elementToHide = document.getElementById(elementToHide);

            var questionValue = currentQuestionInput.value;

            if (questionValue === "1") {
                elementToHide.classList.remove("hidden");
            } else {
                elementToHide.classList.add("hidden");
            }
        }

        function checkQuestionValue96(currentQuestion, elementToHide) {
            var currentQuestionInput = document.getElementById(currentQuestion);
            var elementToHide = document.getElementById(elementToHide);

            var questionValue = currentQuestionInput.value;

            if (questionValue === "96") {
                elementToHide.classList.remove("hidden");
            } else {
                elementToHide.classList.add("hidden");
            }
        }

        function checkQuestionValue45(currentQuestion, elementToHide1, elementToHide2) {
            var currentQuestionInput = document.getElementById(currentQuestion);
            var elementToHide1 = document.getElementById(elementToHide1);
            var elementToHide2 = document.getElementById(elementToHide2);

            var questionValue = currentQuestionInput.value;

            if (questionValue === "4") {
                elementToHide1.classList.remove("hidden");
            } else if (questionValue === "5") {
                elementToHide2.classList.remove("hidden");

            } else {
                elementToHide1.classList.add("hidden");
                elementToHide2.classList.add("hidden");

            }
        }

        function toggleQuestionVisibility(currentQuestionId, nextQuestionId) {
            var currentQuestion = document.getElementById(currentQuestionId);
            var nextQuestion = document.getElementById(nextQuestionId);

            // Check if the current question has a value
            if (currentQuestion.value) {
                nextQuestion.classList.remove("hidden"); // Show the next question
            } else {
                nextQuestion.classList.add("hidden"); // Hide the next question
            }
        }


        function autocomplete(inp, arr) {
            /*the autocomplete function takes two arguments,
            the text field element and an array of possible autocompleted values:*/
            var currentFocus;
            /*execute a function when someone writes in the text field:*/
            inp.addEventListener("input", function (e) {
                var a, b, i, val = this.value;
                /*close any already open lists of autocompleted values*/
                closeAllLists();
                if (!val) {
                    return false;
                }
                currentFocus = -1;
                /*create a DIV element that will contain the items (values):*/
                a = document.createElement("DIV");
                a.setAttribute("id", this.id + "autocomplete-list");
                a.setAttribute("class", "autocomplete-items");
                /*append the DIV element as a child of the autocomplete container:*/
                this.parentNode.appendChild(a);
                /*for each item in the array...*/
                for (i = 0; i < arr.length; i++) {
                    /*check if the item starts with the same letters as the text field value:*/
                    if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
                        /*create a DIV element for each matching element:*/
                        b = document.createElement("DIV");
                        /*make the matching letters bold:*/
                        b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
                        b.innerHTML += arr[i].substr(val.length);
                        /*insert a input field that will hold the current array item's value:*/
                        b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
                        /*execute a function when someone clicks on the item value (DIV element):*/
                        b.addEventListener("click", function (e) {
                            /*insert the value for the autocomplete text field:*/
                            inp.value = this.getElementsByTagName("input")[0].value;
                            /*close the list of autocompleted values,
                            (or any other open lists of autocompleted values:*/
                            closeAllLists();
                        });
                        a.appendChild(b);
                    }
                }
            });
            /*execute a function presses a key on the keyboard:*/
            inp.addEventListener("keydown", function (e) {
                var x = document.getElementById(this.id + "autocomplete-list");
                if (x) x = x.getElementsByTagName("div");
                if (e.keyCode == 40) {
                    /*If the arrow DOWN key is pressed,
                    increase the currentFocus variable:*/
                    currentFocus++;
                    /*and and make the current item more visible:*/
                    addActive(x);
                } else if (e.keyCode == 38) { //up
                    /*If the arrow UP key is pressed,
                    decrease the currentFocus variable:*/
                    currentFocus--;
                    /*and and make the current item more visible:*/
                    addActive(x);
                } else if (e.keyCode == 13) {
                    /*If the ENTER key is pressed, prevent the form from being submitted,*/
                    e.preventDefault();
                    if (currentFocus > -1) {
                        /*and simulate a click on the "active" item:*/
                        if (x) x[currentFocus].click();
                    }
                }
            });

            function addActive(x) {
                /*a function to classify an item as "active":*/
                if (!x) return false;
                /*start by removing the "active" class on all items:*/
                removeActive(x);
                if (currentFocus >= x.length) currentFocus = 0;
                if (currentFocus < 0) currentFocus = (x.length - 1);
                /*add class "autocomplete-active":*/
                x[currentFocus].classList.add("autocomplete-active");
            }

            function removeActive(x) {
                /*a function to remove the "active" class from all autocomplete items:*/
                for (var i = 0; i < x.length; i++) {
                    x[i].classList.remove("autocomplete-active");
                }
            }

            function closeAllLists(elmnt) {
                /*close all autocomplete lists in the document,
                except the one passed as an argument:*/
                var x = document.getElementsByClassName("autocomplete-items");
                for (var i = 0; i < x.length; i++) {
                    if (elmnt != x[i] && elmnt != inp) {
                        x[i].parentNode.removeChild(x[i]);
                    }
                }
            }
            /*execute a function when someone clicks in the document:*/
            document.addEventListener("click", function (e) {
                closeAllLists(e.target);
            });
        }

        /*An array containing all the country names in the world:*/
        // var countries = ["Afghanistan", "Albania", "Algeria", "Andorra", "Angola", "Anguilla", "Antigua & Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia & Herzegovina", "Botswana", "Brazil", "British Virgin Islands", "Brunei", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands", "Central Arfrican Republic", "Chad", "Chile", "China", "Colombia", "Congo", "Cook Islands", "Costa Rica", "Cote D Ivoire", "Croatia", "Cuba", "Curacao", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands", "Faroe Islands", "Fiji", "Finland", "France", "French Polynesia", "French West Indies", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guam", "Guatemala", "Guernsey", "Guinea", "Guinea Bissau", "Guyana", "Haiti", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran", "Iraq", "Ireland", "Isle of Man", "Israel", "Italy", "Jamaica", "Japan", "Jersey", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Kosovo", "Kuwait", "Kyrgyzstan", "Laos", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libya", "Liechtenstein", "Lithuania", "Luxembourg", "Macau", "Macedonia", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Mauritania", "Mauritius", "Mexico", "Micronesia", "Moldova", "Monaco", "Mongolia", "Montenegro", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauro", "Nepal", "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "North Korea", "Norway", "Oman", "Pakistan", "Palau", "Palestine", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Poland", "Portugal", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russia", "Rwanda", "Saint Pierre & Miquelon", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Serbia", "Seychelles", "Sierra Leone", "Singapore", "Slovakia", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Korea", "South Sudan", "Spain", "Sri Lanka", "St Kitts & Nevis", "St Lucia", "St Vincent", "Sudan", "Suriname", "Swaziland", "Sweden", "Switzerland", "Syria", "Taiwan", "Tajikistan", "Tanzania", "Thailand", "Timor L'Este", "Togo", "Tonga", "Trinidad & Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks & Caicos", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States of America", "Uruguay", "Uzbekistan", "Vanuatu", "Vatican City", "Venezuela", "Vietnam", "Virgin Islands (US)", "Yemen", "Zambia", "Zimbabwe"];
        // var getUid = $(this).val();
        fetch('fetch_medications.php')
            .then(response => response.json())
            .then(data => {
                // Process the data received from the PHP script
                // console.log(data);
                autocomplete(document.getElementById("medication_name"), data);
            })
            .catch(error => {
                // Handle any errors that occurred during the fetch request
                console.error('Error:', error);
            });

        fetch('fetching_brand.php')
            .then(response => response.json())
            .then(data => {
                // Process the data received from the PHP script
                // console.log(data);
                autocomplete(document.getElementById("brand_id2"), data);
            })
            .catch(error => {
                // Handle any errors that occurred during the fetch request
                console.error('Error:', error);
            });


        fetch('fetching_batch.php')
            .then(response => response.json())
            .then(data => {
                // Process the data received from the PHP script
                // console.log(data);
                autocomplete(document.getElementById("batch_no"), data);
            })
            .catch(error => {
                // Handle any errors that occurred during the fetch request
                console.error('Error:', error);
            });

        fetch('fetching_manufacturer.php')
            .then(response => response.json())
            .then(data => {
                // Process the data received from the PHP script
                // console.log(data);
                autocomplete(document.getElementById("manufacturer"), data);
            })
            .catch(error => {
                // Handle any errors that occurred during the fetch request
                console.error('Error:', error);
            });

        /*initiate the autocomplete function on the "myInput" element, and pass along the countries array as possible autocomplete values:*/
        // autocomplete(document.getElementById("myInput"), countries);
    </script>
</body>

</html>

</html>