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
        } elseif (Input::get('delete_staff')) {
            $user->updateRecord('user', array(
                'status' => 0,
            ), Input::get('id'));
            $successMessage = 'User Deleted Successful';
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
                'participant_id' => array(
                    'required' => true,
                ),
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
                            'participant_id' => Input::get('participant_id'),
                            'clinic_date' => Input::get('clinic_date'),
                            'firstname' => Input::get('firstname'),
                            'midlename' => Input::get('midlename'),
                            'lastname' => Input::get('lastname'),
                            'dob' => Input::get('dob'),
                            'age' => $age,
                            'id_number' => Input::get('id_number'),
                            'gender' => Input::get('gender'),
                            'marital_status' => Input::get('marital_status'),
                            'education_level' => Input::get('education_level'),
                            'workplace' => Input::get('workplace'),
                            'occupation' => Input::get('occupation'),
                            'phone_number' => Input::get('phone_number'),
                            'other_phone' => Input::get('other_phone'),
                            'street' => Input::get('street'),
                            'ward' => Input::get('ward'),
                            'block_no' => Input::get('block_no'),
                            'client_image' => $image,
                            'comments' => Input::get('comments'),
                        ), Input::get('id'));

                        $successMessage = 'Client Updated Successful';
                    }
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
                if ((Input::get('wbc') >= 1.5 && Input::get('wbc') <= 11.0) && (Input::get('hb') >= 8.5 && Input::get('hb') <= 16.5)
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
                $url = 'info.php?id=3&sid=' . Input::get('site');
                Redirect::to($url);
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_Inclusion')) {
            $validate = $validate->check($_POST, array());
            if ($validate->passed()) {
                $eligibility = 0;
                $clnt = $override->get('clients', 'id', Input::get('cid'))[0];
                $sc_e = $override->get('screening', 'client_id', Input::get('cid'))[0];
                $std_id = $override->getNews('study_id', 'site_id', $user->data()->site_id, 'status', 0)[0];
                if (
                    Input::get('age_18') == 1 && Input::get('biopsy') == 1 && Input::get('breast_cancer') == 1 && Input::get('brain_cancer') == 1
                    && Input::get('consented') == 1
                ) {
                    print_r($eligibility);
                    $eligibility = 1;
                    if ($clnt['gender'] == 'male' && Input::get('prostate_cancer') == 1 && $sc_e['eligibility'] == 1) {
                        $eligibility = 1;
                        if ($override->getCount('visit', 'client_id', Input::get('cid')) == 1) {
                            $user->visit(Input::get('cid'), 0);
                            $user->updateRecord('study_id', array('status' => 1, 'client_id' => Input::get('cid')), $std_id['id']);
                            $user->updateRecord('clients', array('study_id' => $std_id['study_id'], 'enrolled' => 1), Input::get('cid'));
                        }
                    } elseif ($clnt['gender'] == 'female' && Input::get('cervical_cancer') == 1 && $sc_e['eligibility'] == 1) {
                        print_r($eligibility);

                        $eligibility = 1;
                        if ($override->getCount('visit', 'client_id', Input::get('cid')) == 1) {
                            $user->visit(Input::get('cid'), 0);
                            $user->updateRecord('study_id', array('status' => 1, 'client_id' => Input::get('cid')), $std_id['id']);
                            $user->updateRecord('clients', array('study_id' => $std_id['study_id'], 'enrolled' => 1), Input::get('cid'));
                        }
                    }
                }
                try {
                    if ($override->get('screening', 'client_id', Input::get('cid'))) {
                        $cl_id = $override->get('screening', 'client_id', Input::get('cid'))[0]['id'];
                        $user->updateRecord('screening', array(
                            'age_18' => Input::get('age_18'),
                            'biopsy' => Input::get('biopsy'),
                            'breast_cancer' => Input::get('breast_cancer'),
                            'brain_cancer' => Input::get('brain_cancer'),
                            'cervical_cancer' => Input::get('cervical_cancer'),
                            'prostate_cancer' => Input::get('prostate_cancer'),
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
                            'age_18' => Input::get('age_18'),
                            'biopsy' => Input::get('biopsy'),
                            'breast_cancer' => Input::get('breast_cancer'),
                            'brain_cancer' => Input::get('brain_cancer'),
                            'cervical_cancer' => Input::get('cervical_cancer'),
                            'prostate_cancer' => Input::get('prostate_cancer'),
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
                    $successMessage = 'Inclusion Successful Added';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_Exclusion')) {
            $validate = $validate->check($_POST, array());
            if ($validate->passed()) {
                $eligibility = 0;
                $clnt = $override->get('clients', 'id', Input::get('cid'))[0];
                $sc_e = $override->get('screening', 'client_id', Input::get('cid'))[0];
                $std_id = $override->getNews('study_id', 'site_id', $user->data()->site_id, 'status', 0)[0];
                if (((Input::get('pregnant') == 2 || Input::get('pregnant') == 3) && Input::get('breast_feeding') == 2 && Input::get('cdk') == 2 && Input::get('liver_disease') == 2)) {
                    if (Input::get('pregnant') == 2 && Input::get('breast_feeding') == 2 && Input::get('cdk') == 2 && Input::get('liver_disease') == 2 && $sc_e['eligibility'] == 1) {
                        $eligibility = 1;
                        if ($override->getCount('visit', 'client_id', Input::get('cid')) == 1) {
                            $user->visit(Input::get('cid'), 0);
                            $user->updateRecord('study_id', array('status' => 1, 'client_id' => Input::get('cid')), $std_id['id']);
                            $user->updateRecord('clients', array('study_id' => $std_id['study_id'], 'enrolled' => 1), Input::get('cid'));
                        }
                    } elseif ((Input::get('pregnant') == 2 || Input::get('pregnant') == 3) && Input::get('breast_feeding') == 2 && Input::get('cdk') == 2 && Input::get('liver_disease') == 2 && $sc_e['eligibility'] == 1) {
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
                            'pregnant' => Input::get('pregnant'),
                            'breast_feeding' => Input::get('breast_feeding'),
                            'cdk' => Input::get('cdk'),
                            'liver_disease' => Input::get('liver_disease'),
                            'eligibility' => $eligibility,
                            'created_on' => date('Y-m-d'),
                            'staff_id' => $user->data()->id,
                            'site_id' => $user->data()->site_id,
                            'status' => 1,
                            'client_id' => Input::get('cid'),
                        ), $l_id);
                    } else {
                        $user->createRecord('lab', array(
                            'pregnant' => Input::get('pregnant'),
                            'breast_feeding' => Input::get('breast_feeding'),
                            'cdk' => Input::get('cdk'),
                            'liver_disease' => Input::get('liver_disease'),
                            'eligibility' => $eligibility,
                            'created_on' => date('Y-m-d'),
                            'staff_id' => $user->data()->id,
                            'site_id' => $user->data()->site_id,
                            'status' => 1,
                            'client_id' => Input::get('cid'),
                        ));
                    }

                    $successMessage = 'Exclusion Successful Added';
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
                        'vid' => $_GET['vid'],
                        'vcode' => $_GET['vcode'],
                        'crf2_date' => Input::get('crf2_date'),
                        'height' => Input::get('height'),
                        'weight' => Input::get('weight'),
                        'bmi' => Input::get('bmi'),
                        'time' => Input::get('time'),
                        'temperature' => Input::get('temperature'),
                        'method' => Input::get('method'),
                        'oxygen' => Input::get('oxygen'),
                        'on_oxygen' => Input::get('on_oxygen'),
                        'days_on_oxygen' => Input::get('days_on_oxygen'),
                        'cylinder_used' => Input::get('cylinder_used'),
                        'respiratory_rate' => Input::get('respiratory_rate'),
                        'heart_rate' => Input::get('heart_rate'),
                        'systolic' => Input::get('systolic'),
                        'diastolic' => Input::get('diastolic'),
                        'method2' => Input::get('method2'),
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
                    Redirect::to('info.php?id=6&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode']);
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
                        'vid' => $_GET["vid"],
                        'vcode' => $_GET["vcode"],
                        'crf3_date' => Input::get('crf3_date'),
                        'fever' => Input::get('fever'),
                        'vomiting' => Input::get('vomiting'),
                        'diarrhoea' => Input::get('diarrhoea'),
                        'headaches' => Input::get('headaches'),
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
                        'crf3_cmpltd_date' => Input::get('crf3_cmpltd_date'),
                        'patient_id' => $_GET['cid'],
                        'staff_id' => $user->data()->id,
                        'status' => 1,
                        'created_on' => date('Y-m-d'),
                        'site_id' => $user->data()->site_id,
                    ), Input::get('id'));
                    $successMessage = 'CRF3 updated Successful';
                    Redirect::to('info.php?id=6&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode']);
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
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
    <title> Info - NIMREGENIN </title>
    <?php include "head.php"; ?>
</head>

<body>
    <div class="wrapper">

        <?php include 'topbar.php' ?>
        <?php include 'menu.php' ?>
        <div class="content">


            <div class="breadLine">
                <ul class="breadcrumb">
                    <li><a href="#">Info</a> <span class="divider">></span></li>
                </ul>
                <?php include 'pageInfo.php' ?>
            </div>

            <div class="workplace">
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

                <div class="row">
                    <?php if ($_GET['id'] == 1 && ($user->data()->position == 1 || $user->data()->position == 2)) { ?>
                        <div class="col-md-12">
                            <div class="head clearfix">
                                <div class="isw-grid"></div>
                                <h1>List of Staff</h1>
                                <ul class="buttons">
                                    <li><a href="#" class="isw-download"></a></li>
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
                            <div class="block-fluid">
                                <?php if ($user->data()->power == 1) {
                                    $user = $override->get('user', 'status', 1);
                                } else {
                                    $users = $override->getNews('user', 'site_id', $user->data()->site_id, 'status', 1);
                                } ?>
                                <table cellpadding="0" cellspacing="0" width="100%" class="table">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" name="checkall" /></th>
                                            <th width="20%">Name</th>
                                            <th width="20%">Username</th>
                                            <th width="20%">Position</th>
                                            <th width="20%">Site</th>
                                            <th width="20%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($override->get('user', 'status', 1) as $staff) {
                                            $position = $override->get('position', 'id', $staff['position'])[0];
                                            $site = $override->get('site', 'id', $staff['site_id'])[0] ?>
                                            <tr>
                                                <td><input type="checkbox" name="checkbox" /></td>
                                                <td> <?= $staff['firstname'] . ' ' . $staff['lastname'] ?></td>
                                                <td><?= $staff['username'] ?></td>
                                                <td><?= $position['name'] ?></td>
                                                <td><?= $site['name'] ?></td>
                                                <td>
                                                    <a href="#user<?= $staff['id'] ?>" role="button" class="btn btn-info" data-toggle="modal">Edit</a>
                                                    <a href="#reset<?= $staff['id'] ?>" role="button" class="btn btn-warning" data-toggle="modal">Reset</a>
                                                    <a href="#unlock<?= $staff['id'] ?>" role="button" class="btn btn-default" data-toggle="modal">Unlock</a>
                                                    <a href="#delete<?= $staff['id'] ?>" role="button" class="btn btn-danger" data-toggle="modal">Delete</a>
                                                </td>

                                            </tr>
                                            <div class="modal fade" id="user<?= $staff['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <form method="post">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                <h4>Edit User Info</h4>
                                                            </div>
                                                            <div class="modal-body modal-body-np">
                                                                <div class="row">
                                                                    <div class="block-fluid">
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">First name:</div>
                                                                            <div class="col-md-9"><input type="text" name="firstname" value="<?= $staff['firstname'] ?>" required /></div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Last name:</div>
                                                                            <div class="col-md-9"><input type="text" name="lastname" value="<?= $staff['lastname'] ?>" required /></div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Position</div>
                                                                            <div class="col-md-9">
                                                                                <select name="position" style="width: 100%;" required>
                                                                                    <option value="<?= $position['id'] ?>"><?= $position['name'] ?></option>
                                                                                    <?php foreach ($override->getData('position') as $position) { ?>
                                                                                        <option value="<?= $position['id'] ?>"><?= $position['name'] ?></option>
                                                                                    <?php } ?>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Phone Number:</div>
                                                                            <div class="col-md-9"><input value="<?= $staff['phone_number'] ?>" class="" type="text" name="phone_number" id="phone" required /> <span>Example: 0700 000 111</span></div>
                                                                        </div>

                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">E-mail Address:</div>
                                                                            <div class="col-md-9"><input value="<?= $staff['email_address'] ?>" class="validate[required,custom[email]]" type="text" name="email_address" id="email" /> <span>Example: someone@nowhere.com</span></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="dr"><span></span></div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <input type="hidden" name="id" value="<?= $staff['id'] ?>">
                                                                <input type="submit" name="edit_staff" value="Save updates" class="btn btn-warning">
                                                                <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="modal fade" id="reset<?= $staff['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <form method="post">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                <h4>Reset Password</h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Are you sure you want to reset password to default (12345678)</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <input type="hidden" name="id" value="<?= $staff['id'] ?>">
                                                                <input type="submit" name="reset_pass" value="Reset" class="btn btn-warning">
                                                                <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="modal fade" id="unlock<?= $staff['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <form method="post">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                <h4>Unlock Account</h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Are you sure you want to unlock this account </p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <input type="hidden" name="id" value="<?= $staff['id'] ?>">
                                                                <input type="submit" name="unlock_account" value="Unlock" class="btn btn-warning">
                                                                <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="modal fade" id="delete<?= $staff['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <form method="post">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                <h4>Delete User</h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <strong style="font-weight: bold;color: red">
                                                                    <p>Are you sure you want to delete this user</p>
                                                                </strong>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <input type="hidden" name="id" value="<?= $staff['id'] ?>">
                                                                <input type="submit" name="delete_staff" value="Delete" class="btn btn-danger">
                                                                <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php } elseif ($_GET['id'] == 2 && $user->data()->accessLevel == 1) { ?>
                        <div class="col-md-6">
                            <div class="head clearfix">
                                <div class="isw-grid"></div>
                                <h1>List of Positions</h1>
                                <ul class="buttons">
                                    <li><a href="#" class="isw-download"></a></li>
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
                            <div class="block-fluid">
                                <table cellpadding="0" cellspacing="0" width="100%" class="table">
                                    <thead>
                                        <tr>
                                            <th width="25%">Name</th>
                                            <th width="5%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($override->getData('position') as $position) { ?>
                                            <tr>
                                                <td> <?= $position['name'] ?></td>
                                                <td><a href="#position<?= $position['id'] ?>" role="button" class="btn btn-info" data-toggle="modal">Edit</a></td>
                                                <!-- EOF Bootrstrap modal form -->
                                            </tr>
                                            <div class="modal fade" id="position<?= $position['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form method="post">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                <h4>Edit Position Info</h4>
                                                            </div>
                                                            <div class="modal-body modal-body-np">
                                                                <div class="row">
                                                                    <div class="block-fluid">
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Name:</div>
                                                                            <div class="col-md-9"><input type="text" name="name" value="<?= $position['name'] ?>" required /></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="dr"><span></span></div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <input type="hidden" name="id" value="<?= $position['id'] ?>">
                                                                <input type="submit" name="edit_position" class="btn btn-warning" value="Save updates">
                                                                <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="head clearfix">
                                <div class="isw-grid"></div>
                                <h1>Studies</h1>
                                <ul class="buttons">
                                    <li><a href="#" class="isw-download"></a></li>
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
                            <div class="block-fluid">
                                <table cellpadding="0" cellspacing="0" width="100%" class="table">
                                    <thead>
                                        <tr>
                                            <th width="30%">Name</th>
                                            <th width="10%">Code</th>
                                            <th width="10%">Sample Size</th>
                                            <th width="15%">Start Date</th>
                                            <th width="15%">End Date</th>
                                            <th width="10%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($override->getData('study') as $study) { ?>
                                            <tr>
                                                <td><?= $study['name'] ?></td>
                                                <td><?= $study['code'] ?></td>
                                                <td><?= $study['sample_size'] ?></td>
                                                <td><?= $study['start_date'] ?></td>
                                                <td><?= $study['end_date'] ?></td>
                                                <td><a href="#study<?= $study['id'] ?>" role="button" class="btn btn-info" data-toggle="modal">Edit</a></td>
                                                <!-- EOF Bootrstrap modal form -->
                                            </tr>
                                            <div class="modal fade" id="study<?= $study['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <form method="post">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                <h4>Edit Info</h4>
                                                            </div>
                                                            <div class="modal-body modal-body-np">
                                                                <div class="row">
                                                                    <div class="row-form clearfix">
                                                                        <div class="col-md-3">Name:</div>
                                                                        <div class="col-md-9">
                                                                            <input value="<?= $study['name'] ?>" class="validate[required]" type="text" name="name" id="name" />
                                                                        </div>
                                                                    </div>
                                                                    <div class="row-form clearfix">
                                                                        <div class="col-md-3">Code:</div>
                                                                        <div class="col-md-9">
                                                                            <input value="<?= $study['code'] ?>" class="validate[required]" type="text" name="code" id="code" />
                                                                        </div>
                                                                    </div>
                                                                    <div class="row-form clearfix">
                                                                        <div class="col-md-3">Sample Size:</div>
                                                                        <div class="col-md-9">
                                                                            <input value="<?= $study['sample_size'] ?>" class="validate[required]" type="number" name="sample_size" id="sample_size" />
                                                                        </div>
                                                                    </div>
                                                                    <div class="row-form clearfix">
                                                                        <div class="col-md-3">Start Date:</div>
                                                                        <div class="col-md-9">
                                                                            <input value="<?= $study['start_date'] ?>" class="validate[required,custom[date]]" type="text" name="start_date" id="start_date" /> <span>Example: 2010-12-01</span>
                                                                        </div>
                                                                    </div>

                                                                    <div class="row-form clearfix">
                                                                        <div class="col-md-3">End Date:</div>
                                                                        <div class="col-md-9">
                                                                            <input value="<?= $study['end_date'] ?>" class="validate[required,custom[date]]" type="text" name="end_date" id="end_date" /> <span>Example: 2010-12-01</span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="dr"><span></span></div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <input type="hidden" name="id" value="<?= $study['id'] ?>">
                                                                <input type="submit" name="edit_study" class="btn btn-warning" value="Save updates">
                                                                <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="head clearfix">
                                <div class="isw-grid"></div>
                                <h1>List of Sites</h1>
                                <ul class="buttons">
                                    <li><a href="#" class="isw-download"></a></li>
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
                            <div class="block-fluid">
                                <table cellpadding="0" cellspacing="0" width="100%" class="table">
                                    <thead>
                                        <tr>
                                            <th width="25%">Name</th>
                                            <th width="5%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($override->getData('site') as $site) { ?>
                                            <tr>
                                                <td> <?= $site['name'] ?></td>
                                                <td><a href="#site<?= $site['id'] ?>" role="button" class="btn btn-info" data-toggle="modal">Edit</a></td>
                                                <!-- EOF Bootrstrap modal form -->
                                            </tr>
                                            <div class="modal fade" id="site<?= $site['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form method="post">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                <h4>Edit Site Info</h4>
                                                            </div>
                                                            <div class="modal-body modal-body-np">
                                                                <div class="row">
                                                                    <div class="block-fluid">
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Name:</div>
                                                                            <div class="col-md-9"><input type="text" name="name" value="<?= $site['name'] ?>" required /></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="dr"><span></span></div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <input type="hidden" name="id" value="<?= $site['id'] ?>">
                                                                <input type="submit" name="edit_site" class="btn btn-warning" value="Save updates">
                                                                <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php } elseif ($_GET['id'] == 3) { ?>
                        <div class="col-md-12">
                            <?php if ($user->data()->power == 1) { ?>
                                <div class="head clearfix">
                                    <div class="isw-ok"></div>
                                    <h1>Search by Site</h1>
                                </div>
                                <div class="block-fluid">
                                    <form id="validation" method="post">
                                        <div class="row-form clearfix">
                                            <div class="col-md-1">Site:</div>
                                            <div class="col-md-4">
                                                <select name="site" required>
                                                    <option value="">Select Site</option>
                                                    <?php foreach ($override->getData('site') as $site) { ?>
                                                        <option value="<?= $site['id'] ?>"><?= $site['name'] ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <input type="submit" name="search_by_site" value="Search" class="btn btn-info">
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            <?php } ?>
                            <div class="head clearfix">
                                <div class="isw-grid"></div>
                                <h1>List of Clients</h1>
                                <ul class="buttons">
                                    <li><a href="#" class="isw-download"></a></li>
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
                                if ($_GET['sid'] != null) {
                                    $pagNum = 0;
                                    $pagNum = $override->countData('clients', 'status', 1, 'site_id', $_GET['sid']);
                                    $pages = ceil($pagNum / $numRec);
                                    if (!$_GET['page'] || $_GET['page'] == 1) {
                                        $page = 0;
                                    } else {
                                        $page = ($_GET['page'] * $numRec) - $numRec;
                                    }
                                    $clients = $override->getWithLimit1('clients', 'site_id', $_GET['sid'], 'status', 1, $page, $numRec);
                                } else {
                                    $pagNum = 0;
                                    $pagNum = $override->getCount('clients', 'status', 1);
                                    $pages = ceil($pagNum / $numRec);
                                    if (!$_GET['page'] || $_GET['page'] == 1) {
                                        $page = 0;
                                    } else {
                                        $page = ($_GET['page'] * $numRec) - $numRec;
                                    }
                                    $clients = $override->getWithLimit('clients', 'status', 1, $page, $numRec);
                                }
                            } else {
                                $pagNum = 0;
                                $pagNum = $override->countData('clients', 'site_id', $user->data()->site_id, 'status', 1);
                                $pages = ceil($pagNum / $numRec);
                                if (!$_GET['page'] || $_GET['page'] == 1) {
                                    $page = 0;
                                } else {
                                    $page = ($_GET['page'] * $numRec) - $numRec;
                                }
                                $clients = $override->getWithLimit1('clients', 'site_id', $user->data()->site_id, 'status', 1, $page, $numRec);
                            } ?>
                            <div class="block-fluid">
                                <table cellpadding="0" cellspacing="0" width="100%" class="table">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" name="checkall" /></th>
                                            <td width="20">#</td>
                                            <th width="40">Picture</th>
                                            <th width="20%">ParticipantID</th>
                                            <th width="10%">Name</th>
                                            <th width="10%">Gender</th>
                                            <th width="10%">Age</th>
                                            <th width="40%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $x = 1;
                                        foreach ($clients as $client) { ?>
                                            <tr>
                                                <td><input type="checkbox" name="checkbox" /></td>
                                                <td><?= $x ?></td>
                                                <td width="100">
                                                    <?php if ($client['client_image'] != '' || is_null($client['client_image'])) {
                                                        $img = $client['client_image'];
                                                    } else {
                                                        $img = 'img/users/blank.png';
                                                    } ?>
                                                    <a href="#img<?= $client['id'] ?>" data-toggle="modal"><img src="<?= $img ?>" width="90" height="90" class="" /></a>
                                                </td>
                                                <td><?= $client['participant_id'] ?></td>
                                                <td> <?= $client['firstname'] . ' ' . $client['lastname'] ?></td>
                                                <td><?= $client['gender'] ?></td>
                                                <td><?= $client['age'] ?></td>
                                                <td>
                                                    <a href="#clientView<?= $client['id'] ?>" role="button" class="btn btn-default" data-toggle="modal">View</a>
                                                    <a href="#client<?= $client['id'] ?>" role="button" class="btn btn-info" data-toggle="modal">Edit</a>
                                                    <a href="id.php?cid=<?= $client['id'] ?>" class="btn btn-warning">Patient ID</a>
                                                    <!-- <a href="info.php?id=6&cid=<?= $client['id'] ?>" role="button" class="btn btn-success">Study CRF</a> -->
                                                    <?php if ($user->data()->accessLevel == 1) { ?>
                                                        <a href="#delete<?= $client['id'] ?>" role="button" class="btn btn-danger" data-toggle="modal">Delete</a>
                                                    <?php } ?>
                                                    <!-- <a href="info.php?id=4&cid=<?= $client['id'] ?>" role="button" class="btn btn-warning">Schedule</a> -->
                                                    <a href="info.php?id=7&cid=<?= $client['id'] ?>" role="button" class="btn btn-warning">Schedule</a>
                                                </td>

                                            </tr>
                                            <div class="modal fade" id="clientView<?= $client['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <form method="post">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                <h4>Edit Client View</h4>
                                                            </div>
                                                            <div class="modal-body modal-body-np">
                                                                <div class="row">
                                                                    <div class="block-fluid">
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-2">Study</div>
                                                                            <div class="col-md-6">
                                                                                <select name="position" style="width: 100%;" disabled>
                                                                                    <?php foreach ($override->getData('study') as $study) { ?>
                                                                                        <option value="<?= $study['id'] ?>"><?= $study['name'] ?></option>
                                                                                    <?php } ?>
                                                                                </select>
                                                                            </div>
                                                                            <div class="col-md-4 pull-right">
                                                                                <img src="<?= $img ?>" class="img-thumbnail" width="50%" height="50%" />
                                                                            </div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">ParticipantID:</div>
                                                                            <div class="col-md-9">
                                                                                <input value="<?= $client['participant_id'] ?>" class="validate[required]" type="text" name="participant_id" id="participant_id" disabled />
                                                                            </div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Date:</div>
                                                                            <div class="col-md-9">
                                                                                <input value="<?= $client['clinic_date'] ?>" class="validate[required,custom[date]]" type="text" name="clinic_date" id="clinic_date" disabled /> <span>Example: 2010-12-01</span>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">First Name:</div>
                                                                            <div class="col-md-9">
                                                                                <input value="<?= $client['firstname'] ?>" class="validate[required]" type="text" name="firstname" id="firstname" disabled />
                                                                            </div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Middle Name:</div>
                                                                            <div class="col-md-9">
                                                                                <input value="<?= $client['middlename'] ?>" class="validate[required]" type="text" name="middlename" id="middlename" disabled />
                                                                            </div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Last Name:</div>
                                                                            <div class="col-md-9">
                                                                                <input value="<?= $client['lastname'] ?>" class="validate[required]" type="text" name="lastname" id="lastname" disabled />
                                                                            </div>
                                                                        </div>

                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Date of Birth:</div>
                                                                            <div class="col-md-9">
                                                                                <input value="<?= $client['dob'] ?>" class="validate[required,custom[date]]" type="text" name="dob" id="dob" disabled /> <span>Example: 2010-12-01</span>
                                                                            </div>
                                                                        </div>

                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Age:</div>
                                                                            <div class="col-md-9">
                                                                                <input value="<?= $client['age'] ?>" class="validate[required]" type="text" name="age" id="age" disabled />
                                                                            </div>
                                                                        </div>

                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Initials:</div>
                                                                            <div class="col-md-9">
                                                                                <input value="<?= $client['initials'] ?>" class="validate[required]" type="text" name="initials" id="initials" disabled />
                                                                            </div>
                                                                        </div>

                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Gender</div>
                                                                            <div class="col-md-9">
                                                                                <select name="gender" style="width: 100%;" disabled>
                                                                                    <option value="<?= $client['gender'] ?>"><?= $client['gender'] ?></option>
                                                                                    <option value="male">Male</option>
                                                                                    <option value="female">Female</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>

                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Hospital ID:</div>
                                                                            <div class="col-md-9">
                                                                                <input value="<?= $client['id_number'] ?>" class="validate[required]" type="text" name="id_number" id="id_number" disabled />
                                                                            </div>
                                                                        </div>

                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Marital Status</div>
                                                                            <div class="col-md-9">
                                                                                <select name="marital_status" style="width: 100%;" disabled>
                                                                                    <option value="<?= $client['marital_status'] ?>"><?= $client['marital_status'] ?></option>
                                                                                    <option value="Single">Single</option>
                                                                                    <option value="Married">Married</option>
                                                                                    <option value="Divorced">Divorced</option>
                                                                                    <option value="Separated">Separated</option>
                                                                                    <option value="Widower">Widower/Widow</option>
                                                                                    <option value="Cohabit">Cohabit</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>

                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Education Level</div>
                                                                            <div class="col-md-9">
                                                                                <select name="education_level" style="width: 100%;" disabled>
                                                                                    <option value="<?= $client['education_level'] ?>"><?= $client['education_level'] ?></option>
                                                                                    <option value="Not attended school">Not attended school</option>
                                                                                    <option value="Primary">Primary</option>
                                                                                    <option value="Secondary">Secondary</option>
                                                                                    <option value="Certificate">Certificate</option>
                                                                                    <option value="Diploma">Diploma</option>
                                                                                    <option value="Undergraduate degree">Undergraduate degree</option>
                                                                                    <option value="Postgraduate degree">Postgraduate degree</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Workplace/station site:</div>
                                                                            <div class="col-md-9"><input value="<?= $client['workplace'] ?>" class="" type="text" name="workplace" id="workplace" disabled /></div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Occupation:</div>
                                                                            <div class="col-md-9"><input value="<?= $client['occupation'] ?>" class="" type="text" name="occupation" id="occupation" disabled /></div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Phone Number:</div>
                                                                            <div class="col-md-9"><input value="<?= $client['phone_number'] ?>" class="" type="text" name="phone_number" id="phone" disabled /> <span>Example: 0700 000 111</span></div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Relative's Phone Number:</div>
                                                                            <div class="col-md-9"><input value="<?= $client['other_phone'] ?>" class="" type="text" name="other_phone" id="phone" disabled /> <span>Example: 0700 000 111</span></div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Residence Street:</div>
                                                                            <div class="col-md-9"><input value="<?= $client['street'] ?>" class="" type="text" name="street" id="street" disabled /></div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Ward:</div>
                                                                            <div class="col-md-9"><input value="<?= $client['ward'] ?>" class="" type="text" name="ward" id="ward" disabled /></div>
                                                                        </div>

                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">House Number:</div>
                                                                            <div class="col-md-9"><input value="<?= $client['block_no'] ?>" class="" type="text" name="block_no" id="block_no" disabled /></div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Comments:</div>
                                                                            <div class="col-md-9"><textarea name="comments" rows="4" disabled><?= $client['comments'] ?></textarea> </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="dr"><span></span></div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <input type="hidden" name="id" value="<?= $client['id'] ?>">
                                                                <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="modal fade" id="client<?= $client['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <form id="validation" enctype="multipart/form-data" method="post">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                <h4>Edit Client Info</h4>
                                                            </div>
                                                            <div class="modal-body modal-body-np">
                                                                <div class="row">
                                                                    <div class="block-fluid">
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Study</div>
                                                                            <div class="col-md-9">
                                                                                <select name="position" style="width: 100%;" required>
                                                                                    <?php foreach ($override->getData('study') as $study) { ?>
                                                                                        <option value="<?= $study['id'] ?>"><?= $study['name'] ?></option>
                                                                                    <?php } ?>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">ParticipantID:</div>
                                                                            <div class="col-md-9">
                                                                                <input value="<?= $client['participant_id'] ?>" class="validate[required]" type="text" name="participant_id" id="participant_id" />
                                                                            </div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Date:</div>
                                                                            <div class="col-md-9">
                                                                                <input value="<?= $client['clinic_date'] ?>" class="validate[required,custom[date]]" type="text" name="clinic_date" id="clinic_date" /> <span>Example: 2010-12-01</span>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">First Name:</div>
                                                                            <div class="col-md-9">
                                                                                <input value="<?= $client['firstname'] ?>" class="validate[required]" type="text" name="firstname" id="firstname" />
                                                                            </div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Middle Name:</div>
                                                                            <div class="col-md-9">
                                                                                <input value="<?= $client['middlename'] ?>" class="validate[required]" type="text" name="middlename" id="middlename" />
                                                                            </div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Last Name:</div>
                                                                            <div class="col-md-9">
                                                                                <input value="<?= $client['lastname'] ?>" class="validate[required]" type="text" name="lastname" id="lastname" />
                                                                            </div>
                                                                        </div>

                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Date of Birth:</div>
                                                                            <div class="col-md-9">
                                                                                <input value="<?= $client['dob'] ?>" class="validate[required,custom[date]]" type="text" name="dob" id="dob" /> <span>Example: 2010-12-01</span>
                                                                            </div>
                                                                        </div>

                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Age:</div>
                                                                            <div class="col-md-9">
                                                                                <input value="<?= $client['age'] ?>" class="validate[required]" type="text" name="age" id="age" disabled />
                                                                            </div>
                                                                        </div>

                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Initials:</div>
                                                                            <div class="col-md-9">
                                                                                <input value="<?= $client['initials'] ?>" class="validate[required]" type="text" name="initials" id="initials" />
                                                                            </div>
                                                                        </div>

                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-5">Client Image:</div>
                                                                            <div class="col-md-7">
                                                                                <input type="file" id="image" name="image" />
                                                                            </div>
                                                                        </div>

                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Gender</div>
                                                                            <div class="col-md-9">
                                                                                <select name="gender" style="width: 100%;" required>
                                                                                    <option value="<?= $client['gender'] ?>"><?= $client['gender'] ?></option>
                                                                                    <option value="male">Male</option>
                                                                                    <option value="female">Female</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Hospital ID:</div>
                                                                            <div class="col-md-9">
                                                                                <input value="<?= $client['id_number'] ?>" class="validate[required]" type="text" name="id_number" id="id_number" />
                                                                            </div>
                                                                        </div>


                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Marital Status</div>
                                                                            <div class="col-md-9">
                                                                                <select name="marital_status" style="width: 100%;" required>
                                                                                    <option value="<?= $client['marital_status'] ?>"><?= $client['marital_status'] ?></option>
                                                                                    <option value="Single">Single</option>
                                                                                    <option value="Married">Married</option>
                                                                                    <option value="Divorced">Divorced</option>
                                                                                    <option value="Separated">Separated</option>
                                                                                    <option value="Widower">Widower/Widow</option>
                                                                                    <option value="Cohabit">Cohabit</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>

                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Education Level</div>
                                                                            <div class="col-md-9">
                                                                                <select name="education_level" style="width: 100%;" required>
                                                                                    <option value="<?= $client['education_level'] ?>"><?= $client['education_level'] ?></option>
                                                                                    <option value="Not attended school">Not attended school</option>
                                                                                    <option value="Primary">Primary</option>
                                                                                    <option value="Secondary">Secondary</option>
                                                                                    <option value="Certificate">Certificate</option>
                                                                                    <option value="Diploma">Diploma</option>
                                                                                    <option value="Undergraduate degree">Undergraduate degree</option>
                                                                                    <option value="Postgraduate degree">Postgraduate degree</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Workplace/station site:</div>
                                                                            <div class="col-md-9"><input value="<?= $client['workplace'] ?>" class="" type="text" name="workplace" id="workplace" required /></div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Occupation:</div>
                                                                            <div class="col-md-9"><input value="<?= $client['occupation'] ?>" class="" type="text" name="occupation" id="occupation" required /></div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Phone Number:</div>
                                                                            <div class="col-md-9"><input value="<?= $client['phone_number'] ?>" class="" type="text" name="phone_number" id="phone" required /> <span>Example: 0700 000 111</span></div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Relative's Phone Number:</div>
                                                                            <div class="col-md-9"><input value="<?= $client['other_phone'] ?>" class="" type="text" name="other_phone" id="other_phone" /> <span>Example: 0700 000 111</span></div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Residence Street:</div>
                                                                            <div class="col-md-9"><input value="<?= $client['street'] ?>" class="" type="text" name="street" id="street" required /></div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Ward:</div>
                                                                            <div class="col-md-9"><input value="<?= $client['ward'] ?>" class="" type="text" name="ward" id="ward" required /></div>
                                                                        </div>

                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">House Number:</div>
                                                                            <div class="col-md-9"><input value="<?= $client['block_no'] ?>" class="" type="text" name="block_no" id="block_no" /></div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Comments:</div>
                                                                            <div class="col-md-9"><textarea name="comments" rows="4"><?= $client['comments'] ?></textarea> </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="dr"><span></span></div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <input type="hidden" name="client_image" value="<?= $client['client_image'] ?>" />
                                                                <input type="hidden" name="id" value="<?= $client['id'] ?>">
                                                                <input type="submit" name="edit_client" value="Save updates" class="btn btn-warning">
                                                                <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="modal fade" id="delete<?= $client['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <form method="post">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                <h4>Delete User</h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <strong style="font-weight: bold;color: red">
                                                                    <p>Are you sure you want to delete this user</p>
                                                                </strong>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <input type="hidden" name="id" value="<?= $client['id'] ?>">
                                                                <input type="submit" name="delete_staff" value="Delete" class="btn btn-danger">
                                                                <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="modal fade" id="img<?= $client['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <form method="post">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                <h4>Client Image</h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <img src="<?= $img ?>" width="350">
                                                            </div>
                                                            <div class="modal-footer">
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
                            </div>
                            <div class="pull-right">
                                <div class="btn-group">
                                    <a href="info.php?id=3&sid=&page=<?php if (($_GET['page'] - 1) > 0) {
                                                                            echo $_GET['page'] - 1;
                                                                        } else {
                                                                            echo 1;
                                                                        } ?>" class="btn btn-default">
                                        < </a>
                                            <?php for ($i = 1; $i <= $pages; $i++) { ?>
                                                <a href="info.php?id=3&sid=&page=<?= $_GET['id'] ?>&page=<?= $i ?>" class="btn btn-default <?php if ($i == $_GET['page']) {
                                                                                                                                                echo 'active';
                                                                                                                                            } ?>"><?= $i ?></a>
                                            <?php } ?>
                                            <a href="info.php?id=3&sid=&page=<?php if (($_GET['page'] + 1) <= $pages) {
                                                                                    echo $_GET['page'] + 1;
                                                                                } else {
                                                                                    echo $i - 1;
                                                                                } ?>" class="btn btn-default"> > </a>
                                </div>
                            </div>
                        </div>
                    <?php } elseif ($_GET['id'] == 4) { ?>
                        <div class="col-md-12">
                            <?php $patient = $override->get('clients', 'id', $_GET['cid'])[0] ?>
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="ucard clearfix">
                                        <div class="right">
                                            <div class="image">
                                                <?php if ($patient['client_image'] != '' || is_null($patient['client_image'])) {
                                                    $img = $patient['client_image'];
                                                } else {
                                                    $img = 'img/users/blank.png';
                                                } ?>
                                                <a href="#"><img src="<?= $img ?>" width="300" class="img-thumbnail"></a>
                                            </div>
                                            <h5><?= 'Name: ' . $patient['firstname'] . ' ' . $patient['lastname'] . ' Age: ' . $patient['age'] ?></h5>
                                            <h4><strong style="font-size: medium">Screening ID: <?= $patient['participant_id'] ?></strong></h4>
                                            <h4><strong style="font-size: larger">Study ID: <?= $patient['study_id'] ?></strong></h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-10">
                                    <div class="head clearfix">
                                        <div class="isw-grid"></div>
                                        <h1>Schedule</h1>
                                        <ul class="buttons">
                                            <li><a href="#" class="isw-download"></a></li>
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
                                    <div class="block-fluid">
                                        <table cellpadding="0" cellspacing="0" width="100%" class="table">
                                            <thead>
                                                <tr>
                                                    <th width="5%">#</th>
                                                    <th width="15%">Visit Name</th>
                                                    <th width="15%">Visit Code</th>
                                                    <th width="15%">Visit Type</th>
                                                    <th width="15%">Visit Date</th>
                                                    <th width="10%">Status</th>
                                                    <th width="35%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $x = 1;
                                                foreach ($override->get('visit', 'client_id', $_GET['cid']) as $visit) {
                                                    $sc = $override->get('screening', 'client_id', $_GET['cid'])[0];
                                                    $lb = $override->get('lab', 'client_id', $_GET['cid'])[0];
                                                    $cntV = $override->getCount('visit', 'client_id', $visit['client_id']);
                                                    if ($visit['status'] == 0) {
                                                        $btnV = 'Add';
                                                    } elseif ($visit['status'] == 1) {
                                                        $btnV = 'Edit';
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
                                                        $v_typ = 'Screening & Enrollment';
                                                    } else {
                                                        $v_typ = 'Follow Up';
                                                    }
                                                    if ($x == 1 || ($x > 1 && $sc['eligibility'] == 1 && $lb['eligibility'] == 1)) { ?>
                                                        <tr>
                                                            <td><?= $x ?></td>
                                                            <td> <?= $visit['visit_name'] ?></td>
                                                            <td> <?= $visit['visit_code'] ?></td>
                                                            <td> <?= $v_typ ?></td>
                                                            <td> <?= $visit['visit_date'] ?></td>
                                                            <td>
                                                                <?php if ($visit['status'] == 1) { ?>
                                                                    <a href="#" role="button" class="btn btn-success">Done</a>
                                                                <?php } elseif ($visit['status'] == 0) { ?>
                                                                    <a href="#" role="button" class="btn btn-warning">Pending</a>
                                                                <?php } ?>
                                                            </td>
                                                            <td>
                                                                <?php if ($visit['seq_no'] > 0) { ?>
                                                                    <a href="#addVisit<?= $visit['id'] ?>" role="button" class="btn btn-info" data-toggle="modal"><?= $btnV ?> Visit</a>
                                                                <?php } else { ?>
                                                                    <a href="#addScreening<?= $visit['id'] ?>" role="button" class="btn btn-info" data-toggle="modal"><?= $btnS ?> Screening</a>
                                                                    <?php if ($sc['eligibility'] == 1) { ?>
                                                                        <a href="#addLab<?= $visit['id'] ?>" role="button" class="btn btn-info" data-toggle="modal"><?= $btnL ?> Lab Results</a>
                                                                    <?php } ?>
                                                                <?php } ?>
                                                                <?php if ($sc['eligibility'] == 0) { ?>
                                                                    <a href="#" role="button" class="btn btn-danger" data-toggle="modal">Not Eligible</a>
                                                                <?php } elseif ($lb['eligibility'] == 0) { ?>
                                                                    <a href="#" role="button" class="btn btn-danger" data-toggle="modal">Not Eligible</a>
                                                                <?php } ?>
                                                            </td>
                                                        </tr>
                                                        <div class="modal fade" id="addVisit<?= $visit['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <form id="validation" method="post">
                                                                        <div class="modal-header">
                                                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                            <h4>Add Visit</h4>
                                                                        </div>
                                                                        <div class="modal-body modal-body-np">
                                                                            <div class="row">
                                                                                <div class="block-fluid">
                                                                                    <div class="row-form clearfix">
                                                                                        <div class="col-md-3">Visit:</div>
                                                                                        <div class="col-md-9"><input type="text" name="name" value="<?= $visit['visit_name'] . ' (' . $visit['visit_code'] . ')' ?>" disabled /></div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="block-fluid">
                                                                                    <div class="row-form clearfix">
                                                                                        <div class="col-md-3">Visit Type:</div>
                                                                                        <div class="col-md-9"><input type="text" name="name" value="<?= $v_typ ?>" disabled /></div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row-form clearfix">
                                                                                    <div class="col-md-3">Current Status</div>
                                                                                    <div class="col-md-9">
                                                                                        <select name="visit_status" style="width: 100%;" required>
                                                                                            <?php if ($visit['status'] != 0) { ?>
                                                                                                <option value="<?= $visit['visit_status'] ?>"><?= $visit['visit_status'] ?></option>
                                                                                            <?php } else { ?>
                                                                                                <option value="">Select</option>
                                                                                            <?php } ?>
                                                                                            <option value="1">Attended</option>
                                                                                            <option value="2">Missed Visit</option>
                                                                                            <option value="3">Vaccinated</option>
                                                                                            <option value="4">Not Vaccinated</option>
                                                                                            <option value="5">Follow Up Visit</option>
                                                                                            <option value="6">Early Termination</option>
                                                                                            <option value="7">Termination</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="row-form clearfix">
                                                                                    <div class="col-md-3">Notes:</div>
                                                                                    <div class="col-md-9">
                                                                                        <textarea name="reasons" rows="4"><?php if ($visit['status'] != 0) {
                                                                                                                                echo $visit['reasons'];
                                                                                                                            } ?></textarea>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row-form clearfix">
                                                                                    <div class="col-md-3">Date:</div>
                                                                                    <div class="col-md-9">
                                                                                        <input value="<?php if ($visit['status'] != 0) {
                                                                                                            echo $visit['visit_date'];
                                                                                                        } ?>" class="validate[required,custom[date]]" type="text" name="visit_date" id="visit_date" /> <span>Example: 2010-12-01</span>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="dr"><span></span></div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <input type="hidden" name="id" value="<?= $visit['id'] ?>">
                                                                            <input type="hidden" name="vc" value="<?= $visit['visit_code'] ?>">
                                                                            <input type="hidden" name="cid" value="<?= $visit['client_id'] ?>">
                                                                            <input type="submit" name="edit_visit" class="btn btn-warning" value="Save">
                                                                            <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal fade" id="addScreening<?= $visit['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <form method="post">
                                                                        <div class="modal-header">
                                                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                            <h4>Add Screening</h4>
                                                                        </div>
                                                                        <div class="modal-body modal-body-np">
                                                                            <div class="row">
                                                                                <div class="block-fluid">
                                                                                    <div class="row-form clearfix">
                                                                                        <div class="col-md-8">Date of COVID-19 sample taken:</div>
                                                                                        <div class="col-md-4"><input type="text" name="sample_date" value="<?php if ($sc) {
                                                                                                                                                                echo $sc['sample_date'];
                                                                                                                                                            } ?>" /><span>Example: 2010-12-01</span></div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="block-fluid">
                                                                                    <div class="row-form clearfix">
                                                                                        <div class="col-md-8">Date of COVID-19 results:</div>
                                                                                        <div class="col-md-4"><input type="text" name="results_date" value="<?php if ($sc) {
                                                                                                                                                                echo $sc['results_date'];
                                                                                                                                                            } ?>" /><span>Example: 2010-12-01</span></div>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="row-form clearfix">
                                                                                    <div class="col-md-8">COVID-19 results</div>
                                                                                    <div class="col-md-4">
                                                                                        <select name="covid_result" style="width: 100%;" required>
                                                                                            <option value="<?= $sc['covid_result'] ?>"><?php if ($sc) {
                                                                                                                                            if ($sc['covid_result'] == 1) {
                                                                                                                                                echo 'Positive';
                                                                                                                                            } elseif ($sc['covid_result'] == 2) {
                                                                                                                                                echo 'Negative';
                                                                                                                                            }
                                                                                                                                        } else {
                                                                                                                                            echo 'Select';
                                                                                                                                        } ?></option>
                                                                                            <option value="1">Positive</option>
                                                                                            <option value="2">Negative</option>
                                                                                            <option value="3">PCR not done – Rapid Antigen test for COVID-19 is Negative</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="row-form clearfix">
                                                                                    <div class="col-md-8">Aged eighteen years and above</div>
                                                                                    <div class="col-md-4">
                                                                                        <select name="age_18" style="width: 100%;" required>
                                                                                            <option value="<?= $sc['age_18'] ?>"><?php if ($sc) {
                                                                                                                                        if ($sc['age_18'] == 1) {
                                                                                                                                            echo 'Yes';
                                                                                                                                        } elseif ($sc['age_18'] == 2) {
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

                                                                                <div class="row-form clearfix">
                                                                                    <div class="col-md-8">Is the participant confirmed to have COVID-19 by RT-PCR?</div>
                                                                                    <div class="col-md-4">
                                                                                        <select name="tr_pcr" style="width: 100%;" required>
                                                                                            <option value="<?= $sc['tr_pcr'] ?>"><?php if ($sc) {
                                                                                                                                        if ($sc['tr_pcr'] == 1) {
                                                                                                                                            echo 'Yes';
                                                                                                                                        } elseif ($sc['tr_pcr'] == 2) {
                                                                                                                                            echo 'No';
                                                                                                                                        }
                                                                                                                                    } else {
                                                                                                                                        echo 'Select';
                                                                                                                                    } ?></option>
                                                                                            <option value="1">Yes</option>
                                                                                            <option value="2">No</option>
                                                                                            <option value="3">Not Applicable</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="row-form clearfix">
                                                                                    <div class="col-md-8">Is the participant hospitalized?</div>
                                                                                    <div class="col-md-4">
                                                                                        <select name="hospitalized" style="width: 100%;" required>
                                                                                            <option value="<?= $sc['hospitalized'] ?>"><?php if ($sc) {
                                                                                                                                            if ($sc['hospitalized'] == 1) {
                                                                                                                                                echo 'Yes';
                                                                                                                                            } elseif ($sc['hospitalized'] == 2) {
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

                                                                                <div class="row-form clearfix">
                                                                                    <div class="col-md-8">Does the participant have moderate or severe form of COVID-19?</div>
                                                                                    <div class="col-md-4">
                                                                                        <select name="moderate_severe" style="width: 100%;" required>
                                                                                            <option value="<?= $sc['moderate_severe'] ?>"><?php if ($sc) {
                                                                                                                                                if ($sc['moderate_severe'] == 1) {
                                                                                                                                                    echo 'Yes';
                                                                                                                                                } elseif ($sc['moderate_severe'] == 2) {
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

                                                                                <div class="row-form clearfix">
                                                                                    <div class="col-md-8">Does the participant have history of peptic ulcers disease?</div>
                                                                                    <div class="col-md-4">
                                                                                        <select name="peptic_ulcers" style="width: 100%;" required>
                                                                                            <option value="<?= $sc['peptic_ulcers'] ?>"><?php if ($sc) {
                                                                                                                                            if ($sc['peptic_ulcers'] == 1) {
                                                                                                                                                echo 'Yes';
                                                                                                                                            } elseif ($sc['peptic_ulcers'] == 2) {
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

                                                                                <div class="row-form clearfix">
                                                                                    <div class="col-md-8">Is the participant pregnant?</div>
                                                                                    <div class="col-md-4">
                                                                                        <select name="pregnant" style="width: 100%;" required>
                                                                                            <option value="<?= $sc['pregnant'] ?>"><?php if ($sc) {
                                                                                                                                        if ($sc['pregnant'] == 1) {
                                                                                                                                            echo 'Yes';
                                                                                                                                        } elseif ($sc['pregnant'] == 2) {
                                                                                                                                            echo 'No';
                                                                                                                                        } elseif ($sc['pregnant'] == 3) {
                                                                                                                                            echo 'Not Applicable';
                                                                                                                                        }
                                                                                                                                    } else {
                                                                                                                                        echo 'Select';
                                                                                                                                    } ?></option>
                                                                                            <option value="1">Yes</option>
                                                                                            <option value="2">No</option>
                                                                                            <option value="3">Not Applicable</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="row-form clearfix">
                                                                                    <div class="col-md-8">Did the participant consent to be part of the study?</div>
                                                                                    <div class="col-md-4">
                                                                                        <select name="consented" style="width: 100%;" required>
                                                                                            <option value="<?= $sc['consented'] ?>"><?php if ($sc) {
                                                                                                                                        if ($sc['consented'] == 1) {
                                                                                                                                            echo 'Yes';
                                                                                                                                        } elseif ($sc['consented'] == 2) {
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

                                                                                <div class="dr"><span></span></div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <input type="hidden" name="id" value="<?= $visit['id'] ?>">
                                                                            <input type="hidden" name="cid" value="<?= $visit['client_id'] ?>">
                                                                            <input type="submit" name="add_screening" class="btn btn-warning" value="Save">
                                                                            <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal fade" id="addLab<?= $visit['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <form method="post">
                                                                        <div class="modal-header">
                                                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                            <h4>Add Lab Results</h4>
                                                                        </div>
                                                                        <div class="modal-body modal-body-np">
                                                                            <div class="row">
                                                                                <div class="row-form clearfix">
                                                                                    <div class="col-md-7">White blood cell count (WBC)</div>
                                                                                    <div class="col-md-5"><input type="number" name="wbc" value="<?php if ($lb) {
                                                                                                                                                        echo $lb['wbc'];
                                                                                                                                                    } ?>" step="0.001" /><span> x 10^9/L</span></div>
                                                                                </div>
                                                                                <div class="row-form clearfix">
                                                                                    <div class="col-md-7">Hemoglobin levels (Hb)</div>
                                                                                    <div class="col-md-5"><input type="number" name="hb" value="<?php if ($lb) {
                                                                                                                                                    echo $lb['hb'];
                                                                                                                                                } ?>" step="0.01" /><span> g/dL</span></div>
                                                                                </div>
                                                                                <div class="row-form clearfix">
                                                                                    <div class="col-md-7">Platelet count (Plt)</div>
                                                                                    <div class="col-md-5"><input type="number" name="plt" value="<?php if ($lb) {
                                                                                                                                                        echo $lb['plt'];
                                                                                                                                                    } ?>" step="0.01" /><span> x 10^9/L</span></div>
                                                                                </div>
                                                                                <div class="row-form clearfix">
                                                                                    <div class="col-md-7">ALT levels</div>
                                                                                    <div class="col-md-5"><input type="number" name="alt" value="<?php if ($lb) {
                                                                                                                                                        echo $lb['alt'];
                                                                                                                                                    } ?>" step="0.01" /><span> U/L</span></div>
                                                                                </div>
                                                                                <div class="row-form clearfix">
                                                                                    <div class="col-md-7">AST levels</div>
                                                                                    <div class="col-md-5"><input type="number" name="ast" value="<?php if ($lb) {
                                                                                                                                                        echo $lb['ast'];
                                                                                                                                                    } ?>" step="0.01" /><span> U/L</span></div>
                                                                                </div>
                                                                                <div class="row-form clearfix">
                                                                                    <div class="col-md-7">Serum creatinine levels</div>
                                                                                    <div class="col-md-5"><input type="number" name="sc" value="<?php if ($lb) {
                                                                                                                                                    echo $lb['sc'];
                                                                                                                                                } ?>" step="0.01" /><span> umol/L</span></div>
                                                                                </div>

                                                                                <div class="dr"><span></span></div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <input type="hidden" name="id" value="<?= $visit['id'] ?>">
                                                                            <input type="hidden" name="cid" value="<?= $visit['client_id'] ?>">
                                                                            <input type="submit" name="add_lab" class="btn btn-warning" value="Save">
                                                                            <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                <?php }
                                                    $x++;
                                                } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } elseif ($_GET['id'] == 5) { ?>
                        <div class="col-md-6">
                            <div class="head clearfix">
                                <div class="isw-grid"></div>
                                <h1>List of IDs</h1>
                                <ul class="buttons">
                                    <li><a href="#" class="isw-download"></a></li>
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
                            <div class="block-fluid">
                                <table cellpadding="0" cellspacing="0" width="100%" class="table">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" name="checkall" /></th>
                                            <td width="40">#</td>
                                            <th width="70">STUDY ID</th>
                                            <th width="80%">STATUS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $x = 1;
                                        $pagNum = $override->getCount('study_id', 'site_id', $user->data()->site_id);
                                        $pages = ceil($pagNum / $numRec);
                                        if (!$_GET['page'] || $_GET['page'] == 1) {
                                            $page = 0;
                                        } else {
                                            $page = ($_GET['page'] * $numRec) - $numRec;
                                        }
                                        foreach ($override->getWithLimit('study_id', 'site_id', $user->data()->site_id, $page, $numRec) as $study_id) { ?>
                                            <tr>
                                                <td><input type="checkbox" name="checkbox" /></td>
                                                <td><?= $x ?></td>
                                                <td><?= $study_id['study_id'] ?></td>
                                                <td>
                                                    <?php if ($study_id['status'] == 1) { ?>
                                                        <a href="#" role="button" class="btn btn-success">Assigned</a>
                                                    <?php } else { ?>
                                                        <a href="#" role="button" class="btn btn-warning">Not Assigned</a>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php $x++;
                                        } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="pull-left">
                                <div class="btn-group">
                                    <a href="info.php?id=5&page=<?php if (($_GET['page'] - 1) > 0) {
                                                                    echo $_GET['page'] - 1;
                                                                } else {
                                                                    echo 1;
                                                                } ?>" class="btn btn-default">
                                        < </a>
                                            <?php for ($i = 1; $i <= $pages; $i++) { ?>
                                                <a href="info.php?id=5&page=<?= $_GET['id'] ?>&page=<?= $i ?>" class="btn btn-default <?php if ($i == $_GET['page']) {
                                                                                                                                            echo 'active';
                                                                                                                                        } ?>"><?= $i ?></a>
                                            <?php } ?>
                                            <a href="info.php?id=5&page=<?php if (($_GET['page'] + 1) <= $pages) {
                                                                            echo $_GET['page'] + 1;
                                                                        } else {
                                                                            echo $i - 1;
                                                                        } ?>" class="btn btn-default"> > </a>
                                </div>
                            </div>
                        </div>
                    <?php } elseif ($_GET['id'] == 6) { ?>
                        <div class="col-md-2">
                            <?php $patient = $override->get('clients', 'id', $_GET['cid'])[0] ?>
                            <div class="ucard clearfix">
                                <div class="right">
                                    <div class="image">
                                        <?php if ($patient['client_image'] != '' || is_null($patient['client_image'])) {
                                            $img = $patient['client_image'];
                                        } else {
                                            $img = 'img/users/blank.png';
                                        } ?>
                                        <a href="#"><img src="<?= $img ?>" width="300" class="img-thumbnail"></a>
                                    </div>
                                    <h5><?= 'Name: ' . $patient['firstname'] . ' ' . $patient['lastname'] . ' Age: ' . $patient['age'] ?></h5>
                                    <h4><strong style="font-size: medium">Screening ID: <?= $patient['participant_id'] ?></strong></h4>
                                    <h4><strong style="font-size: larger">Study ID: <?= $patient['study_id'] ?></strong></h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="head clearfix">
                                <div class="isw-grid"></div>
                                <h1>Study CRF (Enrollment)</h1>
                                <ul class="buttons">
                                    <li><a href="#" class="isw-download"></a></li>
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
                            <div class="block-fluid">
                                <table cellpadding="0" cellspacing="0" width="100%" class="table">
                                    <thead>
                                        <tr>
                                            <th width="5%">#</th>
                                            <th width="25%">Name</th>
                                            <th width="65%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>CRF 1: MEDICAL HISTORY, USE OF HERBAL MEDICINES AND STANDARD TREATMENT</td>
                                            <?php if ($override->get('crf1', 'patient_id', $_GET['cid'])) { ?>
                                                <td><a href="add.php?id=8&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>" class="btn btn-success" disabled> Change </a> </td>
                                            <?php } else { ?>
                                                <td><a href="add.php?id=8&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>" class="btn btn-warning">Add </a> </td>
                                            <?php } ?>
                                        </tr>

                                        <tr>
                                            <td>2</td>
                                            <td>CRF 2</td>
                                            <?php if ($override->get1('crf2', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode'])) { ?>
                                                <td><a href="info.php?id=9&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>" class="btn btn-success" disabled> Change </a> </td>
                                            <?php } else { ?>
                                                <td><a href="add.php?id=9&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>" class="btn btn-warning">Add </a> </td>
                                            <?php } ?>
                                        </tr>

                                        <tr>
                                            <td>3</td>
                                            <td>CRF 3</td>
                                            <?php if ($override->get('crf3', 'patient_id', $_GET['cid'])) { ?>
                                                <td><a href="info.php?id=10&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>" class="btn btn-success" disabled> Change </a> </td>
                                            <?php } else { ?>
                                                <td><a href="add.php?id=10&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>" class="btn btn-warning">Add </a> </td>
                                            <?php } ?>
                                        </tr>

                                        <tr>
                                            <td>4</td>
                                            <td>CRF 4</td>
                                            <?php if ($override->get('crf4', 'patient_id', $_GET['cid'])) { ?>
                                                <td><a href="add.php?id=11&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>" class="btn btn-success" disabled> Change </a> </td>
                                            <?php } else { ?>
                                                <td><a href="add.php?id=11&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>" class="btn btn-warning">Add </a> </td>
                                            <?php } ?>
                                        </tr>

                                        <tr>
                                            <td>5</td>
                                            <td>CRF 5</td>
                                            <?php if ($override->get('crf5', 'patient_id', $_GET['cid'])) { ?>
                                                <td><a href="add.php?id=12&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>" class="btn btn-success" disabled> Change </a> </td>
                                            <?php } else { ?>
                                                <td><a href="add.php?id=12&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>" class="btn btn-warning">Add </a> </td>
                                            <?php } ?>
                                        </tr>

                                        <tr>
                                            <td>6</td>
                                            <td>CRF 6</td>
                                            <?php if ($override->get('crf6', 'patient_id', $_GET['cid'])) { ?>
                                                <td><a href="add.php?id=13&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>" class="btn btn-success" disabled> Change </a> </td>
                                            <?php } else { ?>
                                                <td><a href="add.php?id=13&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>" class="btn btn-warning">Add </a> </td>
                                            <?php } ?>
                                        </tr>

                                        <tr>
                                            <td>7</td>
                                            <td>CRF 7</td>
                                            <?php if ($override->get('crf7', 'patient_id', $_GET['cid'])) { ?>
                                                <td><a href="add.php?id=15&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>" class="btn btn-success"> Change </a> </td>
                                            <?php } else { ?>
                                                <td><a href="add.php?id=15&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>" class="btn btn-warning">Add </a> </td>
                                            <?php } ?>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php } elseif ($_GET['id'] == 7) { ?>
                        <div class="col-md-12">
                            <?php $patient = $override->get('clients', 'id', $_GET['cid'])[0] ?>
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="ucard clearfix">
                                        <div class="right">
                                            <div class="image">
                                                <?php if ($patient['client_image'] != '' || is_null($patient['client_image'])) {
                                                    $img = $patient['client_image'];
                                                } else {
                                                    $img = 'img/users/blank.png';
                                                } ?>
                                                <a href="#"><img src="<?= $img ?>" width="300" class="img-thumbnail"></a>
                                            </div>
                                            <h5><?= 'Name: ' . $patient['firstname'] . ' ' . $patient['lastname'] . ' Age: ' . $patient['age'] ?></h5>
                                            <h4><strong style="font-size: medium">Screening ID: <?= $patient['participant_id'] ?></strong></h4>
                                            <h4><strong style="font-size: larger">Study ID: <?= $patient['study_id'] ?></strong></h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-10">
                                    <div class="head clearfix">
                                        <div class="isw-grid"></div>
                                        <h1>Schedule</h1>
                                        <ul class="buttons">
                                            <li><a href="#" class="isw-download"></a></li>
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
                                    <div class="block-fluid">
                                        <table cellpadding="0" cellspacing="0" width="100%" class="table">
                                            <thead>
                                                <tr>
                                                    <th width="2%">#</th>
                                                    <th width="8%">Visit Name</th>
                                                    <th width="3%">Visit Code</th>
                                                    <th width="10%">Visit Type</th>
                                                    <th width="10%">Visit Date</th>
                                                    <th width="5%">Status</th>
                                                    <th width="15%">Action</th>
                                                    <th width="15%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $x = 1;
                                                foreach ($override->get('visit', 'client_id', $_GET['cid']) as $visit) {
                                                    $sc = $override->get('screening', 'client_id', $_GET['cid'])[0];
                                                    $lb = $override->get('lab', 'client_id', $_GET['cid'])[0];
                                                    $cntV = $override->getCount('visit', 'client_id', $visit['client_id']);
                                                    $client = $override->get('clients', 'id', $_GET['cid'])[0];
                                                    if ($visit['status'] == 0) {
                                                        $btnV = 'Add';
                                                    } elseif ($visit['status'] == 1) {
                                                        $btnV = 'Edit';
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
                                                        $v_typ = 'Screening & Enrollment';
                                                    } else {
                                                        $v_typ = 'Follow Up';
                                                    }

                                                    if ($x == 1 || ($x > 1 && $sc['eligibility'] == 1 && $lb['eligibility'] == 1)) { ?>
                                                        <tr>
                                                            <td><?= $x ?></td>
                                                            <td> <?= $visit['visit_name'] ?></td>
                                                            <td> <?= $visit['visit_code'] ?></td>
                                                            <td> <?= $v_typ ?></td>
                                                            <td> <?= $visit['visit_date'] ?></td>
                                                            <td>
                                                                <?php if ($visit['status'] == 1) { ?>
                                                                    <a href="#" role="button" class="btn btn-success">Done</a>
                                                                <?php } elseif ($visit['status'] == 0) { ?>
                                                                    <a href="#" role="button" class="btn btn-warning">Pending</a>
                                                                <?php } ?>
                                                            </td>
                                                            <td>
                                                                <?php if ($visit['seq_no'] > 0) { ?>
                                                                    <?php if ($btnV == 'Add') { ?>
                                                                        <a href="#addVisit<?= $visit['id'] ?>" role="button" class="btn btn-warning" data-toggle="modal"><?= $btnV ?>Visit</a>
                                                                    <?php } else { ?>
                                                                        <a href="#addVisit<?= $visit['id'] ?>" role="button" class="btn btn-info" data-toggle="modal"><?= $btnV ?>Visit</a>
                                                                    <?php } ?>
                                                            </td>
                                                            <td>
                                                                <?php if ($btnV == 'Add') { ?>
                                                                    <a href="info.php?id=6&cid=<?= $_GET['cid'] ?>&vid=<?= $visit['id'] ?>&vcode=<?= $visit['visit_code'] ?>" role="button" class="btn btn-warning"><?= $btnV ?>Study CRF</a>
                                                                <?php } else { ?>
                                                                    <a href="info.php?id=6&cid=<?= $_GET['cid'] ?>&vid=<?= $visit['id'] ?>&vcode=<?= $visit['visit_code'] ?>" role="button" class="btn btn-info"><?= $btnV ?>Study CRF</a>
                                                                <?php } ?>
                                                            </td>
                                                            <td>
                                                            <?php } else { ?>
                                                                <a href="#addInclusion<?= $visit['id'] ?>" role="button" class="btn btn-info" data-toggle="modal"><?= $btnS ?> Inclusion Criteria</a>
                                                                <?php if ($sc['eligibility'] == 1) { ?>
                                                                    <a href="#addExclusion<?= $visit['id'] ?>" role="button" class="btn btn-info" data-toggle="modal"><?= $btnL ?> Exclusion Criteria</a>
                                                            </td>
                                                            <td>
                                                                <?php if ($lb['eligibility'] == 1) { ?>
                                                                    <?php if ($btnV == 'Add') { ?>
                                                            <td>
                                                                <a href="info.php?id=6&cid=<?= $_GET['cid'] ?>&vid=<?= $visit['id'] ?>&vcode=<?= $visit['visit_code'] ?>" role="button" class="btn btn-warning"><?= $btnV ?>Study CRF</a>
                                                            <?php } else { ?>
                                                                <a href="info.php?id=6&cid=<?= $_GET['cid'] ?>&vid=<?= $visit['id'] ?>&vcode=<?= $visit['visit_code'] ?>" role="button" class="btn btn-info"><?= $btnV ?>Study CRF</a>
                                                            </td>
                                                    <?php }
                                                                        } ?>
                                                <?php } ?>
                                                </td>
                                                <td>
                                                <?php } ?>
                                                <?php if ($sc['eligibility'] == 0) { ?>
                                                    <a href="#" role="button" class="btn btn-danger" data-toggle="modal">Not Eligible(By Inclusion)</a>
                                                </td>
                                                <td>
                                                <?php } elseif ($lb['eligibility'] == 0) { ?>
                                                    <a href="#" role="button" class="btn btn-danger" data-toggle="modal">Not Eligible(By Exclusion)</a>
                                                <?php } ?>
                                                </td>
                                                        </tr>
                                                        <div class="modal fade" id="addVisit<?= $visit['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <form id="validation" method="post">
                                                                        <div class="modal-header">
                                                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                            <h4>Add Visit</h4>
                                                                        </div>
                                                                        <div class="modal-body modal-body-np">
                                                                            <div class="row">
                                                                                <div class="block-fluid">
                                                                                    <div class="row-form clearfix">
                                                                                        <div class="col-md-3">Visit:</div>
                                                                                        <div class="col-md-9"><input type="text" name="name" value="<?= $visit['visit_name'] . ' (' . $visit['visit_code'] . ')' ?>" disabled /></div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="block-fluid">
                                                                                    <div class="row-form clearfix">
                                                                                        <div class="col-md-3">Visit Type:</div>
                                                                                        <div class="col-md-9"><input type="text" name="name" value="<?= $v_typ ?>" disabled /></div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row-form clearfix">
                                                                                    <div class="col-md-3">Current Status</div>
                                                                                    <div class="col-md-9">
                                                                                        <select name="visit_status" style="width: 100%;" required>
                                                                                            <?php if ($visit['status'] != 0) { ?>
                                                                                                <option value="<?= $visit['visit_status'] ?>"><?= $visit['visit_status'] ?></option>
                                                                                            <?php } else { ?>
                                                                                                <option value="">Select</option>
                                                                                            <?php } ?>
                                                                                            <option value="1">Attended</option>
                                                                                            <option value="2">Missed Visit</option>
                                                                                            <option value="3">Vaccinated</option>
                                                                                            <option value="4">Not Vaccinated</option>
                                                                                            <option value="5">Follow Up Visit</option>
                                                                                            <option value="6">Early Termination</option>
                                                                                            <option value="7">Termination</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="row-form clearfix">
                                                                                    <div class="col-md-3">Notes:</div>
                                                                                    <div class="col-md-9">
                                                                                        <textarea name="reasons" rows="4"><?php if ($visit['status'] != 0) {
                                                                                                                                echo $visit['reasons'];
                                                                                                                            } ?></textarea>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row-form clearfix">
                                                                                    <div class="col-md-3">Date:</div>
                                                                                    <div class="col-md-9">
                                                                                        <input value="<?php if ($visit['status'] != 0) {
                                                                                                            echo $visit['visit_date'];
                                                                                                        } ?>" class="validate[required,custom[date]]" type="text" name="visit_date" id="visit_date" /> <span>Example: 2010-12-01</span>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="dr"><span></span></div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <input type="hidden" name="id" value="<?= $visit['id'] ?>">
                                                                            <input type="hidden" name="vc" value="<?= $visit['visit_code'] ?>">
                                                                            <input type="hidden" name="cid" value="<?= $visit['client_id'] ?>">
                                                                            <input type="submit" name="edit_visit" class="btn btn-warning" value="Save">
                                                                            <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>


                                                        <div class="modal fade" id="addScreening<?= $visit['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <form method="post">
                                                                        <div class="modal-header">
                                                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                            <h4>Add Screening</h4>
                                                                        </div>
                                                                        <div class="modal-body modal-body-np">
                                                                            <div class="row">
                                                                                <div class="block-fluid">
                                                                                    <div class="row-form clearfix">
                                                                                        <div class="col-md-8">Date of COVID-19 sample taken:</div>
                                                                                        <div class="col-md-4"><input type="text" name="sample_date" value="<?php if ($sc) {
                                                                                                                                                                echo $sc['sample_date'];
                                                                                                                                                            } ?>" /><span>Example: 2010-12-01</span></div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="block-fluid">
                                                                                    <div class="row-form clearfix">
                                                                                        <div class="col-md-8">Date of COVID-19 results:</div>
                                                                                        <div class="col-md-4"><input type="text" name="results_date" value="<?php if ($sc) {
                                                                                                                                                                echo $sc['results_date'];
                                                                                                                                                            } ?>" /><span>Example: 2010-12-01</span></div>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="row-form clearfix">
                                                                                    <div class="col-md-8">COVID-19 results</div>
                                                                                    <div class="col-md-4">
                                                                                        <select name="covid_result" style="width: 100%;" required>
                                                                                            <option value="<?= $sc['covid_result'] ?>"><?php if ($sc) {
                                                                                                                                            if ($sc['covid_result'] == 1) {
                                                                                                                                                echo 'Positive';
                                                                                                                                            } elseif ($sc['covid_result'] == 2) {
                                                                                                                                                echo 'Negative';
                                                                                                                                            }
                                                                                                                                        } else {
                                                                                                                                            echo 'Select';
                                                                                                                                        } ?></option>
                                                                                            <option value="1">Positive</option>
                                                                                            <option value="2">Negative</option>
                                                                                            <option value="3">PCR not done – Rapid Antigen test for COVID-19 is Negative</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="row-form clearfix">
                                                                                    <div class="col-md-8">Aged eighteen years and above</div>
                                                                                    <div class="col-md-4">
                                                                                        <select name="age_18" style="width: 100%;" required>
                                                                                            <option value="<?= $sc['age_18'] ?>"><?php if ($sc) {
                                                                                                                                        if ($sc['age_18'] == 1) {
                                                                                                                                            echo 'Yes';
                                                                                                                                        } elseif ($sc['age_18'] == 2) {
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

                                                                                <div class="row-form clearfix">
                                                                                    <div class="col-md-8">Is the participant confirmed to have COVID-19 by RT-PCR?</div>
                                                                                    <div class="col-md-4">
                                                                                        <select name="tr_pcr" style="width: 100%;" required>
                                                                                            <option value="<?= $sc['tr_pcr'] ?>"><?php if ($sc) {
                                                                                                                                        if ($sc['tr_pcr'] == 1) {
                                                                                                                                            echo 'Yes';
                                                                                                                                        } elseif ($sc['tr_pcr'] == 2) {
                                                                                                                                            echo 'No';
                                                                                                                                        }
                                                                                                                                    } else {
                                                                                                                                        echo 'Select';
                                                                                                                                    } ?></option>
                                                                                            <option value="1">Yes</option>
                                                                                            <option value="2">No</option>
                                                                                            <option value="3">Not Applicable</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="row-form clearfix">
                                                                                    <div class="col-md-8">Is the participant hospitalized?</div>
                                                                                    <div class="col-md-4">
                                                                                        <select name="hospitalized" style="width: 100%;" required>
                                                                                            <option value="<?= $sc['hospitalized'] ?>"><?php if ($sc) {
                                                                                                                                            if ($sc['hospitalized'] == 1) {
                                                                                                                                                echo 'Yes';
                                                                                                                                            } elseif ($sc['hospitalized'] == 2) {
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

                                                                                <div class="row-form clearfix">
                                                                                    <div class="col-md-8">Does the participant have moderate or severe form of COVID-19?</div>
                                                                                    <div class="col-md-4">
                                                                                        <select name="moderate_severe" style="width: 100%;" required>
                                                                                            <option value="<?= $sc['moderate_severe'] ?>"><?php if ($sc) {
                                                                                                                                                if ($sc['moderate_severe'] == 1) {
                                                                                                                                                    echo 'Yes';
                                                                                                                                                } elseif ($sc['moderate_severe'] == 2) {
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

                                                                                <div class="row-form clearfix">
                                                                                    <div class="col-md-8">Does the participant have history of peptic ulcers disease?</div>
                                                                                    <div class="col-md-4">
                                                                                        <select name="peptic_ulcers" style="width: 100%;" required>
                                                                                            <option value="<?= $sc['peptic_ulcers'] ?>"><?php if ($sc) {
                                                                                                                                            if ($sc['peptic_ulcers'] == 1) {
                                                                                                                                                echo 'Yes';
                                                                                                                                            } elseif ($sc['peptic_ulcers'] == 2) {
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

                                                                                <div class="row-form clearfix">
                                                                                    <div class="col-md-8">Is the participant pregnant?</div>
                                                                                    <div class="col-md-4">
                                                                                        <select name="pregnant" style="width: 100%;" required>
                                                                                            <option value="<?= $sc['pregnant'] ?>"><?php if ($sc) {
                                                                                                                                        if ($sc['pregnant'] == 1) {
                                                                                                                                            echo 'Yes';
                                                                                                                                        } elseif ($sc['pregnant'] == 2) {
                                                                                                                                            echo 'No';
                                                                                                                                        } elseif ($sc['pregnant'] == 3) {
                                                                                                                                            echo 'Not Applicable';
                                                                                                                                        }
                                                                                                                                    } else {
                                                                                                                                        echo 'Select';
                                                                                                                                    } ?></option>
                                                                                            <option value="1">Yes</option>
                                                                                            <option value="2">No</option>
                                                                                            <option value="3">Not Applicable</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="row-form clearfix">
                                                                                    <div class="col-md-8">Did the participant consent to be part of the study?</div>
                                                                                    <div class="col-md-4">
                                                                                        <select name="consented" style="width: 100%;" required>
                                                                                            <option value="<?= $sc['consented'] ?>"><?php if ($sc) {
                                                                                                                                        if ($sc['consented'] == 1) {
                                                                                                                                            echo 'Yes';
                                                                                                                                        } elseif ($sc['consented'] == 2) {
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

                                                                                <div class="dr"><span></span></div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <input type="hidden" name="id" value="<?= $visit['id'] ?>">
                                                                            <input type="hidden" name="cid" value="<?= $visit['client_id'] ?>">
                                                                            <input type="submit" name="add_screening" class="btn btn-warning" value="Save">
                                                                            <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="modal fade" id="addInclusion<?= $visit['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <form method="post">
                                                                        <div class="modal-header">
                                                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                            <h4>Add Inclusion</h4>
                                                                        </div>
                                                                        <div class="modal-body modal-body-np">
                                                                            <div class="row">

                                                                                <div class="row-form clearfix">
                                                                                    <div class="col-md-8">Aged eighteen years and above</div>
                                                                                    <div class="col-md-4">
                                                                                        <select name="age_18" style="width: 100%;" required>
                                                                                            <option value="<?= $sc['age_18'] ?>"><?php if ($sc) {
                                                                                                                                        if ($sc['age_18'] == 1) {
                                                                                                                                            echo 'Yes';
                                                                                                                                        } elseif ($sc['age_18'] == 2) {
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

                                                                                <div class="row-form clearfix">
                                                                                    <div class="col-md-8">Confirmed cancer with biopsy?</div>
                                                                                    <div class="col-md-4">
                                                                                        <select name="biopsy" style="width: 100%;" required>
                                                                                            <option value="<?= $sc['biopsy'] ?>"><?php if ($sc) {
                                                                                                                                        if ($sc['biopsy'] == 1) {
                                                                                                                                            echo 'Yes';
                                                                                                                                        } elseif ($sc['biopsy'] == 2) {
                                                                                                                                            echo 'No';
                                                                                                                                        } elseif ($sc['biopsy'] == 3) {
                                                                                                                                            echo 'Not Applicable';
                                                                                                                                        }
                                                                                                                                    } else {
                                                                                                                                        echo 'Select';
                                                                                                                                    } ?></option>
                                                                                            <option value="1">Yes</option>
                                                                                            <option value="2">No</option>
                                                                                            <option value="3">Not Applicable</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="row-form clearfix">
                                                                                    <div class="col-md-8">Brain cancer</div>
                                                                                    <div class="col-md-4">
                                                                                        <select name="brain_cancer" style="width: 100%;" required>
                                                                                            <option value="<?= $sc['brain_cancer'] ?>"><?php if ($sc) {
                                                                                                                                            if ($sc['brain_cancer'] == 1) {
                                                                                                                                                echo 'Yes';
                                                                                                                                            } elseif ($sc['brain_cancer'] == 2) {
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

                                                                                <div class="row-form clearfix">
                                                                                    <div class="col-md-8">Brain cancer</div>
                                                                                    <div class="col-md-4">
                                                                                        <select name="breast_cancer" style="width: 100%;" required>
                                                                                            <option value="<?= $sc['breast_cancer'] ?>"><?php if ($sc) {
                                                                                                                                            if ($sc['breast_cancer'] == 1) {
                                                                                                                                                echo 'Yes';
                                                                                                                                            } elseif ($sc['breast_cancer'] == 2) {
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

                                                                                <?php if ($client['gender'] == "female") { ?>

                                                                                    <div class="row-form clearfix">
                                                                                        <div class="col-md-8">Cervical cancer</div>
                                                                                        <div class="col-md-4">
                                                                                            <select name="cervical_cancer" style="width: 100%;" required>
                                                                                                <option value="<?= $sc['cervical_cancer'] ?>"><?php if ($sc) {
                                                                                                                                                    if ($sc['cervical_cancer'] == 1) {
                                                                                                                                                        echo 'Yes';
                                                                                                                                                    } elseif ($sc['cervical_cancer'] == 2) {
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

                                                                                <?php } elseif ($client['gender'] == "male") { ?>
                                                                                    <div class="row-form clearfix">
                                                                                        <div class="col-md-8">Prostate cancer</div>
                                                                                        <div class="col-md-4">
                                                                                            <select name="prostate_cancer" style="width: 100%;" required>
                                                                                                <option value="<?= $sc['prostate_cancer'] ?>"><?php if ($sc) {
                                                                                                                                                    if ($sc['prostate_cancer'] == 1) {
                                                                                                                                                        echo 'Yes';
                                                                                                                                                    } elseif ($sc['prostate_cancer'] == 2) {
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
                                                                                <?php } ?>

                                                                                <div class="row-form clearfix">
                                                                                    <div class="col-md-8">Did the participant consent to be part of the study?</div>
                                                                                    <div class="col-md-4">
                                                                                        <select name="consented" style="width: 100%;" required>
                                                                                            <option value="<?= $sc['consented'] ?>"><?php if ($sc) {
                                                                                                                                        if ($sc['consented'] == 1) {
                                                                                                                                            echo 'Yes';
                                                                                                                                        } elseif ($sc['consented'] == 2) {
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

                                                                                <div class="dr"><span></span></div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <input type="hidden" name="id" value="<?= $visit['id'] ?>">
                                                                            <input type="hidden" name="cid" value="<?= $visit['client_id'] ?>">
                                                                            <input type="submit" name="add_Inclusion" class="btn btn-warning" value="Save">
                                                                            <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal fade" id="addLab<?= $visit['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <form method="post">
                                                                        <div class="modal-header">
                                                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                            <h4>Add Lab Results</h4>
                                                                        </div>
                                                                        <div class="modal-body modal-body-np">
                                                                            <div class="row">
                                                                                <div class="row-form clearfix">
                                                                                    <div class="col-md-7">White blood cell count (WBC)</div>
                                                                                    <div class="col-md-5"><input type="number" name="wbc" value="<?php if ($lb) {
                                                                                                                                                        echo $lb['wbc'];
                                                                                                                                                    } ?>" step="0.001" /><span> x 10^9/L</span></div>
                                                                                </div>
                                                                                <div class="row-form clearfix">
                                                                                    <div class="col-md-7">Hemoglobin levels (Hb)</div>
                                                                                    <div class="col-md-5"><input type="number" name="hb" value="<?php if ($lb) {
                                                                                                                                                    echo $lb['hb'];
                                                                                                                                                } ?>" step="0.01" /><span> g/dL</span></div>
                                                                                </div>
                                                                                <div class="row-form clearfix">
                                                                                    <div class="col-md-7">Platelet count (Plt)</div>
                                                                                    <div class="col-md-5"><input type="number" name="plt" value="<?php if ($lb) {
                                                                                                                                                        echo $lb['plt'];
                                                                                                                                                    } ?>" step="0.01" /><span> x 10^9/L</span></div>
                                                                                </div>
                                                                                <div class="row-form clearfix">
                                                                                    <div class="col-md-7">ALT levels</div>
                                                                                    <div class="col-md-5"><input type="number" name="alt" value="<?php if ($lb) {
                                                                                                                                                        echo $lb['alt'];
                                                                                                                                                    } ?>" step="0.01" /><span> U/L</span></div>
                                                                                </div>
                                                                                <div class="row-form clearfix">
                                                                                    <div class="col-md-7">AST levels</div>
                                                                                    <div class="col-md-5"><input type="number" name="ast" value="<?php if ($lb) {
                                                                                                                                                        echo $lb['ast'];
                                                                                                                                                    } ?>" step="0.01" /><span> U/L</span></div>
                                                                                </div>
                                                                                <div class="row-form clearfix">
                                                                                    <div class="col-md-7">Serum creatinine levels</div>
                                                                                    <div class="col-md-5"><input type="number" name="sc" value="<?php if ($lb) {
                                                                                                                                                    echo $lb['sc'];
                                                                                                                                                } ?>" step="0.01" /><span> umol/L</span></div>
                                                                                </div>

                                                                                <div class="dr"><span></span></div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <input type="hidden" name="id" value="<?= $visit['id'] ?>">
                                                                            <input type="hidden" name="cid" value="<?= $visit['client_id'] ?>">
                                                                            <input type="submit" name="add_lab" class="btn btn-warning" value="Save">
                                                                            <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal fade" id="addExclusion<?= $visit['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <form method="post">
                                                                        <div class="modal-header">
                                                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                            <h4>Add Exclusion criteria</h4>
                                                                        </div>
                                                                        <div class="modal-body modal-body-np">
                                                                            <div class="row">

                                                                                <div class="row-form clearfix">
                                                                                    <div class="col-md-8">Is the participant pregnant?</div>
                                                                                    <div class="col-md-4">
                                                                                        <select name="pregnant" style="width: 100%;" required>
                                                                                            <option value="<?= $lb['pregnant'] ?>"><?php if ($lb) {
                                                                                                                                        if ($lb['pregnant'] == 1) {
                                                                                                                                            echo 'Yes';
                                                                                                                                        } elseif ($lb['pregnant'] == 2) {
                                                                                                                                            echo 'No';
                                                                                                                                        } elseif ($lb['pregnant'] == 3) {
                                                                                                                                            echo 'Not Applicable';
                                                                                                                                        }
                                                                                                                                    } else {
                                                                                                                                        echo 'Select';
                                                                                                                                    } ?></option>
                                                                                            <option value="1">Yes</option>
                                                                                            <option value="2">No</option>
                                                                                            <option value="3">Not Applicable</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="row-form clearfix">
                                                                                    <div class="col-md-8">Is the participant Breast feeding?</div>
                                                                                    <div class="col-md-4">
                                                                                        <select name="breast_feeding" style="width: 100%;" required>
                                                                                            <option value="<?= $lb['breast_feeding'] ?>"><?php if ($lb) {
                                                                                                                                                if ($lb['breast_feeding'] == 1) {
                                                                                                                                                    echo 'Yes';
                                                                                                                                                } elseif ($lb['breast_feeding'] == 2) {
                                                                                                                                                    echo 'No';
                                                                                                                                                } elseif ($lb['breast_feeding'] == 3) {
                                                                                                                                                    echo 'Not Applicable';
                                                                                                                                                }
                                                                                                                                            } else {
                                                                                                                                                echo 'Select';
                                                                                                                                            } ?></option>
                                                                                            <option value="1">Yes</option>
                                                                                            <option value="2">No</option>
                                                                                            <option value="3">Not Applicable</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="row-form clearfix">
                                                                                    <div class="col-md-8">CKD?</div>
                                                                                    <div class="col-md-4">
                                                                                        <select name="cdk" style="width: 100%;" required>
                                                                                            <option value="<?= $lb['cdk'] ?>"><?php if ($lb) {
                                                                                                                                    if ($lb['cdk'] == 1) {
                                                                                                                                        echo 'Yes';
                                                                                                                                    } elseif ($lb['cdk'] == 2) {
                                                                                                                                        echo 'No';
                                                                                                                                    } elseif ($lb['cdk'] == 3) {
                                                                                                                                        echo 'Not Applicable';
                                                                                                                                    }
                                                                                                                                } else {
                                                                                                                                    echo 'Select';
                                                                                                                                } ?></option>
                                                                                            <option value="1">Yes</option>
                                                                                            <option value="2">No</option>
                                                                                            <option value="3">Not Applicable</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="row-form clearfix">
                                                                                    <div class="col-md-8">Liver Disease ?</div>
                                                                                    <div class="col-md-4">
                                                                                        <select name="liver_disease" style="width: 100%;" required>
                                                                                            <option value="<?= $lb['liver_disease'] ?>"><?php if ($lb) {
                                                                                                                                            if ($lb['liver_disease'] == 1) {
                                                                                                                                                echo 'Yes';
                                                                                                                                            } elseif ($lb['liver_disease'] == 2) {
                                                                                                                                                echo 'No';
                                                                                                                                            } elseif ($lb['liver_disease'] == 3) {
                                                                                                                                                echo 'Not Applicable';
                                                                                                                                            }
                                                                                                                                        } else {
                                                                                                                                            echo 'Select';
                                                                                                                                        } ?></option>
                                                                                            <option value="1">Yes</option>
                                                                                            <option value="2">No</option>
                                                                                            <option value="3">Not Applicable</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="dr"><span></span></div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <input type="hidden" name="id" value="<?= $visit['id'] ?>">
                                                                            <input type="hidden" name="cid" value="<?= $visit['client_id'] ?>">
                                                                            <input type="submit" name="add_Exclusion" class="btn btn-warning" value="Save">
                                                                            <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                <?php }
                                                    $x++;
                                                } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php } elseif ($_GET['id'] == 8) { ?>
                        <div class="col-md-12">
                            <?php if ($user->data()->power == 1) { ?>
                                <div class="head clearfix">
                                    <div class="isw-ok"></div>
                                    <h1>Search by Site</h1>
                                </div>
                                <div class="block-fluid">
                                    <form id="validation" method="post">
                                        <div class="row-form clearfix">
                                            <div class="col-md-1">Site:</div>
                                            <div class="col-md-4">
                                                <select name="site" required>
                                                    <option value="">Select Site</option>
                                                    <?php foreach ($override->getData('site') as $site) { ?>
                                                        <option value="<?= $site['id'] ?>"><?= $site['name'] ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <input type="submit" name="search_by_site" value="Search" class="btn btn-info">
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            <?php } ?>
                            <div class="head clearfix">
                                <div class="isw-grid"></div>
                                <h1>List of Clients</h1>
                                <ul class="buttons">
                                    <li><a href="#" class="isw-download"></a></li>
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
                                if ($_GET['sid'] != null) {
                                    $pagNum = 0;
                                    $pagNum = $override->countData('clients', 'enrolled', 1, 'site_id', $_GET['sid']);
                                    $pages = ceil($pagNum / $numRec);
                                    if (!$_GET['page'] || $_GET['page'] == 1) {
                                        $page = 0;
                                    } else {
                                        $page = ($_GET['page'] * $numRec) - $numRec;
                                    }
                                    $clients = $override->getWithLimit1('clients', 'site_id', $_GET['sid'], 'enrolled', 1, $page, $numRec);
                                } else {
                                    $pagNum = 0;
                                    $pagNum = $override->getCount('clients', 'enrolled', 1);
                                    $pages = ceil($pagNum / $numRec);
                                    if (!$_GET['page'] || $_GET['page'] == 1) {
                                        $page = 0;
                                    } else {
                                        $page = ($_GET['page'] * $numRec) - $numRec;
                                    }
                                    $clients = $override->getWithLimit('clients', 'enrolled', 1, $page, $numRec);
                                }
                            } else {
                                $pagNum = 0;
                                $pagNum = $override->countData('clients', 'site_id', $user->data()->site_id, 'enrolled', 1);
                                $pages = ceil($pagNum / $numRec);
                                if (!$_GET['page'] || $_GET['page'] == 1) {
                                    $page = 0;
                                } else {
                                    $page = ($_GET['page'] * $numRec) - $numRec;
                                }
                                $clients = $override->getWithLimit1('clients', 'site_id', $user->data()->site_id, 'enrolled', 1, $page, $numRec);
                            } ?>
                            <div class="block-fluid">
                                <table cellpadding="0" cellspacing="0" width="100%" class="table">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" name="checkall" /></th>
                                            <td width="20">#</td>
                                            <th width="40">Picture</th>
                                            <th width="20%">ParticipantID</th>
                                            <th width="10%">Name</th>
                                            <th width="10%">Gender</th>
                                            <th width="10%">Age</th>
                                            <th width="40%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $x = 1;
                                        foreach ($clients as $client) { ?>
                                            <tr>
                                                <td><input type="checkbox" name="checkbox" /></td>
                                                <td><?= $x ?></td>
                                                <td width="100">
                                                    <?php if ($client['client_image'] != '' || is_null($client['client_image'])) {
                                                        $img = $client['client_image'];
                                                    } else {
                                                        $img = 'img/users/blank.png';
                                                    } ?>
                                                    <a href="#img<?= $client['id'] ?>" data-toggle="modal"><img src="<?= $img ?>" width="90" height="90" class="" /></a>
                                                </td>
                                                <td><?= $client['participant_id'] ?></td>
                                                <td> <?= $client['firstname'] . ' ' . $client['lastname'] ?></td>
                                                <td><?= $client['gender'] ?></td>
                                                <td><?= $client['age'] ?></td>
                                                <td>
                                                    <a href="#clientView<?= $client['id'] ?>" role="button" class="btn btn-default" data-toggle="modal">View</a>
                                                    <a href="#client<?= $client['id'] ?>" role="button" class="btn btn-info" data-toggle="modal">Edit</a>
                                                    <a href="id.php?cid=<?= $client['id'] ?>" class="btn btn-warning">Patient ID</a>
                                                    <!-- <a href="info.php?id=6&cid=<?= $client['id'] ?>" role="button" class="btn btn-success">Study CRF</a> -->
                                                    <?php if ($user->data()->accessLevel == 1) { ?>
                                                        <a href="#delete<?= $client['id'] ?>" role="button" class="btn btn-danger" data-toggle="modal">Delete</a>
                                                    <?php } ?>
                                                    <!-- <a href="info.php?id=4&cid=<?= $client['id'] ?>" role="button" class="btn btn-warning">Schedule</a> -->
                                                    <a href="info.php?id=7&cid=<?= $client['id'] ?>" role="button" class="btn btn-warning">Schedule</a>
                                                </td>

                                            </tr>
                                            <div class="modal fade" id="clientView<?= $client['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <form method="post">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                <h4>Edit Client View</h4>
                                                            </div>
                                                            <div class="modal-body modal-body-np">
                                                                <div class="row">
                                                                    <div class="block-fluid">
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-2">Study</div>
                                                                            <div class="col-md-6">
                                                                                <select name="position" style="width: 100%;" disabled>
                                                                                    <?php foreach ($override->getData('study') as $study) { ?>
                                                                                        <option value="<?= $study['id'] ?>"><?= $study['name'] ?></option>
                                                                                    <?php } ?>
                                                                                </select>
                                                                            </div>
                                                                            <div class="col-md-4 pull-right">
                                                                                <img src="<?= $img ?>" class="img-thumbnail" width="50%" height="50%" />
                                                                            </div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">ParticipantID:</div>
                                                                            <div class="col-md-9">
                                                                                <input value="<?= $client['participant_id'] ?>" class="validate[required]" type="text" name="participant_id" id="participant_id" disabled />
                                                                            </div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Date:</div>
                                                                            <div class="col-md-9">
                                                                                <input value="<?= $client['clinic_date'] ?>" class="validate[required,custom[date]]" type="text" name="clinic_date" id="clinic_date" disabled /> <span>Example: 2010-12-01</span>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">First Name:</div>
                                                                            <div class="col-md-9">
                                                                                <input value="<?= $client['firstname'] ?>" class="validate[required]" type="text" name="firstname" id="firstname" disabled />
                                                                            </div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Middle Name:</div>
                                                                            <div class="col-md-9">
                                                                                <input value="<?= $client['middlename'] ?>" class="validate[required]" type="text" name="middlename" id="middlename" disabled />
                                                                            </div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Last Name:</div>
                                                                            <div class="col-md-9">
                                                                                <input value="<?= $client['lastname'] ?>" class="validate[required]" type="text" name="lastname" id="lastname" disabled />
                                                                            </div>
                                                                        </div>

                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Date of Birth:</div>
                                                                            <div class="col-md-9">
                                                                                <input value="<?= $client['dob'] ?>" class="validate[required,custom[date]]" type="text" name="dob" id="dob" disabled /> <span>Example: 2010-12-01</span>
                                                                            </div>
                                                                        </div>

                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Age:</div>
                                                                            <div class="col-md-9">
                                                                                <input value="<?= $client['age'] ?>" class="validate[required]" type="text" name="age" id="age" disabled />
                                                                            </div>
                                                                        </div>

                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Initials:</div>
                                                                            <div class="col-md-9">
                                                                                <input value="<?= $client['initials'] ?>" class="validate[required]" type="text" name="initials" id="initials" disabled />
                                                                            </div>
                                                                        </div>

                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Gender</div>
                                                                            <div class="col-md-9">
                                                                                <select name="gender" style="width: 100%;" disabled>
                                                                                    <option value="<?= $client['gender'] ?>"><?= $client['gender'] ?></option>
                                                                                    <option value="male">Male</option>
                                                                                    <option value="female">Female</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>

                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Hospital ID:</div>
                                                                            <div class="col-md-9">
                                                                                <input value="<?= $client['id_number'] ?>" class="validate[required]" type="text" name="id_number" id="id_number" disabled />
                                                                            </div>
                                                                        </div>

                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Marital Status</div>
                                                                            <div class="col-md-9">
                                                                                <select name="marital_status" style="width: 100%;" disabled>
                                                                                    <option value="<?= $client['marital_status'] ?>"><?= $client['marital_status'] ?></option>
                                                                                    <option value="Single">Single</option>
                                                                                    <option value="Married">Married</option>
                                                                                    <option value="Divorced">Divorced</option>
                                                                                    <option value="Separated">Separated</option>
                                                                                    <option value="Widower">Widower/Widow</option>
                                                                                    <option value="Cohabit">Cohabit</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>

                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Education Level</div>
                                                                            <div class="col-md-9">
                                                                                <select name="education_level" style="width: 100%;" disabled>
                                                                                    <option value="<?= $client['education_level'] ?>"><?= $client['education_level'] ?></option>
                                                                                    <option value="Not attended school">Not attended school</option>
                                                                                    <option value="Primary">Primary</option>
                                                                                    <option value="Secondary">Secondary</option>
                                                                                    <option value="Certificate">Certificate</option>
                                                                                    <option value="Diploma">Diploma</option>
                                                                                    <option value="Undergraduate degree">Undergraduate degree</option>
                                                                                    <option value="Postgraduate degree">Postgraduate degree</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Workplace/station site:</div>
                                                                            <div class="col-md-9"><input value="<?= $client['workplace'] ?>" class="" type="text" name="workplace" id="workplace" disabled /></div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Occupation:</div>
                                                                            <div class="col-md-9"><input value="<?= $client['occupation'] ?>" class="" type="text" name="occupation" id="occupation" disabled /></div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Phone Number:</div>
                                                                            <div class="col-md-9"><input value="<?= $client['phone_number'] ?>" class="" type="text" name="phone_number" id="phone" disabled /> <span>Example: 0700 000 111</span></div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Relative's Phone Number:</div>
                                                                            <div class="col-md-9"><input value="<?= $client['other_phone'] ?>" class="" type="text" name="other_phone" id="phone" disabled /> <span>Example: 0700 000 111</span></div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Residence Street:</div>
                                                                            <div class="col-md-9"><input value="<?= $client['street'] ?>" class="" type="text" name="street" id="street" disabled /></div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Ward:</div>
                                                                            <div class="col-md-9"><input value="<?= $client['ward'] ?>" class="" type="text" name="ward" id="ward" disabled /></div>
                                                                        </div>

                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">House Number:</div>
                                                                            <div class="col-md-9"><input value="<?= $client['block_no'] ?>" class="" type="text" name="block_no" id="block_no" disabled /></div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Comments:</div>
                                                                            <div class="col-md-9"><textarea name="comments" rows="4" disabled><?= $client['comments'] ?></textarea> </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="dr"><span></span></div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <input type="hidden" name="id" value="<?= $client['id'] ?>">
                                                                <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="modal fade" id="client<?= $client['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <form id="validation" enctype="multipart/form-data" method="post">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                <h4>Edit Client Info</h4>
                                                            </div>
                                                            <div class="modal-body modal-body-np">
                                                                <div class="row">
                                                                    <div class="block-fluid">
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Study</div>
                                                                            <div class="col-md-9">
                                                                                <select name="position" style="width: 100%;" required>
                                                                                    <?php foreach ($override->getData('study') as $study) { ?>
                                                                                        <option value="<?= $study['id'] ?>"><?= $study['name'] ?></option>
                                                                                    <?php } ?>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">ParticipantID:</div>
                                                                            <div class="col-md-9">
                                                                                <input value="<?= $client['participant_id'] ?>" class="validate[required]" type="text" name="participant_id" id="participant_id" />
                                                                            </div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Date:</div>
                                                                            <div class="col-md-9">
                                                                                <input value="<?= $client['clinic_date'] ?>" class="validate[required,custom[date]]" type="text" name="clinic_date" id="clinic_date" /> <span>Example: 2010-12-01</span>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">First Name:</div>
                                                                            <div class="col-md-9">
                                                                                <input value="<?= $client['firstname'] ?>" class="validate[required]" type="text" name="firstname" id="firstname" />
                                                                            </div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Middle Name:</div>
                                                                            <div class="col-md-9">
                                                                                <input value="<?= $client['middlename'] ?>" class="validate[required]" type="text" name="middlename" id="middlename" />
                                                                            </div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Last Name:</div>
                                                                            <div class="col-md-9">
                                                                                <input value="<?= $client['lastname'] ?>" class="validate[required]" type="text" name="lastname" id="lastname" />
                                                                            </div>
                                                                        </div>

                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Date of Birth:</div>
                                                                            <div class="col-md-9">
                                                                                <input value="<?= $client['dob'] ?>" class="validate[required,custom[date]]" type="text" name="dob" id="dob" /> <span>Example: 2010-12-01</span>
                                                                            </div>
                                                                        </div>

                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Age:</div>
                                                                            <div class="col-md-9">
                                                                                <input value="<?= $client['age'] ?>" class="validate[required]" type="text" name="age" id="age" disabled />
                                                                            </div>
                                                                        </div>

                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Initials:</div>
                                                                            <div class="col-md-9">
                                                                                <input value="<?= $client['initials'] ?>" class="validate[required]" type="text" name="initials" id="initials" />
                                                                            </div>
                                                                        </div>

                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-5">Client Image:</div>
                                                                            <div class="col-md-7">
                                                                                <input type="file" id="image" name="image" />
                                                                            </div>
                                                                        </div>

                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Gender</div>
                                                                            <div class="col-md-9">
                                                                                <select name="gender" style="width: 100%;" required>
                                                                                    <option value="<?= $client['gender'] ?>"><?= $client['gender'] ?></option>
                                                                                    <option value="male">Male</option>
                                                                                    <option value="female">Female</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Hospital ID:</div>
                                                                            <div class="col-md-9">
                                                                                <input value="<?= $client['id_number'] ?>" class="validate[required]" type="text" name="id_number" id="id_number" />
                                                                            </div>
                                                                        </div>


                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Marital Status</div>
                                                                            <div class="col-md-9">
                                                                                <select name="marital_status" style="width: 100%;" required>
                                                                                    <option value="<?= $client['marital_status'] ?>"><?= $client['marital_status'] ?></option>
                                                                                    <option value="Single">Single</option>
                                                                                    <option value="Married">Married</option>
                                                                                    <option value="Divorced">Divorced</option>
                                                                                    <option value="Separated">Separated</option>
                                                                                    <option value="Widower">Widower/Widow</option>
                                                                                    <option value="Cohabit">Cohabit</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>

                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Education Level</div>
                                                                            <div class="col-md-9">
                                                                                <select name="education_level" style="width: 100%;" required>
                                                                                    <option value="<?= $client['education_level'] ?>"><?= $client['education_level'] ?></option>
                                                                                    <option value="Not attended school">Not attended school</option>
                                                                                    <option value="Primary">Primary</option>
                                                                                    <option value="Secondary">Secondary</option>
                                                                                    <option value="Certificate">Certificate</option>
                                                                                    <option value="Diploma">Diploma</option>
                                                                                    <option value="Undergraduate degree">Undergraduate degree</option>
                                                                                    <option value="Postgraduate degree">Postgraduate degree</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Workplace/station site:</div>
                                                                            <div class="col-md-9"><input value="<?= $client['workplace'] ?>" class="" type="text" name="workplace" id="workplace" required /></div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Occupation:</div>
                                                                            <div class="col-md-9"><input value="<?= $client['occupation'] ?>" class="" type="text" name="occupation" id="occupation" required /></div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Phone Number:</div>
                                                                            <div class="col-md-9"><input value="<?= $client['phone_number'] ?>" class="" type="text" name="phone_number" id="phone" required /> <span>Example: 0700 000 111</span></div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Relative's Phone Number:</div>
                                                                            <div class="col-md-9"><input value="<?= $client['other_phone'] ?>" class="" type="text" name="other_phone" id="other_phone" /> <span>Example: 0700 000 111</span></div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Residence Street:</div>
                                                                            <div class="col-md-9"><input value="<?= $client['street'] ?>" class="" type="text" name="street" id="street" required /></div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Ward:</div>
                                                                            <div class="col-md-9"><input value="<?= $client['ward'] ?>" class="" type="text" name="ward" id="ward" required /></div>
                                                                        </div>

                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">House Number:</div>
                                                                            <div class="col-md-9"><input value="<?= $client['block_no'] ?>" class="" type="text" name="block_no" id="block_no" /></div>
                                                                        </div>
                                                                        <div class="row-form clearfix">
                                                                            <div class="col-md-3">Comments:</div>
                                                                            <div class="col-md-9"><textarea name="comments" rows="4"><?= $client['comments'] ?></textarea> </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="dr"><span></span></div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <input type="hidden" name="client_image" value="<?= $client['client_image'] ?>" />
                                                                <input type="hidden" name="id" value="<?= $client['id'] ?>">
                                                                <input type="submit" name="edit_client" value="Save updates" class="btn btn-warning">
                                                                <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="modal fade" id="delete<?= $client['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <form method="post">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                <h4>Delete User</h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <strong style="font-weight: bold;color: red">
                                                                    <p>Are you sure you want to delete this user</p>
                                                                </strong>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <input type="hidden" name="id" value="<?= $client['id'] ?>">
                                                                <input type="submit" name="delete_staff" value="Delete" class="btn btn-danger">
                                                                <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="modal fade" id="img<?= $client['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <form method="post">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                <h4>Client Image</h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <img src="<?= $img ?>" width="350">
                                                            </div>
                                                            <div class="modal-footer">
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
                            </div>
                            <div class="pull-right">
                                <div class="btn-group">
                                    <a href="info.php?id=3&sid=&page=<?php if (($_GET['page'] - 1) > 0) {
                                                                            echo $_GET['page'] - 1;
                                                                        } else {
                                                                            echo 1;
                                                                        } ?>" class="btn btn-default">
                                        < </a>
                                            <?php for ($i = 1; $i <= $pages; $i++) { ?>
                                                <a href="info.php?id=3&sid=&page=<?= $_GET['id'] ?>&page=<?= $i ?>" class="btn btn-default <?php if ($i == $_GET['page']) {
                                                                                                                                                echo 'active';
                                                                                                                                            } ?>"><?= $i ?></a>
                                            <?php } ?>
                                            <a href="info.php?id=3&sid=&page=<?php if (($_GET['page'] + 1) <= $pages) {
                                                                                    echo $_GET['page'] + 1;
                                                                                } else {
                                                                                    echo $i - 1;
                                                                                } ?>" class="btn btn-default"> > </a>
                                </div>
                            </div>
                        </div>
                    <?php } elseif ($_GET['id'] == 9) { ?>
                        <?php $patient = $override->get('crf2', 'patient_id', $_GET['cid'])[0] ?>
                        <div class="col-md-offset-1 col-md-8">
                            <div class="head clearfix">
                                <div class="isw-ok"></div>
                                <h1>CRF 2: VITAL SIGN MEASUREMENTS (STANDARD) AND PHYSICAL EXAMINATION</h1>
                            </div>
                            <div class="block-fluid">
                                <form id="validation" method="post">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Date</label>
                                                    <input value="<?= $patient['crf2_date'] ?>" class="validate[required,custom[date]]" type="text" name="crf2_date" id="crf2_date" required /> <span>Example: 2023-01-01</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Height</label>
                                                    <input value="<?= $patient['height'] ?>" type="text" name="height" id="height" required /> <span>cm</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Weight</label>
                                                    <input value="<?= $patient['weight'] ?>" type="text" name="weight" id="weight" required /> <span>kg</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>BMI</label>
                                                    <button onclick="calculateBMI()">Calculate</button>
                                                    <div id="bmi"><span>kg/m2:</span></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Time</label>
                                                    <input value="<?= $patient['time'] ?>" type="text" name="time" id="time" required /> <span>(using the 24-hour format of hh: mm):</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Temperature</label>
                                                    <input value="<?= $patient['temperature'] ?>" type="text" name="temperature" id="temperature" required /> <span>Celsius:</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Method</label>
                                                    <select name="method" style="width: 100%;" required>
                                                        <?php if ($patient['method'] == "1") { ?>
                                                            <option value="<?= $patient['method'] ?>">Oral</option>
                                                        <?php } elseif ($patient['method'] == "2") { ?>
                                                            <option value="<?= $patient['method'] ?>">Axillary</option>
                                                        <?php } elseif ($patient['method'] == "3") { ?>
                                                            <option value="<?= $patient['method'] ?>">Tympanic</option>
                                                        <?php } ?>
                                                        <option value="1">Oral</option>
                                                        <option value="2">Axillary</option>
                                                        <option value="3">Tympanic</option>
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
                                                    <label>Respiratory Rate</label>
                                                    <input value="<?= $patient['respiratory_rate'] ?>" type="text" name="respiratory_rate" id="respiratory_rate" required /> <span>breaths/min:</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Heart Rate</label>
                                                    <input value="<?= $patient['heart_rate'] ?>" type="text" name="heart_rate" id="heart_rate" required /> <span>beats/min:</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Systolic Blood Pressure:</label>
                                                    <input value="<?= $patient['systolic'] ?>" type="text" name="systolic" id="systolic" required /> <span>mmHg:</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Diastolic Blood Pressure</label>
                                                    <input value="<?= $patient['diastolic'] ?>" type="text" name="diastolic" id="diastolic" required /> <span>mmHg:</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="head clearfix">
                                        <div class="isw-ok"></div>
                                        <h1>PHYSICAL EXAMINATION</h1>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Physical Examnitaion Time:</div>
                                        <div class="col-md-9"><input value="<?= $patient['time2'] ?>" type="text" name="time2" id="time2" required /> <span>(using the 24-hour format of hh: mm):</span></div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>General Appearance:</label>
                                                    <select name="appearance" id="appearance" style="width: 100%;" required>
                                                        <?php if ($patient['appearance'] == "1") { ?>
                                                            <option value="<?= $patient['appearance'] ?>">Normal</option>
                                                        <?php } elseif ($patient['appearance'] == "2") { ?>
                                                            <option value="<?= $patient['appearance'] ?>">Abnormal</option>
                                                        <?php } elseif ($patient['appearance'] == "3") { ?>
                                                            <option value="<?= $patient['appearance'] ?>">Not examined</option>
                                                        <?php } ?>
                                                        <option value="1">Normal</option>
                                                        <option value="2">Abnormal</option>
                                                        <option value="3">Not examined</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-sm-4" id="appearance_comments">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Comments:</label>
                                                    <input value="<?= $patient['appearance_comments'] ?>" type="text" name="appearance_comments" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4" id="appearance_signifcnt">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Clinically Significant?:</label>
                                                    <select name="appearance_signifcnt" style="width: 100%;">
                                                        <?php if ($patient['appearance_signifcnt'] == "1") { ?>
                                                            <option value="<?= $patient['appearance_signifcnt'] ?>">Yes</option>
                                                        <?php } elseif ($patient['appearance_signifcnt'] == "2") { ?>
                                                            <option value="<?= $patient['appearance_signifcnt'] ?>">No</option>
                                                        <?php } ?>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>H/E/E/N/T:</label>
                                                    <select name="heent" id="heent" style="width: 100%;" required>
                                                        <?php if ($patient['heent'] == "1") { ?>
                                                            <option value="<?= $patient['heent'] ?>">Normal</option>
                                                        <?php } elseif ($patient['heent'] == "2") { ?>
                                                            <option value="<?= $patient['heent'] ?>">Abnormal</option>
                                                        <?php } elseif ($patient['heent'] == "3") { ?>
                                                            <option value="<?= $patient['heent'] ?>">Not examined</option>
                                                        <?php } ?>
                                                        <option value="1">Normal</option>
                                                        <option value="2">Abnormal</option>
                                                        <option value="3">Not examined</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-sm-4" id="heent_comments">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Comments:</label>
                                                    <input value="<?= $patient['heent_comments'] ?>" type="text" name="heent_comments" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4" id="heent_signifcnt">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Clinically Significant?:</label>
                                                    <select name="heent_signifcnt" style="width: 100%;">
                                                        <?php if ($patient['heent_signifcnt'] == "1") { ?>
                                                            <option value="<?= $patient['heent_signifcnt'] ?>">Yes</option>
                                                        <?php } elseif ($patient['heent_signifcnt'] == "2") { ?>
                                                            <option value="<?= $patient['heent_signifcnt'] ?>">No</option>
                                                        <?php } ?>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Respiratory:</label>
                                                    <select name="respiratory" id="respiratory" style="width: 100%;" required>
                                                        <?php if ($patient['heent'] == "1") { ?>
                                                            <option value="<?= $patient['respiratory'] ?>">Normal</option>
                                                        <?php } elseif ($patient['respiratory'] == "2") { ?>
                                                            <option value="<?= $patient['respiratory'] ?>">Abnormal</option>
                                                        <?php } elseif ($patient['respiratory'] == "3") { ?>
                                                            <option value="<?= $patient['respiratory'] ?>">Not examined</option>
                                                        <?php } ?>
                                                        <option value="1">Normal</option>
                                                        <option value="2">Abnormal</option>
                                                        <option value="3">Not examined</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-sm-4" id="respiratory_comments">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Comments:</label>
                                                    <input value="<?= $patient['respiratory_comments'] ?>" type="text" name="respiratory_comments" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4" id="respiratory_signifcnt">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Clinically Significant?:</label>
                                                    <select name="respiratory_signifcnt" style="width: 100%;">
                                                        <?php if ($patient['respiratory_signifcnt'] == "1") { ?>
                                                            <option value="<?= $patient['respiratory_signifcnt'] ?>">Yes</option>
                                                        <?php } elseif ($patient['respiratory_signifcnt'] == "2") { ?>
                                                            <option value="<?= $patient['respiratory_signifcnt'] ?>">No</option>
                                                        <?php } ?>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Cardiovascular:</label>
                                                    <select name="cardiovascular" id="cardiovascular" style="width: 100%;" required>
                                                        <?php if ($patient['cardiovascular'] == "1") { ?>
                                                            <option value="<?= $patient['cardiovascular'] ?>">Normal</option>
                                                        <?php } elseif ($patient['cardiovascular'] == "2") { ?>
                                                            <option value="<?= $patient['cardiovascular'] ?>">Abnormal</option>
                                                        <?php } elseif ($patient['cardiovascular'] == "3") { ?>
                                                            <option value="<?= $patient['cardiovascular'] ?>">Not examined</option>
                                                        <?php } ?>
                                                        <option value="1">Normal</option>
                                                        <option value="2">Abnormal</option>
                                                        <option value="3">Not examined</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-sm-4" id="cardiovascular_comments">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Comments:</label>
                                                    <input value="<?= $patient['cardiovascular_comments'] ?>" type="text" name="cardiovascular_comments" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4" id="cardiovascular_signifcnt">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Clinically Significant?:</label>
                                                    <select name="cardiovascular_signifcnt" style="width: 100%;">
                                                        <?php if ($patient['cardiovascular_signifcnt'] == "1") { ?>
                                                            <option value="<?= $patient['cardiovascular_signifcnt'] ?>">Yes</option>
                                                        <?php } elseif ($patient['cardiovascular_signifcnt'] == "2") { ?>
                                                            <option value="<?= $patient['cardiovascular_signifcnt'] ?>">No</option>
                                                        <?php } ?>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Abdominal/Gastrointestinal:</label>
                                                    <select name="abdnominal" id="abdnominal" style="width: 100%;" required>
                                                        <?php if ($patient['abdnominal'] == "1") { ?>
                                                            <option value="<?= $patient['abdnominal'] ?>">Normal</option>
                                                        <?php } elseif ($patient['abdnominal'] == "2") { ?>
                                                            <option value="<?= $patient['abdnominal'] ?>">Abnormal</option>
                                                        <?php } elseif ($patient['abdnominal'] == "3") { ?>
                                                            <option value="<?= $patient['abdnominal'] ?>">Not examined</option>
                                                        <?php } ?>
                                                        <option value="1">Normal</option>
                                                        <option value="2">Abnormal</option>
                                                        <option value="3">Not examined</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-sm-4" id="abdnominal_comments">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Comments:</label>
                                                    <input value="<?= $patient['abdnominal_comments'] ?>" type="text" name="abdnominal_comments" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4" id="abdnominal_signifcnt">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Clinically Significant?:</label>
                                                    <select name="abdnominal_signifcnt" style="width: 100%;">
                                                        <?php if ($patient['abdnominal_signifcnt'] == "1") { ?>
                                                            <option value="<?= $patient['abdnominal_signifcnt'] ?>">Yes</option>
                                                        <?php } elseif ($patient['abdnominal_signifcnt'] == "2") { ?>
                                                            <option value="<?= $patient['abdnominal_signifcnt'] ?>">No</option>
                                                        <?php } ?>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Urogenital:</label>
                                                    <select name="urogenital" id="urogenital" style="width: 100%;" required>
                                                        <?php if ($patient['urogenital'] == "1") { ?>
                                                            <option value="<?= $patient['urogenital'] ?>">Normal</option>
                                                        <?php } elseif ($patient['urogenital'] == "2") { ?>
                                                            <option value="<?= $patient['urogenital'] ?>">Abnormal</option>
                                                        <?php } elseif ($patient['urogenital'] == "3") { ?>
                                                            <option value="<?= $patient['urogenital'] ?>">Not examined</option>
                                                        <?php } ?>
                                                        <option value="1">Normal</option>
                                                        <option value="2">Abnormal</option>
                                                        <option value="3">Not examined</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-sm-4" id="urogenital_comments">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Comments:</label>
                                                    <input value="<?= $patient['urogenital_comments'] ?>" type="text" name="urogenital_comments" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4" id="urogenital_signifcnt">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Clinically Significant?:</label>
                                                    <select name="urogenital_signifcnt" style="width: 100%;">
                                                        <?php if ($patient['urogenital_signifcnt'] == "1") { ?>
                                                            <option value="<?= $patient['urogenital_signifcnt'] ?>">Yes</option>
                                                        <?php } elseif ($patient['urogenital_signifcnt'] == "2") { ?>
                                                            <option value="<?= $patient['urogenital_signifcnt'] ?>">No</option>
                                                        <?php } ?>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Musculoskeletal:</label>
                                                    <select name="musculoskeletal" id="musculoskeletal" style="width: 100%;" required>
                                                        <?php if ($patient['musculoskeletal'] == "1") { ?>
                                                            <option value="<?= $patient['musculoskeletal'] ?>">Normal</option>
                                                        <?php } elseif ($patient['musculoskeletal'] == "2") { ?>
                                                            <option value="<?= $patient['musculoskeletal'] ?>">Abnormal</option>
                                                        <?php } elseif ($patient['musculoskeletal'] == "3") { ?>
                                                            <option value="<?= $patient['musculoskeletal'] ?>">Not examined</option>
                                                        <?php } ?>
                                                        <option value="1">Normal</option>
                                                        <option value="2">Abnormal</option>
                                                        <option value="3">Not examined</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-sm-4" id="musculoskeletal_comments">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Comments:</label>
                                                    <input value="<?= $patient['musculoskeletal_comments'] ?>" type="text" name="musculoskeletal_comments" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4" id="musculoskeletal_signifcnt">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Clinically Significant?:</label>
                                                    <select name="musculoskeletal_signifcnt" style="width: 100%;">
                                                        <?php if ($patient['musculoskeletal_signifcnt'] == "1") { ?>
                                                            <option value="<?= $patient['musculoskeletal_signifcnt'] ?>">Yes</option>
                                                        <?php } elseif ($patient['musculoskeletal_signifcnt'] == "2") { ?>
                                                            <option value="<?= $patient['musculoskeletal_signifcnt'] ?>">No</option>
                                                        <?php } ?>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Neurological:</label>
                                                    <select name="neurological" id="neurological" style="width: 100%;" required>
                                                        <?php if ($patient['neurological'] == "1") { ?>
                                                            <option value="<?= $patient['neurological'] ?>">Normal</option>
                                                        <?php } elseif ($patient['neurological'] == "2") { ?>
                                                            <option value="<?= $patient['neurological'] ?>">Abnormal</option>
                                                        <?php } elseif ($patient['neurological'] == "3") { ?>
                                                            <option value="<?= $patient['neurological'] ?>">Not examined</option>
                                                        <?php } ?>
                                                        <option value="1">Normal</option>
                                                        <option value="2">Abnormal</option>
                                                        <option value="3">Not examined</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-sm-4" id="neurological_comments">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Comments:</label>
                                                    <input value="<?= $patient['neurological_comments'] ?>" type="text" name="neurological_comments" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4" id="neurological_signifcnt">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Clinically Significant?:</label>
                                                    <select name="neurological_signifcnt" style="width: 100%;">
                                                        <?php if ($patient['neurological_signifcnt'] == "1") { ?>
                                                            <option value="<?= $patient['neurological_signifcnt'] ?>">Yes</option>
                                                        <?php } elseif ($patient['neurological_signifcnt'] == "2") { ?>
                                                            <option value="<?= $patient['neurological_signifcnt'] ?>">No</option>
                                                        <?php } ?>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Psychological:</label>
                                                    <select name="psychological" id="psychological" style="width: 100%;" required>
                                                        <?php if ($patient['heent'] == "1") { ?>
                                                            <option value="<?= $patient['psychological'] ?>">Normal</option>
                                                        <?php } elseif ($patient['psychological'] == "2") { ?>
                                                            <option value="<?= $patient['psychological'] ?>">Abnormal</option>
                                                        <?php } elseif ($patient['psychological'] == "3") { ?>
                                                            <option value="<?= $patient['psychological'] ?>">Not examined</option>
                                                        <?php } ?>
                                                        <option value="1">Normal</option>
                                                        <option value="2">Abnormal</option>
                                                        <option value="3">Not examined</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-sm-4" id="psychological_comments">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Comments:</label>
                                                    <input value="<?= $patient['psychological_comments'] ?>" type="text" name="psychological_comments" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4" id="psychological_signifcnt">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Clinically Significant?:</label>
                                                    <select name="psychological_signifcnt" style="width: 100%;">
                                                        <?php if ($patient['psychological_signifcnt'] == "1") { ?>
                                                            <option value="<?= $patient['psychological_signifcnt'] ?>">Yes</option>
                                                        <?php } elseif ($patient['psychological_signifcnt'] == "2") { ?>
                                                            <option value="<?= $patient['psychological_signifcnt'] ?>">No</option>
                                                        <?php } ?>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Endocrine:</label>
                                                    <select name="endocrime" id="endocrime" style="width: 100%;" required>
                                                        <?php if ($patient['endocrime'] == "1") { ?>
                                                            <option value="<?= $patient['endocrime'] ?>">Normal</option>
                                                        <?php } elseif ($patient['endocrime'] == "2") { ?>
                                                            <option value="<?= $patient['endocrime'] ?>">Abnormal</option>
                                                        <?php } elseif ($patient['endocrime'] == "3") { ?>
                                                            <option value="<?= $patient['endocrime'] ?>">Not examined</option>
                                                        <?php } ?>
                                                        <option value="1">Normal</option>
                                                        <option value="2">Abnormal</option>
                                                        <option value="3">Not examined</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-sm-4" id="endocrime_comments">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Comments:</label>
                                                    <input value="<?= $patient['endocrime_comments'] ?>" type="text" name="endocrime_comments" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4" id="endocrime_signifcnt">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Clinically Significant?:</label>
                                                    <select name="endocrime_signifcnt" style="width: 100%;">
                                                        <?php if ($patient['endocrime_signifcnt'] == "1") { ?>
                                                            <option value="<?= $patient['endocrime_signifcnt'] ?>">Yes</option>
                                                        <?php } elseif ($patient['endocrime_signifcnt'] == "2") { ?>
                                                            <option value="<?= $patient['endocrime_signifcnt'] ?>">No</option>
                                                        <?php } ?>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Lymphatic:</label>
                                                    <select name="lymphatic" id="lymphatic" style="width: 100%;" required>
                                                        <?php if ($patient['lymphatic'] == "1") { ?>
                                                            <option value="<?= $patient['lymphatic'] ?>">Normal</option>
                                                        <?php } elseif ($patient['lymphatic'] == "2") { ?>
                                                            <option value="<?= $patient['lymphatic'] ?>">Abnormal</option>
                                                        <?php } elseif ($patient['lymphatic'] == "3") { ?>
                                                            <option value="<?= $patient['lymphatic'] ?>">Not examined</option>
                                                        <?php } ?>
                                                        <option value="1">Normal</option>
                                                        <option value="2">Abnormal</option>
                                                        <option value="3">Not examined</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-sm-4" id="lymphatic_comments">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Comments:</label>
                                                    <input value="<?= $patient['lymphatic_comments'] ?>" type="text" name="lymphatic_comments" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4" id="lymphatic_signifcnt">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Clinically Significant?:</label>
                                                    <select name="lymphatic_signifcnt" style="width: 100%;">
                                                        <?php if ($patient['lymphatic_signifcnt'] == "1") { ?>
                                                            <option value="<?= $patient['lymphatic_signifcnt'] ?>">Yes</option>
                                                        <?php } elseif ($patient['lymphatic_signifcnt'] == "2") { ?>
                                                            <option value="<?= $patient['lymphatic_signifcnt'] ?>">No</option>
                                                        <?php } ?>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Skin/Dermatological:</label>
                                                    <select name="skin" id="skin" style="width: 100%;" required>
                                                        <?php if ($patient['skin'] == "1") { ?>
                                                            <option value="<?= $patient['skin'] ?>">Normal</option>
                                                        <?php } elseif ($patient['skin'] == "2") { ?>
                                                            <option value="<?= $patient['skin'] ?>">Abnormal</option>
                                                        <?php } elseif ($patient['skin'] == "3") { ?>
                                                            <option value="<?= $patient['skin'] ?>">Not examined</option>
                                                        <?php } ?>
                                                        <option value="1">Normal</option>
                                                        <option value="2">Abnormal</option>
                                                        <option value="3">Not examined</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-sm-4" id="skin_comments">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Comments:</label>
                                                    <input value="<?= $patient['skin_comments'] ?>" type="text" name="skin_comments" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4" id="skin_signifcnt">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Clinically Significant?:</label>
                                                    <select name="skin_signifcnt" style="width: 100%;">
                                                        <?php if ($patient['skin_signifcnt'] == "1") { ?>
                                                            <option value="<?= $patient['skin_signifcnt'] ?>">Yes</option>
                                                        <?php } elseif ($patient['skin_signifcnt'] == "2") { ?>
                                                            <option value="<?= $patient['skin_signifcnt'] ?>">No</option>
                                                        <?php } ?>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
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
                                                    <label>Is there any Other physical System?:</label>
                                                    <select name="physical_exams_other" id="physical_exams_other" style="width: 100%;" required>
                                                        <?php if ($patient['physical_exams_other'] == "1") { ?>
                                                            <option value="<?= $patient['physical_exams_other'] ?>">Yes</option>
                                                        <?php } elseif ($patient['physical_exams_other'] == "2") { ?>
                                                            <option value="<?= $patient['physical_exams_other'] ?>">No</option>
                                                        <?php } ?>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6" id="physical_other_specify">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Other (specify):</label>
                                                    <input value="<?= $patient['physical_other_specify'] ?>" type="text" name="physical_other_specify" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-3" id="physical_other_system1">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Finding:</label>
                                                    <select name="physical_other_system" id="physical_other_system" style="width: 100%;">
                                                        <?php if ($patient['physical_other_system'] == "1") { ?>
                                                            <option value="<?= $patient['physical_other_system'] ?>">Normal</option>
                                                        <?php } elseif ($patient['physical_other_system'] == "2") { ?>
                                                            <option value="<?= $patient['physical_other_system'] ?>">Abnormal</option>
                                                        <?php } elseif ($patient['physical_other_system'] == "3") { ?>
                                                            <option value="<?= $patient['physical_other_system'] ?>">Not examined</option>
                                                        <?php } ?>
                                                        <option value="1">Normal</option>
                                                        <option value="2">Abnormal</option>
                                                        <option value="3">Not examined</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6" id="physical_other_comments">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Comments:</label>
                                                    <input value="<?= $patient['physical_other_comments'] ?>" type="text" name="physical_other_comments" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6" id="physical_other_signifcnt">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Clinically Significant?:</label>
                                                    <select name="physical_other_signifcnt" style="width: 100%;">
                                                        <?php if ($patient['physical_other_signifcnt'] == "1") { ?>
                                                            <option value="<?= $patient['physical_other_signifcnt'] ?>">Yes</option>
                                                        <?php } elseif ($patient['physical_other_signifcnt'] == "2") { ?>
                                                            <option value="<?= $patient['physical_other_signifcnt'] ?>">No</option>
                                                        <?php } ?>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Additional Notes:</label>
                                                    <input value="<?= $patient['additional_notes'] ?>" type="text" name="additional_notes" id="additional_notes" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Physical Examination performed by:</label>
                                                    <input value="<?= $patient['physical_performed'] ?>" type="text" name="physical_performed" id="physical_performed" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row-form clearfix" id="crf2_cmpltd_date">
                                        <div class="col-md-3">Date of Completion</div>
                                        <input value="<?= $patient['crf2_cmpltd_date'] ?>" class="validate[required]" type="text" name="crf2_cmpltd_date" />
                                    </div>

                                    <div class="footer tar">
                                        <input type="hidden" name="id" value="<?= $patient['id'] ?>">
                                        <input type="submit" name="update_crf2" value="Update CRF2" class="btn btn-default">
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php } elseif ($_GET['id'] == 10) { ?>
                        <?php $patient = $override->get('crf3', 'patient_id', $_GET['cid'])[0] ?>
                        <div class="col-md-offset-1 col-md-8">
                            <div class="head clearfix">
                                <div class="isw-ok"></div>
                                <h1>CRF 3: SHORT-TERM QUESTIONNAIRE AT BASELINE AND FOLLOW-UP</h1>
                            </div>
                            <div class="block-fluid">
                                <form id="validation" method="post">
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Date:</div>
                                        <div class="col-md-9"><input value="<?= $patient['crf3_date'] ?>" class="validate[required,custom[date]]" type="text" name="crf3_date" id="crf3_date" required /> <span>Example: 2023-01-01</span></div>
                                    </div>

                                    <div class="head clearfix">
                                        <div class="isw-ok"></div>
                                        <h1>CLINICAL SYMPTOMS</h1>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>A. Fever:</label>
                                                    <select name="fever" id="fever" style="width: 100%;" required>
                                                        <?php if ($patient['fever'] == "1") { ?>
                                                            <option value="<?= $patient['fever'] ?>">Yes</option>
                                                        <?php } elseif ($patient['fever'] == "2") { ?>
                                                            <option value="<?= $patient['fever'] ?>">No</option>
                                                        <?php } ?>
                                                            <option value="">Select</option>
                                                            <option value="1">Yes</option>
                                                            <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>B. Vomiting:</label>
                                                    <select name="vomiting" id="vomiting" style="width: 100%;" required>
                                                        <?php if ($patient['vomiting'] == "1") { ?>
                                                            <option value="<?= $patient['vomiting'] ?>">Yes</option>
                                                        <?php } elseif ($patient['vomiting'] == "2") { ?>
                                                            <option value="<?= $patient['vomiting'] ?>">No</option>
                                                        <?php } ?>
                                                        <option value="">Select</option>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>G. Diarrhoea:</label>
                                                    <select name="diarrhoea" id="diarrhoea" style="width: 100%;" required>
                                                        <?php if ($patient['diarrhoea'] == "1") { ?>
                                                            <option value="<?= $patient['diarrhoea'] ?>">Yes</option>
                                                        <?php } elseif ($patient['diarrhoea'] == "2") { ?>
                                                            <option value="<?= $patient['diarrhoea'] ?>">No</option>
                                                        <?php } ?>
                                                        <option value="">Select</option>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>H. Headaches:</label>
                                                    <select name="headaches" id="headaches" style="width: 100%;" required>
                                                        <?php if ($patient['headaches'] == "1") { ?>
                                                            <option value="<?= $patient['headaches'] ?>">Yes</option>
                                                        <?php } elseif ($patient['headaches'] == "2") { ?>
                                                            <option value="<?= $patient['headaches'] ?>">No</option>
                                                        <?php } ?>
                                                        <option value="">Select</option>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
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
                                                    <label>I. Difficulty in breathing:</label>
                                                    <select name="difficult_breathing" id="difficult_breathing" style="width: 100%;" required>
                                                        <?php if ($patient['difficult_breathing'] == "1") { ?>
                                                            <option value="<?= $patient['difficult_breathing'] ?>">Yes</option>
                                                        <?php } elseif ($patient['difficult_breathing'] == "2") { ?>
                                                            <option value="<?= $patient['difficult_breathing'] ?>">No</option>
                                                        <?php } ?>
                                                        <option value="">Select</option>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>K. Sore throat:</label>
                                                    <select name="sore_throat" id="sore_throat" style="width: 100%;" required>
                                                        <?php if ($patient['sore_throat'] == "1") { ?>
                                                            <option value="<?= $patient['sore_throat'] ?>">Yes</option>
                                                        <?php } elseif ($patient['sore_throat'] == "2") { ?>
                                                            <option value="<?= $patient['sore_throat'] ?>">No</option>
                                                        <?php } ?>
                                                        <option value="">Select</option>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>L. Fatigue:</label>
                                                    <select name="fatigue" id="fatigue" style="width: 100%;" required>
                                                        <?php if ($patient['fatigue'] == "1") { ?>
                                                            <option value="<?= $patient['fatigue'] ?>">Yes</option>
                                                        <?php } elseif ($patient['fatigue'] == "2") { ?>
                                                            <option value="<?= $patient['fatigue'] ?>">No</option>
                                                        <?php } ?>
                                                        <option value="">Select</option>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>M. Muscle pain:</label>
                                                    <select name="muscle_pain" id="muscle_pain" style="width: 100%;" required>
                                                        <?php if ($patient['muscle_pain'] == "1") { ?>
                                                            <option value="<?= $patient['muscle_pain'] ?>">Yes</option>
                                                        <?php } elseif ($patient['muscle_pain'] == "2") { ?>
                                                            <option value="<?= $patient['muscle_pain'] ?>">No</option>
                                                        <?php } ?>
                                                        <option value="">Select</option>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
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
                                                    <label>N. Loss of consciousness:</label>
                                                    <select name="loss_consciousness" id="loss_consciousness" style="width: 100%;" required>
                                                        <?php if ($patient['loss_consciousness'] == "1") { ?>
                                                            <option value="<?= $patient['loss_consciousness'] ?>">Yes</option>
                                                        <?php } elseif ($patient['loss_consciousness'] == "2") { ?>
                                                            <option value="<?= $patient['loss_consciousness'] ?>">No</option>
                                                        <?php } ?>
                                                        <option value="">Select</option>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>N. Backpain:</label>
                                                    <select name="backpain" id="backpain" style="width: 100%;" required>
                                                        <?php if ($patient['backpain'] == "1") { ?>
                                                            <option value="<?= $patient['backpain'] ?>">Yes</option>
                                                        <?php } elseif ($patient['backpain'] == "2") { ?>
                                                            <option value="<?= $patient['backpain'] ?>">No</option>
                                                        <?php } ?>
                                                        <option value="">Select</option>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>N. Unexplained weight loss:</label>
                                                    <select name="weight_loss" style="width: 100%;">
                                                        <?php if ($patient['weight_loss'] == "1") { ?>
                                                            <option value="<?= $patient['weight_loss'] ?>">Yes</option>
                                                        <?php } elseif ($patient['weight_loss'] == "2") { ?>
                                                            <option value="<?= $patient['weight_loss'] ?>">No</option>
                                                        <?php } ?>
                                                        <option value="">Select</option>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>N. Heartburn and Indigestion:</label>
                                                    <select name="heartburn_indigestion" style="width: 100%;">
                                                        <?php if ($patient['heartburn_indigestion'] == "1") { ?>
                                                            <option value="<?= $patient['heartburn_indigestion'] ?>">Yes</option>
                                                        <?php } elseif ($patient['heartburn_indigestion'] == "2") { ?>
                                                            <option value="<?= $patient['heartburn_indigestion'] ?>">No</option>
                                                        <?php } ?>
                                                        <option value="">Select</option>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
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
                                                    <label>N. Swelling:</label>
                                                    <select name="swelling" style="width: 100%;">
                                                        <?php if ($patient['swelling'] == "1") { ?>
                                                            <option value="<?= $patient['swelling'] ?>">Yes</option>
                                                        <?php } elseif ($patient['swelling'] == "2") { ?>
                                                            <option value="<?= $patient['swelling'] ?>">No</option>
                                                        <?php } ?>
                                                        <option value="">Select</option>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>N. Abnormal PV bleeding:</label>
                                                    <select name="pv_bleeding" id="pv_bleeding" style="width: 100%;" required>
                                                        <?php if ($patient['pv_bleeding'] == "1") { ?>
                                                            <option value="<?= $patient['pv_bleeding'] ?>">Yes</option>
                                                        <?php } elseif ($patient['pv_bleeding'] == "2") { ?>
                                                            <option value="<?= $patient['pv_bleeding'] ?>">No</option>
                                                        <?php } ?>
                                                        <option value="">Select</option>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>N. Abnormal PV discharge:</label>
                                                    <select name="pv_discharge" id="pv_discharge" style="width: 100%;" required>
                                                        <?php if ($patient['pv_discharge'] == "1") { ?>
                                                            <option value="<?= $patient['pv_discharge'] ?>">Yes</option>
                                                        <?php } elseif ($patient['pv_discharge'] == "2") { ?>
                                                            <option value="<?= $patient['pv_discharge'] ?>">No</option>
                                                        <?php } ?>
                                                        <option value="">Select</option>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>N. Abnormal micitrition habits:</label>
                                                    <select name="micitrition" id="micitrition" style="width: 100%;" required>
                                                        <?php if ($patient['micitrition'] == "1") { ?>
                                                            <option value="<?= $patient['micitrition'] ?>">Yes</option>
                                                        <?php } elseif ($patient['micitrition'] == "2") { ?>
                                                            <option value="<?= $patient['micitrition'] ?>">No</option>
                                                        <?php } ?>
                                                        <option value="">Select</option>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="row">

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>N. Convulsions:</label>
                                                    <select name="convulsions" id="convulsions" style="width: 100%;" required>
                                                        <?php if ($patient['convulsions'] == "1") { ?>
                                                            <option value="<?= $patient['convulsions'] ?>">Yes</option>
                                                        <?php } elseif ($patient['convulsions'] == "2") { ?>
                                                            <option value="<?= $patient['convulsions'] ?>">No</option>
                                                        <?php } ?>
                                                        <option value="">Select</option>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>N. Blood in urine:</label>
                                                    <select name="blood_urine" id="blood_urine" style="width: 100%;" required>
                                                        <?php if ($patient['blood_urine'] == "1") { ?>
                                                            <option value="<?= $patient['blood_urine'] ?>">Yes</option>
                                                        <?php } elseif ($patient['blood_urine'] == "2") { ?>
                                                            <option value="<?= $patient['blood_urine'] ?>">No</option>
                                                        <?php } ?>
                                                        <option value="">Select</option>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>S. Other symptoms:</label>
                                                    <select name="symptoms_other" id="symptoms_other" style="width: 100%;" required>
                                                        <?php if ($patient['symptoms_other'] == "1") { ?>
                                                            <option value="<?= $patient['symptoms_other'] ?>">Yes</option>
                                                        <?php } elseif ($patient['symptoms_other'] == "2") { ?>
                                                            <option value="<?= $patient['symptoms_other'] ?>">No</option>
                                                        <?php } ?>
                                                        <option value="">Select</option>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6" id="symptoms_other_specify">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>S. Specify:</label>
                                                    <input value="<?= $patient['symptoms_other_specify'] ?>" type="text" name="symptoms_other_specify" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="row-form clearfix">
                                            <!-- select -->
                                            <div class="form-group">
                                                <label>S. Comments:</label>
                                                <input value="<?= $patient['other_comments'] ?>" type="text" name="other_comments" id="other_comments" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="row-form clearfix">
                                            <!-- select -->
                                            <div class="form-group">
                                                <label>Date of Completion:</label>
                                                <input value="<?= $patient['crf3_cmpltd_date'] ?>" class="validate[required]" type="text" name="crf3_cmpltd_date" id="crf3_cmpltd_date" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="footer tar">
                                        <input type="hidden" name="id" value="<?= $patient['id'] ?>">
                                        <input type="submit" name="update_crf3" value="Submit" class="btn btn-default">
                                    </div>
                                </form>
                            </div>
                        </div>

                    <?php } elseif ($_GET['id'] == 11) { ?>


                    <?php } ?>
                </div>

                <div class="dr"><span></span></div>
            </div>
        </div>
    </div>
</body>
<script>
    <?php if ($user->data()->pswd == 0) { ?>
        $(window).on('load', function() {
            $("#change_password_n").modal({
                backdrop: 'static',
                keyboard: false
            }, 'show');
        });
    <?php } ?>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }


    $('#diabetic_medicatn1').hide();
    $('#diabetic').change(function() {
        var getUid = $(this).val();
        if (getUid === "1") {
            $('#diabetic_medicatn1').show();
        } else {
            $('#diabetic_medicatn1').hide();
        }
    });

    $('#diabetic_medicatn_name').hide();
    $('#diabetic_medicatn').change(function() {
        var getUid = $(this).val();
        if (getUid === "1") {
            $('#diabetic_medicatn_name').show();
        } else {
            $('#diabetic_medicatn_name').hide();
        }
    });


    $('#hypertension_medicatn1').hide();
    $('#hypertension').change(function() {
        var getUid = $(this).val();
        if (getUid === "1") {
            $('#hypertension_medicatn1').show();
        } else {
            $('#hypertension_medicatn1').hide();
        }
    });

    $('#hypertension_medicatn_name').hide();
    $('#hypertension_medicatn').change(function() {
        var getUid = $(this).val();
        if (getUid === "1") {
            $('#hypertension_medicatn_name').show();
        } else {
            $('#hypertension_medicatn_name').hide();
        }
    });


    $('#heart_medicatn1').hide();
    $('#heart').change(function() {
        var getUid = $(this).val();
        if (getUid === "1") {
            $('#heart_medicatn1').show();
        } else {
            $('#heart_medicatn1').hide();
        }
    });

    $('#heart_medicatn_name').hide();
    $('#heart_medicatn').change(function() {
        var getUid = $(this).val();
        if (getUid === "1") {
            $('#heart_medicatn_name').show();
        } else {
            $('#heart_medicatn_name').hide();
        }
    });


    $('#asthma_medicatn1').hide();
    $('#asthma').change(function() {
        var getUid = $(this).val();
        if (getUid === "1") {
            $('#asthma_medicatn1').show();
        } else {
            $('#asthma_medicatn1').hide();
        }
    });

    $('#asthma_medicatn_name').hide();
    $('#asthma_medicatn').change(function() {
        var getUid = $(this).val();
        if (getUid === "1") {
            $('#asthma_medicatn_name').show();
        } else {
            $('#asthma_medicatn_name').hide();
        }
    });


    $('#chronic_medicatn1').hide();
    $('#chronic').change(function() {
        var getUid = $(this).val();
        if (getUid === "1") {
            $('#chronic_medicatn1').show();
        } else {
            $('#chronic_medicatn1').hide();
        }
    });

    $('#chronic_medicatn_name').hide();
    $('#chronic_medicatn').change(function() {
        var getUid = $(this).val();
        if (getUid === "1") {
            $('#chronic_medicatn_name').show();
        } else {
            $('#chronic_medicatn_name').hide();
        }
    });


    $('#hiv_aids_medicatn1').hide();
    $('#hiv_aids').change(function() {
        var getUid = $(this).val();
        if (getUid === "1") {
            $('#hiv_aids_medicatn1').show();
        } else {
            $('#hiv_aids_medicatn1').hide();
        }
    });

    $('#hiv_aids_medicatn_name').hide();
    $('#hiv_aids_medicatn').change(function() {
        var getUid = $(this).val();
        if (getUid === "1") {
            $('#hiv_aids_medicatn_name').show();
        } else {
            $('#hiv_aids_medicatn_name').hide();
        }
    });

    $('#other_medical_medicatn1').hide();
    $('#other_specify').hide();
    $('#other_medical').change(function() {
        var getUid = $(this).val();
        if (getUid === "1") {
            $('#other_specify').show();
            $('#other_medical_medicatn1').show();
        } else {
            $('#other_specify').hide();
            $('#other_medical_medicatn1').hide();
        }
    });

    $('#other_medicatn_name').hide();
    $('#other_medical_medicatn').change(function() {
        var getUid = $(this).val();
        if (getUid === "1") {
            $('#other_medicatn_name').show();
        } else {
            $('#other_medicatn_name').hide();
        }
    });


    $('#nimregenin_preparation').hide();
    $('#nimregenin_header').hide();
    $('#nimregenin_herbal').change(function() {
        var getUid = $(this).val();
        if (getUid === "1") {
            $('#nimregenin_preparation').show();
            $('#nimregenin_header').show();
        } else {
            $('#nimregenin_header').hide();
            $('#nimregenin_preparation').hide();
        }
    });

    $('#nimregenin_end').hide();
    $('#nimregenin_ongoing').change(function() {
        var getUid = $(this).val();
        if (getUid === "2") {
            $('#nimregenin_end').show();
        } else {
            $('#nimregenin_end').hide();
        }
    });

    $('#herbal_preparation').hide();
    $('#herbal_header').hide();
    $('#other_herbal').change(function() {
        var getUid = $(this).val();
        if (getUid === "1") {
            $('#herbal_preparation').show();
            $('#herbal_header').show();
        } else {
            $('#herbal_header').hide();
            $('#herbal_preparation').hide();
        }
    });

    $('#herbal_end').hide();
    $('#herbal_ongoing').change(function() {
        var getUid = $(this).val();
        if (getUid === "2") {
            $('#herbal_end').show();
        } else {
            $('#herbal_end').hide();
        }
    });


    $('#standard_end').hide();
    $('#standard_ongoing').change(function() {
        var getUid = $(this).val();
        if (getUid === "1") {
            $('#standard_end').show();
        } else {
            $('#standard_end').hide();
        }
    });


    // function calculateBMI() {

    //     let height = parseInt(document.querySelector("#height").value);
    //     let weight = parseInt(document.querySelector("#weight").value);

    //     alert(weight);

    //     let result = document.querySelector("#result");


    //     // validation value or not
    //     if (height === "" || isNaN(height))
    //         result.innerHTML = "Enter a valid Height!";

    //     else if (weight === "" || isNaN(weight))
    //         result.innerHTML = "Enter a valid Weight!";

    //     // If entered value is valid, calculate the BMI
    //     else {

    //         let bmi = (weight / ((height * height) / 10000)).toFixed(2);

    //         // Dividing as per the bmi conditions
    //         if (bmi < 18.6) result.innerHTML =
    //             `Under Weight : <span>${bmi}</span>`;

    //         else if (bmi >= 18.6 && bmi < 24.9)
    //             result.innerHTML =
    //             `Normal : <span>${bmi}</span>`;

    //         else result.innerHTML =
    //             `Over Weight : <span>${bmi}</span>`;
    //     }
    // }


    $('#appearance_comments').hide();
    $('#appearance_signifcnt').hide();
    $('#appearance').change(function() {
        var getUid = $(this).val();
        if (getUid === "2") {
            $('#appearance_comments').show();
            $('#appearance_signifcnt').show();
        } else {
            $('#appearance_comments').hide();
            $('#appearance_signifcnt').hide();
        }
    });

    $('#heent_comments').hide();
    $('#heent_signifcnt').hide();
    $('#heent').change(function() {
        var getUid = $(this).val();
        if (getUid === "2") {
            $('#heent_comments').show();
            $('#heent_signifcnt').show();
        } else {
            $('#heent_comments').hide();
            $('#heent_signifcnt').hide();
        }
    });

    $('#respiratory_comments').hide();
    $('#respiratory_signifcnt').hide();
    $('#respiratory').change(function() {
        var getUid = $(this).val();
        if (getUid === "2") {
            $('#respiratory_comments').show();
            $('#respiratory_signifcnt').show();
        } else {
            $('#respiratory_comments').hide();
            $('#respiratory_signifcnt').hide();
        }
    });

    $('#cardiovascular_comments').hide();
    $('#cardiovascular_signifcnt').hide();
    $('#cardiovascular').change(function() {
        var getUid = $(this).val();
        if (getUid === "2") {
            $('#cardiovascular_comments').show();
            $('#cardiovascular_signifcnt').show();
        } else {
            $('#cardiovascular_comments').hide();
            $('#cardiovascular_signifcnt').hide();
        }
    });

    $('#abdnominal_comments').hide();
    $('#abdnominal_signifcnt').hide();
    $('#abdnominal').change(function() {
        var getUid = $(this).val();
        if (getUid === "2") {
            $('#abdnominal_comments').show();
            $('#abdnominal_signifcnt').show();
        } else {
            $('#abdnominal_comments').hide();
            $('#abdnominal_signifcnt').hide();
        }
    });

    $('#urogenital_comments').hide();
    $('#urogenital_signifcnt').hide();
    $('#urogenital').change(function() {
        var getUid = $(this).val();
        if (getUid === "2") {
            $('#urogenital_comments').show();
            $('#urogenital_signifcnt').show();
        } else {
            $('#urogenital_comments').hide();
            $('#urogenital_signifcnt').hide();
        }
    });


    $('#musculoskeletal_comments').hide();
    $('#musculoskeletal_signifcnt').hide();
    $('#musculoskeletal').change(function() {
        var getUid = $(this).val();
        if (getUid === "2") {
            $('#musculoskeletal_comments').show();
            $('#musculoskeletal_signifcnt').show();
        } else {
            $('#musculoskeletal_comments').hide();
            $('#musculoskeletal_signifcnt').hide();
        }
    });

    $('#neurological_comments').hide();
    $('#neurological_signifcnt').hide();
    $('#neurological').change(function() {
        var getUid = $(this).val();
        if (getUid === "2") {
            $('#neurological_comments').show();
            $('#neurological_signifcnt').show();
        } else {
            $('#neurological_comments').hide();
            $('#neurological_signifcnt').hide();
        }
    });

    $('#psychological_comments').hide();
    $('#psychological_signifcnt').hide();
    $('#psychological').change(function() {
        var getUid = $(this).val();
        if (getUid === "2") {
            $('#psychological_comments').show();
            $('#psychological_signifcnt').show();
        } else {
            $('#psychological_comments').hide();
            $('#psychological_signifcnt').hide();
        }
    });

    $('#endocrime_comments').hide();
    $('#endocrime_signifcnt').hide();
    $('#endocrime').change(function() {
        var getUid = $(this).val();
        if (getUid === "2") {
            $('#endocrime_comments').show();
            $('#endocrime_signifcnt').show();
        } else {
            $('#endocrime_comments').hide();
            $('#endocrime_signifcnt').hide();
        }
    });

    $('#lymphatic_comments').hide();
    $('#lymphatic_signifcnt').hide();
    $('#lymphatic').change(function() {
        var getUid = $(this).val();
        if (getUid === "2") {
            $('#lymphatic_comments').show();
            $('#lymphatic_signifcnt').show();
        } else {
            $('#lymphatic_comments').hide();
            $('#lymphatic_signifcnt').hide();
        }
    });

    $('#skin_comments').hide();
    $('#skin_signifcnt').hide();
    $('#skin').change(function() {
        var getUid = $(this).val();
        if (getUid === "2") {
            $('#skin_comments').show();
            $('#skin_signifcnt').show();
        } else {
            $('#skin_comments').hide();
            $('#skin_signifcnt').hide();
        }
    });

    $('#physical_other_specify').hide();
    $('#physical_other_system1').hide();
    $('#physical_exams_other').change(function() {
        var getUid = $(this).val();
        if (getUid === "1") {
            $('#physical_other_specify').show();
            $('#physical_other_system1').show();
        } else {
            $('#physical_other_specify').hide();
            $('#physical_other_system1').hide();
        }
    });

    $('#physical_other_comments').hide();
    $('#physical_other_signifcnt').hide();
    $('#physical_other_system').change(function() {
        var getUid = $(this).val();
        if (getUid === "2") {
            $('#physical_other_comments').show();
            $('#physical_other_signifcnt').show();
        } else {
            $('#physical_other_comments').hide();
            $('#physical_other_signifcnt').hide();
        }
    });

    $('#symptoms_other_specify').hide();
    $('#symptoms_other').change(function() {
        var getUid = $(this).val();
        if (getUid === "1") {
            $('#symptoms_other_specify').show();
        } else {
            $('#symptoms_other_specify').hide();
        }
    });

    $('#ecg_specify').hide();
    $('#ecg').change(function() {
        var getUid = $(this).val();
        if (getUid === "2") {
            $('#ecg_specify').show();
        } else {
            $('#ecg_specify').hide();
        }
    });

    $('#ct_chest_specify').hide();
    $('#ct_chest').change(function() {
        var getUid = $(this).val();
        if (getUid === "2") {
            $('#ct_chest_specify').show();
        } else {
            $('#ct_chest_specify').hide();
        }
    });

    $('#chest_specify').hide();
    $('#chest_xray').change(function() {
        var getUid = $(this).val();
        if (getUid === "2") {
            $('#chest_specify').show();
        } else {
            $('#chest_specify').hide();
        }
    });



    if ($('#ae_ongoing').val() == "1") {
        $('#ae_end_date').hide();
    } else if ($('#ae_ongoing').change(function() {
            var getUid = $(this).val();
        })) {
        if (getUid === "2") {
            $('#ae_end_date').show();
        } else {
            $('#ae_end_date').hide();
        }
    } else {
        $('#ae_end_date').show();
    }

    if ("#start_end_date" != "") {
        $('#start_end_date').show();
        $('#completed120days').change(function() {
            var getUid = $(this).val();
            if (getUid === "1") {
                $('#start_end_date').show();
            } else {
                $('#start_end_date').hide();
            }
        });
    } else {
        $('#start_end_date').hide();
    }


    $('#death_details').hide();
    $('#reported_dead').change(function() {
        var getUid = $(this).val();
        if (getUid === "1") {
            $('#death_details').show();
        } else {
            $('#death_details').hide();
        }
    });

    $('#withdrew_reason1').hide();
    $('#withdrew_consent').change(function() {
        var getUid = $(this).val();
        if (getUid === "1") {
            $('#withdrew_reason1').show();
        } else {
            $('#withdrew_reason1').hide();
        }
    });

    $('#withdrew_other').hide();
    $('#withdrew_reason').change(function() {
        var getUid = $(this).val();
        if (getUid === "5") {
            $('#withdrew_other').show();
        } else {
            $('#withdrew_other').hide();
        }
    });
</script>

</html>