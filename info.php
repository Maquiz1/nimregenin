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
                if (((Input::get('pregnant') == 2 || Input::get('pregnant') == 3) && (Input::get('breast_feeding') == 2 || Input::get('breast_feeding') == 3) && Input::get('cdk') == 2 && Input::get('liver_disease') == 2)) {
                    if (Input::get('pregnant') == 2 && (Input::get('breast_feeding') == 2 || Input::get('breast_feeding') == 3) && Input::get('cdk') == 2 && Input::get('liver_disease') == 2 && $sc_e['eligibility'] == 1) {
                        $eligibility = 1;
                        if ($override->getCount('visit', 'client_id', Input::get('cid')) == 1) {
                            $user->visit(Input::get('cid'), 0);
                            $user->updateRecord('study_id', array('status' => 1, 'client_id' => Input::get('cid')), $std_id['id']);
                            $user->updateRecord('clients', array('study_id' => $std_id['study_id'], 'enrolled' => 1), Input::get('cid'));
                        }
                    } elseif ((Input::get('pregnant') == 2 || Input::get('pregnant') == 3) && (Input::get('breast_feeding') == 2 || Input::get('breast_feeding') == 3) && Input::get('cdk') == 2 && Input::get('liver_disease') == 2 && $sc_e['eligibility'] == 1) {
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
        } elseif (Input::get('update_crf1')) {
            $validate = $validate->check($_POST, array(
                'diagnosis_date' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('crf1', array(
                        'vid' => $_GET["vid"],
                        'vcode' => $_GET["vcode"],
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
                        'other_specify' => Input::get('other_specify'),
                        'other_medical_medicatn' => Input::get('other_medical_medicatn'),
                        'other_medicatn_name' => Input::get('other_medicatn_name'),
                        'nimregenin_herbal' => Input::get('nimregenin_herbal'),
                        'nimregenin_preparation' => Input::get('nimregenin_preparation'),
                        'nimregenin_start' => Input::get('nimregenin_start'),
                        'nimregenin_ongoing' => Input::get('nimregenin_ongoing'),
                        'nimregenin_end' => Input::get('nimregenin_end'),
                        'nimregenin_dose' => Input::get('nimregenin_dose'),
                        'nimregenin_frequecy' => Input::get('nimregenin_frequecy'),
                        'other_herbal' => Input::get('other_herbal'),
                        'radiotherapy' => Input::get('radiotherapy'),
                        'radiotherapy_start' => Input::get('radiotherapy_start'),
                        'radiotherapy_ongoing' => Input::get('radiotherapy_ongoing'),
                        'radiotherapy_end' => Input::get('radiotherapy_end'),
                        'radiotherapy_dose' => Input::get('radiotherapy_dose'),
                        'radiotherapy_frequecy' => Input::get('radiotherapy_frequecy'),
                        'radiotherapy_remarks' => Input::get('radiotherapy_remarks'),
                        'crf1_cmpltd_date' => Input::get('crf1_cmpltd_date'),
                        'patient_id' => $_GET['cid'],
                        'staff_id' => $user->data()->id,
                        'status' => 1,
                        'created_on' => date('Y-m-d'),
                        'site_id' => $user->data()->site_id,
                    ), Input::get('id'));

                    $user->updateRecord('herbal_treatment', array(
                        'vid' => $_GET["vid"],
                        'vcode' => $_GET["vcode"],
                        'other_herbal' => Input::get('other_herbal'),
                        'herbal_preparation1' => Input::get('herbal_preparation1'),
                        'herbal_start1' => Input::get('herbal_start1'),
                        'herbal_ongoing1' => Input::get('herbal_ongoing1'),
                        'herbal_end1' => Input::get('herbal_end1'),
                        'herbal_dose1' => Input::get('herbal_dose1'),
                        'herbal_frequency1' => Input::get('herbal_frequency1'),
                        'herbal_preparation2' => Input::get('herbal_preparation2'),
                        'herbal_start2' => Input::get('herbal_start2'),
                        'herbal_ongoing2' => Input::get('herbal_ongoing2'),
                        'herbal_end2' => Input::get('herbal_end2'),
                        'herbal_dose2' => Input::get('herbal_dose2'),
                        'herbal_frequency2' => Input::get('herbal_frequency2'),
                        'herbal_preparation3' => Input::get('herbal_preparation3'),
                        'herbal_start3' => Input::get('herbal_start3'),
                        'herbal_ongoing3' => Input::get('herbal_ongoing3'),
                        'herbal_end3' => Input::get('herbal_end3'),
                        'herbal_dose3' => Input::get('herbal_dose3'),
                        'herbal_frequency3' => Input::get('herbal_frequency3'),
                        'herbal_preparation4' => Input::get('herbal_preparation4'),
                        'herbal_start4' => Input::get('herbal_start4'),
                        'herbal_ongoing4' => Input::get('herbal_ongoing4'),
                        'herbal_end4' => Input::get('herbal_end4'),
                        'herbal_dose4' => Input::get('herbal_dose4'),
                        'herbal_frequency4' => Input::get('herbal_frequency4'),
                        'herbal_preparation5' => Input::get('herbal_preparation5'),
                        'herbal_start5' => Input::get('herbal_start5'),
                        'herbal_ongoing5' => Input::get('herbal_ongoing5'),
                        'herbal_end5' => Input::get('herbal_end5'),
                        'herbal_dose5' => Input::get('herbal_dose5'),
                        'herbal_frequency5' => Input::get('herbal_frequency5'),
                        'crf1_cmpltd_date' => Input::get('crf1_cmpltd_date'),
                        'patient_id' => $_GET['cid'],
                        'staff_id' => $user->data()->id,
                        'status' => 1,
                        'created_on' => date('Y-m-d'),
                        'site_id' => $user->data()->site_id,
                    ), Input::get('id'));


                    $user->updateRecord('chemotherapy', array(
                        'vid' => $_GET["vid"],
                        'vcode' => $_GET["vcode"],
                        'other_herbal' => Input::get('other_herbal'),
                        'chemotherapy1' => Input::get('chemotherapy1'),
                        'chemotherapy_start1' => Input::get('chemotherapy_start1'),
                        'chemotherapy_ongoing1' => Input::get('chemotherapy_ongoing1'),
                        'chemotherapy_end1' => Input::get('chemotherapy_end1'),
                        'chemotherapy_dose1' => Input::get('chemotherapy_dose1'),
                        'chemotherapy_frequecy1' => Input::get('chemotherapy_frequecy1'),
                        'chemotherapy_remarks1' => Input::get('chemotherapy_remarks1'),
                        'chemotherapy2' => Input::get('chemotherapy2'),
                        'chemotherapy_start2' => Input::get('chemotherapy_start2'),
                        'chemotherapy_ongoing2' => Input::get('chemotherapy_ongoing2'),
                        'chemotherapy_end2' => Input::get('chemotherapy_end2'),
                        'chemotherapy_dose2' => Input::get('chemotherapy_dose2'),
                        'chemotherapy_frequecy2' => Input::get('chemotherapy_frequecy2'),
                        'chemotherapy_remarks2' => Input::get('chemotherapy_remarks2'),
                        'chemotherapy3' => Input::get('chemotherapy3'),
                        'chemotherapy_start3' => Input::get('chemotherapy_start3'),
                        'chemotherapy_ongoing3' => Input::get('chemotherapy_ongoing3'),
                        'chemotherapy_end3' => Input::get('chemotherapy_end3'),
                        'chemotherapy_dose3' => Input::get('chemotherapy_dose3'),
                        'chemotherapy_frequecy3' => Input::get('chemotherapy_frequecy3'),
                        'chemotherapy_remarks3' => Input::get('chemotherapy_remarks3'),
                        'chemotherapy4' => Input::get('chemotherapy4'),
                        'chemotherapy_start4' => Input::get('chemotherapy_start4'),
                        'chemotherapy_ongoing4' => Input::get('chemotherapy_ongoing4'),
                        'chemotherapy_end4' => Input::get('chemotherapy_end4'),
                        'chemotherapy_dose4' => Input::get('chemotherapy_dose4'),
                        'chemotherapy_frequecy4' => Input::get('chemotherapy_frequecy4'),
                        'chemotherapy_remarks4' => Input::get('chemotherapy_remarks4'),
                        'chemotherapy5' => Input::get('chemotherapy5'),
                        'chemotherapy_start5' => Input::get('chemotherapy_start5'),
                        'chemotherapy_ongoing5' => Input::get('chemotherapy_ongoing5'),
                        'chemotherapy_end5' => Input::get('chemotherapy_end5'),
                        'chemotherapy_dose5' => Input::get('chemotherapy_dose5'),
                        'chemotherapy_frequecy5' => Input::get('chemotherapy_frequecy5'),
                        'chemotherapy_remarks5' => Input::get('chemotherapy_remarks5'),
                        'crf1_cmpltd_date' => Input::get('crf1_cmpltd_date'),
                        'patient_id' => $_GET['cid'],
                        'staff_id' => $user->data()->id,
                        'status' => 1,
                        'created_on' => date('Y-m-d'),
                        'site_id' => $user->data()->site_id,
                    ), Input::get('id'));

                    $user->updateRecord('surgery', array(
                        'vid' => $_GET["vid"],
                        'vcode' => $_GET["vcode"],
                        'other_herbal' => Input::get('other_herbal'),
                        'surgery1' => Input::get('surgery1'),
                        'surgery_start1' => Input::get('surgery_start1'),
                        'surgery_number1' => Input::get('surgery_number1'),
                        'surgery_remarks1' => Input::get('surgery_remarks1'),
                        'surgery2' => Input::get('surgery2'),
                        'surgery_start2' => Input::get('surgery_start2'),
                        'surgery_number2' => Input::get('surgery_number2'),
                        'surgery_remarks2' => Input::get('surgery_remarks2'),
                        'surgery3' => Input::get('surgery3'),
                        'surgery_start3' => Input::get('surgery_start3'),
                        'surgery_number3' => Input::get('surgery_number3'),
                        'surgery_remarks3' => Input::get('surgery_remarks3'),
                        'crf1_cmpltd_date' => Input::get('crf1_cmpltd_date'),
                        'patient_id' => $_GET['cid'],
                        'staff_id' => $user->data()->id,
                        'status' => 1,
                        'created_on' => date('Y-m-d'),
                        'site_id' => $user->data()->site_id,
                    ), Input::get('id'));

                    $successMessage = 'CRF1 Updated Successful';
                    Redirect::to('info.php?id=6&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode']);
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
        } elseif (Input::get('update_crf4')) {
            $validate = $validate->check($_POST, array(
                'sample_date' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('crf4', array(
                        'vid' => $_GET["vid"],
                        'vcode' => $_GET["vcode"],
                        'sample_date' => Input::get('sample_date'),
                        'renal_urea' => Input::get('renal_urea'),
                        'renal_creatinine' => Input::get('renal_creatinine'),
                        'renal_creatinine_grade' => Input::get('renal_creatinine_grade'),
                        'renal_egfr' => Input::get('renal_egfr'),
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
                        'bilirubin_total_grade' => Input::get('bilirubin_total_grade'),
                        'liver_bilirubin_direct' => Input::get('liver_bilirubin_direct'),
                        'bilirubin_direct_grade' => Input::get('bilirubin_direct_grade'),
                        'rbg' => Input::get('rbg'),
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
                    Redirect::to('info.php?id=6&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode']);
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
                    Redirect::to('info.php?id=6&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode']);
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
                    Redirect::to('info.php?id=6&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode']);
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
                                        <?php if ($_GET['vcode'] == 'D0') { ?>
                                            <tr>
                                                <td>1</td>
                                                <td>CRF 1: MEDICAL HISTORY, USE OF HERBAL MEDICINES AND STANDARD TREATMENT</td>
                                                <?php if ($override->get1('crf1', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode'])) { ?>
                                                    <td><a href="info.php?id=8&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>" class="btn btn-success"> Change </a> </td>
                                                <?php } else { ?>
                                                    <td><a href="add.php?id=8&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>" class="btn btn-warning">Add </a> </td>
                                                <?php } ?>
                                            </tr>
                                        <?php } ?>

                                        <tr>
                                            <td>2</td>
                                            <td>CRF 2</td>
                                            <?php if ($override->get1('crf2', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode'])) { ?>
                                                <td><a href="info.php?id=9&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>" class="btn btn-success"> Change </a> </td>
                                            <?php } else { ?>
                                                <td><a href="add.php?id=9&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>" class="btn btn-warning">Add </a> </td>
                                            <?php } ?>
                                        </tr>

                                        <tr>
                                            <td>3</td>
                                            <td>CRF 3</td>
                                            <?php if ($override->get1('crf3', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode'])) { ?>
                                                <td><a href="info.php?id=10&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>" class="btn btn-success"> Change </a> </td>
                                            <?php } else { ?>
                                                <td><a href="add.php?id=10&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>" class="btn btn-warning">Add </a> </td>
                                            <?php } ?>
                                        </tr>

                                        <tr>
                                            <td>4</td>
                                            <td>CRF 4</td>
                                            <?php if ($override->get1('crf4', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode'])) { ?>
                                                <td><a href="info.php?id=11&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>" class="btn btn-success"> Change </a> </td>
                                            <?php } else { ?>
                                                <td><a href="add.php?id=11&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>" class="btn btn-warning">Add </a> </td>
                                            <?php } ?>
                                        </tr>

                                        <tr>
                                            <td>5</td>
                                            <td>CRF 5</td>
                                            <?php if ($override->get1('crf5', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode'])) { ?>
                                                <td><a href="info.php?id=12&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>" class="btn btn-success"> Change </a> </td>
                                            <?php } else { ?>
                                                <td><a href="add.php?id=12&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>" class="btn btn-warning">Add </a> </td>
                                            <?php } ?>
                                        </tr>

                                        <tr>
                                            <td>6</td>
                                            <td>CRF 6</td>
                                            <?php if ($override->get1('crf6', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode'])) { ?>
                                                <td><a href="info.php?id=13&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>" class="btn btn-success"> Change </a> </td>
                                            <?php } else { ?>
                                                <td><a href="add.php?id=13&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>" class="btn btn-warning">Add </a> </td>
                                            <?php } ?>
                                        </tr>

                                        <tr>
                                            <td>7</td>
                                            <td>CRF 7</td>
                                            <?php if ($override->get1('crf7', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode'])) { ?>
                                                <td><a href="info.php?id=15&cid=<?= $_GET['cid'] ?>&vid=<?= $_GET['vid'] ?>&vcode=<?= $_GET['vcode'] ?>" class="btn btn-success"> Change </a> </td>
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
                        <?php $patient = $override->get1('crf1', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode'])[0] ?>
                        <?php $herbal_treatment = $override->get1('herbal_treatment', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode'])[0] ?>
                        <?php $chemotherapy = $override->get1('chemotherapy', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode'])[0] ?>
                        <?php $surgery = $override->get1('surgery', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode'])[0] ?>
                        <div class="col-md-offset-1 col-md-8">
                            <div class="head clearfix">
                                <div class="isw-ok"></div>
                                <h1>CRF 1: MEDICAL HISTORY, USE OF HERBAL MEDICINES AND STANDARD TREATMENT</h1>
                            </div>
                            <div class="block-fluid">
                                <form id="validation" method="post">

                                    <div class="head clearfix">
                                        <div class="isw-ok"></div>
                                        <h1>Medical History</h1>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Date of diagnosis:</div>
                                        <div class="col-md-9">
                                            <input value="<?= $patient['diagnosis_date'] ?>" class="validate[required]" type="text" name="diagnosis_date" id="diagnosis_date" required />
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
                                            <select name="diabetic" id="diabetic" style="width: 100%;" required>
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
                                            <select name="diabetic_medicatn" id="diabetic_medicatn" style="width: 100%;">
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
                                        <div class="col-md-9"><textarea value="<?= $patient['diabetic_medicatn_name'] ?>" name="diabetic_medicatn_name" rows="4"></textarea> </div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">2. Hypertension:</div>
                                        <div class="col-md-9">
                                            <select name="hypertension" id="hypertension" style="width: 100%;" required>
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
                                            <select name="hypertension_medicatn" id="hypertension_medicatn" style="width: 100%;">
                                                <?php if ($patient['hypertension_medicatn1'] == "1") { ?>
                                                    <option value="<?= $patient['hypertension_medicatn1'] ?>">Yes</option>
                                                <?php } elseif ($patient['hypertension_medicatn1'] == "2") { ?>
                                                    <option value="<?= $patient['hypertension_medicatn1'] ?>">No</option>
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
                                        <div class="col-md-9"><textarea value="<?= $patient['hypertension_medicatn_name'] ?>" name="hypertension_medicatn_name" rows="4"></textarea> </div>
                                    </div>


                                    <div class="row-form clearfix">
                                        <div class="col-md-3">3. Any other heart problem apart from hypertension?:</div>
                                        <div class="col-md-9">
                                            <select name="heart" id="heart" style="width: 100%;" required>
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
                                        <div class="col-md-9"><textarea value="<?= $patient['heart_medicatn_name'] ?>" name="heart_medicatn_name" rows="4"></textarea> </div>
                                    </div>


                                    <div class="row-form clearfix">
                                        <div class="col-md-3">4. Asthma:</div>
                                        <div class="col-md-9">
                                            <select name="asthma" id="asthma" style="width: 100%;" required>
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
                                            <select name="asthma_medicatn" id="asthma_medicatn" style="width: 100%;">
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
                                        <div class="col-md-9"><textarea value="<?= $patient['asthma_medicatn_name'] ?>" name="asthma_medicatn_name" rows="4"></textarea> </div>
                                    </div>


                                    <div class="row-form clearfix">
                                        <div class="col-md-3">5. HIV/AIDS:</div>
                                        <div class="col-md-9">
                                            <select name="hiv_aids" id="hiv_aids" style="width: 100%;" required>
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
                                            <select name="hiv_aids_medicatn" id="hiv_aids_medicatn" style="width: 100%;">
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
                                        <div class="col-md-9"><textarea value="<?= $patient['hiv_aids_medicatn_name'] ?>" name="hiv_aids_medicatn_name" rows="4"></textarea> </div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">6. Any other medical condition:</div>
                                        <div class="col-md-9">
                                            <select name="other_medical" id="other_medical" style="width: 100%;" required>
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

                                    <div class="row-form clearfix" id="other_specify">
                                        <div class="col-md-3">6. Specify the medical conditions?:</div>
                                        <div class="col-md-9"><textarea value="<?= $patient['other_specify'] ?>" name="other_specify" rows="4"></textarea> </div>
                                    </div>

                                    <div class="row-form clearfix" id="other_medical_medicatn1">
                                        <div class="col-md-3">6. Is the patient on Medication?</div>
                                        <div class="col-md-9">
                                            <select name="other_medical_medicatn" id="other_medical_medicatn" style="width: 100%;">
                                                <?php if ($patient['other_medical_medicatn'] == "1") { ?>
                                                    <option value="<?= $patient['other_medical_medicatn'] ?>">Yes</option>
                                                <?php } elseif ($patient['other_medical_medicatn'] == "2") { ?>
                                                    <option value="<?= $patient['other_medical_medicatn'] ?>">No</option>
                                                <?php } else { ?>
                                                    <option value="">Select</option>
                                                <?php } ?>
                                                <option value="1">Yes</option>
                                                <option value="2">No</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row-form clearfix" id="other_medicatn_name">
                                        <div class="col-md-3">6. Mention the medications:</div>
                                        <div class="col-md-9"><textarea value="<?= $patient['other_medicatn_name'] ?>" name="other_medicatn_name" rows="4"></textarea> </div>
                                    </div>


                                    <div class="head clearfix">
                                        <div class="isw-ok"></div>
                                        <h1>USE OF HERBAL MEDICINES</h1>
                                    </div>


                                    <div class="row-form clearfix">
                                        <div class="col-md-3">8. Are you using NIMREGENIN herbal preparation?:</div>
                                        <div class="col-md-9">
                                            <select name="nimregenin_herbal" id="nimregenin_herbal" style="width: 100%;" required>
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

                                    <div class="row" id="nimregenin_preparation">
                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>NIMREGENIN</label>
                                                    <input value="NIMREGENIN" type="text" name="nimregenin_preparation" readonly />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Start Date:</label>
                                                    <input value="<?= $patient['nimregenin_start'] ?>" class="validate[required]" type="text" name="nimregenin_start" id="nimregenin_start" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Ongoing?:</label>
                                                    <select name="nimregenin_ongoing" id="nimregenin_ongoing" style="width: 100%;">
                                                        <?php if ($patient['nimregenin_ongoing'] == "1") { ?>
                                                            <option value="<?= $patient['nimregenin_ongoing'] ?>">Yes</option>
                                                        <?php } elseif ($patient['nimregenin_ongoing'] == "2") { ?>
                                                            <option value="<?= $patient['nimregenin_ongoing'] ?>">No</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-2" id="nimregenin_end">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>End Date:</label>
                                                    <input value="<?= $patient['nimregenin_end'] ?>" class="validate[required]" type="text" name="nimregenin_end" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Dose:</label>
                                                    <input value="<?= $patient['nimregenin_dose'] ?>" class="validate[required]" type="text" name="nimregenin_dose" id="nimregenin_dose" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Frequecy:</label>
                                                    <input value="<?= $patient['nimregenin_frequecy'] ?>" class="validate[required]" type="text" name="nimregenin_frequecy" id="nimregenin_frequecy" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="head clearfix">
                                        <div class="isw-ok"></div>
                                        <h1>Other Herbal preparation</h1>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">8. Are you using any other herbal preparation?:</div>
                                        <div class="col-md-9">
                                            <select name="other_herbal" id="other_herbal" style="width: 100%;" required>
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

                                    <div class="row" id="herbal_preparation1">
                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>1. Herbal preparation</label>
                                                    <input value="<?= $herbal_treatment['herbal_preparation1'] ?>" type="text" name="herbal_preparation1" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>1. Start Date:</label>
                                                    <input value="<?= $herbal_treatment['herbal_start1'] ?>" class="validate[required,custom[date]]" type="text" name="herbal_start1" />
                                                    <span>Example: 2010-12-01</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-1">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>1. Ongoing?:</label>
                                                    <select name="herbal_ongoing1" id="herbal_ongoing1" style="width: 100%;">
                                                        <?php if ($herbal_treatment['herbal_ongoing1'] == "1") { ?>
                                                            <option value="<?= $herbal_treatment['herbal_ongoing1'] ?>">Yes</option>
                                                        <?php } elseif ($herbal_treatment['herbal_ongoing1'] == "2") { ?>
                                                            <option value="<?= $herbal_treatment['herbal_ongoing1'] ?>">No</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-2" id="herbal_end1">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>1. End Date:</label>
                                                    <input value="<?= $herbal_treatment['herbal_end1'] ?>" class="validate[required,custom[date]]" type="text" name="herbal_end1" />
                                                    <span>Example: 2010-12-01</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>1. Dose:</label>
                                                    <input value="<?= $herbal_treatment['herbal_dose1'] ?>" type="text" name="herbal_dose1" id="herbal_dose1" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>1. Frequecy:</label>
                                                    <input value="<?= $herbal_treatment['herbal_frequency1'] ?>" type="text" name="herbal_frequency1" id="herbal_frequency1" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="row" id="herbal_preparation2">
                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>2. Herbal preparation</label>
                                                    <input value="<?= $herbal_treatment['herbal_preparation2'] ?>" type="text" name="herbal_preparation2" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>2. Start Date:</label>
                                                    <input value="<?= $herbal_treatment['herbal_start2'] ?>" class="validate[required,custom[date]]" type="text" name="herbal_start2" />
                                                    <span>Example: 2010-12-01</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-1">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>2. Ongoing?:</label>
                                                    <select name="herbal_ongoing2" id="herbal_ongoing2" style="width: 100%;">
                                                        <?php if ($herbal_treatment['herbal_ongoing2'] == "1") { ?>
                                                            <option value="<?= $herbal_treatment['herbal_ongoing2'] ?>">Yes</option>
                                                        <?php } elseif ($herbal_treatment['herbal_ongoing2'] == "2") { ?>
                                                            <option value="<?= $herbal_treatment['herbal_ongoing2'] ?>">No</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-2" id="herbal_end2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>2. End Date:</label>
                                                    <input value="<?= $herbal_treatment['herbal_end2'] ?>" class="validate[required,custom[date]]" type="text" name="herbal_end2" />
                                                    <span>Example: 2010-12-01</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>2. Dose:</label>
                                                    <input value="<?= $herbal_treatment['herbal_dose2'] ?>" type="text" name="herbal_dose2" id="herbal_dose2" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>2. Frequecy:</label>
                                                    <input value="<?= $herbal_treatment['herbal_frequency2'] ?>" type="text" name="herbal_frequency2" id="herbal_frequency2" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="row" id="herbal_preparation3">
                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>3. Herbal preparation</label>
                                                    <input value="<?= $herbal_treatment['herbal_preparation3'] ?>" type="text" name="herbal_preparation3" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>3. Start Date:</label>
                                                    <input value="<?= $herbal_treatment['herbal_start3'] ?>" class="validate[required,custom[date]]" type="text" name="herbal_start3" />
                                                    <span>Example: 2010-12-01</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-1">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>3. Ongoing?:</label>
                                                    <select name="herbal_ongoing3" id="herbal_ongoing3" style="width: 100%;">
                                                        <?php if ($herbal_treatment['herbal_ongoing3'] == "1") { ?>
                                                            <option value="<?= $herbal_treatment['herbal_ongoing3'] ?>">Yes</option>
                                                        <?php } elseif ($herbal_treatment['herbal_ongoing3'] == "2") { ?>
                                                            <option value="<?= $herbal_treatment['herbal_ongoing3'] ?>">No</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-2" id="herbal_end3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>3. End Date:</label>
                                                    <input value="<?= $herbal_treatment['herbal_end3'] ?>" class="validate[required,custom[date]]" type="text" name="herbal_end3" />
                                                    <span>Example: 2010-12-01</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>3. Dose:</label>
                                                    <input value="<?= $herbal_treatment['herbal_dose3'] ?>" type="text" name="herbal_dose3" id="herbal_dose3" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>3. Frequecy:</label>
                                                    <input value="<?= $herbal_treatment['herbal_frequency3'] ?>" type="text" name="herbal_frequency3" id="herbal_frequency3" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="row" id="herbal_preparation4">
                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>4. Herbal preparation</label>
                                                    <input value="<?= $herbal_treatment['herbal_preparation4'] ?>" type="text" name="herbal_preparation4" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>4. Start Date:</label>
                                                    <input value="<?= $herbal_treatment['herbal_start4'] ?>" class="validate[required,custom[date]]" type="text" name="herbal_start4" />
                                                    <span>Example: 2010-12-01</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-1">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>4. Ongoing?:</label>
                                                    <select name="herbal_ongoing4" id="herbal_ongoing4" style="width: 100%;">
                                                        <?php if ($herbal_treatment['herbal_ongoing4'] == "1") { ?>
                                                            <option value="<?= $herbal_treatment['herbal_ongoing4'] ?>">Yes</option>
                                                        <?php } elseif ($herbal_treatment['herbal_ongoing4'] == "2") { ?>
                                                            <option value="<?= $herbal_treatment['herbal_ongoing4'] ?>">No</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-2" id="herbal_end4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>4. End Date:</label>
                                                    <input value="<?= $herbal_treatment['herbal_end4'] ?>" class="validate[required,custom[date]]" type="text" name="herbal_end4" />
                                                    <span>Example: 2010-12-01</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>4. Dose:</label>
                                                    <input value="<?= $herbal_treatment['herbal_dose4'] ?>" type="text" name="herbal_dose4" id="herbal_dose4" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>4. Frequecy:</label>
                                                    <input value="<?= $herbal_treatment['herbal_frequency4'] ?>" type="text" name="herbal_frequency4" id="herbal_frequency4" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" id="herbal_preparation5">
                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>5. Herbal preparation</label>
                                                    <input value="<?= $herbal_treatment['herbal_preparation5'] ?>" type="text" name="herbal_preparation5" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>5. Start Date:</label>
                                                    <input value="<?= $herbal_treatment['herbal_start5'] ?>" class="validate[required,custom[date]]" type="text" name="herbal_start5" />
                                                    <span>Example: 2010-12-01</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-1">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>5. Ongoing?:</label>
                                                    <select name="herbal_ongoing5" id="herbal_ongoing5" style="width: 100%;">
                                                        <?php if ($herbal_treatment['herbal_ongoing5'] == "1") { ?>
                                                            <option value="<?= $herbal_treatment['herbal_ongoing5'] ?>">Yes</option>
                                                        <?php } elseif ($herbal_treatment['herbal_ongoing5'] == "2") { ?>
                                                            <option value="<?= $herbal_treatment['herbal_ongoing5'] ?>">No</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-2" id="herbal_end5">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>5. End Date:</label>
                                                    <input value="<?= $herbal_treatment['herbal_end5'] ?>" class="validate[required,custom[date]]" type="text" name="herbal_end5" />
                                                    <span>Example: 2010-12-01</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>5. Dose:</label>
                                                    <input value="<?= $herbal_treatment['herbal_dose5'] ?>" type="text" name="herbal_dose5" id="herbal_dose5" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>5. Frequecy:</label>
                                                    <input value="<?= $herbal_treatment['herbal_frequency5'] ?>" type="text" name="herbal_frequency5" id="herbal_frequency5" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="head clearfix">
                                        <div class="isw-ok"></div>
                                        <h2>STANDARD OF CARE TREATMENT
                                        </h2>
                                        <h2>Provide lists of treatments and supportive care given to the cancer patient</h2>
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


                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>1. Radiotherapy :</label>
                                                    <input value="Radiotherapy" type="text" name="radiotherapy" id="radiotherapy" readonly />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Start:</label>
                                                    <input value="<?= $patient['radiotherapy_start'] ?>" class="validate[required]" type="text" name="radiotherapy_start" id="radiotherapy_start" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Ongoing?:</label>
                                                    <select name="radiotherapy_ongoing" id="radiotherapy_ongoing" style="width: 100%;">
                                                        <?php if ($patient['radiotherapy_ongoing'] == "1") { ?>
                                                            <option value="<?= $patient['radiotherapy_ongoing'] ?>">Yes</option>
                                                        <?php } elseif ($patient['radiotherapy_ongoing'] == "2") { ?>
                                                            <option value="<?= $patient['radiotherapy_ongoing'] ?>">No</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-2" id="radiotherapy_end">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>End:</label>
                                                    <input value="<?= $patient['radiotherapy_end'] ?>" class="validate[required]" type="text" name="radiotherapy_end" />
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Dose:</label>
                                                    <input value="<?= $patient['radiotherapy_dose'] ?>" class="validate[required]" type="text" name="radiotherapy_dose" id="radiotherapy_dose" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Frequecy:</label>
                                                    <input value="<?= $patient['radiotherapy_frequecy'] ?>" class="validate[required]" type="text" name="radiotherapy_frequecy" id="radiotherapy_frequecy" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="head clearfix">
                                        <div class="isw-ok"></div>
                                        <h1>2. Chemotherapy :</h1>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>1. Type of Chemotherapy</label>
                                                    <input value="<?= $chemotherapy['chemotherapy1'] ?>" type="text" name="chemotherapy1" id="chemotherapy1" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>1. Start Date:</label>
                                                    <input value="<?= $chemotherapy['chemotherapy_start1'] ?>" class="validate[required,custom[date]]" type="text" name="chemotherapy_start1" id="chemotherapy_start1" />
                                                    <span>Example: 2010-12-01</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>1. Ongoing?</label>
                                                    <select name="chemotherapy_ongoing1" id="chemotherapy_ongoing1" style="width: 100%;">
                                                        <?php if ($chemotherapy['chemotherapy_ongoing1'] == "1") { ?>
                                                            <option value="<?= $chemotherapy['chemotherapy_ongoing1'] ?>">Yes</option>
                                                        <?php } elseif ($chemotherapy['chemotherapy_ongoing1'] == "2") { ?>
                                                            <option value="<?= $chemotherapy['chemotherapy_ongoing1'] ?>">No</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-2" id="chemotherapy_end1">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>1. End Date:</label>
                                                    <input value="<?= $chemotherapy['chemotherapy_end1'] ?>" class="validate[required,custom[date]]" type="text" name="chemotherapy_end1" />
                                                    <span>Example: 2010-12-01</span>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>1. Dose:</label>
                                                    <input value="<?= $chemotherapy['chemotherapy_dose1'] ?>" class="validate[required]" type="text" name="chemotherapy_dose1" id="chemotherapy_dose1" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>1. Frequecy:</label>
                                                    <input value="<?= $chemotherapy['chemotherapy_frequecy1'] ?>" class="validate[required]" type="text" name="chemotherapy_frequecy1" id="chemotherapy_frequecy1" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>1. Remarks:</label>
                                                    <input value="<?= $chemotherapy['chemotherapy_remarks1'] ?>" type="text" name="chemotherapy_remarks1" id="chemotherapy_remarks1" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>2. Type of Chemotherapy</label>
                                                    <input value="<?= $chemotherapy['chemotherapy2'] ?>" type="text" name="chemotherapy2" id="chemotherapy2" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>2. Start Date:</label>
                                                    <input value="<?= $chemotherapy['chemotherapy_start2'] ?>" class="validate[required,custom[date]]" type="text" name="chemotherapy_start2" id="chemotherapy_start2" />
                                                    <span>Example: 2010-12-01</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>2. Ongoing?</label>
                                                    <select name="chemotherapy_ongoing2" id="chemotherapy_ongoing2" style="width: 100%;">
                                                        <?php if ($chemotherapy['chemotherapy_ongoing2'] == "1") { ?>
                                                            <option value="<?= $chemotherapy['chemotherapy_ongoing2'] ?>">Yes</option>
                                                        <?php } elseif ($chemotherapy['chemotherapy_ongoing2'] == "2") { ?>
                                                            <option value="<?= $chemotherapy['chemotherapy_ongoing2'] ?>">No</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-2" id="chemotherapy_end2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>2. End Date:</label>
                                                    <input value="<?= $chemotherapy['chemotherapy_end2'] ?>" class="validate[required,custom[date]]" type="text" name="chemotherapy_end2" />
                                                    <span>Example: 2010-12-01</span>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>2. Dose:</label>
                                                    <input value="<?= $chemotherapy['chemotherapy_dose2'] ?>" class="validate[required]" type="text" name="chemotherapy_dose2" id="chemotherapy_dose2" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>2. Frequecy:</label>
                                                    <input value="<?= $chemotherapy['chemotherapy_frequecy2'] ?>" class="validate[required]" type="text" name="chemotherapy_frequecy2" id="chemotherapy_frequecy2" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>2. Remarks:</label>
                                                    <input value="<?= $chemotherapy['chemotherapy_remarks2'] ?>" type="text" name="chemotherapy_remarks2" id="chemotherapy_remarks2" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>3. Type of Chemotherapy</label>
                                                    <input value="<?= $chemotherapy['chemotherapy3'] ?>" type="text" name="chemotherapy3" id="chemotherapy3" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>3. Start Date:</label>
                                                    <input value="<?= $chemotherapy['chemotherapy_start3'] ?>" class="validate[required,custom[date]]" type="text" name="chemotherapy_start3" id="chemotherapy_start3" />
                                                    <span>Example: 2010-12-01</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>3. Ongoing?</label>
                                                    <select name="chemotherapy_ongoing3" id="chemotherapy_ongoing3" style="width: 100%;">
                                                        <?php if ($chemotherapy['chemotherapy_ongoing3'] == "1") { ?>
                                                            <option value="<?= $chemotherapy['chemotherapy_ongoing3'] ?>">Yes</option>
                                                        <?php } elseif ($chemotherapy['chemotherapy_ongoing3'] == "2") { ?>
                                                            <option value="<?= $chemotherapy['chemotherapy_ongoing3'] ?>">No</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-2" id="chemotherapy_end3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>3. End Date:</label>
                                                    <input value="<?= $chemotherapy['chemotherapy_end3'] ?>" class="validate[required,custom[date]]" type="text" name="chemotherapy_end3" />
                                                    <span>Example: 2010-12-01</span>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>3. Dose:</label>
                                                    <input value="<?= $chemotherapy['chemotherapy_dose3'] ?>" class="validate[required]" type="text" name="chemotherapy_dose3" id="chemotherapy_dose3" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>3. Frequecy:</label>
                                                    <input value="<?= $chemotherapy['chemotherapy_frequecy3'] ?>" class="validate[required]" type="text" name="chemotherapy_frequecy3" id="chemotherapy_frequecy3" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>3. Remarks:</label>
                                                    <input value="<?= $chemotherapy['chemotherapy_remarks3'] ?>" type="text" name="chemotherapy_remarks3" id="chemotherapy_remarks3" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>4. Type of Chemotherapy</label>
                                                    <input value="<?= $chemotherapy['chemotherapy4'] ?>" type="text" name="chemotherapy4" id="chemotherapy4" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>4. Start Date:</label>
                                                    <input value="<?= $chemotherapy['chemotherapy_start4'] ?>" class="validate[required,custom[date]]" type="text" name="chemotherapy_start4" id="chemotherapy_start4" />
                                                    <span>Example: 2010-12-01</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>4. Ongoing?</label>
                                                    <select name="chemotherapy_ongoing4" id="chemotherapy_ongoing4" style="width: 100%;">
                                                        <?php if ($chemotherapy['chemotherapy_ongoing4'] == "1") { ?>
                                                            <option value="<?= $chemotherapy['chemotherapy_ongoing4'] ?>">Yes</option>
                                                        <?php } elseif ($chemotherapy['chemotherapy_ongoing4'] == "2") { ?>
                                                            <option value="<?= $chemotherapy['chemotherapy_ongoing4'] ?>">No</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?> <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-2" id="chemotherapy_end4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>4. End Date:</label>
                                                    <input value="<?= $chemotherapy['chemotherapy_end4'] ?>" class="validate[required,custom[date]]" type="text" name="chemotherapy_end4" />
                                                    <span>Example: 2010-12-01</span>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>4. Dose:</label>
                                                    <input value="<?= $chemotherapy['chemotherapy_dose4'] ?>" class="validate[required]" type="text" name="chemotherapy_dose4" id="chemotherapy_dose4" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>4. Frequecy:</label>
                                                    <input value="<?= $chemotherapy['chemotherapy_frequecy4'] ?>" class="validate[required]" type="text" name="chemotherapy_frequecy4" id="chemotherapy_frequecy4" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>4. Remarks:</label>
                                                    <input value="<?= $chemotherapy['chemotherapy_remarks4'] ?>" type="text" name="chemotherapy_remarks4" id="chemotherapy_remarks4" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>5. Type of Chemotherapy</label>
                                                    <input value="<?= $chemotherapy['chemotherapy5'] ?>" type="text" name="chemotherapy5" id="chemotherapy5" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>5. Start Date:</label>
                                                    <input value="<?= $chemotherapy['chemotherapy_start5'] ?>" class="validate[required,custom[date]]" type="text" name="chemotherapy_start5" id="chemotherapy_start5" />
                                                    <span>Example: 2010-12-01</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>5. Ongoing?</label>
                                                    <select name="chemotherapy_ongoing5" id="chemotherapy_ongoing5" style="width: 100%;">
                                                        <?php if ($chemotherapy['chemotherapy_ongoing5'] == "1") { ?>
                                                            <option value="<?= $chemotherapy['chemotherapy_ongoing5'] ?>">Yes</option>
                                                        <?php } elseif ($chemotherapy['chemotherapy_ongoing5'] == "2") { ?>
                                                            <option value="<?= $chemotherapy['chemotherapy_ongoing5'] ?>">No</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?> <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-2" id="chemotherapy_end5">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>5. End Date:</label>
                                                    <input value="<?= $chemotherapy['chemotherapy_end5'] ?>" class="validate[required,custom[date]]" type="text" name="chemotherapy_end5" />
                                                    <span>Example: 2010-12-01</span>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>5. Dose:</label>
                                                    <input value="<?= $chemotherapy['chemotherapy_dose5'] ?>" class="validate[required]" type="text" name="chemotherapy_dose5" id="chemotherapy_dose5" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>5. Frequecy:</label>
                                                    <input value="<?= $chemotherapy['chemotherapy_frequecy5'] ?>" class="validate[required]" type="text" name="chemotherapy_frequecy5" id="chemotherapy_frequecy5" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>5. Remarks:</label>
                                                    <input value="<?= $chemotherapy['chemotherapy_remarks5'] ?>" type="text" name="chemotherapy_remarks5" id="chemotherapy_remarks5" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="head clearfix">
                                        <div class="isw-ok"></div>
                                        <h1>3. Surgery :</h1>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>1. Type of surgery</label>
                                                    <input value="<?= $surgery['surgery1'] ?>" type="text" name="surgery1" id="surgery1" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>1. Start Date:</label>
                                                    <input value="<?= $surgery['surgery_start1'] ?>" class="validate[required,custom[date]]" type="text" name="surgery_start1" id="surgery_start1" />
                                                    <span>Example: 2010-12-01</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>1. Number:</label>
                                                    <input value="<?= $surgery['surgery_number1'] ?>" class="validate[required]" type="text" name="surgery_number1" id="surgery_number1" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>1. Remarks:</label>
                                                    <input value="<?= $surgery['surgery_remarks1'] ?>" type="text" name="surgery_remarks1" id="surgery_remarks1" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>2. Type of surgery</label>
                                                    <input value="<?= $surgery['surgery2'] ?>" type="text" name="surgery2" id="surgery2" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>2. Start Date:</label>
                                                    <input value="<?= $surgery['surgery_start2'] ?>" class="validate[required,custom[date]]" type="text" name="surgery_start2" id="surgery_start2" />
                                                    <span>Example: 2010-12-01</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>2. Number:</label>
                                                    <input value="<?= $surgery['surgery_number2'] ?>" class="validate[required]" type="text" name="surgery_number2" id="surgery_number2" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>2. Remarks:</label>
                                                    <input value="<?= $surgery['surgery_remarks2'] ?>" type="text" name="surgery_remarks2" id="surgery_remarks2" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>3. Type of surgery</label>
                                                    <input value="<?= $surgery['surgery3'] ?>" type="text" name="surgery3" id="surgery3" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>3. Start Date:</label>
                                                    <input value="<?= $surgery['surgery_start3'] ?>" class="validate[required,custom[date]]" type="text" name="surgery_start3" id="surgery_start3" />
                                                    <span>Example: 2010-12-01</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>3. Number:</label>
                                                    <input value="<?= $surgery['surgery_number3'] ?>" class="validate[required]" type="text" name="surgery_number3" id="surgery_number3" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>3. Remarks:</label>
                                                    <input value="<?= $surgery['surgery_remarks3'] ?>" type="text" name="surgery_remarks3" id="surgery_remarks3" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>



                                    <div class="col-sm-12">
                                        <div class="row-form clearfix">
                                            <!-- select -->
                                            <div class="form-group">
                                                <label>Date of Completion:</label>
                                                <input value="<?= $patient['crf1_cmpltd_date'] ?>" class="validate[required]" type="text" name="crf1_cmpltd_date" id="crf1_cmpltd_date" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="footer tar">
                                        <input type="hidden" name="id" value="<?= $patient['id'] ?>">
                                        <input type="submit" name="update_crf1" value="Submit" class="btn btn-default">
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php } elseif ($_GET['id'] == 9) { ?>
                        <?php $patient = $override->get1('crf2', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode'])[0] ?>
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
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
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
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
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
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
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
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
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
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
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
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
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
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
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
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
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
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
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
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
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
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
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
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
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
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
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
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
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
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
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
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
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
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
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
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
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
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
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
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
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
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
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
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
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
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
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
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
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
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
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
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
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
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
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
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
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
                        <?php $patient = $override->get1('crf3', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode'])[0] ?>
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
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
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
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
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
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
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
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
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
                                                    <label>I. Difficulty in breathing:</label>
                                                    <select name="difficult_breathing" id="difficult_breathing" style="width: 100%;" required>
                                                        <?php if ($patient['difficult_breathing'] == "1") { ?>
                                                            <option value="<?= $patient['difficult_breathing'] ?>">Yes</option>
                                                        <?php } elseif ($patient['difficult_breathing'] == "2") { ?>
                                                            <option value="<?= $patient['difficult_breathing'] ?>">No</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
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
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
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
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
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
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
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
                                                    <label>N. Loss of consciousness:</label>
                                                    <select name="loss_consciousness" id="loss_consciousness" style="width: 100%;" required>
                                                        <?php if ($patient['loss_consciousness'] == "1") { ?>
                                                            <option value="<?= $patient['loss_consciousness'] ?>">Yes</option>
                                                        <?php } elseif ($patient['loss_consciousness'] == "2") { ?>
                                                            <option value="<?= $patient['loss_consciousness'] ?>">No</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
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
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
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
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
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
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
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
                                                    <label>N. Swelling:</label>
                                                    <select name="swelling" style="width: 100%;">
                                                        <?php if ($patient['swelling'] == "1") { ?>
                                                            <option value="<?= $patient['swelling'] ?>">Yes</option>
                                                        <?php } elseif ($patient['swelling'] == "2") { ?>
                                                            <option value="<?= $patient['swelling'] ?>">No</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
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
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
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
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
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
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
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
                                                    <label>N. Convulsions:</label>
                                                    <select name="convulsions" id="convulsions" style="width: 100%;" required>
                                                        <?php if ($patient['convulsions'] == "1") { ?>
                                                            <option value="<?= $patient['convulsions'] ?>">Yes</option>
                                                        <?php } elseif ($patient['convulsions'] == "2") { ?>
                                                            <option value="<?= $patient['convulsions'] ?>">No</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
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
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
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
                                                    <label>S. Other symptoms:</label>
                                                    <select name="symptoms_other" id="symptoms_other" style="width: 100%;" required>
                                                        <?php if ($patient['symptoms_other'] == "1") { ?>
                                                            <option value="<?= $patient['symptoms_other'] ?>">Yes</option>
                                                        <?php } elseif ($patient['symptoms_other'] == "2") { ?>
                                                            <option value="<?= $patient['symptoms_other'] ?>">No</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
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
                        <?php $patient = $override->get1('crf4', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode'])[0] ?>
                        <div class="col-md-offset-1 col-md-8">
                            <div class="head clearfix">
                                <div class="isw-ok"></div>
                                <h1>CRF 4</h1>
                            </div>
                            <div class="block-fluid">
                                <form id="validation" method="post">
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Date of Sample Collection:</div>
                                        <div class="col-md-9"><input value="<?= $patient['sample_date'] ?>" class="validate[required,custom[date]]" type="text" name="sample_date" id="sample_date" required /> <span>Example: 2023-01-01</span></div>
                                    </div>

                                    <div class="head clearfix">
                                        <div class="isw-ok"></div>
                                        <h1>Blood tests:</h1>
                                    </div>

                                    <div class="head clearfix">
                                        <div class="isw-ok"></div>
                                        <h1>1. Renal function test</h1>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Serum urea levels</label>
                                                    <input value="<?= $patient['renal_urea'] ?>" type="text" name="renal_urea" id="renal_urea" />
                                                    <SPan>XX.X ( mg/dl )</SPan>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Serum creatinine levels</label>
                                                    <input value="<?= $patient['renal_creatinine'] ?>" type="text" name="renal_creatinine" id="renal_creatinine" />
                                                    <SPan>X.X ( mg/dl )</SPan>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>eGFR mL/min per 1.73 m2</label>
                                                    <input value="<?= $patient['renal_egfr'] ?>" type="text" name="renal_egfr" id="renal_egfr" />
                                                    <SPan>XXX.X ( ml/min )</SPan>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="head clearfix">
                                        <div class="isw-ok"></div>
                                        <h1>2. Liver function test</h1>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>AST levels</label>
                                                    <input value="<?= $patient['liver_ast'] ?>" type="text" name="liver_ast" id="liver_ast" />
                                                    <SPan>XXX ( units/L )</SPan>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Grade</label>
                                                    <select name="liver_ast_grade" id="liver_ast_grade" style="width: 100%;" required>
                                                        <?php if ($patient['liver_ast_grade'] == "0") { ?>
                                                            <option value="<?= $patient['liver_ast_grade'] ?>">Zero</option>
                                                        <?php } elseif ($patient['liver_ast_grade'] == "1") { ?>
                                                            <option value="<?= $patient['liver_ast_grade'] ?>">One</option>
                                                        <?php } else if ($patient['liver_ast_grade'] == "2") { ?>
                                                            <option value="<?= $patient['liver_ast_grade'] ?>">Two</option>
                                                        <?php } elseif ($patient['liver_ast_grade'] == "3") { ?>
                                                            <option value="<?= $patient['liver_ast_grade'] ?>">Three</option>
                                                        <?php } else if ($patient['liver_ast_grade'] == "4") { ?>
                                                            <option value="<?= $patient['liver_ast_grade'] ?>">Four</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="0">Zero</option>
                                                        <option value="1">One</option>
                                                        <option value="2">Two</option>
                                                        <option value="3">Three</option>
                                                        <option value="4">Four</option>
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
                                                    <label>ALT levels</label>
                                                    <input value="<?= $patient['liver_alt'] ?>" type="text" name="liver_alt" id="liver_alt" />
                                                    <SPan>XXX ( units/L )</SPan>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Grade</label>
                                                    <select name="liver_alt_grade" id="liver_alt_grade" style="width: 100%;" required>
                                                        <?php if ($patient['liver_alt_grade'] == "0") { ?>
                                                            <option value="<?= $patient['liver_alt_grade'] ?>">Zero</option>
                                                        <?php } elseif ($patient['liver_alt_grade'] == "1") { ?>
                                                            <option value="<?= $patient['liver_alt_grade'] ?>">One</option>
                                                        <?php } else if ($patient['liver_alt_grade'] == "2") { ?>
                                                            <option value="<?= $patient['liver_alt_grade'] ?>">Two</option>
                                                        <?php } elseif ($patient['liver_alt_grade'] == "3") { ?>
                                                            <option value="<?= $patient['liver_alt_grade'] ?>">Three</option>
                                                        <?php } else if ($patient['liver_alt_grade'] == "4") { ?>
                                                            <option value="<?= $patient['liver_alt_grade'] ?>">Four</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="0">Zero</option>
                                                        <option value="1">One</option>
                                                        <option value="2">Two</option>
                                                        <option value="3">Three</option>
                                                        <option value="4">Four</option>
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
                                                    <label>ALP levels</label>
                                                    <input value="<?= $patient['liver_alp'] ?>" type="text" name="liver_alp" id="liver_alp" />
                                                    <SPan>XXX ( units/L )</SPan>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Grade</label>
                                                    <select name="liver_alp_grade" id="liver_alp_grade" style="width: 100%;" required>
                                                        <?php if ($patient['liver_alp_grade'] == "0") { ?>
                                                            <option value="<?= $patient['liver_alp_grade'] ?>">Zero</option>
                                                        <?php } elseif ($patient['liver_alp_grade'] == "1") { ?>
                                                            <option value="<?= $patient['liver_alp_grade'] ?>">One</option>
                                                        <?php } else if ($patient['liver_alp_grade'] == "2") { ?>
                                                            <option value="<?= $patient['liver_alp_grade'] ?>">Two</option>
                                                        <?php } elseif ($patient['liver_alp_grade'] == "3") { ?>
                                                            <option value="<?= $patient['liver_alp_grade'] ?>">Three</option>
                                                        <?php } else if ($patient['liver_alp_grade'] == "4") { ?>
                                                            <option value="<?= $patient['liver_alp_grade'] ?>">Four</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="0">Zero</option>
                                                        <option value="1">One</option>
                                                        <option value="2">Two</option>
                                                        <option value="3">Three</option>
                                                        <option value="4">Four</option>
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
                                                    <label>PT</label>
                                                    <input value="<?= $patient['liver_pt'] ?>" type="text" name="liver_pt" id="liver_pt" />
                                                    <SPan>XXX ( units/L )</SPan>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Grade</label>
                                                    <select name="liver_pt_grade" id="liver_pt_grade" style="width: 100%;" required>
                                                        <?php if ($patient['liver_pt_grade'] == "0") { ?>
                                                            <option value="<?= $patient['liver_pt_grade'] ?>">Zero</option>
                                                        <?php } elseif ($patient['liver_pt_grade'] == "1") { ?>
                                                            <option value="<?= $patient['liver_pt_grade'] ?>">One</option>
                                                        <?php } else if ($patient['liver_pt_grade'] == "2") { ?>
                                                            <option value="<?= $patient['liver_pt_grade'] ?>">Two</option>
                                                        <?php } elseif ($patient['liver_pt_grade'] == "3") { ?>
                                                            <option value="<?= $patient['liver_pt_grade'] ?>">Three</option>
                                                        <?php } else if ($patient['liver_pt_grade'] == "4") { ?>
                                                            <option value="<?= $patient['liver_pt_grade'] ?>">Four</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="0">Zero</option>
                                                        <option value="1">One</option>
                                                        <option value="2">Two</option>
                                                        <option value="3">Three</option>
                                                        <option value="4">Four</option>
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
                                                    <label>PTT</label>
                                                    <input value="<?= $patient['liver_ptt'] ?>" type="text" name="liver_ptt" id="liver_ptt" />
                                                    <SPan>XXX ( units/L )</SPan>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Grade</label>
                                                    <select name="liver_ptt_grade" id="liver_ptt_grade" style="width: 100%;" required>
                                                        <?php if ($patient['liver_ptt_grade'] == "0") { ?>
                                                            <option value="<?= $patient['liver_ptt_grade'] ?>">Zero</option>
                                                        <?php } elseif ($patient['liver_ptt_grade'] == "1") { ?>
                                                            <option value="<?= $patient['liver_ptt_grade'] ?>">One</option>
                                                        <?php } else if ($patient['liver_ptt_grade'] == "2") { ?>
                                                            <option value="<?= $patient['liver_ptt_grade'] ?>">Two</option>
                                                        <?php } elseif ($patient['liver_ptt_grade'] == "3") { ?>
                                                            <option value="<?= $patient['liver_ptt_grade'] ?>">Three</option>
                                                        <?php } else if ($patient['liver_ptt_grade'] == "4") { ?>
                                                            <option value="<?= $patient['liver_ptt_grade'] ?>">Four</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="0">Zero</option>
                                                        <option value="1">One</option>
                                                        <option value="2">Two</option>
                                                        <option value="3">Three</option>
                                                        <option value="4">Four</option>
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
                                                    <label>INR</label>
                                                    <input value="<?= $patient['liver_inr'] ?>" type="text" name="liver_inr" id="liver_inr" />
                                                    <SPan>XXX ( units/L )</SPan>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Grade</label>
                                                    <select name="liver_inr_grade" id="liver_inr_grade" style="width: 100%;" required>
                                                        <?php if ($patient['liver_inr_grade'] == "0") { ?>
                                                            <option value="<?= $patient['liver_inr_grade'] ?>">Zero</option>
                                                        <?php } elseif ($patient['liver_inr_grade'] == "1") { ?>
                                                            <option value="<?= $patient['liver_inr_grade'] ?>">One</option>
                                                        <?php } else if ($patient['liver_inr_grade'] == "2") { ?>
                                                            <option value="<?= $patient['liver_inr_grade'] ?>">Two</option>
                                                        <?php } elseif ($patient['liver_inr_grade'] == "3") { ?>
                                                            <option value="<?= $patient['liver_inr_grade'] ?>">Three</option>
                                                        <?php } else if ($patient['liver_inr_grade'] == "4") { ?>
                                                            <option value="<?= $patient['liver_inr_grade'] ?>">Four</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="0">Zero</option>
                                                        <option value="1">One</option>
                                                        <option value="2">Two</option>
                                                        <option value="3">Three</option>
                                                        <option value="4">Four</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>GGT levels</label>
                                                    <input value="<?= $patient['liver_ggt'] ?>" type="text" name="liver_ggt" id="liver_ggt" />
                                                    <SPan>XXX ( units/L )</SPan>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Serum albumin levels</label>
                                                    <input value="<?= $patient['liver_albumin'] ?>" type="text" name="liver_albumin" id="liver_albumin" />
                                                    <SPan>XXX ( grams/L )</SPan>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Grade</label>
                                                    <select name="liver_albumin_grade" id="liver_albumin_grade" style="width: 100%;" required>
                                                        <?php if ($patient['liver_albumin_grade'] == "0") { ?>
                                                            <option value="<?= $patient['liver_albumin_grade'] ?>">Zero</option>
                                                        <?php } elseif ($patient['liver_albumin_grade'] == "1") { ?>
                                                            <option value="<?= $patient['liver_albumin_grade'] ?>">One</option>
                                                        <?php } else if ($patient['liver_albumin_grade'] == "2") { ?>
                                                            <option value="<?= $patient['liver_albumin_grade'] ?>">Two</option>
                                                        <?php } elseif ($patient['liver_albumin_grade'] == "3") { ?>
                                                            <option value="<?= $patient['liver_albumin_grade'] ?>">Three</option>
                                                        <?php } else if ($patient['liver_albumin_grade'] == "4") { ?>
                                                            <option value="<?= $patient['liver_albumin_grade'] ?>">Four</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="0">Zero</option>
                                                        <option value="1">One</option>
                                                        <option value="2">Two</option>
                                                        <option value="3">Three</option>
                                                        <option value="4">Four</option>
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
                                                    <label>Bilirubin total</label>
                                                    <input value="<?= $patient['liver_bilirubin_total'] ?>" type="text" name="liver_bilirubin_total" id="liver_bilirubin_total" />
                                                    <SPan>XXX ( grams/L )</SPan>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Grade</label>
                                                    <select name="bilirubin_total_grade" id="bilirubin_total_grade" style="width: 100%;" required>
                                                        <?php if ($patient['bilirubin_total_grade'] == "0") { ?>
                                                            <option value="<?= $patient['bilirubin_total_grade'] ?>">Zero</option>
                                                        <?php } elseif ($patient['bilirubin_total_grade'] == "1") { ?>
                                                            <option value="<?= $patient['bilirubin_total_grade'] ?>">One</option>
                                                        <?php } else if ($patient['bilirubin_total_grade'] == "2") { ?>
                                                            <option value="<?= $patient['bilirubin_total_grade'] ?>">Two</option>
                                                        <?php } elseif ($patient['bilirubin_total_grade'] == "3") { ?>
                                                            <option value="<?= $patient['bilirubin_total_grade'] ?>">Three</option>
                                                        <?php } else if ($patient['bilirubin_total_grade'] == "4") { ?>
                                                            <option value="<?= $patient['bilirubin_total_grade'] ?>">Four</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="0">Zero</option>
                                                        <option value="1">One</option>
                                                        <option value="2">Two</option>
                                                        <option value="3">Three</option>
                                                        <option value="4">Four</option>
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
                                                    <label>Bilirubin direct</label>
                                                    <input value="<?= $patient['liver_bilirubin_direct'] ?>" type="text" name="liver_bilirubin_direct" id="liver_bilirubin_direct" />
                                                    <SPan>XXX ( grams/L )</SPan>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Grade</label>
                                                    <select name="bilirubin_direct_grade" id="bilirubin_direct_grade" style="width: 100%;" required>
                                                        <?php if ($patient['bilirubin_direct_grade'] == "0") { ?>
                                                            <option value="<?= $patient['bilirubin_direct_grade'] ?>">Zero</option>
                                                        <?php } elseif ($patient['bilirubin_direct_grade'] == "1") { ?>
                                                            <option value="<?= $patient['bilirubin_direct_grade'] ?>">One</option>
                                                        <?php } else if ($patient['bilirubin_direct_grade'] == "2") { ?>
                                                            <option value="<?= $patient['bilirubin_direct_grade'] ?>">Two</option>
                                                        <?php } elseif ($patient['bilirubin_direct_grade'] == "3") { ?>
                                                            <option value="<?= $patient['bilirubin_direct_grade'] ?>">Three</option>
                                                        <?php } else if ($patient['bilirubin_direct_grade'] == "4") { ?>
                                                            <option value="<?= $patient['bilirubin_direct_grade'] ?>">Four</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="0">Zero</option>
                                                        <option value="1">One</option>
                                                        <option value="2">Two</option>
                                                        <option value="3">Three</option>
                                                        <option value="4">Four</option>
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
                                                    <label>RBG</label>
                                                    <input value="<?= $patient['rbg'] ?>" type="text" name="rbg" id="rbg" />
                                                    <SPan>XX ( mg/dl )</SPan>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Grade</label>
                                                    <select name="rbg_grade" id="rbg_grade" style="width: 100%;" required>
                                                        <?php if ($patient['rbg_grade'] == "0") { ?>
                                                            <option value="<?= $patient['rbg_grade'] ?>">Zero</option>
                                                        <?php } elseif ($patient['rbg_grade'] == "1") { ?>
                                                            <option value="<?= $patient['rbg_grade'] ?>">One</option>
                                                        <?php } else if ($patient['rbg_grade'] == "2") { ?>
                                                            <option value="<?= $patient['rbg_grade'] ?>">Two</option>
                                                        <?php } elseif ($patient['rbg_grade'] == "3") { ?>
                                                            <option value="<?= $patient['rbg_grade'] ?>">Three</option>
                                                        <?php } else if ($patient['rbg_grade'] == "4") { ?>
                                                            <option value="<?= $patient['rbg_grade'] ?>">Four</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="0">Zero</option>
                                                        <option value="1">One</option>
                                                        <option value="2">Two</option>
                                                        <option value="3">Three</option>
                                                        <option value="4">Four</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="head clearfix">
                                        <div class="isw-ok"></div>
                                        <h1>Full blood count</h1>
                                    </div>


                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>AHemoglobin levels (Hb)</label>
                                                    <input value="<?= $patient['hb'] ?>" type="text" name="hb" id="hb" />
                                                    <SPan>XX.X ( mg/dl )</SPan>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Grade</label>
                                                    <select name="hb_grade" id="hb_grade" style="width: 100%;" required>
                                                        <?php if ($patient['hb_grade'] == "0") { ?>
                                                            <option value="<?= $patient['hb_grade'] ?>">Zero</option>
                                                        <?php } elseif ($patient['hb_grade'] == "1") { ?>
                                                            <option value="<?= $patient['hb_grade'] ?>">One</option>
                                                        <?php } else if ($patient['hb_grade'] == "2") { ?>
                                                            <option value="<?= $patient['hb_grade'] ?>">Two</option>
                                                        <?php } elseif ($patient['hb_grade'] == "3") { ?>
                                                            <option value="<?= $patient['hb_grade'] ?>">Three</option>
                                                        <?php } else if ($patient['hb_grade'] == "4") { ?>
                                                            <option value="<?= $patient['hb_grade'] ?>">Four</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="0">Zero</option>
                                                        <option value="1">One</option>
                                                        <option value="2">Two</option>
                                                        <option value="3">Three</option>
                                                        <option value="4">Four</option>
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
                                                    <label>Hematocrit levels (Hct)</label>
                                                    <input value="<?= $patient['hct'] ?>" type="text" name="hct" id="hct" />
                                                    <SPan>XX ( % )</SPan>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Red blood cell count (RBC)</label>
                                                    <input value="<?= $patient['rbc'] ?>" type="text" name="rbc" id="rbc" />
                                                    <SPan>XXXXXXX ( celss/microliter )</SPan>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>White blood cell count (WBC)</label>
                                                    <input value="<?= $patient['wbc'] ?>" type="text" name="wbc" id="wbc" />
                                                    <SPan>XXXXXXX ( celss/microliter )</SPan>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Grade</label>
                                                    <select name="wbc_grade" id="wbc_grade" style="width: 100%;" required>
                                                        <?php if ($patient['wbc_grade'] == "0") { ?>
                                                            <option value="<?= $patient['wbc_grade'] ?>">Zero</option>
                                                        <?php } elseif ($patient['wbc_grade'] == "1") { ?>
                                                            <option value="<?= $patient['wbc_grade'] ?>">One</option>
                                                        <?php } else if ($patient['wbc_grade'] == "2") { ?>
                                                            <option value="<?= $patient['wbc_grade'] ?>">Two</option>
                                                        <?php } elseif ($patient['wbc_grade'] == "3") { ?>
                                                            <option value="<?= $patient['wbc_grade'] ?>">Three</option>
                                                        <?php } else if ($patient['wbc_grade'] == "4") { ?>
                                                            <option value="<?= $patient['wbc_grade'] ?>">Four</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="0">Zero</option>
                                                        <option value="1">One</option>
                                                        <option value="2">Two</option>
                                                        <option value="3">Three</option>
                                                        <option value="4">Four</option>
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
                                                    <label>ABS Lymphocytes</label>
                                                    <input value="<?= $patient['abs_lymphocytes'] ?>" type="text" name="abs_lymphocytes" id="abs_lymphocytes" />
                                                    <SPan>XXXXX</SPan>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Grade</label>
                                                    <select name="abs_lymphocytes_grade" id="abs_lymphocytes_grade" style="width: 100%;" required>
                                                        <?php if ($patient['abs_lymphocytes_grade'] == "0") { ?>
                                                            <option value="<?= $patient['abs_lymphocytes_grade'] ?>">Zero</option>
                                                        <?php } elseif ($patient['abs_lymphocytes_grade'] == "1") { ?>
                                                            <option value="<?= $patient['abs_lymphocytes_grade'] ?>">One</option>
                                                        <?php } else if ($patient['abs_lymphocytes_grade'] == "2") { ?>
                                                            <option value="<?= $patient['abs_lymphocytes_grade'] ?>">Two</option>
                                                        <?php } elseif ($patient['abs_lymphocytes_grade'] == "3") { ?>
                                                            <option value="<?= $patient['abs_lymphocytes_grade'] ?>">Three</option>
                                                        <?php } else if ($patient['abs_lymphocytes_grade'] == "4") { ?>
                                                            <option value="<?= $patient['abs_lymphocytes_grade'] ?>">Four</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="0">Zero</option>
                                                        <option value="1">One</option>
                                                        <option value="2">Two</option>
                                                        <option value="3">Three</option>
                                                        <option value="4">Four</option>
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
                                                    <label>Platelet count (Plt)</label>
                                                    <input value="<?= $patient['plt'] ?>" type="text" name="plt" id="plt" />
                                                    <SPan>XXXXXX ( celss/microliter )</SPan>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Grade</label>
                                                    <select name="plt_grade" id="plt_grade" style="width: 100%;" required>
                                                        <?php if ($patient['plt_grade'] == "0") { ?>
                                                            <option value="<?= $patient['plt_grade'] ?>">Zero</option>
                                                        <?php } elseif ($patient['plt_grade'] == "1") { ?>
                                                            <option value="<?= $patient['plt_grade'] ?>">One</option>
                                                        <?php } else if ($patient['plt_grade'] == "2") { ?>
                                                            <option value="<?= $patient['plt_grade'] ?>">Two</option>
                                                        <?php } elseif ($patient['plt_grade'] == "3") { ?>
                                                            <option value="<?= $patient['plt_grade'] ?>">Three</option>
                                                        <?php } else if ($patient['plt_grade'] == "4") { ?>
                                                            <option value="<?= $patient['plt_grade'] ?>">Four</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="0">Zero</option>
                                                        <option value="1">One</option>
                                                        <option value="2">Two</option>
                                                        <option value="3">Three</option>
                                                        <option value="4">Four</option>
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
                                                    <label>9. Cancer antigen 15-3</label>
                                                    <input value="<?= $patient['cancer'] ?>" type="text" name="cancer" id="cancer" />
                                                    <SPan>XX ( U/ml )</SPan>

                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>9. PSA (Prostate specific antigen)</label>
                                                    <input value="<?= $patient['prostate'] ?>" type="text" name="prostate" id="prostate" />
                                                    <SPan>XX ( ng/ml )</SPan>

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>9. Chest X-ray</label>
                                                    <select name="chest_xray" id="chest_xray" style="width: 100%;" required>
                                                        <?php if ($patient['chest_xray'] == "1") { ?>
                                                            <option value="<?= $patient['chest_xray'] ?>">Normal</option>
                                                        <?php } elseif ($patient['chest_xray'] == "2") { ?>
                                                            <option value="<?= $patient['chest_xray'] ?>">Abnormal</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="1">Normal</option>
                                                        <option value="2">Abnormal</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6" id="chest_specify">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>9. Specify (Report from Radiologist)</label>
                                                    <input value="<?= $patient['chest_specify'] ?>" type="text" name="chest_specify" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>



                                    <div class="row">

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>10. CT-Scan chest and abdomen report</label>
                                                    <select name="ct_chest" style="width: 100%;" required>
                                                        <?php if ($patient['ct_chest'] == "1") { ?>
                                                            <option value="<?= $patient['ct_chest'] ?>">Normal</option>
                                                        <?php } elseif ($patient['ct_chest'] == "2") { ?>
                                                            <option value="<?= $patient['ct_chest'] ?>">Abnormal</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="1">Normal</option>
                                                        <option value="2">Abnormal</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6" id="ct_chest_specify">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>10. Specify (Report from Radiologist)</label>
                                                    <input value="<?= $patient['ct_chest_specify'] ?>" type="text" name="ct_chest_specify" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>11. Abdominal Ultrasound report</label>
                                                    <select name="ultrasound" id="ultrasound" style="width: 100%;" required>
                                                        <?php if ($patient['ultrasound'] == "1") { ?>
                                                            <option value="<?= $patient['ultrasound'] ?>">Normal</option>
                                                        <?php } elseif ($patient['ultrasound'] == "2") { ?>
                                                            <option value="<?= $patient['ultrasound'] ?>">Abnormal</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="1">Normal</option>
                                                        <option value="2">Abnormal</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6" id="ultrasound_specify">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>11. Specify (Report from Radiologist)</label>
                                                    <input value="<?= $patient['ultrasound_specify'] ?>" type="text" name="ultrasound_specify" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row-form clearfix" id="crf4_cmpltd_date">
                                        <div class="col-md-3">Date of Completion</div>
                                        <input value="<?= $patient['crf4_cmpltd_date'] ?>" class="validate[required]" type="text" name="crf4_cmpltd_date" id="crf1_cmpltd_date" />
                                    </div>

                                    <div class="footer tar">
                                        <input type="hidden" name="id" value="<?= $patient['id'] ?>">
                                        <input type="submit" name="update_crf4" value="Submit" class="btn btn-default">
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php } elseif ($_GET['id'] == 12) { ?>
                        <?php $patient = $override->get1('crf5', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode'])[0] ?>
                        <div class="col-md-offset-1 col-md-8">
                            <div class="head clearfix">
                                <div class="isw-ok"></div>
                                <h1>CRF 5: ADVERSE EVENT TRACKING LOG</h1>
                            </div>
                            <div class="block-fluid">
                                <form id="validation" method="post">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <div class="form-group">
                                                    <label>Date Reported:</label>
                                                    <?php
                                                    $data = $override->get1('crf5', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                    foreach ($data as $st) {
                                                        if ($st['date_reported'] != "") {
                                                    ?>
                                                            <div class="col-md-9"><input value="<?= $st['date_reported'] ?>" class="validate[required,custom[date]]" type="text" name="date_reported" id="date_reported" required /> <span>Example: 2023-01-01</span></div>
                                                        <?php
                                                        } else {
                                                        ?>
                                                            <div class="col-md-9"><input value="" class="validate[required,custom[date]]" type="text" name="date_reported" id="date_reported" required /> <span>Example: 2023-01-01</span></div>
                                                    <?php
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-8">
                                            <div class="row-form clearfix">
                                                <div class="form-group">
                                                    <label>Adverse Event Description:</label>
                                                    <?php
                                                    $data = $override->get1('crf5', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                    foreach ($data as $st) {
                                                        if ($st['ae_description'] != "") {
                                                    ?>
                                                            <textarea value="<?= $st['tdate'] ?>" name="ae_description" rows="4"></textarea>
                                                        <?php
                                                        } else {
                                                        ?>
                                                            <textarea value="" name="ae_description" rows="4"></textarea>
                                                    <?php
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Adverse Event Category</label>
                                                    <?php
                                                    $data = $override->get1('crf5', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                    foreach ($data as $st) {
                                                        if ($st['ae_category'] != "") {
                                                    ?>
                                                            <input value="<?= $st['ae_category'] ?>" type="text" name="ae_category" id="ae_category" required />
                                                        <?php
                                                        } else {
                                                        ?>
                                                            <input value="" type="text" name="ae_category" id="ae_category" required />
                                                    <?php
                                                        }
                                                    }
                                                    ?>
                                                    <span>
                                                        <a href="http://safetyprofiler-ctep.nci.nih.gov/CTC/CTC.aspx" class="btn btn-info">
                                                            **lookup corresponding AE Category at: http://safetyprofiler-ctep.nci.nih.gov/CTC/CTC.aspx
                                                        </a>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Start date</label>
                                                    <?php
                                                    $data = $override->get1('crf5', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                    foreach ($data as $st) {
                                                        if ($st['ae_start_date'] != "") {
                                                    ?>
                                                            <input value="<?= $st['ae_start_date'] ?>" type="text" name="ae_start_date" id="ae_start_date" />
                                                        <?php
                                                        } else {
                                                        ?>
                                                            <input value="" type="text" name="ae_start_date" />
                                                    <?php
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Ongoing ?</label>
                                                    <select name="ae_ongoing" id="ae_ongoing" style="width: 100%;" required>
                                                        <?php
                                                        $data = $override->get1('crf5', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                        foreach ($data as $st) {
                                                            if ($st['ae_ongoing'] == 1) { ?>
                                                                <option value="<?= $st['ae_ongoing'] ?>">Yes</option>
                                                            <?php } else if ($st['ae_ongoing'] == 2) { ?>
                                                                <option value="<?= $st['ae_ongoing'] ?>">No</option>
                                                            <?php } else { ?>
                                                                <option value="">Select</option>
                                                        <?php }
                                                        } ?>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4" id="ae_end_date">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>End date:</label>
                                                    <?php
                                                    $data = $override->get1('crf5', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                    foreach ($data as $st) {
                                                        if ($st['ae_end_date'] != "") {
                                                    ?>
                                                            <input value="<?= $st['ae_end_date'] ?>" type="text" name="ae_end_date" id="ae_end_date" />
                                                        <?php
                                                        } else {
                                                        ?>
                                                            <input value="" type="text" name="ae_end_date" />
                                                    <?php
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">

                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Outcome</label>
                                                    <select name="ae_outcome" id="ae_outcome" style="width: 100%;" required>
                                                        <?php
                                                        $data = $override->get1('crf5', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                        foreach ($data as $st) {
                                                            if ($st['ae_outcome'] == 0) { ?>
                                                                <option value="<?= $st['ae_outcome'] ?>">Fatal</option>
                                                            <?php } else if ($st['ae_outcome'] == 1) { ?>
                                                                <option value="<?= $st['ae_outcome'] ?>">Intervention continues</option>
                                                            <?php } else if ($st['ae_outcome'] == 2) { ?>
                                                                <option value="<?= $st['ae_outcome'] ?>">Not recovered/not resolved </option>
                                                            <?php } else if ($st['ae_outcome'] == 3) { ?>
                                                                <option value="<?= $st['ae_outcome'] ?>">Recovered w/sequelae</option>
                                                            <?php } else if ($st['ae_outcome'] == 4) { ?>
                                                                <option value="<?= $st['ae_outcome'] ?>">Recovered w/o sequelae</option>
                                                            <?php } else if ($st['ae_outcome'] == 5) { ?>
                                                                <option value="<?= $st['ae_outcome'] ?>">Recovered/ Resolving</option>
                                                            <?php } else { ?>
                                                                <option value="">Select</option>
                                                        <?php }
                                                        } ?>
                                                        <option value="0">Fatal</option>
                                                        <option value="1">Intervention continues</option>
                                                        <option value="2">Not recovered/not resolved </option>
                                                        <option value="3">Recovered w/sequelae</option>
                                                        <option value="4">Recovered w/o sequelae</option>
                                                        <option value="5">Recovered/ Resolving</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Severity</label>
                                                    <select name="ae_severity" id="ae_severity" style="width: 100%;" required>
                                                        <?php
                                                        $data = $override->get1('crf5', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                        foreach ($data as $st) {
                                                            if ($st['ae_severity'] == 1) { ?>
                                                                <option value="<?= $st['ae_severity'] ?>">Mild</option>
                                                            <?php } else if ($st['ae_severity'] == 2) { ?>
                                                                <option value="<?= $st['ae_severity'] ?>">Moderate</option>
                                                            <?php } else if ($st['ae_severity'] == 3) { ?>
                                                                <option value="<?= $st['ae_severity'] ?>">severe </option>
                                                            <?php } else if ($st['ae_severity'] == 4) { ?>
                                                                <option value="<?= $st['ae_severity'] ?>">Life-threatening</option>
                                                            <?php } else if ($st['ae_severity'] == 5) { ?>
                                                                <option value="<?= $st['ae_severity'] ?>">Fatal</option>
                                                            <?php } else { ?>
                                                                <option value="">Select</option>
                                                        <?php }
                                                        } ?>
                                                        <option value="1">Mild</option>
                                                        <option value="2">Moderate</option>
                                                        <option value="3">severe</option>
                                                        <option value="4">Life-threatening</option>
                                                        <option value="5">Fatal</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>



                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Serious</label>
                                                    <select name="ae_serious" id="ae_serious" style="width: 100%;" required>
                                                        <?php
                                                        $data = $override->get1('crf5', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                        foreach ($data as $st) {
                                                            if ($st['ae_serious'] == 1) { ?>
                                                                <option value="<?= $st['ae_serious'] ?>">Yes</option>
                                                            <?php } else if ($st['ae_serious'] == 2) { ?>
                                                                <option value="<?= $st['ae_serious'] ?>">No</option>
                                                            <?php } else { ?>
                                                                <option value="">Select</option>
                                                        <?php }
                                                        } ?>
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
                                                    <label>Expected</label>
                                                    <select name="ae_expected" id="ae_expected" style="width: 100%;" required>
                                                        <?php
                                                        $data = $override->get1('crf5', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                        foreach ($data as $st) {
                                                            if ($st['ae_expected'] == 1) { ?>
                                                                <option value="<?= $st['ae_expected'] ?>">Yes</option>
                                                            <?php } else if ($st['ae_expected'] == 2) { ?>
                                                                <option value="<?= $st['ae_expected'] ?>">No</option>
                                                            <?php } else { ?>
                                                                <option value="">Select</option>
                                                        <?php }
                                                        } ?>
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
                                                    <label>Treatment</label>
                                                    <select name="ae_treatment" id="ae_treatment" style="width: 100%;" required>
                                                        <?php
                                                        $data = $override->get1('crf5', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                        foreach ($data as $st) {
                                                            if ($st['ae_treatment'] == 1) { ?>
                                                                <option value="<?= $st['ae_treatment'] ?>">Medication(s)</option>
                                                            <?php } else if ($st['ae_treatment'] == 2) { ?>
                                                                <option value="<?= $st['ae_treatment'] ?>">Non-medication TX</option>
                                                            <?php } else if ($st['ae_treatment'] == 3) { ?>
                                                                <option value="<?= $st['ae_treatment'] ?>">Subject discontinued</option>
                                                            <?php } else { ?>
                                                                <option value="">Select</option>
                                                        <?php }
                                                        } ?>
                                                        <option value="0">None</option>
                                                        <option value="1">Medication(s)</option>
                                                        <option value="2">Non-medication TX </option>
                                                        <option value="3">Subject discontinued</option>
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
                                                    <label>Action Taken</label>
                                                    <select name="ae_taken" id="ae_taken" style="width: 100%;" required>
                                                        <?php
                                                        $data = $override->get1('crf5', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                        foreach ($data as $st) {
                                                            if ($st['ae_taken'] == 0) { ?>
                                                                <option value="<?= $st['ae_taken'] ?>">Not Applicable</option>
                                                            <?php } else if ($st['ae_taken'] == 1) { ?>
                                                                <option value="<?= $st['ae_taken'] ?>">None</option>
                                                            <?php } else if ($st['ae_taken'] == 2) { ?>
                                                                <option value="<?= $st['ae_taken'] ?>">Interrupted</option>
                                                            <?php } else if ($st['ae_taken'] == 3) { ?>
                                                                <option value="<?= $st['ae_taken'] ?>">Discontinued</option>
                                                            <?php } else { ?>
                                                                <option value="">Select</option>
                                                        <?php }
                                                        } ?>
                                                        <option value="0">Not Applicable</option>
                                                        <option value="1">None</option>
                                                        <option value="2">Interrupted </option>
                                                        <option value="3">Discontinued</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Relationship to study teatment</label>
                                                    <select name="ae_relationship" id="ae_relationship" style="width: 100%;" required>
                                                        <?php
                                                        $data = $override->get1('crf5', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                        foreach ($data as $st) {
                                                            if ($st['ae_relationship'] == 1) { ?>
                                                                <option value="<?= $st['ae_relationship'] ?>">Unrelated</option>
                                                            <?php } else if ($st['ae_relationship'] == 2) { ?>
                                                                <option value="<?= $st['ae_relationship'] ?>">Unlikely</option>
                                                            <?php } else if ($st['ae_relationship'] == 3) { ?>
                                                                <option value="<?= $st['ae_relationship'] ?>">Possible</option>
                                                            <?php } else if ($st['ae_relationship'] == 4) { ?>
                                                                <option value="<?= $st['ae_relationship'] ?>">Probable</option>
                                                            <?php } else if ($st['ae_relationship'] == 5) { ?>
                                                                <option value="<?= $st['ae_relationship'] ?>">Definite</option>
                                                            <?php } else { ?>
                                                                <option value="">Select</option>
                                                        <?php }
                                                        } ?>
                                                        <option value="1">Unrelated</option>
                                                        <option value="2">Unlikely</option>
                                                        <option value="3">Possible </option>
                                                        <option value="4">Probable</option>
                                                        <option value="5">Definite</option>
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
                                                    <label>Staff Initials</label>
                                                    <?php
                                                    $data = $override->get1('crf5', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                    foreach ($data as $st) {
                                                        if ($st['ae_staff_initial'] != "") {
                                                    ?>
                                                            <input value="<?= $st['ae_staff_initial'] ?>" type="text" name="ae_staff_initial" id="ae_staff_initial" />
                                                        <?php
                                                        } else {
                                                        ?>
                                                            <input value="" type="text" name="ae_staff_initial" id="ae_staff_initial" />
                                                    <?php
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Date:</label>
                                                    <?php
                                                    $data = $override->get1('crf5', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                    foreach ($data as $st) {
                                                        if ($st['ae_date'] != "") {
                                                    ?>
                                                            <input value="<?= $st['ae_date'] ?>" type="text" name="ae_date" id="ae_date" />

                                                        <?php
                                                        } else {
                                                        ?>
                                                            <input value="" type="text" name="ae_date" id="ae_date" />
                                                    <?php
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="footer tar">
                                        <input type="hidden" name="id" value="<?= $patient['id'] ?>" class="btn btn-default">
                                        <input type="submit" name="update_crf5" value="Submit" class="btn btn-default">
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php } elseif ($_GET['id'] == 13) { ?>
                        <?php $patient = $override->get1('crf6', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode'])[0] ?>
                        <div class="col-md-offset-1 col-md-8">
                            <div class="head clearfix">
                                <div class="isw-ok"></div>
                                <h1>CRF 6: TERMINATION OF STUDY</h1>
                            </div>
                            <div class="block-fluid">
                                <form id="validation" method="post">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <div class="form-group">
                                                    <label>1.a Todays date:</label>
                                                    <?php
                                                    $data = $override->get1('crf6', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                    if ($data) {
                                                        foreach ($data as $st) {
                                                            if ($st['today_date'] != "") {
                                                    ?>
                                                                <div class="col-md-9"><input value="<?= $st['today_date'] ?>" class="validate[required,custom[date]]" type="text" name="today_date" id="today_date" required /> <span>Example: 2023-01-01</span></div>
                                                        <?php
                                                            }
                                                        }
                                                    } else {
                                                        ?>
                                                        <div class="col-md-9"><input value="" class="validate[required,custom[date]]" type="text" name="today_date" id="today_date" required /> <span>Example: 2023-01-01</span></div>
                                                    <?php
                                                    } ?>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <div class="form-group">
                                                    <label>1.b Date patient terminated the study:</label>
                                                    <?php
                                                    $data = $override->get1('crf6', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                    if ($data) {
                                                        foreach ($data as $st) {
                                                            if ($st['terminate_date'] != "") {
                                                    ?>
                                                                <div class="col-md-9"><input value="<?= $st['terminate_date'] ?>" class="validate[required,custom[date]]" type="text" name="terminate_date" id="terminate_date" required /> <span>Example: 2023-01-01</span></div>
                                                        <?php
                                                            }
                                                        }
                                                    } else {
                                                        ?>
                                                        <div class="col-md-9"><input value="" class="validate[required,custom[date]]" type="text" name="terminate_date" id="terminate_date" required /> <span>Example: 2023-01-01</span></div>
                                                    <?php
                                                    } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <label>2. Reason for study termination</label>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>2. a. Patient completed 120 days of follow-up</label>
                                                    <select name="completed120days" id="completed120days" style="width: 100%;" required>
                                                        <?php
                                                        $data = $override->get1('crf6', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                        foreach ($data as $st) {
                                                            if ($st['completed120days'] == 1) { ?>
                                                                <option value="<?= $st['completed120days'] ?>">Yes</option>
                                                            <?php } else if ($st['completed120days'] == 2) { ?>
                                                                <option value="<?= $st['completed120days'] ?>">No</option>
                                                            <?php } else { ?>
                                                                <option value="">Select</option>
                                                        <?php }
                                                        } ?>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>2. b. Patient is reported/known to have died </label>
                                                    <select name="reported_dead" id="reported_dead" style="width: 100%;" required>
                                                        <?php
                                                        $data = $override->get1('crf6', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                        foreach ($data as $st) {
                                                            if ($st['reported_dead'] == 1) { ?>
                                                                <option value="<?= $st['reported_dead'] ?>">Yes</option>
                                                            <?php } else if ($st['reported_dead'] == 2) { ?>
                                                                <option value="<?= $st['reported_dead'] ?>">No</option>
                                                            <?php } else { ?>
                                                                <option value="">Select</option>
                                                        <?php }
                                                        } ?>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>2. c. Patient withdrew consent to participate </label>
                                                    <select name="withdrew_consent" id="withdrew_consent" style="width: 100%;" required>
                                                        <?php
                                                        $data = $override->get1('crf6', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                        foreach ($data as $st) {
                                                            if ($st['withdrew_consent'] == 1) { ?>
                                                                <option value="<?= $st['withdrew_consent'] ?>">Yes</option>
                                                            <?php } else if ($st['withdrew_consent'] == 2) { ?>
                                                                <option value="<?= $st['withdrew_consent'] ?>">No</option>
                                                            <?php } else { ?>
                                                                <option value="">Select</option>
                                                        <?php }
                                                        } ?>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="row" id="start_end_date">
                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>2.a.i Start date</label>
                                                    <?php
                                                    $data = $override->get1('crf6', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                    if ($data) {
                                                        foreach ($data as $st) {
                                                            if ($st['start_date'] != "") {
                                                    ?>
                                                                <input value="<?= $st['start_date'] ?>" class="validate[required,custom[date]]" type="text" name="start_date" id="start_date" required /> <span>Example: 2023-01-01</span>
                                                            <?php
                                                            } else { ?>
                                                                <input value="" class="validate[required,custom[date]]" type="text" name="start_date" id="start_date" /> <span>Example: 2023-01-01</span>
                                                        <?php
                                                            }
                                                        }
                                                    } else {
                                                        ?>
                                                        <input value="" class="validate[required,custom[date]]" type="text" name="start_date" id="start_date" required /> <span>Example: 2023-01-01</span>
                                                    <?php
                                                    } ?>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>2.a.ii End date:</label>
                                                    <?php
                                                    $data = $override->get1('crf6', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                    if ($data) {
                                                        foreach ($data as $st) {
                                                            if ($st['end_date'] != "") {
                                                    ?>
                                                                <input value="<?= $st['end_date'] ?>" class="validate[required,custom[date]]" type="text" name="end_date" id="end_date" required /> <span>Example: 2023-01-01</span>
                                                            <?php
                                                            } else { ?>
                                                                <input value="" class="validate[required,custom[date]]" type="text" name="end_date" id="end_date" /> <span>Example: 2023-01-01</span>
                                                        <?php
                                                            }
                                                        }
                                                    } else {
                                                        ?>
                                                        <input value="" type="text" name="end_date" id="end_date" />
                                                    <?php
                                                    } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>



                                    <div class="row" id="death_details">
                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>2. b.i when was the date of death? </label>
                                                    <?php
                                                    $data = $override->get1('crf6', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                    if ($data) {
                                                        foreach ($data as $st) {
                                                            if ($st['date_death'] != "") {
                                                    ?>
                                                                <input value="<?= $st['date_death'] ?>" type="text" name="date_death" id="date_death" />
                                                        <?php
                                                            }
                                                        }
                                                    } else {
                                                        ?>
                                                        <input value="" type="text" name="date_death" id="date_death" />
                                                    <?php
                                                    } ?>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>2. b.ii The primary cause of death</label>
                                                    <?php
                                                    $data = $override->get1('crf6', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                    if ($data) {
                                                        foreach ($data as $st) {
                                                            if ($st['primary_cause'] != "") {
                                                    ?>
                                                                <textarea value="<?= $st['primary_cause'] ?>" name="primary_cause" rows="4"></textarea>
                                                        <?php
                                                            }
                                                        }
                                                    } else {
                                                        ?>
                                                        <textarea value="" name="primary_cause" rows="4"></textarea>
                                                    <?php
                                                    } ?>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>2. b.iii The secondary cause of death</label>
                                                    <?php
                                                    $data = $override->get1('crf6', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                    if ($data) {
                                                        foreach ($data as $st) {
                                                            if ($st['secondary_cause'] != "") {
                                                    ?>
                                                                <textarea value="<?= $st['secondary_cause'] ?>" name="secondary_cause" rows="4"></textarea>
                                                        <?php
                                                            }
                                                        }
                                                    } else {
                                                        ?>
                                                        <textarea value="" name="secondary_cause" rows="4"></textarea>
                                                    <?php
                                                    } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" id="withdrew_reason1">
                                        <div class="col-sm-12">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>2. b). Reason for withdrawal</label>
                                                    <select name="withdrew_reason" id="withdrew_reason" style="width: 100%;">
                                                        <?php
                                                        $data = $override->get1('crf6', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                        foreach ($data as $st) {
                                                            if ($st['withdrew_reason'] == 1) { ?>
                                                                <option value="<?= $st['withdrew_reason'] ?>">Unwilling to say</option>
                                                            <?php } else if ($st['withdrew_reason'] == 2) { ?>
                                                                <option value="<?= $st['withdrew_reason'] ?>">Side effects of the herbal preparation (NIMRCAF/ Covidol / Bupiji )</option>
                                                            <?php } else if ($st['withdrew_reason'] == 3) { ?>
                                                                <option value="<?= $st['withdrew_reason'] ?>">Side effects of Standard Care</option>
                                                            <?php } else if ($st['withdrew_reason'] == 4) { ?>
                                                                <option value="<?= $st['withdrew_reason'] ?>">Moving to another area</option>
                                                            <?php } else if ($st['withdrew_reason'] == 5) { ?>
                                                                <option value="<?= $st['withdrew_reason'] ?>">Other {withdrew_other}</option>
                                                            <?php } else { ?>
                                                                <option value="">Select</option>
                                                        <?php }
                                                        } ?>
                                                        <option value="1">Unwilling to say</option>
                                                        <option value="2">Side effects of the herbal preparation (NIMRCAF/ Covidol / Bupiji )</option>
                                                        <option value="3">Side effects of Standard Care</option>
                                                        <option value="4">Moving to another area</option>
                                                        <option value="5">Other {withdrew_other}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-12" id="withdrew_other">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>2 d) Specify the reason</label>
                                                    <?php
                                                    $data = $override->get1('crf6', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                    if ($data) {
                                                        foreach ($data as $st) {
                                                            if ($st['withdrew_other'] != "") {
                                                    ?>
                                                                <textarea value="<?= $st['withdrew_other'] ?>" name="withdrew_other" rows="4"></textarea>
                                                        <?php
                                                            }
                                                        }
                                                    } else {
                                                        ?>
                                                        <textarea value="" name="withdrew_other" rows="4"></textarea>
                                                    <?php
                                                    } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>4. Outcome</label>
                                                    <select name="outcome" id="outcome" style="width: 100%;" required>
                                                        <?php
                                                        $data = $override->get1('crf6', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                        foreach ($data as $st) {
                                                            if ($st['outcome'] == 1) { ?>
                                                                <option value="<?= $st['outcome'] ?>">Recovered/Resolved</option>
                                                            <?php } else if ($st['outcome'] == 2) { ?>
                                                                <option value="<?= $st['outcome'] ?>">Recovered with sequelae</option>
                                                            <?php } else if ($st['outcome'] == 3) { ?>
                                                                <option value="<?= $st['outcome'] ?>">Severity worsened</option>
                                                            <?php } else if ($st['outcome'] == 4) { ?>
                                                                <option value="<?= $st['outcome'] ?>">Recovering/Resolving at the end of study</option>
                                                            <?php } else if ($st['outcome'] == 5) { ?>
                                                                <option value="<?= $st['outcome'] ?>">Not recovered/resolved at the end of study</option>
                                                            <?php } else if ($st['outcome'] == 6) { ?>
                                                                <option value="<?= $st['outcome'] ?>">Unknown/Lost to follow up</option>
                                                            <?php } else { ?>
                                                                <option value="">Select</option>
                                                        <?php }
                                                        } ?>
                                                        <option value="1">Recovered/Resolved</option>
                                                        <option value="2">Recovered with sequelae</option>
                                                        <option value="3">Severity worsened</option>
                                                        <option value="4">Recovering/Resolving at the end of study</option>
                                                        <option value="5">Not recovered/resolved at the end of study</option>
                                                        <option value="6">Unknown/Lost to follow up</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>5. Outcome date</label>
                                                    <?php
                                                    $data = $override->get1('crf6', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                    if ($data) {
                                                        foreach ($data as $st) {
                                                            if ($st['outcome_date'] != "") {
                                                    ?>
                                                                <input value="<?= $st['outcome_date'] ?>" type="text" name="outcome_date" id="outcome_date" />
                                                        <?php
                                                            }
                                                        }
                                                    } else {
                                                        ?>
                                                        <input value="" type="text" name="outcome_date" id="outcome_date" />
                                                    <?php
                                                    } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>6. Provide/summarise the adverse event</label>
                                                    <?php
                                                    $data = $override->get1('crf6', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                    if ($data) {
                                                        foreach ($data as $st) {
                                                            if ($st['summary'] != "") {
                                                    ?>
                                                                <textarea value="<?= $st['summary'] ?>" name="summary" rows="4"></textarea>
                                                        <?php
                                                            }
                                                        }
                                                    } else {
                                                        ?>
                                                        <textarea value="" name="summary" rows="4"></textarea>
                                                    <?php
                                                    } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-12">
                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>7.Responsible Clinician Name</label>
                                                    <?php
                                                    $data = $override->get1('crf6', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                    if ($data) {
                                                        foreach ($data as $st) {
                                                            if ($st['clinician_name'] != "") {
                                                    ?>
                                                                <input value="<?= $st['clinician_name'] ?>" type="text" name="clinician_name" id="clinician_name" />
                                                            <?php
                                                            } else { ?>
                                                                <input value="" class="validate[required,custom[date]]" type="text" name="clinician_name" id="clinician_name" /> <span>Example: 2023-01-01</span>
                                                        <?php
                                                            }
                                                        }
                                                    } else {
                                                        ?>
                                                        <input value="" type="text" name="clinician_name" id="clinician_name" />
                                                    <?php
                                                    } ?>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Date of Completion</label>
                                                    <?php
                                                    $data = $override->get1('crf6', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                    if ($data) {
                                                        foreach ($data as $st) {
                                                            if ($st['crf6_cmpltd_date'] != "") {
                                                    ?>
                                                                <input value="<?= $st['crf6_cmpltd_date'] ?>" class="validate[required]" type="text" name="crf6_cmpltd_date" id="crf6_cmpltd_date" />
                                                                <span>Example : 2002-08-21</span>
                                                        <?php
                                                            }
                                                        }
                                                    } else {
                                                        ?>
                                                        <input value="" class="validate[required]" type="text" name="crf6_cmpltd_date" id="crf6_cmpltd_date" />
                                                        <span>Example : 2002-08-21</span> <?php
                                                                                        } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="footer tar">patient
                                        <input type="hidden" name="id" value="<?= $st['id'] ?>">
                                        <input type="submit" name="update_crf6" value="Submit" class="btn btn-default">
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php } elseif ($_GET['id'] == 14) { ?>
                        <?php $patient = $override->get1('crf4', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode'])[0] ?>
                        <div class="col-md-offset-1 col-md-8">
                            <div class="head clearfix">
                                <div class="isw-ok"></div>
                                <h1>CRF 4</h1>
                            </div>
                            <div class="block-fluid">
                                <form id="validation" method="post">
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Date of Sample Collection:</div>
                                        <div class="col-md-9"><input value="<?= $patient['sample_date'] ?>" class="validate[required,custom[date]]" type="text" name="sample_date" id="sample_date" required /> <span>Example: 2023-01-01</span></div>
                                    </div>

                                    <div class="head clearfix">
                                        <div class="isw-ok"></div>
                                        <h1>Blood tests:</h1>
                                    </div>

                                    <div class="head clearfix">
                                        <div class="isw-ok"></div>
                                        <h1>1. Renal function test</h1>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Serum urea levels</label>
                                                    <input value="<?= $patient['renal_urea'] ?>" type="text" name="renal_urea" id="renal_urea" />
                                                    <SPan>XX.X ( mg/dl )</SPan>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Serum creatinine levels</label>
                                                    <input value="<?= $patient['renal_creatinine'] ?>" type="text" name="renal_creatinine" id="renal_creatinine" />
                                                    <SPan>X.X ( mg/dl )</SPan>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>eGFR mL/min per 1.73 m2</label>
                                                    <input value="<?= $patient['renal_egfr'] ?>" type="text" name="renal_egfr" id="renal_egfr" />
                                                    <SPan>XXX.X ( ml/min )</SPan>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="head clearfix">
                                        <div class="isw-ok"></div>
                                        <h1>2. Liver function test</h1>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>AST levels</label>
                                                    <input value="<?= $patient['liver_ast'] ?>" type="text" name="liver_ast" id="liver_ast" />
                                                    <SPan>XXX ( units/L )</SPan>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Grade</label>
                                                    <select name="liver_ast_grade" id="liver_ast_grade" style="width: 100%;" required>
                                                        <?php if ($patient['liver_ast_grade'] == "0") { ?>
                                                            <option value="<?= $patient['liver_ast_grade'] ?>">Zero</option>
                                                        <?php } elseif ($patient['liver_ast_grade'] == "1") { ?>
                                                            <option value="<?= $patient['liver_ast_grade'] ?>">One</option>
                                                        <?php } else if ($patient['liver_ast_grade'] == "2") { ?>
                                                            <option value="<?= $patient['liver_ast_grade'] ?>">Two</option>
                                                        <?php } elseif ($patient['liver_ast_grade'] == "3") { ?>
                                                            <option value="<?= $patient['liver_ast_grade'] ?>">Three</option>
                                                        <?php } else if ($patient['liver_ast_grade'] == "4") { ?>
                                                            <option value="<?= $patient['liver_ast_grade'] ?>">Four</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="0">Zero</option>
                                                        <option value="1">One</option>
                                                        <option value="2">Two</option>
                                                        <option value="3">Three</option>
                                                        <option value="4">Four</option>
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
                                                    <label>ALT levels</label>
                                                    <input value="<?= $patient['liver_alt'] ?>" type="text" name="liver_alt" id="liver_alt" />
                                                    <SPan>XXX ( units/L )</SPan>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Grade</label>
                                                    <select name="liver_alt_grade" id="liver_alt_grade" style="width: 100%;" required>
                                                        <?php if ($patient['liver_alt_grade'] == "0") { ?>
                                                            <option value="<?= $patient['liver_alt_grade'] ?>">Zero</option>
                                                        <?php } elseif ($patient['liver_alt_grade'] == "1") { ?>
                                                            <option value="<?= $patient['liver_alt_grade'] ?>">One</option>
                                                        <?php } else if ($patient['liver_alt_grade'] == "2") { ?>
                                                            <option value="<?= $patient['liver_alt_grade'] ?>">Two</option>
                                                        <?php } elseif ($patient['liver_alt_grade'] == "3") { ?>
                                                            <option value="<?= $patient['liver_alt_grade'] ?>">Three</option>
                                                        <?php } else if ($patient['liver_alt_grade'] == "4") { ?>
                                                            <option value="<?= $patient['liver_alt_grade'] ?>">Four</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="0">Zero</option>
                                                        <option value="1">One</option>
                                                        <option value="2">Two</option>
                                                        <option value="3">Three</option>
                                                        <option value="4">Four</option>
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
                                                    <label>ALP levels</label>
                                                    <input value="<?= $patient['liver_alp'] ?>" type="text" name="liver_alp" id="liver_alp" />
                                                    <SPan>XXX ( units/L )</SPan>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Grade</label>
                                                    <select name="liver_alp_grade" id="liver_alp_grade" style="width: 100%;" required>
                                                        <?php if ($patient['liver_alp_grade'] == "0") { ?>
                                                            <option value="<?= $patient['liver_alp_grade'] ?>">Zero</option>
                                                        <?php } elseif ($patient['liver_alp_grade'] == "1") { ?>
                                                            <option value="<?= $patient['liver_alp_grade'] ?>">One</option>
                                                        <?php } else if ($patient['liver_alp_grade'] == "2") { ?>
                                                            <option value="<?= $patient['liver_alp_grade'] ?>">Two</option>
                                                        <?php } elseif ($patient['liver_alp_grade'] == "3") { ?>
                                                            <option value="<?= $patient['liver_alp_grade'] ?>">Three</option>
                                                        <?php } else if ($patient['liver_alp_grade'] == "4") { ?>
                                                            <option value="<?= $patient['liver_alp_grade'] ?>">Four</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="0">Zero</option>
                                                        <option value="1">One</option>
                                                        <option value="2">Two</option>
                                                        <option value="3">Three</option>
                                                        <option value="4">Four</option>
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
                                                    <label>PT</label>
                                                    <input value="<?= $patient['liver_pt'] ?>" type="text" name="liver_pt" id="liver_pt" />
                                                    <SPan>XXX ( units/L )</SPan>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Grade</label>
                                                    <select name="liver_pt_grade" id="liver_pt_grade" style="width: 100%;" required>
                                                        <?php if ($patient['liver_pt_grade'] == "0") { ?>
                                                            <option value="<?= $patient['liver_pt_grade'] ?>">Zero</option>
                                                        <?php } elseif ($patient['liver_pt_grade'] == "1") { ?>
                                                            <option value="<?= $patient['liver_pt_grade'] ?>">One</option>
                                                        <?php } else if ($patient['liver_pt_grade'] == "2") { ?>
                                                            <option value="<?= $patient['liver_pt_grade'] ?>">Two</option>
                                                        <?php } elseif ($patient['liver_pt_grade'] == "3") { ?>
                                                            <option value="<?= $patient['liver_pt_grade'] ?>">Three</option>
                                                        <?php } else if ($patient['liver_pt_grade'] == "4") { ?>
                                                            <option value="<?= $patient['liver_pt_grade'] ?>">Four</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="0">Zero</option>
                                                        <option value="1">One</option>
                                                        <option value="2">Two</option>
                                                        <option value="3">Three</option>
                                                        <option value="4">Four</option>
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
                                                    <label>PTT</label>
                                                    <input value="<?= $patient['liver_ptt'] ?>" type="text" name="liver_ptt" id="liver_ptt" />
                                                    <SPan>XXX ( units/L )</SPan>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Grade</label>
                                                    <select name="liver_ptt_grade" id="liver_ptt_grade" style="width: 100%;" required>
                                                        <?php if ($patient['liver_ptt_grade'] == "0") { ?>
                                                            <option value="<?= $patient['liver_ptt_grade'] ?>">Zero</option>
                                                        <?php } elseif ($patient['liver_ptt_grade'] == "1") { ?>
                                                            <option value="<?= $patient['liver_ptt_grade'] ?>">One</option>
                                                        <?php } else if ($patient['liver_ptt_grade'] == "2") { ?>
                                                            <option value="<?= $patient['liver_ptt_grade'] ?>">Two</option>
                                                        <?php } elseif ($patient['liver_ptt_grade'] == "3") { ?>
                                                            <option value="<?= $patient['liver_ptt_grade'] ?>">Three</option>
                                                        <?php } else if ($patient['liver_ptt_grade'] == "4") { ?>
                                                            <option value="<?= $patient['liver_ptt_grade'] ?>">Four</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="0">Zero</option>
                                                        <option value="1">One</option>
                                                        <option value="2">Two</option>
                                                        <option value="3">Three</option>
                                                        <option value="4">Four</option>
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
                                                    <label>INR</label>
                                                    <input value="<?= $patient['liver_inr'] ?>" type="text" name="liver_inr" id="liver_inr" />
                                                    <SPan>XXX ( units/L )</SPan>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Grade</label>
                                                    <select name="liver_inr_grade" id="liver_inr_grade" style="width: 100%;" required>
                                                        <?php if ($patient['liver_inr_grade'] == "0") { ?>
                                                            <option value="<?= $patient['liver_inr_grade'] ?>">Zero</option>
                                                        <?php } elseif ($patient['liver_inr_grade'] == "1") { ?>
                                                            <option value="<?= $patient['liver_inr_grade'] ?>">One</option>
                                                        <?php } else if ($patient['liver_inr_grade'] == "2") { ?>
                                                            <option value="<?= $patient['liver_inr_grade'] ?>">Two</option>
                                                        <?php } elseif ($patient['liver_inr_grade'] == "3") { ?>
                                                            <option value="<?= $patient['liver_inr_grade'] ?>">Three</option>
                                                        <?php } else if ($patient['liver_inr_grade'] == "4") { ?>
                                                            <option value="<?= $patient['liver_inr_grade'] ?>">Four</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="0">Zero</option>
                                                        <option value="1">One</option>
                                                        <option value="2">Two</option>
                                                        <option value="3">Three</option>
                                                        <option value="4">Four</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>GGT levels</label>
                                                    <input value="<?= $patient['liver_ggt'] ?>" type="text" name="liver_ggt" id="liver_ggt" />
                                                    <SPan>XXX ( units/L )</SPan>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Serum albumin levels</label>
                                                    <input value="<?= $patient['liver_albumin'] ?>" type="text" name="liver_albumin" id="liver_albumin" />
                                                    <SPan>XXX ( grams/L )</SPan>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Grade</label>
                                                    <select name="liver_albumin_grade" id="liver_albumin_grade" style="width: 100%;" required>
                                                        <?php if ($patient['liver_albumin_grade'] == "0") { ?>
                                                            <option value="<?= $patient['liver_albumin_grade'] ?>">Zero</option>
                                                        <?php } elseif ($patient['liver_albumin_grade'] == "1") { ?>
                                                            <option value="<?= $patient['liver_albumin_grade'] ?>">One</option>
                                                        <?php } else if ($patient['liver_albumin_grade'] == "2") { ?>
                                                            <option value="<?= $patient['liver_albumin_grade'] ?>">Two</option>
                                                        <?php } elseif ($patient['liver_albumin_grade'] == "3") { ?>
                                                            <option value="<?= $patient['liver_albumin_grade'] ?>">Three</option>
                                                        <?php } else if ($patient['liver_albumin_grade'] == "4") { ?>
                                                            <option value="<?= $patient['liver_albumin_grade'] ?>">Four</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="0">Zero</option>
                                                        <option value="1">One</option>
                                                        <option value="2">Two</option>
                                                        <option value="3">Three</option>
                                                        <option value="4">Four</option>
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
                                                    <label>Bilirubin total</label>
                                                    <input value="<?= $patient['liver_bilirubin_total'] ?>" type="text" name="liver_bilirubin_total" id="liver_bilirubin_total" />
                                                    <SPan>XXX ( grams/L )</SPan>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Grade</label>
                                                    <select name="bilirubin_total_grade" id="bilirubin_total_grade" style="width: 100%;" required>
                                                        <?php if ($patient['bilirubin_total_grade'] == "0") { ?>
                                                            <option value="<?= $patient['bilirubin_total_grade'] ?>">Zero</option>
                                                        <?php } elseif ($patient['bilirubin_total_grade'] == "1") { ?>
                                                            <option value="<?= $patient['bilirubin_total_grade'] ?>">One</option>
                                                        <?php } else if ($patient['bilirubin_total_grade'] == "2") { ?>
                                                            <option value="<?= $patient['bilirubin_total_grade'] ?>">Two</option>
                                                        <?php } elseif ($patient['bilirubin_total_grade'] == "3") { ?>
                                                            <option value="<?= $patient['bilirubin_total_grade'] ?>">Three</option>
                                                        <?php } else if ($patient['bilirubin_total_grade'] == "4") { ?>
                                                            <option value="<?= $patient['bilirubin_total_grade'] ?>">Four</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="0">Zero</option>
                                                        <option value="1">One</option>
                                                        <option value="2">Two</option>
                                                        <option value="3">Three</option>
                                                        <option value="4">Four</option>
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
                                                    <label>Bilirubin direct</label>
                                                    <input value="<?= $patient['liver_bilirubin_direct'] ?>" type="text" name="liver_bilirubin_direct" id="liver_bilirubin_direct" />
                                                    <SPan>XXX ( grams/L )</SPan>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Grade</label>
                                                    <select name="bilirubin_direct_grade" id="bilirubin_direct_grade" style="width: 100%;" required>
                                                        <?php if ($patient['bilirubin_direct_grade'] == "0") { ?>
                                                            <option value="<?= $patient['bilirubin_direct_grade'] ?>">Zero</option>
                                                        <?php } elseif ($patient['bilirubin_direct_grade'] == "1") { ?>
                                                            <option value="<?= $patient['bilirubin_direct_grade'] ?>">One</option>
                                                        <?php } else if ($patient['bilirubin_direct_grade'] == "2") { ?>
                                                            <option value="<?= $patient['bilirubin_direct_grade'] ?>">Two</option>
                                                        <?php } elseif ($patient['bilirubin_direct_grade'] == "3") { ?>
                                                            <option value="<?= $patient['bilirubin_direct_grade'] ?>">Three</option>
                                                        <?php } else if ($patient['bilirubin_direct_grade'] == "4") { ?>
                                                            <option value="<?= $patient['bilirubin_direct_grade'] ?>">Four</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="0">Zero</option>
                                                        <option value="1">One</option>
                                                        <option value="2">Two</option>
                                                        <option value="3">Three</option>
                                                        <option value="4">Four</option>
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
                                                    <label>RBG</label>
                                                    <input value="<?= $patient['rbg'] ?>" type="text" name="rbg" id="rbg" />
                                                    <SPan>XX ( mg/dl )</SPan>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Grade</label>
                                                    <select name="rbg_grade" id="rbg_grade" style="width: 100%;" required>
                                                        <?php if ($patient['rbg_grade'] == "0") { ?>
                                                            <option value="<?= $patient['rbg_grade'] ?>">Zero</option>
                                                        <?php } elseif ($patient['rbg_grade'] == "1") { ?>
                                                            <option value="<?= $patient['rbg_grade'] ?>">One</option>
                                                        <?php } else if ($patient['rbg_grade'] == "2") { ?>
                                                            <option value="<?= $patient['rbg_grade'] ?>">Two</option>
                                                        <?php } elseif ($patient['rbg_grade'] == "3") { ?>
                                                            <option value="<?= $patient['rbg_grade'] ?>">Three</option>
                                                        <?php } else if ($patient['rbg_grade'] == "4") { ?>
                                                            <option value="<?= $patient['rbg_grade'] ?>">Four</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="0">Zero</option>
                                                        <option value="1">One</option>
                                                        <option value="2">Two</option>
                                                        <option value="3">Three</option>
                                                        <option value="4">Four</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="head clearfix">
                                        <div class="isw-ok"></div>
                                        <h1>Full blood count</h1>
                                    </div>


                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>AHemoglobin levels (Hb)</label>
                                                    <input value="<?= $patient['hb'] ?>" type="text" name="hb" id="hb" />
                                                    <SPan>XX.X ( mg/dl )</SPan>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Grade</label>
                                                    <select name="hb_grade" id="hb_grade" style="width: 100%;" required>
                                                        <?php if ($patient['hb_grade'] == "0") { ?>
                                                            <option value="<?= $patient['hb_grade'] ?>">Zero</option>
                                                        <?php } elseif ($patient['hb_grade'] == "1") { ?>
                                                            <option value="<?= $patient['hb_grade'] ?>">One</option>
                                                        <?php } else if ($patient['hb_grade'] == "2") { ?>
                                                            <option value="<?= $patient['hb_grade'] ?>">Two</option>
                                                        <?php } elseif ($patient['hb_grade'] == "3") { ?>
                                                            <option value="<?= $patient['hb_grade'] ?>">Three</option>
                                                        <?php } else if ($patient['hb_grade'] == "4") { ?>
                                                            <option value="<?= $patient['hb_grade'] ?>">Four</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="0">Zero</option>
                                                        <option value="1">One</option>
                                                        <option value="2">Two</option>
                                                        <option value="3">Three</option>
                                                        <option value="4">Four</option>
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
                                                    <label>Hematocrit levels (Hct)</label>
                                                    <input value="<?= $patient['hct'] ?>" type="text" name="hct" id="hct" />
                                                    <SPan>XX ( % )</SPan>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Red blood cell count (RBC)</label>
                                                    <input value="<?= $patient['rbc'] ?>" type="text" name="rbc" id="rbc" />
                                                    <SPan>XXXXXXX ( celss/microliter )</SPan>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>White blood cell count (WBC)</label>
                                                    <input value="<?= $patient['wbc'] ?>" type="text" name="wbc" id="wbc" />
                                                    <SPan>XXXXXXX ( celss/microliter )</SPan>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Grade</label>
                                                    <select name="wbc_grade" id="wbc_grade" style="width: 100%;" required>
                                                        <?php if ($patient['wbc_grade'] == "0") { ?>
                                                            <option value="<?= $patient['wbc_grade'] ?>">Zero</option>
                                                        <?php } elseif ($patient['wbc_grade'] == "1") { ?>
                                                            <option value="<?= $patient['wbc_grade'] ?>">One</option>
                                                        <?php } else if ($patient['wbc_grade'] == "2") { ?>
                                                            <option value="<?= $patient['wbc_grade'] ?>">Two</option>
                                                        <?php } elseif ($patient['wbc_grade'] == "3") { ?>
                                                            <option value="<?= $patient['wbc_grade'] ?>">Three</option>
                                                        <?php } else if ($patient['wbc_grade'] == "4") { ?>
                                                            <option value="<?= $patient['wbc_grade'] ?>">Four</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="0">Zero</option>
                                                        <option value="1">One</option>
                                                        <option value="2">Two</option>
                                                        <option value="3">Three</option>
                                                        <option value="4">Four</option>
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
                                                    <label>ABS Lymphocytes</label>
                                                    <input value="<?= $patient['abs_lymphocytes'] ?>" type="text" name="abs_lymphocytes" id="abs_lymphocytes" />
                                                    <SPan>XXXXX</SPan>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Grade</label>
                                                    <select name="abs_lymphocytes_grade" id="abs_lymphocytes_grade" style="width: 100%;" required>
                                                        <?php if ($patient['abs_lymphocytes_grade'] == "0") { ?>
                                                            <option value="<?= $patient['abs_lymphocytes_grade'] ?>">Zero</option>
                                                        <?php } elseif ($patient['abs_lymphocytes_grade'] == "1") { ?>
                                                            <option value="<?= $patient['abs_lymphocytes_grade'] ?>">One</option>
                                                        <?php } else if ($patient['abs_lymphocytes_grade'] == "2") { ?>
                                                            <option value="<?= $patient['abs_lymphocytes_grade'] ?>">Two</option>
                                                        <?php } elseif ($patient['abs_lymphocytes_grade'] == "3") { ?>
                                                            <option value="<?= $patient['abs_lymphocytes_grade'] ?>">Three</option>
                                                        <?php } else if ($patient['abs_lymphocytes_grade'] == "4") { ?>
                                                            <option value="<?= $patient['abs_lymphocytes_grade'] ?>">Four</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="0">Zero</option>
                                                        <option value="1">One</option>
                                                        <option value="2">Two</option>
                                                        <option value="3">Three</option>
                                                        <option value="4">Four</option>
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
                                                    <label>Platelet count (Plt)</label>
                                                    <input value="<?= $patient['plt'] ?>" type="text" name="plt" id="plt" />
                                                    <SPan>XXXXXX ( celss/microliter )</SPan>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Grade</label>
                                                    <select name="plt_grade" id="plt_grade" style="width: 100%;" required>
                                                        <?php if ($patient['plt_grade'] == "0") { ?>
                                                            <option value="<?= $patient['plt_grade'] ?>">Zero</option>
                                                        <?php } elseif ($patient['plt_grade'] == "1") { ?>
                                                            <option value="<?= $patient['plt_grade'] ?>">One</option>
                                                        <?php } else if ($patient['plt_grade'] == "2") { ?>
                                                            <option value="<?= $patient['plt_grade'] ?>">Two</option>
                                                        <?php } elseif ($patient['plt_grade'] == "3") { ?>
                                                            <option value="<?= $patient['plt_grade'] ?>">Three</option>
                                                        <?php } else if ($patient['plt_grade'] == "4") { ?>
                                                            <option value="<?= $patient['plt_grade'] ?>">Four</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="0">Zero</option>
                                                        <option value="1">One</option>
                                                        <option value="2">Two</option>
                                                        <option value="3">Three</option>
                                                        <option value="4">Four</option>
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
                                                    <label>9. Cancer antigen 15-3</label>
                                                    <input value="<?= $patient['cancer'] ?>" type="text" name="cancer" id="cancer" />
                                                    <SPan>XX ( U/ml )</SPan>

                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>9. PSA (Prostate specific antigen)</label>
                                                    <input value="<?= $patient['prostate'] ?>" type="text" name="prostate" id="prostate" />
                                                    <SPan>XX ( ng/ml )</SPan>

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>9. Chest X-ray</label>
                                                    <select name="chest_xray" id="chest_xray" style="width: 100%;" required>
                                                        <?php if ($patient['chest_xray'] == "1") { ?>
                                                            <option value="<?= $patient['chest_xray'] ?>">Normal</option>
                                                        <?php } elseif ($patient['chest_xray'] == "2") { ?>
                                                            <option value="<?= $patient['chest_xray'] ?>">Abnormal</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="1">Normal</option>
                                                        <option value="2">Abnormal</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6" id="chest_specify">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>9. Specify (Report from Radiologist)</label>
                                                    <input value="<?= $patient['chest_specify'] ?>" type="text" name="chest_specify" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>



                                    <div class="row">

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>10. CT-Scan chest and abdomen report</label>
                                                    <select name="ct_chest" style="width: 100%;" required>
                                                        <?php if ($patient['ct_chest'] == "1") { ?>
                                                            <option value="<?= $patient['ct_chest'] ?>">Normal</option>
                                                        <?php } elseif ($patient['ct_chest'] == "2") { ?>
                                                            <option value="<?= $patient['ct_chest'] ?>">Abnormal</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="1">Normal</option>
                                                        <option value="2">Abnormal</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6" id="ct_chest_specify">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>10. Specify (Report from Radiologist)</label>
                                                    <input value="<?= $patient['ct_chest_specify'] ?>" type="text" name="ct_chest_specify" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>11. Abdominal Ultrasound report</label>
                                                    <select name="ultrasound" id="ultrasound" style="width: 100%;" required>
                                                        <?php if ($patient['ultrasound'] == "1") { ?>
                                                            <option value="<?= $patient['ultrasound'] ?>">Normal</option>
                                                        <?php } elseif ($patient['ultrasound'] == "2") { ?>
                                                            <option value="<?= $patient['ultrasound'] ?>">Abnormal</option>
                                                        <?php } else { ?>
                                                            <option value="">Select</option>
                                                        <?php } ?>
                                                        <option value="1">Normal</option>
                                                        <option value="2">Abnormal</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6" id="ultrasound_specify">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>11. Specify (Report from Radiologist)</label>
                                                    <input value="<?= $patient['ultrasound_specify'] ?>" type="text" name="ultrasound_specify" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row-form clearfix" id="crf4_cmpltd_date">
                                        <div class="col-md-3">Date of Completion</div>
                                        <input value="<?= $patient['crf4_cmpltd_date'] ?>" class="validate[required]" type="text" name="crf4_cmpltd_date" id="crf1_cmpltd_date" />
                                    </div>

                                    <div class="footer tar">
                                        <input type="hidden" name="id" value="<?= $patient['id'] ?>">
                                        <input type="submit" name="update_crf4" value="Submit" class="btn btn-default">
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php } elseif ($_GET['id'] == 15) { ?>
                        <?php $patient = $override->get1('crf7', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode'])[0] ?>
                        <div class="col-md-offset-1 col-md-8">
                            <div class="head clearfix">
                                <div class="isw-ok"></div>
                                <h1>CRF 7: Quality of Life Questionnaire </h1>
                            </div>
                            <div class="block-fluid">
                                <form id="validation" method="post">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <div class="form-group">
                                                    <label>Tarehe ya Leo:</label>
                                                    <?php
                                                    $data = $override->get1('crf7', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                    foreach ($data as $st) {
                                                        if ($st['tdate'] != "") {
                                                    ?>
                                                            <div class="col-md-9"><input value="<?= $st['tdate'] ?>" class="validate[required,custom[date]]" type="text" name="tdate" id="tdate" required /> <span>Example: 2023-01-01</span></div>
                                                        <?php
                                                        } else {
                                                        ?>
                                                            <div class="col-md-9"><input value="" class="validate[required,custom[date]]" type="text" name="tdate" id="tdate" required /> <span>Example: 2023-01-01</span></div>
                                                    <?php
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>A. Uwezo wa kutembea</label>
                                                    <select name="mobility" id="mobility" style="width: 100%;" required>
                                                        <?php
                                                        $data = $override->get1('crf7', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                        foreach ($data as $st) {
                                                            if ($st['mobility'] == 1) { ?>
                                                                <option value="<?= $st['mobility'] ?>">Sina tatizo katika kutembea</option>
                                                            <?php } else if ($st['mobility'] == 2) { ?>
                                                                <option value="<?= $st['mobility'] ?>">Nina matatizo kiasi katika kutembea</option>
                                                            <?php } else if ($st['mobility'] == 3) { ?>
                                                                <option value="<?= $st['mobility'] ?>">Siwezi kutembea kabisa</option>
                                                            <?php } else { ?>
                                                                <option value="">Select</option>
                                                        <?php }
                                                        } ?>
                                                        <option value="1">Sina tatizo katika kutembea </option>
                                                        <option value="2">Nina matatizo kiasi katika kutembea</option>
                                                        <option value="3">Siwezi kutembea kabisa</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>B. Uwezo wa kujihudumia</label>
                                                    <select name="self_care" id="self_care" style="width: 100%;" required>
                                                        <?php
                                                        $data = $override->get1('crf7', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                        foreach ($data as $st) {
                                                            if ($st['self_care'] == 1) { ?>
                                                                <option value="<?= $st['self_care'] ?>">Sina tatizo kujihudumia mwenyewe</option>
                                                            <?php } else if ($st['self_care'] == 2) { ?>
                                                                <option value="<?= $st['self_care'] ?>">Nina matatizo kiasi katika kujisafisha au kuvaa mwenyewe</option>
                                                            <?php } else if ($st['self_care'] == 3) { ?>
                                                                <option value="<?= $st['self_care'] ?>">Siwezi kujisafisha wala kuvaa mwenyewe</option>
                                                            <?php } else { ?>
                                                                <option value="">Select</option>
                                                        <?php }
                                                        } ?>
                                                        <option value="1">Sina tatizo kujihudumia mwenyewe</option>
                                                        <option value="2">Nina matatizo kiasi katika kujisafisha au kuvaa mwenyewe</option>
                                                        <option value="3">Siwezi kujisafisha wala kuvaa mwenyewe</option>
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
                                                    <label>C. Shughuli za kila siku (mfano: kazi, kusoma shuleni/chuoni, kazi za nyumbani,
                                                        shughuli za kifamilia au starehe)</label>
                                                    <select name="usual_active" id="usual_active" style="width: 100%;" required>
                                                        <?php
                                                        $data = $override->get1('crf7', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                        foreach ($data as $st) {
                                                            if ($st['usual_active'] == 1) { ?>
                                                                <option value="<?= $st['usual_active'] ?>">Sina tatizo katika kufanya shughuli zangu za kila siku</option>
                                                            <?php } else if ($st['usual_active'] == 2) { ?>
                                                                <option value="<?= $st['usual_active'] ?>">Nina matatizo kiasi katika kufanya shughuli zangu za kila siku</option>
                                                            <?php } else if ($st['usual_active'] == 3) { ?>
                                                                <option value="<?= $st['usual_active'] ?>">Siwezi kabisa kufanya shughuli zangu za kila siku</option>
                                                            <?php } else { ?>
                                                                <option value="">Select</option>
                                                        <?php }
                                                        } ?>
                                                        <option value="1">Sina tatizo katika kufanya shughuli zangu za kila siku</option>
                                                        <option value="2">Nina matatizo kiasi katika kufanya shughuli zangu za kila siku</option>
                                                        <option value="3">Siwezi kabisa kufanya shughuli zangu za kila siku</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>D. Maumivu/Kutojisikia vizuri</label>
                                                    <select name="pain" id="pain" style="width: 100%;" required>
                                                        <?php
                                                        $data = $override->get1('crf7', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                        foreach ($data as $st) {
                                                            if ($st['usual_active'] == 1) { ?>
                                                                <option value="<?= $st['usual_active'] ?>">Sina maumivu au najisikia vizuri</option>
                                                            <?php } else if ($st['usual_active'] == 2) { ?>
                                                                <option value="<?= $st['usual_active'] ?>">Nina maumivu kiasi au najisikia vibaya kiasi</option>
                                                            <?php } else if ($st['usual_active'] == 3) { ?>
                                                                <option value="<?= $st['usual_active'] ?>">Nina maumivu makali au najisikia vibaya sana</option>
                                                            <?php } else { ?>
                                                                <option value="">Select</option>
                                                        <?php }
                                                        } ?>
                                                        <option value="1">Sina maumivu au najisikia vizuri</option>
                                                        <option value="2">Nina maumivu kiasi au najisikia vibaya kiasi</option>
                                                        <option value="3">Nina maumivu makali au najisikia vibaya sana</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>E. Wasiwasi/sonona</label>
                                                    <select name="anxiety" id="anxiety" style="width: 100%;" required>
                                                        <?php
                                                        $data = $override->get1('crf7', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                        foreach ($data as $st) {
                                                            if ($st['anxiety'] == 1) { ?>
                                                                <option value="<?= $st['anxiety'] ?>">Sina wasiwasi au sonona</option>
                                                            <?php } else if ($st['anxiety'] == 2) { ?>
                                                                <option value="<?= $st['anxiety'] ?>">Nina wasiwasi kiasi au sonona kiasi</option>
                                                            <?php } else if ($st['anxiety'] == 3) { ?>
                                                                <option value="<?= $st['anxiety'] ?>">Nina wasiwasi sana au nina sonona sana</option>
                                                            <?php } else { ?>
                                                                <option value="">Select</option>
                                                        <?php }
                                                        } ?>
                                                        <option value="1">Sina wasiwasi au sonona</option>
                                                        <option value="2">Nina wasiwasi kiasi au sonona kiasi</option>
                                                        <option value="3">Nina wasiwasi sana au nina sonona sana</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="head clearfix">
                                        <div class="isw-ok"></div>
                                        <h1>ON-SITE MONITORING</h1>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <div class="form-group">
                                                    <label>DATE FORM COMPLETED:</label>
                                                    <?php
                                                    $data = $override->get1('crf7', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                    foreach ($data as $st) {
                                                        if ($st['FDATE'] != "") {
                                                    ?>
                                                            <div class="col-md-9"><input value="<?= $st['FDATE'] ?>" class="validate[required,custom[date]]" type="text" name="FDATE" id="FDATE" required /> <span>Example: 2023-01-01</span></div>
                                                        <?php
                                                        } else {
                                                        ?>
                                                            <input value="" class="validate[required,custom[date]]" type="text" name="FDATE" id="FDATE" required /> <span>Example: 2023-01-01</span>
                                                    <?php
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <div class="form-group">
                                                    <label>NAME OF PERSON CHECKING FORM:</label>
                                                    <?php
                                                    $data = $override->get1('crf7', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                    foreach ($data as $st) {
                                                        if ($st['cpersid'] != "") {
                                                    ?>
                                                            <input value="<?= $st['cpersid'] ?>" type="text" name="cpersid" id="cpersid" required />
                                                        <?php
                                                        } else {
                                                        ?>
                                                            <input value="" type="text" name="cpersid" id="cpersid" required />
                                                    <?php
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <div class="form-group">
                                                    <label>DATE FORM CHECKED:</label>
                                                    <?php
                                                    $data = $override->get1('crf7', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                    foreach ($data as $st) {
                                                        if ($st['cDATE'] != "") {
                                                    ?>
                                                            <div class="col-md-9"><input value="<?= $st['cDATE'] ?>" class="validate[required,custom[date]]" type="text" name="cDATE" id="cDATE" required /> <span>Example: 2023-01-01</span></div>
                                                        <?php
                                                        } else {
                                                        ?>
                                                            <input value="" class="validate[required,custom[date]]" type="text" name="cDATE" id="cDATE" required /> <span>Example: 2023-01-01</span>
                                                    <?php
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="footer tar">
                                        <input type="hidden" name="id" value="<?= $patient['id'] ?>" class="btn btn-default">
                                        <input type="submit" name="update_crf7" value="Submit" class="btn btn-default">
                                    </div>
                                </form>
                            </div>
                        </div>
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
</script>

</html>