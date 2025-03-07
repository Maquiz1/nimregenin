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
        if (Input::get('add_user')) {
            $staff = $override->getNews('user', 'status', 1, 'id', $_GET['staff_id']);
            if ($staff) {
                $validate = $validate->check($_POST, array(
                    'firstname' => array(
                        'required' => true,
                    ),
                    'middlename' => array(
                        'required' => true,
                    ),
                    'lastname' => array(
                        'required' => true,
                    ),
                    'position' => array(
                        'required' => true,
                    ),
                    'site_id' => array(
                        'required' => true,
                    ),
                ));
            } else {
                $validate = $validate->check($_POST, array(
                    'firstname' => array(
                        'required' => true,
                    ),
                    'middlename' => array(
                        'required' => true,
                    ),
                    'lastname' => array(
                        'required' => true,
                    ),
                    'position' => array(
                        'required' => true,
                    ),
                    'site_id' => array(
                        'required' => true,
                    ),
                    'username' => array(
                        'required' => true,
                        'unique' => 'user'
                    ),
                    'phone_number' => array(
                        'required' => true,
                        'unique' => 'user'
                    ),
                    'email_address' => array(
                        'unique' => 'user'
                    ),
                ));
            }
            if ($validate->passed()) {
                $salt = $random->get_rand_alphanumeric(32);
                $password = '12345678';
                switch (Input::get('position')) {
                    case 1:
                        $accessLevel = 1;
                        break;
                    case 2:
                        $accessLevel = 1;
                        break;
                    case 3:
                        $accessLevel = 2;
                        break;
                    case 4:
                        $accessLevel = 3;
                        break;
                    case 5:
                        $accessLevel = 3;
                        break;
                }
                try {

                    // $staff = $override->getNews('user', 'status', 1, 'id', $_GET['staff_id']);

                    if ($staff) {
                        $user->updateRecord('user', array(
                            'firstname' => Input::get('firstname'),
                            'middlename' => Input::get('middlename'),
                            'lastname' => Input::get('lastname'),
                            'username' => Input::get('username'),
                            'phone_number' => Input::get('phone_number'),
                            'phone_number2' => Input::get('phone_number2'),
                            'email_address' => Input::get('email_address'),
                            'sex' => Input::get('sex'),
                            'position' => Input::get('position'),
                            'accessLevel' => Input::get('accessLevel'),
                            'power' => Input::get('power'),
                            'site_id' => Input::get('site_id'),
                        ), $_GET['staff_id']);

                        $successMessage = 'Account Updated Successful';
                    } else {
                        $user->createRecord('user', array(
                            'firstname' => Input::get('firstname'),
                            'middlename' => Input::get('middlename'),
                            'lastname' => Input::get('lastname'),
                            'username' => Input::get('username'),
                            'phone_number' => Input::get('phone_number'),
                            'phone_number2' => Input::get('phone_number2'),
                            'email_address' => Input::get('email_address'),
                            'sex' => Input::get('sex'),
                            'position' => Input::get('position'),
                            'accessLevel' => $accessLevel,
                            'power' => Input::get('power'),
                            'password' => Hash::make($password, $salt),
                            'salt' => $salt,
                            'create_on' => date('Y-m-d'),
                            'last_login' => '',
                            'status' => 1,
                            'user_id' => $user->data()->id,
                            'site_id' => Input::get('site_id'),
                            'count' => 0,
                            'pswd' => 0,
                        ));
                        $successMessage = 'Account Created Successful';
                    }

                    Redirect::to('info.php?id=1&status=1');
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_positions')) {
            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $position = $override->getNews('position', 'status', 1, 'id', $_GET['position_id']);
                    if ($position) {
                        $user->updateRecord('position', array(
                            'name' => Input::get('name'),
                        ), $_GET['position_id']);
                        $successMessage = 'Position Successful Updated';
                    } else {
                        $user->createRecord('position', array(
                            'name' => Input::get('name'),
                            'access_level' => 1,
                            'status' => 1,
                        ));
                        $successMessage = 'Position Successful Added';
                    }
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_sites')) {
            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $site = $override->getNews('sites', 'status', 1, 'id', $_GET['site_id']);
                    if ($site) {
                        $user->updateRecord('sites', array(
                            'name' => Input::get('name'),
                            'update_on' => date('Y-m-d H:i:s'),
                            'update_id' => $user->data()->id,
                        ), $_GET['site_id']);
                        $successMessage = 'Site Successful Updated';
                    } else {
                        $user->createRecord('sites', array(
                            'name' => Input::get('name'),
                            'entry_date' => date('Y-m-d'),
                            'arm' => 1,
                            'level' => 1,
                            'type' => 1,
                            'category' => 1,
                            'respondent' => 2,
                            'region' => 1,
                            'district' => 1,
                            'ward' => 1,
                            'status' => 1,
                            'create_on' => date('Y-m-d H:i:s'),
                            'staff_id' => $user->data()->id,
                            'update_on' => date('Y-m-d H:i:s'),
                            'update_id' => $user->data()->id,
                        ));
                        $successMessage = 'Site Successful Added';
                    }
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_site')) {
            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $site = $override->getNews('sites', 'status', 1, 'id', $_GET['site_id']);
                    if ($site) {
                        $user->updateRecord('sites', array(
                            'name' => Input::get('name'),
                            'entry_date' => Input::get('entry_date'),
                            'arm' => Input::get('arm'),
                            'level' => Input::get('level'),
                            'type' => Input::get('type'),
                            'category' => Input::get('category'),
                            'respondent' => Input::get('respondent'),
                            'region' => Input::get('region'),
                            'district' => Input::get('district'),
                            'ward' => Input::get('ward'),
                            'update_on' => date('Y-m-d H:i:s'),
                            'update_id' => $user->data()->id,
                        ), $site[0]['id']);
                        $successMessage = 'Site Successful Updated';
                    } else {
                        $user->createRecord('sites', array(
                            'name' => Input::get('name'),
                            'entry_date' => Input::get('entry_date'),
                            'arm' => Input::get('arm'),
                            'level' => Input::get('level'),
                            'type' => Input::get('type'),
                            'category' => Input::get('category'),
                            'respondent' => Input::get('respondent'),
                            'region' => Input::get('region'),
                            'district' => Input::get('district'),
                            'ward' => Input::get('ward'),
                            'status' => 1,
                            'create_on' => date('Y-m-d H:i:s'),
                            'staff_id' => $user->data()->id,
                            'update_on' => date('Y-m-d H:i:s'),
                            'update_id' => $user->data()->id,
                        ));
                        $successMessage = 'Site Successful Added';
                    }

                    // $user->visit_delete1($_GET['site_id'], Input::get('visit_date'), $_GET['site_id'], $user->data()->id, $_GET['site_id'], $eligible, $sequence, $visit_code, $visit_name);

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
            ));

            if ($validate->passed()) {
                $screening = $override->getNews('screening', 'status', 1, 'id', $_GET['sid'])[0];
                $eligible = 0;
                if (
                    (Input::get('consent') == 1 && Input::get('age18years') == 1 && Input::get('produce_resp_sample') == 1 &&
                        (Input::get('present_symptoms') == 1) && Input::get('unable_understand') == 2 && Input::get('not_willing') == 2)
                ) {
                    $eligible = 1;
                }

                $date_completed = "";
                $completed_by = "";
                $date_verified = "";
                $verified_by = "";

                if (
                    (Input::get('form_status') == 1)
                ) {
                    $date_completed = "";
                    $completed_by = "";
                    $date_verified = "";
                    $verified_by = "";
                } elseif (Input::get('form_status') == 2) {
                    $date_completed = Input::get('date_completed');
                    $completed_by = $user->data()->id;
                    $date_verified = "";
                    $verified_by = "";
                } elseif (Input::get('form_status') == 3) {
                    $date_completed = $screening['date_completed'];
                    $completed_by = $screening['completed_by'];
                    $date_verified = Input::get('date_completed');
                    $verified_by = $user->data()->id;
                }

                $pid = $override->getNews('pids', 'facility_id', $user->data()->site_id, 'status', 1)[0];
                $pid_merged = $pid['pid'] . '_' . Input::get('pid1');

                // if (Input::get('consent') == 1 && (Input::get('consent_date') < $screening['screening_date'])) {
                //     $errorMessage = 'Consent Date Can not be less than Screening Date';
                // } elseif (Input::get('consent') == 2 && !empty(trim(Input::get('consent_date')))) {
                //     $errorMessage = 'Please Remove Consent date before Submit again';
                // } elseif ((Input::get('pid1') != Input::get('pid2')) && !empty(trim(Input::get('pid1')))) {
                //     $errorMessage = 'PID"s are not Matching please re-check and Submit again';
                // } else {
                if ($screening) {
                    $user->updateRecord('screening', array(
                        'pid' => $pid_merged,
                        'screening_date' => Input::get('screening_date'),
                        'consent' => Input::get('consent'),
                        'consent_date' => Input::get('consent_date'),
                        'age18years' => Input::get('age18years'),
                        'present_symptoms' => Input::get('present_symptoms'),
                        'produce_resp_sample' => Input::get('produce_resp_sample'),
                        'pid1' => Input::get('pid1'),
                        'pid2' => Input::get('pid2'),
                        'unable_understand' => Input::get('unable_understand'),
                        'not_willing' => Input::get('not_willing'),
                        'remarks' => Input::get('remarks'),
                        'form_status' => Input::get('form_status'),
                        'date_completed' => $date_completed,
                        'completed_by' => $completed_by,
                        'date_verified' => $date_verified,
                        'verified_by' => $verified_by,
                        'eligible' => $eligible,
                        'update_on' => date('Y-m-d H:i:s'),
                        'update_id' => $user->data()->id,
                        'facility_id' => $screening['facility_id'],
                    ), $screening['id']);

                    $user->createRecord('screening_records', array(
                        'screening_id' => $screening['id'],
                        'pid' => $pid_merged,
                        'screening_date' => Input::get('screening_date'),
                        'consent' => Input::get('consent'),
                        'consent_date' => Input::get('consent_date'),
                        'age18years' => Input::get('age18years'),
                        'present_symptoms' => Input::get('present_symptoms'),
                        'produce_resp_sample' => Input::get('produce_resp_sample'),
                        'pid1' => Input::get('pid1'),
                        'pid2' => Input::get('pid2'),
                        'unable_understand' => Input::get('unable_understand'),
                        'not_willing' => Input::get('not_willing'),
                        'remarks' => Input::get('remarks'),
                        'form_status' => Input::get('form_status'),
                        'date_completed' => $date_completed,
                        'completed_by' => $completed_by,
                        'date_verified' => $date_verified,
                        'verified_by' => $verified_by,
                        'eligible' => $eligible,
                        'status' => 1,
                        'create_on' => date('Y-m-d H:i:s'),
                        'staff_id' => $user->data()->id,
                        'update_on' => date('Y-m-d H:i:s'),
                        'update_id' => $user->data()->id,
                        'facility_id' => $screening['facility_id'],
                    ));

                    $successMessage = 'Screening  Successful Updated';
                } else {
                    if ($screening['pid'] == $pid_merged) {
                        $errorMessage = 'PID"s Exists Please use Another';
                    } else {
                        $user->createRecord('screening', array(
                            'pid' => $pid_merged,
                            'screening_date' => Input::get('screening_date'),
                            'consent' => Input::get('consent'),
                            'consent_date' => Input::get('consent_date'),
                            'age18years' => Input::get('age18years'),
                            'present_symptoms' => Input::get('present_symptoms'),
                            'produce_resp_sample' => Input::get('produce_resp_sample'),
                            'pid1' => Input::get('pid1'),
                            'pid2' => Input::get('pid2'),
                            'unable_understand' => Input::get('unable_understand'),
                            'not_willing' => Input::get('not_willing'),
                            'remarks' => Input::get('remarks'),
                            'form_status' => Input::get('form_status'),
                            'date_completed' => $date_completed,
                            'completed_by' => $completed_by,
                            'date_verified' => $date_verified,
                            'verified_by' => $verified_by,
                            'eligible' => $eligible,
                            'status' => 1,
                            'create_on' => date('Y-m-d H:i:s'),
                            'staff_id' => $user->data()->id,
                            'update_on' => date('Y-m-d H:i:s'),
                            'update_id' => $user->data()->id,
                            'facility_id' => $user->data()->site_id,
                        ));

                        $last_row = $override->lastRow('screening', 'id')[0];

                        $user->createRecord('screening_records', array(
                            'screening_id' => $last_row['id'],
                            'pid' => $pid_merged,
                            'screening_date' => Input::get('screening_date'),
                            'consent' => Input::get('consent'),
                            'consent_date' => Input::get('consent_date'),
                            'age18years' => Input::get('age18years'),
                            'present_symptoms' => Input::get('present_symptoms'),
                            'produce_resp_sample' => Input::get('produce_resp_sample'),
                            'pid1' => Input::get('pid1'),
                            'pid2' => Input::get('pid2'),
                            'unable_understand' => Input::get('unable_understand'),
                            'not_willing' => Input::get('not_willing'),
                            'remarks' => Input::get('remarks'),
                            'form_status' => Input::get('form_status'),
                            'date_completed' => $date_completed,
                            'completed_by' => $completed_by,
                            'date_verified' => $date_verified,
                            'verified_by' => $verified_by,
                            'eligible' => $eligible,
                            'status' => 1,
                            'create_on' => date('Y-m-d H:i:s'),
                            'staff_id' => $user->data()->id,
                            'update_on' => date('Y-m-d H:i:s'),
                            'update_id' => $user->data()->id,
                            'facility_id' => $screening['facility_id'],
                        ));

                        $successMessage = 'Screening  Successful Added';
                    }
                }
                Redirect::to('info.php?id=3&status=' . $_GET['status'] . '&facility_id=' . $_GET['facility_id'] . '&page=' . $_GET['page'] . '&msg=' . $successMessage);
                // }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_enrollment_form')) {
            $validate = $validate->check($_POST, array(
                'enrollment_date' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $screening = $override->getNews('screening', 'status', 1, 'id', $_GET['sid'])[0];
                    $enrollment_form = $override->getNews('enrollment_form', 'status', 1, 'enrollment_id', $_GET['sid'])[0];
                    $diseases_medical = implode(',', Input::get('diseases_medical'));
                    $immunosuppressive_diseases = implode(',', Input::get('immunosuppressive_diseases'));
                    $sputum_samples = implode(',', Input::get('sputum_samples'));


                    $date_completed = "";
                    $completed_by = "";
                    $date_verified = "";
                    $verified_by = "";

                    if (
                        (Input::get('form_status') == 1)
                    ) {
                        $date_completed = "";
                        $completed_by = "";
                        $date_verified = "";
                        $verified_by = "";
                    } elseif (Input::get('form_status') == 2) {
                        $date_completed = Input::get('date_completed');
                        $completed_by = $user->data()->id;
                        $date_verified = "";
                        $verified_by = "";
                    } elseif (Input::get('form_status') == 3) {
                        $date_completed = $screening['date_completed'];
                        $completed_by = $screening['completed_by'];
                        $date_verified = Input::get('date_completed');
                        $verified_by = $user->data()->id;
                    }


                    if (Input::get('enrollment_completed') == 3 && Input::get('enrollment_verified_date') == "") {
                        $errorMessage = 'You do not have Permissions to Verify this form pleae you can only "Complete Form "';
                    } else {
                        if ($enrollment_form) {
                            $user->updateRecord('enrollment_form', array(
                                'enrollment_date' => Input::get('enrollment_date'),
                                'dob' => Input::get('dob'),
                                'age' => Input::get('age'),
                                'sex' => Input::get('sex'),
                                'region' => Input::get('region'),
                                'district' => Input::get('district'),
                                'ward' => Input::get('ward'),
                                'village_street' => Input::get('village_street'),
                                'cough2weeks' => Input::get('cough2weeks'),
                                'poor_weight' => Input::get('poor_weight'),
                                'coughing_blood' => Input::get('coughing_blood'),
                                'unexplained_fever' => Input::get('unexplained_fever'),
                                'night_sweats' => Input::get('night_sweats'),
                                'neck_lymph' => Input::get('neck_lymph'),
                                'date_information_collected' => Input::get('date_information_collected'),
                                'history_tb' => Input::get('history_tb'),
                                'tx_previous' => Input::get('tx_previous'),
                                'tx_month' => Input::get('tx_month'),
                                'tx_unknown_month' => Input::get('tx_unknown_month'),
                                'tx_year' => Input::get('tx_year'),
                                'tx_unknown_year' => Input::get('tx_unknown_year'),
                                'dr_ds' => Input::get('dr_ds'),
                                'tb_category' => Input::get('tb_category'),
                                'tb_category_specify' => Input::get('tb_category_specify'),
                                'relapse_years' => Input::get('relapse_years'),
                                'ltf_months' => Input::get('ltf_months'),
                                'ltf_months_unknown' => Input::get('ltf_months_unknown'),
                                'tb_regimen' => Input::get('tb_regimen'),
                                'tb_regimen_specify' => Input::get('tb_regimen_specify'),
                                'regimen_months' => Input::get('regimen_months'),
                                'regimen_months_unknown' => Input::get('regimen_months_unknown'),
                                'tb_otcome' => Input::get('tb_otcome'),
                                'hiv_status' => Input::get('hiv_status'),
                                'other_diseases' => Input::get('other_diseases'),
                                'diseases_medical' => $diseases_medical,
                                'diseases_specify' => Input::get('diseases_specify'),
                                'sputum_collected' => Input::get('sputum_collected'),
                                'sputum_date' => Input::get('sputum_date'),
                                'sputum_reasons' => Input::get('sputum_reasons'),
                                'remarks' => Input::get('remarks'),
                                'form_status' => Input::get('form_status'),
                                'date_completed' => $date_completed,
                                'completed_by' => $completed_by,
                                'date_verified' => $date_verified,
                                'verified_by' => $verified_by,
                                'update_on' => date('Y-m-d H:i:s'),
                                'update_id' => $user->data()->id,
                            ), $enrollment_form['id']);

                            $user->createRecord('enrollment_form_records', array(
                                'enroll_id' => $enrollment_form['id'],
                                'enrollment_id' => $_GET['sid'],
                                'pid' => $screening['pid'],
                                'enrollment_date' => Input::get('enrollment_date'),
                                'dob' => Input::get('dob'),
                                'age' => Input::get('age'),
                                'sex' => Input::get('sex'),
                                'region' => Input::get('region'),
                                'district' => Input::get('district'),
                                'ward' => Input::get('ward'),
                                'village_street' => Input::get('village_street'),
                                'cough2weeks' => Input::get('cough2weeks'),
                                'poor_weight' => Input::get('poor_weight'),
                                'coughing_blood' => Input::get('coughing_blood'),
                                'unexplained_fever' => Input::get('unexplained_fever'),
                                'night_sweats' => Input::get('night_sweats'),
                                'neck_lymph' => Input::get('neck_lymph'),
                                'date_information_collected' => Input::get('date_information_collected'),
                                'history_tb' => Input::get('history_tb'),
                                'tx_previous' => Input::get('tx_previous'),
                                'tx_month' => Input::get('tx_month'),
                                'tx_unknown_month' => Input::get('tx_unknown_month'),
                                'tx_year' => Input::get('tx_year'),
                                'tx_unknown_year' => Input::get('tx_unknown_year'),
                                'dr_ds' => Input::get('dr_ds'),
                                'tb_category' => Input::get('tb_category'),
                                'tb_category_specify' => Input::get('tb_category_specify'),
                                'relapse_years' => Input::get('relapse_years'),
                                'ltf_months' => Input::get('ltf_months'),
                                'tb_regimen' => Input::get('tb_regimen'),
                                'tb_regimen_specify' => Input::get('tb_regimen_specify'),
                                'regimen_months' => Input::get('regimen_months'),
                                'tb_otcome' => Input::get('tb_otcome'),
                                'hiv_status' => Input::get('hiv_status'),
                                'other_diseases' => Input::get('other_diseases'),
                                'diseases_medical' => $diseases_medical,
                                'diseases_specify' => Input::get('diseases_specify'),
                                'sputum_collected' => Input::get('sputum_collected'),
                                'sputum_date' => Input::get('sputum_date'),
                                'sputum_reasons' => Input::get('sputum_reasons'),
                                'remarks' => Input::get('remarks'),
                                'form_status' => Input::get('form_status'),
                                'date_completed' => $date_completed,
                                'completed_by' => $completed_by,
                                'date_verified' => $date_verified,
                                'verified_by' => $verified_by,
                                'status' => 1,
                                'create_on' => date('Y-m-d H:i:s'),
                                'staff_id' => $user->data()->id,
                                'update_on' => date('Y-m-d H:i:s'),
                                'update_id' => $user->data()->id,
                                'facility_id' => $screening['facility_id'],
                            ));

                            $successMessage = 'Enrollment Form Updated Successful';
                        } else {

                            $user->createRecord('enrollment_form', array(
                                'enrollment_id' => $_GET['sid'],
                                'pid' => $screening['pid'],
                                'enrollment_date' => Input::get('enrollment_date'),
                                'dob' => Input::get('dob'),
                                'age' => Input::get('age'),
                                'sex' => Input::get('sex'),
                                'region' => Input::get('region'),
                                'district' => Input::get('district'),
                                'ward' => Input::get('ward'),
                                'village_street' => Input::get('village_street'),
                                'cough2weeks' => Input::get('cough2weeks'),
                                'poor_weight' => Input::get('poor_weight'),
                                'coughing_blood' => Input::get('coughing_blood'),
                                'unexplained_fever' => Input::get('unexplained_fever'),
                                'night_sweats' => Input::get('night_sweats'),
                                'neck_lymph' => Input::get('neck_lymph'),
                                'date_information_collected' => Input::get('date_information_collected'),
                                'history_tb' => Input::get('history_tb'),
                                'tx_previous' => Input::get('tx_previous'),
                                'tx_month' => Input::get('tx_month'),
                                'tx_unknown_month' => Input::get('tx_unknown_month'),
                                'tx_year' => Input::get('tx_year'),
                                'tx_unknown_year' => Input::get('tx_unknown_year'),
                                'dr_ds' => Input::get('dr_ds'),
                                'tb_category' => Input::get('tb_category'),
                                'tb_category_specify' => Input::get('tb_category_specify'),
                                'relapse_years' => Input::get('relapse_years'),
                                'ltf_months' => Input::get('ltf_months'),
                                'tb_regimen' => Input::get('tb_regimen'),
                                'tb_regimen_specify' => Input::get('tb_regimen_specify'),
                                'regimen_months' => Input::get('regimen_months'),
                                'tb_otcome' => Input::get('tb_otcome'),
                                'hiv_status' => Input::get('hiv_status'),
                                'other_diseases' => Input::get('other_diseases'),
                                'diseases_medical' => $diseases_medical,
                                'diseases_specify' => Input::get('diseases_specify'),
                                'sputum_collected' => Input::get('sputum_collected'),
                                'sputum_date' => Input::get('sputum_date'),
                                'sputum_reasons' => Input::get('sputum_reasons'),
                                'remarks' => Input::get('remarks'),
                                'form_status' => Input::get('form_status'),
                                'date_completed' => $date_completed,
                                'completed_by' => $completed_by,
                                'date_verified' => $date_verified,
                                'verified_by' => $verified_by,
                                'status' => 1,
                                'create_on' => date('Y-m-d H:i:s'),
                                'staff_id' => $user->data()->id,
                                'update_on' => date('Y-m-d H:i:s'),
                                'update_id' => $user->data()->id,
                                'facility_id' => $screening['facility_id'],
                            ));

                            $last_row = $override->lastRow('enrollment_form', 'id')[0];

                            $user->createRecord('enrollment_form_records', array(
                                'enroll_id' => $last_row['id'],
                                'enrollment_id' => $_GET['sid'],
                                'pid' => $screening['pid'],
                                'enrollment_date' => Input::get('enrollment_date'),
                                'dob' => Input::get('dob'),
                                'age' => Input::get('age'),
                                'sex' => Input::get('sex'),
                                'region' => Input::get('region'),
                                'district' => Input::get('district'),
                                'ward' => Input::get('ward'),
                                'village_street' => Input::get('village_street'),
                                'cough2weeks' => Input::get('cough2weeks'),
                                'poor_weight' => Input::get('poor_weight'),
                                'coughing_blood' => Input::get('coughing_blood'),
                                'unexplained_fever' => Input::get('unexplained_fever'),
                                'night_sweats' => Input::get('night_sweats'),
                                'neck_lymph' => Input::get('neck_lymph'),
                                'date_information_collected' => Input::get('date_information_collected'),
                                'history_tb' => Input::get('history_tb'),
                                'tx_previous' => Input::get('tx_previous'),
                                'tx_month' => Input::get('tx_month'),
                                'tx_unknown_month' => Input::get('tx_unknown_month'),
                                'tx_year' => Input::get('tx_year'),
                                'tx_unknown_year' => Input::get('tx_unknown_year'),
                                'dr_ds' => Input::get('dr_ds'),
                                'tb_category' => Input::get('tb_category'),
                                'tb_category_specify' => Input::get('tb_category_specify'),
                                'relapse_years' => Input::get('relapse_years'),
                                'ltf_months' => Input::get('ltf_months'),
                                'tb_regimen' => Input::get('tb_regimen'),
                                'tb_regimen_specify' => Input::get('tb_regimen_specify'),
                                'regimen_months' => Input::get('regimen_months'),
                                'tb_otcome' => Input::get('tb_otcome'),
                                'hiv_status' => Input::get('hiv_status'),
                                'other_diseases' => Input::get('other_diseases'),
                                'diseases_medical' => $diseases_medical,
                                'diseases_specify' => Input::get('diseases_specify'),
                                'sputum_collected' => Input::get('sputum_collected'),
                                'sputum_date' => Input::get('sputum_date'),
                                'sputum_reasons' => Input::get('sputum_reasons'),
                                'remarks' => Input::get('remarks'),
                                'form_status' => Input::get('form_status'),
                                'date_completed' => $date_completed,
                                'completed_by' => $completed_by,
                                'date_verified' => $date_verified,
                                'verified_by' => $verified_by,
                                'status' => 1,
                                'create_on' => date('Y-m-d H:i:s'),
                                'staff_id' => $user->data()->id,
                                'update_on' => date('Y-m-d H:i:s'),
                                'update_id' => $user->data()->id,
                                'facility_id' => $screening['facility_id'],
                            ));

                            $successMessage = 'Enrollment Form  Added Successful';
                        }
                        Redirect::to('info.php?id=6&status=' . $_GET['status'] . '&sid=' . $_GET['sid'] . '&facility_id=' . $_GET['facility_id'] . '&page=' . $_GET['page'] . '&msg=' . $successMessage);
                    }

                } catch (Exception $e) {
                    die($e->getMessage());
                }
            }
        } elseif (Input::get('add_diagnosis_test')) {
            $validate = $validate->check($_POST, array(
                'culture_performed' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                $screening = $override->getNews('screening', 'status', 1, 'id', $_GET['sid'])[0];
                $individual = $override->getNews('diagnosis_test', 'status', 1, 'enrollment_id', $_GET['sid']);
                $first_line = 0;
                $second_line = 0;
                $third_line = 0;
                $sequence = '';
                $visit_code = '';
                $visit_name = '';

                if (Input::get('first_line')) {
                    $first_line = Input::get('first_line');
                }

                if (Input::get('second_line')) {
                    $second_line = Input::get('second_line');
                }

                if (Input::get('third_line')) {
                    $third_line = Input::get('third_line');
                }
                $enrolled = 0;
                $end_study = 0;

                $first_line_drugs = implode(',', Input::get('first_line_drugs'));
                $second_line_drugs = implode(',', Input::get('second_line_drugs'));
                $culture_method = implode(',', Input::get('culture_method'));


                $date_completed = "";
                $completed_by = "";
                $date_verified = "";
                $verified_by = "";

                if (
                    (Input::get('form_status') == 1)
                ) {
                    $date_completed = "";
                    $completed_by = "";
                    $date_verified = "";
                    $verified_by = "";
                } elseif (Input::get('form_status') == 2) {
                    $date_completed = Input::get('date_completed');
                    $completed_by = $user->data()->id;
                    $date_verified = "";
                    $verified_by = "";
                } elseif (Input::get('form_status') == 3) {
                    $date_completed = $screening['date_completed'];
                    $completed_by = $screening['completed_by'];
                    $date_verified = Input::get('date_completed');
                    $verified_by = $user->data()->id;
                }

                if (Input::get('form_completness') == 3 && Input::get('diagnosis_test_verified_date') == "") {
                    $errorMessage = 'You do not have Permissions to Verify this form pleae you can only "Complete Form "';
                } else {
                    if ($individual) {
                        $user->updateRecord('diagnosis_test', array(
                            'culture_performed' => Input::get('culture_performed'),
                            'culture_method' => $culture_method,
                            'culture_results' => Input::get('culture_results'),
                            'phenotypic_performed' => Input::get('phenotypic_performed'),
                            'phenotypic_date_performed' => Input::get('phenotypic_date_performed'),
                            'phenotypic_date_results' => Input::get('phenotypic_date_results'),
                            'date_sputum_received' => Input::get('date_sputum_received'),
                            'appearance' => Input::get('appearance'),
                            'sample_volume' => Input::get('sample_volume'),
                            'unique_lab_no' => Input::get('unique_lab_no'),
                            'microscopy_type' => Input::get('microscopy_type'),
                            'microscopy_results' => Input::get('microscopy_results'),
                            'microscopy_date' => Input::get('microscopy_date'),
                            'lj_inoculation_date' => Input::get('lj_inoculation_date'),
                            'lj_results_date' => Input::get('lj_results_date'),
                            'lj_results' => Input::get('lj_results'),
                            'mgit_inoculation_date' => Input::get('mgit_inoculation_date'),
                            'mgit_results_date' => Input::get('mgit_results_date'),
                            'mgit_results' => Input::get('mgit_results'),
                            'culture_isolate' => Input::get('culture_isolate'),
                            'isolate_date' => Input::get('isolate_date'),
                            'rifampicin' => Input::get('rifampicin'),
                            'isoniazid' => Input::get('isoniazid'),
                            'levofloxacin' => Input::get('levofloxacin'),
                            'moxifloxacin' => Input::get('moxifloxacin'),
                            'bedaquiline' => Input::get('bedaquiline'),
                            'linezolid' => Input::get('linezolid'),
                            'clofazimine' => Input::get('clofazimine'),
                            'cycloserine' => Input::get('cycloserine'),
                            'terizidone' => Input::get('terizidone'),
                            'ethambutol' => Input::get('ethambutol'),
                            'delamanid' => Input::get('delamanid'),
                            'pyrazinamide' => Input::get('pyrazinamide'),
                            'imipenem' => Input::get('imipenem'),
                            'cilastatin' => Input::get('cilastatin'),
                            'meropenem' => Input::get('meropenem'),
                            'amikacin' => Input::get('amikacin'),
                            'streptomycin' => Input::get('streptomycin'),
                            'ethionamide' => Input::get('ethionamide'),
                            'prothionamide' => Input::get('prothionamide'),
                            'para_aminosalicylic_acid' => Input::get('para_aminosalicylic_acid'),
                            'nano_rifampicin' => Input::get('nano_rifampicin'),
                            'nano_isoniazid' => Input::get('nano_isoniazid'),
                            'nano_levofloxacin' => Input::get('nano_levofloxacin'),
                            'nano_moxifloxacin' => Input::get('nano_moxifloxacin'),
                            'nano_bedaquiline' => Input::get('nano_bedaquiline'),
                            'nano_linezolid' => Input::get('nano_linezolid'),
                            'nano_clofazimine' => Input::get('nano_clofazimine'),
                            'nano_cycloserine' => Input::get('nano_cycloserine'),
                            'nano_delamanid' => Input::get('nano_delamanid'),
                            'nano_terizidone' => Input::get('nano_terizidone'),
                            'nano_ethambutol' => Input::get('nano_ethambutol'),
                            'nano_pyrazinamide' => Input::get('nano_pyrazinamide'),
                            'nano_cilastatin' => Input::get('nano_cilastatin'),
                            'nano_imipenem' => Input::get('nano_imipenem'),
                            'nano_meropenem' => Input::get('nano_meropenem'),
                            'nano_amikacin' => Input::get('nano_amikacin'),
                            'nano_streptomycin' => Input::get('nano_streptomycin'),
                            'nano_ethionamide' => Input::get('nano_ethionamide'),
                            'nano_prothionamide' => Input::get('nano_prothionamide'),
                            'nano_para_aminosalicylic_acid' => Input::get('nano_para_aminosalicylic_acid'),
                            'nano_capreomycin' => Input::get('nano_capreomycin'),
                            'nano_kanamycin' => Input::get('nano_kanamycin'),
                            'nano_pretomanid' => Input::get('nano_pretomanid'),
                            'xpert_xdr_performed' => Input::get('xpert_xdr_performed'),
                            'xpert_xdr_date_performed' => Input::get('xpert_xdr_date_performed'),
                            'isoniazid2' => Input::get('isoniazid2'),
                            'fluoroquinolones' => Input::get('fluoroquinolones'),
                            'amikacin2' => Input::get('amikacin2'),
                            'kanamycin' => Input::get('kanamycin'),
                            'capreomycin' => Input::get('capreomycin'),
                            'ethionamide2' => Input::get('ethionamide2'),
                            'first_line_drugs' => $first_line_drugs,
                            'second_line_drugs' => $second_line_drugs,
                            'version_number' => Input::get('version_number'),
                            'lot_number' => Input::get('lot_number'),
                            'mutations_detected_list' => Input::get('mutations_detected_list'),
                            'nanopore_done' => Input::get('nanopore_done'),
                            'sequencing_results' => Input::get('sequencing_results'),
                            'EPI2ME' => Input::get('EPI2ME'),
                            'EPI2ME_vesrion' => Input::get('EPI2ME_vesrion'),
                            'first_line_lpa' => Input::get('first_line_lpa'),
                            'first_line_lpa_date' => Input::get('first_line_lpa_date'),
                            'second_line_lpa' => Input::get('second_line_lpa'),
                            'second_line_lpa_date' => Input::get('second_line_lpa_date'),
                            'lpa1_mtb' => Input::get('lpa1_mtb'),
                            'lpa1_rif' => Input::get('lpa1_rif'),
                            'lpa1_inh' => Input::get('lpa1_inh'),
                            'lpa2_mtb' => Input::get('lpa2_mtb'),
                            'lpa2_rfluoroquinolones' => Input::get('lpa2_rfluoroquinolones'),
                            'lpa2_aminoglycosides' => Input::get('lpa2_aminoglycosides'),
                            'lpa2_kanamycin' => Input::get('lpa2_kanamycin'),
                            'remarks' => Input::get('remarks'),
                            'form_status' => Input::get('form_status'),
                            'date_completed' => $date_completed,
                            'completed_by' => $completed_by,
                            'date_verified' => $date_verified,
                            'verified_by' => $verified_by,
                            'update_on' => date('Y-m-d H:i:s'),
                            'update_id' => $user->data()->id,
                            'facility_id' => $screening['facility_id'],
                        ), $individual[0]['id']);

                        $user->createRecord('diagnosis_test_records', array(
                            'diagnosis_test_id' => $individual[0]['id'],
                            'pid' => $screening['pid'],
                            'culture_performed' => Input::get('culture_performed'),
                            'culture_method' => $culture_method,
                            'culture_results' => Input::get('culture_results'),
                            'phenotypic_performed' => Input::get('phenotypic_performed'),
                            'phenotypic_date_performed' => Input::get('phenotypic_date_performed'),
                            'phenotypic_date_results' => Input::get('phenotypic_date_results'),
                            'date_sputum_received' => Input::get('date_sputum_received'),
                            'appearance' => Input::get('appearance'),
                            'sample_volume' => Input::get('sample_volume'),
                            'unique_lab_no' => Input::get('unique_lab_no'),
                            'microscopy_type' => Input::get('microscopy_type'),
                            'microscopy_results' => Input::get('microscopy_results'),
                            'microscopy_date' => Input::get('microscopy_date'),
                            'lj_inoculation_date' => Input::get('lj_inoculation_date'),
                            'lj_results_date' => Input::get('lj_results_date'),
                            'lj_results' => Input::get('lj_results'),
                            'mgit_inoculation_date' => Input::get('mgit_inoculation_date'),
                            'mgit_results_date' => Input::get('mgit_results_date'),
                            'mgit_results' => Input::get('mgit_results'),
                            'culture_isolate' => Input::get('culture_isolate'),
                            'isolate_date' => Input::get('isolate_date'),
                            'rifampicin' => Input::get('rifampicin'),
                            'isoniazid' => Input::get('isoniazid'),
                            'levofloxacin' => Input::get('levofloxacin'),
                            'moxifloxacin' => Input::get('moxifloxacin'),
                            'bedaquiline' => Input::get('bedaquiline'),
                            'linezolid' => Input::get('linezolid'),
                            'clofazimine' => Input::get('clofazimine'),
                            'cycloserine' => Input::get('cycloserine'),
                            'terizidone' => Input::get('terizidone'),
                            'ethambutol' => Input::get('ethambutol'),
                            'delamanid' => Input::get('delamanid'),
                            'pyrazinamide' => Input::get('pyrazinamide'),
                            'imipenem' => Input::get('imipenem'),
                            'cilastatin' => Input::get('cilastatin'),
                            'meropenem' => Input::get('meropenem'),
                            'amikacin' => Input::get('amikacin'),
                            'streptomycin' => Input::get('streptomycin'),
                            'ethionamide' => Input::get('ethionamide'),
                            'prothionamide' => Input::get('prothionamide'),
                            'para_aminosalicylic_acid' => Input::get('para_aminosalicylic_acid'),
                            'nano_rifampicin' => Input::get('nano_rifampicin'),
                            'nano_isoniazid' => Input::get('nano_isoniazid'),
                            'nano_levofloxacin' => Input::get('nano_levofloxacin'),
                            'nano_moxifloxacin' => Input::get('nano_moxifloxacin'),
                            'nano_bedaquiline' => Input::get('nano_bedaquiline'),
                            'nano_linezolid' => Input::get('nano_linezolid'),
                            'nano_clofazimine' => Input::get('nano_clofazimine'),
                            'nano_cycloserine' => Input::get('nano_cycloserine'),
                            'nano_delamanid' => Input::get('nano_delamanid'),
                            'nano_terizidone' => Input::get('nano_terizidone'),
                            'nano_ethambutol' => Input::get('nano_ethambutol'),
                            'nano_pyrazinamide' => Input::get('nano_pyrazinamide'),
                            'nano_cilastatin' => Input::get('nano_cilastatin'),
                            'nano_imipenem' => Input::get('nano_imipenem'),
                            'nano_meropenem' => Input::get('nano_meropenem'),
                            'nano_amikacin' => Input::get('nano_amikacin'),
                            'nano_streptomycin' => Input::get('nano_streptomycin'),
                            'nano_ethionamide' => Input::get('nano_ethionamide'),
                            'nano_prothionamide' => Input::get('nano_prothionamide'),
                            'nano_para_aminosalicylic_acid' => Input::get('nano_para_aminosalicylic_acid'),
                            'nano_capreomycin' => Input::get('nano_capreomycin'),
                            'nano_kanamycin' => Input::get('nano_kanamycin'),
                            'nano_pretomanid' => Input::get('nano_pretomanid'),
                            'xpert_xdr_performed' => Input::get('xpert_xdr_performed'),
                            'xpert_xdr_date_performed' => Input::get('xpert_xdr_date_performed'),
                            'isoniazid2' => Input::get('isoniazid2'),
                            'fluoroquinolones' => Input::get('fluoroquinolones'),
                            'amikacin2' => Input::get('amikacin2'),
                            'kanamycin' => Input::get('kanamycin'),
                            'capreomycin' => Input::get('capreomycin'),
                            'ethionamide2' => Input::get('ethionamide2'),
                            'first_line_drugs' => $first_line_drugs,
                            'second_line_drugs' => $second_line_drugs,
                            'version_number' => Input::get('version_number'),
                            'lot_number' => Input::get('lot_number'),
                            'mutations_detected_list' => Input::get('mutations_detected_list'),
                            'nanopore_done' => Input::get('nanopore_done'),
                            'sequencing_results' => Input::get('sequencing_results'),
                            'EPI2ME' => Input::get('EPI2ME'),
                            'EPI2ME_vesrion' => Input::get('EPI2ME_vesrion'),
                            'first_line_lpa' => Input::get('first_line_lpa'),
                            'first_line_lpa_date' => Input::get('first_line_lpa_date'),
                            'second_line_lpa' => Input::get('second_line_lpa'),
                            'second_line_lpa_date' => Input::get('second_line_lpa_date'),
                            'lpa1_mtb' => Input::get('lpa1_mtb'),
                            'lpa1_rif' => Input::get('lpa1_rif'),
                            'lpa1_inh' => Input::get('lpa1_inh'),
                            'lpa2_mtb' => Input::get('lpa2_mtb'),
                            'lpa2_rfluoroquinolones' => Input::get('lpa2_rfluoroquinolones'),
                            'lpa2_aminoglycosides' => Input::get('lpa2_aminoglycosides'),
                            'lpa2_kanamycin' => Input::get('lpa2_kanamycin'),
                            'remarks' => Input::get('remarks'),
                            'form_status' => Input::get('form_status'),
                            'date_completed' => $date_completed,
                            'completed_by' => $completed_by,
                            'date_verified' => $date_verified,
                            'verified_by' => $verified_by,
                            'status' => 1,
                            'enrollment_id' => $_GET['sid'],
                            'create_on' => date('Y-m-d H:i:s'),
                            'staff_id' => $user->data()->id,
                            'update_on' => date('Y-m-d H:i:s'),
                            'update_id' => $user->data()->id,
                            'facility_id' => $screening['facility_id'],
                        ));

                        $successMessage = 'Diagnosis test  Successful Updated';
                    } else {
                        $user->createRecord('diagnosis_test', array(
                            'pid' => $screening['pid'],
                            'culture_performed' => Input::get('culture_performed'),
                            'culture_method' => $culture_method,
                            'culture_results' => Input::get('culture_results'),
                            'phenotypic_performed' => Input::get('phenotypic_performed'),
                            'phenotypic_date_performed' => Input::get('phenotypic_date_performed'),
                            'phenotypic_date_results' => Input::get('phenotypic_date_results'),
                            'date_sputum_received' => Input::get('date_sputum_received'),
                            'appearance' => Input::get('appearance'),
                            'sample_volume' => Input::get('sample_volume'),
                            'unique_lab_no' => Input::get('unique_lab_no'),
                            'microscopy_type' => Input::get('microscopy_type'),
                            'microscopy_results' => Input::get('microscopy_results'),
                            'microscopy_date' => Input::get('microscopy_date'),
                            'lj_inoculation_date' => Input::get('lj_inoculation_date'),
                            'lj_results_date' => Input::get('lj_results_date'),
                            'lj_results' => Input::get('lj_results'),
                            'mgit_inoculation_date' => Input::get('mgit_inoculation_date'),
                            'mgit_results_date' => Input::get('mgit_results_date'),
                            'mgit_results' => Input::get('mgit_results'),
                            'culture_isolate' => Input::get('culture_isolate'),
                            'isolate_date' => Input::get('isolate_date'),
                            'rifampicin' => Input::get('rifampicin'),
                            'isoniazid' => Input::get('isoniazid'),
                            'levofloxacin' => Input::get('levofloxacin'),
                            'moxifloxacin' => Input::get('moxifloxacin'),
                            'bedaquiline' => Input::get('bedaquiline'),
                            'linezolid' => Input::get('linezolid'),
                            'clofazimine' => Input::get('clofazimine'),
                            'cycloserine' => Input::get('cycloserine'),
                            'terizidone' => Input::get('terizidone'),
                            'ethambutol' => Input::get('ethambutol'),
                            'delamanid' => Input::get('delamanid'),
                            'pyrazinamide' => Input::get('pyrazinamide'),
                            'imipenem' => Input::get('imipenem'),
                            'cilastatin' => Input::get('cilastatin'),
                            'meropenem' => Input::get('meropenem'),
                            'amikacin' => Input::get('amikacin'),
                            'streptomycin' => Input::get('streptomycin'),
                            'ethionamide' => Input::get('ethionamide'),
                            'prothionamide' => Input::get('prothionamide'),
                            'para_aminosalicylic_acid' => Input::get('para_aminosalicylic_acid'),
                            'nano_rifampicin' => Input::get('nano_rifampicin'),
                            'nano_isoniazid' => Input::get('nano_isoniazid'),
                            'nano_levofloxacin' => Input::get('nano_levofloxacin'),
                            'nano_moxifloxacin' => Input::get('nano_moxifloxacin'),
                            'nano_bedaquiline' => Input::get('nano_bedaquiline'),
                            'nano_linezolid' => Input::get('nano_linezolid'),
                            'nano_clofazimine' => Input::get('nano_clofazimine'),
                            'nano_cycloserine' => Input::get('nano_cycloserine'),
                            'nano_delamanid' => Input::get('nano_delamanid'),
                            'nano_terizidone' => Input::get('nano_terizidone'),
                            'nano_ethambutol' => Input::get('nano_ethambutol'),
                            'nano_pyrazinamide' => Input::get('nano_pyrazinamide'),
                            'nano_cilastatin' => Input::get('nano_cilastatin'),
                            'nano_imipenem' => Input::get('nano_imipenem'),
                            'nano_meropenem' => Input::get('nano_meropenem'),
                            'nano_amikacin' => Input::get('nano_amikacin'),
                            'nano_streptomycin' => Input::get('nano_streptomycin'),
                            'nano_ethionamide' => Input::get('nano_ethionamide'),
                            'nano_prothionamide' => Input::get('nano_prothionamide'),
                            'nano_para_aminosalicylic_acid' => Input::get('nano_para_aminosalicylic_acid'),
                            'nano_capreomycin' => Input::get('nano_capreomycin'),
                            'nano_kanamycin' => Input::get('nano_kanamycin'),
                            'nano_pretomanid' => Input::get('nano_pretomanid'),
                            'xpert_xdr_performed' => Input::get('xpert_xdr_performed'),
                            'xpert_xdr_date_performed' => Input::get('xpert_xdr_date_performed'),
                            'isoniazid2' => Input::get('isoniazid2'),
                            'fluoroquinolones' => Input::get('fluoroquinolones'),
                            'amikacin2' => Input::get('amikacin2'),
                            'kanamycin' => Input::get('kanamycin'),
                            'capreomycin' => Input::get('capreomycin'),
                            'ethionamide2' => Input::get('ethionamide2'),
                            'first_line_drugs' => $first_line_drugs,
                            'second_line_drugs' => $second_line_drugs,
                            'version_number' => Input::get('version_number'),
                            'lot_number' => Input::get('lot_number'),
                            'mutations_detected_list' => Input::get('mutations_detected_list'),
                            'nanopore_done' => Input::get('nanopore_done'),
                            'sequencing_results' => Input::get('sequencing_results'),
                            'EPI2ME' => Input::get('EPI2ME'),
                            'EPI2ME_vesrion' => Input::get('EPI2ME_vesrion'),
                            'first_line_lpa' => Input::get('first_line_lpa'),
                            'first_line_lpa_date' => Input::get('first_line_lpa_date'),
                            'second_line_lpa' => Input::get('second_line_lpa'),
                            'second_line_lpa_date' => Input::get('second_line_lpa_date'),
                            'lpa1_mtb' => Input::get('lpa1_mtb'),
                            'lpa1_rif' => Input::get('lpa1_rif'),
                            'lpa1_inh' => Input::get('lpa1_inh'),
                            'lpa2_mtb' => Input::get('lpa2_mtb'),
                            'lpa2_rfluoroquinolones' => Input::get('lpa2_rfluoroquinolones'),
                            'lpa2_aminoglycosides' => Input::get('lpa2_aminoglycosides'),
                            'lpa2_kanamycin' => Input::get('lpa2_kanamycin'),
                            'remarks' => Input::get('remarks'),
                            'form_status' => Input::get('form_status'),
                            'date_completed' => $date_completed,
                            'completed_by' => $completed_by,
                            'date_verified' => $date_verified,
                            'verified_by' => $verified_by,
                            'status' => 1,
                            'enrollment_id' => $_GET['sid'],
                            'create_on' => date('Y-m-d H:i:s'),
                            'staff_id' => $user->data()->id,
                            'update_on' => date('Y-m-d H:i:s'),
                            'update_id' => $user->data()->id,
                            'facility_id' => $screening['facility_id'],
                        ));

                        $last_row = $override->lastRow('diagnosis_test', 'id')[0];

                        $user->createRecord('diagnosis_test_records', array(
                            'diagnosis_test_id' => $last_row['id'],
                            'pid' => $screening['pid'],
                            'culture_performed' => Input::get('culture_performed'),
                            'culture_method' => $culture_method,
                            'culture_results' => Input::get('culture_results'),
                            'phenotypic_performed' => Input::get('phenotypic_performed'),
                            'phenotypic_date_performed' => Input::get('phenotypic_date_performed'),
                            'phenotypic_date_results' => Input::get('phenotypic_date_results'),
                            'date_sputum_received' => Input::get('date_sputum_received'),
                            'appearance' => Input::get('appearance'),
                            'sample_volume' => Input::get('sample_volume'),
                            'unique_lab_no' => Input::get('unique_lab_no'),
                            'microscopy_type' => Input::get('microscopy_type'),
                            'microscopy_results' => Input::get('microscopy_results'),
                            'microscopy_date' => Input::get('microscopy_date'),
                            'lj_inoculation_date' => Input::get('lj_inoculation_date'),
                            'lj_results_date' => Input::get('lj_results_date'),
                            'lj_results' => Input::get('lj_results'),
                            'mgit_inoculation_date' => Input::get('mgit_inoculation_date'),
                            'mgit_results_date' => Input::get('mgit_results_date'),
                            'mgit_results' => Input::get('mgit_results'),
                            'culture_isolate' => Input::get('culture_isolate'),
                            'isolate_date' => Input::get('isolate_date'),
                            'rifampicin' => Input::get('rifampicin'),
                            'isoniazid' => Input::get('isoniazid'),
                            'levofloxacin' => Input::get('levofloxacin'),
                            'moxifloxacin' => Input::get('moxifloxacin'),
                            'bedaquiline' => Input::get('bedaquiline'),
                            'linezolid' => Input::get('linezolid'),
                            'clofazimine' => Input::get('clofazimine'),
                            'cycloserine' => Input::get('cycloserine'),
                            'terizidone' => Input::get('terizidone'),
                            'ethambutol' => Input::get('ethambutol'),
                            'delamanid' => Input::get('delamanid'),
                            'pyrazinamide' => Input::get('pyrazinamide'),
                            'imipenem' => Input::get('imipenem'),
                            'cilastatin' => Input::get('cilastatin'),
                            'meropenem' => Input::get('meropenem'),
                            'amikacin' => Input::get('amikacin'),
                            'streptomycin' => Input::get('streptomycin'),
                            'ethionamide' => Input::get('ethionamide'),
                            'prothionamide' => Input::get('prothionamide'),
                            'para_aminosalicylic_acid' => Input::get('para_aminosalicylic_acid'),
                            'nano_rifampicin' => Input::get('nano_rifampicin'),
                            'nano_isoniazid' => Input::get('nano_isoniazid'),
                            'nano_levofloxacin' => Input::get('nano_levofloxacin'),
                            'nano_moxifloxacin' => Input::get('nano_moxifloxacin'),
                            'nano_bedaquiline' => Input::get('nano_bedaquiline'),
                            'nano_linezolid' => Input::get('nano_linezolid'),
                            'nano_clofazimine' => Input::get('nano_clofazimine'),
                            'nano_cycloserine' => Input::get('nano_cycloserine'),
                            'nano_delamanid' => Input::get('nano_delamanid'),
                            'nano_terizidone' => Input::get('nano_terizidone'),
                            'nano_ethambutol' => Input::get('nano_ethambutol'),
                            'nano_pyrazinamide' => Input::get('nano_pyrazinamide'),
                            'nano_cilastatin' => Input::get('nano_cilastatin'),
                            'nano_imipenem' => Input::get('nano_imipenem'),
                            'nano_meropenem' => Input::get('nano_meropenem'),
                            'nano_amikacin' => Input::get('nano_amikacin'),
                            'nano_streptomycin' => Input::get('nano_streptomycin'),
                            'nano_ethionamide' => Input::get('nano_ethionamide'),
                            'nano_prothionamide' => Input::get('nano_prothionamide'),
                            'nano_para_aminosalicylic_acid' => Input::get('nano_para_aminosalicylic_acid'),
                            'nano_capreomycin' => Input::get('nano_capreomycin'),
                            'nano_kanamycin' => Input::get('nano_kanamycin'),
                            'nano_pretomanid' => Input::get('nano_pretomanid'),
                            'xpert_xdr_performed' => Input::get('xpert_xdr_performed'),
                            'xpert_xdr_date_performed' => Input::get('xpert_xdr_date_performed'),
                            'isoniazid2' => Input::get('isoniazid2'),
                            'fluoroquinolones' => Input::get('fluoroquinolones'),
                            'amikacin2' => Input::get('amikacin2'),
                            'kanamycin' => Input::get('kanamycin'),
                            'capreomycin' => Input::get('capreomycin'),
                            'ethionamide2' => Input::get('ethionamide2'),
                            'first_line_drugs' => $first_line_drugs,
                            'second_line_drugs' => $second_line_drugs,
                            'version_number' => Input::get('version_number'),
                            'lot_number' => Input::get('lot_number'),
                            'mutations_detected_list' => Input::get('mutations_detected_list'),
                            'nanopore_done' => Input::get('nanopore_done'),
                            'sequencing_results' => Input::get('sequencing_results'),
                            'EPI2ME' => Input::get('EPI2ME'),
                            'EPI2ME_vesrion' => Input::get('EPI2ME_vesrion'),
                            'first_line_lpa' => Input::get('first_line_lpa'),
                            'first_line_lpa_date' => Input::get('first_line_lpa_date'),
                            'second_line_lpa' => Input::get('second_line_lpa'),
                            'second_line_lpa_date' => Input::get('second_line_lpa_date'),
                            'lpa1_mtb' => Input::get('lpa1_mtb'),
                            'lpa1_rif' => Input::get('lpa1_rif'),
                            'lpa1_inh' => Input::get('lpa1_inh'),
                            'lpa2_mtb' => Input::get('lpa2_mtb'),
                            'lpa2_rfluoroquinolones' => Input::get('lpa2_rfluoroquinolones'),
                            'lpa2_aminoglycosides' => Input::get('lpa2_aminoglycosides'),
                            'lpa2_kanamycin' => Input::get('lpa2_kanamycin'),
                            'remarks' => Input::get('remarks'),
                            'form_status' => Input::get('form_status'),
                            'date_completed' => $date_completed,
                            'completed_by' => $completed_by,
                            'date_verified' => $date_verified,
                            'verified_by' => $verified_by,
                            'status' => 1,
                            'enrollment_id' => $_GET['sid'],
                            'create_on' => date('Y-m-d H:i:s'),
                            'staff_id' => $user->data()->id,
                            'update_on' => date('Y-m-d H:i:s'),
                            'update_id' => $user->data()->id,
                            'facility_id' => $screening['facility_id'],
                        ));

                        $successMessage = 'Diagnosis test  Successful Added';
                    }
                    Redirect::to('info.php?id=6&status=' . $_GET['status'] . '&sid=' . $_GET['sid'] . '&facility_id=' . $_GET['facility_id'] . '&page=' . $_GET['page'] . '&msg=' . $successMessage);
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_respiratory')) {
            $validate = $validate->check($_POST, array(
                'sample_received' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                $screening = $override->getNews('screening', 'status', 1, 'id', $_GET['sid'])[0];
                $costing = $override->getNews('respiratory', 'status', 1, 'enrollment_id', $_GET['sid']);


                $date_completed = "";
                $completed_by = "";
                $date_verified = "";
                $verified_by = "";

                if (
                    (Input::get('form_status') == 1)
                ) {
                    $date_completed = "";
                    $completed_by = "";
                    $date_verified = "";
                    $verified_by = "";
                } elseif (Input::get('form_status') == 2) {
                    $date_completed = Input::get('date_completed');
                    $completed_by = $user->data()->id;
                    $date_verified = "";
                    $verified_by = "";
                } elseif (Input::get('form_status') == 3) {
                    $date_completed = $screening['date_completed'];
                    $completed_by = $screening['completed_by'];
                    $date_verified = Input::get('date_completed');
                    $verified_by = $user->data()->id;
                }

                if (Input::get('respiratory_completness') == 3 && Input::get('respiratory_verified_date') == "") {
                    $errorMessage = 'You do not have Permissions to Verify this form pleae you can only "Complete Form "';
                } else {
                    if ($costing) {
                        $user->updateRecord('respiratory', array(
                            'lab_name' => Input::get('lab_name'),
                            'sample_received' => Input::get('sample_received'),
                            'sample_reason' => Input::get('sample_reason'),
                            'other_reason' => Input::get('other_reason'),
                            'new_sample' => Input::get('new_sample'),
                            'new_reason' => Input::get('new_reason'),
                            'date_sample1_collected' => Input::get('date_sample1_collected'),
                            'date_sample1_received' => Input::get('date_sample1_received'),
                            'date_sample2_collected' => Input::get('date_sample2_collected'),
                            'date_sample2_received' => Input::get('date_sample2_received'),
                            'appearance_sample2' => Input::get('appearance_sample2'),
                            'appearance_sample1' => Input::get('appearance_sample1'),
                            'sample2_volume' => Input::get('sample2_volume'),
                            'sample1_volume' => Input::get('sample1_volume'),
                            'afb_microscopy_conducted' => Input::get('afb_microscopy_conducted'),
                            'xpert_mtb_rif_conducted' => Input::get('xpert_mtb_rif_conducted'),
                            'number_received' => Input::get('number_received'),
                            'technique_a' => Input::get('technique_a'),
                            'afb_a_results' => Input::get('afb_a_results'),
                            'afb_a_date' => Input::get('afb_a_date'),
                            'technique_b' => Input::get('technique_b'),
                            'afb_b_results' => Input::get('afb_b_results'),
                            'afb_b_date' => Input::get('afb_b_date'),
                            'xpert_date' => Input::get('xpert_date'),
                            'xpert_mtb' => Input::get('xpert_mtb'),
                            'error_code' => Input::get('error_code'),
                            'xpert_rif' => Input::get('xpert_rif'),
                            'ct_value' => Input::get('ct_value'),
                            'ct_na' => Input::get('ct_na'),
                            'remarks' => Input::get('remarks'),
                            'form_status' => Input::get('form_status'),
                            'date_completed' => $date_completed,
                            'completed_by' => $completed_by,
                            'date_verified' => $date_verified,
                            'verified_by' => $verified_by,
                            'update_on' => date('Y-m-d H:i:s'),
                            'update_id' => $user->data()->id,
                            'facility_id' => $screening['facility_id'],
                        ), $costing[0]['id']);


                        $user->createRecord('respiratory_records', array(
                            'respiratory_id' => $costing[0]['id'],
                            'pid' => $screening['pid'],
                            'lab_name' => Input::get('lab_name'),
                            'sample_received' => Input::get('sample_received'),
                            'sample_reason' => Input::get('sample_reason'),
                            'other_reason' => Input::get('other_reason'),
                            'new_sample' => Input::get('new_sample'),
                            'new_reason' => Input::get('new_reason'),
                            'date_sample1_collected' => Input::get('date_sample1_collected'),
                            'date_sample1_received' => Input::get('date_sample1_received'),
                            'date_sample2_collected' => Input::get('date_sample2_collected'),
                            'date_sample2_received' => Input::get('date_sample2_received'),
                            'appearance_sample2' => Input::get('appearance_sample2'),
                            'appearance_sample1' => Input::get('appearance_sample1'),
                            'sample2_volume' => Input::get('sample2_volume'),
                            'sample1_volume' => Input::get('sample1_volume'),
                            'afb_microscopy_conducted' => Input::get('afb_microscopy_conducted'),
                            'xpert_mtb_rif_conducted' => Input::get('xpert_mtb_rif_conducted'),
                            'number_received' => Input::get('number_received'),
                            'technique_a' => Input::get('technique_a'),
                            'afb_a_results' => Input::get('afb_a_results'),
                            'afb_a_date' => Input::get('afb_a_date'),
                            'technique_b' => Input::get('technique_b'),
                            'afb_b_results' => Input::get('afb_b_results'),
                            'afb_b_date' => Input::get('afb_b_date'),
                            'xpert_date' => Input::get('xpert_date'),
                            'xpert_mtb' => Input::get('xpert_mtb'),
                            'error_code' => Input::get('error_code'),
                            'xpert_rif' => Input::get('xpert_rif'),
                            'ct_value' => Input::get('ct_value'),
                            'ct_na' => Input::get('ct_na'),
                            'remarks' => Input::get('remarks'),
                            'form_status' => Input::get('form_status'),
                            'date_completed' => $date_completed,
                            'completed_by' => $completed_by,
                            'date_verified' => $date_verified,
                            'verified_by' => $verified_by,
                            'status' => 1,
                            'enrollment_id' => $_GET['sid'],
                            'create_on' => date('Y-m-d H:i:s'),
                            'staff_id' => $user->data()->id,
                            'update_on' => date('Y-m-d H:i:s'),
                            'update_id' => $user->data()->id,
                            'facility_id' => $screening['facility_id'],
                        ));
                        $successMessage = 'Respiratory Data  Successful Updated';
                    } else {
                        $user->createRecord('respiratory', array(
                            'pid' => $screening['pid'],
                            'lab_name' => Input::get('lab_name'),
                            'sample_received' => Input::get('sample_received'),
                            'sample_reason' => Input::get('sample_reason'),
                            'other_reason' => Input::get('other_reason'),
                            'new_sample' => Input::get('new_sample'),
                            'date_sample1_collected' => Input::get('date_sample1_collected'),
                            'date_sample1_received' => Input::get('date_sample1_received'),
                            'date_sample2_collected' => Input::get('date_sample2_collected'),
                            'date_sample2_received' => Input::get('date_sample2_received'),
                            'appearance_sample2' => Input::get('appearance_sample2'),
                            'appearance_sample1' => Input::get('appearance_sample1'),
                            'sample2_volume' => Input::get('sample2_volume'),
                            'sample1_volume' => Input::get('sample1_volume'),
                            'afb_microscopy_conducted' => Input::get('afb_microscopy_conducted'),
                            'xpert_mtb_rif_conducted' => Input::get('xpert_mtb_rif_conducted'),
                            'number_received' => Input::get('number_received'),
                            'technique_a' => Input::get('technique_a'),
                            'afb_a_results' => Input::get('afb_a_results'),
                            'afb_a_date' => Input::get('afb_a_date'),
                            'technique_b' => Input::get('technique_b'),
                            'afb_b_results' => Input::get('afb_b_results'),
                            'afb_b_date' => Input::get('afb_b_date'),
                            'xpert_date' => Input::get('xpert_date'),
                            'xpert_mtb' => Input::get('xpert_mtb'),
                            'error_code' => Input::get('error_code'),
                            'xpert_rif' => Input::get('xpert_rif'),
                            'ct_value' => Input::get('ct_value'),
                            'ct_na' => Input::get('ct_na'),
                            'remarks' => Input::get('remarks'),
                            'form_status' => Input::get('form_status'),
                            'date_completed' => $date_completed,
                            'completed_by' => $completed_by,
                            'date_verified' => $date_verified,
                            'verified_by' => $verified_by,
                            'status' => 1,
                            'enrollment_id' => $_GET['sid'],
                            'create_on' => date('Y-m-d H:i:s'),
                            'staff_id' => $user->data()->id,
                            'update_on' => date('Y-m-d H:i:s'),
                            'update_id' => $user->data()->id,
                            'facility_id' => $screening['facility_id'],
                        ));


                        $last_row = $override->lastRow('respiratory', 'id')[0];

                        $user->createRecord('respiratory_records', array(
                            'respiratory_id' => $last_row['id'],
                            'pid' => $screening['pid'],
                            'lab_name' => Input::get('lab_name'),
                            'sample_received' => Input::get('sample_received'),
                            'sample_reason' => Input::get('sample_reason'),
                            'other_reason' => Input::get('other_reason'),
                            'new_sample' => Input::get('new_sample'),
                            'new_reason' => Input::get('new_reason'),
                            'date_sample1_collected' => Input::get('date_sample1_collected'),
                            'date_sample1_received' => Input::get('date_sample1_received'),
                            'date_sample2_collected' => Input::get('date_sample2_collected'),
                            'date_sample2_received' => Input::get('date_sample2_received'),
                            'appearance_sample2' => Input::get('appearance_sample2'),
                            'appearance_sample1' => Input::get('appearance_sample1'),
                            'sample2_volume' => Input::get('sample2_volume'),
                            'sample1_volume' => Input::get('sample1_volume'),
                            'afb_microscopy_conducted' => Input::get('afb_microscopy_conducted'),
                            'xpert_mtb_rif_conducted' => Input::get('xpert_mtb_rif_conducted'),
                            'number_received' => Input::get('number_received'),
                            'technique_a' => Input::get('technique_a'),
                            'afb_a_results' => Input::get('afb_a_results'),
                            'afb_a_date' => Input::get('afb_a_date'),
                            'technique_b' => Input::get('technique_b'),
                            'afb_b_results' => Input::get('afb_b_results'),
                            'afb_b_date' => Input::get('afb_b_date'),
                            'xpert_date' => Input::get('xpert_date'),
                            'xpert_mtb' => Input::get('xpert_mtb'),
                            'error_code' => Input::get('error_code'),
                            'xpert_rif' => Input::get('xpert_rif'),
                            'ct_value' => Input::get('ct_value'),
                            'ct_na' => Input::get('ct_na'),
                            'remarks' => Input::get('remarks'),
                            'form_status' => Input::get('form_status'),
                            'date_completed' => $date_completed,
                            'completed_by' => $completed_by,
                            'date_verified' => $date_verified,
                            'verified_by' => $verified_by,
                            'status' => 1,
                            'enrollment_id' => $_GET['sid'],
                            'create_on' => date('Y-m-d H:i:s'),
                            'staff_id' => $user->data()->id,
                            'update_on' => date('Y-m-d H:i:s'),
                            'update_id' => $user->data()->id,
                            'facility_id' => $screening['facility_id'],
                        ));

                        $successMessage = 'Respiratory Data  Successful Added';
                    }
                    Redirect::to('info.php?id=6&status=' . $_GET['status'] . '&sid=' . $_GET['sid'] . '&facility_id=' . $_GET['facility_id'] . '&page=' . $_GET['page'] . '&msg=' . $successMessage);
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_non_respiratory')) {
            $validate = $validate->check($_POST, array(
                'sample_name' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                $screening = $override->getNews('screening', 'status', 1, 'id', $_GET['sid'])[0];
                $costing = $override->getNews('non_respiratory', 'status', 1, 'enrollment_id', $_GET['sid']);

                // $test_reasons = implode(',', Input::get('test_reasons'));
                // $sample_type = implode(',', Input::get('sample_type'));
                if (Input::get('form_completness') == 3 && Input::get('non_respiratory_verified_date') == "") {
                    $errorMessage = 'You do not have Permissions to Verify this form pleae you can only "Complete Form "';
                } else {
                    if ($costing) {
                        $user->updateRecord('non_respiratory', array(
                            'sample_name' => Input::get('sample_name'),
                            'tests_conducted' => Input::get('tests_conducted'),
                            'tests_conducted_other' => Input::get('tests_conducted_other'),
                            'test_results' => Input::get('test_results'),
                            'sample_name_2' => Input::get('sample_name_2'),
                            'tests_conducted_2' => Input::get('tests_conducted_2'),
                            'tests_conducted_other2' => Input::get('tests_conducted_other2'),
                            'test_results_2' => Input::get('test_results_2'),
                            'remarks' => Input::get('remarks'),
                            'form_completness' => Input::get('form_completness'),
                            'date_completed' => Input::get('date_completed'),
                            'non_respiratory_completed' => Input::get('non_respiratory_completed'),
                            'non_respiratory_verified_by' => $user->data()->id,
                            'non_respiratory_verified_date' => Input::get('non_respiratory_verified_date'),
                            'update_on' => date('Y-m-d H:i:s'),
                            'update_id' => $user->data()->id,
                            'facility_id' => $screening['facility_id'],
                        ), $costing[0]['id']);

                        $user->createRecord('non_respiratory_records', array(
                            'non_respiratory_id' => $costing[0]['id'],
                            'pid' => $screening['pid'],
                            'sample_name' => Input::get('sample_name'),
                            'tests_conducted' => Input::get('tests_conducted'),
                            'tests_conducted_other' => Input::get('tests_conducted_other'),
                            'test_results' => Input::get('test_results'),
                            'sample_name_2' => Input::get('sample_name_2'),
                            'tests_conducted_2' => Input::get('tests_conducted_2'),
                            'tests_conducted_other2' => Input::get('tests_conducted_other2'),
                            'test_results_2' => Input::get('test_results_2'),
                            'remarks' => Input::get('remarks'),
                            'form_completness' => Input::get('form_completness'),
                            'date_completed' => Input::get('date_completed'),
                            'non_respiratory_completed' => Input::get('non_respiratory_completed'),
                            'non_respiratory_verified_by' => $user->data()->id,
                            'non_respiratory_verified_date' => Input::get('non_respiratory_verified_date'),
                            'status' => 1,
                            'enrollment_id' => $_GET['sid'],
                            'create_on' => date('Y-m-d H:i:s'),
                            'staff_id' => $user->data()->id,
                            'update_on' => date('Y-m-d H:i:s'),
                            'update_id' => $user->data()->id,
                            'facility_id' => $screening['facility_id'],
                        ));

                        $successMessage = 'Non Respiratory Data  Successful Updated';
                    } else {
                        $user->createRecord('non_respiratory', array(
                            'pid' => $screening['pid'],
                            'sample_name' => Input::get('sample_name'),
                            'tests_conducted' => Input::get('tests_conducted'),
                            'tests_conducted_other' => Input::get('tests_conducted_other'),
                            'test_results' => Input::get('test_results'),
                            'sample_name_2' => Input::get('sample_name_2'),
                            'tests_conducted_2' => Input::get('tests_conducted_2'),
                            'tests_conducted_other2' => Input::get('tests_conducted_other2'),
                            'test_results_2' => Input::get('test_results_2'),
                            'remarks' => Input::get('remarks'),
                            'form_completness' => Input::get('form_completness'),
                            'date_completed' => Input::get('date_completed'),
                            'non_respiratory_completed' => Input::get('non_respiratory_completed'),
                            'non_respiratory_verified_by' => $user->data()->id,
                            'non_respiratory_verified_date' => Input::get('non_respiratory_verified_date'),
                            'status' => 1,
                            'enrollment_id' => $_GET['sid'],
                            'create_on' => date('Y-m-d H:i:s'),
                            'staff_id' => $user->data()->id,
                            'update_on' => date('Y-m-d H:i:s'),
                            'update_id' => $user->data()->id,
                            'facility_id' => $screening['facility_id'],
                        ));

                        $last_row = $override->lastRow('non_respiratory', 'id')[0];

                        $user->createRecord('non_respiratory_records', array(
                            'non_respiratory_id' => $last_row['id'],
                            'pid' => $screening['pid'],
                            'sample_name' => Input::get('sample_name'),
                            'tests_conducted' => Input::get('tests_conducted'),
                            'tests_conducted_other' => Input::get('tests_conducted_other'),
                            'test_results' => Input::get('test_results'),
                            'sample_name_2' => Input::get('sample_name_2'),
                            'tests_conducted_2' => Input::get('tests_conducted_2'),
                            'tests_conducted_other2' => Input::get('tests_conducted_other2'),
                            'test_results_2' => Input::get('test_results_2'),
                            'remarks' => Input::get('remarks'),
                            'form_completness' => Input::get('form_completness'),
                            'date_completed' => Input::get('date_completed'),
                            'non_respiratory_completed' => Input::get('non_respiratory_completed'),
                            'non_respiratory_verified_by' => $user->data()->id,
                            'non_respiratory_verified_date' => Input::get('non_respiratory_verified_date'),
                            'status' => 1,
                            'enrollment_id' => $_GET['sid'],
                            'create_on' => date('Y-m-d H:i:s'),
                            'staff_id' => $user->data()->id,
                            'update_on' => date('Y-m-d H:i:s'),
                            'update_id' => $user->data()->id,
                            'facility_id' => $screening['facility_id'],
                        ));


                        $successMessage = 'Non Respiratory Data  Successful Added';
                    }

                    Redirect::to('info.php?id=6&status=' . $_GET['status'] . '&sid=' . $_GET['sid'] . '&facility_id=' . $_GET['facility_id'] . '&page=' . $_GET['page'] . '&msg=' . $successMessage);
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_diagnosis')) {
            $validate = $validate->check($_POST, array(
                'tb_diagnosis' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                $screening = $override->getNews('screening', 'status', 1, 'id', $_GET['sid'])[0];
                $costing = $override->getNews('diagnosis', 'status', 1, 'enrollment_id', $_GET['sid']);

                $date_completed = "";
                $completed_by = "";
                $date_verified = "";
                $verified_by = "";

                if (
                    (Input::get('form_status') == 1)
                ) {
                    $date_completed = "";
                    $completed_by = "";
                    $date_verified = "";
                    $verified_by = "";
                } elseif (Input::get('form_status') == 2) {
                    $date_completed = Input::get('date_completed');
                    $completed_by = $user->data()->id;
                    $date_verified = "";
                    $verified_by = "";
                } elseif (Input::get('form_status') == 3) {
                    $date_completed = $screening['date_completed'];
                    $completed_by = $screening['completed_by'];
                    $date_verified = Input::get('date_completed');
                    $verified_by = $user->data()->id;
                }

                $bacteriological_diagnosis = implode(',', Input::get('bacteriological_diagnosis'));
                $tb_diagnosed_clinically = implode(',', Input::get('tb_diagnosed_clinically'));
                $laboratory_test_used = implode(',', Input::get('laboratory_test_used'));
                $laboratory_test_used2 = implode(',', Input::get('laboratory_test_used2'));
                if (Input::get('diagnosis_completness') == 3 && Input::get('diagnosis_verified_date') == "") {
                    $errorMessage = 'You do not have Permissions to Verify this form pleae you can only "Complete Form "';
                } else {
                    if ($costing) {
                        $user->updateRecord('diagnosis', array(
                            'tb_diagnosis' => Input::get('tb_diagnosis'),
                            'tb_diagnosis_date' => Input::get('tb_diagnosis_date'),
                            'tb_diagnosis_made' => Input::get('tb_diagnosis_made'),
                            'diagnosis_made_other' => Input::get('diagnosis_made_other'),
                            'bacteriological_diagnosis' => Input::get('bacteriological_diagnosis'),
                            'clinician_received_date' => Input::get('clinician_received_date'),
                            'tb_register_number' => Input::get('tb_register_number'),
                            'xpert_truenat_date' => Input::get('xpert_truenat_date'),
                            'other_bacteriological' => Input::get('other_bacteriological'),
                            'other_bacteriological_date' => Input::get('other_bacteriological_date'),
                            'tb_diagnosed_clinically' => $tb_diagnosed_clinically,
                            'tb_clinically_other' => Input::get('tb_clinically_other'),
                            'tb_treatment' => Input::get('tb_treatment'),
                            'tb_treatment_date' => Input::get('tb_treatment_date'),
                            'tb_facility' => Input::get('tb_facility'),
                            'tb_reason' => Input::get('tb_reason'),
                            'tb_regimen' => Input::get('tb_regimen'),
                            'tb_regimen_other' => Input::get('tb_regimen_other'),
                            'tb_regimen_based' => Input::get('tb_regimen_based'),
                            'tb_regimen_based_other' => Input::get('tb_regimen_based_other'),
                            'regimen_changed' => Input::get('regimen_changed'),
                            'regimen_changed_other' => Input::get('regimen_changed_other'),
                            'regimen_changed__date' => Input::get('regimen_changed__date'),
                            'regimen_removed_name' => Input::get('regimen_removed_name'),
                            'regimen_added_name' => Input::get('regimen_added_name'),
                            'laboratory_test_used_other' => Input::get('laboratory_test_used_other'),
                            'regimen_changed__reason' => Input::get('regimen_changed__reason'),
                            'tb_otcome2' => Input::get('tb_otcome2'),
                            'tb_otcome2_date' => Input::get('tb_otcome2_date'),
                            'tb_other_diagnosis' => Input::get('tb_other_diagnosis'),
                            'tb_other_specify' => Input::get('tb_other_specify'),
                            'tb_diagnosis_made2' => Input::get('tb_diagnosis_made2'),
                            'laboratory_test_used' => $laboratory_test_used,
                            'laboratory_test_used2' => $laboratory_test_used2,
                            'laboratory_test_used_date' => Input::get('laboratory_test_used_date'),
                            'remarks' => Input::get('remarks'),
                            'form_status' => Input::get('form_status'),
                            'date_completed' => $date_completed,
                            'completed_by' => $completed_by,
                            'date_verified' => $date_verified,
                            'verified_by' => $verified_by,
                            'update_on' => date('Y-m-d H:i:s'),
                            'update_id' => $user->data()->id,
                            'facility_id' => $screening['facility_id'],
                        ), $costing[0]['id']);

                        $user->createRecord('diagnosis_records', array(
                            'diagnosis_id' => $costing[0]['id'],
                            'pid' => $screening['pid'],
                            'entry_date' => Input::get('entry_date'),
                            'tb_diagnosis' => Input::get('tb_diagnosis'),
                            'tb_diagnosis_date' => Input::get('tb_diagnosis_date'),
                            'tb_diagnosis_made' => Input::get('tb_diagnosis_made'),
                            'diagnosis_made_other' => Input::get('diagnosis_made_other'),
                            'bacteriological_diagnosis' => Input::get('bacteriological_diagnosis'),
                            'clinician_received_date' => Input::get('clinician_received_date'),
                            'tb_register_number' => Input::get('tb_register_number'),
                            'xpert_truenat_date' => Input::get('xpert_truenat_date'),
                            'other_bacteriological' => Input::get('other_bacteriological'),
                            'other_bacteriological_date' => Input::get('other_bacteriological_date'),
                            'tb_diagnosed_clinically' => $tb_diagnosed_clinically,
                            'tb_clinically_other' => Input::get('tb_clinically_other'),
                            'tb_treatment' => Input::get('tb_treatment'),
                            'tb_treatment_date' => Input::get('tb_treatment_date'),
                            'tb_facility' => Input::get('tb_facility'),
                            'tb_reason' => Input::get('tb_reason'),
                            'tb_regimen' => Input::get('tb_regimen'),
                            'tb_regimen_other' => Input::get('tb_regimen_other'),
                            'tb_regimen_based' => Input::get('tb_regimen_based'),
                            'tb_regimen_based_other' => Input::get('tb_regimen_based_other'),
                            'regimen_changed' => Input::get('regimen_changed'),
                            'regimen_changed_other' => Input::get('regimen_changed_other'),
                            'regimen_changed__date' => Input::get('regimen_changed__date'),
                            'regimen_removed_name' => Input::get('regimen_removed_name'),
                            'regimen_added_name' => Input::get('regimen_added_name'),
                            'laboratory_test_used_other' => Input::get('laboratory_test_used_other'),
                            'regimen_changed__reason' => Input::get('regimen_changed__reason'),
                            'tb_otcome2' => Input::get('tb_otcome2'),
                            'tb_otcome2_date' => Input::get('tb_otcome2_date'),
                            'tb_other_diagnosis' => Input::get('tb_other_diagnosis'),
                            'tb_other_specify' => Input::get('tb_other_specify'),
                            'tb_diagnosis_made2' => Input::get('tb_diagnosis_made2'),
                            'laboratory_test_used' => $laboratory_test_used,
                            'laboratory_test_used2' => $laboratory_test_used2,
                            'laboratory_test_used_date' => Input::get('laboratory_test_used_date'),
                            'remarks' => Input::get('remarks'),
                            'form_status' => Input::get('form_status'),
                            'date_completed' => $date_completed,
                            'completed_by' => $completed_by,
                            'date_verified' => $date_verified,
                            'verified_by' => $verified_by,
                            'status' => 1,
                            'enrollment_id' => $_GET['sid'],
                            'create_on' => date('Y-m-d H:i:s'),
                            'staff_id' => $user->data()->id,
                            'update_on' => date('Y-m-d H:i:s'),
                            'update_id' => $user->data()->id,
                            'facility_id' => $screening['facility_id'],
                        ));
                        $successMessage = 'Diagnosis Data  Successful Updated';
                    } else {
                        $user->createRecord('diagnosis', array(
                            'pid' => $screening['pid'],
                            'entry_date' => Input::get('entry_date'),
                            'tb_diagnosis' => Input::get('tb_diagnosis'),
                            'tb_diagnosis_date' => Input::get('tb_diagnosis_date'),
                            'tb_diagnosis_made' => Input::get('tb_diagnosis_made'),
                            'diagnosis_made_other' => Input::get('diagnosis_made_other'),
                            'bacteriological_diagnosis' => Input::get('bacteriological_diagnosis'),
                            'clinician_received_date' => Input::get('clinician_received_date'),
                            'tb_register_number' => Input::get('tb_register_number'),
                            'xpert_truenat_date' => Input::get('xpert_truenat_date'),
                            'other_bacteriological' => Input::get('other_bacteriological'),
                            'other_bacteriological_date' => Input::get('other_bacteriological_date'),
                            'tb_diagnosed_clinically' => $tb_diagnosed_clinically,
                            'tb_clinically_other' => Input::get('tb_clinically_other'),
                            'tb_treatment' => Input::get('tb_treatment'),
                            'tb_treatment_date' => Input::get('tb_treatment_date'),
                            'tb_facility' => Input::get('tb_facility'),
                            'tb_reason' => Input::get('tb_reason'),
                            'tb_regimen' => Input::get('tb_regimen'),
                            'tb_regimen_other' => Input::get('tb_regimen_other'),
                            'tb_regimen_based' => Input::get('tb_regimen_based'),
                            'tb_regimen_based_other' => Input::get('tb_regimen_based_other'),
                            'regimen_changed' => Input::get('regimen_changed'),
                            'regimen_changed_other' => Input::get('regimen_changed_other'),
                            'regimen_changed__date' => Input::get('regimen_changed__date'),
                            'regimen_removed_name' => Input::get('regimen_removed_name'),
                            'regimen_added_name' => Input::get('regimen_added_name'),
                            'laboratory_test_used_other' => Input::get('laboratory_test_used_other'),
                            'regimen_changed__reason' => Input::get('regimen_changed__reason'),
                            'tb_otcome2' => Input::get('tb_otcome2'),
                            'tb_otcome2_date' => Input::get('tb_otcome2_date'),
                            'tb_other_diagnosis' => Input::get('tb_other_diagnosis'),
                            'tb_other_specify' => Input::get('tb_other_specify'),
                            'tb_diagnosis_made2' => Input::get('tb_diagnosis_made2'),
                            'laboratory_test_used' => $laboratory_test_used,
                            'laboratory_test_used2' => $laboratory_test_used2,
                            'laboratory_test_used_date' => Input::get('laboratory_test_used_date'),
                            'remarks' => Input::get('remarks'),
                            'form_status' => Input::get('form_status'),
                            'date_completed' => $date_completed,
                            'completed_by' => $completed_by,
                            'date_verified' => $date_verified,
                            'verified_by' => $verified_by,
                            'status' => 1,
                            'enrollment_id' => $_GET['sid'],
                            'create_on' => date('Y-m-d H:i:s'),
                            'staff_id' => $user->data()->id,
                            'update_on' => date('Y-m-d H:i:s'),
                            'update_id' => $user->data()->id,
                            'facility_id' => $screening['facility_id'],
                        ));

                        $last_row = $override->lastRow('diagnosis', 'id')[0];

                        $user->createRecord('diagnosis_records', array(
                            'diagnosis_id' => $last_row['id'],
                            'pid' => $screening['pid'],
                            'entry_date' => Input::get('entry_date'),
                            'tb_diagnosis' => Input::get('tb_diagnosis'),
                            'tb_diagnosis_date' => Input::get('tb_diagnosis_date'),
                            'tb_diagnosis_made' => Input::get('tb_diagnosis_made'),
                            'diagnosis_made_other' => Input::get('diagnosis_made_other'),
                            'bacteriological_diagnosis' => Input::get('bacteriological_diagnosis'),
                            'clinician_received_date' => Input::get('clinician_received_date'),
                            'tb_register_number' => Input::get('tb_register_number'),
                            'xpert_truenat_date' => Input::get('xpert_truenat_date'),
                            'other_bacteriological' => Input::get('other_bacteriological'),
                            'other_bacteriological_date' => Input::get('other_bacteriological_date'),
                            'tb_diagnosed_clinically' => $tb_diagnosed_clinically,
                            'tb_clinically_other' => Input::get('tb_clinically_other'),
                            'tb_treatment' => Input::get('tb_treatment'),
                            'tb_treatment_date' => Input::get('tb_treatment_date'),
                            'tb_facility' => Input::get('tb_facility'),
                            'tb_reason' => Input::get('tb_reason'),
                            'tb_regimen' => Input::get('tb_regimen'),
                            'tb_regimen_other' => Input::get('tb_regimen_other'),
                            'tb_regimen_based' => Input::get('tb_regimen_based'),
                            'tb_regimen_based_other' => Input::get('tb_regimen_based_other'),
                            'regimen_changed' => Input::get('regimen_changed'),
                            'regimen_changed_other' => Input::get('regimen_changed_other'),
                            'regimen_changed__date' => Input::get('regimen_changed__date'),
                            'regimen_removed_name' => Input::get('regimen_removed_name'),
                            'regimen_added_name' => Input::get('regimen_added_name'),
                            'laboratory_test_used_other' => Input::get('laboratory_test_used_other'),
                            'regimen_changed__reason' => Input::get('regimen_changed__reason'),
                            'tb_otcome2' => Input::get('tb_otcome2'),
                            'tb_otcome2_date' => Input::get('tb_otcome2_date'),
                            'tb_other_diagnosis' => Input::get('tb_other_diagnosis'),
                            'tb_other_specify' => Input::get('tb_other_specify'),
                            'tb_diagnosis_made2' => Input::get('tb_diagnosis_made2'),
                            'laboratory_test_used' => $laboratory_test_used,
                            'laboratory_test_used2' => $laboratory_test_used2,
                            'laboratory_test_used_date' => Input::get('laboratory_test_used_date'),
                            'remarks' => Input::get('remarks'),
                            'form_status' => Input::get('form_status'),
                            'date_completed' => $date_completed,
                            'completed_by' => $completed_by,
                            'date_verified' => $date_verified,
                            'verified_by' => $verified_by,
                            'status' => 1,
                            'enrollment_id' => $_GET['sid'],
                            'create_on' => date('Y-m-d H:i:s'),
                            'staff_id' => $user->data()->id,
                            'update_on' => date('Y-m-d H:i:s'),
                            'update_id' => $user->data()->id,
                            'facility_id' => $screening['facility_id'],
                        ));


                        $successMessage = 'Diagnosis Data  Successful Added';
                    }

                    Redirect::to('info.php?id=6&status=' . $_GET['status'] . '&sid=' . $_GET['sid'] . '&facility_id=' . $_GET['facility_id'] . '&page=' . $_GET['page'] . '&msg=' . $successMessage);
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_validations')) {
            $validate = $validate->check($_POST, array(
                'date_collect' => array(
                    'required' => true,
                ),
                'date_receictrl' => array(
                    'required' => true,
                ),
                'lab_no' => array(
                    'required' => true,
                ),
                'form_completness' => array(
                    'required' => true,
                ),
                'transit_time' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                $individual = $override->getNews('validations', 'status', 1, 'id', $_GET['cid'])[0];
                $first_line = 0;
                $second_line = 0;
                $third_line = 0;
                $sequence = '';
                $visit_code = '';
                $visit_name = '';

                if (Input::get('first_line')) {
                    $first_line = Input::get('first_line');
                }

                if (Input::get('second_line')) {
                    $second_line = Input::get('second_line');
                }

                if (Input::get('third_line')) {
                    $third_line = Input::get('third_line');
                }

                $sequence = intval($_GET['sequence']) + 1;
                if ($sequence) {
                    $visit_code = 'M' . $sequence;
                    $visit_name = 'Month ' . $sequence;
                }

                $enrolled = 0;
                $end_study = 0;
                if (Input::get('next_appointment') == 1) {
                    $enrolled = 1;
                }

                $sample_methods = implode(',', Input::get('sample_methods'));
                $genotyping_asay = implode(',', Input::get('genotyping_asay'));
                $nanopore_sequencing = implode(',', Input::get('nanopore_sequencing'));
                $_1st_line_drugs = implode(',', Input::get('_1st_line_drugs'));
                $_2st_line_drugs = implode(',', Input::get('_2st_line_drugs'));

                if ($individual) {
                    $user->updateRecord('validations', array(
                        'pid' => $individual['study_id'],
                        'study_id' => $individual['study_id'],
                        'date_collect' => Input::get('date_collect'),
                        'date_receictrl' => Input::get('date_receictrl'),
                        'lab_no' => Input::get('lab_no'),
                        'transit_time' => Input::get('transit_time'),
                        'resid_distr' => Input::get('resid_distr'),
                        'h_facil' => Input::get('h_facil'),
                        'hf_district' => Input::get('hf_district'),
                        'tb_region' => Input::get('tb_region'),
                        'samplae_type' => Input::get('samplae_type'),
                        'pat_category' => Input::get('pat_category'),
                        'testrequest_reason' => Input::get('testrequest_reason'),
                        'follow_up_months' => Input::get('follow_up_months'),
                        'treatment_month' => Input::get('treatment_month'),
                        'hiv_status' => Input::get('hiv_status'),
                        'gx_results' => Input::get('gx_results'),
                        'gxmtb_ct' => Input::get('gxmtb_ct'),
                        'gx_mtbamount' => Input::get('gx_mtbamount'),
                        'fm_done' => Input::get('fm_done'),
                        'fm_date' => Input::get('fm_date'),
                        'fm_results' => Input::get('fm_results'),
                        'dec_date' => Input::get('dec_date'),
                        'cult_done' => Input::get('cult_done'),
                        'inno_date' => Input::get('inno_date'),
                        'ljcul_results' => Input::get('ljcul_results'),
                        'ljculres_date' => Input::get('ljculres_date'),
                        // 'mgitcul_done' => Input::get('mgitcul_done'),
                        // 'mgitcul_date' => Input::get('mgitcul_date'),
                        'mgitcul_resul' => Input::get('mgitcul_resul'),
                        'ljdst_rif' => Input::get('ljdst_rif'),
                        'ljdst_iso' => Input::get('ljdst_iso'),
                        'ljdst_ethamb' => Input::get('ljdst_ethamb'),
                        'mgitdst_stm' => Input::get('mgitdst_stm'),
                        'mgitdst_rif' => Input::get('mgitdst_rif'),
                        'mgitdst_iso' => Input::get('mgitdst_iso'),
                        'mgitdst_ethamb' => Input::get('mgitdst_ethamb'),
                        'mgitdst_bed' => Input::get('mgitdst_bed'),
                        'mgitdst_2cfz' => Input::get('mgitdst_2cfz'),
                        'mgitdst_2dlm' => Input::get('mgitdst_2dlm'),
                        'mgitdst_2levo' => Input::get('mgitdst_2levo'),
                        'mgitdst_2lzd' => Input::get('mgitdst_2lzd'),
                        'lpa1_done' => Input::get('lpa1_done'),
                        'lpa1_date1' => Input::get('lpa1_date1'),
                        'lpa1_mtbdetected' => Input::get('lpa1_mtbdetected'),
                        'lpaa1dst_rif' => Input::get('lpaa1dst_rif'),
                        'lpa1dst_inh' => Input::get('lpa1dst_inh'),
                        'lpa2_done' => Input::get('lpa2_done'),
                        'lpa2_date' => Input::get('lpa2_date'),
                        'lpa2_mtbdetected' => Input::get('lpa2_mtbdetected'),
                        'lpa2dst_lfx' => Input::get('lpa2dst_lfx'),
                        'lpa2dst_ag_cp' => Input::get('lpa2dst_ag_cp'),
                        'lpa2dstag_lowkan' => Input::get('lpa2dstag_lowkan'),
                        'nanop_done' => Input::get('nanop_done'),
                        'pos_control' => Input::get('pos_control'),
                        'neg_control' => Input::get('neg_control'),
                        'sample_control' => Input::get('sample_control'),
                        'internalcontrol' => Input::get('internalcontrol'),
                        'hsp65' => Input::get('hsp65'),
                        'nanopseq_date' => Input::get('nanopseq_date'),
                        'myco_results' => Input::get('myco_results'),
                        'myco_type' => Input::get('myco_type'),
                        'ntm_spp' => Input::get('ntm_spp'),
                        'myco_lineage' => Input::get('myco_lineage'),
                        'nano_rif' => Input::get('nano_rif'),
                        'nano_inh' => Input::get('nano_inh'),
                        'nano_kan' => Input::get('nano_kan'),
                        'nano_mxf' => Input::get('nano_mxf'),
                        'nano_cap' => Input::get('nano_cap'),
                        'nano_emb' => Input::get('nano_emb'),
                        'nano_pza' => Input::get('nano_pza'),
                        'nano_amk' => Input::get('nano_amk'),
                        'nano_bdq' => Input::get('nano_bdq'),
                        'nano_cfz' => Input::get('nano_cfz'),
                        'nano_dlm' => Input::get('nano_dlm'),
                        'nano_eto' => Input::get('nano_eto'),
                        'nano_lfx' => Input::get('nano_lfx'),
                        'nano_lzd' => Input::get('nano_lzd'),
                        'nano_pmd' => Input::get('nano_pmd'),
                        'nano_stm' => Input::get('nano_stm'),
                        'form_completness' => Input::get('form_completness'),
                        'date_completed' => Input::get('date_completed'),
                        'update_on' => date('Y-m-d H:i:s'),
                        'update_id' => $user->data()->id,
                    ), $individual['id']);

                    $successMessage = 'Validations Data  Successful Updated';
                } else {

                    $std_id = $override->get('study_id', 'status', 0)[0];


                    $user->createRecord('validations', array(
                        'pid' => $std_id['study_id'],
                        'study_id' => $std_id['study_id'],
                        'date_collect' => Input::get('date_collect'),
                        'date_receictrl' => Input::get('date_receictrl'),
                        'lab_no' => Input::get('lab_no'),
                        'transit_time' => Input::get('transit_time'),
                        'resid_distr' => Input::get('resid_distr'),
                        'h_facil' => Input::get('h_facil'),
                        'hf_district' => Input::get('hf_district'),
                        'tb_region' => Input::get('tb_region'),
                        'samplae_type' => Input::get('samplae_type'),
                        'pat_category' => Input::get('pat_category'),
                        'testrequest_reason' => Input::get('testrequest_reason'),
                        'follow_up_months' => Input::get('follow_up_months'),
                        'treatment_month' => Input::get('treatment_month'),
                        'hiv_status' => Input::get('hiv_status'),
                        'gx_results' => Input::get('gx_results'),
                        'gxmtb_ct' => Input::get('gxmtb_ct'),
                        'gx_mtbamount' => Input::get('gx_mtbamount'),
                        'fm_done' => Input::get('fm_done'),
                        'fm_date' => Input::get('fm_date'),
                        'fm_results' => Input::get('fm_results'),
                        'dec_date' => Input::get('dec_date'),
                        'cult_done' => Input::get('cult_done'),
                        'inno_date' => Input::get('inno_date'),
                        'ljcul_results' => Input::get('ljcul_results'),
                        'ljculres_date' => Input::get('ljculres_date'),
                        // 'mgitcul_done' => Input::get('mgitcul_done'),
                        // 'mgitcul_date' => Input::get('mgitcul_date'),
                        'mgitcul_resul' => Input::get('mgitcul_resul'),
                        'ljdst_rif' => Input::get('ljdst_rif'),
                        'ljdst_iso' => Input::get('ljdst_iso'),
                        'ljdst_ethamb' => Input::get('ljdst_ethamb'),
                        'mgitdst_stm' => Input::get('mgitdst_stm'),
                        'mgitdst_rif' => Input::get('mgitdst_rif'),
                        'mgitdst_iso' => Input::get('mgitdst_iso'),
                        'mgitdst_ethamb' => Input::get('mgitdst_ethamb'),
                        'mgitdst_bed' => Input::get('mgitdst_bed'),
                        'mgitdst_2cfz' => Input::get('mgitdst_2cfz'),
                        'mgitdst_2dlm' => Input::get('mgitdst_2dlm'),
                        'mgitdst_2levo' => Input::get('mgitdst_2levo'),
                        'mgitdst_2lzd' => Input::get('mgitdst_2lzd'),
                        'lpa1_done' => Input::get('lpa1_done'),
                        'lpa1_date1' => Input::get('lpa1_date1'),
                        'lpa1_mtbdetected' => Input::get('lpa1_mtbdetected'),
                        'lpaa1dst_rif' => Input::get('lpaa1dst_rif'),
                        'lpa1dst_inh' => Input::get('lpa1dst_inh'),
                        'lpa2_done' => Input::get('lpa2_done'),
                        'lpa2_date' => Input::get('lpa2_date'),
                        'lpa2_mtbdetected' => Input::get('lpa2_mtbdetected'),
                        'lpa2dst_lfx' => Input::get('lpa2dst_lfx'),
                        'lpa2dst_ag_cp' => Input::get('lpa2dst_ag_cp'),
                        'lpa2dstag_lowkan' => Input::get('lpa2dstag_lowkan'),
                        'nanop_done' => Input::get('nanop_done'),
                        'pos_control' => Input::get('pos_control'),
                        'neg_control' => Input::get('neg_control'),
                        'sample_control' => Input::get('sample_control'),
                        'internalcontrol' => Input::get('internalcontrol'),
                        'hsp65' => Input::get('hsp65'),
                        'nanopseq_date' => Input::get('nanopseq_date'),
                        'myco_results' => Input::get('myco_results'),
                        'myco_type' => Input::get('myco_type'),
                        'ntm_spp' => Input::get('ntm_spp'),
                        'myco_lineage' => Input::get('myco_lineage'),
                        'nano_rif' => Input::get('nano_rif'),
                        'nano_inh' => Input::get('nano_inh'),
                        'nano_kan' => Input::get('nano_kan'),
                        'nano_mxf' => Input::get('nano_mxf'),
                        'nano_cap' => Input::get('nano_cap'),
                        'nano_emb' => Input::get('nano_emb'),
                        'nano_pza' => Input::get('nano_pza'),
                        'nano_amk' => Input::get('nano_amk'),
                        'nano_bdq' => Input::get('nano_bdq'),
                        'nano_cfz' => Input::get('nano_cfz'),
                        'nano_dlm' => Input::get('nano_dlm'),
                        'nano_eto' => Input::get('nano_eto'),
                        'nano_lfx' => Input::get('nano_lfx'),
                        'nano_lzd' => Input::get('nano_lzd'),
                        'nano_pmd' => Input::get('nano_pmd'),
                        'nano_stm' => Input::get('nano_stm'),
                        'form_completness' => Input::get('form_completness'),
                        'date_completed' => Input::get('date_completed'),
                        'status' => 1,
                        // 'patient_id' => $individual['id'],
                        'create_on' => date('Y-m-d H:i:s'),
                        'staff_id' => $user->data()->id,
                        'update_on' => date('Y-m-d H:i:s'),
                        'update_id' => $user->data()->id,
                        'site_id' => Input::get('h_facil'),
                    ));

                    $successMessage = 'Validations Data  Successful Added';
                }

                Redirect::to('info.php?id=16' . '&status=' . $_GET['status']);
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_visit')) {
            $validate = $validate->check($_POST, array(
                'visit_date' => array(
                    'required' => true,
                ),
            ));

            if ($validate->passed()) {
                $user->updateRecord('visit', array(
                    'visit_date' => Input::get('visit_date'),
                    'visit_status' => Input::get('visit_status'),
                    'comments' => Input::get('comments'),
                    'status' => 1,
                    'patient_id' => Input::get('cid'),
                    'update_on' => date('Y-m-d H:i:s'),
                    'update_id' => $user->data()->id,
                ), Input::get('id'));

                $successMessage = 'Visit Updates  Successful';
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_region')) {
            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $regions = $override->get('regions', 'id', $_GET['region_id']);
                    if ($regions) {
                        $user->updateRecord('regions', array(
                            'name' => Input::get('name'),
                        ), $_GET['region_id']);
                        $successMessage = 'Region Successful Updated';
                    } else {
                        $user->createRecord('regions', array(
                            'name' => Input::get('name'),
                            'status' => 1,
                        ));
                        $successMessage = 'Region Successful Added';
                    }
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_district')) {
            $validate = $validate->check($_POST, array(
                'region_id' => array(
                    'required' => true,
                ),
                'name' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $districts = $override->get('districts', 'id', $_GET['district_id']);
                    if ($districts) {
                        $user->updateRecord('districts', array(
                            'region_id' => $_GET['region_id'],
                            'name' => Input::get('name'),
                        ), $_GET['district_id']);
                        $successMessage = 'District Successful Updated';
                    } else {
                        $user->createRecord('districts', array(
                            'region_id' => Input::get('region_id'),
                            'name' => Input::get('name'),
                            'status' => 1,
                        ));
                        $successMessage = 'District Successful Added';
                    }
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_ward')) {
            $validate = $validate->check($_POST, array(
                'region_id' => array(
                    'required' => true,
                ),
                'district_id' => array(
                    'required' => true,
                ),
                'name' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $wards = $override->get('wards', 'id', $_GET['ward_id']);
                    if ($wards) {
                        $user->updateRecord('wards', array(
                            'region_id' => $_GET['region_id'],
                            'district_id' => $_GET['district_id'],
                            'name' => Input::get('name'),
                        ), $_GET['ward_id']);
                        $successMessage = 'Ward Successful Updated';
                    } else {
                        $user->createRecord('wards', array(
                            'region_id' => Input::get('region_id'),
                            'district_id' => Input::get('district_id'),
                            'name' => Input::get('name'),
                            'status' => 1,
                        ));
                        $successMessage = 'Ward Successful Added';
                    }
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
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dream Fund Sub-Studies Database | Add Page</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <!-- daterange picker -->
    <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
    <!-- iCheck for checkboxes and radio inputs -->
    <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Bootstrap Color Picker -->
    <link rel="stylesheet" href="plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <!-- Bootstrap4 Duallistbox -->
    <link rel="stylesheet" href="plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css">
    <!-- BS Stepper -->
    <link rel="stylesheet" href="plugins/bs-stepper/css/bs-stepper.min.css">
    <!-- dropzonejs -->
    <link rel="stylesheet" href="plugins/dropzone/min/dropzone.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/adminlte.min.css">

    <style>
        .afb-section {
            border: 2px solid #000;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        .afb-header {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .afb-subheader {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .is-invalid {
            border: 2px solid red !important;
            background-color: #ffe6e6;
            /* Light red background for error indication */
        }
    </style>
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
        <?php } elseif ($successMessage) { ?>
                <div class="alert alert-success text-center">
                    <h4>Success!</h4>
                    <?= $successMessage ?>
                </div>
        <?php } ?>

        <?php if ($_GET['id'] == 1 && ($user->data()->position == 1 || $user->data()->position == 2)) { ?>
                <!-- Content Wrapper. Contains page content -->
                <div class="content-wrapper">
                    <!-- Content Header (Page header) -->
                    <section class="content-header">
                        <div class="container-fluid">
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <h1>Add New Staff</h1>
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
                                        <li class="breadcrumb-item active">Add New Staff</li>
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
                                $staff = $override->getNews('user', 'status', 1, 'id', $_GET['staff_id'])[0];
                                $site = $override->get('sites', 'id', $staff['site_id'])[0];
                                $position = $override->get('position', 'id', $staff['position'])[0];
                                ?>
                                <!-- right column -->
                                <div class="col-md-12">
                                    <!-- general form elements disabled -->
                                    <div class="card card-warning">
                                        <div class="card-header">
                                            <h3 class="card-title">Client Details</h3>
                                        </div>
                                        <!-- /.card-header -->
                                        <form id="validation" enctype="multipart/form-data" method="post" autocomplete="off">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>First Name</label>
                                                                <input class="form-control" type="text" name="firstname"
                                                                    id="firstname" value="<?php if ($staff['firstname']) {
                                                                        print_r($staff['firstname']);
                                                                    } ?>" required />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Middle Name</label>
                                                                <input class="form-control" type="text" name="middlename"
                                                                    id="middlename" value="<?php if ($staff['middlename']) {
                                                                        print_r($staff['middlename']);
                                                                    } ?>" required />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Last Name</label>
                                                                <input class="form-control" type="text" name="lastname"
                                                                    id="lastname" value="<?php if ($staff['lastname']) {
                                                                        print_r($staff['lastname']);
                                                                    } ?>" required />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>User Name</label>
                                                                <input class="form-control" type="text" name="username"
                                                                    id="username" value="<?php if ($staff['username']) {
                                                                        print_r($staff['username']);
                                                                    } ?>" required />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="card card-warning">
                                                    <div class="card-header">
                                                        <h3 class="card-title">Staff Contacts</h3>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>Phone Number</label>
                                                                <input class="form-control" type="tel" pattern=[0]{1}[0-9]{9}
                                                                    minlength="10" maxlength="10" name="phone_number"
                                                                    id="phone_number" value="<?php if ($staff['phone_number']) {
                                                                        print_r($staff['phone_number']);
                                                                    } ?>" required /> <span>Example: 0700 000 111</span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>Phone Number 2</label>
                                                                <input class="form-control" type="tel" pattern=[0]{1}[0-9]{9}
                                                                    minlength="10" maxlength="10" name="phone_number2"
                                                                    id="phone_number2" value="<?php if ($staff['phone_number2']) {
                                                                        print_r($staff['phone_number2']);
                                                                    } ?>" />
                                                                <span>Example: 0700 000 111</span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>E-mail Address</label>
                                                                <input class="form-control" type="email" name="email_address"
                                                                    id="email_address" value="<?php if ($staff['email_address']) {
                                                                        print_r($staff['email_address']);
                                                                    } ?>" required />
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>SEX</label>
                                                                <select class="form-control" name="sex" style="width: 100%;"
                                                                    required>
                                                                    <option value="<?= $staff['sex'] ?>"><?php if ($staff['sex']) {
                                                                          if ($staff['sex'] == 1) {
                                                                              echo 'Male';
                                                                          } elseif ($staff['sex'] == 2) {
                                                                              echo 'Female';
                                                                          }
                                                                      } else {
                                                                          echo 'Select';
                                                                      } ?></option>
                                                                    <option value="1">Male</option>
                                                                    <option value="2">Female</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="card card-warning">
                                                    <div class="card-header">
                                                        <h3 class="card-title">Staff Location And Access Levels</h3>
                                                    </div>
                                                </div>
                                                <div class="row">

                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Site</label>
                                                                <select class="form-control" name="site_id" style="width: 100%;"
                                                                    required>
                                                                    <option value="<?= $site['id'] ?>"><?php if ($staff['site_id']) {
                                                                          print_r($site['name']);
                                                                      } else {
                                                                          echo 'Select';
                                                                      } ?>
                                                                    </option>
                                                                    <?php foreach ($override->getData('sites') as $site) { ?>
                                                                            <option value="<?= $site['id'] ?>"><?= $site['name'] ?>
                                                                            </option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Position</label>
                                                                <select class="form-control" name="position"
                                                                    style="width: 100%;" required>
                                                                    <option value="<?= $position['id'] ?>"><?php if ($staff['position']) {
                                                                          print_r($position['name']);
                                                                      } else {
                                                                          echo 'Select';
                                                                      } ?>
                                                                    </option>
                                                                    <?php foreach ($override->get('position', 'status', 1) as $position) { ?>
                                                                            <option value="<?= $position['id'] ?>">
                                                                                <?= $position['name'] ?>
                                                                            </option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Access Level</label>
                                                                <input class="form-control" type="number" min="0" max="3"
                                                                    name="accessLevel" id="accessLevel" value="<?php if ($staff['accessLevel']) {
                                                                        print_r($staff['accessLevel']);
                                                                    } ?>" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Power</label>
                                                                <input class="form-control" type="number" min="0" max="2"
                                                                    name="power" id="power" value="0" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /.card-body -->
                                            <div class="card-footer">
                                                <a href="info.php?id=1" class="btn btn-default">Back</a>
                                                <input type="submit" name="add_user" value="Submit" class="btn btn-primary">
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
        <?php } elseif ($_GET['id'] == 2) { ?>
                <?php
                $sites = $override->getNews('sites', 'status', 1, 'id', $_GET['site_id'])[0];
                ?>
                <!-- Content Wrapper. Contains page content -->
                <div class="content-wrapper">
                    <!-- Content Header (Page header) -->
                    <section class="content-header">
                        <div class="container-fluid">
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <?php if ($sites) { ?>
                                            <h1>Add New Site</h1>
                                    <?php } else { ?>
                                            <h1>Update Site</h1>
                                    <?php } ?>
                                </div>
                                <div class="col-sm-6">
                                    <ol class="breadcrumb float-sm-right">
                                        <li class="breadcrumb-item">
                                            <a
                                                href="info.php?id=12&site_id=<?= $_GET['site_id']; ?>&status=<?= $_GET['status']; ?>">
                                                < Back</a>
                                        </li>&nbsp;&nbsp;
                                        <li class="breadcrumb-item">
                                            <a href="index1.php">Home</a>
                                        </li>&nbsp;&nbsp;
                                        <li class="breadcrumb-item">
                                            <a href="info.php?id=11&status=<?= $_GET['status']; ?>">
                                                Go to sites list > </a>
                                        </li>&nbsp;&nbsp;
                                        <?php if ($sites) { ?>
                                                <li class="breadcrumb-item active">Add New Site</li>
                                        <?php } else { ?>
                                                <li class="breadcrumb-item active">Update Site</li>
                                        <?php } ?>
                                    </ol>
                                </div>
                            </div>
                        </div><!-- /.container-fluid -->
                    </section>

                    <!-- Main content -->
                    <section class="content">
                        <div class="container-fluid">
                            <div class="row">
                                <!-- right column -->
                                <div class="col-md-12">
                                    <!-- general form elements disabled -->
                                    <div class="card card-warning">
                                        <div class="card-header">
                                            <h3 class="card-title">Name</h3>
                                        </div>
                                        <!-- /.card-header -->
                                        <form id="validation" enctype="multipart/form-data" method="post" autocomplete="off">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <div class="mb-2">
                                                            <label for="name" class="form-label">Name</label>
                                                            <input type="text" value="<?php if ($sites['name']) {
                                                                print_r($sites['name']);
                                                            } ?>" id="name" name="name" class="form-control"
                                                                placeholder="Enter here name" required />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /.card-body -->
                                            <div class="card-footer">
                                                <a href="info.php?id=2&status=<?= $_GET['status']; ?>"
                                                    class="btn btn-default">Back</a>
                                                <input type="submit" name="add_sites" value="Submit" class="btn btn-primary">
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
        <?php } elseif ($_GET['id'] == 3) { ?>
                <?php
                $sites = $override->getNews('sites', 'status', 1, 'id', $_GET['site_id'])[0];
                $regions = $override->get('regions', 'id', $_GET['region_id']);
                $districts = $override->getNews('districts', 'region_id', $_GET['region_id'], 'id', $_GET['district_id']);
                $wards = $override->get('wards', 'id', $_GET['ward_id']);
                // print_r($regions)
                ?>
                <!-- Content Wrapper. Contains page content -->
                <div class="content-wrapper">
                    <!-- Content Header (Page header) -->
                    <section class="content-header">
                        <div class="container-fluid">
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <?php if ($sites) { ?>
                                            <h1>Add New Site</h1>
                                    <?php } else { ?>
                                            <h1>Update Site</h1>
                                    <?php } ?>
                                </div>
                                <div class="col-sm-6">
                                    <ol class="breadcrumb float-sm-right">
                                        <li class="breadcrumb-item">
                                            <a
                                                href="info.php?id=12&site_id=<?= $_GET['site_id']; ?>&status=<?= $_GET['status']; ?>">
                                                < Back</a>
                                        </li>&nbsp;&nbsp;
                                        <li class="breadcrumb-item">
                                            <a href="index1.php">Home</a>
                                        </li>&nbsp;&nbsp;
                                        <li class="breadcrumb-item">
                                            <a href="info.php?id=11&status=<?= $_GET['status']; ?>">
                                                Go to Facilities list > </a>
                                        </li>&nbsp;&nbsp;
                                        <?php if ($sites) { ?>
                                                <li class="breadcrumb-item active">Add New Site</li>
                                        <?php } else { ?>
                                                <li class="breadcrumb-item active">Update Site</li>
                                        <?php } ?>
                                    </ol>
                                </div>
                            </div>
                        </div><!-- /.container-fluid -->
                    </section>

                    <!-- Main content -->
                    <section class="content">
                        <div class="container-fluid">
                            <div class="row">
                                <!-- right column -->
                                <div class="col-md-12">
                                    <!-- general form elements disabled -->
                                    <div class="card card-warning">
                                        <div class="card-header">
                                            <h3 class="card-title">Name & Date</h3>
                                        </div>
                                        <!-- /.card-header -->
                                        <form id="validation" enctype="multipart/form-data" method="post" autocomplete="off">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <div class="mb-2">
                                                            <label for="entry_date" class="form-label">Date of Entry</label>
                                                            <input type="date" value="<?php if ($sites['entry_date']) {
                                                                print_r($sites['entry_date']);
                                                            } ?>" id="entry_date" name="entry_date" class="form-control"
                                                                placeholder="Enter date" required />
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="mb-2">
                                                            <label for="name" class="form-label">Name</label>
                                                            <input type="text" value="<?php if ($sites['name']) {
                                                                print_r($sites['name']);
                                                            } ?>" id="name" name="name" class="form-control"
                                                                placeholder="Enter here name" required />
                                                        </div>
                                                    </div>
                                                </div>

                                                <hr>

                                                <div class="card card-warning">
                                                    <div class="card-header">
                                                        <h3 class="card-title">ARMS, LEVEL , TYPE & CATEGORY</h3>
                                                    </div>
                                                </div>

                                                <hr>

                                                <div class="row">
                                                    <div class="col-sm-2">
                                                        <label for="arm" class="form-label">Arm</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('facility_arm', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio" name="arm"
                                                                                id="arm<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($sites['arm'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>
                                                                                required>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <label for="level" class="form-label">Level</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('facility_level', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio" name="level"
                                                                                id="level<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($sites['level'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>
                                                                                required>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <label for="type" class="form-label">Type</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('facility_type', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio" name="type"
                                                                                id="type<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($sites['type'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>
                                                                                required>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <label for="category" class="form-label">Category</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('facility_category', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio" name="category"
                                                                                id="category<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($sites['category'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?> required>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <label for="category" class="form-label">Respondent Type</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->getNews('respondent_type', 'status', 1, 'respondent', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="respondent" id="respondent<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($sites['respondent'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?> required>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="card card-warning">
                                                    <div class="card-header">
                                                        <h3 class="card-title">Adress</h3>
                                                    </div>
                                                </div>

                                                <hr>

                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Region</label>
                                                                <select id="region" name="region" class="form-control" required>
                                                                    <option value="<?= $regions['id'] ?>"><?php if ($sites['region']) {
                                                                          print_r($regions[0]['name']);
                                                                      } else {
                                                                          echo 'Select region';
                                                                      } ?>
                                                                    </option>
                                                                    <?php foreach ($override->get('regions', 'status', 1) as $region) { ?>
                                                                            <option value="<?= $region['id'] ?>"><?= $region['name'] ?>
                                                                            </option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-4">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>District</label>
                                                                <select id="district" name="district" class="form-control"
                                                                    required>
                                                                    <option value="<?= $districts['id'] ?>"><?php if ($sites['district']) {
                                                                          print_r($districts[0]['name']);
                                                                      } else {
                                                                          echo 'Select district';
                                                                      } ?>
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-4">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Ward</label>
                                                                <select id="ward" name="ward" class="form-control" required>
                                                                    <option value="<?= $wards['id'] ?>"><?php if ($sites['ward']) {
                                                                          print_r($wards[0]['name']);
                                                                      } else {
                                                                          echo 'Select district';
                                                                      } ?>
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                            </div>
                                            <!-- /.card-body -->
                                            <div class="card-footer">
                                                <a href="info.php?id=11&site_id=<?= $sites['id'] ?>&status=<?= $_GET['status']; ?>"
                                                    class="btn btn-default">Back</a>
                                                <input type="hidden" name="site_id" value="<?= $sites['id'] ?>">
                                                <input type="submit" name="add_site" value="Submit" class="btn btn-primary">
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
        <?php } elseif ($_GET['id'] == 4) { ?>
                <!-- Content Wrapper. Contains page content -->
                <div class="content-wrapper">
                    <!-- Content Header (Page header) -->
                    <section class="content-header">
                        <div class="container-fluid">
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <h1>Add Participant Eliginility form</h1>
                                </div>
                                <div class="col-sm-6">
                                    <ol class="breadcrumb float-sm-right">
                                        <li class="breadcrumb-item"><a href="info.php?id=3&status=<?= $_GET['status']; ?>">
                                                < Back</a>
                                        </li>&nbsp;&nbsp;
                                        <li class="breadcrumb-item"><a href="index1.php">Home</a></li>&nbsp;&nbsp;
                                        <li class="breadcrumb-item">
                                            <a href="info.php?id=3&status=<?= $_GET['status']; ?>">
                                                <?php if ($_GET['status'] == 1) { ?>
                                                        Go to screening list >
                                                <?php } elseif ($_GET['status'] == 2) { ?>
                                                        Go to eligible list >
                                                <?php } elseif ($_GET['status'] == 3) { ?>
                                                        Go to enrollment list >
                                                <?php } ?>
                                            </a>
                                        </li>&nbsp;&nbsp;
                                        <li class="breadcrumb-item active">Add New Patient</li>
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
                                $clients = $override->getNews('screening', 'status', 1, 'id', $_GET['cid'])[0];
                                $sex = $override->get('sex', 'id', $clients['sex'])[0];
                                $education = $override->get('education', 'id', $clients['education'])[0];
                                $occupation = $override->get('occupation', 'id', $clients['occupation'])[0];
                                $regions = $override->get('regions', 'id', $clients['region'])[0];
                                $districts = $override->get('districts', 'id', $clients['district'])[0];
                                $wards = $override->get('wards', 'id', $clients['ward'])[0];
                                $facility = $override->get('districts', 'id', $clients['facility_district'])[0];
                                $site = $override->get('sites', 'id', $clients['facility_id'])[0];


                                // $screening = $override->get3('screening', 'status', 1, 'sequence', $_GET['sequence'], 'patient_id', $_GET['cid'])[0];
                            
                                ?>
                                <!-- right column -->
                                <div class="col-md-12">
                                    <!-- general form elements disabled -->
                                    <div class="card card-warning">
                                        <div class="card-header">
                                            <h3 class="card-title">Screening Details</h3>
                                        </div>
                                        <!-- /.card-header -->
                                        <form id="clients" enctype="multipart/form-data" method="post" autocomplete="off">
                                            <div class="card-body">
                                                <hr>
                                                <div class="card card-warning">
                                                    <div class="card-header">
                                                        <h3 class="card-title">PID : <?php print_r($clients['study_id']); ?>
                                                        </h3>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-2">
                                                        <div class="mb-2">
                                                            <label for="test_date" class="form-label">Date of Screening</label>
                                                            <input type="date" value="<?php if ($clients['screening_date']) {
                                                                print_r($clients['screening_date']);
                                                            } ?>" id="screening_date" name="screening_date"
                                                                class="form-control" placeholder="Enter date" required />
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-2">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Date of birth:</label>
                                                                <input class="form-control" max="<?= date('Y-m-d'); ?>"
                                                                    type="date" name="dob" id="dob" style="width: 100%;" value="<?php if ($clients['dob']) {
                                                                        print_r($clients['dob']);
                                                                    } ?>" required />
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-2">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Age</label>
                                                                <input class="form-control" type="number" name="age" value="<?php if ($clients['age']) {
                                                                    print_r($clients['age']);
                                                                } ?>" readonly />
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-2">
                                                        <label>SEX</label>
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio" name="sex"
                                                                        id="sex" value="1" <?php if ($clients['sex'] == 1) {
                                                                            echo 'checked';
                                                                        } ?> required>
                                                                    <label class="form-check-label">Male</label>
                                                                </div>

                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio" name="sex"
                                                                        id="sex" value="2" <?php if ($clients['sex'] == 2) {
                                                                            echo 'checked';
                                                                        } ?>>
                                                                    <label class="form-check-label">Female</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-2">
                                                        <label for="conset" class="form-label">Patient Conset?</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('yes_no', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio" name="conset"
                                                                                id="conset<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($clients['conset'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?> required>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-2" id="conset_date1">
                                                        <div class="mb-2">
                                                            <label for="results_date" class="form-label">Date of Conset</label>
                                                            <input type="date" value="<?php if ($clients['conset_date']) {
                                                                print_r($clients['conset_date']);
                                                            } ?>" id="conset_date" name="conset_date" class="form-control"
                                                                placeholder="Enter date" />
                                                        </div>
                                                    </div>
                                                </div>


                                                <hr>

                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Name of Health facility:</label>
                                                                <select name="site" class="form-control" required>
                                                                    <option value="<?= $site['id'] ?>"><?php if ($clients['facility_id']) {
                                                                          print_r($site['name']);
                                                                      } else {
                                                                          echo 'Select';
                                                                      } ?>
                                                                    </option>
                                                                    <?php foreach ($override->get('sites', 'status', 1) as $district) { ?>
                                                                            <option value="<?= $district['id'] ?>">
                                                                                <?= $district['name'] ?>
                                                                            </option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Facility District</label>
                                                                <select name="facility_district" class="form-control" required>
                                                                    <option value="<?= $facility['id'] ?>"><?php if ($clients['facility_district']) {
                                                                          print_r($facility['name']);
                                                                      } else {
                                                                          echo 'Select district';
                                                                      } ?>
                                                                    </option>
                                                                    <?php foreach ($override->get('districts', 'status', 1) as $district) { ?>
                                                                            <option value="<?= $district['id'] ?>">
                                                                                <?= $district['name'] ?>
                                                                            </option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <hr>

                                                <div class="card card-warning">
                                                    <div class="card-header">
                                                        <h3 class="card-title">Patient’s Residence address </h3>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Region</label>
                                                                <select id="region" name="region" class="form-control" required>
                                                                    <option value="<?= $regions['id'] ?>"><?php if ($clients['region']) {
                                                                          print_r($regions['name']);
                                                                      } else {
                                                                          echo 'Select region';
                                                                      } ?>
                                                                    </option>
                                                                    <?php foreach ($override->get('regions', 'status', 1) as $region) { ?>
                                                                            <option value="<?= $region['id'] ?>"><?= $region['name'] ?>
                                                                            </option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-4">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>District</label>
                                                                <select id="district" name="district" class="form-control"
                                                                    required>
                                                                    <option value="<?= $districts['id'] ?>"><?php if ($clients['district']) {
                                                                          print_r($districts['name']);
                                                                      } else {
                                                                          echo 'Select district';
                                                                      } ?>
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-4">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Ward</label>
                                                                <select id="ward" name="ward" class="form-control" required>
                                                                    <option value="<?= $wards['id'] ?>"><?php if ($clients['ward']) {
                                                                          print_r($wards['name']);
                                                                      } else {
                                                                          echo 'Select district';
                                                                      } ?>
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <hr>

                                                <div class="row">

                                                    <div class="col-md-12">
                                                        <div class="card card-warning">
                                                            <div class="card-header">
                                                                <h3 class="card-title">ANY OTHER COMENT OR REMARKS</h3>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">

                                                    <div class="col-sm-12">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>Remarks / Comments:</label>
                                                                <textarea class="form-control" name="comments" rows="3"
                                                                    placeholder="Type comments here..."><?php if ($clients['comments']) {
                                                                        print_r($clients['comments']);
                                                                    } ?></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                            </div>
                                            <!-- /.card-body -->
                                            <div class="card-footer">
                                                <a href="info.php?id=3&status=<?= $_GET['status']; ?>"
                                                    class="btn btn-default">Back</a>
                                                <input type="submit" name="add_client" value="Submit" class="btn btn-primary">
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
        <?php } elseif ($_GET['id'] == 5) { ?>
                <?php
                $sites = $override->getNews('position', 'status', 1, 'id', $_GET['position_id'])[0];
                ?>
                <!-- Content Wrapper. Contains page content -->
                <div class="content-wrapper">
                    <!-- Content Header (Page header) -->
                    <section class="content-header">
                        <div class="container-fluid">
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <?php if ($sites) { ?>
                                            <h1>Add New positions</h1>
                                    <?php } else { ?>
                                            <h1>Update positions</h1>
                                    <?php } ?>
                                </div>
                                <div class="col-sm-6">
                                    <ol class="breadcrumb float-sm-right">
                                        <li class="breadcrumb-item">
                                            <a
                                                href="info.php?id=12&site_id=<?= $_GET['site_id']; ?>&status=<?= $_GET['status']; ?>">
                                                < Back</a>
                                        </li>&nbsp;&nbsp;
                                        <li class="breadcrumb-item">
                                            <a href="index1.php">Home</a>
                                        </li>&nbsp;&nbsp;
                                        <li class="breadcrumb-item">
                                            <a href="info.php?id=11&status=<?= $_GET['status']; ?>">
                                                Go to positions list > </a>
                                        </li>&nbsp;&nbsp;
                                        <?php if ($sites) { ?>
                                                <li class="breadcrumb-item active">Add New positions</li>
                                        <?php } else { ?>
                                                <li class="breadcrumb-item active">Update positions</li>
                                        <?php } ?>
                                    </ol>
                                </div>
                            </div>
                        </div><!-- /.container-fluid -->
                    </section>

                    <!-- Main content -->
                    <section class="content">
                        <div class="container-fluid">
                            <div class="row">
                                <!-- right column -->
                                <div class="col-md-12">
                                    <!-- general form elements disabled -->
                                    <div class="card card-warning">
                                        <div class="card-header">
                                            <h3 class="card-title">Name</h3>
                                        </div>
                                        <!-- /.card-header -->
                                        <form id="validation" enctype="multipart/form-data" method="post" autocomplete="off">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <div class="mb-2">
                                                            <label for="name" class="form-label">Name</label>
                                                            <input type="text" value="<?php if ($sites['name']) {
                                                                print_r($sites['name']);
                                                            } ?>" id="name" name="name" class="form-control"
                                                                placeholder="Enter here name" required />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /.card-body -->
                                            <div class="card-footer">
                                                <a href="info.php?id=5" class="btn btn-default">Back</a>
                                                <input type="submit" name="add_positions" value="Submit"
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
        <?php } elseif ($_GET['id'] == 7) { ?>

        <?php } elseif ($_GET['id'] == 8) { ?>
                <!-- Content Wrapper. Contains page content -->
                <div class="content-wrapper">
                    <!-- Content Header (Page header) -->
                    <section class="content-header">
                        <div class="container-fluid">
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <h1>Region Form</h1>
                                </div>
                                <div class="col-sm-6">
                                    <ol class="breadcrumb float-sm-right">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active">Region Form</li>
                                    </ol>
                                </div>
                            </div>
                        </div><!-- /.container-fluid -->
                    </section>

                    <!-- Main content -->
                    <section class="content">
                        <div class="container-fluid">
                            <div class="row">
                                <?php $regions = $override->get('regions', 'id', $_GET['region_id']); ?>
                                <!-- right column -->
                                <div class="col-md-6">
                                    <!-- general form elements disabled -->
                                    <div class="card card-warning">
                                        <div class="card-header">
                                            <h3 class="card-title">Region</h3>
                                        </div>
                                        <!-- /.card-header -->
                                        <form id="validation" enctype="multipart/form-data" method="post" autocomplete="off">
                                            <div class="card-body">
                                                <hr>
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Region</label>
                                                                <input class="form-control" type="text" name="name" id="name"
                                                                    placeholder="Type region..." onkeyup="fetchData()" value="<?php if ($regions['0']['name']) {
                                                                        print_r($regions['0']['name']);
                                                                    } ?>" required />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /.card-body -->
                                            <div class="card-footer">
                                                <a href='index1.php' class="btn btn-default">Back</a>
                                                <input type="hidden" name="region_id" value="<?= $regions['0']['id'] ?>">
                                                <input type="submit" name="add_region" value="Submit" class="btn btn-primary">
                                            </div>
                                        </form>
                                    </div>
                                    <!-- /.card -->
                                </div>
                                <!--/.col (left) -->

                                <div class="col-6">
                                    <div class="card">
                                        <section class="content-header">
                                            <div class="container-fluid">
                                                <div class="row mb-2">
                                                    <div class="col-sm-6">
                                                        <div class="card-header">
                                                            List of Regions
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
                                        <?php
                                        $pagNum = 0;
                                        $pagNum = $override->getCount('regions', 'status', 1);
                                        $pages = ceil($pagNum / $numRec);
                                        if (!$_GET['page'] || $_GET['page'] == 1) {
                                            $page = 0;
                                        } else {
                                            $page = ($_GET['page'] * $numRec) - $numRec;
                                        }
                                        $regions = $override->getWithLimit('regions', 'status', 1, $page, $numRec);
                                        ?>
                                        <!-- /.card-header -->
                                        <div class="card-body">
                                            <table id="example1" class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Region Name</th>
                                                        <th>Status</th>
                                                        <th class="text-center">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $x = 1;
                                                    foreach ($regions as $value) {
                                                        $regions = $override->get('regions', 'id', $value['region_id'])[0];
                                                        ?>
                                                            <tr>
                                                                <td class="table-user">
                                                                    <?= $x; ?>
                                                                </td>
                                                                <td class="table-user">
                                                                    <?= $value['name']; ?>
                                                                </td>

                                                                <?php if ($value['status'] == 1) { ?>
                                                                        <td class="text-center">
                                                                            <a href="#" class="btn btn-success">
                                                                                <i class="ri-edit-box-line">
                                                                                </i>Active
                                                                            </a>
                                                                        </td>
                                                                <?php } else { ?>
                                                                        <td class="text-center">
                                                                            <a href="#" class="btn btn-success">
                                                                                <i class="ri-edit-box-line">
                                                                                </i>Not Active
                                                                            </a>
                                                                        </td>
                                                                <?php } ?>
                                                                <td>
                                                                    <a href="add.php?id=24&region_id=<?= $value['id'] ?>"
                                                                        class="btn btn-info">Update</a>
                                                                    <?php if ($user->data()->power == 1) { ?>
                                                                            <a href="#delete<?= $staff['id'] ?>" role="button"
                                                                                class="btn btn-danger" data-toggle="modal">Delete</a>
                                                                            <a href="#restore<?= $staff['id'] ?>" role="button"
                                                                                class="btn btn-secondary" data-toggle="modal">Restore</a>
                                                                    <?php } ?>
                                                                </td>
                                                            </tr>
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
                                                        <th>Region Name</th>
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
                                                    <a class="page-link" href="add.php?id=24&page=<?php if (($_GET['page'] - 1) > 0) {
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
                                                            } ?>" href="add.php?id=24&page=<?= $i ?>"><?= $i ?>
                                                            </a>
                                                        </li>
                                                <?php } ?>
                                                <li class="page-item">
                                                    <a class="page-link" href="add.php?id=24&page=<?php if (($_GET['page'] + 1) <= $pages) {
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
        <?php } elseif ($_GET['id'] == 9) { ?>
                <!-- Content Wrapper. Contains page content -->
                <div class="content-wrapper">
                    <!-- Content Header (Page header) -->
                    <section class="content-header">
                        <div class="container-fluid">
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <h1>District Form</h1>
                                </div>
                                <div class="col-sm-6">
                                    <ol class="breadcrumb float-sm-right">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active">District Form</li>
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
                                $regions = $override->get('regions', 'id', $_GET['region_id']);
                                $districts = $override->get('districts', 'id', $_GET['district_id']);
                                ?>
                                <!-- right left -->
                                <div class="col-md-6">
                                    <!-- general form elements disabled -->
                                    <div class="card card-warning">
                                        <div class="card-header">
                                            <h3 class="card-title">District</h3>
                                        </div>
                                        <!-- /.card-header -->
                                        <form id="validation" enctype="multipart/form-data" method="post" autocomplete="off">
                                            <div class="card-body">
                                                <hr>
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Region</label>
                                                                <select id="region_id" name="region_id" class="form-control"
                                                                    required <?php if ($_GET['region_id']) {
                                                                        echo 'disabled';
                                                                    } ?>>
                                                                    <option value="<?= $regions[0]['id'] ?>"><?php if ($regions[0]['name']) {
                                                                          print_r($regions[0]['name']);
                                                                      } else {
                                                                          echo 'Select region';
                                                                      } ?>
                                                                    </option>
                                                                    <?php foreach ($override->get('regions', 'status', 1) as $region) { ?>
                                                                            <option value="<?= $region['id'] ?>"><?= $region['name'] ?>
                                                                            </option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-6">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>District Name</label>
                                                                <input class="form-control" type="text" name="name" id="name"
                                                                    placeholder="Type district..." onkeyup="fetchData()" value="<?php if ($districts['0']['name']) {
                                                                        print_r($districts['0']['name']);
                                                                    } ?>" required />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /.card-body -->
                                            <div class="card-footer">
                                                <a href='index1.php' class="btn btn-default">Back</a>
                                                <input type="submit" name="add_district" value="Submit" class="btn btn-primary">
                                            </div>
                                        </form>
                                    </div>
                                    <!-- /.card -->
                                </div>
                                <!--/.col (left) -->

                                <div class="col-6">
                                    <div class="card">
                                        <section class="content-header">
                                            <div class="container-fluid">
                                                <div class="row mb-2">
                                                    <div class="col-sm-6">
                                                        <div class="card-header">
                                                            List of Districts
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
                                        <?php
                                        $pagNum = 0;
                                        $pagNum = $override->getCount('districts', 'status', 1);
                                        $pages = ceil($pagNum / $numRec);
                                        if (!$_GET['page'] || $_GET['page'] == 1) {
                                            $page = 0;
                                        } else {
                                            $page = ($_GET['page'] * $numRec) - $numRec;
                                        }

                                        $districts = $override->getWithLimit('districts', 'status', 1, $page, $numRec);
                                        ?>
                                        <!-- /.card-header -->
                                        <div class="card-body">
                                            <table id="example1" class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Region Name</th>
                                                        <th>District Name</th>
                                                        <th>Status</th>
                                                        <th class="text-center">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $x = 1;
                                                    foreach ($districts as $value) {
                                                        $regions = $override->get('regions', 'id', $value['region_id'])[0];
                                                        ?>
                                                            <tr>
                                                                <td class="table-user">
                                                                    <?= $x; ?>
                                                                </td>
                                                                <td class="table-user">
                                                                    <?= $regions['name']; ?>
                                                                </td>

                                                                <td class="table-user">
                                                                    <?= $value['name']; ?>
                                                                </td>

                                                                <?php if ($value['status'] == 1) { ?>
                                                                        <td class="text-center">
                                                                            <a href="#" class="btn btn-success">
                                                                                <i class="ri-edit-box-line">
                                                                                </i>Active
                                                                            </a>
                                                                        </td>
                                                                <?php } else { ?>
                                                                        <td class="text-center">
                                                                            <a href="#" class="btn btn-success">
                                                                                <i class="ri-edit-box-line">
                                                                                </i>Not Active
                                                                            </a>
                                                                        </td>
                                                                <?php } ?>
                                                                <td>
                                                                    <a href="add.php?id=25&region_id=<?= $value['region_id'] ?>&district_id=<?= $value['id'] ?>"
                                                                        class="btn btn-info">Update</a>
                                                                    <?php if ($user->data()->power == 1) { ?>
                                                                            <a href="#delete<?= $staff['id'] ?>" role="button"
                                                                                class="btn btn-danger" data-toggle="modal">Delete</a>
                                                                            <a href="#restore<?= $staff['id'] ?>" role="button"
                                                                                class="btn btn-secondary" data-toggle="modal">Restore</a>
                                                                    <?php } ?>
                                                                </td>
                                                            </tr>
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
                                                        <th>Region Name</th>
                                                        <th>District Name</th>
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
                                                    <a class="page-link" href="add.php?id=25&page=<?php if (($_GET['page'] - 1) > 0) {
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
                                                            } ?>" href="add.php?id=25&page=<?= $i ?>"><?= $i ?>
                                                            </a>
                                                        </li>
                                                <?php } ?>
                                                <li class="page-item">
                                                    <a class="page-link" href="add.php?id=25&page=<?php if (($_GET['page'] + 1) <= $pages) {
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
        <?php } elseif ($_GET['id'] == 10) { ?>
                <!-- Content Wrapper. Contains page content -->
                <div class="content-wrapper">
                    <!-- Content Header (Page header) -->
                    <section class="content-header">
                        <div class="container-fluid">
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <h1>Wards Form</h1>
                                </div>
                                <div class="col-sm-6">
                                    <ol class="breadcrumb float-sm-right">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active">Wards Form</li>
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
                                $regions = $override->get('regions', 'id', $_GET['region_id']);
                                $districts = $override->getNews('districts', 'region_id', $_GET['region_id'], 'id', $_GET['district_id']);
                                $wards = $override->get('wards', 'id', $_GET['ward_id']);
                                ?>
                                <!-- right left -->
                                <div class="col-md-6">
                                    <!-- general form elements disabled -->
                                    <div class="card card-warning">
                                        <div class="card-header">
                                            <h3 class="card-title">Ward</h3>
                                        </div>
                                        <!-- /.card-header -->
                                        <form id="validation" enctype="multipart/form-data" method="post" autocomplete="off">
                                            <div class="card-body">
                                                <hr>
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Region</label>
                                                                <select id="regions_id" name="region_id" class="form-control"
                                                                    required <?php if ($_GET['region_id']) {
                                                                        echo 'disabled';
                                                                    } ?>>
                                                                    <option value="<?= $regions[0]['id'] ?>"><?php if ($regions[0]['name']) {
                                                                          print_r($regions[0]['name']);
                                                                      } else {
                                                                          echo 'Select region';
                                                                      } ?>
                                                                    </option>
                                                                    <?php foreach ($override->get('regions', 'status', 1) as $region) { ?>
                                                                            <option value="<?= $region['id'] ?>"><?= $region['name'] ?>
                                                                            </option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-4">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>District</label>
                                                                <select id="districts_id" name="district_id"
                                                                    class="form-control" required <?php if ($_GET['district_id']) {
                                                                        echo 'disabled';
                                                                    } ?>>
                                                                    <option value="<?= $districts[0]['id'] ?>"><?php if ($districts[0]['name']) {
                                                                          print_r($districts[0]['name']);
                                                                      } else {
                                                                          echo 'Select District';
                                                                      } ?>
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-4">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Ward Name</label>
                                                                <input class="form-control" type="text" name="name" id="name"
                                                                    placeholder="Type ward..." onkeyup="fetchData()" value="<?php if ($wards['0']['name']) {
                                                                        print_r($wards['0']['name']);
                                                                    } ?>" required />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /.card-body -->
                                            <div class="card-footer">
                                                <a href='index1.php' class="btn btn-default">Back</a>
                                                <input type="submit" name="add_ward" value="Submit" class="btn btn-primary">
                                            </div>
                                        </form>
                                    </div>
                                    <!-- /.card -->
                                </div>
                                <!--/.col (left) -->

                                <div class="col-6">
                                    <div class="card">
                                        <section class="content-header">
                                            <div class="container-fluid">
                                                <div class="row mb-2">
                                                    <div class="col-sm-6">
                                                        <div class="card-header">
                                                            List of Wards
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
                                        <?php
                                        $pagNum = 0;
                                        $pagNum = $override->getCount('wards', 'status', 1);
                                        $pages = ceil($pagNum / $numRec);
                                        if (!$_GET['page'] || $_GET['page'] == 1) {
                                            $page = 0;
                                        } else {
                                            $page = ($_GET['page'] * $numRec) - $numRec;
                                        }

                                        $ward = $override->getWithLimit('wards', 'status', 1, $page, $numRec);
                                        ?>
                                        <!-- /.card-header -->
                                        <div class="card-body">
                                            <table id="search-results" class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Region Name</th>
                                                        <th>District Name</th>
                                                        <th>Ward Name</th>
                                                        <th>Status</th>
                                                        <th class="text-center">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $x = 1;
                                                    foreach ($ward as $value) {
                                                        $regions = $override->get('regions', 'id', $value['region_id'])[0];
                                                        $districts = $override->get('districts', 'id', $value['district_id'])[0];
                                                        ?>
                                                            <tr>
                                                                <td class="table-user">
                                                                    <?= $x; ?>
                                                                </td>
                                                                <td class="table-user">
                                                                    <?= $regions['name']; ?>
                                                                </td>

                                                                <td class="table-user">
                                                                    <?= $districts['name']; ?>
                                                                </td>
                                                                <td class="table-user">
                                                                    <?= $value['name']; ?>
                                                                </td>

                                                                <?php if ($value['status'] == 1) { ?>
                                                                        <td class="text-center">
                                                                            <a href="#" class="btn btn-success">
                                                                                <i class="ri-edit-box-line">
                                                                                </i>Active
                                                                            </a>
                                                                        </td>
                                                                <?php } else { ?>
                                                                        <td class="text-center">
                                                                            <a href="#" class="btn btn-success">
                                                                                <i class="ri-edit-box-line">
                                                                                </i>Not Active
                                                                            </a>
                                                                        </td>
                                                                <?php } ?>
                                                                <td>
                                                                    <a href="add.php?id=26&region_id=<?= $value['region_id'] ?>&district_id=<?= $value['district_id'] ?>&ward_id=<?= $value['id'] ?>"
                                                                        class="btn btn-info">Update</a> <br><br>
                                                                    <?php if ($user->data()->power == 1) { ?>
                                                                            <a href="#delete<?= $staff['id'] ?>" role="button"
                                                                                class="btn btn-danger" data-toggle="modal">Delete</a>
                                                                            <a href="#restore<?= $staff['id'] ?>" role="button"
                                                                                class="btn btn-secondary" data-toggle="modal">Restore</a>
                                                                    <?php } ?>
                                                                </td>
                                                            </tr>
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
                                                        <th>Region Name</th>
                                                        <th>District Name</th>
                                                        <th>Ward Name</th>
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
                                                    <a class="page-link" href="add.php?id=26&page=<?php if (($_GET['page'] - 1) > 0) {
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
                                                            } ?>" href="add.php?id=26&page=<?= $i ?>"><?= $i ?>
                                                            </a>
                                                        </li>
                                                <?php } ?>
                                                <li class="page-item">
                                                    <a class="page-link" href="add.php?id=26&page=<?php if (($_GET['page'] + 1) <= $pages) {
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
        <?php } elseif ($_GET['id'] == 11) { ?>
                <?php
                $screening = $override->getNews('screening', 'status', 1, 'id', $_GET['sid'])[0];
                $costing = $override->getNews('respiratory', 'status', 1, 'enrollment_id', $_GET['sid'])[0];
                $lab_name = $override->getNews('sites', 'status', 1, 'id', $user->data()->site_id)[0];
                $enrollment = $override->getNews('enrollment_form', 'status', 1, 'enrollment_id', $_GET['sid'])[0];
                $remarks = $override->getNews('respiratory', 'status', 1, 'enrollment_id', $_GET['sid'])[0];
                ?>
                <!-- Content Wrapper. Contains page content -->
                <div class="content-wrapper">
                    <!-- Content Header (Page header) -->
                    <section class="content-header">
                        <div class="container-fluid">
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <?php if (!$costing) { ?>
                                            <h1>Add sputum (PID :<?= $screening['pid'] ?>)</h1>
                                    <?php } else { ?>
                                            <h1>Update sputum (PID :<?= $screening['pid'] ?>)</h1>
                                    <?php } ?>
                                </div>
                                <div class="col-sm-6">
                                    <ol class="breadcrumb float-sm-right">
                                        <li class="breadcrumb-item"><a
                                                href="info.php?id=6&status=<?= $_GET['status']; ?>&sid=<?= $_GET['sid']; ?>&facility_id=<?= $_GET['facility_id']; ?>&page=<?= $_GET['page']; ?>">
                                                < Back</a>
                                        </li>&nbsp;&nbsp;
                                        <li class="breadcrumb-item"><a href="index1.php">Home</a></li>&nbsp;&nbsp;
                                        <li class="breadcrumb-item"><a
                                                href="info.php?id=6&status=<?= $_GET['status']; ?>&facility_id=<?= $_GET['facility_id']; ?>&page=<?= $_GET['page']; ?>">
                                                Go to Enrollment list > </a>
                                        </li>&nbsp;&nbsp;
                                        <?php if (!$costing) { ?>
                                                <li class="breadcrumb-item active">Add New sputum sample</li>
                                        <?php } else { ?>
                                                <li class="breadcrumb-item active">Update sputum sample</li>
                                        <?php } ?>
                                    </ol>
                                </div>
                            </div>
                        </div><!-- /.container-fluid -->
                    </section>

                    <!-- Main content -->
                    <section class="content">
                        <div class="container-fluid">
                            <div class="row">
                                <!-- right column -->
                                <div class="col-md-12">
                                    <!-- general form elements disabled -->
                                    <div class="card card-warning">
                                        <div class="card-header">
                                            <h3 class="card-title">General information Form</h3>
                                        </div>
                                        <!-- /.card-header -->
                                        <form id="labForm_clinic" enctype="multipart/form-data" method="post"
                                            autocomplete="off">
                                            <div class="card-body">
                                                <hr>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="mb-2">
                                                            <label for="lab_name" class="form-label">1a. Name of
                                                                laboratory / Site</label>
                                                            <input type="text" value="<?= $lab_name['name']; ?>" id="lab_name"
                                                                name="lab_name" class="form-control" placeholder="Enter here"
                                                                readonly />
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="card card-warning">
                                                    <div class="card-header">
                                                        <h3 class="card-title">Sputum sample</h3>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <label for="sample_received" class="form-label">2(a). Is at least one
                                                            sputum sample received?</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('yes_no', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="sample_received"
                                                                                id="sample_received<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['sample_received'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                        <button type="button"
                                                            onclick="unsetRadio('sample_received')">Unset</button>
                                                    </div>

                                                    <div class="col-sm-3" id="sample_reason_section">
                                                        <label for="tested_this_month" class="form-label">2(b). If no what was
                                                            the
                                                            reason?</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('sample_reason', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="sample_reason"
                                                                                id="sample_reason<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['sample_reason'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('sample_reason')">Unset</button>
                                                        </div>
                                                        <input type="text" value="<?php if ($costing['other_reason']) {
                                                            print_r($costing['other_reason']);
                                                        } ?>" id="other_reason" name="other_reason" class="form-control"
                                                            placeholder="If No give reasons here" />
                                                    </div>

                                                    <div class="col-sm-3" id="new_sample_section">
                                                        <label for="new_sample" class="form-label">3(a). was a new sample
                                                            collected?</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('yes_no', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="new_sample" id="new_sample<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['new_sample'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('new_sample')">Unset</button>
                                                        </div>
                                                    </div>

                                                    <div class="col-3" id="other_new_reason_section">
                                                        <div class="mb-3">
                                                            <label class="form-label">3(b) If no new sample collected ,what was
                                                                the reason ?</label>
                                                            <input type="text"
                                                                value="<?php echo $costing['other_new_reason'] ?? ''; ?>"
                                                                id="other_new_reason" name="other_new_reason" min="1" max="5"
                                                                class="form-control"
                                                                placeholder="If No give new sample reasons here" />
                                                        </div>
                                                    </div>

                                                    <div class="col-3" id="number_received_section">
                                                        <div class="mb-3">
                                                            <label for="number_received" class="form-label">4. Number of
                                                                samples received</label>
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('one_two', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="number_received"
                                                                                    id="number_received<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?php if ($costing['number_received'] == $value['id']) {
                                                                                          echo 'checked';
                                                                                      } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('number_received')">Unset</button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <?php
                                                    $enrollment_date = $enrollment['enrollment_date'] ?? '';
                                                    ?>
                                                    <input type="hidden" id="enrollment_date_hidden"
                                                        value="<?php echo $enrollment_date; ?>" />
                                                </div>

                                                <div id="sample_received_section">
                                                    <hr>
                                                    <div class="row">
                                                        <!-- Sample 1 -->
                                                        <div class="col-sm-6 p-3 border rounded" id="sample1_section">
                                                            <h5>Sample 1</h5>
                                                            <div class="mb-3">
                                                                <label class="form-label">5(a). Date sample collected</label>
                                                                <input type="date"
                                                                    value="<?php echo $costing['date_sample1_collected'] ?? ''; ?>"
                                                                    id="date_sample1_collected" name="date_sample1_collected"
                                                                    class="form-control" />
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">5(b). Date sample received</label>
                                                                <input type="date"
                                                                    value="<?php echo $costing['date_sample1_received'] ?? ''; ?>"
                                                                    id="date_sample1_received" name="date_sample1_received"
                                                                    class="form-control" />
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">6. Appearance</label>
                                                                <?php foreach ($override->get('appearance', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="appearance_sample1"
                                                                                id="appearance_sample1<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['appearance_sample1'] == $value['id'])
                                                                                      echo 'checked'; ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                                <button type="button"
                                                                    onclick="unsetRadio('appearance_sample1')">Unset</button>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">7. Sample Volume (mL)</label>
                                                                <input type="number"
                                                                    value="<?php echo $costing['sample1_volume'] ?? ''; ?>"
                                                                    id="sample1_volume" name="sample1_volume" steps="0.1"
                                                                    min="0" max="99" class="form-control" />
                                                            </div>
                                                        </div>

                                                        <!-- Sample 2 -->
                                                        <div class="col-sm-6 p-3 border rounded" id="sample2_section">
                                                            <h5>Sample 2</h5>
                                                            <div class="mb-3">
                                                                <label class="form-label">5(a). Date sample collected</label>
                                                                <input type="date"
                                                                    value="<?php echo $costing['date_sample2_collected'] ?? ''; ?>"
                                                                    id="date_sample2_collected" name="date_sample2_collected"
                                                                    class="form-control" />
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">5(b). Date sample received</label>
                                                                <input type="date"
                                                                    value="<?php echo $costing['date_sample2_received'] ?? ''; ?>"
                                                                    id="date_sample2_received" name="date_sample2_received"
                                                                    class="form-control" />
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">6. Appearance</label>
                                                                <?php foreach ($override->get('appearance', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="appearance_sample2"
                                                                                id="appearance_sample2<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['appearance_sample2'] == $value['id'])
                                                                                      echo 'checked'; ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                                <button type="button"
                                                                    onclick="unsetRadio('appearance_sample2')">Unset</button>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">7. Sample Volume (mL)</label>
                                                                <input type="number"
                                                                    value="<?php echo $costing['sample2_volume'] ?? ''; ?>"
                                                                    id="sample2_volume" name="sample2_volume" steps="0.1"
                                                                    min="0" max="99" class="form-control" />
                                                            </div>
                                                        </div>

                                                        <hr>
                                                    </div>

                                                    <div class="afb-header" id="afb-header">
                                                        <h3><strong>AFB Microscopy</strong></h3>
                                                    </div>
                                                    <hr>

                                                    <div class="row">
                                                        <div class="col-4" id="afb_microscopy_conducted_section">
                                                            <label for="afb_microscopy_conducted_section" class="form-label">8.
                                                                Was AFB microscopy
                                                                conducted at TB clinic?</label>
                                                            <!-- radio -->
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('yes_no', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="afb_microscopy_conducted"
                                                                                    id="afb_microscopy_conducted<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?php if ($costing['afb_microscopy_conducted'] == $value['id']) {
                                                                                          echo 'checked';
                                                                                      } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('afb_microscopy_conducted')">Unset</button>
                                                        </div>
                                                    </div>

                                                    <div id="afb_microscopy_section">
                                                        <hr>
                                                        <div class="row">
                                                            <!-- Column A -->
                                                            <div class="col-md-6">
                                                                <div class="afb-section">
                                                                    <h4 class="afb-subheader-A" id="afb-subheader-A">Slide A:
                                                                    </h4>
                                                                    <hr>
                                                                    <div id="afb_a_date_section">
                                                                        <label class="form-label"><strong>8(a). Date of AFB
                                                                                microscopy
                                                                            </strong></label>
                                                                        <input type="date" value="<?php if ($costing['afb_a_date']) {
                                                                            print_r($costing['afb_a_date']);
                                                                        } ?>" id="afb_a_date" name="afb_a_date"
                                                                            class="form-control" />
                                                                    </div>
                                                                    <hr>
                                                                    <div id="afb_technique_a_section">
                                                                        <label class="form-label"><strong>8(b). AFB technique
                                                                                used
                                                                            </strong></label>
                                                                        <div class="row-form clearfix">
                                                                            <div class="form-group">
                                                                                <?php foreach ($override->get('afb_microscopy', 'status', 1) as $value) { ?>
                                                                                        <div class="form-check">
                                                                                            <input class="form-check-input" type="radio"
                                                                                                name="technique_a"
                                                                                                id="technique_a<?= $value['id']; ?>"
                                                                                                value="<?= $value['id']; ?>" <?php if ($costing['technique_a'] == $value['id']) {
                                                                                                      echo 'checked';
                                                                                                  } ?>>
                                                                                            <label
                                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                                        </div>
                                                                                <?php } ?>
                                                                            </div>
                                                                        </div>
                                                                        <button type="button"
                                                                            onclick="unsetRadio('technique_a')">Unset</button>
                                                                    </div>
                                                                    <hr>
                                                                    <div id="afb_results_a_section">
                                                                        <label class="form-label"><strong>8(c). AFB microscopy
                                                                                result
                                                                            </strong></label>
                                                                        <div class="row-form clearfix">
                                                                            <div class="form-group">
                                                                                <?php foreach ($override->get('afb_results', 'status', 1) as $value) { ?>
                                                                                        <div class="form-check">
                                                                                            <input class="form-check-input" type="radio"
                                                                                                name="afb_a_results"
                                                                                                id="afb_a_results<?= $value['id']; ?>"
                                                                                                value="<?= $value['id']; ?>" <?php if ($costing['afb_a_results'] == $value['id']) {
                                                                                                      echo 'checked';
                                                                                                  } ?>>
                                                                                            <label
                                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                                        </div>
                                                                                <?php } ?>
                                                                            </div>
                                                                        </div>
                                                                        <button type="button"
                                                                            onclick="unsetRadio('afb_a_results')">Unset</button>
                                                                    </div>
                                                                </div>
                                                                <hr>
                                                            </div>

                                                            <!-- Column B -->
                                                            <div class="col-md-6">
                                                                <div class="afb-section">
                                                                    <h4 class="afb-subheader-B" id="afb-subheader-B">Slide B:
                                                                    </h4>
                                                                    <hr>
                                                                    <div id="afb_b_date_section">
                                                                        <label class="form-label"><strong>8(a). Date of AFB
                                                                                microscopy
                                                                            </strong></label>
                                                                        <input type="date" value="<?php if ($costing['afb_b_date']) {
                                                                            print_r($costing['afb_b_date']);
                                                                        } ?>" id="afb_date_b" name="afb_b_date"
                                                                            class="form-control" />
                                                                    </div>
                                                                    <hr>
                                                                    <div id="afb_technique_b_section">
                                                                        <label class="form-label"><strong>8(b). AFB technique
                                                                                used
                                                                            </strong></label>
                                                                        <div class="row-form clearfix">
                                                                            <div class="form-group">
                                                                                <?php foreach ($override->get('afb_microscopy', 'status', 1) as $value) { ?>
                                                                                        <div class="form-check">
                                                                                            <input class="form-check-input" type="radio"
                                                                                                name="technique_b"
                                                                                                id="technique_b<?= $value['id']; ?>"
                                                                                                value="<?= $value['id']; ?>" <?php if ($costing['technique_b'] == $value['id']) {
                                                                                                      echo 'checked';
                                                                                                  } ?>>
                                                                                            <label
                                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                                        </div>
                                                                                <?php } ?>
                                                                            </div>
                                                                        </div>
                                                                        <button type="button"
                                                                            onclick="unsetRadio('technique_b')">Unset</button>
                                                                    </div>
                                                                    <hr>
                                                                    <div id="afb_results_b_section">
                                                                        <label class="form-label"><strong>8(c). AFB microscopy
                                                                                result
                                                                            </strong></label>
                                                                        <div class="row-form clearfix">
                                                                            <div class="form-group">
                                                                                <?php foreach ($override->get('afb_results', 'status', 1) as $value) { ?>
                                                                                        <div class="form-check">
                                                                                            <input class="form-check-input" type="radio"
                                                                                                name="afb_b_results"
                                                                                                id="afb_results_b<?= $value['id']; ?>"
                                                                                                value="<?= $value['id']; ?>" <?php if ($costing['afb_b_results'] == $value['id']) {
                                                                                                      echo 'checked';
                                                                                                  } ?>>
                                                                                            <label
                                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                                        </div>
                                                                                <?php } ?>
                                                                            </div>
                                                                        </div>
                                                                        <button type="button"
                                                                            onclick="unsetRadio('afb_b_results')">Unset</button>
                                                                    </div>
                                                                </div>
                                                                <hr>
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class="text-center" id="xpert_rif_test_section">
                                                        <h3><strong>Xpert MTB/RIF (Ultra) Test</strong></h3>
                                                    </div>
                                                    <hr>

                                                    <div class="row">
                                                        <div class="col-4" id="xpert_rif_conducted_section">
                                                            <label for="xpert_mtb_rif_conducted" class="form-label">9. Was Xpert
                                                                MTB/RIF
                                                                (Ultra)
                                                                Conducted?</label>
                                                            <!-- radio -->
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('yes_no', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="xpert_mtb_rif_conducted"
                                                                                    id="xpert_mtb_rif_conducted<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?php if ($costing['xpert_mtb_rif_conducted'] == $value['id']) {
                                                                                          echo 'checked';
                                                                                      } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('xpert_mtb_rif_conducted')">Unset</button>
                                                        </div>
                                                    </div>

                                                    <div id="xpert_mtb_rif_section">
                                                        <hr>
                                                        <span>
                                                            <strong class="text-center d-block text-success fw-bold">
                                                                Note: if initial test result was non-determinate, include data
                                                                for the final test result here.
                                                            </strong>
                                                        </span>
                                                        <hr>
                                                        <div class="row">

                                                            <!-- Date Column -->
                                                            <div class="col-3 border p-3 rounded" id="xpert_date_section">
                                                                <div class="mb-3">
                                                                    <label for="xpert_date" class="form-label"><strong>9(a).
                                                                            Date of
                                                                            conducting Xpert MTB/RIF (Ultra)</strong></label>
                                                                    <input type="date" value="<?php if ($costing['xpert_date']) {
                                                                        print_r($costing['xpert_date']);
                                                                    } ?>" id="xpert_date" name="xpert_date"
                                                                        class="form-control" placeholder="Enter here" />
                                                                </div>
                                                            </div>

                                                            <!-- Xpert Results Subtitle -->
                                                            <div class="col-6">
                                                                <h5 class="text-center" id="xpert_rif_results_section">
                                                                    <strong>9(b). Xpert MTB/RIF (Ultra) Test
                                                                        Result
                                                                    </strong>
                                                                </h5>
                                                                <div class="row">
                                                                    <!-- MTB Column -->
                                                                    <div class="col-6 border p-3 rounded"
                                                                        id="xpert_mtb_section">
                                                                        <label for="xpert_mtb"
                                                                            class="form-label"><strong>MTB</strong></label>
                                                                        <div class="row-form clearfix">
                                                                            <div class="form-group">
                                                                                <?php foreach ($override->get('mtb_detections', 'status', 1) as $value) { ?>
                                                                                        <div class="form-check">
                                                                                            <input class="form-check-input" type="radio"
                                                                                                name="xpert_mtb" id="xpert_mtb"
                                                                                                value="<?= $value['id']; ?>" <?php if ($costing['xpert_mtb'] == $value['id']) {
                                                                                                      echo 'checked';
                                                                                                  } ?>>
                                                                                            <label
                                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                                        </div>
                                                                                <?php } ?>
                                                                                <input type="text" value="<?php if ($costing['error_code']) {
                                                                                    print_r($costing['error_code']);
                                                                                } ?>" id="error_code" name="error_code"
                                                                                    min="1000" max="9999" class="form-control"
                                                                                    placeholder="Enter error code here" />
                                                                            </div>
                                                                        </div>
                                                                        <button type="button"
                                                                            onclick="unsetRadio('xpert_mtb')">Unset</button>
                                                                    </div>

                                                                    <!-- RIF Resistance Column -->
                                                                    <div class="col-6 border p-3 rounded"
                                                                        id="xpert_rif_section">
                                                                        <label for="xpert_rif" class="form-label"><strong>RIF
                                                                                Resistance</strong></label>
                                                                        <div class="row-form clearfix">
                                                                            <div class="form-group">
                                                                                <?php foreach ($override->get('rif_resistance', 'status', 1) as $value) { ?>
                                                                                        <div class="form-check">
                                                                                            <input class="form-check-input" type="radio"
                                                                                                name="xpert_rif" id="xpert_rif"
                                                                                                value="<?= $value['id']; ?>" <?php if ($costing['xpert_rif'] == $value['id']) {
                                                                                                      echo 'checked';
                                                                                                  } ?>>
                                                                                            <label
                                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                                        </div>
                                                                                <?php } ?>
                                                                            </div>
                                                                        </div>
                                                                        <button type="button"
                                                                            onclick="unsetRadio('xpert_rif')">Unset</button>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- CT Value Column -->
                                                            <div class="col-3 border p-3 rounded" id="ct_value_section">
                                                                <div class="mb-3">
                                                                    <label for="ct_value" class="form-label"><strong>9(c). SPC
                                                                            Cycle
                                                                            Threshold (Ct) Value</strong></label>
                                                                    <input type="number" value="<?php if ($costing['ct_value']) {
                                                                        print_r($costing['ct_value']);
                                                                    } ?>" id="ct_value" name="ct_value" step="0.1" min="0"
                                                                        max="99" class="form-control"
                                                                        placeholder="Enter here" />

                                                                    <!-- Checkbox below input -->
                                                                    <div class="form-check mt-2">
                                                                        <input type="checkbox" class="form-check-input"
                                                                            id="ct_na" name="ct_na" onclick="handleCheckbox()"
                                                                            <?php
                                                                            if ($costing['ct_na']) {
                                                                                echo 'checked';
                                                                            } ?>>
                                                                        <label class="form-check-label" for="ct_na">No SPC-Ct
                                                                            value (no result)</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                                <!-- Remarks Row -->
                                                <div class="row mt-3">
                                                    <div class="col-12 border p-3 rounded">
                                                        <label for="remarks" class="form-label"><strong>10. Any comments or
                                                                remarks
                                                                about the patient or sample</strong></label>
                                                        <textarea id="remarks" name="remarks" class="form-control" rows="3"
                                                            placeholder="Enter any additional remarks here...">
                                                                                                        <?php if ($remarks['remarks']) {
                                                                                                            print_r($remarks['remarks']);
                                                                                                        } ?>
                                                                                                                                                            </textarea>
                                                    </div>
                                                </div>

                                                <hr>

                                                <hr>
                                                <div class="card card-warning">
                                                    <div class="card-header">
                                                        <h3 class="card-title">FORM STATUS</h3>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <label>Complete?</label>
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('form_completness', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="form_status" id="form_status<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>"
                                                                                <?= ($costing['form_status'] == $value['id']) ? 'checked' : ''; ?> required onchange="updateFormStatus()">
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('form_status')">Unset</button>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-4">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Completed Date</label>
                                                                <input class="form-control" type="date" name="date_completed"
                                                                    id="date_completed"
                                                                    value="<?= ($costing['date_completed']) ? $costing['date_completed'] : ''; ?>" />
                                                                <span id="date_completed_error" class="text-danger"></span>
                                                            </div>
                                                        </div>
                                                        <?php if ($costing['form_status'] >= 2) { ?>

                                                                <div class="row-form clearfix">
                                                                    <div class="form-group">
                                                                        <label>Completed By</label>
                                                                        <input class="form-control" type="text"
                                                                            value="<?= $override->get('user', 'id', $costing['completed_by'])[0]['username']; ?>"
                                                                            readonly />
                                                                    </div>
                                                                </div>
                                                        <?php } ?>
                                                    </div>

                                                    <div class="col-sm-4">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Verified Date</label>
                                                                <input class="form-control" type="date" name="date_verified"
                                                                    id="date_verified"
                                                                    value="<?= ($costing['date_verified']) ? $costing['date_verified'] : ''; ?>" />
                                                                <span id="date_verified_error" class="text-danger"></span>
                                                            </div>
                                                        </div>
                                                        <?php if ($costing['form_status'] >= 3) { ?>
                                                                <div class="row-form clearfix">
                                                                    <div class="form-group">
                                                                        <label>Verified By</label>
                                                                        <input class="form-control" type="text"
                                                                            value="<?= $override->get('user', 'id', $costing['verified_by'])[0]['username']; ?>"
                                                                            readonly />
                                                                    </div>
                                                                </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                                <hr>
                                            </div>
                                            <!-- /.card-body -->
                                            <div class="card-footer">
                                                <a href="info.php?id=id=6&status=<?= $_GET['status']; ?>&sid=<?= $_GET['sid']; ?>&facility_id=<?= $_GET['facility_id']; ?>&page=<?= $_GET['page']; ?>"
                                                    class="btn btn-default">Back</a>
                                                <input type="submit" name="add_respiratory" value="Submit"
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
        <?php } elseif ($_GET['id'] == 12) { ?>
        <?php } elseif ($_GET['id'] == 13) { ?>
                <?php
                $screening = $override->getNews('screening', 'status', 1, 'id', $_GET['sid'])[0];
                ?>
                <!-- Content Wrapper. Contains page content -->
                <div class="content-wrapper">
                    <!-- Content Header (Page header) -->
                    <section class="content-header">
                        <div class="container-fluid">
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <?php if ($screening) { ?>
                                            <h1>Update Screening (PID :<?= $screening['pid'] ?>)</h1>
                                    <?php } else { ?>
                                            <h1>Add Screening
                                            </h1>
                                    <?php } ?>
                                </div>
                                <div class="col-sm-6">
                                    <ol class="breadcrumb float-sm-right">
                                        <li class="breadcrumb-item">
                                            <a
                                                href="info.php?id=3&status=<?= $_GET['status']; ?>&facility_id=<?= $_GET['facility_id']; ?>&page=<?= $_GET['page']; ?>">
                                                < Back</a>
                                        </li>&nbsp;&nbsp;
                                        <li class="breadcrumb-item">
                                            <a href="index1.php">Home</a>
                                        </li>&nbsp;&nbsp;
                                        <li class="breadcrumb-item">
                                            <a
                                                href="info.php?id=3&status=<?= $_GET['status']; ?>&facility_id=<?= $_GET['facility_id']; ?>&page=<?= $_GET['page']; ?>">
                                                Go to screening list > </a>
                                        </li>&nbsp;&nbsp;
                                        <?php if ($results) { ?>
                                                <li class="breadcrumb-item active">Add New Screening </li>
                                        <?php } else { ?>
                                                <li class="breadcrumb-item active">Update Screening</li>
                                        <?php } ?>
                                    </ol>
                                </div>
                            </div>
                        </div><!-- /.container-fluid -->
                    </section>

                    <!-- Main content -->
                    <section class="content">
                        <div class="container-fluid">
                            <div class="row">
                                <!-- right column -->
                                <div class="col-md-12">
                                    <!-- general form elements disabled -->
                                    <div class="card card-warning">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                Screening
                                            </h3>
                                        </div>
                                        <!-- /.card-header -->
                                        <form id="screening" enctype="multipart/form-data" method="post" autocomplete="off">
                                            <div class="card-body">
                                                <hr>
                                                <div class="row">
                                                    <!-- Screening Date -->
                                                    <div class="col-4">
                                                        <div class="mb-2">
                                                            <label for="screening_date" class="form-label">1. Date of
                                                                Screening</label>
                                                            <input type="date" value="<?php if ($screening['screening_date']) {
                                                                echo $screening['screening_date'];
                                                            } ?>" id="screening_date" name="screening_date"
                                                                class="form-control" placeholder="Enter date" required />
                                                            <small id="screening_date_error" class="text-danger"
                                                                style="display: none;">Screening date must be
                                                                between 2025-01-20 and today.</small>
                                                        </div>
                                                    </div>

                                                    <!-- PID1 -->
                                                    <div class="col-4">
                                                        <div class="mb-2">
                                                            <label for="pid1" class="form-label">2. PID</label>
                                                            <input type="text" value="<?php if ($screening['pid1']) {
                                                                echo $screening['pid1'];
                                                            } ?>" id="pid1" name="pid1" class="form-control"
                                                                placeholder="Enter Last Three Digits" required />
                                                            <small id="pid1_error" class="text-danger"
                                                                style="display: none;">PID1
                                                                and PID2 do not
                                                                match.</small>
                                                        </div>
                                                    </div>

                                                    <!-- PID2 -->
                                                    <div class="col-4">
                                                        <div class="mb-2">
                                                            <label for="pid2" class="form-label">3. Re-enter PID</label>
                                                            <input type="text" value="<?php if ($screening['pid2']) {
                                                                echo $screening['pid2'];
                                                            } ?>" id="pid2" name="pid2" class="form-control"
                                                                placeholder="Re-Enter Last Three Digits" required />
                                                            <small id="pid2_error" class="text-danger"
                                                                style="display: none;">PID1
                                                                and PID2 do not
                                                                match.</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>

                                                <!-- Inclusion Section -->
                                                <div class="card card-warning">
                                                    <div class="card-header">
                                                        <h3 class="card-title">Inclusion (Include if patient responds yes to ALL
                                                            of the following)</h3>
                                                    </div>
                                                </div>
                                                <hr>

                                                <!-- Present Symptoms -->
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <label for="present_symptoms" class="form-label">4. Does the patient
                                                            present with signs and symptoms
                                                            suggestive of pulmonary TB or another pulmonary infection of
                                                            bacterial, viral, or fungal
                                                            origin?</label>
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('yes_no', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="present_symptoms"
                                                                                id="present_symptoms<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($screening['present_symptoms'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?> required>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Produce Respiratory Sample -->
                                                    <div class="col-sm-6">
                                                        <label for="produce_resp_sample" class="form-label">5. Is the patient
                                                            capable of producing a sputum
                                                            sample?</label>
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('yes_no', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="produce_resp_sample"
                                                                                id="produce_resp_sample<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($screening['produce_resp_sample'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?> required>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Age 18 Years and Consent -->
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <label for="age18years" class="form-label">6. Is the Patient at least 18
                                                            years old?</label>
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('yes_no', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="age18years" id="age18years<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($screening['age18years'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?> required>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Consent -->
                                                    <div class="col-sm-4">
                                                        <label for="consent" class="form-label">7. Has the patient provided
                                                            written informed consent to
                                                            participate?</label>
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('yes_no', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio" name="consent"
                                                                                id="consent<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($screening['consent'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?> required>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Consent Date -->
                                                    <div class="col-4" id="consent_date_container" style="display: none;">
                                                        <div class="mb-2">
                                                            <label for="consent_date" class="form-label">8. Date of
                                                                Consent</label>
                                                            <input type="date" value="<?php if ($screening) {
                                                                echo $screening['consent_date'];
                                                            } ?>" id="consent_date" name="consent_date"
                                                                class="form-control" placeholder="Enter date" />
                                                            <small id="consent_date_error" class="text-danger"
                                                                style="display: none;">Consent date is required if
                                                                consent is selected as "Yes".</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>

                                                <!-- Exclusion Section -->
                                                <div class="card card-warning">
                                                    <div class="card-header">
                                                        <h3 class="card-title">Exclusion (Exclude if patient responds yes to Any
                                                            of the following)</h3>
                                                    </div>
                                                </div>
                                                <hr>

                                                <!-- Not Willing -->
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <label for="not_willing" class="form-label">9. Not willing to sign the
                                                            informed consent form?</label>
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('yes_no', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="not_willing" id="not_willing<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($screening['not_willing'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?> required>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Unable to Understand -->
                                                    <div class="col-sm-6">
                                                        <label for="unable_understand" class="form-label">10. Unable to
                                                            understand the informed consent form
                                                            and/or the study procedures?</label>
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('yes_no', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="unable_understand"
                                                                                id="unable_understand<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($screening['unable_understand'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?> required>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <hr>

                                                <div class="card card-warning">
                                                    <div class="card-header">
                                                        <h3 class="card-title">Remarks</h3>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>11. Any remarks or comments on patient:</label>
                                                                <textarea class="form-control" name="remarks" rows="3"
                                                                    placeholder="Type comments here..."><?php if ($screening['remarks']) {
                                                                        print_r($screening['remarks']);
                                                                    } ?>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>

                                                <div class="card card-warning">
                                                    <div class="card-header">
                                                        <h3 class="card-title">FORM STATUS</h3>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <label>Complete?</label>
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('form_completness', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="form_status" id="form_status<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>"
                                                                                <?= ($screening['form_status'] == $value['id']) ? 'checked' : ''; ?> required onchange="updateFormStatus()">
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('form_status')">Unset</button>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-4">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Completed Date</label>
                                                                <input class="form-control" type="date" name="date_completed"
                                                                    id="date_completed"
                                                                    value="<?= ($screening['date_completed']) ? $screening['date_completed'] : ''; ?>" />
                                                                <span id="date_completed_error" class="text-danger"></span>
                                                            </div>
                                                        </div>
                                                        <?php if ($screening['form_status'] >= 2) { ?>

                                                                <div class="row-form clearfix">
                                                                    <div class="form-group">
                                                                        <label>Completed By</label>
                                                                        <input class="form-control" type="text"
                                                                            value="<?= $override->get('user', 'id', $screening['completed_by'])[0]['username']; ?>"
                                                                            readonly />
                                                                    </div>
                                                                </div>
                                                        <?php } ?>
                                                    </div>

                                                    <div class="col-sm-4">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Verified Date</label>
                                                                <input class="form-control" type="date" name="date_verified"
                                                                    id="date_verified"
                                                                    value="<?= ($screening['date_verified']) ? $screening['date_verified'] : ''; ?>" />
                                                                <span id="date_verified_error" class="text-danger"></span>
                                                            </div>
                                                        </div>
                                                        <?php if ($screening['form_status'] >= 3) { ?>
                                                                <div class="row-form clearfix">
                                                                    <div class="form-group">
                                                                        <label>Verified By</label>
                                                                        <input class="form-control" type="text"
                                                                            value="<?= $override->get('user', 'id', $screening['verified_by'])[0]['username']; ?>"
                                                                            readonly />
                                                                    </div>
                                                                </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /.card-body -->

                                            <!-- Card Footer -->
                                            <div class="card-footer">
                                                <a href="info.php?id=3&status=<?= $_GET['status'] ?>&facility_id=<?= $_GET['facility_id'] ?>&page=<?= $_GET['page'] ?>"
                                                    class="btn btn-default">Back</a>
                                                <input type="hidden" name="cid" value="<?= $_GET['cid'] ?>">
                                                <input type="submit" name="add_screening" value="Submit"
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

        <?php } elseif ($_GET['id'] == 14) { ?>
                <?php
                $screening = $override->getNews('screening', 'status', 1, 'id', $_GET['sid'])[0];
                $costing = $override->getNews('diagnosis_test', 'status', 1, 'enrollment_id', $_GET['sid'])[0];
                $lab_name = $override->getNews('sites', 'status', 1, 'id', $user->data()->site_id)[0];
                ?>
                <!-- Content Wrapper. Contains page content -->
                <div class="content-wrapper">
                    <!-- Content Header (Page header) -->
                    <section class="content-header">
                        <div class="container-fluid">
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <?php if ($costing) { ?>
                                            <h1>Update zonal/CTRL laboratory <br> (PID :<?= $screening['pid'] ?>)</h1>
                                    <?php } else { ?>
                                            <h1>Add zonal/CTRL laboratory <br> (PID :<?= $screening['pid'] ?>)</h1>
                                    <?php } ?>
                                </div>
                                <div class="col-sm-6">
                                    <ol class="breadcrumb float-sm-right">
                                        <li class="breadcrumb-item"><a
                                                href="info.php?id=6&status=<?= $_GET['status']; ?>&sid=<?= $_GET['sid']; ?>&facility_id=<?= $_GET['facility_id']; ?>&page=<?= $_GET['page']; ?>">
                                                < Back</a>
                                        </li>&nbsp;&nbsp;
                                        <li class="breadcrumb-item"><a href="index1.php">Home</a></li>&nbsp;&nbsp;
                                        <li class="breadcrumb-item"><a
                                                href="info.php?id=3&status=<?= $_GET['status']; ?>&sid=<?= $_GET['sid']; ?>&facility_id=<?= $_GET['facility_id']; ?>&page=<?= $_GET['page']; ?>">
                                                Go to enrolled list > </a>
                                        </li>&nbsp;&nbsp;
                                        <?php if (!$costing) { ?>
                                                <li class="breadcrumb-item active">Add New zonal/CTRL laboratory</li>
                                        <?php } else { ?>
                                                <li class="breadcrumb-item active">Update zonal/CTRL laboratory</li>
                                        <?php } ?>
                                    </ol>
                                </div>
                            </div>
                        </div><!-- /.container-fluid -->
                    </section>

                    <!-- Main content -->
                    <section class="content">
                        <div class="container-fluid">
                            <div class="row">
                                <!-- right column -->
                                <div class="col-md-12">
                                    <!-- general form elements disabled -->
                                    <div class="card card-warning">
                                        <div class="card-header">
                                            <h3 class="card-title">Administration</h3>
                                        </div>
                                        <!-- /.card-header -->
                                        <form id="validation" enctype="multipart/form-data" method="post" autocomplete="off">
                                            <div class="card-body">
                                                <hr>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="mb-2">
                                                            <label for="lab_name" class="form-label">1a. Name of
                                                                laboratory / Site</label>
                                                            <input type="text" value="<?= $lab_name['name']; ?>" id="lab_name"
                                                                name="lab_name" class="form-control" placeholder="Enter here"
                                                                readonly />
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="card card-warning">
                                                    <div class="card-header">
                                                        <h3 class="card-title">Specimen receipt</h3>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="row">
                                                    <?php
                                                    // Fetch enrollment_date from database
                                                    $enrollment_date = ''; // Replace with actual query to fetch enrollment_date
                                                    if ($enrollment['enrollment_date']) {
                                                        $enrollment_date = $enrollment['enrollment_date'];
                                                    }
                                                    ?>
                                                    <input type="hidden" id="enrollment_date_hidded"
                                                        value="<?php echo $enrollment_date; ?>" />

                                                    <div class="col-sm-3" id="date_sputum_received_section">
                                                        <div class=" col-12">
                                                            <label class="form-label">2. Date sputum sample received in
                                                                laboratory</label>
                                                            <div class="mb-3">
                                                                <input type="date" value="<?php if ($costing['date_sputum_received']) {
                                                                    print_r($costing['date_sputum_received']);
                                                                } ?>" id="date_sputum_received" name="date_sputum_received"
                                                                    class="form-control" placeholder="Enter here" required />
                                                                <span id="date_sputum_received_error"
                                                                    class="text-danger"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-3" id="appearance_section">
                                                        <label for="appearance" class="form-label">3. Appearance</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('appearance', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="appearance" id="appearance<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['appearance'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('appearance')">Unset</button>

                                                        </div>
                                                    </div>

                                                    <div class="col-3" id="sample_volume_section">
                                                        <div class="mb-3">
                                                            <label for="sample_volume" class="form-label">4. Approximate
                                                                volume</label>
                                                            <input type="number" value="<?php if ($costing['sample_volume']) {
                                                                print_r($costing['sample_volume']);
                                                            } ?>" id="sample_volume" name="sample_volume" min="1" max="5"
                                                                class="form-control" placeholder="Enter here" required />
                                                        </div>
                                                        <span>mL</span>
                                                    </div>

                                                    <div class="col-3" id="unique_lab_no_section">
                                                        <div class="mb-3">
                                                            <label for="unique_lab_no" class="form-label">5. Unique laboratory
                                                                number assigned to sample</label>
                                                            <input type="text" value="<?php if ($costing['unique_lab_no']) {
                                                                print_r($costing['unique_lab_no']);
                                                            } ?>" id="unique_lab_no" name="unique_lab_no"
                                                                class="form-control" placeholder="Enter here" required />
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="card card-warning">
                                                    <div class="card-header">
                                                        <h3 class="card-title">Culture</h3>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="row">
                                                    <div class="col-4">
                                                        <label for="culture_performed" class="form-label">6(a). Was culture
                                                            performed?</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('yes_no', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="culture_performed"
                                                                                id="culture_performed<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['culture_performed'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?> required>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('culture_performed')">Unset</button>
                                                        </div>
                                                    </div>


                                                    <div class="col-sm-4" id="culture_method_section">
                                                        <label for="culture_method" class="form-label">6(b). Culture Method ?
                                                        </label><br>
                                                        <span>(tick any that apply)</span><br><br>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('culture_method', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                name="culture_method[]"
                                                                                id="culture_method<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php foreach (explode(',', $costing['culture_method']) as $values) {
                                                                                      if ($values == $value['id']) {
                                                                                          echo 'checked';
                                                                                      }
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-4" id="microscopy_type_section">
                                                        <label for="microscopy_type" class="form-label">7(a). Type
                                                            Microscopy conducted ?
                                                        </label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('culture_type', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="microscopy_type"
                                                                                id="microscopy_type<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['microscopy_type'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                        <button type="button"
                                                            onclick="unsetRadio('microscopy_type')">Unset</button>
                                                    </div>

                                                </div>

                                                <div id="culture_performed_section">
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-sm-6" id="microscopy_date_section">
                                                            <label for="microscopy_date" class="form-label">7(b). Date
                                                                Microscopy conducted
                                                            </label>
                                                            <!-- radio -->
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <input type="date" value="<?php if ($costing['microscopy_date']) {
                                                                        print_r($costing['microscopy_date']);
                                                                    } ?>" id="microscopy_date" name="microscopy_date"
                                                                        class="form-control" />
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6" id="microscopy_results_section">
                                                            <label for="microscopy_results" class="form-label">7(c).
                                                                Microscopy results</label>
                                                            <!-- radio -->
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('sediments_results', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="microscopy_results"
                                                                                    id="microscopy_results<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?php if ($costing['microscopy_results'] == $value['id']) {
                                                                                          echo 'checked';
                                                                                      } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>

                                                                </div>
                                                                <button type="button"
                                                                    onclick="unsetRadio('microscopy_results')">Unset</button>

                                                            </div>
                                                        </div>

                                                    </div>

                                                    <hr>

                                                    <div class="row">
                                                        <!-- LJ Section -->
                                                        <div class="col-sm-6 rounded border p-3"
                                                            id="lj_inoculation_date_section">
                                                            <h5 class="text-center"><strong>LJ</strong></h5>
                                                            <!-- Centered Title for LJ column -->
                                                            <hr>
                                                            <label for="lj_inoculation_date" class="form-label">8. LJ
                                                                Date of culture inoculation</label>
                                                            <input type="date" value="<?php if ($costing['lj_inoculation_date']) {
                                                                print_r($costing['lj_inoculation_date']);
                                                            } ?>" id="lj_inoculation_date" name="lj_inoculation_date"
                                                                class="form-control" />
                                                        </div>

                                                        <!-- MGIT Section -->
                                                        <div class="col-sm-6 rounded border p-3"
                                                            id="mgit_inoculation_date_section">
                                                            <h5 class="text-center"><strong>MGIT</strong></h5>
                                                            <!-- Centered Title for MGIT column -->
                                                            <hr>
                                                            <label for="mgit_inoculation_date" class="form-label">8.
                                                                MGIT Date of culture inoculation</label>
                                                            <input type="date" value="<?php if ($costing['mgit_inoculation_date']) {
                                                                print_r($costing['mgit_inoculation_date']);
                                                            } ?>" id="mgit_inoculation_date" name="mgit_inoculation_date"
                                                                class="form-control" />
                                                        </div>
                                                    </div>

                                                    <div class="row mt-3">
                                                        <!-- LJ Culture Results Date -->
                                                        <div class="col-sm-6 rounded border p-3" id="lj_results_date_section">
                                                            <label for="lj_results_date" class="form-label">9. LJ Date
                                                                of culture results</label>
                                                            <hr>
                                                            <input type="date" value="<?php if ($costing['lj_results_date']) {
                                                                print_r($costing['lj_results_date']);
                                                            } ?>" id="lj_results_date" name="lj_results_date"
                                                                class="form-control" />
                                                        </div>

                                                        <!-- MGIT Culture Results Date -->
                                                        <div class="col-sm-6 rounded border p-3" id="mgit_results_date_section">
                                                            <label for="mgit_results_date" class="form-label">9. MGIT
                                                                Date of culture results</label>
                                                            <input type="date" value="<?php if ($costing['mgit_results_date']) {
                                                                print_r($costing['mgit_results_date']);
                                                            } ?>" id="mgit_results_date" name="mgit_results_date"
                                                                class="form-control" />
                                                        </div>
                                                    </div>

                                                    <div class="row mt-3">
                                                        <!-- LJ Culture Result -->
                                                        <div class="col-sm-6 rounded border p-3" id="lj_results_section">
                                                            <label for="lj_results" class="form-label">10. LJ Culture
                                                                result</label>
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('culture_results2', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="lj_results"
                                                                                    id="lj_results<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?php if ($costing['lj_results'] == $value['id']) {
                                                                                          echo 'checked';
                                                                                      } ?>>
                                                                                <label class="form-check-label"
                                                                                    for="lj_results<?= $value['id']; ?>"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('lj_results')">Unset</button>
                                                        </div>

                                                        <!-- MGIT Culture Result -->
                                                        <div class="col-sm-6 rounded border p-3" id="mgit_results_section">
                                                            <label for="mgit_results" class="form-label">10. MGIT
                                                                Culture result</label>
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('culture_results', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="mgit_results"
                                                                                    id="mgit_results<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?php if ($costing['mgit_results'] == $value['id']) {
                                                                                          echo 'checked';
                                                                                      } ?>>
                                                                                <label class="form-check-label"
                                                                                    for="mgit_results<?= $value['id']; ?>"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('mgit_results')">Unset</button>
                                                        </div>
                                                    </div>

                                                    <hr>

                                                    <div class="row">
                                                        <div class="col-sm-6" id="culture_isolate_section">
                                                            <label for="culture_isolate" class="form-label">11. Was the
                                                                culture
                                                                isolate submitted to CTRL for phenotypic DST testing?
                                                            </label>
                                                            <!-- radio -->
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php
                                                                    $table = 'yes_no';
                                                                    if ($user->data()->site_id == 20) {
                                                                        $table = 'yes_no_na';
                                                                    } ?>
                                                                    <?php foreach ($override->get($table, 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="culture_isolate"
                                                                                    id="culture_isolate<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?php if ($costing['culture_isolate'] == $value['id']) {
                                                                                          echo 'checked';
                                                                                      } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>

                                                                </div>
                                                                <button type="button"
                                                                    onclick="unsetRadio('culture_isolate')">Unset</button>

                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6" id="isolate_date_section">
                                                            <label for="isolate_date" class="form-label">If yes, date
                                                                submitted</label>
                                                            <input type="date" value="<?= $costing['isolate_date'] ?? ''; ?>"
                                                                id="isolate_date" name="isolate_date" class="form-control"
                                                                <?= $disabled; ?> />
                                                        </div>
                                                    </div>
                                                    <hr>
                                                </div>


                                                <?php
                                                //  if ($user->data()->site_id == 20) { 
                                                $disabled = ($user->data()->site_id != 20) ? 'disabled' : '';

                                                ?>
                                                <div id="phenotypic_dst_section">
                                                    <div class="card card-warning">
                                                        <div class="card-header">
                                                            <h3 class="card-title">
                                                                12. Phenotypic DST (CTRL ONLY)
                                                            </h3>
                                                        </div>
                                                    </div>

                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-sm-4" id="phenotypic_performed_section">
                                                            <label for="phenotypic_performed" class="form-label">12a. Was
                                                                phenotypic DST performed?</label>
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('yes_no', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="phenotypic_performed"
                                                                                    id="phenotypic_performed<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?= $disabled; ?>                 <?php if ($costing['phenotypic_performed'] == $value['id']) {
                                                                                                             echo 'checked';
                                                                                                         } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                                <button type="button"
                                                                    onclick="unsetRadio('phenotypic_performed')" <?= $disabled; ?>>Unset</button>
                                                            </div>
                                                        </div>

                                                        <div class="col-sm-4" id="phenotypic_date_performed_section">
                                                            <label for="phenotypic_date_performed" class="form-label">12(b).
                                                                Date of performing phenotypic DST?</label>
                                                            <input type="date"
                                                                value="<?= $costing['phenotypic_date_performed'] ?? ''; ?>"
                                                                id="phenotypic_date_performed" name="phenotypic_date_performed"
                                                                class="form-control" <?= $disabled; ?> />
                                                        </div>

                                                        <div class="col-sm-4" id="phenotypic_date_results_section">
                                                            <label for="phenotypic_date_results" class="form-label">12(c). Date
                                                                of phenotypic DST Results?</label>
                                                            <input type="date"
                                                                value="<?= $costing['phenotypic_date_results'] ?? ''; ?>"
                                                                id="phenotypic_date_results" name="phenotypic_date_results"
                                                                class="form-control" <?= $disabled; ?> />
                                                        </div>
                                                    </div>
                                                </div>

                                                <div id="phenotypic_performed_results_section">
                                                    <hr>

                                                    <div class="card card-warning">
                                                        <div class="card-header">
                                                            <h3 class="card-title">
                                                                13. Phenotypic DST results
                                                            </h3>
                                                        </div>
                                                    </div>

                                                    <hr>

                                                    <div class="row">
                                                        <div class="col-sm-3" id="rifampicin">
                                                            <label for="rifampicin" class="form-label">13(a). Rifampicin</label>
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('phenotypic_dst', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="rifampicin"
                                                                                    id="rifampicin<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?= $disabled; ?>                 <?php if ($costing['rifampicin'] == $value['id']) {
                                                                                                             echo 'checked';
                                                                                                         } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                                <button type="button" onclick="unsetRadio('rifampicin')"
                                                                    <?= $disabled; ?>>Unset</button>
                                                            </div>
                                                        </div>

                                                        <div class="col-sm-3" id="isoniazid">
                                                            <label for="isoniazid" class="form-label">13(b). Isoniazid</label>
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('phenotypic_dst', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="isoniazid" id="isoniazid<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?= $disabled; ?>                 <?php if ($costing['isoniazid'] == $value['id']) {
                                                                                                             echo 'checked';
                                                                                                         } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                                <button type="button" onclick="unsetRadio('isoniazid')"
                                                                    <?= $disabled; ?>>Unset</button>
                                                            </div>
                                                        </div>

                                                        <div class="col-sm-3" id="levofloxacin">
                                                            <label for="levofloxacin" class="form-label">13(c).
                                                                Levofloxacin</label>
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('phenotypic_dst', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="levofloxacin"
                                                                                    id="levofloxacin<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?= $disabled; ?>                 <?php if ($costing['levofloxacin'] == $value['id']) {
                                                                                                             echo 'checked';
                                                                                                         } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                                <button type="button" onclick="unsetRadio('levofloxacin')"
                                                                    <?= $disabled; ?>>Unset</button>
                                                            </div>
                                                        </div>

                                                        <div class="col-sm-3" id="moxifloxacin">
                                                            <label for="moxifloxacin" class="form-label">13(d).
                                                                Moxifloxacin</label>
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('phenotypic_dst', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="moxifloxacin"
                                                                                    id="moxifloxacin<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?= $disabled; ?>                 <?php if ($costing['moxifloxacin'] == $value['id']) {
                                                                                                             echo 'checked';
                                                                                                         } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                                <button type="button" onclick="unsetRadio('moxifloxacin')"
                                                                    <?= $disabled; ?>>Unset</button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <hr>

                                                    <div class="row">
                                                        <div class="col-sm-3" id="bedaquiline">
                                                            <label for="bedaquiline" class="form-label">13(e).
                                                                Bedaquiline</label>
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('phenotypic_dst', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="bedaquiline"
                                                                                    id="bedaquiline<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?= $disabled; ?>                 <?php if ($costing['bedaquiline'] == $value['id']) {
                                                                                                             echo 'checked';
                                                                                                         } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                                <button type="button" onclick="unsetRadio('bedaquiline')"
                                                                    <?= $disabled; ?>>Unset</button>
                                                            </div>
                                                        </div>

                                                        <div class="col-sm-3" id="linezolid">
                                                            <label for="linezolid" class="form-label">13(f). Linezolid</label>
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('phenotypic_dst', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="linezolid" id="linezolid<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?= $disabled; ?>                 <?php if ($costing['linezolid'] == $value['id']) {
                                                                                                             echo 'checked';
                                                                                                         } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                                <button type="button" onclick="unsetRadio('linezolid')"
                                                                    <?= $disabled; ?>>Unset</button>
                                                            </div>
                                                        </div>

                                                        <div class="col-sm-3" id="clofazimine">
                                                            <label for="clofazimine" class="form-label">13(g).
                                                                Clofazimine</label>
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('phenotypic_dst', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="clofazimine"
                                                                                    id="clofazimine<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?= $disabled; ?>                 <?php if ($costing['clofazimine'] == $value['id']) {
                                                                                                             echo 'checked';
                                                                                                         } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                                <button type="button" onclick="unsetRadio('clofazimine')"
                                                                    <?= $disabled; ?>>Unset</button>
                                                            </div>
                                                        </div>

                                                        <div class="col-sm-3" id="cycloserine">
                                                            <label for="cycloserine" class="form-label">13(h).
                                                                Cycloserine</label>
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('phenotypic_dst', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="cycloserine"
                                                                                    id="cycloserine<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?= $disabled; ?>                 <?php if ($costing['cycloserine'] == $value['id']) {
                                                                                                             echo 'checked';
                                                                                                         } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                                <button type="button" onclick="unsetRadio('cycloserine')"
                                                                    <?= $disabled; ?>>Unset</button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <hr>

                                                    <div class="row">
                                                        <div class="col-sm-3" id="terizidone">
                                                            <label for="terizidone" class="form-label">13(i). Terizidone</label>
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('phenotypic_dst', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="terizidone"
                                                                                    id="terizidone<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?= $disabled; ?>                 <?php if ($costing['terizidone'] == $value['id'])
                                                                                                             echo 'checked'; ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                                <button type="button"
                                                                    onclick="unsetRadio('terizidone')">Unset</button>
                                                            </div>
                                                        </div>

                                                        <div class="col-sm-3" id="ethambutol">
                                                            <label for="ethambutol" class="form-label">13(j). Ethambutol</label>
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('phenotypic_dst', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="ethambutol"
                                                                                    id="ethambutol<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?= $disabled; ?>                 <?php if ($costing['ethambutol'] == $value['id'])
                                                                                                             echo 'checked'; ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                                <button type="button"
                                                                    onclick="unsetRadio('ethambutol')">Unset</button>
                                                            </div>
                                                        </div>

                                                        <div class="col-sm-3" id="delamanid">
                                                            <label for="delamanid" class="form-label">13(k). Delamanid</label>
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('phenotypic_dst', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="delamanid" id="delamanid<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?= $disabled; ?>                 <?php if ($costing['delamanid'] == $value['id'])
                                                                                                             echo 'checked'; ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                                <button type="button"
                                                                    onclick="unsetRadio('delamanid')">Unset</button>
                                                            </div>
                                                        </div>

                                                        <div class="col-sm-3" id="pyrazinamide">
                                                            <label for="pyrazinamide" class="form-label">13(l).
                                                                Pyrazinamide</label>
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('phenotypic_dst', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="pyrazinamide"
                                                                                    id="pyrazinamide<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?= $disabled; ?>                 <?php if ($costing['pyrazinamide'] == $value['id'])
                                                                                                             echo 'checked'; ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                                <button type="button"
                                                                    onclick="unsetRadio('pyrazinamide')">Unset</button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <hr>

                                                    <div class="row">
                                                        <div class="col-sm-3" id="imipenem">
                                                            <label for="imipenem" class="form-label">13(m). Imipenem</label>
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('phenotypic_dst', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="imipenem" id="imipenem<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?= $disabled; ?>                 <?php if ($costing['imipenem'] == $value['id'])
                                                                                                             echo 'checked'; ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                                <button type="button"
                                                                    onclick="unsetRadio('imipenem')">Unset</button>
                                                            </div>
                                                        </div>

                                                        <div class="col-sm-3" id="cilastatin">
                                                            <label for="cilastatin" class="form-label">13(n). Cilastatin</label>
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('phenotypic_dst', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="cilastatin"
                                                                                    id="cilastatin<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?= $disabled; ?>                 <?php if ($costing['cilastatin'] == $value['id'])
                                                                                                             echo 'checked'; ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                                <button type="button"
                                                                    onclick="unsetRadio('cilastatin')">Unset</button>
                                                            </div>
                                                        </div>

                                                        <div class="col-sm-3" id="meropenem">
                                                            <label for="meropenem" class="form-label">13(o). Meropenem</label>
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('phenotypic_dst', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="meropenem" id="meropenem<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?= $disabled; ?>                 <?php if ($costing['meropenem'] == $value['id'])
                                                                                                             echo 'checked'; ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                                <button type="button"
                                                                    onclick="unsetRadio('meropenem')">Unset</button>
                                                            </div>
                                                        </div>

                                                        <div class="col-sm-3" id="amikacin">
                                                            <label for="amikacin" class="form-label">13(p). Amikacin</label>
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('phenotypic_dst', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="amikacin" id="amikacin<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?= $disabled; ?>                 <?php if ($costing['amikacin'] == $value['id'])
                                                                                                             echo 'checked'; ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                                <button type="button"
                                                                    onclick="unsetRadio('amikacin')">Unset</button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <hr>

                                                    <div class="row">
                                                        <div class="col-sm-3" id="streptomycin">
                                                            <label for="streptomycin" class="form-label">13(q).
                                                                Streptomycin</label>
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('phenotypic_dst', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="streptomycin"
                                                                                    id="streptomycin<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>"
                                                                                    <?= ($costing['streptomycin'] == $value['id']) ? 'checked' : ''; ?>                 <?= $disabled; ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                                <button type="button" onclick="unsetRadio('streptomycin')"
                                                                    <?= $disabled; ?>>Unset</button>
                                                            </div>
                                                        </div>

                                                        <div class="col-sm-3" id="ethionamide">
                                                            <label for="ethionamide" class="form-label">13(r).
                                                                Ethionamide</label>
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('phenotypic_dst', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="ethionamide"
                                                                                    id="ethionamide<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>"
                                                                                    <?= ($costing['ethionamide'] == $value['id']) ? 'checked' : ''; ?>                 <?= $disabled; ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                                <button type="button" onclick="unsetRadio('ethionamide')"
                                                                    <?= $disabled; ?>>Unset</button>
                                                            </div>
                                                        </div>

                                                        <div class="col-sm-3" id="prothionamide">
                                                            <label for="prothionamide" class="form-label">13(s).
                                                                Prothionamide</label>
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('phenotypic_dst', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="prothionamide"
                                                                                    id="prothionamide<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>"
                                                                                    <?= ($costing['prothionamide'] == $value['id']) ? 'checked' : ''; ?>                 <?= $disabled; ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                                <button type="button" onclick="unsetRadio('prothionamide')"
                                                                    <?= $disabled; ?>>Unset</button>
                                                            </div>
                                                        </div>

                                                        <div class="col-sm-3" id="para_aminosalicylic_acid">
                                                            <label for="para_aminosalicylic_acid" class="form-label">13(t).
                                                                Para-aminosalicylic acid</label>
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('phenotypic_dst', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="para_aminosalicylic_acid"
                                                                                    id="para_aminosalicylic_acid<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>"
                                                                                    <?= ($costing['para_aminosalicylic_acid'] == $value['id']) ? 'checked' : ''; ?>                 <?= $disabled; ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                                <button type="button"
                                                                    onclick="unsetRadio('para_aminosalicylic_acid')"
                                                                    <?= $disabled; ?>>Unset</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                                // }
                                                ?>
                                                <hr>


                                                <div class="card card-warning">
                                                    <div class="card-header">
                                                        <h3 class="card-title">Xpert XDR</h3>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <label for="xpert_xdr_performed" class="form-label">14(a). Was Xpert
                                                            XDR
                                                            performed?</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('yes_no', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="xpert_xdr_performed"
                                                                                id="xpert_xdr_performed<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['xpert_xdr_performed'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?> required>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('xpert_xdr_performed')">Unset</button>

                                                        </div>
                                                    </div>

                                                    <div class="col-sm-6" id="xpert_xdr_date_performed_section">
                                                        <label for="xpert_xdr_date_performed" class="form-label">14(b). Date of
                                                            performing Xpert XDR testing?</label>
                                                        <input type="date" value="<?php if ($costing['xpert_xdr_date_performed']) {
                                                            print_r($costing['xpert_xdr_date_performed']);
                                                        } ?>" id="xpert_xdr_date_performed" name="xpert_xdr_date_performed"
                                                            class="form-control" />
                                                    </div>
                                                </div>
                                                <hr>
                                                <div id="xpert_xdr_results_section">
                                                    <div class="row">
                                                        <div class="col-sm-4" id="isoniazid2">
                                                            <label for="isoniazid2" class="form-label">15(a). Isoniazid</label>
                                                            <!-- radio -->
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('xpert_xdr_results', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="isoniazid2"
                                                                                    id="isoniazid2<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?php if ($costing['isoniazid2'] == $value['id']) {
                                                                                          echo 'checked';
                                                                                      } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                    <button type="button"
                                                                        onclick="unsetRadio('isoniazid2')">Unset</button>

                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-sm-4" id="fluoroquinolones">
                                                            <label for="fluoroquinolones" class="form-label">15(b).
                                                                Fluoroquinolones</label>
                                                            <!-- radio -->
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('xpert_xdr_results', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="fluoroquinolones"
                                                                                    id="fluoroquinolones<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?php if ($costing['fluoroquinolones'] == $value['id']) {
                                                                                          echo 'checked';
                                                                                      } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                    <button type="button"
                                                                        onclick="unsetRadio('fluoroquinolones')">Unset</button>

                                                                </div>

                                                            </div>

                                                        </div>

                                                        <div class="col-sm-4" id="amikacin2">
                                                            <label for="amikacin2" class="form-label">15(c). Amikacin</label>
                                                            <!-- radio -->
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('xpert_xdr_results', 'status1', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="amikacin2" id="amikacin2<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?php if ($costing['amikacin2'] == $value['id']) {
                                                                                          echo 'checked';
                                                                                      } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                    <button type="button"
                                                                        onclick="unsetRadio('amikacin2')">Unset</button>

                                                                </div>
                                                            </div>

                                                        </div>

                                                    </div>

                                                    <hr>

                                                    <div class="row">
                                                        <div class="col-sm-4" id="kanamycin">
                                                            <label for="kanamycin" class="form-label">15(d). Kanamycin</label>
                                                            <!-- radio -->
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('xpert_xdr_results', 'status1', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="kanamycin" id="kanamycin<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?php if ($costing['kanamycin'] == $value['id']) {
                                                                                          echo 'checked';
                                                                                      } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                    <button type="button"
                                                                        onclick="unsetRadio('kanamycin')">Unset</button>

                                                                </div>

                                                            </div>

                                                        </div>
                                                        <div class="col-sm-4" id="capreomycin">
                                                            <label for="capreomycin" class="form-label">15(e).
                                                                Capreomycin</label>
                                                            <!-- radio -->
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('xpert_xdr_results', 'status1', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="capreomycin"
                                                                                    id="capreomycin<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?php if ($costing['capreomycin'] == $value['id']) {
                                                                                          echo 'checked';
                                                                                      } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                    <button type="button"
                                                                        onclick="unsetRadio('capreomycin')">Unset</button>

                                                                </div>

                                                            </div>

                                                        </div>
                                                        <div class="col-sm-4" id="ethionamide2">
                                                            <label for="ethionamide2" class="form-label">15(f).
                                                                Ethionamide</label>
                                                            <!-- radio -->
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('xpert_xdr_results', 'status2', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="ethionamide2"
                                                                                    id="ethionamide2<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?php if ($costing['ethionamide2'] == $value['id']) {
                                                                                          echo 'checked';
                                                                                      } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                    <button type="button"
                                                                        onclick="unsetRadio('ethionamide2')">Unset</button>

                                                                </div>

                                                            </div>

                                                        </div>

                                                    </div>
                                                    <hr>
                                                </div>
                                                <div class="card card-warning">
                                                    <div class="card-header">
                                                        <h3 class="card-title"> Line probe assays(Hain test)</h3>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="row">
                                                    <!-- First-Line LPA -->
                                                    <div class="col-sm-6 border rounded p-3">
                                                        <h5><strong>First-Line LPA</strong></h5>
                                                        <hr>
                                                        <label for="first_line_lpa" class="form-label">16. Was first-line LPA
                                                            conducted?</label>
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('yes_no', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="first_line_lpa"
                                                                                id="first_line_lpa<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['first_line_lpa'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?> required>
                                                                            <label class="form-check-label">
                                                                                <?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                        <button type="button"
                                                            onclick="unsetRadio('first_line_lpa')">Unset</button>
                                                        <hr>
                                                        <div id="first_line_section">
                                                            <label class="form-label mt-3">17(a). Date of performing first-line
                                                                LPA?</label>
                                                            <input type="date"
                                                                value="<?php echo $costing['first_line_lpa_date'] ?? ''; ?>"
                                                                id="first_line_lpa_date" name="first_line_lpa_date"
                                                                class="form-control" />
                                                            <hr>
                                                            <label class="form-label mt-3">17(b). Line probe assay (1st line
                                                                drugs)
                                                                (GenoType MTBDRplus V2)
                                                                <br><small>(Indicate all bands visible on the strip)</small>
                                                            </label>
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('first_line_drugs', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="checkbox"
                                                                                    name="first_line_drugs[]"
                                                                                    id="first_line_drugs<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?php foreach (explode(',', $costing['first_line_drugs']) as $values) {
                                                                                          if ($values == $value['id']) {
                                                                                              echo 'checked';
                                                                                          }
                                                                                      } ?>>
                                                                                <label class="form-check-label">
                                                                                    <?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                            </div>
                                                            <hr>
                                                            <label for="lpa1_mtb" class="form-label">17(c). MTB result on
                                                                LPA1</label>
                                                            <!-- radio -->
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('mtb_results', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="lpa1_mtb" id="lpa1_mtb<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?php if ($costing['lpa1_mtb'] == $value['id']) {
                                                                                          echo 'checked';
                                                                                      } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                    <button type="button"
                                                                        onclick="unsetRadio('lpa1_mtb')">Unset</button>
                                                                </div>
                                                            </div>
                                                            <hr>
                                                            <label for="lpa1_rif" class="form-label">17(d). RIF
                                                                result</label>
                                                            <!-- radio -->
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('rif_results', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="lpa1_rif" id="lpa1_rif<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?php if ($costing['lpa1_rif'] == $value['id']) {
                                                                                          echo 'checked';
                                                                                      } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                    <button type="button"
                                                                        onclick="unsetRadio('lpa1_rif')">Unset</button>
                                                                </div>
                                                            </div>
                                                            <hr>
                                                            <label for="lpa1_inh" class="form-label">17(e). INH
                                                                result</label>
                                                            <!-- radio -->
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('inh_results', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="lpa1_inh" id="lpa1_inh<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?php if ($costing['lpa1_inh'] == $value['id']) {
                                                                                          echo 'checked';
                                                                                      } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                    <button type="button"
                                                                        onclick="unsetRadio('lpa1_inh')">Unset</button>
                                                                </div>
                                                            </div>
                                                            <hr>
                                                        </div>
                                                    </div>

                                                    <!-- Second-Line LPA -->
                                                    <div class="col-sm-6 border rounded p-3">
                                                        <h5><strong>Second-Line LPA</strong></h5>
                                                        <hr>
                                                        <label for="second_line_lpa" class="form-label">18. Was second-line LPA
                                                            conducted?</label>
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('yes_no', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="second_line_lpa"
                                                                                id="second_line_lpa<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['second_line_lpa'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?> required>
                                                                            <label class="form-check-label">
                                                                                <?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                        <button type="button"
                                                            onclick="unsetRadio('second_line_lpa')">Unset</button>
                                                        <hr>
                                                        <div id="second_line_section">
                                                            <label class="form-label mt-3">19(a). Date of performing second-line
                                                                LPA?</label>
                                                            <input type="date"
                                                                value="<?php echo $costing['second_line_lpa_date'] ?? ''; ?>"
                                                                id="second_line_lpa_date" name="second_line_lpa_date"
                                                                class="form-control" />
                                                            <hr>
                                                            <label class="form-label mt-3">19(b). Line probe assay (2nd line
                                                                drugs)
                                                                (GenoType MTBDRsl V2)
                                                                <br><small>(Indicate all bands visible on the strip)</small>

                                                            </label>
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('second_line_drugs', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="checkbox"
                                                                                    name="second_line_drugs[]"
                                                                                    id="second_line_drugs<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?php foreach (explode(',', $costing['second_line_drugs']) as $values) {
                                                                                          if ($values == $value['id']) {
                                                                                              echo 'checked';
                                                                                          }
                                                                                      } ?>>
                                                                                <label class="form-check-label">
                                                                                    <?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                            </div>
                                                            <hr>
                                                            <label for="lpa2_mtb" class="form-label">19(c). MTB result on
                                                                LPA1</label>
                                                            <!-- radio -->
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('mtb_results', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="lpa2_mtb" id="lpa2_mtb<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?php if ($costing['lpa2_mtb'] == $value['id']) {
                                                                                          echo 'checked';
                                                                                      } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                    <button type="button"
                                                                        onclick="unsetRadio('lpa2_mtb')">Unset</button>
                                                                </div>
                                                            </div>
                                                            <hr>
                                                            <label for="lpa2_rfluoroquinolones" class="form-label">19(d).
                                                                RFluoroquinolones</label>
                                                            <!-- radio -->
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('rif_results', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="lpa2_rfluoroquinolones"
                                                                                    id="lpa2_rfluoroquinolones<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?php if ($costing['lpa2_rfluoroquinolones'] == $value['id']) {
                                                                                          echo 'checked';
                                                                                      } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                    <button type="button"
                                                                        onclick="unsetRadio('lpa2_rfluoroquinolones')">Unset</button>
                                                                </div>
                                                            </div>
                                                            <hr>
                                                            <label for="lpa2_aminoglycosides" class="form-label">19(e).
                                                                Aminoglycosides</label>
                                                            <!-- radio -->
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('rif_results', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="lpa2_aminoglycosides"
                                                                                    id="lpa2_aminoglycosides<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?php if ($costing['lpa2_aminoglycosides'] == $value['id']) {
                                                                                          echo 'checked';
                                                                                      } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                    <button type="button"
                                                                        onclick="unsetRadio('lpa2_aminoglycosides')">Unset</button>
                                                                </div>
                                                            </div>
                                                            <hr>
                                                            <label for="lpa2_kanamycin" class="form-label">19(f).
                                                                Kanamycin</label>
                                                            <!-- radio -->
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('rif_results', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="lpa2_kanamycin"
                                                                                    id="lpa2_kanamycin<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?php if ($costing['lpa2_kanamycin'] == $value['id']) {
                                                                                          echo 'checked';
                                                                                      } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                    <button type="button"
                                                                        onclick="unsetRadio('lpa2_kanamycin')">Unset</button>
                                                                </div>
                                                            </div>
                                                            <hr>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>

                                                <div class="card card-warning">
                                                    <div class="card-header">
                                                        <h3 class="card-title">Nanopore sequencing</h3>
                                                    </div>
                                                </div>
                                                <hr>

                                                <div class="row">
                                                    <div class="col-sm-3" id="nanopore_done_section">
                                                        <label for="nanopore_done" class="form-label">20. Was nanopore
                                                            sequencing done for this patient?</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('yes_no', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="nanopore_done"
                                                                                id="nanopore_done<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['nanopore_done'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                                <button type="button"
                                                                    onclick="unsetRadio('nanopore_done')">Unset</button>
                                                            </div>

                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3" id="sequencing_results_section">
                                                        <label for="sequencing_results" class="form-label">21(a). Were the
                                                            sequencing
                                                            results
                                                            exported to a *.csv-file?</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('yes_no', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="sequencing_results"
                                                                                id="sequencing_results<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['sequencing_results'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                                <button type="button"
                                                                    onclick="unsetRadio('sequencing_results')">Unset</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3" id="EPI2ME_section">
                                                        <label for="EPI2ME" class="form-label">21(b). Did you analyse your
                                                            data
                                                            using the EPI2ME workflow?</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('yes_no', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio" name="EPI2ME"
                                                                                id="EPI2ME<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['EPI2ME'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                                <button type="button"
                                                                    onclick="unsetRadio('EPI2ME')">Unset</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3" id="EPI2ME_vesrion_section">
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>21(c). EPI2ME software version used
                                                                    for the analysis (free text):</label>
                                                                <input class="form-control" type="text" name="EPI2ME_vesrion"
                                                                    id="EPI2ME_vesrion" value="<?php if ($costing['EPI2ME_vesrion']) {
                                                                        print_r($costing['EPI2ME_vesrion']);
                                                                    } ?>" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div id="nano_pore_results">
                                                    <div class="card card-warning">
                                                        <div class="card-header">
                                                            <h3 class="card-title">22. Nanopore Results</h3>
                                                        </div>
                                                    </div>

                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-sm-3" id="nano_amikacin">
                                                            <label for="nano_amikacin" class="form-label">22(a).
                                                                Amikacin(AMK)</label>
                                                            <!-- radio -->
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('nanopore_results', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="nano_amikacin"
                                                                                    id="nano_amikacin<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?php if ($costing['nano_amikacin'] == $value['id']) {
                                                                                          echo 'checked';
                                                                                      } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                                <button type="button"
                                                                    onclick="unsetRadio('nano_amikacin')">Unset</button>

                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3" id="nano_bedaquiline">
                                                            <label for="nano_bedaquiline"
                                                                class="form-label">22(b).Bedaquiline(BDQ)</label>
                                                            <!-- radio -->
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('nanopore_results', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="nano_bedaquiline"
                                                                                    id="nano_bedaquiline<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?php if ($costing['nano_bedaquiline'] == $value['id']) {
                                                                                          echo 'checked';
                                                                                      } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                                <button type="button"
                                                                    onclick="unsetRadio('nano_bedaquiline')">Unset</button>

                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3" id="nano_capreomycin">
                                                            <label for="nano_capreomycin" class="form-label">22(c).Capreomycin
                                                                (CAP)</label>
                                                            <!-- radio -->
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('nanopore_results', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="nano_capreomycin"
                                                                                    id="nano_capreomycin<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?php if ($costing['nano_capreomycin'] == $value['id']) {
                                                                                          echo 'checked';
                                                                                      } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                                <button type="button"
                                                                    onclick="unsetRadio('nano_capreomycin')">Unset</button>

                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3" id="nano_clofazimine">
                                                            <label for="nano_clofazimine" class="form-label">22(d).
                                                                Clofazimine(CFZ)</label>
                                                            <!-- radio -->
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('nanopore_results', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="nano_clofazimine"
                                                                                    id="nano_clofazimine<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?php if ($costing['nano_clofazimine'] == $value['id']) {
                                                                                          echo 'checked';
                                                                                      } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                                <button type="button"
                                                                    onclick="unsetRadio('nano_clofazimine')">Unset</button>

                                                            </div>
                                                        </div>
                                                    </div>

                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-sm-3" id="nano_delamanid">
                                                            <label for="clofazimine" class="form-label">22(e).
                                                                Delamanid(DLM)</label>
                                                            <!-- radio -->
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('nanopore_results', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="nano_delamanid"
                                                                                    id="nano_delamanid<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?php if ($costing['nano_delamanid'] == $value['id']) {
                                                                                          echo 'checked';
                                                                                      } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                                <button type="button"
                                                                    onclick="unsetRadio('nano_delamanid')">Unset</button>

                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3" id="nano_ethambutol">
                                                            <label for="nano_ethambutol" class="form-label">22(F).
                                                                Ethambutol(EMB)</label>
                                                            <!-- radio -->
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('nanopore_results', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="nano_ethambutol"
                                                                                    id="nano_ethambutol<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?php if ($costing['nano_ethambutol'] == $value['id']) {
                                                                                          echo 'checked';
                                                                                      } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                                <button type="button"
                                                                    onclick="unsetRadio('nano_ethambutol')">Unset</button>

                                                            </div>
                                                        </div>

                                                        <div class="col-sm-3" id="nano_ethionamide">
                                                            <label for="nano_ethionamide" class="form-label">22(g).
                                                                Ethionamide(ETO)</label>
                                                            <!-- radio -->
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('nanopore_results', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="nano_ethionamide"
                                                                                    id="nano_ethionamide<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?php if ($costing['nano_ethionamide'] == $value['id']) {
                                                                                          echo 'checked';
                                                                                      } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                                <button type="button"
                                                                    onclick="unsetRadio('nano_ethionamide')">Unset</button>

                                                            </div>
                                                        </div>

                                                        <div class="col-sm-3" id="nano_isoniazid">
                                                            <label for="nano_isoniazid" class="form-label">22(h). Isoniazid
                                                                (INH)</label>
                                                            <!-- radio -->
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('nanopore_results', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="nano_isoniazid"
                                                                                    id="nano_isoniazid<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?php if ($costing['nano_isoniazid'] == $value['id']) {
                                                                                          echo 'checked';
                                                                                      } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                                <button type="button"
                                                                    onclick="unsetRadio('nano_isoniazid')">Unset</button>

                                                            </div>
                                                        </div>
                                                    </div>

                                                    <hr>

                                                    <div class="row">
                                                        <div class="col-sm-3" id="nano_kanamycin">
                                                            <label for="nano_kanamycin" class="form-label">22(i).Kanamycin
                                                                (KAN)</label>
                                                            <!-- radio -->
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('nanopore_results', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="nano_kanamycin"
                                                                                    id="nano_kanamycin<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?php if ($costing['nano_kanamycin'] == $value['id']) {
                                                                                          echo 'checked';
                                                                                      } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                                <button type="button"
                                                                    onclick="unsetRadio('nano_kanamycin')">Unset</button>

                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3" id="nano_levofloxacin">
                                                            <label for="nano_levofloxacin" class="form-label">22(j).
                                                                Levofloxacin(LFX)</label>
                                                            <!-- radio -->
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('nanopore_results', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="nano_levofloxacin"
                                                                                    id="nano_levofloxacin<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?php if ($costing['nano_levofloxacin'] == $value['id']) {
                                                                                          echo 'checked';
                                                                                      } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                                <button type="button"
                                                                    onclick="unsetRadio('nano_levofloxacin')">Unset</button>

                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3" id="nano_linezolid">
                                                            <label for="nano_linezolid" class="form-label">22(k).
                                                                Linezolid(LZD)</label>
                                                            <!-- radio -->
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('nanopore_results', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="nano_linezolid"
                                                                                    id="nano_linezolid<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?php if ($costing['nano_linezolid'] == $value['id']) {
                                                                                          echo 'checked';
                                                                                      } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                                <button type="button"
                                                                    onclick="unsetRadio('nano_linezolid')">Unset</button>

                                                            </div>
                                                        </div>

                                                        <div class="col-sm-3" id="nano_moxifloxacin">
                                                            <label for="nano_moxifloxacin" class="form-label">22(l).
                                                                Moxifloxacin(MXF)</label>
                                                            <!-- radio -->
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('nanopore_results', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="nano_moxifloxacin"
                                                                                    id="nano_moxifloxacin<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?php if ($costing['nano_moxifloxacin'] == $value['id']) {
                                                                                          echo 'checked';
                                                                                      } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                                <button type="button"
                                                                    onclick="unsetRadio('nano_moxifloxacin')">Unset</button>

                                                            </div>
                                                        </div>
                                                    </div>

                                                    <hr>

                                                    <div class="row">
                                                        <div class="col-sm-3" id="nano_pretomanid">
                                                            <label for="nano_pretomanid" class="form-label">22(m).Pretomanid
                                                                (PMD)</label>
                                                            <!-- radio -->
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('nanopore_results', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="nano_pretomanid"
                                                                                    id="nano_pretomanid<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?php if ($costing['nano_pretomanid'] == $value['id']) {
                                                                                          echo 'checked';
                                                                                      } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                                <button type="button"
                                                                    onclick="unsetRadio('nano_pretomanid')">Unset</button>

                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3" id="nano_pyrazinamide">
                                                            <label for="nano_pyrazinamide" class="form-label">22(n).
                                                                Pyrazinamide(PZA)</label>
                                                            <!-- radio -->
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('nanopore_results', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="nano_pyrazinamide"
                                                                                    id="nano_pyrazinamide<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?php if ($costing['nano_pyrazinamide'] == $value['id']) {
                                                                                          echo 'checked';
                                                                                      } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                                <button type="button"
                                                                    onclick="unsetRadio('nano_pyrazinamide')">Unset</button>

                                                            </div>
                                                        </div>

                                                        <div class="col-sm-3" id="nano_rifampicin">
                                                            <label for="nano_rifampicin" class="form-label">22(o). Rifampicin
                                                                (RIF)</label>
                                                            <!-- radio -->
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('nanopore_results', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="nano_rifampicin"
                                                                                    id="nano_rifampicin<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?php if ($costing['nano_rifampicin'] == $value['id']) {
                                                                                          echo 'checked';
                                                                                      } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                                <button type="button"
                                                                    onclick="unsetRadio('nano_rifampicin')">Unset</button>

                                                            </div>
                                                        </div>


                                                        <div class="col-sm-3" id="nano_streptomycin">
                                                            <label for="nano_streptomycin" class="form-label">22(p).
                                                                Streptomycin(STM)</label>
                                                            <!-- radio -->
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('nanopore_results', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="nano_streptomycin"
                                                                                    id="nano_streptomycin<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?php if ($costing['nano_streptomycin'] == $value['id']) {
                                                                                          echo 'checked';
                                                                                      } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                                <button type="button"
                                                                    onclick="unsetRadio('nano_streptomycin')">Unset</button>

                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- <hr> -->

                                                    <!-- <div class="row">
                                                    <div class="col-sm-3" id="nano_cycloserine">
                                                        <label for="nano_cycloserine" class="form-label">22().
                                                            Cycloserine</label>
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('nanopore_results', 'status', 1) as $value) { ?>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="radio"
                                                                            name="nano_cycloserine"
                                                                            id="nano_cycloserine<?= $value['id']; ?>"
                                                                            value="<?= $value['id']; ?>" <?php if ($costing['nano_cycloserine'] == $value['id']) {
                                                                                  echo 'checked';
                                                                              } ?>>
                                                                        <label
                                                                            class="form-check-label"><?= $value['name']; ?></label>
                                                                    </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('nano_cycloserine')">Unset</button>

                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3" id="nano_terizidone">
                                                        <label for="nano_terizidone" class="form-label">22().
                                                            Terizidone</label>
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('nanopore_results', 'status', 1) as $value) { ?>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="radio"
                                                                            name="nano_terizidone"
                                                                            id="nano_terizidone<?= $value['id']; ?>"
                                                                            value="<?= $value['id']; ?>" <?php if ($costing['nano_terizidone'] == $value['id']) {
                                                                                  echo 'checked';
                                                                              } ?>>
                                                                        <label
                                                                            class="form-check-label"><?= $value['name']; ?></label>
                                                                    </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('nano_terizidone')">Unset</button>

                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3" id="nano_imipenem">
                                                        <label for="nano_imipenem" class="form-label">22(). Imipenem</label>
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('nanopore_results', 'status', 1) as $value) { ?>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="radio"
                                                                            name="nano_imipenem"
                                                                            id="nano_imipenem<?= $value['id']; ?>"
                                                                            value="<?= $value['id']; ?>" <?php if ($costing['nano_imipenem'] == $value['id']) {
                                                                                  echo 'checked';
                                                                              } ?>>
                                                                        <label
                                                                            class="form-check-label"><?= $value['name']; ?></label>
                                                                    </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('nano_imipenem')">Unset</button>

                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3" id="nano_cilastatin">
                                                        <label for="nano_cilastatin" class="form-label">22().
                                                            Cilastatin</label>
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('nanopore_results', 'status', 1) as $value) { ?>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="radio"
                                                                            name="nano_cilastatin"
                                                                            id="nano_cilastatin<?= $value['id']; ?>"
                                                                            value="<?= $value['id']; ?>" <?php if ($costing['nano_cilastatin'] == $value['id']) {
                                                                                  echo 'checked';
                                                                              } ?>>
                                                                        <label
                                                                            class="form-check-label"><?= $value['name']; ?></label>
                                                                    </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('nano_cilastatin')">Unset</button>

                                                        </div>
                                                    </div>
                                                </div>

                                                <hr>

                                                <div class="row">
                                                    <div class="col-sm-3" id="nano_meropenem">
                                                        <label for="nano_meropenem" class="form-label">22().
                                                            Meropenem</label>
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('nanopore_results', 'status', 1) as $value) { ?>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="radio"
                                                                            name="nano_meropenem"
                                                                            id="nano_meropenem<?= $value['id']; ?>"
                                                                            value="<?= $value['id']; ?>" <?php if ($costing['nano_meropenem'] == $value['id']) {
                                                                                  echo 'checked';
                                                                              } ?>>
                                                                        <label
                                                                            class="form-check-label"><?= $value['name']; ?></label>
                                                                    </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('nano_meropenem')">Unset</button>

                                                        </div>
                                                    </div>


                                                    <div class="col-sm-3" id="nano_prothionamide">
                                                        <label for="nano_prothionamide" class="form-label">22().
                                                            Prothionamide</label>
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('nanopore_results', 'status', 1) as $value) { ?>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="radio"
                                                                            name="nano_prothionamide"
                                                                            id="nano_prothionamide<?= $value['id']; ?>"
                                                                            value="<?= $value['id']; ?>" <?php if ($costing['nano_prothionamide'] == $value['id']) {
                                                                                  echo 'checked';
                                                                              } ?>>
                                                                        <label
                                                                            class="form-check-label"><?= $value['name']; ?></label>
                                                                    </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('nano_prothionamide')">Unset</button>

                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3" id="nano_para_aminosalicylic_acid">
                                                        <label for="nano_para_aminosalicylic_acid" class="form-label">22().
                                                            Para-
                                                            aminosalicylic acid</label>
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('nanopore_results', 'status', 1) as $value) { ?>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="radio"
                                                                            name="nano_para_aminosalicylic_acid"
                                                                            id="nano_para_aminosalicylic_acid<?= $value['id']; ?>"
                                                                            value="<?= $value['id']; ?>" <?php if ($costing['nano_para_aminosalicylic_acid'] == $value['id']) {
                                                                                  echo 'checked';
                                                                              } ?>>
                                                                        <label
                                                                            class="form-check-label"><?= $value['name']; ?></label>
                                                                    </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('nano_para_aminosalicylic_acid')">Unset</button>
                                                        </div>
                                                    </div>
                                                </div> -->
                                                    <hr>
                                                </div>
                                                <div class="card card-warning">
                                                    <div class="card-header">
                                                        <h3 class="card-title">Remarks</h3>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>23. Any remarks on any of the tests above:</label>
                                                                <textarea class="form-control" name="remarks" rows="3"
                                                                    placeholder="Type comments here..."><?php if ($costing['remarks']) {
                                                                        print_r($costing['remarks']);
                                                                    } ?>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <hr>
                                                <div class="card card-warning">
                                                    <div class="card-header">
                                                        <h3 class="card-title">FORM STATUS</h3>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <label>Complete?</label>
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('form_completness', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="form_status" id="form_status<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>"
                                                                                <?= ($costing['form_status'] == $value['id']) ? 'checked' : ''; ?> required onchange="updateFormStatus()">
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('form_status')">Unset</button>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-4">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Completed Date</label>
                                                                <input class="form-control" type="date" name="date_completed"
                                                                    id="date_completed"
                                                                    value="<?= ($costing['date_completed']) ? $costing['date_completed'] : ''; ?>" />
                                                                <span id="date_completed_error" class="text-danger"></span>
                                                            </div>
                                                        </div>
                                                        <?php if ($costing['form_status'] >= 2) { ?>

                                                                <div class="row-form clearfix">
                                                                    <div class="form-group">
                                                                        <label>Completed By</label>
                                                                        <input class="form-control" type="text"
                                                                            value="<?= $override->get('user', 'id', $costing['completed_by'])[0]['username']; ?>"
                                                                            readonly />
                                                                    </div>
                                                                </div>
                                                        <?php } ?>
                                                    </div>

                                                    <div class="col-sm-4">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Verified Date</label>
                                                                <input class="form-control" type="date" name="date_verified"
                                                                    id="date_verified"
                                                                    value="<?= ($costing['date_verified']) ? $costing['date_verified'] : ''; ?>" />
                                                                <span id="date_verified_error" class="text-danger"></span>
                                                            </div>
                                                        </div>
                                                        <?php if ($costing['form_status'] >= 3) { ?>
                                                                <div class="row-form clearfix">
                                                                    <div class="form-group">
                                                                        <label>Verified By</label>
                                                                        <input class="form-control" type="text"
                                                                            value="<?= $override->get('user', 'id', $costing['verified_by'])[0]['username']; ?>"
                                                                            readonly />
                                                                    </div>
                                                                </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                                <hr>
                                            </div>
                                            <!-- /.card-body -->
                                            <div class="card-footer">
                                                <a href="info.php?id=6&status=<?= $_GET['status']; ?>&sid=<?= $_GET['sid']; ?>&facility_id=<?= $_GET['facility_id']; ?>&page=<?= $_GET['page']; ?>"
                                                    class="btn btn-default">Back</a>
                                                <?php
                                                if ($user->data()->site_id == 6 || $user->data()->site_id == 13 || $user->data()->site_id == 20 || $user->data()->site_id == 22) {
                                                    ?>
                                                        <input type="submit" name="add_diagnosis_test" value="Submit"
                                                            class="btn btn-primary">
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

        <?php } elseif ($_GET['id'] == 15) { ?>
                <?php
                $screening = $override->getNews('screening', 'status', 1, 'id', $_GET['sid'])[0];
                $costing = $override->getNews('diagnosis', 'status', 1, 'enrollment_id', $_GET['sid'])[0];
                ?>
                <!-- Content Wrapper. Contains page content -->
                <div class="content-wrapper">
                    <!-- Content Header (Page header) -->
                    <section class="content-header">
                        <div class="container-fluid">
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <?php if ($costing) { ?>
                                            <h1>Update New Diagnosis (PID :<?= $screening['pid'] ?>)</h1>
                                    <?php } else { ?>
                                            <h1>Add Diagnosis (PID :<?= $screening['pid'] ?>)</h1>
                                    <?php } ?>
                                </div>
                                <div class="col-sm-6">
                                    <ol class="breadcrumb float-sm-right">
                                        <li class="breadcrumb-item"><a
                                                href="info.php?id=6&status=<?= $_GET['status']; ?>&sid=<?= $_GET['sid']; ?>&facility_id=<?= $_GET['facility_id']; ?>&page=<?= $_GET['page']; ?>">
                                                < Back</a>
                                        </li>&nbsp;&nbsp;
                                        <li class="breadcrumb-item"><a href="index1.php">Home</a></li>&nbsp;&nbsp;
                                        <li class="breadcrumb-item"><a
                                                href="info.php?id=6&status=<?= $_GET['status']; ?>&facility_id=<?= $_GET['facility_id']; ?>&page=<?= $_GET['page']; ?>">
                                                Go to enrolled list > </a>
                                        </li>&nbsp;&nbsp;
                                        <?php if (!$costing) { ?>
                                                <li class="breadcrumb-item active">Add New Diagnosis Data</li>
                                        <?php } else { ?>
                                                <li class="breadcrumb-item active">Update Diagnosis Data</li>
                                        <?php } ?>
                                    </ol>
                                </div>
                            </div>
                        </div><!-- /.container-fluid -->
                    </section>
                    <div class="modal fade" id="addMedModal">
                        <div class="modal-dialog modal-lg">
                            <form method="post">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Add new
                                            Regimen Changes- <strong class="visit-day-highlight"><?= $_GET['pid']; ?>
                                                - (
                                                <?= $sites['name'] ?>)
                                            </strong></h4> <button type="button" class="close" data-dismiss="modal"
                                            aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- First Row: 3 Inputs -->
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label> Date</label>
                                                    <input type="date" class="form-control" name="date"
                                                        value="<?php if ($treatment['date']) {
                                                            print_r($treatment['date']);
                                                        } ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>Drug</label>
                                                    <input type="text" class="form-control" name="drug"
                                                        value="<?php if ($treatment['drug']) {
                                                            print_r($treatment['drug']);
                                                        } ?>">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Second Row: 2 Inputs -->
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>Medication
                                                        Name</label>
                                                    <select name="changes" id="changes" class="form-control select2"
                                                        style="width: 100%;" required>
                                                        <?php if ($medications[0]['name']) { ?>
                                                                <option value="<?= $medications[0]['id'] ?>">
                                                                    <?= $medications[0]['name']; ?>
                                                                </option>
                                                        <?php } ?>
                                                        <?php foreach ($override->get('regimen_changes', 'status', 1) as $medication) { ?>
                                                                <option value="<?= $medication['id'] ?>">
                                                                    <?= $medication['name']; ?>
                                                                </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>Action</label>
                                                    <select name="medication_action" id="medication_action"
                                                        class="form-control select2" style="width: 100%;" required>
                                                        <?php if ($medication_actions[0]['name']) { ?>
                                                                <option value="<?= $medication_actions[0]['id'] ?>">
                                                                    <?= $medication_actions[0]['name']; ?>
                                                                </option>
                                                        <?php } ?>
                                                        <?php foreach ($override->get('regimen_changes_reasons', 'status', 1) as $medication_action) { ?>
                                                                <option value="<?= $medication_action['id'] ?>">
                                                                    <?= $medication_action['name']; ?>
                                                                </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Third Row: 3 Inputs (Including Textarea) -->
                                        <div class="row">
                                            <div class="col-sm-9">
                                                <div class="form-group">
                                                    <label>Dose
                                                        Description</label>
                                                    <textarea class="form-control" name="specify" rows="3">
                                                        <?php if ($treatment['specify']) {
                                                            print_r($treatment['specify']);
                                                        } ?>
                                                    </textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer justify-content-between">
                                        <input type="hidden" name="diagnosis_id" id="diagnosis_id"
                                            value="<?= $treatment['diagnosis_id'] ?>">
                                        <input type="hidden" name="enrollment_id" id="enrollment_id"
                                            value="<?= $treatment['enrollment_id'] ?>">
                                        <input type="hidden" name="id" value="<?= $treatment['id'] ?>">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        <input type="submit" name="update_medication" class="btn btn-primary"
                                            value="Save Medication">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- Main content -->
                    <section class="content">
                        <div class="container-fluid">
                            <div class="row">
                                <!-- right column -->
                                <div class="col-md-12">
                                    <!-- general form elements disabled -->
                                    <div class="card card-warning">
                                        <div class="card-header">
                                            <h3 class="card-title">Final diagnosis</h3>
                                        </div>
                                        <!-- /.card-header -->
                                        <form id="validation" enctype="multipart/form-data" method="post" autocomplete="off">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <label for="tb_diagnosis" class="form-label">4(a). Was a TB diagnosis
                                                            made?</label>
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('yes_no', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="tb_diagnosis"
                                                                                id="tb_diagnosis<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['tb_diagnosis'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?> required>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('tb_diagnosis')">Unset</button>

                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3" id="tb_diagnosis_date_section">
                                                        <div class="mb-3">
                                                            <label for="tb_diagnosis_date" class="form-label">4(b). Date of TB
                                                                diagnosis:</label>
                                                            <input type="date" value="<?php if ($costing['tb_diagnosis_date']) {
                                                                print_r($costing['tb_diagnosis_date']);
                                                            } ?>" id="tb_diagnosis_date" name="tb_diagnosis_date"
                                                                class="form-control" placeholder="tb_diagnosis_date" />
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3" id="tb_diagnosis_made_section">
                                                        <label for="tb_diagnosis_made" class="form-label">5. How was the TB
                                                            diagnosis made? </label>
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('tb_diagnosis_made', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="tb_diagnosis_made"
                                                                                id="tb_diagnosis_made<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['tb_diagnosis_made'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                                <button type="button"
                                                                    onclick="unsetRadio('tb_diagnosis_made')">Unset</button>
                                                            </div>
                                                            <div id="diagnosis_made_other_section">
                                                                <label for="diagnosis_made_other" class="form-label">If Other
                                                                    Specify ?</label>
                                                                <input type="text" value="<?php if ($costing['diagnosis_made_other']) {
                                                                    print_r($costing['diagnosis_made_other']);
                                                                } ?>" id="diagnosis_made_other" name="diagnosis_made_other"
                                                                    class="form-control" placeholder="If Other Specify here" />
                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div class="col-sm-3" id="bacteriological_diagnosis_section">
                                                        <label for="bacteriological_diagnosis" class="form-label">6. On what
                                                            test result(s) was the bacteriological diagnosis based?<br>
                                                            <small>Positive test result:</small></label>
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('bacteriological_diagnosis', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="bacteriological_diagnosis"
                                                                                id="bacteriological_diagnosis<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['bacteriological_diagnosis'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('bacteriological_diagnosis')">Unset</button>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3" id="tb_diagnosed_clinically_section">
                                                        <label for="tb_diagnosed_clinically" class="form-label">7. In case TB
                                                            was diagnosed clinically, based on what information was the
                                                            diagnosis made? </label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('tb_diagnosed_clinically', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                name="tb_diagnosed_clinically[]"
                                                                                id="tb_diagnosed_clinically<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php foreach (explode(',', $costing['tb_diagnosed_clinically']) as $values) {
                                                                                      if ($values == $value['id']) {
                                                                                          echo 'checked';
                                                                                      }
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                                <div id="tb_clinically_other_section">
                                                                    <label for="tb_clinically_other" class="form-label">Other
                                                                        Specify ?</label>
                                                                    <input type="text" value="<?php if ($costing['tb_clinically_other']) {
                                                                        print_r($costing['tb_clinically_other']);
                                                                    } ?>" id="tb_clinically_other"
                                                                        name="tb_clinically_other" class="form-control"
                                                                        placeholder="Enter here" />
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>

                                                <div class="row">
                                                    <div class="col-sm-4" id="clinician_received_date_section">
                                                        <div class="mb-3">
                                                            <label for="clinician_received_date" class="form-label">6(a). Date
                                                                result
                                                                received by clinician:</label>
                                                            <input type="date" value="<?php if ($costing['clinician_received_date']) {
                                                                print_r($costing['clinician_received_date']);
                                                            } ?>" id="clinician_received_date"
                                                                name="clinician_received_date" class="form-control"
                                                                placeholder="clinician_received_date" />
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4" id="tb_treatment_section">
                                                        <label for="tb_treatment" class="form-label">8(a). Was TB treatment
                                                            started?</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('tb_treatment', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="tb_treatment"
                                                                                id="tb_treatment<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['tb_treatment'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                                <button type="button"
                                                                    onclick="unsetRadio('tb_treatment')">Unset</button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-4" id="tb_treatment_section">
                                                        <!-- <label for="tb_treatment" class="form-label">8(a). Was TB
                                                        treatment
                                                        started?</label> -->
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <div class="form-check">
                                                                    <div id="tb_treatment_date_section">
                                                                        <label for="tb_treatment_date" class="form-label">8(b).
                                                                            What was
                                                                            treatment start date ?</label>
                                                                        <input type="date" value="<?php if ($costing['tb_treatment_date']) {
                                                                            print_r($costing['tb_treatment_date']);
                                                                        } ?>" id="tb_treatment_date"
                                                                            name="tb_treatment_date" class="form-control"
                                                                            placeholder="Enters here" />
                                                                    </div>

                                                                    <div id="tb_facility_section">
                                                                        <label for="tb_facility" class="form-label">8(c).
                                                                            (Name
                                                                            health facility):</label>
                                                                        <input type="text" value="<?php if ($costing['tb_facility']) {
                                                                            print_r($costing['tb_facility']);
                                                                        } ?>" id="tb_facility" name="tb_facility"
                                                                            class="form-control" placeholder="Enter heres" />
                                                                    </div>

                                                                    <div id="tb_reason_section">
                                                                        <label for="tb_reason" class="form-label">8(d).
                                                                            reason
                                                                            (specify):</label>
                                                                        <input type="text" value="<?php if ($costing['tb_reason']) {
                                                                            print_r($costing['tb_reason']);
                                                                        } ?>" id="tb_reason" name="tb_reason"
                                                                            class="form-control" placeholder="Enter here" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="row">
                                                    <div class="col-sm-4" id="tb_register_number_section">
                                                        <label for="tb_register_number" class="form-label">9(a). TB register
                                                            number</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <div class="form-check">
                                                                    <input type="text" value="<?php if ($costing['tb_register_number']) {
                                                                        print_r($costing['tb_register_number']);
                                                                    } ?>" id="tb_register_number" name="tb_register_number"
                                                                        class="form-control" placeholder="Enter here" />
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-4" id="tb_regimen_prescribed_section">
                                                        <label for="tb_regimen" class="form-label">9(b). What treatment
                                                            regimen
                                                            was prescribed? </label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('tb_regimen', 'status1', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="tb_regimen" id="tb_regimen<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['tb_regimen'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('tb_regimen')">Unset</button>
                                                        </div>
                                                        <div id="tb_regimen_other_section">
                                                            <label for="tb_regimen_other" class="form-label">Regimens
                                                                specify</label>
                                                            <input type="text" value="<?php if ($costing['tb_regimen_other']) {
                                                                print_r($costing['tb_regimen_other']);
                                                            } ?>" id="tb_regimen_other" name="tb_regimen_other"
                                                                class="form-control" placeholder="Enter here" />

                                                        </div>
                                                    </div>

                                                    <div class="col-sm-4" id="regimen_changed_section">
                                                        <label for="regimen_changed" class="form-label">10(a). Was the regimen
                                                            changed during the treatment?</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('yes_no', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="regimen_changed"
                                                                                id="regimen_changed<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['regimen_changed'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                                <button type="button"
                                                                    onclick="unsetRadio('regimen_changed')">Unset</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-info mb-3" data-toggle="modal"
                                                    data-target="#addMedModal">
                                                    <ion-icon name='add-circle-outline'></ion-icon> Add New
                                                    Medication
                                                </button>
                                                <div class="row" id="table_section">
                                                    <hr>
                                                    <label class="fw-bold text-center d-block">10(b). List all treatment changes
                                                        below.</label>
                                                    <?php if ($costing['id']) {
                                                        $diagnosis_id = $costing['id'];
                                                    } else {
                                                        echo 0;
                                                    }
                                                    ?>
                                                    <table class="table table-bordered rounded">
                                                        <thead>
                                                            <tr class="text-center fw-bold">
                                                                <th>Date</th>
                                                                <th>Drug</th>
                                                                <th>Type of Change</th>
                                                                <th>Reason for Change</th>
                                                                <!-- <th>Specify (if Other)</th> -->
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="tbody">
                                                            <!-- Existing table body content remains the same -->
                                                            <?php $x = 1;
                                                            foreach ($override->getNewsAs2('treatment_changes', 'enrollment_id', $_GET['sid'], 'status', 1) as $treatment) {
                                                                $medications = $override->getNews('regimen_changes', 'status', 1, 'id', $treatment['changes']);
                                                                $medication_actions = $override->getNews('regimen_changes_reasons', 'status', 1, 'id', $treatment['reason']);
                                                                $sites = $override->getNews('sites', 'status', 1, 'id', $treatment['facility_id'])[0];
                                                                ?>
                                                                    <tr>
                                                                        <td><?= $x; ?> /
                                                                            <hr> <?= $treatment['date'] ?>
                                                                        </td>
                                                                        <td><?= $treatment['drug'] ?> /
                                                                            <hr> <?= $treatment['drug'] ?>
                                                                        </td>
                                                                        <td><?= $medications[0]['name']; ?></td>
                                                                        <td><?= $medication_actions[0]['name']; ?></td>
                                                                        <td><?= $treatment['specify'] ?></td>
                                                                        <td>

                                                                            <span class="badge bg-info">
                                                                                <a href="#update_med<?= $treatment['id'] ?>"
                                                                                    role="button" data-toggle="modal">Update</a>
                                                                            </span>
                                                                            <br>
                                                                            <hr>
                                                                            <span class="badge bg-danger">
                                                                                <a href="#delete_med<?= $treatment['id'] ?>"
                                                                                    role="button" data-toggle="modal">Delete</a>
                                                                            </span>
                                                                        </td>
                                                                    </tr>
                                                                    <!-- Existing table rows and modals -->
                                                                    <!-- Add Medication Modal -->
                                                                    <div class="modal fade" id="update_med<?= $treatment['id'] ?>">
                                                                        <div class="modal-dialog modal-lg">
                                                                            <form method="post">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header">
                                                                                        <h4 class="modal-title">Update
                                                                                            Regimen - <strong
                                                                                                class="visit-day-highlight"><?= $_GET['sid']; ?>
                                                                                                - (
                                                                                                <?= $sites['name'] ?>)
                                                                                            </strong></h4> <button type="button"
                                                                                            class="close" data-dismiss="modal"
                                                                                            aria-label="Close">
                                                                                            <span aria-hidden="true">&times;</span>
                                                                                        </button>
                                                                                    </div>
                                                                                    <div class="modal-body">
                                                                                        <!-- First Row: 3 Inputs -->
                                                                                        <div class="row">
                                                                                            <div class="col-sm-6">
                                                                                                <div class="form-group">
                                                                                                    <label> Date</label>
                                                                                                    <input type="date"
                                                                                                        class="form-control" name="date"
                                                                                                        value="<?php if ($treatment['date']) {
                                                                                                            print_r($treatment['date']);
                                                                                                        } ?>"
                                                                                                        required>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-sm-6">
                                                                                                <div class="form-group">
                                                                                                    <label>Drug</label>
                                                                                                    <input type="text"
                                                                                                        class="form-control" name="drug"
                                                                                                        value="<?php if ($treatment['drug']) {
                                                                                                            print_r($treatment['drug']);
                                                                                                        } ?>">
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>

                                                                                        <!-- Second Row: 2 Inputs -->
                                                                                        <div class="row">
                                                                                            <div class="col-sm-6">
                                                                                                <div class="form-group">
                                                                                                    <label>Medication
                                                                                                        Name</label>
                                                                                                    <select name="changes" id="changes"
                                                                                                        class="form-control select2"
                                                                                                        style="width: 100%;" required>
                                                                                                        <?php if ($medications[0]['name']) { ?>
                                                                                                                <option
                                                                                                                    value="<?= $medications[0]['id'] ?>">
                                                                                                                    <?= $medications[0]['name']; ?>
                                                                                                                </option>
                                                                                                        <?php } ?>
                                                                                                        <?php foreach ($override->get('regimen_changes', 'status', 1) as $medication) { ?>
                                                                                                                <option
                                                                                                                    value="<?= $medication['id'] ?>">
                                                                                                                    <?= $medication['name']; ?>
                                                                                                                </option>
                                                                                                        <?php } ?>
                                                                                                    </select>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-sm-6">
                                                                                                <div class="form-group">
                                                                                                    <label>Action</label>
                                                                                                    <select name="medication_action"
                                                                                                        id="medication_action"
                                                                                                        class="form-control select2"
                                                                                                        style="width: 100%;" required>
                                                                                                        <?php if ($medication_actions[0]['name']) { ?>
                                                                                                                <option
                                                                                                                    value="<?= $medication_actions[0]['id'] ?>">
                                                                                                                    <?= $medication_actions[0]['name']; ?>
                                                                                                                </option>
                                                                                                        <?php } ?>
                                                                                                        <?php foreach ($override->get('regimen_changes_reasons', 'status', 1) as $medication_action) { ?>
                                                                                                                <option
                                                                                                                    value="<?= $medication_action['id'] ?>">
                                                                                                                    <?= $medication_action['name']; ?>
                                                                                                                </option>
                                                                                                        <?php } ?>
                                                                                                    </select>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>

                                                                                        <!-- Third Row: 3 Inputs (Including Textarea) -->
                                                                                        <div class="row">
                                                                                            <div class="col-sm-9">
                                                                                                <div class="form-group">
                                                                                                    <label>Dose
                                                                                                        Description</label>
                                                                                                    <textarea class="form-control"
                                                                                                        name="specify" rows="3">
                                                                                                                                            <?php if ($treatment['specify']) {
                                                                                                                                                print_r($treatment['specify']);
                                                                                                                                            } ?>
                                                                                                                                        </textarea>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="modal-footer justify-content-between">
                                                                                        <input type="hidden" name="diagnosis_id"
                                                                                            id="diagnosis_id"
                                                                                            value="<?= $treatment['diagnosis_id'] ?>">
                                                                                        <input type="hidden" name="enrollment_id"
                                                                                            id="enrollment_id"
                                                                                            value="<?= $treatment['enrollment_id'] ?>">
                                                                                        <input type="hidden" name="id"
                                                                                            value="<?= $treatment['id'] ?>">
                                                                                        <button type="button" class="btn btn-default"
                                                                                            data-dismiss="modal">Close</button>
                                                                                        <input type="submit" name="update_medication"
                                                                                            class="btn btn-primary"
                                                                                            value="Save Medication">
                                                                                    </div>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>

                                                                    <div class="modal fade" id="delete_med<?= $treatment['id'] ?>"
                                                                        tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                                                                        aria-hidden="true">
                                                                        <div class="modal-dialog">
                                                                            <form method="post">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header">
                                                                                        <button type="button" class="close"
                                                                                            data-dismiss="modal"><span
                                                                                                aria-hidden="true">&times;</span><span
                                                                                                class="sr-only">Close</span></button>
                                                                                        <h4>Delete this Regimen</h4>
                                                                                    </div>
                                                                                    <div class="modal-body">
                                                                                        <strong style="font-weight: bold;color: red">
                                                                                            <p>Are you sure you want to
                                                                                                delete this Regimen ?
                                                                                            </p>
                                                                                        </strong>
                                                                                    </div>
                                                                                    <div class="modal-footer">
                                                                                        <input type="hidden" name="diagnosis_id"
                                                                                            id="diagnosis_id"
                                                                                            value="<?= $treatment['diagnosis_id'] ?>">
                                                                                        <input type="hidden" name="enrollment_id"
                                                                                            id="enrollment_id"
                                                                                            value="<?= $treatment['enrollment_id'] ?>">
                                                                                        <input type="hidden" name="id"
                                                                                            value="<?= $treatment['id'] ?>">
                                                                                        <input type="submit" name="delete_medication"
                                                                                            value="Delete" class="btn btn-danger">
                                                                                        <button class="btn btn-default"
                                                                                            data-dismiss="modal"
                                                                                            aria-hidden="true">Close</button>
                                                                                    </div>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                    <?php $x++;
                                                            } ?>
                                                        </tbody>
                                                    </table>

                                                    <!-- <button type="button" class="btn btn-primary"
                                                    onclick="addRow('new', '', '', '', '', '', <?= $diagnosis_id ?>,<?= $_GET['sid'] ?>,<?= $screening['facility_id'] ?>,<?= $user->data()->id ?>)">Add
                                                    Row</button> -->
                                                    <!-- <button type="button" class="btn btn-success"
                                                    onclick="saveTreatmentChanges()">Save Changes</button> -->
                                                </div>
                                                <!-- <script src="treatmentChanges.js"></script> -->
                                                <div id="tb_otcome_section">
                                                    <hr>
                                                    <div class="card card-warning">
                                                        <div class="card-header">
                                                            <h3 class="card-title"> Treatment outcome </h3>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <label for="tb_otcome2" class="form-label">11(a). Treatment
                                                                outcome</label>
                                                            <!-- radio -->
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('tb_otcome2', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="tb_otcome2"
                                                                                    id="tb_otcome2<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?php if ($costing['tb_otcome2'] == $value['id']) {
                                                                                          echo 'checked';
                                                                                      } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                                <button type="button"
                                                                    onclick="unsetRadio('tb_otcome2')">Unset</button>
                                                            </div>
                                                        </div>

                                                        <div class="col-sm-6" id="tb_otcome2_section">
                                                            <label for="tb_otcome2_date_ltf" class="form-label">11(b). Date
                                                                treatment outcome assigned</label>
                                                            <!-- radio -->
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <div class="form-check">
                                                                        <input type="date" value="<?php if ($costing['tb_otcome2_date']) {
                                                                            print_r($costing['tb_otcome2_date']);
                                                                        } ?>" id="tb_otcome2_date" name="tb_otcome2_date"
                                                                            class="form-control" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <hr>
                                                </div>

                                                <div id="tb_other_diagnosis_section">
                                                    <div class="card card-warning">
                                                        <div class="card-header">
                                                            <h3 class="card-title">Diagnosis other than TB</h3>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <label for="tb_other_diagnosis" class="form-label">12a. What
                                                                diagnosis
                                                                other
                                                                than TB was made? </label>
                                                            <!-- radio -->
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('tb_other_diagnosis', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="tb_other_diagnosis"
                                                                                    id="tb_other_diagnosis<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?php if ($costing['tb_other_diagnosis'] == $value['id']) {
                                                                                          echo 'checked';
                                                                                      } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                                <button type="button"
                                                                    onclick="unsetRadio('tb_other_diagnosis')">Unset</button>
                                                            </div>
                                                            <div id="tb_other_specify_section">
                                                                <label for="tb_other_specify" id="tb_other_section"
                                                                    class="form-label">If
                                                                    Other Mention</label>
                                                                <label for="tb_other_specify" id="tb_bacterial_section"
                                                                    class="form-label">If
                                                                    Bacterial pneumonia, specify causative species if
                                                                    known</label>
                                                                <input type="text" value="<?php if ($costing['tb_other_specify']) {
                                                                    print_r($costing['tb_other_specify']);
                                                                } ?>" id="tb_other_specify" name="tb_other_specify"
                                                                    class="form-control" placeholder="Enter here" />
                                                            </div>
                                                        </div>

                                                        <div class="col-sm-6" id="tb_diagnosis_made2">
                                                            <label for="tb_diagnosis_made" class="form-label">12b. How was this
                                                                diagnosis made?</label>
                                                            <!-- radio -->
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('tb_diagnosis_made3', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="tb_diagnosis_made2"
                                                                                    id="tb_diagnosis_made2<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?php if ($costing['tb_diagnosis_made2'] == $value['id']) {
                                                                                          echo 'checked';
                                                                                      } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('tb_diagnosis_made2')">Unset</button>

                                                        </div>

                                                    </div>
                                                    <hr>
                                                </div>

                                                <div class="card card-warning">
                                                    <div class="card-header">
                                                        <h3 class="card-title">ANY COMENT OR REMARKS</h3>
                                                    </div>
                                                </div>
                                                <hr>

                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>13. Any comments or remarks regarding this
                                                                    patient</label>
                                                                <textarea class="form-control" name="comments" rows="3"
                                                                    placeholder="Type comments here..."><?php if ($costing['comments']) {
                                                                        print_r($costing['comments']);
                                                                    } ?>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>

                                                <div class="card card-warning">
                                                    <div class="card-header">
                                                        <h3 class="card-title">FORM STATUS</h3>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <label>Complete?</label>
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('form_completness', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="form_status" id="form_status<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>"
                                                                                <?= ($costing['form_status'] == $value['id']) ? 'checked' : ''; ?> required onchange="updateFormStatus()">
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('form_status')">Unset</button>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-4">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Completed Date</label>
                                                                <input class="form-control" type="date" name="date_completed"
                                                                    id="date_completed"
                                                                    value="<?= ($costing['date_completed']) ? $costing['date_completed'] : ''; ?>" />
                                                                <span id="date_completed_error" class="text-danger"></span>
                                                            </div>
                                                        </div>
                                                        <?php if ($costing['form_status'] >= 2) { ?>

                                                                <div class="row-form clearfix">
                                                                    <div class="form-group">
                                                                        <label>Completed By</label>
                                                                        <input class="form-control" type="text"
                                                                            value="<?= $override->get('user', 'id', $costing['completed_by'])[0]['username']; ?>"
                                                                            readonly />
                                                                    </div>
                                                                </div>
                                                        <?php } ?>
                                                    </div>

                                                    <div class="col-sm-4">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Verified Date</label>
                                                                <input class="form-control" type="date" name="date_verified"
                                                                    id="date_verified"
                                                                    value="<?= ($costing['date_verified']) ? $costing['date_verified'] : ''; ?>" />
                                                                <span id="date_verified_error" class="text-danger"></span>
                                                            </div>
                                                        </div>
                                                        <?php if ($costing['form_status'] >= 3) { ?>
                                                                <div class="row-form clearfix">
                                                                    <div class="form-group">
                                                                        <label>Verified By</label>
                                                                        <input class="form-control" type="text"
                                                                            value="<?= $override->get('user', 'id', $costing['verified_by'])[0]['username']; ?>"
                                                                            readonly />
                                                                    </div>
                                                                </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                                <hr>
                                            </div>
                                            <!-- /.card-body -->
                                            <div class="card-footer">
                                                <a href="info.php?id=6&status=<?= $_GET['status']; ?>&sid=<?= $_GET['sid']; ?>&facility_id=<?= $_GET['facility_id']; ?>&page=<?= $_GET['page']; ?>"
                                                    class="btn btn-default">Back</a>
                                                <input type="submit" name="add_diagnosis" value="Submit"
                                                    class="btn btn-primary">
                                            </div>
                                        </form>
                                    </div>
                                    <!-- /.card -->
                                </div>
                                <!--/.col (right) -->
                            </div>
                            <!-- /.row -->
                        </div>
                        <!-- /.container-fluid -->
                    </section>
                    <!-- /.content -->
                </div>
                <!-- /.content-wrapper -->
        <?php } elseif ($_GET['id'] == 16) { ?>
                <!-- Content Wrapper. Contains page content -->
                <div class="content-wrapper">
                    <?php
                    $clients = $override->getNews('enrollment_form', 'status', 1, 'enrollment_id', $_GET['sid'])[0];
                    $screening = $override->getNews('screening', 'status', 1, 'id', $_GET['sid'])[0];
                    $sex = $override->get('sex', 'id', $clients['sex'])[0];
                    $site = $override->get('sites', 'id', $clients['facility_id'])[0];
                    ?>
                    <!-- Content Header (Page header) -->
                    <section class="content-header">
                        <div class="container-fluid">
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <?php if ($clients) { ?>
                                            <h1>Update enrolment form (PID :<?= $screening['pid'] ?>)</h1>
                                    <?php } else { ?>
                                            <h1>Add enrolment form (PID :<?= $screening['pid'] ?>)</h1>
                                    <?php } ?>
                                </div>
                                <div class="col-sm-6">
                                    <ol class="breadcrumb float-sm-right">
                                        <li class="breadcrumb-item"><a
                                                href="info.php?id=6&status=<?= $_GET['status']; ?>&sid=<?= $_GET['sid']; ?>&facility_id=<?= $_GET['facility_id']; ?>&page=<?= $_GET['page']; ?>">
                                                < Back</a>
                                        </li>&nbsp;&nbsp;
                                        <li class="breadcrumb-item"><a href="index1.php">Home</a></li>&nbsp;&nbsp;
                                        <li class="breadcrumb-item">
                                            <a
                                                href="info.php?id=6&status=<?= $_GET['status']; ?>&facility_id=<?= $_GET['facility_id']; ?>&page=<?= $_GET['page']; ?>">
                                                <?php if ($_GET['status'] == 1) { ?>
                                                        Go to screening list >
                                                <?php } elseif ($_GET['status'] == 2) { ?>
                                                        Go to eligible list >
                                                <?php } elseif ($_GET['status'] == 3) { ?>
                                                        Go to enrollment list >
                                                <?php } ?>
                                            </a>
                                        </li>&nbsp;&nbsp;
                                        <li class="breadcrumb-item active">Add New Client</li>
                                    </ol>
                                </div>
                            </div>
                        </div><!-- /.container-fluid -->
                    </section>

                    <!-- Main content -->
                    <section class="content">
                        <div class="container-fluid">
                            <div class="row">
                                <!-- right column -->
                                <div class="col-md-12">
                                    <!-- general form elements disabled -->
                                    <div class="card card-warning">
                                        <div class="card-header">
                                            <h3 class="card-title">Details of enrolment and patient demographics</h3>
                                        </div>
                                        <!-- /.card-header -->
                                        <form id="enrollment" enctype="multipart/form-data" method="post" autocomplete="off"
                                            style="display: flex; flex-wrap: wrap; gap: 10px;">
                                            <div class="card-body">
                                                <hr>
                                                <div class="row">
                                                    <!-- Enrollment Date -->
                                                    <div class="col-sm-3" style="flex: 1;">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>2. Date of enrolment</label>
                                                                <input class="form-control" type="date" name="enrollment_date"
                                                                    id="enrollment_date"
                                                                    value="<?= $clients['enrollment_date'] ?>" required />
                                                                <small id="enrollment_date_error" class="text-danger"></small>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Date of Birth -->
                                                    <div class="col-sm-3" style="flex: 1;">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>4. Date of birth:</label>
                                                                <input class="form-control" type="date" name="dob" id="dob"
                                                                    value="<?= $clients['dob'] ?>" style="width: 100%;"
                                                                    onchange="validateAge()" />
                                                                <small id="dob_error" class="text-danger"></small>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Age -->
                                                    <div class="col-sm-3" style="flex: 1;">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>5. Age (years)</label>
                                                                <input class="form-control" type="number" name="age" id="age"
                                                                    value="<?= $clients['age'] ?>" oninput="validateAge()" />
                                                                <small id="age_error" class="text-danger"></small>
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <!-- Sex -->
                                                    <div class="col-sm-3" style="flex: 1;">
                                                        <label>6. Sex</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('sex', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio" name="sex"
                                                                                id="sex<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($clients['sex'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>
                                                                                <?php if ($clients['sex'] == 3) { ?> readonly <?php } ?>
                                                                                required>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button" onclick="unsetRadio('sex')">Unset</button>

                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="card card-warning">
                                                    <div class="card-header">
                                                        <h3 class="card-title">Reason(s) for being regarded as presumptive TB
                                                            patient at initial assessment </h3>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="row">
                                                    <div class="col-sm-3" style="flex: 1;">
                                                        <label>9a. Cough of >2 weeks</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('yes_no', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="cough2weeks" id="cough2weeks<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($clients['cough2weeks'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?> required>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('cough2weeks')">Unset</button>

                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3" style="flex: 1;">
                                                        <label>9b. Poor weight gain or loss of weight</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('yes_no', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="poor_weight" id="poor_weight<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($clients['poor_weight'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?> required>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('poor_weight')">Unset</button>

                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3" style="flex: 1;">
                                                        <label>9c. Coughing up blood</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('yes_no', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="coughing_blood"
                                                                                id="coughing_blood<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($clients['coughing_blood'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?> required>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('coughing_blood')">Unset</button>

                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3" style="flex: 1;">
                                                        <label>9d. Unexplained fever</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('yes_no', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="unexplained_fever"
                                                                                id="unexplained_fever<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($clients['unexplained_fever'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?> required>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('unexplained_fever')">Unset</button>

                                                        </div>
                                                    </div>
                                                </div>

                                                <hr>

                                                <div class="row">
                                                    <div class="col-sm-3" style="flex: 1;">
                                                        <label>9e. Drenching night sweats</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('yes_no', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="night_sweats"
                                                                                id="night_sweats<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($clients['night_sweats'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?> required>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('night_sweats')">Unset</button>

                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3" style="flex: 1;">
                                                        <label>9f. Lymph nodes in neck enlarged</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('yes_no', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="neck_lymph" id="neck_lymph<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($clients['neck_lymph'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?> required>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('neck_lymph')">Unset</button>

                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3" style="flex: 1;">
                                                        <label>9g. Contact history with infectious TB patient</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('yes_no', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="history_tb" id="history_tb<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($clients['history_tb'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?> required>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('history_tb')">Unset</button>

                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3" style="flex: 1;">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>9h. Date information collected</label>
                                                                <input class="form-control" type="date"
                                                                    max="<?= date('Y-m-d'); ?>"
                                                                    name="date_information_collected"
                                                                    id="date_information_collected" value="<?php if ($clients['date_information_collected']) {
                                                                        print_r($clients['date_information_collected']);
                                                                    } ?>" required />
                                                                <span id="information_date_error" style="color: red;"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="card card-warning">
                                                    <div class="card-header">
                                                        <h3 class="card-title">History of TB and previous treatment</h3>
                                                    </div>
                                                </div>

                                                <hr>


                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <label>10a. Was the participant treated for TB before?</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('yes_no_unknown', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="tx_previous" id="tx_previous<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($clients['tx_previous'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?> required>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                        <button type="button" onclick="unsetRadio('tx_previous')">Unset</button>
                                                    </div>
                                                    <div class="col-sm-4" id="tb_category_section">
                                                        <label>10b. What category is the previously treated patient </label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('tb_category', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="tb_category" id="tb_category<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($clients['tb_category'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('tb_category')">Unset</button>
                                                        </div>
                                                        <input class="form-control" type="text" name="tb_category_specify"
                                                            id="tb_category_specify" placeholder="Specify Here..." value="<?php if ($clients['tb_category_specify']) {
                                                                print_r($clients['tb_category_specify']);
                                                            } ?>" />
                                                    </div>

                                                    <div class="col-sm-4" id="tx_number_section">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label for="tx_month">10c. When did the patient’s last treatment
                                                                    episode end?</label>

                                                                <!-- Row for Month and Year -->
                                                                <div class="row">
                                                                    <!-- Month Input -->
                                                                    <div class="col-sm-6">
                                                                        <label for="tx_month" class="form-label">Month</label>
                                                                        <input class="form-control" type="number"
                                                                            name="tx_month" id="tx_month"
                                                                            placeholder="Type Month..." min="1" max="12" value="<?php if ($clients['tx_month']) {
                                                                                print_r($clients['tx_month']);
                                                                            } ?>" />

                                                                        <!-- Unknown Month Checkbox -->
                                                                        <div class="form-check mt-2">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="tx_unknown_month" name="tx_unknown_month"
                                                                                value="1" <?php if ($clients['tx_unknown_month'] ?? false) {
                                                                                    echo 'checked';
                                                                                } ?>>
                                                                            <label class="form-check-label"
                                                                                for="tx_unknown_month">if month unknown Check
                                                                                Unknown, fill 99 for month on paper</label>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Year Input -->
                                                                    <div class="col-sm-6">
                                                                        <label for="tx_year" class="form-label">Year</label>
                                                                        <input class="form-control" type="number" name="tx_year"
                                                                            id="tx_year" placeholder="Type Year..." min="1970"
                                                                            max="2025" value="<?php if ($clients['tx_year']) {
                                                                                print_r($clients['tx_year']);
                                                                            } ?>" />

                                                                        <!-- Unknown Year Checkbox -->
                                                                        <div class="form-check mt-2">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="tx_unknown_year" name="tx_unknown_year"
                                                                                value="1" <?php if ($clients['tx_unknown_year'] ?? false) {
                                                                                    echo 'checked';
                                                                                } ?>>
                                                                            <label class="form-check-label"
                                                                                for="tx_unknown_year">Month and year
                                                                                unknown</label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div id="tx_previous_section">
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-sm-4" id="dr_ds_section">
                                                            <label>10d. Was it DR or DS TB </label>
                                                            <!-- radio -->
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('dr_ds', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="dr_ds" id="dr_ds<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?php if ($clients['dr_ds'] == $value['id']) {
                                                                                          echo 'checked';
                                                                                      } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                                <button type="button"
                                                                    onclick="unsetRadio('dr_ds')">Unset</button>

                                                            </div>
                                                        </div>

                                                        <div class="col-sm-4" id="ltf_months_section">
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <label>10e. If LTF or treatment failure for how long the
                                                                        participant received TB
                                                                        treatment? ( Months) </label>

                                                                    <!-- Row for Month and Year -->
                                                                    <div class="row">
                                                                        <!-- Month Input -->
                                                                        <div class="col-sm-12">
                                                                            <!-- <label for="tx_month" class="form-label"></label> -->
                                                                            <input class="form-control" type="number"
                                                                                name="ltf_months" id="ltf_months"
                                                                                placeholder="Type number of months…" min="1"
                                                                                max="10000" value="<?php if ($clients['ltf_months']) {
                                                                                    print_r($clients['ltf_months']);
                                                                                } ?>" />
                                                                        </div>
                                                                    </div>

                                                                    <!-- Unknown Checkbox -->
                                                                    <div class="form-check mt-3">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="ltf_months_unknown" name="ltf_months_unknown"
                                                                            value="1" <?php if ($clients['ltf_months_unknown'] ?? false) {
                                                                                echo 'checked';
                                                                            } ?>>
                                                                        <label class="form-check-label"
                                                                            for="ltf_months_unknown">Unknown</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-sm-4" id="tb_regimen_section">
                                                            <label>10f. Which treatment regimen was initiated </label>
                                                            <!-- radio -->
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('tb_regimen', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="tb_regimen"
                                                                                    id="tb_regimen<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?php if ($clients['tb_regimen'] == $value['id']) {
                                                                                          echo 'checked';
                                                                                      } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                                <button type="button"
                                                                    onclick="unsetRadio('tb_regimen')">Unset</button>
                                                                <hr>
                                                                <input class="form-control" type="text"
                                                                    name="tb_regimen_specify" id="tb_regimen_specify"
                                                                    placeholder="Specify Here..." value="<?php if ($clients['tb_regimen_specify']) {
                                                                        print_r($clients['tb_regimen_specify']);
                                                                    } ?>" />
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <hr>

                                                    <div class="row">
                                                        <div class="col-sm-6" id="regimen_section">
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <label>10g. How long was the treatment regimen( Months)
                                                                    </label>

                                                                    <!-- Row for Month and Year -->
                                                                    <div class="row">
                                                                        <!-- Month Input -->
                                                                        <div class="col-sm-12">
                                                                            <!-- <label for="regimen_months" class="form-label"></label> -->
                                                                            <input class="form-control" type="number"
                                                                                name="regimen_months" id="regimen_months"
                                                                                placeholder="Type number of months…" min="1"
                                                                                max="10000" value="<?php if ($clients['regimen_months']) {
                                                                                    print_r($clients['regimen_months']);
                                                                                } ?>" />
                                                                        </div>
                                                                    </div>

                                                                    <!-- Unknown Checkbox -->
                                                                    <div class="form-check mt-3">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="regimen_months_unknown"
                                                                            name="regimen_months_unknown" value="1" <?php if ($clients['regimen_months_unknown'] ?? false) {
                                                                                echo 'checked';
                                                                            } ?>>
                                                                        <label class="form-check-label"
                                                                            for="regimen_months_unknown">Unknown</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-sm-6" id="tb_otcome_section">
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <label>10h. What was the treatment outcome?</label>
                                                                    <select id="tb_otcome" name="tb_otcome"
                                                                        class="form-control">
                                                                        <?php $tb_otcome = $override->get('tb_otcome', 'id', $clients['tb_otcome'])[0]; ?>
                                                                        <option value="<?= $tb_otcome['id'] ?>"><?php if ($clients['tb_otcome']) {
                                                                              print_r($tb_otcome['name']);
                                                                          } else {
                                                                              echo 'Select';
                                                                          } ?>
                                                                        </option>
                                                                        <?php foreach ($override->get('tb_otcome', 'status', 1) as $value) { ?>
                                                                                <option value="<?= $value['id'] ?>">
                                                                                    <?= $value['name'] ?>
                                                                                </option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                </div>

                                                <div class="card card-warning">
                                                    <div class="card-header">
                                                        <h3 class="card-title">Health-related conditions</h3>
                                                    </div>
                                                </div>

                                                <hr>

                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <label>11. HIV status </label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('hiv_status', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="hiv_status" id="hiv_status<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($clients['hiv_status'] == $value['id']) {
                                                                                      echo 'checked' . ' ' . 'required';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>

                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('hiv_status')">Unset</button>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3" id="other_diseases_section">
                                                        <label>12. Any other relevant diseases/conditions ?</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('yes_no_unknown', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="other_diseases"
                                                                                id="other_diseases<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($clients['other_diseases'] == $value['id']) {
                                                                                      echo 'checked' . ' ' . 'required';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('other_diseases')">Unset</button>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-6" id="diseases_medical_section">
                                                        <label>12(a). If yes, Select relevant diseases/medical conditions
                                                            ( Tick all that apply)
                                                        </label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('diseases_conditions', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                name="diseases_medical[]"
                                                                                id="diseases_medical<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php foreach (explode(',', $clients['diseases_medical']) as $values) {
                                                                                      if ($values == $value['id']) {
                                                                                          echo 'checked';
                                                                                      }
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                                <input class="form-control" type="text" name="diseases_specify"
                                                                    id="diseases_specify" placeholder="If Other specify here..."
                                                                    value="<?php if ($clients['diseases_specify']) {
                                                                        print_r($clients['diseases_specify']);
                                                                    } ?>" />
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>

                                                <hr>

                                                <div class="card card-warning">
                                                    <div class="card-header">
                                                        <h3 class="card-title">Samples collected</h3>
                                                    </div>
                                                </div>

                                                <hr>

                                                <div class="row">
                                                    <div class="col-sm-6" id="sputum_collected_section">
                                                        <label>13(a).After TB was confirmed by a rapid molecular test, was an
                                                            additional sputum sample collected?</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('yes_no', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="sputum_collected"
                                                                                id="sputum_collected<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($clients['sputum_collected'] == $value['id']) {
                                                                                      echo 'checked' . ' ' . 'required';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>

                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('sputum_collected')">Unset</button>
                                                        </div>
                                                        <br>
                                                    </div>

                                                    <div class="col-sm-6" id="sputum_date_section">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>13(b). Date of sputum collection</label>
                                                                <!-- Row for Date and Sample Type Inputs -->
                                                                <div class="row">
                                                                    <!-- DST Sample Date Input -->
                                                                    <div class="col-sm-6">
                                                                        <!-- <label for="dst_sample_date" class="form-label">DST
                                                                        Sample Date</label> -->
                                                                        <input class="form-control" type="date" min="2025-01-17"
                                                                            max="<?= date('Y-m-d'); ?>" name="sputum_date"
                                                                            id="sputum_date" value="<?php if ($clients['sputum_date']) {
                                                                                print_r($clients['sputum_date']);
                                                                            } ?>" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-6" id="sputum_reasons_section">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>13(c) What was the reasons</label>
                                                                <!-- Row for Date and Sample Type Inputs -->
                                                                <div class="row">
                                                                    <!-- DST Sample Date Input -->
                                                                    <div class="col-sm-6">
                                                                        <textarea class="form-control" name="sputum_reasons"
                                                                            rows="6"
                                                                            placeholder="Type reasons here...">                                                                                                                                                                                                                                                                                              <?php if ($clients['sputum_reasons']) {
                                                                                print_r($clients['sputum_reasons']);
                                                                            } ?>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <hr>
                                                <div class="card card-warning">
                                                    <div class="card-header">
                                                        <h3 class="card-title">FORM STATUS</h3>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <label>Complete?</label>
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('form_completness', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="form_status" id="form_status<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>"
                                                                                <?= ($clients['form_status'] == $value['id']) ? 'checked' : ''; ?> required onchange="updateFormStatus()">
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('form_status')">Unset</button>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-4">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Completed Date</label>
                                                                <input class="form-control" type="date" name="date_completed"
                                                                    id="date_completed"
                                                                    value="<?= ($clients['date_completed']) ? $clients['date_completed'] : ''; ?>" />
                                                                <span id="date_completed_error" class="text-danger"></span>
                                                            </div>
                                                        </div>
                                                        <?php if ($clients['form_status'] >= 2) { ?>

                                                                <div class="row-form clearfix">
                                                                    <div class="form-group">
                                                                        <label>Completed By</label>
                                                                        <input class="form-control" type="text"
                                                                            value="<?= $override->get('user', 'id', $clients['completed_by'])[0]['username']; ?>"
                                                                            readonly />
                                                                    </div>
                                                                </div>
                                                        <?php } ?>
                                                    </div>

                                                    <div class="col-sm-4">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>Verified Date</label>
                                                                <input class="form-control" type="date" name="date_verified"
                                                                    id="date_verified"
                                                                    value="<?= ($clients['date_verified']) ? $clients['date_verified'] : ''; ?>" />
                                                                <span id="date_verified_error" class="text-danger"></span>
                                                            </div>
                                                        </div>
                                                        <?php if ($clients['form_status'] >= 3) { ?>
                                                                <div class="row-form clearfix">
                                                                    <div class="form-group">
                                                                        <label>Verified By</label>
                                                                        <input class="form-control" type="text"
                                                                            value="<?= $override->get('user', 'id', $clients['verified_by'])[0]['username']; ?>"
                                                                            readonly />
                                                                    </div>
                                                                </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                                <hr>
                                            </div>
                                            <!-- /.card-body -->

                                            <div class="card-footer">
                                                <a href="info.php?id=6&status=<?= $_GET['status']; ?>&sid=<?= $_GET['sid']; ?>&facility_id=<?= $_GET['facility_id']; ?>&page=<?= $_GET['page']; ?>"
                                                    class="btn btn-default">Back</a>
                                                <input type="submit" name="add_enrollment_form" value="Submit"
                                                    class="btn btn-primary">
                                            </div>
                                        </form>
                                    </div> <!-- /.card -->
                                </div> <!--/.col (right) -->
                            </div> <!-- /.row -->
                        </div><!-- /.container-fluid -->
                    </section>
                    <!-- /.content -->
                </div>
                <!-- /.content-wrapper -->
        <?php } elseif ($_GET['id'] == 17) { ?>
                <?php
                $costing = $override->getNews('validations', 'status', 1, 'id', $_GET['cid'])[0];
                $facility = $override->get('sites', 'id', $costing['site_id'])[0];

                ?>
                <!-- Content Wrapper. Contains page content -->
                <div class="content-wrapper">
                    <!-- Content Header (Page header) -->
                    <section class="content-header">
                        <div class="container-fluid">
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <?php if ($costing) { ?>
                                            <h1>Update New validations Data (PID :<?= $costing['pid'] ?>)</h1>
                                    <?php } else { ?>
                                            <h1>Add validations Data (PID :<?= $costing['pid'] ?>)</h1>
                                    <?php } ?>
                                </div>
                                <div class="col-sm-6">
                                    <ol class="breadcrumb float-sm-right">
                                        <li class="breadcrumb-item"><a
                                                href="info.php?id=4&cid=<?= $_GET['cid']; ?>&status=<?= $_GET['status']; ?>">
                                                < Back</a>
                                        </li>&nbsp;&nbsp;
                                        <li class="breadcrumb-item"><a href="index1.php">Home</a></li>&nbsp;&nbsp;
                                        <li class="breadcrumb-item"><a href="info.php?id=3&status=<?= $_GET['status']; ?>">
                                                Go to screening list > </a>
                                        </li>&nbsp;&nbsp;
                                        <?php if (!$costing) { ?>
                                                <li class="breadcrumb-item active">Add New validations Data</li>
                                        <?php } else { ?>
                                                <li class="breadcrumb-item active">Update validations Data</li>
                                        <?php } ?>
                                    </ol>
                                </div>
                            </div>
                        </div><!-- /.container-fluid -->
                    </section>

                    <!-- Main content -->
                    <section class="content">
                        <div class="container-fluid">
                            <div class="row">
                                <!-- right column -->
                                <div class="col-md-12">
                                    <!-- general form elements disabled -->
                                    <div class="card card-warning">
                                        <div class="card-header">
                                            <h3 class="card-title">validations Form</h3>
                                        </div>
                                        <!-- /.card-header -->
                                        <form id="validation" enctype="multipart/form-data" method="post" autocomplete="off">
                                            <div class="card-body">
                                                <hr>
                                                <div class="row">
                                                    <div class="col-3">
                                                        <div class="mb-2">
                                                            <label for="date_collect" class="form-label">1. Collection
                                                                Date</label>
                                                            <input type="date" value="<?php if ($costing['date_collect']) {
                                                                print_r($costing['date_collect']);
                                                            } ?>" id="date_collect" name="date_collect"
                                                                max="<?= date('Y-m-d') ?>" class="form-control"
                                                                placeholder="Enter date" required />
                                                        </div>
                                                    </div>

                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="date_receictrl" class="form-label">2. Date CTRL
                                                                Received</label>
                                                            <input type="date" value="<?php if ($costing['date_receictrl']) {
                                                                print_r($costing['date_receictrl']);
                                                            } ?>" id="date_receictrl" name="date_receictrl"
                                                                max="<?= date('Y-m-d') ?>" class="form-control"
                                                                placeholder="Enter date" required />
                                                        </div>
                                                    </div>

                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="lab_no" class="form-label">3. lab_no</label>
                                                            <input type="text" value="<?php if ($costing['lab_no']) {
                                                                print_r($costing['lab_no']);
                                                            } ?>" id="lab_no" name="lab_no" class="form-control"
                                                                placeholder="Enter here" required />
                                                        </div>
                                                    </div>

                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="transit_time" class="form-label">4. transit_time (If N /
                                                                A
                                                                Put '99')</label>
                                                            <input type="number" value="<?php if ($costing['transit_time']) {
                                                                print_r($costing['transit_time']);
                                                            } ?>" id="transit_time" name="transit_time" min="0" max="100"
                                                                class="form-control" placeholder="Enter here" required />
                                                        </div>
                                                    </div>

                                                </div>

                                                <hr>

                                                <div class="row">
                                                    <div class="col-3">
                                                        <div class="mb-2">
                                                            <label for="date_collect" class="form-label">5. resid_distr</label>
                                                            <input type="text" value="<?php if ($costing['resid_distr']) {
                                                                print_r($costing['resid_distr']);
                                                            } ?>" id="resid_distr" name="resid_distr" class="form-control"
                                                                placeholder="Enter date" required />
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3">
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <label>6. h_facil</label>
                                                                <select id="h_facil" name="h_facil" class="form-control"
                                                                    required>
                                                                    <option value="<?= $facility['id'] ?>"><?php if ($costing['h_facil']) {
                                                                          print_r($facility['name']);
                                                                      } else {
                                                                          echo 'Select region';
                                                                      } ?>
                                                                    </option>
                                                                    <?php foreach ($override->get('sites', 'status', 1) as $region) { ?>
                                                                            <option value="<?= $region['id'] ?>"><?= $region['name'] ?>
                                                                            </option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="hf_district" class="form-label">7. hf_district</label>
                                                            <input type="text" value="<?php if ($costing['hf_district']) {
                                                                print_r($costing['hf_district']);
                                                            } ?>" id="hf_district" name="hf_district" class="form-control"
                                                                placeholder="Enter here" required />
                                                        </div>
                                                    </div>

                                                    <div class="col-3">
                                                        <div class="mb-3">
                                                            <label for="tb_region" class="form-label">8. tb_region</label>
                                                            <input type="text" value="<?php if ($costing['tb_region']) {
                                                                print_r($costing['tb_region']);
                                                            } ?>" id="tb_region" name="tb_region" class="form-control"
                                                                placeholder="Enter here" required />
                                                        </div>
                                                    </div>

                                                </div>

                                                <hr>

                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <label for="samplae_type" class="form-label">9. Sample type</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('sample_type2', 'status2', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="samplae_type"
                                                                                id="samplae_type<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['samplae_type'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?> required>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>

                                                            <button type="button"
                                                                onclick="unsetRadio('samplae_type')">Unset</button>

                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3" id="pat_category">
                                                        <label for="pat_category" class="form-label">10. Patient
                                                            Category</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('patient_category', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="pat_category"
                                                                                id="pat_category<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['pat_category'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                        <button type="button"
                                                            onclick="unsetRadio('pat_category')">Unset</button>
                                                    </div>

                                                    <div class="col-sm-3" id="testrequest_reason">
                                                        <label for="testrequest_reason" class="form-label">11. Testrequest
                                                            Reason</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('testrequest_reason', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="testrequest_reason"
                                                                                id="testrequest_reason<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['testrequest_reason'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                        <button type="button"
                                                            onclick="unsetRadio('testrequest_reason')">Unset</button>
                                                    </div>
                                                    <div class="col-sm-3" id="follow_up_months1">
                                                        <div class="mb-3">
                                                            <label for="follow_up_months" class="form-label">12. Follow up at
                                                                months
                                                                ?</label>
                                                            <input type="number" value="<?php if ($costing['follow_up_months']) {
                                                                print_r($costing['follow_up_months']);
                                                            } ?>" id="follow_up_months" name="follow_up_months" min="1"
                                                                max="20" class="form-control" placeholder="Enter here" />
                                                        </div>
                                                    </div>
                                                </div>

                                                <div id="tb_diagnosis_hides2222">
                                                    <hr>
                                                    <div class="row">

                                                        <div class="col-sm-3">
                                                            <div class="mb-3">
                                                                <label for="treatment_month" class="form-label">13. Treatment
                                                                    Month
                                                                    (If N/A put '99' ,If Not provided put '98')</label>
                                                                <input type="number" value="<?php if ($costing['treatment_month']) {
                                                                    print_r($costing['treatment_month']);
                                                                } ?>" id="treatment_month" name="treatment_month" min="1"
                                                                    max="20" class="form-control" placeholder="Enter here" />
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3" id="hiv_status">
                                                            <label for="hiv_status" class="form-label">14. Hiv Status</label>
                                                            <!-- radio -->
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('hiv_status2', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="hiv_status"
                                                                                    id="hiv_status<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?php if ($costing['hiv_status'] == $value['id']) {
                                                                                          echo 'checked';
                                                                                      } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('hiv_status')">Unset</button>

                                                        </div>

                                                        <div class="col-sm-2" id="gx_results">
                                                            <label for="gx_results" class="form-label">15. GX results</label>
                                                            <!-- radio -->
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('gx_results', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="gx_results"
                                                                                    id="gx_results<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?php if ($costing['gx_results'] == $value['id']) {
                                                                                          echo 'checked';
                                                                                      } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('gx_results')">Unset</button>

                                                        </div>

                                                        <div class="col-2">
                                                            <div class="mb-2">
                                                                <label for="gxmtb_ct" class="form-label">16. gxmtb_ct</label>
                                                                <input type="text" value="<?php if ($costing['gxmtb_ct']) {
                                                                    print_r($costing['gxmtb_ct']);
                                                                } ?>" id="gxmtb_ct" name="gxmtb_ct" min="0" max="40"
                                                                    class="form-control" placeholder="Enter here" />
                                                            </div>
                                                        </div>

                                                        <div class="col-sm-2" id="gx_mtbamount">
                                                            <label for="gx_mtbamount" class="form-label">17. GX MTB
                                                                Amount</label>
                                                            <!-- radio -->
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('gxmtbamount', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="gx_mtbamount"
                                                                                    id="gx_mtbamount<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?php if ($costing['gx_mtbamount'] == $value['id']) {
                                                                                          echo 'checked';
                                                                                      } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('gx_mtbamount')">Unset</button>

                                                        </div>
                                                    </div>

                                                    <hr>
                                                    <div class="row">

                                                        <div class="col-sm-3" id="fm_done">
                                                            <label for="fm_done" class="form-label">18. Fm done ?</label>
                                                            <!-- radio -->
                                                            <div class="row-form clearfix">
                                                                <div class="form-group">
                                                                    <?php foreach ($override->get('yes_no', 'status', 1) as $value) { ?>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio"
                                                                                    name="fm_done" id="fm_done<?= $value['id']; ?>"
                                                                                    value="<?= $value['id']; ?>" <?php if ($costing['fm_done'] == $value['id']) {
                                                                                          echo 'checked';
                                                                                      } ?>>
                                                                                <label
                                                                                    class="form-check-label"><?= $value['name']; ?></label>
                                                                            </div>
                                                                    <?php } ?>
                                                                </div>
                                                                <button type="button"
                                                                    onclick="unsetRadio('fm_done')">Unset</button>

                                                            </div>
                                                        </div>

                                                        <div class="col-3">
                                                            <div class="mb-2">
                                                                <label for="fm_date" class="form-label">19. Fm date</label>
                                                                <input type="date" value="<?php if ($costing['fm_date']) {
                                                                    print_r($costing['fm_date']);
                                                                } ?>" id="fm_date" name="fm_date" class="form-control"
                                                                    placeholder="Enter here" />
                                                            </div>
                                                        </div>

                                                        <div class="col-3">
                                                            <div class="mb-2">
                                                                <label for="fm_results" class="form-label">20. Fm
                                                                    results</label>
                                                                <input type="text" value="<?php if ($costing['fm_results']) {
                                                                    print_r($costing['fm_results']);
                                                                } ?>" id="fm_results" name="fm_results"
                                                                    class="form-control" placeholder="Enter here" />
                                                            </div>
                                                        </div>

                                                        <div class="col-3">
                                                            <div class="mb-2">
                                                                <label for="dec_date" class="form-label">21. dec_date</label>
                                                                <input type="date" value="<?php if ($costing['dec_date']) {
                                                                    print_r($costing['dec_date']);
                                                                } ?>" id="dec_date" name="dec_date" class="form-control"
                                                                    placeholder="Enter here" />
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>

                                                <hr>
                                                <div class="row">

                                                    <div class="col-sm-3" id="cult_done">
                                                        <label for="cult_done" class="form-label">22. cult_done</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('yes_no_np', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="cult_done" id="cult_done<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['cult_done'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('cult_done')">Unset</button>

                                                        </div>
                                                    </div>

                                                    <div class="col-3">
                                                        <div class="mb-2">
                                                            <label for="inno_date" class="form-label">23. inno_date</label>
                                                            <input type="date" value="<?php if ($costing['inno_date']) {
                                                                print_r($costing['inno_date']);
                                                            } ?>" id="inno_date" name="inno_date" class="form-control"
                                                                placeholder="Enter here" />
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-2" id="ljcul_results">
                                                        <label for="ljcul_results" class="form-label">24. ljcul_results</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('ljcul_results', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="ljcul_results"
                                                                                id="ljcul_results<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['ljcul_results'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                        <button type="button"
                                                            onclick="unsetRadio('ljcul_results')">Unset</button>

                                                    </div>

                                                    <div class="col-3">
                                                        <div class="mb-2">
                                                            <label for="ljculres_date" class="form-label">25.
                                                                ljculres_date</label>
                                                            <input type="date" value="<?php if ($costing['ljculres_date']) {
                                                                print_r($costing['ljculres_date']);
                                                            } ?>" id="ljculres_date" name="ljculres_date"
                                                                class="form-control" placeholder="Enter here" />
                                                        </div>
                                                    </div>
                                                </div>

                                                <hr>
                                                <div class="row">

                                                    <div class="col-sm-4" id="mgitcul_done">
                                                        <label for="mgitcul_done" class="form-label">26. mgitcul_done</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('yes_no_np', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="mgitcul_done"
                                                                                id="mgitcul_done<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['mgitcul_done'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('mgitcul_done')">Unset</button>

                                                        </div>
                                                    </div>

                                                    <div class="col-4">
                                                        <div class="mb-2">
                                                            <label for="mgitcul_date" class="form-label">27.
                                                                mgitcul_date</label>
                                                            <input type="date" value="<?php if ($costing['mgitcul_date']) {
                                                                print_r($costing['mgitcul_date']);
                                                            } ?>" id="mgitcul_date" name="mgitcul_date"
                                                                class="form-control" placeholder="Enter here" />
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-4" id="mgitcul_resul">
                                                        <label for="mgitcul_resul" class="form-label">28. mgitcul_resul</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('mgitcul_resul', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="mgitcul_resul"
                                                                                id="mgitcul_resul<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['mgitcul_resul'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <!-- <button onclick="unsetRadio('mgitcul_resul')">Unset</button> -->
                                                            <button type="button" type="button"
                                                                onclick="unsetRadio('mgitcul_resul')">Unset</button>


                                                        </div>
                                                    </div>
                                                </div>

                                                <hr>
                                                <div class="row">
                                                    <div class="col-sm-3" id="ljdst_rif">
                                                        <label for="ljdst_rif" class="form-label">29. ljdst_rif</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('dst', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="ljdst_rif" id="ljdst_rif<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['ljdst_rif'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('ljdst_rif')">Unset</button>

                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3" id="ljdst_iso">
                                                        <label for="ljdst_iso" class="form-label">30. ljdst_iso</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('dst', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="ljdst_iso" id="ljdst_iso<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['ljdst_iso'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('ljdst_iso')">Unset</button>

                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3" id="ljdst_ethamb">
                                                        <label for="ljdst_ethamb" class="form-label">31. ljdst_ethamb</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('dst', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="ljdst_ethamb"
                                                                                id="ljdst_ethamb<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['ljdst_ethamb'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('ljdst_ethamb')">Unset</button>

                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3" id="mgitdst_stm">
                                                        <label for="mgitdst_stm" class="form-label">32. mgitdst_stm</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('dst', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="mgitdst_stm" id="mgitdst_stm<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['mgitdst_stm'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('mgitdst_stm')">Unset</button>

                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>

                                                <div class="row">
                                                    <div class="col-sm-3" id="mgitdst_rif">
                                                        <label for="mgitdst_rif" class="form-label">33. mgitdst_rif</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('dst', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="mgitdst_rif" id="mgitdst_rif<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['mgitdst_rif'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('mgitdst_rif')">Unset</button>

                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3" id="mgitdst_iso">
                                                        <label for="mgitdst_iso" class="form-label">34. ljdst_iso</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('dst', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="mgitdst_iso" id="ljdst_iso<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['mgitdst_iso'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('mgitdst_iso')">Unset</button>

                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3" id="mgitdst_ethamb">
                                                        <label for="mgitdst_ethamb" class="form-label">35. mgitcul_resul</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('dst', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="mgitdst_ethamb"
                                                                                id="mgitdst_ethamb<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['mgitdst_ethamb'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('mgitdst_ethamb')">Unset</button>

                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3" id="mgitdst_bed">
                                                        <label for="mgitdst_bed" class="form-label">36. mgitdst_stm</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('dst', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="mgitdst_bed" id="mgitdst_bed<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['mgitdst_bed'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('mgitdst_bed')">Unset</button>

                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>

                                                <div class="row">
                                                    <div class="col-sm-3" id="mgitdst_2cfz">
                                                        <label for="mgitdst_2cfz" class="form-label">37. mgitdst_2cfz</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('dst', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="mgitdst_2cfz"
                                                                                id="mgitdst_2cfz<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['mgitdst_2cfz'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('mgitdst_2cfz')">Unset</button>

                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3" id="mgitdst_2dlm">
                                                        <label for="mgitdst_2dlm" class="form-label">38. mgitdst_2dlm</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('dst', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="mgitdst_2dlm"
                                                                                id="mgitdst_2dlm<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['mgitdst_2dlm'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('mgitdst_2dlm')">Unset</button>

                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3" id="mgitdst_2levo">
                                                        <label for="mgitdst_2levo" class="form-label">39. mgitdst_2levo</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('dst', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="mgitdst_2levo"
                                                                                id="mgitdst_2levo<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['mgitdst_2levo'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('mgitdst_2levo')">Unset</button>

                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3" id="mgitdst_2lzd">
                                                        <label for="mgitdst_2lzd" class="form-label">40. mgitdst_2lzd</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('dst', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="mgitdst_2lzd"
                                                                                id="mgitdst_2lzd<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['mgitdst_2lzd'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('mgitdst_2lzd')">Unset</button>

                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>

                                                <div class="row">
                                                    <div class="col-sm-3" id="lpa1_done">
                                                        <label for="lpa1_done" class="form-label">41. lpa1_done</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('yes_no_na_np', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="lpa1_done" id="lpa1_done<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['lpa1_done'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('lpa1_done')">Unset</button>

                                                        </div>
                                                    </div>
                                                    <div class="col-3">
                                                        <div class="mb-2">
                                                            <label for="lpa1_date1" id="lpa1_date1" class="form-label">42.
                                                                lpa1_date</label>
                                                            <input type="date" value="<?php if ($costing['lpa1_date1']) {
                                                                print_r($costing['lpa1_date1']);
                                                            } ?>" id="lpa1_date1" name="lpa1_date1" class="form-control"
                                                                placeholder="Enter here" />
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-2" id="lpa1_mtbdetected">
                                                        <label for="lpa1_mtbdetected" class="form-label">43.
                                                            lpa1_mtbdetected</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('mtb_detected_not', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="lpa1_mtbdetected"
                                                                                id="lpa1_mtbdetected<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['lpa1_mtbdetected'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('lpa1_mtbdetected')">Unset</button>

                                                        </div>
                                                    </div>
                                                    <div class="col-sm-2" id="lpaa1dst_rif">
                                                        <label for="lpaa1dst_rif" class="form-label">44. lpaa1dst_rif</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('dst', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="lpaa1dst_rif"
                                                                                id="lpaa1dst_rif<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['lpaa1dst_rif'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('lpaa1dst_rif')">Unset</button>

                                                        </div>
                                                    </div>
                                                    <div class="col-sm-2" id="lpa1dst_inh">
                                                        <label for="lpa1dst_inh" class="form-label">45. mgitdst_2levo</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('dst', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="lpa1dst_inh" id="lpa1dst_inh<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['lpa1dst_inh'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('lpa1dst_inh')">Unset</button>

                                                        </div>
                                                    </div>

                                                </div>
                                                <hr>

                                                <div class="row">
                                                    <div class="col-sm-4" id="lpa2_done">
                                                        <label for="lpa2_done" class="form-label">46. lpa2_done</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('yes_no_na_np', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="lpa2_done" id="lpa2_done<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['lpa2_done'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('lpa2_done')">Unset</button>

                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="mb-2">
                                                            <label for="lpa2_date" id="lpa2_date1" class="form-label">47.
                                                                lpa2_date</label>
                                                            <input type="date" value="<?php if ($costing['lpa2_date']) {
                                                                print_r($costing['lpa2_date']);
                                                            } ?>" id="lpa2_date" name="lpa2_date" class="form-control"
                                                                placeholder="Enter here" />
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4" id="lpa2_mtbdetected">
                                                        <label for="lpa2_mtbdetected" class="form-label">48.
                                                            lpa2_mtbdetected</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('mtb_detected_not_na', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="lpa2_mtbdetected"
                                                                                id="lpa2_mtbdetected<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['lpa2_mtbdetected'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('lpa2_mtbdetected')">Unset</button>

                                                        </div>
                                                    </div>


                                                </div>
                                                <hr>

                                                <div class="row">
                                                    <div class="col-sm-4" id="lpa2dst_lfx">
                                                        <label for="lpa2dst_lfx" class="form-label">49. lpa2dst_lfx</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('sensitive_resistance_na', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="lpa2dst_lfx" id="lpa2dst_lfx<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['lpa2dst_lfx'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('lpa2dst_lfx')">Unset</button>

                                                        </div>
                                                    </div>


                                                    <div class="col-sm-4" id="lpa2dst_ag_cp">
                                                        <label for="lpa2dst_ag_cp" class="form-label">50. lpa2dst_ag_cp</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('sensitive_resistance_na', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="lpa2dst_ag_cp"
                                                                                id="lpa2dst_ag_cp<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['lpa2dst_ag_cp'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('lpa2dst_ag_cp')">Unset</button>

                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4" id="lpa2dstag_lowkan">
                                                        <label for="lpa2dstag_lowkan" class="form-label">51.
                                                            lpa2dstag_lowkan</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('sensitive_resistance_na', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="lpa2dstag_lowkan"
                                                                                id="lpa2dstag_lowkan<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['lpa2dstag_lowkan'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('lpa2dstag_lowkan')">Unset</button>

                                                        </div>
                                                    </div>


                                                </div>
                                                <hr>

                                                <div class="row">
                                                    <div class="col-sm-3" id="nanop_done">
                                                        <label for="nanop_done" class="form-label">52. nanop_done</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('yes_no', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="nanop_done" id="nanop_done<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['nanop_done'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('nanop_done')">Unset</button>

                                                        </div>
                                                    </div>


                                                    <div class="col-sm-3" id="pos_control">
                                                        <label for="pos_control" class="form-label">53. pos_control</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('pass_fails', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="pos_control" id="pos_control<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['pos_control'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('pos_control')">Unset</button>

                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3" id="neg_control">
                                                        <label for="neg_control" class="form-label">54. neg_control</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('pass_fails', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="neg_control" id="neg_control<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['neg_control'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('neg_control')">Unset</button>

                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3" id="sample_control">
                                                        <label for="sample_control" class="form-label">55.
                                                            sample_control</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('pass_fails', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="sample_control"
                                                                                id="sample_control<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['sample_control'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('sample_control')">Unset</button>

                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>

                                                <div class="row">
                                                    <div class="col-sm-4" id="internalcontrol">
                                                        <label for="internalcontrol" class="form-label">56.
                                                            internalcontrol</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('pass_fails', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="internalcontrol"
                                                                                id="internalcontrol<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['internalcontrol'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('internalcontrol')">Unset</button>

                                                        </div>
                                                    </div>


                                                    <div class="col-sm-4" id="hsp65">
                                                        <label for="hsp65" class="form-label">57. hsp65</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('pass_fails', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio" name="hsp65"
                                                                                id="hsp65<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['hsp65'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button" onclick="unsetRadio('hsp65')">Unset</button>

                                                        </div>
                                                    </div>

                                                    <div class="col-4">
                                                        <div class="mb-2">
                                                            <label for="nanopseq_date" class="form-label">58.
                                                                nanopseq_date</label>
                                                            <input type="date" value="<?php if ($costing['nanopseq_date']) {
                                                                print_r($costing['nanopseq_date']);
                                                            } ?>" id="nanopseq_date" name="nanopseq_date"
                                                                max="<?= date('Y-m-d') ?>" class="form-control"
                                                                placeholder="Enter date" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>

                                                <div class="row">
                                                    <div class="col-sm-3" id="myco_results">
                                                        <label for="myco_results" class="form-label">59. myco_results</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('mtb_detected_not_faled', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="myco_results"
                                                                                id="myco_results<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['myco_results'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('myco_results')">Unset</button>

                                                        </div>
                                                    </div>


                                                    <div class="col-sm-3" id="myco_type">
                                                        <label for="myco_type" class="form-label">60. myco_type</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('myco_type', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="myco_type" id="myco_type<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['myco_type'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('myco_type')">Unset</button>

                                                        </div>
                                                    </div>

                                                    <div class="col-3">
                                                        <div class="mb-2">
                                                            <label for="ntm_spp" class="form-label">61. NTM spp</label>
                                                            <input type="text" value="<?php if ($costing['ntm_spp']) {
                                                                print_r($costing['ntm_spp']);
                                                            } ?>" id="ntm_spp" name="ntm_spp" class="form-control"
                                                                placeholder="Enter HERE" />
                                                        </div>
                                                    </div>

                                                    <div class="col-3">
                                                        <div class="mb-2">
                                                            <label for="myco_lineage" class="form-label">62.
                                                                myco_lineage</label>
                                                            <input type="text" value="<?php if ($costing['myco_lineage']) {
                                                                print_r($costing['myco_lineage']);
                                                            } ?>" id="myco_lineage" name="myco_lineage"
                                                                class="form-control" placeholder="Enter date" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>

                                                <div class="row">
                                                    <div class="col-sm-3" id="nano_rif">
                                                        <label for="nano_rif" class="form-label">63. nano_rif</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('nano_type', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio" name="nano_rif"
                                                                                id="nano_rif<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['nano_rif'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('nano_rif')">Unset</button>

                                                        </div>
                                                    </div>


                                                    <div class="col-sm-3" id="nano_inh">
                                                        <label for="nano_inh" class="form-label">64. nano_inh</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('nano_type', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio" name="nano_inh"
                                                                                id="nano_inh<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['nano_inh'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('nano_inh')">Unset</button>

                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3" id="nano_kan">
                                                        <label for="nano_kan" class="form-label">65. nano_kan</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('nano_type', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio" name="nano_kan"
                                                                                id="nano_kan<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['nano_kan'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('nano_kan')">Unset</button>

                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3" id="nano_mxf">
                                                        <label for="nano_mxf" class="form-label">66. nano_mxf</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('nano_type', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio" name="nano_mxf"
                                                                                id="nano_mxf<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['nano_mxf'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('nano_mxf')">Unset</button>

                                                        </div>
                                                    </div>

                                                </div>

                                                <hr>

                                                <div class="row">
                                                    <div class="col-sm-3" id="nano_cap">
                                                        <label for="nano_cap" class="form-label">67. nano_cap</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('nano_type', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio" name="nano_cap"
                                                                                id="nano_cap<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['nano_cap'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('nano_cap')">Unset</button>

                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3" id="nano_emb">
                                                        <label for="nano_emb" class="form-label">68. nano_emb</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('nano_type', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio" name="nano_emb"
                                                                                id="nano_emb<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['nano_emb'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('nano_emb')">Unset</button>

                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3" id="nano_pza">
                                                        <label for="nano_pza" class="form-label">69. nano_pza</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('nano_type', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio" name="nano_pza"
                                                                                id="nano_pza<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['nano_pza'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('nano_pza')">Unset</button>

                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3" id="nano_amk">
                                                        <label for="nano_amk" class="form-label">70. nano_amk</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('nano_type', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio" name="nano_amk"
                                                                                id="nano_amk<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['nano_amk'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('nano_amk')">Unset</button>

                                                        </div>
                                                    </div>

                                                </div>

                                                <hr>

                                                <div class="row">
                                                    <div class="col-sm-3" id="nano_bdq">
                                                        <label for="nano_bdq" class="form-label">71. nano_bdq</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('nano_type', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio" name="nano_bdq"
                                                                                id="nano_bdq<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['nano_bdq'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('nano_bdq')">Unset</button>

                                                        </div>
                                                    </div>


                                                    <div class="col-sm-3" id="nano_cfz">
                                                        <label for="nano_cfz" class="form-label">72. nano_inh</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('nano_type', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio" name="nano_cfz"
                                                                                id="nano_cfz<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['nano_cfz'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('nano_cfz')">Unset</button>

                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3" id="nano_dlm">
                                                        <label for="nano_dlm" class="form-label">73. nano_dlm</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('nano_type', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio" name="nano_dlm"
                                                                                id="nano_dlm<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['nano_dlm'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('nano_dlm')">Unset</button>

                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3" id="nano_eto">
                                                        <label for="nano_eto" class="form-label">74. nano_eto</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('nano_type', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio" name="nano_eto"
                                                                                id="nano_eto<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['nano_eto'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('nano_eto')">Unset</button>

                                                        </div>
                                                    </div>

                                                </div>

                                                <hr>

                                                <div class="row">
                                                    <div class="col-sm-3" id="nano_lfx">
                                                        <label for="nano_lfx" class="form-label">75. nano_lfx</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('nano_type', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio" name="nano_lfx"
                                                                                id="nano_lfx<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['nano_lfx'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('nano_lfx')">Unset</button>

                                                        </div>
                                                    </div>


                                                    <div class="col-sm-3" id="nano_lzd">
                                                        <label for="nano_lzd" class="form-label">76. nano_lzd</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('nano_type', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio" name="nano_lzd"
                                                                                id="nano_lzd<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['nano_lzd'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('nano_lzd')">Unset</button>

                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3" id="nano_pmd">
                                                        <label for="nano_pmd" class="form-label">77. nano_kan</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('nano_type', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio" name="nano_pmd"
                                                                                id="nano_pmd<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['nano_pmd'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('nano_pmd')">Unset</button>

                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3" id="nano_stm">
                                                        <label for="nano_stm" class="form-label">78. nano_mxf</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('nano_type', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio" name="nano_stm"
                                                                                id="nano_stm<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['nano_stm'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?>>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('nano_stm')">Unset</button>

                                                        </div>
                                                    </div>
                                                </div>

                                                <hr>

                                                <div class="card card-warning">
                                                    <div class="card-header">
                                                        <h3 class="card-title">ANY COMENT OR REMARKS</h3>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>Remarks / Comments:</label>
                                                                <textarea class="form-control" name="comments" rows="3"
                                                                    placeholder="Type comments here..."><?php if ($costing['comments']) {
                                                                        print_r($costing['comments']);
                                                                    } ?>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>

                                                <div class="card card-warning">
                                                    <div class="card-header">
                                                        <h3 class="card-title">FORM STATUS</h3>
                                                    </div>
                                                </div>
                                                <hr>

                                                <div class="row">
                                                    <div class="col-sm-6" id="form_completness">
                                                        <label>Complete?</label>
                                                        <!-- radio -->
                                                        <div class="row-form clearfix">
                                                            <div class="form-group">
                                                                <?php foreach ($override->get('form_completness', 'status', 1) as $value) { ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="form_completness"
                                                                                id="form_completness<?= $value['id']; ?>"
                                                                                value="<?= $value['id']; ?>" <?php if ($costing['form_completness'] == $value['id']) {
                                                                                      echo 'checked';
                                                                                  } ?> required>
                                                                            <label
                                                                                class="form-check-label"><?= $value['name']; ?></label>
                                                                        </div>
                                                                <?php } ?>
                                                            </div>
                                                            <button type="button"
                                                                onclick="unsetRadio('form_completness')">Unset</button>

                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="mb-2">
                                                            <label for="date_completed" class="form-label">Date form
                                                                completed</label>
                                                            <input type="date" value="<?php if ($costing['date_completed']) {
                                                                print_r($costing['date_completed']);
                                                            } ?>" id="date_completed" name="date_completed"
                                                                max="<?= date('Y-m-d') ?>" class="form-control"
                                                                placeholder="Enter date" required />
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                            </div>
                                            <!-- /.card-body -->
                                            <div class="card-footer">
                                                <a href="info.php?id=4&cid=<?= $_GET['cid']; ?>&study_id=<?= $_GET['study_id']; ?>&status=<?= $_GET['status']; ?>"
                                                    class="btn btn-default">Back</a>
                                                <input type="submit" name="add_validations" value="Submit"
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
        <?php } elseif ($_GET['id'] == 18) { ?>
                <?php
                $costing = $override->getData('users')[0];

                ?>
                <!-- Content Wrapper. Contains page content -->
                <div class="content-wrapper">
                    <!-- Content Header (Page header) -->
                    <section class="content-header">
                        <div class="container-fluid">
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <?php if ($costing) { ?>
                                            <h1>Update New validations Data (PID :<?= $costing['id'] ?>)</h1>
                                    <?php } else { ?>
                                            <h1>Add validations Data (PID :<?= $costing['id'] ?>)</h1>
                                    <?php } ?>
                                </div>
                                <div class="col-sm-6">
                                    <ol class="breadcrumb float-sm-right">
                                        <li class="breadcrumb-item"><a href="info.php?id=17">
                                                < Back</a>
                                        </li>&nbsp;&nbsp;
                                        <li class="breadcrumb-item"><a href="index1.php">Home</a></li>&nbsp;&nbsp;
                                        <li class="breadcrumb-item"><a href="info.php?id=17">
                                                Go to Users list > </a>
                                        </li>&nbsp;&nbsp;
                                        <?php if (!$costing) { ?>
                                                <li class="breadcrumb-item active">Add New Users</li>
                                        <?php } else { ?>
                                                <li class="breadcrumb-item active">Update Users</li>
                                        <?php } ?>
                                    </ol>
                                </div>
                            </div>
                        </div><!-- /.container-fluid -->
                    </section>

                    <!-- Main content -->
                    <section class="content">
                        <div class="container-fluid">
                            <div class="row">
                                <!-- right column -->
                                <div class="col-md-12">
                                    <!-- general form elements disabled -->
                                    <div class="card card-warning">
                                        <div class="card-header">
                                            <h3 class="card-title">Users Form</h3>
                                        </div>
                                        <!-- /.card-header -->
                                        <form id="userForm">
                                            <div class="card-body">
                                                <hr>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="mb-2">
                                                            <label for="date_collect" class="form-label">Name</label>
                                                            <input type="text" value="<?php if ($costing['name']) {
                                                                print_r($costing['name']);
                                                            } ?>" id="name" name="name" class="form-control"
                                                                placeholder="Enter here" required />
                                                        </div>
                                                    </div>

                                                    <div class="col-6">
                                                        <div class="mb-3">
                                                            <label for="date_receictrl" class="form-label">email</label>
                                                            <input type="email" value="<?php if ($costing['email']) {
                                                                print_r($costing['email']);
                                                            } ?>" id="email" name="email" class="form-control"
                                                                placeholder="Enter here" required />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /.card-body -->
                                            <div class="card-footer">
                                                <a href="info.php?id=17" class="btn btn-default">Back</a>
                                                <!-- <input type="submit" name="add_Offline" value="Submit"
                                                class="btn btn-primary"> -->
                                                <button type="submit">Submit</button>

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
        <?php } elseif ($_GET['id'] == 19) { ?>
        <?php } elseif ($_GET['id'] == 20) { ?>
        <?php } elseif ($_GET['id'] == 21) { ?>
        <?php } elseif ($_GET['id'] == 22) { ?>
        <?php } elseif ($_GET['id'] == 23) { ?>
        <?php } elseif ($_GET['id'] == 24) { ?>
        <?php } elseif ($_GET['id'] == 25) { ?>
        <?php } elseif ($_GET['id'] == 26) { ?>
        <?php } elseif ($_GET['id'] == 27) { ?>
        <?php } elseif ($_GET['id'] == 28) { ?>
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
    <!-- Select2 -->
    <script src="plugins/select2/js/select2.full.min.js"></script>
    <!-- Bootstrap4 Duallistbox -->
    <script src="plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js"></script>
    <!-- InputMask -->
    <script src="plugins/moment/moment.min.js"></script>
    <script src="plugins/inputmask/jquery.inputmask.min.js"></script>
    <!-- date-range-picker -->
    <script src="plugins/daterangepicker/daterangepicker.js"></script>
    <!-- bootstrap color picker -->
    <script src="plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    <!-- Bootstrap Switch -->
    <script src="plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
    <!-- BS-Stepper -->
    <script src="plugins/bs-stepper/js/bs-stepper.min.js"></script>
    <!-- dropzonejs -->
    <script src="plugins/dropzone/min/dropzone.min.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <!-- <script src="../../dist/js/demo.js"></script> -->
    <!-- Page specific script -->


    <!-- SCREENING Js -->
    <script src="js/screening/screening.js"></script>
    <script src="js/screening/form_status.js"></script>
    <!-- <script src="js/screening/offlineMode.js"></script> -->

    <!-- Enrollment Js -->
    <script src="js/enrollment/enrollment.js"></script>


    <!-- RESPIRATORY format numbers Js -->
    <script src="js/laboratory_clinic/laboratory_clinic.js"></script>
    <!-- <script src="js/laboratory/utils.js"></script>
    <script src="js/laboratory/formVisibility.js"></script>
    <script src="js/laboratory/formValidation.js"></script> -->

    <!-- Diagnosis Test format numbers Js -->
    <script src="js/laboratory_zonal_ctlr/laboratory_zonal_ctlr.js"></script>

    <!-- Diagnosis Js -->
    <script src="js/diagnosis/diagnosis.js"></script>
    <script src="js/diagnosis/treatmentChanges.js"></script>

    <script src="js/radio.js"></script>

    <script>
        $(function () {
            //Initialize Select2 Elements
            $('.select2').select2()

            //Initialize Select2 Elements
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            })

            //Datemask dd/mm/yyyy
            $('#datemask').inputmask('dd/mm/yyyy', {
                'placeholder': 'dd/mm/yyyy'
            })
            //Datemask2 mm/dd/yyyy
            $('#datemask2').inputmask('mm/dd/yyyy', {
                'placeholder': 'mm/dd/yyyy'
            })
            //Money Euro
            $('[data-mask]').inputmask()

            //Date picker
            $('#reservationdate').datetimepicker({
                format: 'L'
            });

            //Date and time picker
            $('#reservationdatetime').datetimepicker({
                icons: {
                    time: 'far fa-clock'
                }
            });

            //Date range picker
            $('#reservation').daterangepicker()
            //Date range picker with time picker
            $('#reservationtime').daterangepicker({
                timePicker: true,
                timePickerIncrement: 30,
                locale: {
                    format: 'MM/DD/YYYY hh:mm A'
                }
            })
            //Date range as a button
            $('#daterange-btn').daterangepicker({
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                startDate: moment().subtract(29, 'days'),
                endDate: moment()
            },
                function (start, end) {
                    $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'))
                }
            )

            //Timepicker
            $('#timepicker').datetimepicker({
                format: 'LT'
            })

            //Bootstrap Duallistbox
            $('.duallistbox').bootstrapDualListbox()

            //Colorpicker
            $('.my-colorpicker1').colorpicker()
            //color picker with addon
            $('.my-colorpicker2').colorpicker()

            $('.my-colorpicker2').on('colorpickerChange', function (event) {
                $('.my-colorpicker2 .fa-square').css('color', event.color.toString());
            })

            $("input[data-bootstrap-switch]").each(function () {
                $(this).bootstrapSwitch('state', $(this).prop('checked'));
            })

        })

        // BS-Stepper Init
        document.addEventListener('DOMContentLoaded', function () {
            window.stepper = new Stepper(document.querySelector('.bs-stepper'))
        })

        // DropzoneJS Demo Code Start
        Dropzone.autoDiscover = false

        // Get the template HTML and remove it from the doumenthe template HTML and remove it from the doument
        var previewNode = document.querySelector("#template")
        previewNode.id = ""
        var previewTemplate = previewNode.parentNode.innerHTML
        previewNode.parentNode.removeChild(previewNode)

        var myDropzone = new Dropzone(document.body, { // Make the whole body a dropzone
            url: "/target-url", // Set the url
            thumbnailWidth: 80,
            thumbnailHeight: 80,
            parallelUploads: 20,
            previewTemplate: previewTemplate,
            autoQueue: false, // Make sure the files aren't queued until manually added
            previewsContainer: "#previews", // Define the container to display the previews
            clickable: ".fileinput-button" // Define the element that should be used as click trigger to select files.
        })

        myDropzone.on("addedfile", function (file) {
            // Hookup the start button
            file.previewElement.querySelector(".start").onclick = function () {
                myDropzone.enqueueFile(file)
            }
        })

        // Update the total progress bar
        myDropzone.on("totaluploadprogress", function (progress) {
            document.querySelector("#total-progress .progress-bar").style.width = progress + "%"
        })

        myDropzone.on("sending", function (file) {
            // Show the total progress bar when upload starts
            document.querySelector("#total-progress").style.opacity = "1"
            // And disable the start button
            file.previewElement.querySelector(".start").setAttribute("disabled", "disabled")
        })

        // Hide the total progress bar when nothing's uploading anymore
        myDropzone.on("queuecomplete", function (progress) {
            document.querySelector("#total-progress").style.opacity = "0"
        })

        // Setup the buttons for all transfers
        // The "add files" button doesn't need to be setup because the config
        // `clickable` has already been specified.
        document.querySelector("#actions .start").onclick = function () {
            myDropzone.enqueueFiles(myDropzone.getFilesWithStatus(Dropzone.ADDED))
        }
        document.querySelector("#actions .cancel").onclick = function () {
            myDropzone.removeAllFiles(true)
        }
        // DropzoneJS Demo Code End

        $('#xpert_mtb').change(function () {
            var xpert_mtb = $(this).val();
            $.ajax({
                url: "process.php?content=xpert_mtb",
                method: "GET",
                data: {
                    xpert_mtb: xpert_mtb
                },
                dataType: "text",
                success: function (data) {
                    $('#xpert_mtb').html(data);
                }
            });
        });
    </script>
</body>

</html>