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
if ($user->isLoggedIn()) {
    if (Input::exists('post')) {
        if (Input::get('add_user')) {
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
                    $user->createRecord('user', array(
                        'firstname' => Input::get('firstname'),
                        'lastname' => Input::get('lastname'),
                        'username' => Input::get('username'),
                        'position' => Input::get('position'),
                        'phone_number' => Input::get('phone_number'),
                        'password' => Hash::make($password, $salt),
                        'salt' => $salt,
                        'create_on' => date('Y-m-d'),
                        'last_login' => '',
                        'status' => 1,
                        'power' => 0,
                        'email_address' => Input::get('email_address'),
                        'accessLevel' => $accessLevel,
                        'user_id' => $user->data()->id,
                        'site_id' => Input::get('site_id'),
                        'count' => 0,
                        'pswd' => 0,
                    ));
                    $successMessage = 'Account Created Successful';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_position')) {
            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $user->createRecord('position', array(
                        'name' => Input::get('name'),
                    ));
                    $successMessage = 'Position Successful Added';
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
                    $user->createRecord('site', array(
                        'name' => Input::get('name'),
                    ));
                    $successMessage = 'Site Successful Added';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_client')) {
            $validate = new validate();
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
                'phone_number' => array(
                    'required' => true,
                    // 'unique' => 'clients',
                ),
            ));
            if ($validate->passed()) {
                try {
                    $age = $user->dateDiffYears(date('Y-m-d'), Input::get('dob'));
                    $client = $override->get('clients', 'id', $_GET['cid']);
                    if ($client) {
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
                        ), $client[0]['id']);
                        $successMessage = 'Client Updated Successful';
                    } else {
                        $user->createRecord('clients', array(
                            'study_id' => '',
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
                            'comments' => Input::get('comments'),
                            'initials' => Input::get('initials'),
                            'status' => 1,
                            'created_on' => date('Y-m-d'),
                        ));
                        $successMessage = 'Client Added Successful';
                    }
                    Redirect::to('info.php?id=3&status=' . $_GET['status'] . '&msg=' . $successMessage);
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_crf1')) {
            $validate = $validate->check($_POST, array(
                // 'diagnosis_date' => array(
                //     'required' => true,
                // ),
            ));
            if ($validate->passed()) {
                try {

                    $data = $override->getNews5('crf1', 'status', 1, 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                    if ($data) {

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

                        $successMessage = 'CRF1 Updated Successful';
                    } else {
                        $user->createRecord('crf1', array(
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
                        ));

                        $successMessage = 'CRF1 added Successful';
                    }

                    $user->updateRecord('clients', array(
                        'nimregenin' => Input::get('nimregenin_herbal'),
                    ), $_GET['cid']);

                    Redirect::to('info.php?id=6&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&sid=' . $_GET['sid']);
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_nimregenin')) {
            $validate = $validate->check($_POST, array(
                // 'diagnosis_date' => array(
                //     'required' => true,
                // ),
            ));
            if ($validate->passed()) {
                try {
                    $user->createRecord('nimregenin', array(
                        'vid' => $_GET["vid"],
                        'vcode' => $_GET["vcode"],
                        'study_id' => $_GET['sid'],
                        'nimregenin_herbal' => Input::get('nimregenin_herbal'),
                        'nimregenin_preparation' => Input::get('nimregenin_preparation'),
                        'nimregenin_start' => Input::get('nimregenin_start'),
                        'nimregenin_ongoing' => Input::get('nimregenin_ongoing'),
                        'nimregenin_end' => Input::get('nimregenin_end'),
                        'nimregenin_dose' => Input::get('nimregenin_dose'),
                        'nimregenin_frequency' => Input::get('nimregenin_frequency'),
                        'nimregenin_remarks' => Input::get('nimregenin_remarks'),
                        'crf1_cmpltd_date' => Input::get('crf1_cmpltd_date'),
                        'patient_id' => $_GET['cid'],
                        'staff_id' => $user->data()->id,
                        'status' => 1,
                        'created_on' => date('Y-m-d'),
                        'site_id' => $user->data()->site_id,
                    ));

                    $user->updateRecord('clients', array(
                        'nimregenin' => Input::get('nimregenin_herbal'),
                    ), $_GET['cid']);

                    Redirect::to('info.php?id=6&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&sid=' . $_GET['sid']);
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('update_nimregenin')) {
            $validate = $validate->check($_POST, array(
                // 'diagnosis_date' => array(
                //     'required' => true,
                // ),
            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('nimregenin', array(
                        'vid' => $_GET["vid"],
                        'vcode' => $_GET["vcode"],
                        'study_id' => $_GET['sid'],
                        'nimregenin_herbal' => Input::get('nimregenin_herbal'),
                        'nimregenin_preparation' => Input::get('nimregenin_preparation'),
                        'nimregenin_start' => Input::get('nimregenin_start'),
                        'nimregenin_ongoing' => Input::get('nimregenin_ongoing'),
                        'nimregenin_end' => Input::get('nimregenin_end'),
                        'nimregenin_dose' => Input::get('nimregenin_dose'),
                        'nimregenin_frequency' => Input::get('nimregenin_frequency'),
                        'nimregenin_remarks' => Input::get('nimregenin_remarks'),
                        'crf1_cmpltd_date' => Input::get('crf1_cmpltd_date'),
                        'patient_id' => $_GET['cid'],
                        'staff_id' => $user->data()->id,
                        'status' => 1,
                        'created_on' => date('Y-m-d'),
                        'site_id' => $user->data()->site_id,
                    ), Input::get('nimregenin_id'));

                    $user->updateRecord('clients', array(
                        'nimregenin' => Input::get('nimregenin_herbal'),
                    ), $_GET['cid']);

                    Redirect::to('info.php?id=6&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&sid=' . $_GET['sid']);
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('delete_nimregenin')) {
            $validate = $validate->check($_POST, array(
                // 'diagnosis_date' => array(
                //     'required' => true,
                // ),
            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('nimregenin', array(
                        'status' => 0,
                    ), Input::get('nimregenin_id'));

                    $user->updateRecord('clients', array(
                        'nimregenin' => 0,
                    ), $_GET['cid']);

                    Redirect::to('info.php?id=6&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&sid=' . $_GET['sid']);
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_other_herbal')) {
            $validate = $validate->check($_POST, array(
                // 'diagnosis_date' => array(
                //     'required' => true,
                // ),
            ));
            if ($validate->passed()) {
                try {
                    $user->createRecord('herbal_treatment', array(
                        'vid' => $_GET["vid"],
                        'vcode' => $_GET["vcode"],
                        'other_herbal' => Input::get('other_herbal'),
                        'herbal_preparation' => Input::get('herbal_preparation'),
                        'herbal_start' => Input::get('herbal_start'),
                        'herbal_ongoing' => Input::get('herbal_ongoing'),
                        'herbal_end' => Input::get('herbal_end'),
                        'herbal_dose' => Input::get('herbal_dose'),
                        'herbal_frequency' => Input::get('herbal_frequency'),
                        'herbal_remarks' => Input::get('herbal_remarks'),
                        'crf1_cmpltd_date' => Input::get('crf1_cmpltd_date'),
                        'patient_id' => $_GET['cid'],
                        'staff_id' => $user->data()->id,
                        'status' => 1,
                        'created_on' => date('Y-m-d'),
                        'site_id' => $user->data()->site_id,
                    ));

                    Redirect::to('info.php?id=6&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&sid=' . $_GET['sid']);
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('update_other_herbal')) {
            $validate = $validate->check($_POST, array(
                // 'diagnosis_date' => array(
                //     'required' => true,
                // ),
            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('herbal_treatment', array(
                        'vid' => $_GET["vid"],
                        'vcode' => $_GET["vcode"],
                        'study_id' => $_GET['sid'],
                        'other_herbal' => Input::get('other_herbal'),
                        'herbal_preparation' => Input::get('herbal_preparation'),
                        'herbal_start' => Input::get('herbal_start'),
                        'herbal_ongoing' => Input::get('herbal_ongoing'),
                        'herbal_end' => Input::get('herbal_end'),
                        'herbal_dose' => Input::get('herbal_dose'),
                        'herbal_frequency' => Input::get('herbal_frequency'),
                        'herbal_remarks' => Input::get('herbal_remarks'),
                        'crf1_cmpltd_date' => Input::get('crf1_cmpltd_date'),
                        'patient_id' => $_GET['cid'],
                        'staff_id' => $user->data()->id,
                        'status' => 1,
                        'created_on' => date('Y-m-d'),
                        'site_id' => $user->data()->site_id,
                    ), Input::get('herbal_id'));

                    Redirect::to('info.php?id=6&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&sid=' . $_GET['sid']);
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('delete_other_herbal')) {
            $validate = $validate->check($_POST, array(
                // 'diagnosis_date' => array(
                //     'required' => true,
                // ),
            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('herbal_treatment', array(
                        'status' => 0,
                    ), Input::get('herbal_id'));

                    Redirect::to('info.php?id=6&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&sid=' . $_GET['sid']);
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_radiotherapy')) {
            $validate = $validate->check($_POST, array(
                // 'diagnosis_date' => array(
                //     'required' => true,
                // ),
            ));
            if ($validate->passed()) {
                try {
                    $user->createRecord('radiotherapy', array(
                        'vid' => $_GET["vid"],
                        'vcode' => $_GET["vcode"],
                        'study_id' => $_GET['sid'],
                        'other_herbal' => Input::get('other_herbal'),
                        'radiotherapy_performed' => Input::get('radiotherapy_performed'),
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
                    ));

                    Redirect::to('info.php?id=6&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&sid=' . $_GET['sid']);
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('update_radiotherapy')) {
            $validate = $validate->check($_POST, array(
                // 'diagnosis_date' => array(
                //     'required' => true,
                // ),
            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('radiotherapy', array(
                        'vid' => $_GET["vid"],
                        'vcode' => $_GET["vcode"],
                        'study_id' => $_GET['sid'],
                        'other_herbal' => Input::get('other_herbal'),
                        'radiotherapy_performed' => Input::get('radiotherapy_performed'),
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
                    ), Input::get('radiotherapy_id'));

                    Redirect::to('info.php?id=6&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&sid=' . $_GET['sid']);
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('delete_radiotherapy')) {
            $validate = $validate->check($_POST, array(
                // 'diagnosis_date' => array(
                //     'required' => true,
                // ),
            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('radiotherapy', array(
                        'status' => 0,
                    ), Input::get('radiotherapy_id'));

                    Redirect::to('info.php?id=6&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&sid=' . $_GET['sid']);
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_chemotherapy')) {
            $validate = $validate->check($_POST, array(
                // 'diagnosis_date' => array(
                //     'required' => true,
                // ),
            ));
            if ($validate->passed()) {
                try {
                    $user->createRecord('chemotherapy', array(
                        'vid' => $_GET["vid"],
                        'vcode' => $_GET["vcode"],
                        'other_herbal' => Input::get('other_herbal'),
                        'chemotherapy_performed' => Input::get('chemotherapy_performed'),
                        'chemotherapy' => Input::get('chemotherapy'),
                        'chemotherapy_start' => Input::get('chemotherapy_start'),
                        'chemotherapy_ongoing' => Input::get('chemotherapy_ongoing'),
                        'chemotherapy_end' => Input::get('chemotherapy_end'),
                        'chemotherapy_dose' => Input::get('chemotherapy_dose'),
                        'chemotherapy_frequecy' => Input::get('chemotherapy_frequecy'),
                        'chemotherapy_remarks' => Input::get('chemotherapy_remarks'),
                        'crf1_cmpltd_date' => Input::get('crf1_cmpltd_date'),
                        'patient_id' => $_GET['cid'],
                        'staff_id' => $user->data()->id,
                        'status' => 1,
                        'created_on' => date('Y-m-d'),
                        'site_id' => $user->data()->site_id,
                    ));

                    Redirect::to('info.php?id=6&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&sid=' . $_GET['sid']);
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('update_chemotherapy')) {
            $validate = $validate->check($_POST, array(
                // 'diagnosis_date' => array(
                //     'required' => true,
                // ),
            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('chemotherapy', array(
                        'vid' => $_GET["vid"],
                        'vcode' => $_GET["vcode"],
                        'other_herbal' => Input::get('other_herbal'),
                        'chemotherapy_performed' => Input::get('chemotherapy_performed'),
                        'chemotherapy' => Input::get('chemotherapy'),
                        'chemotherapy_start' => Input::get('chemotherapy_start'),
                        'chemotherapy_ongoing' => Input::get('chemotherapy_ongoing'),
                        'chemotherapy_end' => Input::get('chemotherapy_end'),
                        'chemotherapy_dose' => Input::get('chemotherapy_dose'),
                        'chemotherapy_frequecy' => Input::get('chemotherapy_frequecy'),
                        'chemotherapy_remarks' => Input::get('chemotherapy_remarks'),
                        'crf1_cmpltd_date' => Input::get('crf1_cmpltd_date'),
                        'patient_id' => $_GET['cid'],
                        'staff_id' => $user->data()->id,
                        'status' => 1,
                        'created_on' => date('Y-m-d'),
                        'site_id' => $user->data()->site_id,
                    ), Input::get('chemotherapy_id'));

                    Redirect::to('info.php?id=6&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&sid=' . $_GET['sid']);
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('delete_chemotherapy')) {
            $validate = $validate->check($_POST, array(
                // 'diagnosis_date' => array(
                //     'required' => true,
                // ),
            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('chemotherapy', array(
                        'status' => 0,
                    ), Input::get('chemotherapy_id'));

                    Redirect::to('info.php?id=6&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&sid=' . $_GET['sid']);
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_surgery')) {
            $validate = $validate->check($_POST, array(
                // 'diagnosis_date' => array(
                //     'required' => true,
                // ),
            ));
            if ($validate->passed()) {
                try {
                    $user->createRecord('surgery', array(
                        'vid' => $_GET["vid"],
                        'vcode' => $_GET["vcode"],
                        'study_id' => $_GET['sid'],
                        'other_herbal' => Input::get('other_herbal'),
                        'surgery_performed' => Input::get('surgery_performed'),
                        'surgery' => Input::get('surgery'),
                        'surgery_start' => Input::get('surgery_start'),
                        'surgery_number' => Input::get('surgery_number'),
                        'surgery_remarks' => Input::get('surgery_remarks'),
                        'crf1_cmpltd_date' => Input::get('crf1_cmpltd_date'),
                        'patient_id' => $_GET['cid'],
                        'staff_id' => $user->data()->id,
                        'status' => 1,
                        'created_on' => date('Y-m-d'),
                        'site_id' => $user->data()->site_id,
                    ));

                    Redirect::to('info.php?id=6&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&sid=' . $_GET['sid']);
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('update_surgery')) {
            $validate = $validate->check($_POST, array(
                // 'diagnosis_date' => array(
                //     'required' => true,
                // ),
            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('surgery', array(
                        'vid' => $_GET["vid"],
                        'vcode' => $_GET["vcode"],
                        'study_id' => $_GET['sid'],
                        'other_herbal' => Input::get('other_herbal'),
                        'surgery_performed' => Input::get('surgery_performed'),
                        'surgery' => Input::get('surgery'),
                        'surgery_start' => Input::get('surgery_start'),
                        'surgery_number' => Input::get('surgery_number'),
                        'surgery_remarks' => Input::get('surgery_remarks'),
                        'crf1_cmpltd_date' => Input::get('crf1_cmpltd_date'),
                        'patient_id' => $_GET['cid'],
                        'staff_id' => $user->data()->id,
                        'status' => 1,
                        'created_on' => date('Y-m-d'),
                        'site_id' => $user->data()->site_id,
                    ), Input::get('surgery_id'));

                    Redirect::to('info.php?id=6&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&sid=' . $_GET['sid']);
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('delete_surgery')) {
            $validate = $validate->check($_POST, array(
                // 'diagnosis_date' => array(
                //     'required' => true,
                // ),
            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('surgery', array(
                        'status' => 0,
                    ), Input::get('surgery_id'));

                    Redirect::to('info.php?id=6&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&sid=' . $_GET['sid']);
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_other_medication')) {
            $validate = $validate->check($_POST, array(
                // 'diagnosis_date' => array(
                //     'required' => true,
                // ),
            ));
            if ($validate->passed()) {
                try {
                    $user->createRecord('other_medication', array(
                        'vid' => $_GET["vid"],
                        'vcode' => $_GET["vcode"],
                        'study_id' => $_GET['sid'],
                        'other_medical' => Input::get('other_medical'),
                        'other_specify' => Input::get('other_specify')[$i],
                        'other_medical_medicatn' => Input::get('other_medical_medicatn')[$i],
                        'other_medicatn_name' => Input::get('other_medicatn_name')[$i],
                        'medication_remarks' => Input::get('other_medication_remarks')[$i],
                        'crf1_cmpltd_date' => Input::get('crf1_cmpltd_date'),
                        'patient_id' => $_GET['cid'],
                        'staff_id' => $user->data()->id,
                        'status' => 1,
                        'created_on' => date('Y-m-d'),
                        'site_id' => $user->data()->site_id,
                    ));

                    Redirect::to('info.php?id=6&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&sid=' . $_GET['sid']);
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('update_other_medication')) {
            $validate = $validate->check($_POST, array(
                // 'diagnosis_date' => array(
                //     'required' => true,
                // ),
            ));
            if ($validate->passed()) {
                try {
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
                    ), Input::get('other_medication_id')[$i]);

                    Redirect::to('info.php?id=6&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&sid=' . $_GET['sid']);
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('delete_other_medication')) {
            $validate = $validate->check($_POST, array(
                // 'diagnosis_date' => array(
                //     'required' => true,
                // ),
            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('other_medication', array(
                        'status' => 0,
                    ), Input::get('other_medication_id'));

                    Redirect::to('info.php?id=6&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&sid=' . $_GET['sid']);
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_crf2')) {
            $validate = $validate->check($_POST, array(
                'crf2_date' => array(
                    'required' => true,
                ),

            ));
            if ($validate->passed()) {
                try {
                    $data = $override->getNews5('crf2', 'status', 1, 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                    if ($data) {
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
                    } else {
                        $user->createRecord('crf2', array(
                            'vid' => $_GET['vid'],
                            'vcode' => $_GET['vcode'],
                            'study_id' => $_GET['sid'],
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
                        ));
                        $successMessage = 'CRF2 added Successful';
                    }
                    Redirect::to('info.php?id=6&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&sid=' . $_GET['sid']);
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_crf3')) {
            $validate = $validate->check($_POST, array(
                'crf3_date' => array(
                    'required' => true,
                ),

            ));
            if ($validate->passed()) {
                try {
                    $data = $override->getNews5('crf3', 'status', 1, 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                    if ($data) {
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
                    } else {
                        $user->createRecord('crf3', array(
                            'vid' => $_GET["vid"],
                            'vcode' => $_GET["vcode"],
                            'study_id' => $_GET['sid'],
                            'crf3_date' => Input::get('crf3_date'),
                            'fever' => Input::get('fever'),
                            'vomiting' => Input::get('vomiting'),
                            'diarrhoea' => Input::get('diarrhoea'),
                            'loss_appetite' => Input::get('loss_appetite'),
                            'nausea' => Input::get('nausea'),
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
                        ));


                        $successMessage = 'CRF3 added Successful';
                    }
                    Redirect::to('info.php?id=6&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&sid=' . $_GET['sid']);
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_crf4')) {
            $validate = $validate->check($_POST, array(
                // 'sample_date' => array(
                //     'required' => true,
                // ),
            ));
            if ($validate->passed()) {
                try {
                    $data = $override->getNews5('crf4', 'status', 1, 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                    if ($data) {
                        $user->updateRecord('crf4', array(
                            // 'study_id' => $_GET['sid'],
                            // 'vid' => $_GET["vid"],
                            // 'vcode' => $_GET["vcode"],
                            // 'sample_date' => Input::get('sample_date'),
                            // 'renal_urea' => Input::get('renal_urea'),
                            // 'renal_urea_units' => Input::get('renal_urea_units'),
                            // 'renal_creatinine' => Input::get('renal_creatinine'),
                            // 'renal_creatinine_units' => Input::get('renal_creatinine_units'),
                            // 'renal_creatinine_grade' => Input::get('renal_creatinine_grade'),
                            // 'renal_egfr' => Input::get('renal_egfr'),
                            // 'renal_egfr_units' => Input::get('renal_egfr_units'),
                            // 'renal_egfr_grade' => Input::get('renal_egfr_grade'),
                            // 'liver_ast' => Input::get('liver_ast'),
                            // 'liver_ast_grade' => Input::get('liver_ast_grade'),
                            // 'liver_alt' => Input::get('liver_alt'),
                            // 'liver_alt_grade' => Input::get('liver_alt_grade'),
                            // 'liver_alp' => Input::get('liver_alp'),
                            // 'liver_alp_grade' => Input::get('liver_alp_grade'),
                            // 'liver_pt' => Input::get('liver_pt'),
                            // 'liver_pt_grade' => Input::get('liver_pt_grade'),
                            // 'liver_ptt' => Input::get('liver_ptt'),
                            // 'liver_ptt_grade' => Input::get('liver_ptt_grade'),
                            // 'liver_inr' => Input::get('liver_inr'),
                            // 'liver_inr_grade' => Input::get('liver_inr_grade'),
                            // 'liver_ggt' => Input::get('liver_ggt'),
                            // 'liver_albumin' => Input::get('liver_albumin'),
                            // 'liver_albumin_grade' => Input::get('liver_albumin_grade'),
                            // 'liver_bilirubin_total' => Input::get('liver_bilirubin_total'),
                            // 'liver_bilirubin_total_units' => Input::get('liver_bilirubin_total_units'),
                            // 'bilirubin_total_grade' => Input::get('bilirubin_total_grade'),
                            // 'liver_bilirubin_direct' => Input::get('liver_bilirubin_direct'),
                            // 'liver_bilirubin_direct_units' => Input::get('liver_bilirubin_direct_units'),
                            // 'bilirubin_direct_grade' => Input::get('bilirubin_direct_grade'),
                            // 'rbg' => Input::get('rbg'),
                            // 'rbg_units' => Input::get('rbg_units'),
                            // 'rbg_grade' => Input::get('rbg_grade'),
                            // 'ldh' => Input::get('ldh'),
                            // 'crp' => Input::get('crp'),
                            // 'd_dimer' => Input::get('d_dimer'),
                            // 'ferritin' => Input::get('ferritin'),
                            // 'wbc' => Input::get('wbc'),
                            // 'wbc_grade' => Input::get('wbc_grade'),
                            // 'abs_neutrophil' => Input::get('abs_neutrophil'),
                            // 'abs_neutrophil_grade' => Input::get('abs_neutrophil_grade'),
                            // 'abs_lymphocytes' => Input::get('abs_lymphocytes'),
                            // 'abs_lymphocytes_grade' => Input::get('abs_lymphocytes_grade'),
                            // 'abs_eosinophils' => Input::get('abs_eosinophils'),
                            // 'abs_monocytes' => Input::get('abs_monocytes'),
                            // 'abs_basophils' => Input::get('abs_basophils'),
                            // 'hb' => Input::get('hb'),
                            // 'hb_grade' => Input::get('hb_grade'),
                            // 'mcv' => Input::get('mcv'),
                            // 'mch' => Input::get('mch'),
                            // 'hct' => Input::get('hct'),
                            // 'rbc' => Input::get('rbc'),
                            // 'plt' => Input::get('plt'),
                            // 'plt_grade' => Input::get('plt_grade'),
                            // 'cancer' => Input::get('cancer'),
                            // 'prostate' => Input::get('prostate'),
                            // 'chest_xray' => Input::get('chest_xray'),
                            // 'chest_specify' => Input::get('chest_specify'),
                            // 'ct_chest' => Input::get('ct_chest'),
                            // 'ct_chest_specify' => Input::get('ct_chest_specify'),
                            // 'ultrasound' => Input::get('ultrasound'),
                            // 'ultrasound_specify' => Input::get('ultrasound_specify'),
                            // 'crf4_cmpltd_date' => Input::get('crf4_cmpltd_date'),
                            // 'patient_id' => $_GET['cid'],
                            // 'staff_id' => $user->data()->id,
                            // 'status' => 1,
                            // 'created_on' => date('Y-m-d'),
                            // 'site_id' => $user->data()->site_id,
                        ), Input::get('id'));
                        $successMessage = 'CRF4 updated Successful';
                    } else {
                        $user->createRecord('crf4', array(
                            'vid' => $_GET["vid"],
                            'vcode' => $_GET["vcode"],
                            'study_id' => $_GET['sid'],
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
                        ));
                        $successMessage = 'CRF4 added Successful';
                    }
                    Redirect::to('info.php?id=6&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&sid=' . $_GET['sid']);
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_crf5')) {
            $validate = $validate->check($_POST, array(
                'date_reported' => array(
                    'required' => true,
                ),

            ));
            if ($validate->passed()) {
                try {
                    $data = $override->getNews5('crf5', 'status', 1, 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                    if ($data) {
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
                    } else {
                        $user->createRecord('crf5', array(
                            'vid' => $_GET["vid"],
                            'vcode' => $_GET["vcode"],
                            'study_id' => $_GET['sid'],
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
                        ));
                        $successMessage = 'CRF5 added Successful';
                    }
                    Redirect::to('info.php?id=6&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&sid=' . $_GET['sid']);
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_crf6')) {
            $validate = $validate->check($_POST, array(
                'today_date' => array(
                    'required' => true,
                ),

            ));
            if ($validate->passed()) {
                try {
                    if ((Input::get('completed120days') == 1 && Input::get('reported_dead') == 1 && Input::get('withdrew_consent') == 1) || ((Input::get('completed120days') == 2 && Input::get('reported_dead') == 2 && Input::get('withdrew_consent') == 2))) {
                        $errorMessage = 'Reason for termination can not all be "NO" and Only one Can be "YES"';
                    } else {

                        $data = $override->getNews5('crf6', 'status', 1, 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                        if ($data) {

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
                        } else {

                            $user->createRecord('crf6', array(
                                'vid' => $_GET["vid"],
                                'vcode' => $_GET["vcode"],
                                'study_id' => $_GET['sid'],
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
                            ));
                            $successMessage = 'CRF6 added Successful';
                        }
                    }
                    Redirect::to('info.php?id=6&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&sid=' . $_GET['sid']);

                    $user->updateRecord('clients', array(
                        'end_study' => 1,
                    ), $_GET['cid']);
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_crf7')) {
            $validate = $validate->check($_POST, array(
                'tdate' => array(
                    'required' => true,
                ),

            ));
            if ($validate->passed()) {
                try {
                    $data = $override->getNews5('crf2', 'status', 1, 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                    if ($data) {
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
                    } else {

                        $user->createRecord('crf7', array(
                            'vid' => $_GET['vid'],
                            'vcode' => $_GET['vcode'],
                            'study_id' => $_GET['sid'],
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
                        ));
                        $successMessage = 'CRF7 added Successful';
                    }
                    Redirect::to('info.php?id=6&cid=' . $_GET['cid'] . '&vid=' . $_GET['vid'] . '&vcode=' . $_GET['vcode'] . '&sid=' . $_GET['sid']);
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
    <title>Nimregenin Database | Add Page</title>

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
        .bordered {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
        }
    </style>
</head>

<style>
    .bordered {
        border: 1px solid #ddd;
        padding: 10px;
        margin-bottom: 10px;
    }
</style>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <!-- Preloader -->
        <div class="preloader flex-column justify-content-center align-items-center">
            <img class="animation__shake" src="dist/img/AdminLTELogo.png" alt="AdminLTELogo" height="60" width="60">
        </div>

        <!-- Navbar -->
        <?php include 'navbar.php'; ?>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <?php include 'sidemenu.php'; ?>
        <!--/. Main Sidebar Container -->

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
        <?php } elseif ($successMessage || $_GET['msg']) { ?>
            <div class="alert alert-success text-center">
                <h4>Success!</h4>
                <?= $successMessage || $_GET['msg'] ?>
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
                            $site = $override->get('site', 'id', $staff['site_id'])[0];
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
                                                                <?php foreach ($override->getData('site') as $site) { ?>
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
                                                                name="power" id="power" value="<?php if ($staff['power']) {
                                                                    print_r($staff['power']);
                                                                } ?>" />
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
        <?php } elseif ($_GET['id'] == 2 && ($user->data()->position == 1 || $user->data()->position == 2)) { ?>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>Add New Position</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item">
                                        <a href="info.php?id=2">
                                            < Back </a>
                                    </li>&nbsp;&nbsp;
                                    <li class="breadcrumb-item"><a href="index1.php">Home</a></li>&nbsp;&nbsp;
                                    <li class="breadcrumb-item">
                                        <a href="info.php?id=2">
                                            Go to Position list >
                                        </a>
                                    </li>&nbsp;&nbsp;
                                    <li class="breadcrumb-item active">Add New Position</li>
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
                            // $staff = $override->getNews('user', 'status', 1, 'id', $_GET['staff_id'])[0];
                            // $site = $override->get('site', 'id', $staff['site_id'])[0];
                            $position = $override->get('position', 'id', $_GET['position_id'])[0];
                            ?>
                            <!-- right column -->
                            <div class="col-md-12">
                                <!-- general form elements disabled -->
                                <div class="card card-warning">
                                    <div class="card-header">
                                        <h3 class="card-title">Positions</h3>
                                    </div>
                                    <!-- /.card-header -->
                                    <form id="validation" enctype="multipart/form-data" method="post" autocomplete="off">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="row-form clearfix">
                                                        <div class="form-group">
                                                            <label>Position Name</label>
                                                            <input class="form-control" type="text" name="name" id="name"
                                                                value="<?php if ($position['name']) {
                                                                    print_r($position['name']);
                                                                } ?>" required />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /.card-body -->
                                        <div class="card-footer">
                                            <a href="info.php?id=2" class="btn btn-default">Back</a>
                                            <input type="submit" name="add_position" value="Submit" class="btn btn-primary">
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
            <?php
            $client = $override->get('clients', 'id', $_GET['cid'])[0];
            ?>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>Client Form</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item">
                                        <a href="index1.php">
                                            < Back </a>
                                    </li>&nbsp;&nbsp;
                                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                                    <li class="breadcrumb-item active">Client Form</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row justify-content-center">
                            <div class="col-md-10">
                                <?php
                                print_r($_POST);
                                ?>
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title">Client Information Form</h3>
                                    </div>
                                    <!-- Form Start -->
                                    <form id="clients" enctype="multipart/form-data" method="post" autocomplete="off">
                                        <div class="card-body">
                                            <div class="row">
                                                <!-- Column 1 -->
                                                <div class="col-md-6">
                                                    <!-- Study -->
                                                    <div class="form-group">
                                                        <label for="position">Study</label>
                                                        <select name="position" class="form-control" required>
                                                            <?php foreach ($override->getData('study') as $study) { ?>
                                                                <option value="<?= $study['id'] ?>"><?= $study['name'] ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <!-- Column 2 -->
                                                <div class="col-md-6">
                                                    <!-- Date -->
                                                    <div class="form-group">
                                                        <label for="clinic_date">Date:</label>
                                                        <input type="date" class="form-control" name="clinic_date"
                                                            id="clinic_date" value="<?php if ($client['clinic_date']) {
                                                                print_r($client['clinic_date']);
                                                            } ?>" required />
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <!-- Column 1 -->
                                                <div class="col-md-4">
                                                    <!-- First Name -->
                                                    <div class="form-group">
                                                        <label for="firstname">First Name</label>
                                                        <input type="text" class="form-control" name="firstname"
                                                            id="firstname" value="<?php if ($client['firstname']) {
                                                                print_r($client['firstname']);
                                                            } ?>" placeholder="Type firstname..."
                                                            onkeyup="myFunction()" required>
                                                    </div>
                                                </div>

                                                <!-- Column 2 -->
                                                <div class="col-md-4">
                                                    <!-- Middle Name -->
                                                    <div class="form-group">
                                                        <label for="middlename">Middle Name</label>
                                                        <input type="text" class="form-control" name="middlename"
                                                            id="middlename" value="<?php if ($client['middlename']) {
                                                                print_r($client['middlename']);
                                                            } ?>" placeholder="Type middlename..."
                                                            onkeyup="myFunction()" required>
                                                    </div>
                                                </div>

                                                <!-- Column 3 -->
                                                <div class="col-md-4">
                                                    <!-- Last Name -->
                                                    <div class="form-group">
                                                        <label for="lastname">Last Name</label>
                                                        <input type="text" class="form-control" name="lastname"
                                                            id="lastname" value="<?php if ($client['lastname']) {
                                                                print_r($client['lastname']);
                                                            } ?>" placeholder="Type lastname..." onkeyup="myFunction()"
                                                            required>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <!-- Column 1 -->
                                                <div class="col-md-4">
                                                    <!-- Date of Birth -->
                                                    <div class="form-group">
                                                        <label for="dob">Date of Birth:</label>
                                                        <input type="date" class="form-control" name="dob" id="dob" value="<?php if ($client['dob']) {
                                                            print_r($client['dob']);
                                                        } ?>" required />
                                                    </div>
                                                </div>

                                                <!-- Column 2 -->
                                                <div class="col-md-4">
                                                    <!-- Age -->
                                                    <div class="form-group">
                                                        <label for="age">Age</label>
                                                        <input type="number" class="form-control" name="age" id="age" value="<?php if ($client['age']) {
                                                            print_r($client['age']);
                                                        } ?>" required>
                                                    </div>
                                                </div>

                                                <!-- Column 3 -->
                                                <div class="col-md-4">
                                                    <!-- Initials -->
                                                    <div class="form-group">
                                                        <label for="initials">Initials</label>
                                                        <input type="text" class="form-control" name="initials"
                                                            id="initials" value="<?php if ($client['initials']) {
                                                                print_r($client['initials']);
                                                            } ?>" required>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <!-- Column 1 -->
                                                <div class="col-md-4">
                                                    <!-- Gender -->
                                                    <div class="form-group">
                                                        <label for="gender">Gender</label>
                                                        <select name="gender" class="form-control" required>
                                                            <option value="<?php if ($client['gender']) {
                                                                print_r($client['gender']);
                                                            } ?>"><?php if ($client['gender']) {
                                                                 print_r($client['gender']);
                                                             } ?></option>
                                                            <option value="male">Male</option>
                                                            <option value="female">Female</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <!-- Column 2 -->
                                                <div class="col-md-4">
                                                    <!-- Hospital ID Number -->
                                                    <div class="form-group">
                                                        <label for="id_number">Hospital ID Number</label>
                                                        <input type="text" class="form-control" name="id_number"
                                                            id="id_number" value="<?php if ($client['id_number']) {
                                                                print_r($client['id_number']);
                                                            } ?>">
                                                    </div>
                                                </div>

                                                <!-- Column 3 -->
                                                <div class="col-md-4">
                                                    <!-- Marital Status -->
                                                    <div class="form-group">
                                                        <label for="marital_status">Marital Status</label>
                                                        <select name="marital_status" class="form-control" required>
                                                            <option value="<?php if ($client['marital_status']) {
                                                                print_r($client['marital_status']);
                                                            } ?>"><?php if ($client['marital_status']) {
                                                                 print_r($client['marital_status']);
                                                             } ?></option>
                                                            <option value="Single">Single</option>
                                                            <option value="Married">Married</option>
                                                            <option value="Divorced">Divorced</option>
                                                            <option value="Separated">Separated</option>
                                                            <option value="Widower">Widower/Widow</option>
                                                            <option value="Cohabit">Cohabit</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <!-- Column 1 -->
                                                <div class="col-md-4">
                                                    <!-- Education Level -->
                                                    <div class="form-group">
                                                        <label for="education_level">Education Level</label>
                                                        <select name="education_level" class="form-control" required>
                                                            <option value="<?php if ($client['education_level']) {
                                                                print_r($client['education_level']);
                                                            } ?>"><?php if ($client['education_level']) {
                                                                 print_r($client['education_level']);
                                                             } ?></option>
                                                            <option value="Not attended school">Not attended school</option>
                                                            <option value="Primary">Primary</option>
                                                            <option value="Secondary">Secondary</option>
                                                            <option value="Certificate">Certificate</option>
                                                            <option value="Diploma">Diploma</option>
                                                            <option value="Undergraduate degree">Undergraduate degree
                                                            </option>
                                                            <option value="Postgraduate degree">Postgraduate degree</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <!-- Column 2 -->
                                                <div class="col-md-4">
                                                    <!-- Occupation -->
                                                    <div class="form-group">
                                                        <label for="occupation">Occupation</label>
                                                        <input type="text" class="form-control" name="occupation"
                                                            id="occupation" value="<?php if ($client['occupation']) {
                                                                print_r($client['occupation']);
                                                            } ?>" required>
                                                    </div>
                                                </div>

                                                <!-- Column 3 -->
                                                <div class="col-md-4">
                                                    <!-- National ID -->
                                                    <div class="form-group">
                                                        <label for="national_id">National ID</label>
                                                        <input type="text" class="form-control" name="national_id"
                                                            id="national_id" value="<?php if ($client['national_id']) {
                                                                print_r($client['national_id']);
                                                            } ?>">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <!-- Column 1 -->
                                                <div class="col-md-4">
                                                    <!-- Phone Number -->
                                                    <div class="form-group">
                                                        <label for="phone">Phone Number</label>
                                                        <input type="text" class="form-control" name="phone_number"
                                                            id="phone" value="<?php if ($client['phone_number']) {
                                                                print_r($client['phone_number']);
                                                            } ?>" placeholder="Example: 0700 000 111" required>
                                                    </div>
                                                </div>

                                                <!-- Column 2 -->
                                                <div class="col-md-4">
                                                    <!-- Relative's Phone Number -->
                                                    <div class="form-group">
                                                        <label for="other_phone">Relative's Phone Number</label>
                                                        <input type="text" class="form-control" name="other_phone"
                                                            id="other_phone" value="<?php if ($client['other_phone']) {
                                                                print_r($client['other_phone']);
                                                            } ?>" placeholder="Example: 0700 000 111">
                                                    </div>
                                                </div>

                                                <!-- Column 3 -->
                                                <div class="col-md-4">
                                                    <!-- Residence Street -->
                                                    <div class="form-group">
                                                        <label for="street">Residence Street</label>
                                                        <input type="text" class="form-control" name="street" id="street"
                                                            value="<?php if ($client['street']) {
                                                                print_r($client['street']);
                                                            } ?>" required>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <!-- Column 1 -->
                                                <div class="col-md-4">
                                                    <!-- Region -->
                                                    <div class="form-group">
                                                        <label for="region">Region</label>
                                                        <input type="text" class="form-control" name="region" id="region"
                                                            value="<?php if ($client['region']) {
                                                                print_r($client['region']);
                                                            } ?>" required>
                                                    </div>
                                                </div>

                                                <!-- Column 2 -->
                                                <div class="col-md-4">
                                                    <!-- District -->
                                                    <div class="form-group">
                                                        <label for="district">District</label>
                                                        <input type="text" class="form-control" name="district"
                                                            id="district" value="<?php if ($client['district']) {
                                                                print_r($client['district']);
                                                            } ?>" required>
                                                    </div>
                                                </div>

                                                <!-- Column 3 -->
                                                <div class="col-md-4">
                                                    <!-- Ward -->
                                                    <div class="form-group">
                                                        <label for="ward">Ward</label>
                                                        <input type="text" class="form-control" name="ward" id="ward" value="<?php if ($client['ward']) {
                                                            print_r($client['ward']);
                                                        } ?>" required>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Comments -->
                                            <div class="form-group">
                                                <label for="comments">Comments</label>
                                                <textarea name="comments" class="form-control" rows="3"><?php if ($client['comments']) {
                                                    print_r($client['comments']);
                                                } ?></textarea>
                                            </div>
                                        </div>
                                        <!-- Submit Button -->
                                        <div class="card-footer">
                                            <div class="form-group">
                                                <input type="hidden" name="id" value="<?= $patient['id'] ?>">
                                                <input type="submit" name="add_client" value="Submit" class="btn btn-info">
                                                <a href="index1.php" class="btn btn-default">Cancel</a>
                                            </div>
                                        </div>
                                    </form>
                                    <!-- Form End -->
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->

        <?php } elseif ($_GET['id'] == 5) { ?>
        <?php } elseif ($_GET['id'] == 8) { ?>
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
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>Add New Position</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item">
                                        <a href="info.php?id=2">
                                            < Back </a>
                                    </li>&nbsp;&nbsp;
                                    <li class="breadcrumb-item"><a href="index1.php">Home</a></li>&nbsp;&nbsp;
                                    <li class="breadcrumb-item">
                                        <a href="info.php?id=2">
                                            Go to Position list >
                                        </a>
                                    </li>&nbsp;&nbsp;
                                    <li class="breadcrumb-item active">Add New Position</li>
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
                            // $staff = $override->getNews('user', 'status', 1, 'id', $_GET['staff_id'])[0];
                            // $site = $override->get('site', 'id', $staff['site_id'])[0];
                            $position = $override->get('position', 'id', $_GET['position_id'])[0];
                            ?>
                            <!-- right column -->
                            <div class="col-md-12">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title">CRF 1: MEDICAL HISTORY, USE OF HERBAL MEDICINES AND STANDARD
                                            TREATMENT</h3>
                                    </div>
                                    <form id="crf1" method="post" onsubmit="return checkForm(event)">
                                        <script>
                                            function checkForm(event) {
                                                console.log("Form is being submitted");
                                                return true; // Allow form submission
                                            }
                                        </script>
                                        <div class="card-body">
                                            <div class="form-group row">
                                                <label for="diagnosis_date" class="col-sm-3 col-form-label">Date of
                                                    diagnosis:</label>
                                                <div class="col-sm-9">
                                                    <input value="<?= $patient['diagnosis_date'] ?>" type="date"
                                                        name="diagnosis_date" id="diagnosis_date" class="form-control"
                                                        required />
                                                    <span>Example : 2000-12-26 </span>
                                                </div>
                                            </div>

                                            <div class="card card-primary">
                                                <div class="card-header">
                                                    <h3 class="card-title">Medical History</h3>
                                                </div>
                                                <h5>Do the patients have any of the following medical conditions</h5>
                                            </div>

                                            <div class="form-group row">
                                                <div class="col-sm-6 bordered">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <label class="col-form-label">1. Diabetic Mellitus:</label>
                                                            <select name="diabetic" id="diabetic" class="form-control">
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

                                                        <div class="col-sm-4">
                                                            <label class="col-form-label">1. Is the patient on
                                                                Medication?</label>
                                                            <select name="diabetic_medicatn" id="diabetic_medicatn"
                                                                class="form-control">
                                                                <?php if ($patient['diabetic_medicatn'] == "1") { ?>
                                                                    <option value="<?= $patient['diabetic_medicatn'] ?>">Yes
                                                                    </option>
                                                                <?php } elseif ($patient['diabetic_medicatn'] == "2") { ?>
                                                                    <option value="<?= $patient['diabetic_medicatn'] ?>">No
                                                                    </option>
                                                                <?php } else { ?>
                                                                    <option value="">Select</option>
                                                                <?php } ?>
                                                                <option value="1">Yes</option>
                                                                <option value="2">No</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-sm-4">
                                                            <label class="col-form-label">1. Mention the
                                                                medications:</label>
                                                            <textarea name="diabetic_medicatn_name" rows="4"
                                                                class="form-control"><?= $patient['diabetic_medicatn_name'] ?></textarea>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-sm-6 bordered">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <label class="col-form-label">2. Hypertension:</label>
                                                            <select name="hypertension" id="hypertension"
                                                                class="form-control">
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

                                                        <div class="col-sm-4">
                                                            <label class="col-form-label">2. Is the patient on
                                                                Medication?</label>
                                                            <select name="hypertension_medicatn" id="hypertension_medicatn"
                                                                class="form-control">
                                                                <?php if ($patient['hypertension_medicatn1'] == "1") { ?>
                                                                    <option value="<?= $patient['hypertension_medicatn1'] ?>">
                                                                        Yes</option>
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

                                                        <div class="col-sm-4">
                                                            <label class="col-form-label">2. Mention the
                                                                medications:</label>
                                                            <textarea name="hypertension_medicatn_name" rows="4"
                                                                class="form-control"><?= $patient['hypertension_medicatn_name'] ?></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <div class="col-sm-6 bordered">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <label class="col-form-label">3. Any other heart problem apart
                                                                from hypertension?:</label>
                                                            <select name="heart" id="heart" class="form-control">
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

                                                        <div class="col-sm-4">
                                                            <label class="col-form-label">3. Is the patient on
                                                                Medication?</label>
                                                            <select name="heart_medicatn" id="heart_medicatn"
                                                                class="form-control">
                                                                <?php if ($patient['heart_medicatn'] == "1") { ?>
                                                                    <option value="<?= $patient['heart_medicatn'] ?>">Yes
                                                                    </option>
                                                                <?php } elseif ($patient['heart_medicatn'] == "2") { ?>
                                                                    <option value="<?= $patient['heart_medicatn'] ?>">No
                                                                    </option>
                                                                <?php } else { ?>
                                                                    <option value="">Select</option>
                                                                <?php } ?>
                                                                <option value="1">Yes</option>
                                                                <option value="2">No</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-sm-4">
                                                            <label class="col-form-label">3. Mention the
                                                                medications:</label>
                                                            <textarea name="heart_medicatn_name" rows="4"
                                                                class="form-control"><?= $patient['heart_medicatn_name'] ?></textarea>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-sm-6 bordered">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <label class="col-form-label">4. Asthma:</label>
                                                            <select name="asthma" id="asthma" class="form-control">
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

                                                        <div class="col-sm-4">
                                                            <label class="col-form-label">4. Is the patient on
                                                                Medication?</label>
                                                            <select name="asthma_medicatn" id="asthma_medicatn"
                                                                class="form-control">
                                                                <?php if ($patient['asthma_medicatn'] == "1") { ?>
                                                                    <option value="<?= $patient['asthma_medicatn'] ?>">Yes
                                                                    </option>
                                                                <?php } elseif ($patient['asthma_medicatn'] == "2") { ?>
                                                                    <option value="<?= $patient['asthma_medicatn'] ?>">No
                                                                    </option>
                                                                <?php } else { ?>
                                                                    <option value="">Select</option>
                                                                <?php } ?>
                                                                <option value="1">Yes</option>
                                                                <option value="2">No</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-sm-4">
                                                            <label class="col-form-label">4. Mention the
                                                                medications:</label>
                                                            <textarea name="asthma_medicatn_name" rows="4"
                                                                class="form-control"><?= $patient['asthma_medicatn_name'] ?></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <div class="col-sm-6 bordered">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <label class="col-form-label">5. HIV/AIDS:</label>
                                                            <select name="hiv_aids" id="hiv_aids" class="form-control">
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

                                                        <div class="col-sm-4">
                                                            <label class="col-form-label">5. Is the patient on
                                                                Medication?</label>
                                                            <select name="hiv_aids_medicatn" id="hiv_aids_medicatn"
                                                                class="form-control">
                                                                <?php if ($patient['hiv_aids_medicatn'] == "1") { ?>
                                                                    <option value="<?= $patient['hiv_aids_medicatn'] ?>">Yes
                                                                    </option>
                                                                <?php } elseif ($patient['hiv_aids_medicatn'] == "2") { ?>
                                                                    <option value="<?= $patient['hiv_aids_medicatn'] ?>">No
                                                                    </option>
                                                                <?php } else { ?>
                                                                    <option value="">Select</option>
                                                                <?php } ?>
                                                                <option value="1">Yes</option>
                                                                <option value="2">No</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-sm-4">
                                                            <label class="col-form-label">5. Mention the
                                                                medications:</label>
                                                            <textarea name="hiv_aids_medicatn_name" rows="4"
                                                                class="form-control"><?= $patient['hiv_aids_medicatn_name'] ?></textarea>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-sm-6 bordered">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <label class="col-form-label">6. Any other medical
                                                                condition:</label>
                                                            <select name="other_medical" id="other_medical"
                                                                class="form-control">
                                                                <?php if ($patient['other_medical'] == "1") { ?>
                                                                    <option value="<?= $patient['other_medical'] ?>">Yes
                                                                    </option>
                                                                <?php } elseif ($patient['other_medical'] == "2") { ?>
                                                                    <option value="<?= $patient['other_medical'] ?>">No</option>
                                                                <?php } else { ?>
                                                                    <option value="">Select</option>
                                                                <?php } ?>
                                                                <option value="1">Yes</option>
                                                                <option value="2">No</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-sm-4">
                                                            <label class="col-form-label">6. Is the patient on
                                                                Medication?</label>
                                                            <select name="other_medical_medicatn"
                                                                id="other_medical_medicatn" class="form-control">
                                                                <?php if ($patient['other_medical_medicatn'] == "1") { ?>
                                                                    <option value="<?= $patient['other_medical_medicatn'] ?>">
                                                                        Yes</option>
                                                                <?php } elseif ($patient['other_medical_medicatn'] == "2") { ?>
                                                                    <option value="<?= $patient['other_medical_medicatn'] ?>">No
                                                                    </option>
                                                                <?php } else { ?>
                                                                    <option value="">Select</option>
                                                                <?php } ?>
                                                                <option value="1">Yes</option>
                                                                <option value="2">No</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-sm-4">
                                                            <label class="col-form-label">6. Mention the
                                                                medications:</label>
                                                            <textarea name="other_medicatn_name" rows="4"
                                                                class="form-control"><?= $patient['other_medicatn_name'] ?></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card card-primary">
                                                <div class="card-header">
                                                    <h4>USE OF HERBAL MEDICINES</h4>
                                                    <h3 class="card-title">NIMREGENIN Herbal preparation</h3>
                                                </div>
                                                <hr>

                                                <div class="form-group row">
                                                    <label class="col-sm-3 col-form-label">8. Are you using NIMREGENIN
                                                        herbal
                                                        preparation?:</label>
                                                    <div class="col-sm-9">
                                                        <select name="nimregenin_herbal" id="nimregenin_herbal"
                                                            class="form-control" required>
                                                            <?php if ($patient['nimregenin_herbal'] == "1") { ?>
                                                                <option value="<?= $patient['nimregenin_herbal'] ?>">Yes
                                                                </option>
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

                                                <hr>
                                                <!-- Add Modal -->
                                                <div class="modal fade" id="addNimregenin" tabindex="-1" role="dialog">
                                                    <div class="modal-dialog modal-lg" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Add New Record</h5>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form method="post">
                                                                    <div class="form-group row">
                                                                        <label class="col-sm-3 col-form-label">Type of
                                                                            NIMREGENIN</label>
                                                                        <div class="col-sm-9">
                                                                            <input type="text" name="nimregenin_preparation"
                                                                                value="<?= $patient['nimregenin_preparation'] ?>"
                                                                                class="form-control"
                                                                                placeholder="Type of NIMREGENIN">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <label class="col-sm-3 col-form-label">Start
                                                                            Date</label>
                                                                        <div class="col-sm-9">
                                                                            <input type="date" name="nimregenin_start"
                                                                                value="<?= $patient['nimregenin_start'] ?>"
                                                                                class="form-control"
                                                                                placeholder="Start Date">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <label
                                                                            class="col-sm-3 col-form-label">Ongoing?</label>
                                                                        <div class="col-sm-9">
                                                                            <select name="nimregenin_ongoing"
                                                                                class="form-control">
                                                                                <option value="">Select</option>
                                                                                <option value="1">Yes</option>
                                                                                <option value="2">No</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <label class="col-sm-3 col-form-label">End
                                                                            Date</label>
                                                                        <div class="col-sm-9">
                                                                            <input type="date" name="nimregenin_end"
                                                                                value="<?= $patient['nimregenin_end'] ?>"
                                                                                class="form-control" placeholder="End Date">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <label class="col-sm-3 col-form-label">Dose
                                                                            (mls)</label>
                                                                        <div class="col-sm-9">
                                                                            <input type="text" name="nimregenin_dose"
                                                                                value="<?= $patient['nimregenin_dose'] ?>"
                                                                                class="form-control"
                                                                                placeholder="Dose (mls)">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <label class="col-sm-3 col-form-label">Frequency
                                                                            (per day)</label>
                                                                        <div class="col-sm-9">
                                                                            <input type="text" name="nimregenin_frequency"
                                                                                value="<?= $patient['nimregenin_frequency'] ?>"
                                                                                class="form-control"
                                                                                placeholder="Frequency (per day)">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <label
                                                                            class="col-sm-3 col-form-label">Remarks</label>
                                                                        <div class="col-sm-9">
                                                                            <input type="text" name="nimregenin_remarks"
                                                                                value="<?= $patient['nimregenin_remarks'] ?>"
                                                                                class="form-control" placeholder="Remarks">
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="nimregenin_herbal"
                                                                        value="<?= $patient['nimregenin_herbal'] ?>">
                                                                    <input type="hidden" name="crf1_cmpltd_date"
                                                                        value="<?= $patient['crf1_cmpltd_date'] ?>">
                                                                    <input type="submit" name="add_nimregenin"
                                                                        class="btn btn-success mt-2l" value="Save">
                                                                    <button type="button" class="btn btn-secondary mt-2"
                                                                        data-dismiss="modal">Cancel</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <button type="button" class="btn btn-info mb-3" data-toggle="modal"
                                                        data-target="#addNimregenin">
                                                        <i class="fas fa-plus"></i> Add New Nimregenin
                                                    </button>
                                                    <hr>
                                                    <table class="table table-bordered" id="nimregenin_table">
                                                        <thead>
                                                            <tr>
                                                                <th>Type of NIMREGENIN</th>
                                                                <th>Start Date</th>
                                                                <th>Ongoing ?</th>
                                                                <th>End Date</th>
                                                                <th>Dose</th>
                                                                <th>Frequency</th>
                                                                <th>Remarks</th>
                                                                <th>Actions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $x = 1;
                                                            foreach ($override->getNews('nimregenin', 'patient_id', $_GET['cid'], 'status', 1) as $nimregenin) {
                                                                ?>
                                                                <tr>
                                                                    <td><?= $nimregenin['nimregenin_preparation'] ?></td>
                                                                    <td><?= $nimregenin['nimregenin_start'] ?></td>
                                                                    <td>
                                                                        <?php if ($nimregenin['nimregenin_ongoing'] == 1) {
                                                                            echo "Yes";
                                                                        } elseif ($nimregenin['nimregenin_ongoing'] == 2) {
                                                                            echo "No";
                                                                        } else {
                                                                            echo " ";
                                                                        } ?>
                                                                    </td>
                                                                    <td><?= $nimregenin['nimregenin_end'] ?></td>
                                                                    <td><?= $nimregenin['nimregenin_dose'] ?></td>
                                                                    <td><?= $nimregenin['nimregenin_frequency'] ?></td>
                                                                    <td><?= $nimregenin['nimregenin_remarks'] ?></td>
                                                                    <td>
                                                                        <button type="button" class="btn btn-success mb-3"
                                                                            data-toggle="modal"
                                                                            data-target="#updateNimregenin<?= $nimregenin['id'] ?>">
                                                                            <i class="fas fa-edit"></i> Update
                                                                        </button>
                                                                        <hr>
                                                                        <button type="button" class="btn btn-danger mb-3"
                                                                            data-toggle="modal"
                                                                            data-target="#deleteNimregenin<?= $nimregenin['id'] ?>">
                                                                            <i class="fas fa-trash"></i> Delete
                                                                        </button>
                                                                    </td>
                                                                </tr>

                                                                <!-- Update Modal -->
                                                                <div class="modal fade"
                                                                    id="updateNimregenin<?= $nimregenin['id'] ?>" tabindex="-1"
                                                                    role="dialog">
                                                                    <div class="modal-dialog modal-lg" role="document">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title">Update Record</h5>
                                                                                <button type="button" class="close"
                                                                                    data-dismiss="modal">&times;</button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <form method="post">
                                                                                    <div class="form-group row">
                                                                                        <label
                                                                                            class="col-sm-3 col-form-label">Type
                                                                                            of
                                                                                            NIMREGENIN</label>
                                                                                        <div class="col-sm-9">
                                                                                            <input type="text"
                                                                                                name="nimregenin_preparation"
                                                                                                value="<?= $nimregenin['nimregenin_preparation'] ?>"
                                                                                                class="form-control"
                                                                                                placeholder="Type of NIMREGENIN">
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="form-group row">
                                                                                        <label
                                                                                            class="col-sm-3 col-form-label">Start
                                                                                            Date</label>
                                                                                        <div class="col-sm-9">
                                                                                            <input type="date"
                                                                                                name="nimregenin_start"
                                                                                                value="<?= $nimregenin['nimregenin_start'] ?>"
                                                                                                class="form-control"
                                                                                                placeholder="Start Date">
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="form-group row">
                                                                                        <label
                                                                                            class="col-sm-3 col-form-label">Ongoing?</label>
                                                                                        <div class="col-sm-9">
                                                                                            <select name="nimregenin_ongoing"
                                                                                                class="form-control">
                                                                                                <option value="">Select</option>
                                                                                                <option
                                                                                                    value="<?= $nimregenin['nimregenin_ongoing'] ?>">
                                                                                                    <?php if ($nimregenin['nimregenin_ongoing'] == 1) {
                                                                                                        echo "Yes";
                                                                                                    } elseif ($nimregenin['nimregenin_ongoing'] == 2) {
                                                                                                        echo "No";
                                                                                                    } else {
                                                                                                        echo " ";
                                                                                                    } ?>
                                                                                                </option>
                                                                                                <option value="1">Yes</option>
                                                                                                <option value="2">No</option>
                                                                                            </select>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="form-group row">
                                                                                        <label
                                                                                            class="col-sm-3 col-form-label">End
                                                                                            Date</label>
                                                                                        <div class="col-sm-9">
                                                                                            <input type="date"
                                                                                                name="nimregenin_end"
                                                                                                value="<?= $nimregenin['nimregenin_end'] ?>"
                                                                                                class="form-control"
                                                                                                placeholder="End Date">
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="form-group row">
                                                                                        <label
                                                                                            class="col-sm-3 col-form-label">Dose
                                                                                            (mls)</label>
                                                                                        <div class="col-sm-9">
                                                                                            <input type="text"
                                                                                                name="nimregenin_dose"
                                                                                                value="<?= $nimregenin['nimregenin_dose'] ?>"
                                                                                                class="form-control"
                                                                                                placeholder="Dose (mls)">
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="form-group row">
                                                                                        <label
                                                                                            class="col-sm-3 col-form-label">Frequency
                                                                                            (per day)</label>
                                                                                        <div class="col-sm-9">
                                                                                            <input type="text"
                                                                                                name="nimregenin_frequency"
                                                                                                value="<?= $nimregenin['nimregenin_frequency'] ?>"
                                                                                                class="form-control"
                                                                                                placeholder="Frequency (per day)">
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="form-group row">
                                                                                        <label
                                                                                            class="col-sm-3 col-form-label">Remarks</label>
                                                                                        <div class="col-sm-9">
                                                                                            <input type="text"
                                                                                                name="nimregenin_remarks"
                                                                                                value="<?= $nimregenin['nimregenin_remarks'] ?>"
                                                                                                class="form-control"
                                                                                                placeholder="Remarks">
                                                                                        </div>
                                                                                    </div>
                                                                                    <input type="hidden" name="nimregenin_id"
                                                                                        value="<?= $nimregenin['id'] ?>">
                                                                                    <input type="hidden"
                                                                                        name="nimregenin_herbal"
                                                                                        value="<?= $patient['nimregenin_herbal'] ?>">
                                                                                    <input type="hidden" name="crf1_cmpltd_date"
                                                                                        value="<?= $patient['crf1_cmpltd_date'] ?>">
                                                                                    <input type="submit"
                                                                                        name="update_nimregenin"
                                                                                        class="btn btn-success mt-2l"
                                                                                        value="Save">
                                                                                    <button type="button"
                                                                                        class="btn btn-secondary mt-2"
                                                                                        data-dismiss="modal">Cancel</button>
                                                                                </form>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- Delete Modal -->
                                                                <div class="modal fade"
                                                                    id="deleteNimregenin<?= $nimregenin['id'] ?>" tabindex="-1"
                                                                    role="dialog">
                                                                    <div class="modal-dialog" role="document">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title">Delete Record</h5>
                                                                                <button type="button" class="close"
                                                                                    data-dismiss="modal">&times;</button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <p>Are you sure you want to delete this record?
                                                                                </p>
                                                                                <form method="post">
                                                                                    <input type="hidden" name="nimregenin_id"
                                                                                        value="<?= $nimregenin['id'] ?>">
                                                                                    <input type="hidden"
                                                                                        name="nimregenin_herbal"
                                                                                        value="<?= $patient['nimregenin_herbal'] ?>">
                                                                                    <input type="submit"
                                                                                        name="delete_nimregenin"
                                                                                        class="btn btn-danger mt-2l"
                                                                                        value="Delete">
                                                                                    <button type="button"
                                                                                        class="btn btn-secondary mt-2"
                                                                                        data-dismiss="modal">Cancel</button>
                                                                                </form>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <?php
                                                                $x++;
                                                            }
                                                            ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="card card-primary">
                                                <div class="card-header">
                                                    <h3 class="card-title">Other Herbal preparation</h3>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-group row">
                                                        <label class="col-sm-3 col-form-label">8. Are you using any other
                                                            herbal
                                                            preparation?:</label>
                                                        <div class="col-sm-9">
                                                            <select name="other_herbal" id="other_herbal"
                                                                class="form-control" required>
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

                                                    <div class="modal fade" id="addOtherHerbal" tabindex="-1" role="dialog">
                                                        <div class="modal-dialog modal-lg" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Add New Other Herbal
                                                                        Preparation</h5>
                                                                    <button type="button" class="close"
                                                                        data-dismiss="modal">&times;</button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <form method="post">
                                                                        <div class="form-group row">
                                                                            <label class="col-sm-3 col-form-label">Type
                                                                                of Herbal Preparation</label>
                                                                            <div class="col-sm-9">
                                                                                <input type="text" name="herbal_preparation"
                                                                                    class="form-control"
                                                                                    placeholder="Type of Herbal Preparation">
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group row">
                                                                            <label class="col-sm-3 col-form-label">Start
                                                                                Date</label>
                                                                            <div class="col-sm-9">
                                                                                <input type="date" name="herbal_start"
                                                                                    class="form-control"
                                                                                    placeholder="Start Date">
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group row">
                                                                            <label
                                                                                class="col-sm-3 col-form-label">Ongoing?</label>
                                                                            <div class="col-sm-9">
                                                                                <select name="herbal_ongoing"
                                                                                    class="form-control">
                                                                                    <option value="">Select</option>
                                                                                    <option value="1">Yes</option>
                                                                                    <option value="2">No</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group row">
                                                                            <label class="col-sm-3 col-form-label">End
                                                                                Date</label>
                                                                            <div class="col-sm-9">
                                                                                <input type="date" name="herbal_end"
                                                                                    class="form-control"
                                                                                    placeholder="End Date">
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group row">
                                                                            <label class="col-sm-3 col-form-label">Dose
                                                                                (mls)</label>
                                                                            <div class="col-sm-9">
                                                                                <input type="text" name="herbal_dose"
                                                                                    class="form-control"
                                                                                    placeholder="Dose (mls)">
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group row">
                                                                            <label class="col-sm-3 col-form-label">Frequency
                                                                                (per day)</label>
                                                                            <div class="col-sm-9">
                                                                                <input type="text" name="herbal_frequency"
                                                                                    class="form-control"
                                                                                    placeholder="Frequency (per day)">
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group row">
                                                                            <label
                                                                                class="col-sm-3 col-form-label">Remarks</label>
                                                                            <div class="col-sm-9">
                                                                                <input type="text" name="herbal_remarks"
                                                                                    class="form-control"
                                                                                    placeholder="Remarks">
                                                                            </div>
                                                                        </div>
                                                                        <input type="hidden" name="other_herbal"
                                                                            value="<?= $patient['other_herbal'] ?>">
                                                                        <input type="submit" name="add_other_herbal"
                                                                            class="btn btn-success mt-2" value="Save" />
                                                                        <button type="button" class="btn btn-secondary mt-2"
                                                                            data-dismiss="modal">Cancel</button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="card-body">
                                                        <button type="button" class="btn btn-info mb-3" data-toggle="modal"
                                                            data-target="#addOtherHerbal">
                                                            <i class="fas fa-plus"></i> Add New Other Herbal Preparation
                                                        </button>
                                                        <hr>
                                                        <table class="table table-bordered" id="other_herbal_table">
                                                            <thead>
                                                                <tr>
                                                                    <th>Type of Herbal Preparation</th>
                                                                    <th>Start Date</th>
                                                                    <th>Ongoing ?</th>
                                                                    <th>End Date</th>
                                                                    <th>Dose</th>
                                                                    <th>Frequency</th>
                                                                    <th>Remarks</th>
                                                                    <th>Actions</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $x = 1;
                                                                foreach ($override->getNews('herbal_treatment', 'patient_id', $_GET['cid'], 'status', 1) as $herbal) {
                                                                    ?>
                                                                    <tr>
                                                                        <td><?= $herbal['herbal_preparation'] ?></td>
                                                                        <td><?= $herbal['herbal_start'] ?></td>
                                                                        <td>
                                                                            <?php if ($herbal['herbal_ongoing'] == 1) {
                                                                                echo "Yes";
                                                                            } elseif ($herbal['herbal_ongoing'] == 2) {
                                                                                echo "No";
                                                                            } else {
                                                                                echo " ";
                                                                            } ?>
                                                                        </td>
                                                                        <td><?= $herbal['herbal_end'] ?></td>
                                                                        <td><?= $herbal['herbal_dose'] ?></td>
                                                                        <td><?= $herbal['herbal_frequency'] ?></td>
                                                                        <td><?= $herbal['herbal_remarks'] ?></td>
                                                                        <td>
                                                                            <button type="button" class="btn btn-success mb-3"
                                                                                data-toggle="modal"
                                                                                data-target="#updateOtherHerbal<?= $herbal['id'] ?>">
                                                                                <i class="fas fa-edit"></i> Update
                                                                            </button>
                                                                            <hr>
                                                                            <button type="button" class="btn btn-danger mb-3"
                                                                                data-toggle="modal"
                                                                                data-target="#deleteOtherHerbal<?= $herbal['id'] ?>">
                                                                                <i class="fas fa-trash"></i> Delete
                                                                            </button>
                                                                        </td>
                                                                    </tr>

                                                                    <!-- Update Modal -->
                                                                    <div class="modal fade"
                                                                        id="updateOtherHerbal<?= $herbal['id'] ?>" tabindex="-1"
                                                                        role="dialog">
                                                                        <div class="modal-dialog modal-lg" role="document">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h5 class="modal-title">Update Other
                                                                                        Herbal Preparation</h5>
                                                                                    <button type="button" class="close"
                                                                                        data-dismiss="modal">&times;</button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <form method="post">
                                                                                        <div class="form-group row">
                                                                                            <label
                                                                                                class="col-sm-3 col-form-label">Type
                                                                                                of Herbal
                                                                                                Preparation</label>
                                                                                            <div class="col-sm-9">
                                                                                                <input type="text"
                                                                                                    name="herbal_preparation"
                                                                                                    value="<?= $herbal['herbal_preparation'] ?>"
                                                                                                    class="form-control">
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="form-group row">
                                                                                            <label
                                                                                                class="col-sm-3 col-form-label">Start
                                                                                                Date</label>
                                                                                            <div class="col-sm-9">
                                                                                                <input type="date"
                                                                                                    name="herbal_start"
                                                                                                    value="<?= $herbal['herbal_start'] ?>"
                                                                                                    class="form-control">
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="form-group row">
                                                                                            <label
                                                                                                class="col-sm-3 col-form-label">Ongoing?</label>
                                                                                            <div class="col-sm-9">
                                                                                                <select name="herbal_ongoing"
                                                                                                    class="form-control">
                                                                                                    <option value="">Select
                                                                                                    </option>
                                                                                                    <option value="1"
                                                                                                        <?= $herbal['herbal_ongoing'] == 1 ? 'selected' : '' ?>>Yes
                                                                                                    </option>
                                                                                                    <option value="2"
                                                                                                        <?= $herbal['herbal_ongoing'] == 2 ? 'selected' : '' ?>>No
                                                                                                    </option>
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="form-group row">
                                                                                            <label
                                                                                                class="col-sm-3 col-form-label">End
                                                                                                Date</label>
                                                                                            <div class="col-sm-9">
                                                                                                <input type="date"
                                                                                                    name="herbal_end"
                                                                                                    value="<?= $herbal['herbal_end'] ?>"
                                                                                                    class="form-control">
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="form-group row">
                                                                                            <label
                                                                                                class="col-sm-3 col-form-label">Dose
                                                                                                (mls)</label>
                                                                                            <div class="col-sm-9">
                                                                                                <input type="text"
                                                                                                    name="herbal_dose"
                                                                                                    value="<?= $herbal['herbal_dose'] ?>"
                                                                                                    class="form-control">
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="form-group row">
                                                                                            <label
                                                                                                class="col-sm-3 col-form-label">Frequency
                                                                                                (per day)</label>
                                                                                            <div class="col-sm-9">
                                                                                                <input type="text"
                                                                                                    name="herbal_frequency"
                                                                                                    value="<?= $herbal['herbal_frequency'] ?>"
                                                                                                    class="form-control">
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="form-group row">
                                                                                            <label
                                                                                                class="col-sm-3 col-form-label">Remarks</label>
                                                                                            <div class="col-sm-9">
                                                                                                <input type="text"
                                                                                                    name="herbal_remarks"
                                                                                                    value="<?= $herbal['herbal_remarks'] ?>"
                                                                                                    class="form-control">
                                                                                            </div>
                                                                                        </div>
                                                                                        <input type="hidden" name="herbal_id"
                                                                                            value="<?= $herbal['id'] ?>">
                                                                                        <input type="hidden" name="other_herbal"
                                                                                            value="<?= $patient['other_herbal'] ?>">
                                                                                        <input type="hidden"
                                                                                            name="crf1_cmpltd_date"
                                                                                            value="<?= $patient['crf1_cmpltd_date'] ?>">
                                                                                        <input type="submit"
                                                                                            name="update_other_herbal"
                                                                                            class="btn btn-success mt-2"
                                                                                            value="Save" />
                                                                                        <button type="button"
                                                                                            class="btn btn-secondary mt-2"
                                                                                            data-dismiss="modal">Cancel</button>
                                                                                    </form>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Delete Modal -->
                                                                    <div class="modal fade"
                                                                        id="deleteOtherHerbal<?= $herbal['id'] ?>" tabindex="-1"
                                                                        role="dialog">
                                                                        <div class="modal-dialog" role="document">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h5 class="modal-title">Delete Other
                                                                                        Herbal Preparation</h5>
                                                                                    <button type="button" class="close"
                                                                                        data-dismiss="modal">&times;</button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <p>Are you sure you want to delete this
                                                                                        record?</p>
                                                                                    <form method="post">
                                                                                        <input type="hidden" name="herbal_id"
                                                                                            value="<?= $herbal['id'] ?>">
                                                                                        <input type="submit"
                                                                                            name="delete_other_herbal"
                                                                                            class="btn btn-danger mt-2"
                                                                                            value="Save" />
                                                                                        <button type="button"
                                                                                            class="btn btn-secondary"
                                                                                            data-dismiss="modal">Cancel</button>
                                                                                    </form>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <?php
                                                                    $x++;
                                                                }
                                                                ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="card card-primary">
                                                <div class="card-header">
                                                    <hr>
                                                    <h2 class="card-title">STANDARD OF CARE TREATMENT</h2>
                                                    <h3 class="card-title">Provide lists of treatments and supportive care
                                                        given to the
                                                        cancer patient</h3>
                                                    <h4>(To be retrieved from patient file/medical personnel)</h4>
                                                    <h5>(all medication should be in generic names)</h5>
                                                    <hr>
                                                </div>
                                                <div class="card-body">
                                                    <hr>
                                                    <div class="card card-primary">
                                                        <div class="card-header">
                                                            <h3 class="card-title">1. Radiotherapy</h3>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="radiotherapy_performed">1. Is there any Radiotherapy
                                                                performed?</label>
                                                            <select name="radiotherapy_performed"
                                                                id="radiotherapy_performed" class="form-control" required>
                                                                <?php if ($patient['radiotherapy_performed'] == "1") { ?>
                                                                    <option value="<?= $patient['radiotherapy_performed'] ?>">
                                                                        Yes
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

                                                        <!-- Add Modal -->
                                                        <div class="modal fade" id="addRadiotherapy" tabindex="-1"
                                                            role="dialog">
                                                            <div class="modal-dialog modal-lg" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Add New Radiotherapy</h5>
                                                                        <button type="button" class="close"
                                                                            data-dismiss="modal">&times;</button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <form method="post">
                                                                            <div class="form-group row">
                                                                                <label class="col-sm-3 col-form-label">Type
                                                                                    of Radiotherapy</label>
                                                                                <div class="col-sm-9">
                                                                                    <input type="text" name="radiotherapy"
                                                                                        class="form-control"
                                                                                        placeholder="Type of Radiotherapy">
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group row">
                                                                                <label class="col-sm-3 col-form-label">Start
                                                                                    Date</label>
                                                                                <div class="col-sm-9">
                                                                                    <input type="date"
                                                                                        name="radiotherapy_start"
                                                                                        class="form-control"
                                                                                        placeholder="Start Date">
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group row">
                                                                                <label
                                                                                    class="col-sm-3 col-form-label">Ongoing?</label>
                                                                                <div class="col-sm-9">
                                                                                    <select name="radiotherapy_ongoing"
                                                                                        class="form-control">
                                                                                        <option value="">Select</option>
                                                                                        <option value="1">Yes</option>
                                                                                        <option value="2">No</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group row">
                                                                                <label class="col-sm-3 col-form-label">End
                                                                                    Date</label>
                                                                                <div class="col-sm-9">
                                                                                    <input type="date"
                                                                                        name="radiotherapy_end"
                                                                                        class="form-control"
                                                                                        placeholder="End Date">
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group row">
                                                                                <label class="col-sm-3 col-form-label">Dose
                                                                                    (Grays)</label>
                                                                                <div class="col-sm-9">
                                                                                    <input type="text"
                                                                                        name="radiotherapy_dose"
                                                                                        class="form-control"
                                                                                        placeholder="Dose (Grays)">
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group row">
                                                                                <label
                                                                                    class="col-sm-3 col-form-label">Frequency
                                                                                    (numbers)</label>
                                                                                <div class="col-sm-9">
                                                                                    <input type="text"
                                                                                        name="radiotherapy_frequecy"
                                                                                        class="form-control"
                                                                                        placeholder="Frequency (numbers)">
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group row">
                                                                                <label
                                                                                    class="col-sm-3 col-form-label">Remarks</label>
                                                                                <div class="col-sm-9">
                                                                                    <input type="text"
                                                                                        name="radiotherapy_remarks"
                                                                                        class="form-control"
                                                                                        placeholder="Remarks">
                                                                                </div>
                                                                            </div>
                                                                            <input type="hidden" name="other_herbal"
                                                                                value="<?= $patient['other_herbal'] ?>">
                                                                            <input type="hidden"
                                                                                name="radiotherapy_performed"
                                                                                value="<?= $patient['radiotherapy_performed'] ?>">
                                                                            <input type="submit" name="add_radiotherapy"
                                                                                class="btn btn-success mt-2" value="Save">
                                                                            <button type="button"
                                                                                class="btn btn-secondary mt-2"
                                                                                data-dismiss="modal">Cancel</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="card card-primary">
                                                            <div class="card-header">
                                                                <h3 class="card-title">Radiotherapy</h3>
                                                                <div class="card-tools">
                                                                    <button type="button" class="btn btn-success btn-sm"
                                                                        data-toggle="modal" data-target="#addRadiotherapy">
                                                                        <i class="fas fa-plus"></i> Add New
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <div class="card-body">
                                                                <table class="table table-bordered" id="radiotherapy_table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Type of Radiotherapy</th>
                                                                            <th>Start Date</th>
                                                                            <th>Ongoing ?</th>
                                                                            <th>End Date</th>
                                                                            <th>Dose</th>
                                                                            <th>Frequency</th>
                                                                            <th>Remarks</th>
                                                                            <th>Actions</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php
                                                                        $x = 1;
                                                                        foreach ($override->get1('radiotherapy', 'patient_id', $_GET['cid'], 'status', 1) as $radiotherapy) {
                                                                            ?>
                                                                            <tr>
                                                                                <td><?= $radiotherapy['radiotherapy'] ?></td>
                                                                                <td><?= $radiotherapy['radiotherapy_start'] ?>
                                                                                </td>
                                                                                <td>
                                                                                    <?= $radiotherapy['radiotherapy_ongoing'] == 1 ? "Yes" : "No" ?>
                                                                                </td>
                                                                                <td><?= $radiotherapy['radiotherapy_end'] ?>
                                                                                </td>
                                                                                <td><?= $radiotherapy['radiotherapy_dose'] ?>
                                                                                </td>
                                                                                <td><?= $radiotherapy['radiotherapy_frequecy'] ?>
                                                                                </td>
                                                                                <td><?= $radiotherapy['radiotherapy_remarks'] ?>
                                                                                </td>
                                                                                <td>
                                                                                    <button type="button"
                                                                                        class="btn btn-success btn-sm"
                                                                                        data-toggle="modal"
                                                                                        data-target="#updateRadiotherapy<?= $radiotherapy['id'] ?>">
                                                                                        <i class="fas fa-edit"></i> Update
                                                                                    </button>
                                                                                    <button type="button"
                                                                                        class="btn btn-danger btn-sm"
                                                                                        data-toggle="modal"
                                                                                        data-target="#deleteRadiotherapy<?= $radiotherapy['id'] ?>">
                                                                                        <i class="fas fa-trash"></i> Delete
                                                                                    </button>
                                                                                </td>
                                                                            </tr>

                                                                            <!-- Update Modal -->
                                                                            <div class="modal fade"
                                                                                id="updateRadiotherapy<?= $radiotherapy['id'] ?>"
                                                                                tabindex="-1" role="dialog">
                                                                                <div class="modal-dialog modal-lg"
                                                                                    role="document">
                                                                                    <div class="modal-content">
                                                                                        <div class="modal-header">
                                                                                            <h5 class="modal-title">Update
                                                                                                Radiotherapy</h5>
                                                                                            <button type="button" class="close"
                                                                                                data-dismiss="modal">&times;</button>
                                                                                        </div>
                                                                                        <div class="modal-body">
                                                                                            <form method="post">
                                                                                                <div class="form-group row">
                                                                                                    <label
                                                                                                        class="col-sm-3 col-form-label">Type
                                                                                                        of Radiotherapy</label>
                                                                                                    <div class="col-sm-9">
                                                                                                        <input type="text"
                                                                                                            name="radiotherapy"
                                                                                                            value="<?= $radiotherapy['radiotherapy'] ?>"
                                                                                                            class="form-control">
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="form-group row">
                                                                                                    <label
                                                                                                        class="col-sm-3 col-form-label">Start
                                                                                                        Date</label>
                                                                                                    <div class="col-sm-9">
                                                                                                        <input type="date"
                                                                                                            name="radiotherapy_start"
                                                                                                            value="<?= $radiotherapy['radiotherapy_start'] ?>"
                                                                                                            class="form-control">
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="form-group row">
                                                                                                    <label
                                                                                                        class="col-sm-3 col-form-label">Ongoing?</label>
                                                                                                    <div class="col-sm-9">
                                                                                                        <select
                                                                                                            name="radiotherapy_ongoing"
                                                                                                            class="form-control">
                                                                                                            <option value="1"
                                                                                                                <?= $radiotherapy['radiotherapy_ongoing'] == 1 ? 'selected' : '' ?>>Yes
                                                                                                            </option>
                                                                                                            <option value="2"
                                                                                                                <?= $radiotherapy['radiotherapy_ongoing'] == 2 ? 'selected' : '' ?>>No
                                                                                                            </option>
                                                                                                        </select>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="form-group row">
                                                                                                    <label
                                                                                                        class="col-sm-3 col-form-label">End
                                                                                                        Date</label>
                                                                                                    <div class="col-sm-9">
                                                                                                        <input type="date"
                                                                                                            name="radiotherapy_end"
                                                                                                            value="<?= $radiotherapy['radiotherapy_end'] ?>"
                                                                                                            class="form-control">
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="form-group row">
                                                                                                    <label
                                                                                                        class="col-sm-3 col-form-label">Dose
                                                                                                        (Grays)</label>
                                                                                                    <div class="col-sm-9">
                                                                                                        <input type="text"
                                                                                                            name="radiotherapy_dose"
                                                                                                            value="<?= $radiotherapy['radiotherapy_dose'] ?>"
                                                                                                            class="form-control">
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="form-group row">
                                                                                                    <label
                                                                                                        class="col-sm-3 col-form-label">Frequency
                                                                                                        (numbers)</label>
                                                                                                    <div class="col-sm-9">
                                                                                                        <input type="text"
                                                                                                            name="radiotherapy_frequecy"
                                                                                                            value="<?= $radiotherapy['radiotherapy_frequecy'] ?>"
                                                                                                            class="form-control">
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="form-group row">
                                                                                                    <label
                                                                                                        class="col-sm-3 col-form-label">Remarks</label>
                                                                                                    <div class="col-sm-9">
                                                                                                        <input type="text"
                                                                                                            name="radiotherapy_remarks"
                                                                                                            value="<?= $radiotherapy['radiotherapy_remarks'] ?>"
                                                                                                            class="form-control">
                                                                                                    </div>
                                                                                                </div>
                                                                                                <input type="hidden"
                                                                                                    name="radiotherapy_id"
                                                                                                    value="<?= $radiotherapy['id'] ?>">
                                                                                                <input type="hidden"
                                                                                                    name="other_herbal"
                                                                                                    value="<?= $patient['other_herbal'] ?>">
                                                                                                <input type="hidden"
                                                                                                    name="radiotherapy_performed"
                                                                                                    value="<?= $patient['radiotherapy_performed'] ?>">
                                                                                                <input type="submit"
                                                                                                    name="update_radiotherapy"
                                                                                                    class="btn btn-success mt-2"
                                                                                                    value="Save">
                                                                                                <button type="button"
                                                                                                    class="btn btn-secondary mt-2"
                                                                                                    data-dismiss="modal">Cancel</button>
                                                                                            </form>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <!-- Delete Modal -->
                                                                            <div class="modal fade"
                                                                                id="deleteRadiotherapy<?= $radiotherapy['id'] ?>"
                                                                                tabindex="-1" role="dialog">
                                                                                <div class="modal-dialog" role="document">
                                                                                    <div class="modal-content">
                                                                                        <div class="modal-header">
                                                                                            <h5 class="modal-title">Delete
                                                                                                Radiotherapy</h5>
                                                                                            <button type="button" class="close"
                                                                                                data-dismiss="modal">&times;</button>
                                                                                        </div>
                                                                                        <div class="modal-body">
                                                                                            <p>Are you sure you want to delete
                                                                                                this record?</p>
                                                                                            <form method="post">
                                                                                                <input type="hidden"
                                                                                                    name="radiotherapy_id"
                                                                                                    value="<?= $radiotherapy['id'] ?>">
                                                                                                <input type="submit"
                                                                                                    name="delete_radiotherapy"
                                                                                                    class="btn btn-danger mt-2"
                                                                                                    value="Delete">
                                                                                                <button type="button"
                                                                                                    class="btn btn-secondary mt-2"
                                                                                                    data-dismiss="modal">Cancel</button>
                                                                                            </form>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <?php
                                                                            $x++;
                                                                        }
                                                                        ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr>

                                                    <!-- Add Modal -->
                                                    <div class="modal fade" id="addChemotherapy" tabindex="-1"
                                                        role="dialog">
                                                        <div class="modal-dialog modal-lg" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Add New Chemotherapy</h5>
                                                                    <button type="button" class="close"
                                                                        data-dismiss="modal">&times;</button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <form method="post">
                                                                        <div class="form-group row">
                                                                            <label class="col-sm-3 col-form-label">Type of
                                                                                Chemotherapy</label>
                                                                            <div class="col-sm-9">
                                                                                <input type="text" name="chemotherapy"
                                                                                    class="form-control"
                                                                                    placeholder="Type of Chemotherapy">
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group row">
                                                                            <label class="col-sm-3 col-form-label">Start
                                                                                Date</label>
                                                                            <div class="col-sm-9">
                                                                                <input type="date" name="chemotherapy_start"
                                                                                    class="form-control"
                                                                                    placeholder="Start Date">
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group row">
                                                                            <label
                                                                                class="col-sm-3 col-form-label">Ongoing?</label>
                                                                            <div class="col-sm-9">
                                                                                <select name="chemotherapy_ongoing"
                                                                                    class="form-control">
                                                                                    <option value="">Select</option>
                                                                                    <option value="1">Yes</option>
                                                                                    <option value="2">No</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group row">
                                                                            <label class="col-sm-3 col-form-label">End
                                                                                Date</label>
                                                                            <div class="col-sm-9">
                                                                                <input type="date" name="chemotherapy_end"
                                                                                    class="form-control"
                                                                                    placeholder="End Date">
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group row">
                                                                            <label class="col-sm-3 col-form-label">Dose
                                                                                (mg)</label>
                                                                            <div class="col-sm-9">
                                                                                <input type="text" name="chemotherapy_dose"
                                                                                    class="form-control"
                                                                                    placeholder="Dose (mg)">
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group row">
                                                                            <label class="col-sm-3 col-form-label">Frequency
                                                                                (numbers)</label>
                                                                            <div class="col-sm-9">
                                                                                <input type="text"
                                                                                    name="chemotherapy_frequecy"
                                                                                    class="form-control"
                                                                                    placeholder="Frequency (numbers)">
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group row">
                                                                            <label
                                                                                class="col-sm-3 col-form-label">Remarks</label>
                                                                            <div class="col-sm-9">
                                                                                <input type="text"
                                                                                    name="chemotherapy_remarks"
                                                                                    class="form-control"
                                                                                    placeholder="Remarks">
                                                                            </div>
                                                                        </div>
                                                                        <input type="hidden" name="other_herbal"
                                                                            value="<?= $patient['other_herbal'] ?>">
                                                                        <input type="hidden" name="chemotherapy_performed"
                                                                            value="<?= $patient['chemotherapy_performed'] ?>">
                                                                        <input type="submit" name="add_chemotherapy"
                                                                            class="btn btn-success mt-2" value="Save">
                                                                        <button type="button" class="btn btn-secondary mt-2"
                                                                            data-dismiss="modal">Cancel</button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="card card-primary">
                                                        <div class="card-header">
                                                            <h3 class="card-title">2. Chemotherapy</h3>
                                                            <div class="card-tools">
                                                                <button type="button" class="btn btn-success btn-sm"
                                                                    data-toggle="modal" data-target="#addChemotherapy">
                                                                    <i class="fas fa-plus"></i> Add New
                                                                </button>
                                                            </div>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="chemotherapy_performed">2. Is there any Chemotherapy
                                                                performed?</label>
                                                            <select name="chemotherapy_performed"
                                                                id="chemotherapy_performed" class="form-control" required>
                                                                <?php if ($patient['chemotherapy_performed'] == "1") { ?>
                                                                    <option value="<?= $patient['chemotherapy_performed'] ?>">
                                                                        Yes
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

                                                        <div class="card-body">
                                                            <table class="table table-bordered" id="chemotherapy_table">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Type of Chemotherapy</th>
                                                                        <th>Start Date</th>
                                                                        <th>Ongoing ?</th>
                                                                        <th>End Date</th>
                                                                        <th>Dose</th>
                                                                        <th>Frequency</th>
                                                                        <th>Remarks</th>
                                                                        <th>Actions</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php
                                                                    $x = 1;
                                                                    foreach ($override->get1('chemotherapy', 'patient_id', $_GET['cid'], 'status', 1) as $chemotherapy) {
                                                                        ?>
                                                                        <tr>
                                                                            <td><?= $chemotherapy['chemotherapy'] ?></td>
                                                                            <td><?= $chemotherapy['chemotherapy_start'] ?></td>
                                                                            <td>
                                                                                <?= $chemotherapy['chemotherapy_ongoing'] == 1 ? "Yes" : "No" ?>
                                                                            </td>
                                                                            <td><?= $chemotherapy['chemotherapy_end'] ?></td>
                                                                            <td><?= $chemotherapy['chemotherapy_dose'] ?></td>
                                                                            <td><?= $chemotherapy['chemotherapy_frequecy'] ?>
                                                                            </td>
                                                                            <td><?= $chemotherapy['chemotherapy_remarks'] ?>
                                                                            </td>
                                                                            <td>
                                                                                <button type="button"
                                                                                    class="btn btn-success btn-sm"
                                                                                    data-toggle="modal"
                                                                                    data-target="#updateChemotherapy<?= $chemotherapy['id'] ?>">
                                                                                    <i class="fas fa-edit"></i> Update
                                                                                </button>
                                                                                <button type="button"
                                                                                    class="btn btn-danger btn-sm"
                                                                                    data-toggle="modal"
                                                                                    data-target="#deleteChemotherapy<?= $chemotherapy['id'] ?>">
                                                                                    <i class="fas fa-trash"></i> Delete
                                                                                </button>
                                                                            </td>
                                                                        </tr>

                                                                        <!-- Update Modal -->
                                                                        <div class="modal fade"
                                                                            id="updateChemotherapy<?= $chemotherapy['id'] ?>"
                                                                            tabindex="-1" role="dialog">
                                                                            <div class="modal-dialog modal-lg" role="document">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header">
                                                                                        <h5 class="modal-title">Update
                                                                                            Chemotherapy</h5>
                                                                                        <button type="button" class="close"
                                                                                            data-dismiss="modal">&times;</button>
                                                                                    </div>
                                                                                    <div class="modal-body">
                                                                                        <form method="post">
                                                                                            <div class="form-group row">
                                                                                                <label
                                                                                                    class="col-sm-3 col-form-label">Type
                                                                                                    of Chemotherapy</label>
                                                                                                <div class="col-sm-9">
                                                                                                    <input type="text"
                                                                                                        name="chemotherapy"
                                                                                                        value="<?= $chemotherapy['chemotherapy'] ?>"
                                                                                                        class="form-control">
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="form-group row">
                                                                                                <label
                                                                                                    class="col-sm-3 col-form-label">Start
                                                                                                    Date</label>
                                                                                                <div class="col-sm-9">
                                                                                                    <input type="date"
                                                                                                        name="chemotherapy_start"
                                                                                                        value="<?= $chemotherapy['chemotherapy_start'] ?>"
                                                                                                        class="form-control">
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="form-group row">
                                                                                                <label
                                                                                                    class="col-sm-3 col-form-label">Ongoing?</label>
                                                                                                <div class="col-sm-9">
                                                                                                    <select
                                                                                                        name="chemotherapy_ongoing"
                                                                                                        class="form-control">
                                                                                                        <option value="1"
                                                                                                            <?= $chemotherapy['chemotherapy_ongoing'] == 1 ? 'selected' : '' ?>>Yes</option>
                                                                                                        <option value="2"
                                                                                                            <?= $chemotherapy['chemotherapy_ongoing'] == 2 ? 'selected' : '' ?>>No</option>
                                                                                                    </select>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="form-group row">
                                                                                                <label
                                                                                                    class="col-sm-3 col-form-label">End
                                                                                                    Date</label>
                                                                                                <div class="col-sm-9">
                                                                                                    <input type="date"
                                                                                                        name="chemotherapy_end"
                                                                                                        value="<?= $chemotherapy['chemotherapy_end'] ?>"
                                                                                                        class="form-control">
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="form-group row">
                                                                                                <label
                                                                                                    class="col-sm-3 col-form-label">Dose
                                                                                                    (mg)</label>
                                                                                                <div class="col-sm-9">
                                                                                                    <input type="text"
                                                                                                        name="chemotherapy_dose"
                                                                                                        value="<?= $chemotherapy['chemotherapy_dose'] ?>"
                                                                                                        class="form-control">
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="form-group row">
                                                                                                <label
                                                                                                    class="col-sm-3 col-form-label">Frequency
                                                                                                    (numbers)</label>
                                                                                                <div class="col-sm-9">
                                                                                                    <input type="text"
                                                                                                        name="chemotherapy_frequecy"
                                                                                                        value="<?= $chemotherapy['chemotherapy_frequecy'] ?>"
                                                                                                        class="form-control">
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="form-group row">
                                                                                                <label
                                                                                                    class="col-sm-3 col-form-label">Remarks</label>
                                                                                                <div class="col-sm-9">
                                                                                                    <input type="text"
                                                                                                        name="chemotherapy_remarks"
                                                                                                        value="<?= $chemotherapy['chemotherapy_remarks'] ?>"
                                                                                                        class="form-control">
                                                                                                </div>
                                                                                            </div>
                                                                                            <input type="hidden"
                                                                                                name="chemotherapy_id"
                                                                                                value="<?= $chemotherapy['id'] ?>">
                                                                                            <input type="hidden"
                                                                                                name="other_herbal"
                                                                                                value="<?= $patient['other_herbal'] ?>">
                                                                                            <input type="hidden"
                                                                                                name="chemotherapy_performed"
                                                                                                value="<?= $patient['chemotherapy_performed'] ?>">
                                                                                            <input type="submit"
                                                                                                name="update_chemotherapy"
                                                                                                class="btn btn-success mt-2"
                                                                                                value="Save">
                                                                                            <button type="button"
                                                                                                class="btn btn-secondary mt-2"
                                                                                                data-dismiss="modal">Cancel</button>
                                                                                        </form>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <!-- Delete Modal -->
                                                                        <div class="modal fade"
                                                                            id="deleteChemotherapy<?= $chemotherapy['id'] ?>"
                                                                            tabindex="-1" role="dialog">
                                                                            <div class="modal-dialog" role="document">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header">
                                                                                        <h5 class="modal-title">Delete
                                                                                            Chemotherapy</h5>
                                                                                        <button type="button" class="close"
                                                                                            data-dismiss="modal">&times;</button>
                                                                                    </div>
                                                                                    <div class="modal-body">
                                                                                        <p>Are you sure you want to delete this
                                                                                            record?</p>
                                                                                        <form method="post">
                                                                                            <input type="hidden"
                                                                                                name="chemotherapy_id"
                                                                                                value="<?= $chemotherapy['id'] ?>">
                                                                                            <input type="submit"
                                                                                                name="delete_chemotherapy"
                                                                                                class="btn btn-danger mt-2"
                                                                                                value="Delete">
                                                                                            <button type="button"
                                                                                                class="btn btn-secondary mt-2"
                                                                                                data-dismiss="modal">Cancel</button>
                                                                                        </form>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <?php
                                                                        $x++;
                                                                    }
                                                                    ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <hr>

                                                    <!-- Add Modal for Surgery -->
                                                    <div class="modal fade" id="addSurgery" tabindex="-1" role="dialog">
                                                        <div class="modal-dialog modal-lg" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Add New Surgery</h5>
                                                                    <button type="button" class="close"
                                                                        data-dismiss="modal">&times;</button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <form method="post">
                                                                        <div class="form-group row">
                                                                            <label class="col-sm-3 col-form-label">Type of
                                                                                Surgery</label>
                                                                            <div class="col-sm-9">
                                                                                <input type="text" name="surgery"
                                                                                    class="form-control"
                                                                                    placeholder="Type of Surgery">
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group row">
                                                                            <label class="col-sm-3 col-form-label">Start
                                                                                Date</label>
                                                                            <div class="col-sm-9">
                                                                                <input type="date" name="surgery_start"
                                                                                    class="form-control"
                                                                                    placeholder="Start Date">
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group row">
                                                                            <label
                                                                                class="col-sm-3 col-form-label">Frequency</label>
                                                                            <div class="col-sm-9">
                                                                                <input type="text" name="surgery_number"
                                                                                    class="form-control"
                                                                                    placeholder="Frequency (numbers)">
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group row">
                                                                            <label
                                                                                class="col-sm-3 col-form-label">Remarks</label>
                                                                            <div class="col-sm-9">
                                                                                <input type="text" name="surgery_remarks"
                                                                                    class="form-control"
                                                                                    placeholder="Remarks">
                                                                            </div>
                                                                        </div>
                                                                        <input type="hidden" name="other_herbal"
                                                                            value="<?= $patient['other_herbal'] ?>">
                                                                        <input type="hidden" name="surgery_performed"
                                                                            value="<?= $patient['surgery_performed'] ?>">
                                                                        <input type="submit" name="add_surgery"
                                                                            class="btn btn-success mt-2" value="Save">
                                                                        <button type="button" class="btn btn-secondary mt-2"
                                                                            data-dismiss="modal">Cancel</button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="card card-primary">
                                                        <div class="card-header">
                                                            <h3 class="card-title">Surgery</h3>
                                                            <div class="card-tools">
                                                                <button type="button" class="btn btn-success btn-sm"
                                                                    data-toggle="modal" data-target="#addSurgery">
                                                                    <i class="fas fa-plus"></i> Add New
                                                                </button>
                                                            </div>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="surgery_performed">3. Is there any Surgery
                                                                performed?</label>
                                                            <select name="surgery_performed" id="surgery_performed"
                                                                class="form-control" required>
                                                                <?php if ($patient['surgery_performed'] == "1") { ?>
                                                                    <option value="<?= $patient['surgery_performed'] ?>">
                                                                        Yes
                                                                    </option>
                                                                <?php } elseif ($patient['surgery_performed'] == "2") { ?>
                                                                    <option value="<?= $patient['surgery_performed'] ?>">No
                                                                    </option>
                                                                <?php } else { ?>
                                                                    <option value="">Select</option>
                                                                <?php } ?>
                                                                <option value="1">Yes</option>
                                                                <option value="2">No</option>
                                                            </select>
                                                        </div>

                                                        <div class="card-body">
                                                            <table class="table table-bordered" id="surgery_table">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Type of Surgery</th>
                                                                        <th>Start Date</th>
                                                                        <th>Frequency</th>
                                                                        <th>Remarks</th>
                                                                        <th>Actions</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php
                                                                    $x = 1;
                                                                    foreach ($override->get1('surgery', 'patient_id', $_GET['cid'], 'status', 1) as $surgery) {
                                                                        ?>
                                                                        <tr>
                                                                            <td><?= $surgery['surgery'] ?></td>
                                                                            <td><?= $surgery['surgery_start'] ?></td>
                                                                            <td><?= $surgery['surgery_number'] ?></td>
                                                                            <td><?= $surgery['surgery_remarks'] ?></td>
                                                                            <td>
                                                                                <button type="button"
                                                                                    class="btn btn-success btn-sm"
                                                                                    data-toggle="modal"
                                                                                    data-target="#updateSurgery<?= $surgery['id'] ?>">
                                                                                    <i class="fas fa-edit"></i> Update
                                                                                </button>
                                                                                <button type="button"
                                                                                    class="btn btn-danger btn-sm"
                                                                                    data-toggle="modal"
                                                                                    data-target="#deleteSurgery<?= $surgery['id'] ?>">
                                                                                    <i class="fas fa-trash"></i> Delete
                                                                                </button>
                                                                            </td>
                                                                        </tr>

                                                                        <!-- Update Modal for Surgery -->
                                                                        <div class="modal fade"
                                                                            id="updateSurgery<?= $surgery['id'] ?>"
                                                                            tabindex="-1" role="dialog">
                                                                            <div class="modal-dialog modal-lg" role="document">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header">
                                                                                        <h5 class="modal-title">Update Surgery
                                                                                        </h5>
                                                                                        <button type="button" class="close"
                                                                                            data-dismiss="modal">&times;</button>
                                                                                    </div>
                                                                                    <div class="modal-body">
                                                                                        <form method="post">
                                                                                            <div class="form-group row">
                                                                                                <label
                                                                                                    class="col-sm-3 col-form-label">Type
                                                                                                    of Surgery</label>
                                                                                                <div class="col-sm-9">
                                                                                                    <input type="text"
                                                                                                        name="surgery"
                                                                                                        value="<?= $surgery['surgery'] ?>"
                                                                                                        class="form-control">
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="form-group row">
                                                                                                <label
                                                                                                    class="col-sm-3 col-form-label">Start
                                                                                                    Date</label>
                                                                                                <div class="col-sm-9">
                                                                                                    <input type="date"
                                                                                                        name="surgery_start"
                                                                                                        value="<?= $surgery['surgery_start'] ?>"
                                                                                                        class="form-control">
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="form-group row">
                                                                                                <label
                                                                                                    class="col-sm-3 col-form-label">Frequency</label>
                                                                                                <div class="col-sm-9">
                                                                                                    <input type="text"
                                                                                                        name="surgery_number"
                                                                                                        value="<?= $surgery['surgery_number'] ?>"
                                                                                                        class="form-control">
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="form-group row">
                                                                                                <label
                                                                                                    class="col-sm-3 col-form-label">Remarks</label>
                                                                                                <div class="col-sm-9">
                                                                                                    <input type="text"
                                                                                                        name="surgery_remarks"
                                                                                                        value="<?= $surgery['surgery_remarks'] ?>"
                                                                                                        class="form-control">
                                                                                                </div>
                                                                                            </div>
                                                                                            <input type="hidden"
                                                                                                name="surgery_id"
                                                                                                value="<?= $surgery['id'] ?>">
                                                                                            <input type="hidden"
                                                                                                name="other_herbal"
                                                                                                value="<?= $patient['other_herbal'] ?>">
                                                                                            <input type="hidden"
                                                                                                name="surgery_performed"
                                                                                                value="<?= $patient['surgery_performed'] ?>">
                                                                                            <input type="submit"
                                                                                                name="update_surgery"
                                                                                                class="btn btn-success mt-2"
                                                                                                value="Save">
                                                                                            <button type="button"
                                                                                                class="btn btn-secondary mt-2"
                                                                                                data-dismiss="modal">Cancel</button>
                                                                                        </form>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <!-- Delete Modal for Surgery -->
                                                                        <div class="modal fade"
                                                                            id="deleteSurgery<?= $surgery['id'] ?>"
                                                                            tabindex="-1" role="dialog">
                                                                            <div class="modal-dialog" role="document">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header">
                                                                                        <h5 class="modal-title">Delete Surgery
                                                                                        </h5>
                                                                                        <button type="button" class="close"
                                                                                            data-dismiss="modal">&times;</button>
                                                                                    </div>
                                                                                    <div class="modal-body">
                                                                                        <p>Are you sure you want to delete this
                                                                                            record?</p>
                                                                                        <form method="post">
                                                                                            <input type="hidden"
                                                                                                name="surgery_id"
                                                                                                value="<?= $surgery['id'] ?>">
                                                                                            <input type="submit"
                                                                                                name="delete_surgery"
                                                                                                class="btn btn-danger mt-2"
                                                                                                value="Delete">
                                                                                            <button type="button"
                                                                                                class="btn btn-secondary mt-2"
                                                                                                data-dismiss="modal">Cancel</button>
                                                                                        </form>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <?php
                                                                        $x++;
                                                                    }
                                                                    ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <div class="form-group">
                                                <input type="hidden" name="id" value="<?= $patient['id'] ?>">
                                                <input type="submit" name="add_crf1" value="Submit" class="btn btn-info">
                                                <a href="index1.php" class="btn btn-default">Cancel</a>
                                            </div>
                                        </div>
                                    </form>
                                    <!-- Form End -->
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->
        <?php } elseif ($_GET['id'] == 9) { ?>
            <?php $patient = $override->get1('crf2', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode'])[0] ?>
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
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>Add New Position</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item">
                                        <a href="info.php?id=2">
                                            < Back </a>
                                    </li>&nbsp;&nbsp;
                                    <li class="breadcrumb-item"><a href="index1.php">Home</a></li>&nbsp;&nbsp;
                                    <li class="breadcrumb-item">
                                        <a href="info.php?id=2">
                                            Go to Position list >
                                        </a>
                                    </li>&nbsp;&nbsp;
                                    <li class="breadcrumb-item active">Add New Position</li>
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
                            // $staff = $override->getNews('user', 'status', 1, 'id', $_GET['staff_id'])[0];
                            // $site = $override->get('site', 'id', $staff['site_id'])[0];
                            $position = $override->get('position', 'id', $_GET['position_id'])[0];
                            ?>
                            <!-- right column -->
                            <div class="col-md-12">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title">CRF 2: VITAL SIGN MEASUREMENTS (STANDARD) AND PHYSICAL
                                            EXAMINATION</h3>
                                    </div>
                                    <form id="crf2" method="post">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label for="crf2_date">Date</label>
                                                        <input value="<?= $patient['crf2_date'] ?>" type="text"
                                                            class="form-control" name="crf2_date" id="crf2_date"
                                                            placeholder="Example: 2023-01-01">
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label for="height">Height</label>
                                                        <input value="<?= $patient['height'] ?>" type="text"
                                                            class="form-control" name="height" id="height" placeholder="cm">
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label for="weight">Weight</label>
                                                        <input value="<?= $patient['weight'] ?>" type="text"
                                                            class="form-control" name="weight" id="weight" placeholder="kg">
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label for="bmi">BMI</label>
                                                        <span id="bmi"></span>&nbsp;&nbsp;kg/m2
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label for="time">Time</label>
                                                        <input value="<?= $patient['time'] ?>" type="text"
                                                            class="form-control" name="time" id="time"
                                                            placeholder="hh:mm (24-hour format)">
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label for="temperature">Temperature</label>
                                                        <input value="<?= $patient['temperature'] ?>" type="text"
                                                            class="form-control" name="temperature" id="temperature"
                                                            placeholder="Celsius">
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label for="method">Method</label>
                                                        <select name="method" class="form-control" id="method">
                                                            <?php if ($patient['method'] == "1") { ?>
                                                                <option value="<?= $patient['method'] ?>">Oral</option>
                                                            <?php } elseif ($patient['method'] == "2") { ?>
                                                                <option value="<?= $patient['method'] ?>">Axillary
                                                                </option>
                                                            <?php } elseif ($patient['method'] == "3") { ?>
                                                                <option value="<?= $patient['method'] ?>">Tympanic
                                                                </option>
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

                                            <div class="row">
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label for="respiratory_rate">Respiratory Rate</label>
                                                        <input value="<?= $patient['respiratory_rate'] ?>" type="text"
                                                            class="form-control" name="respiratory_rate"
                                                            id="respiratory_rate" placeholder="breaths/min">
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label for="heart_rate">Heart Rate</label>
                                                        <input value="<?= $patient['heart_rate'] ?>" type="text"
                                                            class="form-control" name="heart_rate" id="heart_rate"
                                                            placeholder="beats/min">
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label for="systolic">Systolic Blood Pressure</label>
                                                        <input value="<?= $patient['systolic'] ?>" type="text"
                                                            class="form-control" name="systolic" id="systolic"
                                                            placeholder="mmHg">
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label for="diastolic">Diastolic Blood Pressure</label>
                                                        <input value="<?= $patient['diastolic'] ?>" type="text"
                                                            class="form-control" name="diastolic" id="diastolic"
                                                            placeholder="mmHg">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card card-primary">
                                                <div class="card-header">
                                                    <h3 class="card-title">PHYSICAL EXAMINATION</h3>
                                                </div>

                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <label for="time2">Physical Examination Time</label>
                                                            <input value="<?= $patient['time2'] ?>" type="time"
                                                                class="form-control" name="time2" id="time2"
                                                                placeholder="hh:mm (24-hour format)">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group row border">
                                                    <div class="col-sm-4 border">
                                                        <div class="form-group row align-items-center">
                                                            <label for="appearance" class="col-sm-4 col-form-label">General
                                                                Appearance:</label>
                                                            <div class="col-sm-8">
                                                                <select class="form-control" id="appearance"
                                                                    name="appearance">
                                                                    <?php if ($patient['appearance'] == "1") { ?>
                                                                        <option value="<?= $patient['appearance'] ?>">Normal
                                                                        </option>
                                                                    <?php } elseif ($patient['appearance'] == "2") { ?>
                                                                        <option value="<?= $patient['appearance'] ?>">Abnormal
                                                                        </option>
                                                                    <?php } elseif ($patient['appearance'] == "3") { ?>
                                                                        <option value="<?= $patient['appearance'] ?>">Not
                                                                            examined</option>
                                                                    <?php } else { ?>
                                                                        <option value="">Select</option>
                                                                    <?php } ?>
                                                                    <option value="1">Normal</option>
                                                                    <option value="2">Abnormal</option>
                                                                    <option value="3">Not examined</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center">
                                                            <label for="appearance_comments"
                                                                class="col-sm-4 col-form-label">Comments:</label>
                                                            <div class="col-sm-8">
                                                                <input type="text" class="form-control"
                                                                    id="appearance_comments" name="appearance_comments"
                                                                    value="<?= $patient['appearance_comments'] ?>">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center">
                                                            <label for="appearance_signifcnt"
                                                                class="col-sm-4 col-form-label">Clinically
                                                                Significant?:</label>
                                                            <div class="col-sm-8">
                                                                <select class="form-control" id="appearance_signifcnt"
                                                                    name="appearance_signifcnt">
                                                                    <?php if ($patient['appearance_signifcnt'] == "1") { ?>
                                                                        <option value="<?= $patient['appearance_signifcnt'] ?>">
                                                                            Yes</option>
                                                                    <?php } elseif ($patient['appearance_signifcnt'] == "2") { ?>
                                                                        <option value="<?= $patient['appearance_signifcnt'] ?>">
                                                                            No</option>
                                                                    <?php } else { ?>
                                                                        <option value="">Select</option>
                                                                    <?php } ?>
                                                                    <option value="1">Yes</option>
                                                                    <option value="2">No</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 border">
                                                        <div class="form-group row align-items-center">
                                                            <label for="heent"
                                                                class="col-sm-4 col-form-label">H/E/E/N/T:</label>
                                                            <div class="col-sm-8">
                                                                <select class="form-control" id="heent" name="heent">
                                                                    <?php if ($patient['heent'] == "1") { ?>
                                                                        <option value="<?= $patient['heent'] ?>">Normal</option>
                                                                    <?php } elseif ($patient['heent'] == "2") { ?>
                                                                        <option value="<?= $patient['heent'] ?>">Abnormal
                                                                        </option>
                                                                    <?php } elseif ($patient['heent'] == "3") { ?>
                                                                        <option value="<?= $patient['heent'] ?>">Not examined
                                                                        </option>
                                                                    <?php } else { ?>
                                                                        <option value="">Select</option>
                                                                    <?php } ?>
                                                                    <option value="1">Normal</option>
                                                                    <option value="2">Abnormal</option>
                                                                    <option value="3">Not examined</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center">
                                                            <label for="heent_comments"
                                                                class="col-sm-4 col-form-label">Comments:</label>
                                                            <div class="col-sm-8">
                                                                <input type="text" class="form-control" id="heent_comments"
                                                                    name="heent_comments"
                                                                    value="<?= $patient['heent_comments'] ?>">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center">
                                                            <label for="heent_signifcnt"
                                                                class="col-sm-4 col-form-label">Clinically
                                                                Significant?:</label>
                                                            <div class="col-sm-8">
                                                                <select class="form-control" id="heent_signifcnt"
                                                                    name="heent_signifcnt">
                                                                    <?php if ($patient['heent_signifcnt'] == "1") { ?>
                                                                        <option value="<?= $patient['heent_signifcnt'] ?>">Yes
                                                                        </option>
                                                                    <?php } elseif ($patient['heent_signifcnt'] == "2") { ?>
                                                                        <option value="<?= $patient['heent_signifcnt'] ?>">No
                                                                        </option>
                                                                    <?php } else { ?>
                                                                        <option value="">Select</option>
                                                                    <?php } ?>
                                                                    <option value="1">Yes</option>
                                                                    <option value="2">No</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 border">
                                                        <div class="form-group row align-items-center">
                                                            <label for="respiratory"
                                                                class="col-sm-4 col-form-label">Respiratory:</label>
                                                            <div class="col-sm-8">
                                                                <select class="form-control" id="respiratory"
                                                                    name="respiratory">
                                                                    <?php if ($patient['respiratory'] == "1") { ?>
                                                                        <option value="<?= $patient['respiratory'] ?>">Normal
                                                                        </option>
                                                                    <?php } elseif ($patient['respiratory'] == "2") { ?>
                                                                        <option value="<?= $patient['respiratory'] ?>">Abnormal
                                                                        </option>
                                                                    <?php } elseif ($patient['respiratory'] == "3") { ?>
                                                                        <option value="<?= $patient['respiratory'] ?>">Not
                                                                            examined</option>
                                                                    <?php } else { ?>
                                                                        <option value="">Select</option>
                                                                    <?php } ?>
                                                                    <option value="1">Normal</option>
                                                                    <option value="2">Abnormal</option>
                                                                    <option value="3">Not examined</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center">
                                                            <label for="respiratory_comments"
                                                                class="col-sm-4 col-form-label">Comments:</label>
                                                            <div class="col-sm-8">
                                                                <input type="text" class="form-control"
                                                                    id="respiratory_comments" name="respiratory_comments"
                                                                    value="<?= $patient['respiratory_comments'] ?>">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center">
                                                            <label for="respiratory_signifcnt"
                                                                class="col-sm-4 col-form-label">Clinically
                                                                Significant?:</label>
                                                            <div class="col-sm-8">
                                                                <select class="form-control" id="respiratory_signifcnt"
                                                                    name="respiratory_signifcnt">
                                                                    <?php if ($patient['respiratory_signifcnt'] == "1") { ?>
                                                                        <option
                                                                            value="<?= $patient['respiratory_signifcnt'] ?>">Yes
                                                                        </option>
                                                                    <?php } elseif ($patient['respiratory_signifcnt'] == "2") { ?>
                                                                        <option
                                                                            value="<?= $patient['respiratory_signifcnt'] ?>">No
                                                                        </option>
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
                                                <div class="form-group row border">
                                                    <div class="col-sm-4 border">
                                                        <div class="form-group row align-items-center">
                                                            <label for="cardiovascular"
                                                                class="col-sm-4 col-form-label">Cardiovascular:</label>
                                                            <div class="col-sm-8">
                                                                <select class="form-control" id="cardiovascular"
                                                                    name="cardiovascular">
                                                                    <?php if ($patient['cardiovascular'] == "1") { ?>
                                                                        <option value="<?= $patient['cardiovascular'] ?>">Normal
                                                                        </option>
                                                                    <?php } elseif ($patient['cardiovascular'] == "2") { ?>
                                                                        <option value="<?= $patient['cardiovascular'] ?>">
                                                                            Abnormal</option>
                                                                    <?php } elseif ($patient['cardiovascular'] == "3") { ?>
                                                                        <option value="<?= $patient['cardiovascular'] ?>">Not
                                                                            examined</option>
                                                                    <?php } else { ?>
                                                                        <option value="">Select</option>
                                                                    <?php } ?>
                                                                    <option value="1">Normal</option>
                                                                    <option value="2">Abnormal</option>
                                                                    <option value="3">Not examined</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center">
                                                            <label for="cardiovascular_comments"
                                                                class="col-sm-4 col-form-label">Comments:</label>
                                                            <div class="col-sm-8">
                                                                <input type="text" class="form-control"
                                                                    id="cardiovascular_comments"
                                                                    name="cardiovascular_comments"
                                                                    value="<?= $patient['cardiovascular_comments'] ?>">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center">
                                                            <label for="cardiovascular_signifcnt"
                                                                class="col-sm-4 col-form-label">Clinically
                                                                Significant?:</label>
                                                            <div class="col-sm-8">
                                                                <select class="form-control" id="cardiovascular_signifcnt"
                                                                    name="cardiovascular_signifcnt">
                                                                    <?php if ($patient['cardiovascular_signifcnt'] == "1") { ?>
                                                                        <option
                                                                            value="<?= $patient['cardiovascular_signifcnt'] ?>">
                                                                            Yes</option>
                                                                    <?php } elseif ($patient['cardiovascular_signifcnt'] == "2") { ?>
                                                                        <option
                                                                            value="<?= $patient['cardiovascular_signifcnt'] ?>">
                                                                            No</option>
                                                                    <?php } else { ?>
                                                                        <option value="">Select</option>
                                                                    <?php } ?>
                                                                    <option value="1">Yes</option>
                                                                    <option value="2">No</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 border">
                                                        <div class="form-group row align-items-center">
                                                            <label for="abdnominal"
                                                                class="col-sm-4 col-form-label">Abdominal/Gastrointestinal:</label>
                                                            <div class="col-sm-8">
                                                                <select class="form-control" id="abdnominal"
                                                                    name="abdnominal">
                                                                    <?php if ($patient['abdnominal'] == "1") { ?>
                                                                        <option value="<?= $patient['abdnominal'] ?>">Normal
                                                                        </option>
                                                                    <?php } elseif ($patient['abdnominal'] == "2") { ?>
                                                                        <option value="<?= $patient['abdnominal'] ?>">Abnormal
                                                                        </option>
                                                                    <?php } elseif ($patient['abdnominal'] == "3") { ?>
                                                                        <option value="<?= $patient['abdnominal'] ?>">Not
                                                                            examined</option>
                                                                    <?php } else { ?>
                                                                        <option value="">Select</option>
                                                                    <?php } ?>
                                                                    <option value="1">Normal</option>
                                                                    <option value="2">Abnormal</option>
                                                                    <option value="3">Not examined</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center">
                                                            <label for="abdnominal_comments"
                                                                class="col-sm-4 col-form-label">Comments:</label>
                                                            <div class="col-sm-8">
                                                                <input type="text" class="form-control"
                                                                    id="abdnominal_comments" name="abdnominal_comments"
                                                                    value="<?= $patient['abdnominal_comments'] ?>">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center">
                                                            <label for="abdnominal_signifcnt"
                                                                class="col-sm-4 col-form-label">Clinically
                                                                Significant?:</label>
                                                            <div class="col-sm-8">
                                                                <select class="form-control" id="abdnominal_signifcnt"
                                                                    name="abdnominal_signifcnt">
                                                                    <?php if ($patient['abdnominal_signifcnt'] == "1") { ?>
                                                                        <option value="<?= $patient['abdnominal_signifcnt'] ?>">
                                                                            Yes</option>
                                                                    <?php } elseif ($patient['abdnominal_signifcnt'] == "2") { ?>
                                                                        <option value="<?= $patient['abdnominal_signifcnt'] ?>">
                                                                            No</option>
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

                                                <div class="form-group row border">
                                                    <div class="col-sm-4 border">
                                                        <div class="form-group row align-items-center">
                                                            <label for="abdnominal"
                                                                class="col-sm-4 col-form-label">Abdominal/Gastrointestinal:</label>
                                                            <div class="col-sm-8">
                                                                <select class="form-control" id="abdnominal"
                                                                    name="abdnominal">
                                                                    <?php if ($patient['abdnominal'] == "1") { ?>
                                                                        <option value="<?= $patient['abdnominal'] ?>">Normal
                                                                        </option>
                                                                    <?php } elseif ($patient['abdnominal'] == "2") { ?>
                                                                        <option value="<?= $patient['abdnominal'] ?>">Abnormal
                                                                        </option>
                                                                    <?php } elseif ($patient['abdnominal'] == "3") { ?>
                                                                        <option value="<?= $patient['abdnominal'] ?>">Not
                                                                            examined</option>
                                                                    <?php } else { ?>
                                                                        <option value="">Select</option>
                                                                    <?php } ?>
                                                                    <option value="1">Normal</option>
                                                                    <option value="2">Abnormal</option>
                                                                    <option value="3">Not examined</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center">
                                                            <label for="abdnominal_comments"
                                                                class="col-sm-4 col-form-label">Comments:</label>
                                                            <div class="col-sm-8">
                                                                <input type="text" class="form-control"
                                                                    id="abdnominal_comments" name="abdnominal_comments"
                                                                    value="<?= $patient['abdnominal_comments'] ?>">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center">
                                                            <label for="abdnominal_signifcnt"
                                                                class="col-sm-4 col-form-label">Clinically
                                                                Significant?:</label>
                                                            <div class="col-sm-8">
                                                                <select class="form-control" id="abdnominal_signifcnt"
                                                                    name="abdnominal_signifcnt">
                                                                    <?php if ($patient['abdnominal_signifcnt'] == "1") { ?>
                                                                        <option value="<?= $patient['abdnominal_signifcnt'] ?>">
                                                                            Yes</option>
                                                                    <?php } elseif ($patient['abdnominal_signifcnt'] == "2") { ?>
                                                                        <option value="<?= $patient['abdnominal_signifcnt'] ?>">
                                                                            No</option>
                                                                    <?php } else { ?>
                                                                        <option value="">Select</option>
                                                                    <?php } ?>
                                                                    <option value="1">Yes</option>
                                                                    <option value="2">No</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 border">
                                                        <div class="form-group row align-items-center">
                                                            <label for="urogenital"
                                                                class="col-sm-4 col-form-label">Urogenital:</label>
                                                            <div class="col-sm-8">
                                                                <select class="form-control" id="urogenital"
                                                                    name="urogenital">
                                                                    <?php if ($patient['urogenital'] == "1") { ?>
                                                                        <option value="<?= $patient['urogenital'] ?>">Normal
                                                                        </option>
                                                                    <?php } elseif ($patient['urogenital'] == "2") { ?>
                                                                        <option value="<?= $patient['urogenital'] ?>">Abnormal
                                                                        </option>
                                                                    <?php } elseif ($patient['urogenital'] == "3") { ?>
                                                                        <option value="<?= $patient['urogenital'] ?>">Not
                                                                            examined</option>
                                                                    <?php } else { ?>
                                                                        <option value="">Select</option>
                                                                    <?php } ?>
                                                                    <option value="1">Normal</option>
                                                                    <option value="2">Abnormal</option>
                                                                    <option value="3">Not examined</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center">
                                                            <label for="urogenital_comments"
                                                                class="col-sm-4 col-form-label">Comments:</label>
                                                            <div class="col-sm-8">
                                                                <input type="text" class="form-control"
                                                                    id="urogenital_comments" name="urogenital_comments"
                                                                    value="<?= $patient['urogenital_comments'] ?>">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center">
                                                            <label for="urogenital_signifcnt"
                                                                class="col-sm-4 col-form-label">Clinically
                                                                Significant?:</label>
                                                            <div class="col-sm-8">
                                                                <select class="form-control" id="urogenital_signifcnt"
                                                                    name="urogenital_signifcnt">
                                                                    <?php if ($patient['urogenital_signifcnt'] == "1") { ?>
                                                                        <option value="<?= $patient['urogenital_signifcnt'] ?>">
                                                                            Yes</option>
                                                                    <?php } elseif ($patient['urogenital_signifcnt'] == "2") { ?>
                                                                        <option value="<?= $patient['urogenital_signifcnt'] ?>">
                                                                            No</option>
                                                                    <?php } else { ?>
                                                                        <option value="">Select</option>
                                                                    <?php } ?>
                                                                    <option value="1">Yes</option>
                                                                    <option value="2">No</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 border">
                                                        <div class="form-group row align-items-center">
                                                            <label for="musculoskeletal"
                                                                class="col-sm-4 col-form-label">Musculoskeletal:</label>
                                                            <div class="col-sm-8">
                                                                <select class="form-control" id="musculoskeletal"
                                                                    name="musculoskeletal">
                                                                    <?php if ($patient['musculoskeletal'] == "1") { ?>
                                                                        <option value="<?= $patient['musculoskeletal'] ?>">
                                                                            Normal</option>
                                                                    <?php } elseif ($patient['musculoskeletal'] == "2") { ?>
                                                                        <option value="<?= $patient['musculoskeletal'] ?>">
                                                                            Abnormal</option>
                                                                    <?php } elseif ($patient['musculoskeletal'] == "3") { ?>
                                                                        <option value="<?= $patient['musculoskeletal'] ?>">Not
                                                                            examined</option>
                                                                    <?php } else { ?>
                                                                        <option value="">Select</option>
                                                                    <?php } ?>
                                                                    <option value="1">Normal</option>
                                                                    <option value="2">Abnormal</option>
                                                                    <option value="3">Not examined</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center">
                                                            <label for="musculoskeletal_comments"
                                                                class="col-sm-4 col-form-label">Comments:</label>
                                                            <div class="col-sm-8">
                                                                <input type="text" class="form-control"
                                                                    id="musculoskeletal_comments"
                                                                    name="musculoskeletal_comments"
                                                                    value="<?= $patient['musculoskeletal_comments'] ?>">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center">
                                                            <label for="musculoskeletal_signifcnt"
                                                                class="col-sm-4 col-form-label">Clinically
                                                                Significant?:</label>
                                                            <div class="col-sm-8">
                                                                <select class="form-control" id="musculoskeletal_signifcnt"
                                                                    name="musculoskeletal_signifcnt">
                                                                    <?php if ($patient['musculoskeletal_signifcnt'] == "1") { ?>
                                                                        <option
                                                                            value="<?= $patient['musculoskeletal_signifcnt'] ?>">
                                                                            Yes</option>
                                                                    <?php } elseif ($patient['musculoskeletal_signifcnt'] == "2") { ?>
                                                                        <option
                                                                            value="<?= $patient['musculoskeletal_signifcnt'] ?>">
                                                                            No</option>
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
                                                <div class="form-group row border">
                                                    <div class="col-sm-4 border">
                                                        <div class="form-group row align-items-center">
                                                            <label for="neurological"
                                                                class="col-sm-4 col-form-label">Neurological:</label>
                                                            <div class="col-sm-8">
                                                                <select class="form-control" id="neurological"
                                                                    name="neurological">
                                                                    <?php if ($patient['neurological'] == "1") { ?>
                                                                        <option value="<?= $patient['neurological'] ?>">Normal
                                                                        </option>
                                                                    <?php } elseif ($patient['neurological'] == "2") { ?>
                                                                        <option value="<?= $patient['neurological'] ?>">Abnormal
                                                                        </option>
                                                                    <?php } elseif ($patient['neurological'] == "3") { ?>
                                                                        <option value="<?= $patient['neurological'] ?>">Not
                                                                            examined</option>
                                                                    <?php } else { ?>
                                                                        <option value="">Select</option>
                                                                    <?php } ?>
                                                                    <option value="1">Normal</option>
                                                                    <option value="2">Abnormal</option>
                                                                    <option value="3">Not examined</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center">
                                                            <label for="neurological_comments"
                                                                class="col-sm-4 col-form-label">Comments:</label>
                                                            <div class="col-sm-8">
                                                                <input type="text" class="form-control"
                                                                    id="neurological_comments" name="neurological_comments"
                                                                    value="<?= $patient['neurological_comments'] ?>">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center">
                                                            <label for="neurological_signifcnt"
                                                                class="col-sm-4 col-form-label">Clinically
                                                                Significant?:</label>
                                                            <div class="col-sm-8">
                                                                <select class="form-control" id="neurological_signifcnt"
                                                                    name="neurological_signifcnt">
                                                                    <?php if ($patient['neurological_signifcnt'] == "1") { ?>
                                                                        <option
                                                                            value="<?= $patient['neurological_signifcnt'] ?>">
                                                                            Yes</option>
                                                                    <?php } elseif ($patient['neurological_signifcnt'] == "2") { ?>
                                                                        <option
                                                                            value="<?= $patient['neurological_signifcnt'] ?>">No
                                                                        </option>
                                                                    <?php } else { ?>
                                                                        <option value="">Select</option>
                                                                    <?php } ?>
                                                                    <option value="1">Yes</option>
                                                                    <option value="2">No</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 border">
                                                        <div class="form-group row align-items-center">
                                                            <label for="psychological"
                                                                class="col-sm-4 col-form-label">Psychological:</label>
                                                            <div class="col-sm-8">
                                                                <select class="form-control" id="psychological"
                                                                    name="psychological">
                                                                    <?php if ($patient['psychological'] == "1") { ?>
                                                                        <option value="<?= $patient['psychological'] ?>">Normal
                                                                        </option>
                                                                    <?php } elseif ($patient['psychological'] == "2") { ?>
                                                                        <option value="<?= $patient['psychological'] ?>">
                                                                            Abnormal</option>
                                                                    <?php } elseif ($patient['psychological'] == "3") { ?>
                                                                        <option value="<?= $patient['psychological'] ?>">Not
                                                                            examined</option>
                                                                    <?php } else { ?>
                                                                        <option value="">Select</option>
                                                                    <?php } ?>
                                                                    <option value="1">Normal</option>
                                                                    <option value="2">Abnormal</option>
                                                                    <option value="3">Not examined</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center">
                                                            <label for="psychological_comments"
                                                                class="col-sm-4 col-form-label">Comments:</label>
                                                            <div class="col-sm-8">
                                                                <input type="text" class="form-control"
                                                                    id="psychological_comments"
                                                                    name="psychological_comments"
                                                                    value="<?= $patient['psychological_comments'] ?>">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center">
                                                            <label for="psychological_signifcnt"
                                                                class="col-sm-4 col-form-label">Clinically
                                                                Significant?:</label>
                                                            <div class="col-sm-8">
                                                                <select class="form-control" id="psychological_signifcnt"
                                                                    name="psychological_signifcnt">
                                                                    <?php if ($patient['psychological_signifcnt'] == "1") { ?>
                                                                        <option
                                                                            value="<?= $patient['psychological_signifcnt'] ?>">
                                                                            Yes</option>
                                                                    <?php } elseif ($patient['psychological_signifcnt'] == "2") { ?>
                                                                        <option
                                                                            value="<?= $patient['psychological_signifcnt'] ?>">
                                                                            No</option>
                                                                    <?php } else { ?>
                                                                        <option value="">Select</option>
                                                                    <?php } ?>
                                                                    <option value="1">Yes</option>
                                                                    <option value="2">No</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 border">
                                                        <div class="form-group row align-items-center">
                                                            <label for="endocrime"
                                                                class="col-sm-4 col-form-label">Endocrine:</label>
                                                            <div class="col-sm-8">
                                                                <select class="form-control" id="endocrime"
                                                                    name="endocrime">
                                                                    <?php if ($patient['endocrime'] == "1") { ?>
                                                                        <option value="<?= $patient['endocrime'] ?>">Normal
                                                                        </option>
                                                                    <?php } elseif ($patient['endocrime'] == "2") { ?>
                                                                        <option value="<?= $patient['endocrime'] ?>">Abnormal
                                                                        </option>
                                                                    <?php } elseif ($patient['endocrime'] == "3") { ?>
                                                                        <option value="<?= $patient['endocrime'] ?>">Not
                                                                            examined</option>
                                                                    <?php } else { ?>
                                                                        <option value="">Select</option>
                                                                    <?php } ?>
                                                                    <option value="1">Normal</option>
                                                                    <option value="2">Abnormal</option>
                                                                    <option value="3">Not examined</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center">
                                                            <label for="endocrime_comments"
                                                                class="col-sm-4 col-form-label">Comments:</label>
                                                            <div class="col-sm-8">
                                                                <input type="text" class="form-control"
                                                                    id="endocrime_comments" name="endocrime_comments"
                                                                    value="<?= $patient['endocrime_comments'] ?>">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center">
                                                            <label for="endocrime_signifcnt"
                                                                class="col-sm-4 col-form-label">Clinically
                                                                Significant?:</label>
                                                            <div class="col-sm-8">
                                                                <select class="form-control" id="endocrime_signifcnt"
                                                                    name="endocrime_signifcnt">
                                                                    <?php if ($patient['endocrime_signifcnt'] == "1") { ?>
                                                                        <option value="<?= $patient['endocrime_signifcnt'] ?>">
                                                                            Yes</option>
                                                                    <?php } elseif ($patient['endocrime_signifcnt'] == "2") { ?>
                                                                        <option value="<?= $patient['endocrime_signifcnt'] ?>">
                                                                            No</option>
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

                                                <div class="form-group row border">
                                                    <div class="col-sm-4 border">
                                                        <div class="form-group row align-items-center">
                                                            <label for="lymphatic"
                                                                class="col-sm-4 col-form-label">Lymphatic:</label>
                                                            <div class="col-sm-8">
                                                                <select class="form-control" name="lymphatic"
                                                                    id="lymphatic">
                                                                    <?php if ($patient['lymphatic'] == "1") { ?>
                                                                        <option value="<?= $patient['lymphatic'] ?>">Normal
                                                                        </option>
                                                                    <?php } elseif ($patient['lymphatic'] == "2") { ?>
                                                                        <option value="<?= $patient['lymphatic'] ?>">Abnormal
                                                                        </option>
                                                                    <?php } elseif ($patient['lymphatic'] == "3") { ?>
                                                                        <option value="<?= $patient['lymphatic'] ?>">Not
                                                                            examined</option>
                                                                    <?php } else { ?>
                                                                        <option value="">Select</option>
                                                                    <?php } ?>
                                                                    <option value="1">Normal</option>
                                                                    <option value="2">Abnormal</option>
                                                                    <option value="3">Not examined</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center">
                                                            <label for="lymphatic_comments"
                                                                class="col-sm-4 col-form-label">Comments:</label>
                                                            <div class="col-sm-8">
                                                                <input class="form-control"
                                                                    value="<?= $patient['lymphatic_comments'] ?>"
                                                                    type="text" name="lymphatic_comments"
                                                                    id="lymphatic_comments" />
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center">
                                                            <label for="lymphatic_signifcnt"
                                                                class="col-sm-4 col-form-label">Clinically
                                                                Significant?:</label>
                                                            <div class="col-sm-8">
                                                                <select class="form-control" name="lymphatic_signifcnt"
                                                                    id="lymphatic_signifcnt">
                                                                    <?php if ($patient['lymphatic_signifcnt'] == "1") { ?>
                                                                        <option value="<?= $patient['lymphatic_signifcnt'] ?>">
                                                                            Yes</option>
                                                                    <?php } elseif ($patient['lymphatic_signifcnt'] == "2") { ?>
                                                                        <option value="<?= $patient['lymphatic_signifcnt'] ?>">
                                                                            No</option>
                                                                    <?php } else { ?>
                                                                        <option value="">Select</option>
                                                                    <?php } ?>
                                                                    <option value="1">Yes</option>
                                                                    <option value="2">No</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 border">
                                                        <div class="form-group row align-items-center">
                                                            <label for="skin"
                                                                class="col-sm-4 col-form-label">Skin/Dermatological:</label>
                                                            <div class="col-sm-8">
                                                                <select class="form-control" name="skin" id="skin">
                                                                    <?php if ($patient['skin'] == "1") { ?>
                                                                        <option value="<?= $patient['skin'] ?>">Normal</option>
                                                                    <?php } elseif ($patient['skin'] == "2") { ?>
                                                                        <option value="<?= $patient['skin'] ?>">Abnormal
                                                                        </option>
                                                                    <?php } elseif ($patient['skin'] == "3") { ?>
                                                                        <option value="<?= $patient['skin'] ?>">Not examined
                                                                        </option>
                                                                    <?php } else { ?>
                                                                        <option value="">Select</option>
                                                                    <?php } ?>
                                                                    <option value="1">Normal</option>
                                                                    <option value="2">Abnormal</option>
                                                                    <option value="3">Not examined</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center">
                                                            <label for="skin_comments"
                                                                class="col-sm-4 col-form-label">Comments:</label>
                                                            <div class="col-sm-8">
                                                                <input class="form-control"
                                                                    value="<?= $patient['skin_comments'] ?>" type="text"
                                                                    name="skin_comments" id="skin_comments" />
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center">
                                                            <label for="skin_signifcnt"
                                                                class="col-sm-4 col-form-label">Clinically
                                                                Significant?:</label>
                                                            <div class="col-sm-8">
                                                                <select class="form-control" name="skin_signifcnt"
                                                                    id="skin_signifcnt">
                                                                    <?php if ($patient['skin_signifcnt'] == "1") { ?>
                                                                        <option value="<?= $patient['skin_signifcnt'] ?>">Yes
                                                                        </option>
                                                                    <?php } elseif ($patient['skin_signifcnt'] == "2") { ?>
                                                                        <option value="<?= $patient['skin_signifcnt'] ?>">No
                                                                        </option>
                                                                    <?php } else { ?>
                                                                        <option value="">Select</option>
                                                                    <?php } ?>
                                                                    <option value="1">Yes</option>
                                                                    <option value="2">No</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 border">
                                                        <div class="form-group row align-items-center">
                                                            <label for="local_examination"
                                                                class="col-sm-4 col-form-label">Local
                                                                examination:</label>
                                                            <div class="col-sm-8">
                                                                <select class="form-control" name="local_examination"
                                                                    id="local_examination">
                                                                    <?php if ($patient['local_examination'] == "1") { ?>
                                                                        <option value="<?= $patient['local_examination'] ?>">
                                                                            Normal</option>
                                                                    <?php } elseif ($patient['local_examination'] == "2") { ?>
                                                                        <option value="<?= $patient['local_examination'] ?>">
                                                                            Abnormal</option>
                                                                    <?php } elseif ($patient['local_examination'] == "3") { ?>
                                                                        <option value="<?= $patient['local_examination'] ?>">Not
                                                                            examined</option>
                                                                    <?php } else { ?>
                                                                        <option value="">Select</option>
                                                                    <?php } ?>
                                                                    <option value="1">Normal</option>
                                                                    <option value="2">Abnormal</option>
                                                                    <option value="3">Not examined</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center">
                                                            <label for="local_examination_comments"
                                                                class="col-sm-4 col-form-label">Comments:</label>
                                                            <div class="col-sm-8">
                                                                <input class="form-control"
                                                                    value="<?= $patient['local_examination_comments'] ?>"
                                                                    type="text" name="local_examination_comments"
                                                                    id="local_examination_comments" />
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center">
                                                            <label for="local_examination_signifcnt"
                                                                class="col-sm-4 col-form-label">Clinically
                                                                Significant?:</label>
                                                            <div class="col-sm-8">
                                                                <select class="form-control"
                                                                    name="local_examination_signifcnt"
                                                                    id="local_examination_signifcnt">
                                                                    <?php if ($patient['local_examination_signifcnt'] == "1") { ?>
                                                                        <option
                                                                            value="<?= $patient['local_examination_signifcnt'] ?>">
                                                                            Yes</option>
                                                                    <?php } elseif ($patient['local_examination_signifcnt'] == "2") { ?>
                                                                        <option
                                                                            value="<?= $patient['local_examination_signifcnt'] ?>">
                                                                            No</option>
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

                                                <div class="form-group row border">
                                                    <div class="col-sm-4 border">
                                                        <div class="form-group row align-items-center">
                                                            <label for="physical_exams_other"
                                                                class="col-sm-4 col-form-label">Is
                                                                there any Other physical
                                                                System?:</label>
                                                            <div class="col-sm-8">
                                                                <select class="form-control" name="physical_exams_other"
                                                                    id="physical_exams_other">
                                                                    <?php if ($patient['physical_exams_other'] == "1") { ?>
                                                                        <option value="<?= $patient['physical_exams_other'] ?>">
                                                                            Yes</option>
                                                                    <?php } elseif ($patient['physical_exams_other'] == "2") { ?>
                                                                        <option value="<?= $patient['physical_exams_other'] ?>">
                                                                            No</option>
                                                                    <?php } else { ?>
                                                                        <option value="">Select</option>
                                                                    <?php } ?>
                                                                    <option value="1">Yes</option>
                                                                    <option value="2">No</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center">
                                                            <label for="physical_other_specify"
                                                                class="col-sm-4 col-form-label">Other (specify):</label>
                                                            <div class="col-sm-8">
                                                                <input class="form-control"
                                                                    value="<?= $patient['physical_other_specify'] ?>"
                                                                    type="text" name="physical_other_specify"
                                                                    id="physical_other_specify" />
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center">
                                                            <label for="physical_other_system"
                                                                class="col-sm-4 col-form-label">Finding:</label>
                                                            <div class="col-sm-8">
                                                                <select class="form-control" name="physical_other_system"
                                                                    id="physical_other_system">
                                                                    <?php if ($patient['physical_other_system'] == "1") { ?>
                                                                        <option
                                                                            value="<?= $patient['physical_other_system'] ?>">
                                                                            Normal</option>
                                                                    <?php } elseif ($patient['physical_other_system'] == "2") { ?>
                                                                        <option
                                                                            value="<?= $patient['physical_other_system'] ?>">
                                                                            Abnormal</option>
                                                                    <?php } elseif ($patient['physical_other_system'] == "3") { ?>
                                                                        <option
                                                                            value="<?= $patient['physical_other_system'] ?>">Not
                                                                            examined</option>
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
                                                    <div class="col-sm-4 border">
                                                        <div class="form-group row align-items-center">
                                                            <label for="physical_other_comments"
                                                                class="col-sm-4 col-form-label">Comments:</label>
                                                            <div class="col-sm-8">
                                                                <input class="form-control"
                                                                    value="<?= $patient['physical_other_comments'] ?>"
                                                                    type="text" name="physical_other_comments"
                                                                    id="physical_other_comments" />
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center">
                                                            <label for="physical_other_signifcnt"
                                                                class="col-sm-4 col-form-label">Clinically
                                                                Significant?:</label>
                                                            <div class="col-sm-8">
                                                                <select class="form-control" name="physical_other_signifcnt"
                                                                    id="physical_other_signifcnt">
                                                                    <?php if ($patient['physical_other_signifcnt'] == "1") { ?>
                                                                        <option
                                                                            value="<?= $patient['physical_other_signifcnt'] ?>">
                                                                            Yes</option>
                                                                    <?php } elseif ($patient['physical_other_signifcnt'] == "2") { ?>
                                                                        <option
                                                                            value="<?= $patient['physical_other_signifcnt'] ?>">
                                                                            No</option>
                                                                    <?php } else { ?>
                                                                        <option value="">Select</option>
                                                                    <?php } ?>
                                                                    <option value="1">Yes</option>
                                                                    <option value="2">No</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 border">
                                                        <div class="form-group row align-items-center">
                                                            <label for="additional_notes"
                                                                class="col-sm-4 col-form-label">Additional Notes:</label>
                                                            <div class="col-sm-8">
                                                                <input class="form-control"
                                                                    value="<?= $patient['additional_notes'] ?>" type="text"
                                                                    name="additional_notes" id="additional_notes" />
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center">
                                                            <label for="physical_performed"
                                                                class="col-sm-4 col-form-label">Physical Examination
                                                                performed by:</label>
                                                            <div class="col-sm-8">
                                                                <input class="form-control"
                                                                    value="<?= $patient['physical_performed'] ?>"
                                                                    type="text" name="physical_performed"
                                                                    id="physical_performed" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group row" id="crf2_cmpltd_date">
                                                    <label for="crf2_cmpltd_date" class="col-md-3 col-form-label">Date of
                                                        Completion</label>
                                                    <div class="col-md-9">
                                                        <input class="form-control"
                                                            value="<?= $patient['crf2_cmpltd_date'] ?>" type="date"
                                                            name="crf2_cmpltd_date" id="crf2_cmpltd_date" />
                                                        <span class="form-text text-muted">example : 2000-08-28</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card-footer">
                                            <div class="form-group">
                                                <input type="hidden" name="id" value="<?= $patient['id'] ?>">
                                                <input type="submit" name="add_crf2" value="Submit" class="btn btn-info">
                                                <a href="index1.php" class="btn btn-default">Cancel</a>
                                            </div>
                                        </div>
                                    </form>
                                    <!-- Form End -->
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->
        <?php } elseif ($_GET['id'] == 10) { ?>
            <?php $patient = $override->get1('crf3', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode'])[0] ?>
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
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>Add New Position</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item">
                                        <a href="info.php?id=2">
                                            < Back </a>
                                    </li>&nbsp;&nbsp;
                                    <li class="breadcrumb-item"><a href="index1.php">Home</a></li>&nbsp;&nbsp;
                                    <li class="breadcrumb-item">
                                        <a href="info.php?id=2">
                                            Go to Position list >
                                        </a>
                                    </li>&nbsp;&nbsp;
                                    <li class="breadcrumb-item active">Add New Position</li>
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
                            // $staff = $override->getNews('user', 'status', 1, 'id', $_GET['staff_id'])[0];
                            // $site = $override->get('site', 'id', $staff['site_id'])[0];
                            $position = $override->get('position', 'id', $_GET['position_id'])[0];
                            ?>
                            <!-- right column -->
                            <div class="col-md-12">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title">CRF 3: SHORT-TERM QUESTIONNAIRE AT BASELINE AND FOLLOW-UP
                                        </h3>
                                    </div>
                                    <form id="crf3" method="post">
                                        <div class="card-body">
                                            <div class="row-form clearfix">
                                                <div class="col-md-3">Date:</div>
                                                <div class="col-md-9"><input value="<?= $patient['crf3_date'] ?>"
                                                        type="text" name="crf3_date" id="crf3_date" /> <span>Example:
                                                        2023-01-01</span>
                                                </div>
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
                                                            <select name="fever" id="fever" style="width: 100%;">
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
                                                            <select name="vomiting" id="vomiting" style="width: 100%;">
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
                                                            <label>C. Nausea:</label>
                                                            <select name="nausea" id="nausea" style="width: 100%;">
                                                                <?php if ($patient['nausea'] == "1") { ?>
                                                                    <option value="<?= $patient['nausea'] ?>">Yes</option>
                                                                <?php } elseif ($patient['nausea'] == "2") { ?>
                                                                    <option value="<?= $patient['nausea'] ?>">No</option>
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
                                                            <label>D. Diarrhoea:</label>
                                                            <select name="diarrhoea" id="diarrhoea" style="width: 100%;">
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
                                            </div>

                                            <div class="row">

                                                <div class="col-sm-3">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>E. Loss of appetite:</label>
                                                            <select name="loss_appetite" id="loss_appetite"
                                                                style="width: 100%;">
                                                                <?php if ($patient['loss_appetite'] == "1") { ?>
                                                                    <option value="<?= $patient['loss_appetite'] ?>">Yes
                                                                    </option>
                                                                <?php } elseif ($patient['loss_appetite'] == "2") { ?>
                                                                    <option value="<?= $patient['loss_appetite'] ?>">No</option>
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
                                                            <label>F. Headaches:</label>
                                                            <select name="headaches" id="headaches" style="width: 100%;">
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

                                                <div class="col-sm-3">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>G. Difficulty in breathing:</label>
                                                            <select name="difficult_breathing" id="difficult_breathing"
                                                                style="width: 100%;">
                                                                <?php if ($patient['difficult_breathing'] == "1") { ?>
                                                                    <option value="<?= $patient['difficult_breathing'] ?>">Yes
                                                                    </option>
                                                                <?php } elseif ($patient['difficult_breathing'] == "2") { ?>
                                                                    <option value="<?= $patient['difficult_breathing'] ?>">No
                                                                    </option>
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
                                                            <label>H. Sore throat:</label>
                                                            <select name="sore_throat" id="sore_throat"
                                                                style="width: 100%;">
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
                                            </div>


                                            <div class="row">
                                                <div class="col-sm-3">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>I. Fatigue:</label>
                                                            <select name="fatigue" id="fatigue" style="width: 100%;">
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
                                                            <label>J. Muscle pain:</label>
                                                            <select name="muscle_pain" id="muscle_pain"
                                                                style="width: 100%;">
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

                                                <div class="col-sm-3">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>K. Loss of consciousness:</label>
                                                            <select name="loss_consciousness" id="loss_consciousness"
                                                                style="width: 100%;">
                                                                <?php if ($patient['loss_consciousness'] == "1") { ?>
                                                                    <option value="<?= $patient['loss_consciousness'] ?>">Yes
                                                                    </option>
                                                                <?php } elseif ($patient['loss_consciousness'] == "2") { ?>
                                                                    <option value="<?= $patient['loss_consciousness'] ?>">No
                                                                    </option>
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
                                                            <label>L. Backpain:</label>
                                                            <select name="backpain" id="backpain" style="width: 100%;">
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

                                            </div>


                                            <div class="row">

                                                <div class="col-sm-3">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>M. Unexplained weight loss:</label>
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
                                                                    <option value="<?= $patient['heartburn_indigestion'] ?>">Yes
                                                                    </option>
                                                                <?php } elseif ($patient['heartburn_indigestion'] == "2") { ?>
                                                                    <option value="<?= $patient['heartburn_indigestion'] ?>">No
                                                                    </option>
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
                                                            <label>O. Swelling(changes of existing swelling):</label>
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
                                                            <label>P. Abnormal PV bleeding:</label>
                                                            <select name="pv_bleeding" id="pv_bleeding"
                                                                style="width: 100%;">
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

                                            </div>
                                            <div class="row">

                                                <div class="col-sm-3">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>Q. Abnormal PV discharge:</label>
                                                            <select name="pv_discharge" id="pv_discharge"
                                                                style="width: 100%;">
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
                                                            <label>R. Abnormal micitrition habits:</label>
                                                            <select name="micitrition" id="micitrition"
                                                                style="width: 100%;">
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


                                                <div class="col-sm-3">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>S. Convulsions:</label>
                                                            <select name="convulsions" id="convulsions"
                                                                style="width: 100%;">
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

                                                <div class="col-sm-3">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>T. Blood in urine:</label>
                                                            <select name="blood_urine" id="blood_urine"
                                                                style="width: 100%;">
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
                                                            <label>U. Other symptoms:</label>
                                                            <select name="symptoms_other" id="symptoms_other"
                                                                style="width: 100%;">
                                                                <?php if ($patient['symptoms_other'] == "1") { ?>
                                                                    <option value="<?= $patient['symptoms_other'] ?>">Yes
                                                                    </option>
                                                                <?php } elseif ($patient['symptoms_other'] == "2") { ?>
                                                                    <option value="<?= $patient['symptoms_other'] ?>">No
                                                                    </option>
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
                                                            <label>U. Specify:</label>
                                                            <input value="<?= $patient['symptoms_other_specify'] ?>"
                                                                type="text" name="symptoms_other_specify" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <?php if (!$_GET['vcode'] == "D0") { ?>

                                                <div class="head clearfix">
                                                    <div class="isw-ok"></div>
                                                    <h1>Drug adherence (To be asked on day 7,14,30,60,90,120) For patients on
                                                        NIMREGENIN only</h1>
                                                </div>

                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>1. Do you take NIMREGENIN as advised ie daily?:</label>
                                                                <select name="adherence" id="adherence" style="width: 100%;">
                                                                    <?php if ($patient['adherence'] == "1") { ?>
                                                                        <option value="<?= $patient['adherence'] ?>">Yes</option>
                                                                    <?php } elseif ($patient['adherence'] == "2") { ?>
                                                                        <option value="<?= $patient['adherence'] ?>">No</option>
                                                                    <?php } else { ?>
                                                                        <option value="">Select</option>
                                                                    <?php } ?>
                                                                    <option value="1">Yes</option>
                                                                    <option value="2">No</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-6" id="adherence_specify">
                                                        <div class="row-form clearfix">
                                                            <!-- select -->
                                                            <div class="form-group">
                                                                <label>1. Specify why:</label>
                                                                <input value="<?= $patient['adherence_specify'] ?>" type="text"
                                                                    name="adherence_specify" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>


                                            <div class="head clearfix">
                                                <div class="isw-ok"></div>
                                                <h1>For patients not on NIMREGENIN</h1>
                                            </div>

                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>2. Have you take any herbal medication?:</label>
                                                            <select name="herbal_medication" id="herbal_medication"
                                                                style="width: 100%;">
                                                                <?php if ($patient['herbal_medication'] == "1") { ?>
                                                                    <option value="<?= $patient['herbal_medication'] ?>">Yes
                                                                    </option>
                                                                <?php } elseif ($patient['herbal_medication'] == "2") { ?>
                                                                    <option value="<?= $patient['herbal_medication'] ?>">No
                                                                    </option>
                                                                <?php } else { ?>
                                                                    <option value="">Select</option>
                                                                <?php } ?>
                                                                <option value="1">Yes</option>
                                                                <option value="2">No</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-sm-6" id="herbal_ingredients">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>2. Specify type by name or ingredients:</label>
                                                            <input value="<?= $patient['herbal_ingredients'] ?>" type="text"
                                                                name="herbal_ingredients" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>U. Comments:</label>
                                                            <input value="<?= $patient['other_comments'] ?>" type="text"
                                                                name="other_comments" id="other_comments" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="row-form clearfix">
                                                        <!-- select -->
                                                        <div class="form-group">
                                                            <label>Date of Completion:</label>
                                                            <input value="<?= $patient['crf3_cmpltd_date'] ?>" type="text"
                                                                name="crf3_cmpltd_date" id="crf3_cmpltd_date" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card-footer">
                                            <div class="form-group">
                                                <input type="hidden" name="id" value="<?= $patient['id'] ?>">
                                                <input type="submit" name="add_crf3" value="Submit" class="btn btn-info">
                                                <a href="index1.php" class="btn btn-default">Cancel</a>
                                            </div>
                                        </div>
                                    </form>
                                    <!-- Form End -->
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->
        <?php } elseif ($_GET['id'] == 11) { ?>

            <?php $patient = $override->get1('crf4', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode'])[0] ?>
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
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>Add New Position</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item">
                                        <a href="info.php?id=2">
                                            < Back </a>
                                    </li>&nbsp;&nbsp;
                                    <li class="breadcrumb-item"><a href="index1.php">Home</a></li>&nbsp;&nbsp;
                                    <li class="breadcrumb-item">
                                        <a href="info.php?id=2">
                                            Go to Position list >
                                        </a>
                                    </li>&nbsp;&nbsp;
                                    <li class="breadcrumb-item active">Add New Position</li>
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
                            // $staff = $override->getNews('user', 'status', 1, 'id', $_GET['staff_id'])[0];
                            // $site = $override->get('site', 'id', $staff['site_id'])[0];
                            $position = $override->get('position', 'id', $_GET['position_id'])[0];
                            ?>
                            <!-- right column -->
                            <div class="col-md-12">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title">CRF 4
                                        </h3>
                                    </div>
                                    <form id="crf4" method="post">
                                        <!-- Blood Tests -->
                                        <div class="card card-primary">
                                            <div class="card-header">
                                                <h3 class="card-title">Blood Tests</h3>
                                            </div>
                                            <div class="card-body">

                                                <!-- Renal Function Test -->
                                                <div class="card card-secondary">
                                                    <div class="card-header">
                                                        <h3 class="card-title">1. Renal Function Test</h3>
                                                    </div>
                                                    <div class="card-body">

                                                        <div class="row">
                                                            <!-- Serum Creatinine Levels -->
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    <label for="renal_creatinine">Serum Creatinine
                                                                        Levels</label>
                                                                    <div class="input-group">
                                                                        <input type="text" class="form-control"
                                                                            name="renal_creatinine" id="renal_creatinine"
                                                                            value="<?= $patient['renal_creatinine'] ?>" />
                                                                        <div class="input-group-append">
                                                                            <span class="input-group-text">mg/dl</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Sample Date -->
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    <label for="sample_date">Date of Sample
                                                                        Collection</label>
                                                                    <input type="date" class="form-control"
                                                                        name="sample_date" id="sample_date"
                                                                        value="<?= $patient['sample_date'] ?>" />
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <!-- Serum Urea Levels -->
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    <label for="renal_urea">Serum Urea Levels</label>
                                                                    <div class="input-group">
                                                                        <input type="text" class="form-control"
                                                                            name="renal_urea" id="renal_urea"
                                                                            value="<?= $patient['renal_urea'] ?>"
                                                                            required />
                                                                        <div class="input-group-append">
                                                                            <span class="input-group-text">Units</span>
                                                                        </div>
                                                                    </div>
                                                                    <select class="form-control select2 mt-1"
                                                                        name="renal_urea_units" id="renal_urea_units"
                                                                        required>
                                                                        <option value=""
                                                                            <?= empty($patient['renal_urea_units']) ? 'selected' : '' ?>>Select Units</option>
                                                                        <option value="1"
                                                                            <?= $patient['renal_urea_units'] == "1" ? 'selected' : '' ?>>mg/dl</option>
                                                                        <option value="2"
                                                                            <?= $patient['renal_urea_units'] == "2" ? 'selected' : '' ?>>mmol/l</option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <!-- Creatinine Grade -->
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    <label for="renal_creatinine_grade">Grade</label>
                                                                    <select class="form-control select2"
                                                                        name="renal_creatinine_grade"
                                                                        id="renal_creatinine_grade">
                                                                        <option value=""
                                                                            <?= empty($patient['renal_creatinine_grade']) ? 'selected' : '' ?>>Select</option>
                                                                        <option value="0"
                                                                            <?= $patient['renal_creatinine_grade'] == "0" ? 'selected' : '' ?>>Zero</option>
                                                                        <option value="1"
                                                                            <?= $patient['renal_creatinine_grade'] == "1" ? 'selected' : '' ?>>One</option>
                                                                        <option value="2"
                                                                            <?= $patient['renal_creatinine_grade'] == "2" ? 'selected' : '' ?>>Two</option>
                                                                        <option value="3"
                                                                            <?= $patient['renal_creatinine_grade'] == "3" ? 'selected' : '' ?>>Three</option>
                                                                        <option value="4"
                                                                            <?= $patient['renal_creatinine_grade'] == "4" ? 'selected' : '' ?>>Four</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <!-- eGFR -->
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    <label for="renal_egfr">eGFR (mL/min per 1.73
                                                                        m²)</label>
                                                                    <div class="input-group">
                                                                        <input type="text" class="form-control"
                                                                            name="renal_egfr" id="renal_egfr"
                                                                            value="<?= $patient['renal_egfr'] ?>" />
                                                                        <div class="input-group-append">
                                                                            <span class="input-group-text">mL/min</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- eGFR Grade -->
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    <label for="renal_egfr_grade">Grade</label>
                                                                    <select class="form-control select2"
                                                                        name="renal_egfr_grade" id="renal_egfr_grade">
                                                                        <option value=""
                                                                            <?= empty($patient['renal_egfr_grade']) ? 'selected' : '' ?>>Select</option>
                                                                        <option value="0"
                                                                            <?= $patient['renal_egfr_grade'] == "0" ? 'selected' : '' ?>>Zero</option>
                                                                        <option value="1"
                                                                            <?= $patient['renal_egfr_grade'] == "1" ? 'selected' : '' ?>>One</option>
                                                                        <option value="2"
                                                                            <?= $patient['renal_egfr_grade'] == "2" ? 'selected' : '' ?>>Two</option>
                                                                        <option value="3"
                                                                            <?= $patient['renal_egfr_grade'] == "3" ? 'selected' : '' ?>>Three</option>
                                                                        <option value="4"
                                                                            <?= $patient['renal_egfr_grade'] == "4" ? 'selected' : '' ?>>Four</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>

                                            </div>
                                        </div>



                                        <!-- Liver Function Test -->
                                        <div class="card card-primary">
                                            <div class="card-header">
                                                <h3 class="card-title">2. Liver Function Test</h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <!-- AST levels -->
                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label for="liver_ast">AST levels</label>
                                                            <div class="input-group">
                                                                <input type="text" class="form-control" name="liver_ast"
                                                                    id="liver_ast" value="<?= $patient['liver_ast'] ?>" />
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">units/L</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- ALT levels -->
                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label for="liver_alt">ALT levels</label>
                                                            <div class="input-group">
                                                                <input type="text" class="form-control" name="liver_alt"
                                                                    id="liver_alt" value="<?= $patient['liver_alt'] ?>" />
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">units/L</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- ALP levels -->
                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label for="liver_alp">ALP levels</label>
                                                            <div class="input-group">
                                                                <input type="text" class="form-control" name="liver_alp"
                                                                    id="liver_alp" value="<?= $patient['liver_alp'] ?>" />
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">units/L</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <!-- PT -->
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label for="liver_pt">PT</label>
                                                            <div class="input-group">
                                                                <input type="text" class="form-control" name="liver_pt"
                                                                    id="liver_pt" value="<?= $patient['liver_pt'] ?>" />
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">units/L</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Grade -->
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label for="liver_pt_grade">Grade</label>
                                                            <select class="form-control select2" name="liver_pt_grade"
                                                                id="liver_pt_grade" style="width: 100%;">
                                                                <option value="" <?= empty($patient['liver_pt_grade']) ? 'selected' : '' ?>>Select</option>
                                                                <option value="0" <?= $patient['liver_pt_grade'] == "0" ? 'selected' : '' ?>>Zero</option>
                                                                <option value="1" <?= $patient['liver_pt_grade'] == "1" ? 'selected' : '' ?>>One</option>
                                                                <option value="2" <?= $patient['liver_pt_grade'] == "2" ? 'selected' : '' ?>>Two</option>
                                                                <option value="3" <?= $patient['liver_pt_grade'] == "3" ? 'selected' : '' ?>>Three</option>
                                                                <option value="4" <?= $patient['liver_pt_grade'] == "4" ? 'selected' : '' ?>>Four</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="liver_ptt">PTT</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" name="liver_ptt"
                                                            id="liver_ptt" value="<?= $patient['liver_ptt'] ?>">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">units/L</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="liver_ptt_grade">Grade</label>
                                                    <select class="form-control select2" name="liver_ptt_grade"
                                                        id="liver_ptt_grade">
                                                        <option value="">Select</option>
                                                        <option value="0" <?= $patient['liver_ptt_grade'] == "0" ? "selected" : "" ?>>Zero</option>
                                                        <option value="1" <?= $patient['liver_ptt_grade'] == "1" ? "selected" : "" ?>>One</option>
                                                        <option value="2" <?= $patient['liver_ptt_grade'] == "2" ? "selected" : "" ?>>Two</option>
                                                        <option value="3" <?= $patient['liver_ptt_grade'] == "3" ? "selected" : "" ?>>Three</option>
                                                        <option value="4" <?= $patient['liver_ptt_grade'] == "4" ? "selected" : "" ?>>Four</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="liver_inr">INR</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" name="liver_inr"
                                                            id="liver_inr" value="<?= $patient['liver_inr'] ?>">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">units/L</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="liver_inr_grade">Grade</label>
                                                    <select class="form-control select2" name="liver_inr_grade"
                                                        id="liver_inr_grade">
                                                        <option value="">Select</option>
                                                        <option value="0" <?= $patient['liver_inr_grade'] == "0" ? "selected" : "" ?>>Zero</option>
                                                        <option value="1" <?= $patient['liver_inr_grade'] == "1" ? "selected" : "" ?>>One</option>
                                                        <option value="2" <?= $patient['liver_inr_grade'] == "2" ? "selected" : "" ?>>Two</option>
                                                        <option value="3" <?= $patient['liver_inr_grade'] == "3" ? "selected" : "" ?>>Three</option>
                                                        <option value="4" <?= $patient['liver_inr_grade'] == "4" ? "selected" : "" ?>>Four</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="liver_ggt">GGT Levels</label>
                                                    <input type="text" class="form-control" name="liver_ggt" id="liver_ggt"
                                                        value="<?= $patient['liver_ggt'] ?>">
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="liver_albumin">Serum Albumin Levels</label>
                                                    <input type="text" class="form-control" name="liver_albumin"
                                                        id="liver_albumin" value="<?= $patient['liver_albumin'] ?>">
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="liver_bilirubin_total">Bilirubin Total</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" name="liver_bilirubin_total"
                                                            id="liver_bilirubin_total"
                                                            value="<?= $patient['liver_bilirubin_total'] ?>">
                                                        <select class="form-control select2"
                                                            name="liver_bilirubin_total_units" required>
                                                            <option value="">Select Units</option>
                                                            <option value="1"
                                                                <?= $patient['liver_bilirubin_total_units'] == "1" ? "selected" : "" ?>>micromol/L</option>
                                                            <option value="2"
                                                                <?= $patient['liver_bilirubin_total_units'] == "2" ? "selected" : "" ?>>mg/dL</option>
                                                            <option value="3"
                                                                <?= $patient['liver_bilirubin_total_units'] == "3" ? "selected" : "" ?>>grams/L</option>
                                                        </select>
                                                    </div>
                                                    <span class="text-danger" id="liver_bilirubin_totalError"></span>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="bilirubin_total_grade">Grade</label>
                                                    <select class="form-control select2" name="bilirubin_total_grade"
                                                        id="bilirubin_total_grade">
                                                        <option value="">Select</option>
                                                        <option value="0" <?= $patient['bilirubin_total_grade'] == "0" ? "selected" : "" ?>>Zero</option>
                                                        <option value="1" <?= $patient['bilirubin_total_grade'] == "1" ? "selected" : "" ?>>One</option>
                                                        <option value="2" <?= $patient['bilirubin_total_grade'] == "2" ? "selected" : "" ?>>Two</option>
                                                        <option value="3" <?= $patient['bilirubin_total_grade'] == "3" ? "selected" : "" ?>>Three</option>
                                                        <option value="4" <?= $patient['bilirubin_total_grade'] == "4" ? "selected" : "" ?>>Four</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="row">
                                            <!-- Bilirubin Direct Grade -->
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="bilirubin_direct_grade">Grade</label>
                                                    <select class="form-control select2" name="bilirubin_direct_grade"
                                                        id="bilirubin_direct_grade" style="width: 100%;">
                                                        <option value="">Select</option>
                                                        <option value="0" <?= $patient['bilirubin_direct_grade'] == "0" ? "selected" : "" ?>>Zero</option>
                                                        <option value="1" <?= $patient['bilirubin_direct_grade'] == "1" ? "selected" : "" ?>>One</option>
                                                        <option value="2" <?= $patient['bilirubin_direct_grade'] == "2" ? "selected" : "" ?>>Two</option>
                                                        <option value="3" <?= $patient['bilirubin_direct_grade'] == "3" ? "selected" : "" ?>>Three</option>
                                                        <option value="4" <?= $patient['bilirubin_direct_grade'] == "4" ? "selected" : "" ?>>Four</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- RBG Input and Units -->
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="rbg">RBG</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" name="rbg" id="rbg"
                                                            value="<?= $patient['rbg'] ?>" required>
                                                        <div class="input-group-append">
                                                            <select class="form-control select2" name="rbg_units" required>
                                                                <option value="">Select Units</option>
                                                                <option value="1" <?= $patient['rbg_units'] == "1" ? "selected" : "" ?>>mmol/L</option>
                                                                <option value="2" <?= $patient['rbg_units'] == "2" ? "selected" : "" ?>>mg/dL</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <small class="text-muted">XX</small>
                                                </div>
                                            </div>

                                            <!-- RBG Grade -->
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="rbg_grade">Grade</label>
                                                    <select class="form-control select2" name="rbg_grade" id="rbg_grade"
                                                        style="width: 100%;">
                                                        <option value="">Select</option>
                                                        <option value="0" <?= $patient['rbg_grade'] == "0" ? "selected" : "" ?>>Zero</option>
                                                        <option value="1" <?= $patient['rbg_grade'] == "1" ? "selected" : "" ?>>One</option>
                                                        <option value="2" <?= $patient['rbg_grade'] == "2" ? "selected" : "" ?>>Two</option>
                                                        <option value="3" <?= $patient['rbg_grade'] == "3" ? "selected" : "" ?>>Three</option>
                                                        <option value="4" <?= $patient['rbg_grade'] == "4" ? "selected" : "" ?>>Four</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="box box-primary">
                                            <div class="box-header with-border">
                                                <h3 class="box-title">Full blood count</h3>
                                            </div>

                                            <div class="box-body">
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <label for="hb">Hemoglobin levels (Hb)</label>
                                                            <input value="<?= $patient['hb'] ?>" type="text"
                                                                class="form-control" name="hb" id="hb" />
                                                            <span class="help-block">XX.X (mg/dl)</span>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <label for="hb_grade">Grade</label>
                                                            <select name="hb_grade" id="hb_grade" class="form-control">
                                                                <option value="0" <?= $patient['hb_grade'] == "0" ? 'selected' : '' ?>>Zero</option>
                                                                <option value="1" <?= $patient['hb_grade'] == "1" ? 'selected' : '' ?>>One</option>
                                                                <option value="2" <?= $patient['hb_grade'] == "2" ? 'selected' : '' ?>>Two</option>
                                                                <option value="3" <?= $patient['hb_grade'] == "3" ? 'selected' : '' ?>>Three</option>
                                                                <option value="4" <?= $patient['hb_grade'] == "4" ? 'selected' : '' ?>>Four</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <label for="hct">Hematocrit levels (Hct)</label>
                                                            <input value="<?= $patient['hct'] ?>" type="text"
                                                                class="form-control" name="hct" id="hct" />
                                                            <span class="help-block">XX ( % )</span>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <label for="rbc">Red blood cell count (RBC)</label>
                                                            <input value="<?= $patient['rbc'] ?>" type="text"
                                                                class="form-control" name="rbc" id="rbc" />
                                                            <span class="help-block">XXXXXXX (cells/microliter)</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <label for="wbc">White blood cell count (WBC)</label>
                                                            <input value="<?= $patient['wbc'] ?>" type="text"
                                                                class="form-control" name="wbc" id="wbc" />
                                                            <span class="help-block">XXXXXXX (cells/microliter)</span>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <label for="wbc_grade">Grade</label>
                                                            <select name="wbc_grade" id="wbc_grade" class="form-control">
                                                                <option value="0" <?= $patient['wbc_grade'] == "0" ? 'selected' : '' ?>>Zero</option>
                                                                <option value="1" <?= $patient['wbc_grade'] == "1" ? 'selected' : '' ?>>One</option>
                                                                <option value="2" <?= $patient['wbc_grade'] == "2" ? 'selected' : '' ?>>Two</option>
                                                                <option value="3" <?= $patient['wbc_grade'] == "3" ? 'selected' : '' ?>>Three</option>
                                                                <option value="4" <?= $patient['wbc_grade'] == "4" ? 'selected' : '' ?>>Four</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <label for="abs_lymphocytes">ABS Lymphocytes</label>
                                                            <input value="<?= $patient['abs_lymphocytes'] ?>" type="text"
                                                                class="form-control" name="abs_lymphocytes"
                                                                id="abs_lymphocytes" />
                                                            <span class="help-block">XXXXX</span>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <label for="abs_lymphocytes_grade">Grade</label>
                                                            <select name="abs_lymphocytes_grade" id="abs_lymphocytes_grade"
                                                                class="form-control">
                                                                <option value="0" <?= $patient['abs_lymphocytes_grade'] == "0" ? 'selected' : '' ?>>Zero</option>
                                                                <option value="1" <?= $patient['abs_lymphocytes_grade'] == "1" ? 'selected' : '' ?>>One</option>
                                                                <option value="2" <?= $patient['abs_lymphocytes_grade'] == "2" ? 'selected' : '' ?>>Two</option>
                                                                <option value="3" <?= $patient['abs_lymphocytes_grade'] == "3" ? 'selected' : '' ?>>Three</option>
                                                                <option value="4" <?= $patient['abs_lymphocytes_grade'] == "4" ? 'selected' : '' ?>>Four</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label for="plt">Platelet count (Plt)</label>
                                                            <input value="<?= $patient['plt'] ?>" type="text"
                                                                class="form-control" name="plt" id="plt" />
                                                            <span class="help-block">XXXXXX (cells/microliter)</span>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label for="plt_grade">Grade</label>
                                                            <select name="plt_grade" id="plt_grade" class="form-control">
                                                                <option value="0" <?= $patient['plt_grade'] == "0" ? 'selected' : '' ?>>Zero</option>
                                                                <option value="1" <?= $patient['plt_grade'] == "1" ? 'selected' : '' ?>>One</option>
                                                                <option value="2" <?= $patient['plt_grade'] == "2" ? 'selected' : '' ?>>Two</option>
                                                                <option value="3" <?= $patient['plt_grade'] == "3" ? 'selected' : '' ?>>Three</option>
                                                                <option value="4" <?= $patient['plt_grade'] == "4" ? 'selected' : '' ?>>Four</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>



                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>9. Cancer antigen 15-3</label>
                                                    <input value="<?= $patient['cancer'] ?>" type="text" name="cancer"
                                                        id="cancer" class="form-control" />
                                                    <small>XX ( U/ml )</small>
                                                </div>
                                            </div>

                                            <!-- PSA Section -->
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>9. PSA (Prostate specific antigen)</label>
                                                    <input value="<?= $patient['prostate'] ?>" type="text" name="prostate"
                                                        id="prostate" class="form-control" />
                                                    <small>XX ( ng/ml )</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>9. Chest X-ray</label>
                                                    <select name="chest_xray" id="chest_xray" class="form-control">
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

                                            <div class="col-sm-6" id="chest_specify">
                                                <div class="form-group">
                                                    <label>9. Specify (Report from Radiologist)</label>
                                                    <input value="<?= $patient['chest_specify'] ?>" type="text"
                                                        name="chest_specify" class="form-control" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>10. CT-Scan chest and abdomen report</label>
                                                    <select name="ct_chest" class="form-control" required>
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

                                            <div class="col-sm-6" id="ct_chest_specify">
                                                <div class="form-group">
                                                    <label>10. Specify (Report from Radiologist)</label>
                                                    <input value="<?= $patient['ct_chest_specify'] ?>" type="text"
                                                        name="ct_chest_specify" class="form-control" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>11. Abdominal Ultrasound report</label>
                                                    <select name="ultrasound" id="ultrasound" class="form-control">
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

                                            <div class="col-sm-6" id="ultrasound_specify">
                                                <div class="form-group">
                                                    <label>11. Specify (Report from Radiologist)</label>
                                                    <input value="<?= $patient['ultrasound_specify'] ?>" type="text"
                                                        name="ultrasound_specify" class="form-control" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row-form clearfix" id="crf4_cmpltd_date">
                                            <div class="col-md-3">Date of Completion</div>
                                            <input value="<?= $patient['crf4_cmpltd_date'] ?>" type="text"
                                                name="crf4_cmpltd_date" id="crf1_cmpltd_date" class="form-control" />
                                            <small>example : 2023-02-24</small>
                                        </div>


                                        <div class="card-footer">
                                            <div class="form-group">
                                                <input type="hidden" name="id" value="<?= $patient['id'] ?>">
                                                <input type="submit" name="add_crf4" value="Submit" class="btn btn-info">
                                                <a href="index1.php" class="btn btn-default">Cancel</a>
                                            </div>
                                        </div>
                                    </form>
                                    <!-- Form End -->
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->
        <?php } elseif ($_GET['id'] == 12) { ?>

            <?php $patient = $override->get1('crf5', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode'])[0] ?>
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
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>Add New Position</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item">
                                        <a href="info.php?id=2">
                                            < Back </a>
                                    </li>&nbsp;&nbsp;
                                    <li class="breadcrumb-item"><a href="index1.php">Home</a></li>&nbsp;&nbsp;
                                    <li class="breadcrumb-item">
                                        <a href="info.php?id=2">
                                            Go to Position list >
                                        </a>
                                    </li>&nbsp;&nbsp;
                                    <li class="breadcrumb-item active">Add New Position</li>
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
                            // $staff = $override->getNews('user', 'status', 1, 'id', $_GET['staff_id'])[0];
                            // $site = $override->get('site', 'id', $staff['site_id'])[0];
                            $position = $override->get('position', 'id', $_GET['position_id'])[0];
                            ?>
                            <!-- right column -->
                            <div class="col-md-12">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title">CRF 5: ADVERSE EVENT TRACKING LOG
                                        </h3>
                                    </div>
                                    <form id="crf5" method="post">

                                        <div class="card-body">
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
                                                                    <div class="col-md-9"><input value="<?= $st['date_reported'] ?>"
                                                                            class="validate[required,custom[date]]" type="text"
                                                                            name="date_reported" id="date_reported" required />
                                                                        <span>Example: 2023-01-01</span>
                                                                    </div>
                                                                    <?php
                                                                } else {
                                                                    ?>
                                                                    <div class="col-md-9"><input value=""
                                                                            class="validate[required,custom[date]]" type="text"
                                                                            name="date_reported" id="date_reported" required />
                                                                        <span>Example: 2023-01-01</span>
                                                                    </div>
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
                                                                    <textarea value="<?= $st['tdate'] ?>" name="ae_description"
                                                                        rows="4"></textarea>
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
                                                                    <input value="<?= $st['ae_category'] ?>" type="text"
                                                                        name="ae_category" id="ae_category" required />
                                                                    <?php
                                                                } else {
                                                                    ?>
                                                                    <input value="" type="text" name="ae_category" id="ae_category"
                                                                        required />
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                            <!-- <span>
                                                        <a href="http://safetyprofiler-ctep.nci.nih.gov/CTC/CTC.aspx" class="btn btn-info">
                                                            **lookup corresponding AE Category at: http://safetyprofiler-ctep.nci.nih.gov/CTC/CTC.aspx
                                                        </a>
                                                    </span> -->
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
                                                                    <input value="<?= $st['ae_start_date'] ?>" type="text"
                                                                        name="ae_start_date" id="ae_start_date" />
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
                                                            <select name="ae_ongoing" id="ae_ongoing" style="width: 100%;"
                                                                required>
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
                                                                    <input value="<?= $st['ae_end_date'] ?>" type="text"
                                                                        name="ae_end_date" id="ae_end_date" />
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
                                                            <select name="ae_outcome" id="ae_outcome" style="width: 100%;"
                                                                required>
                                                                <?php
                                                                $data = $override->get1('crf5', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                                foreach ($data as $st) {
                                                                    if ($st['ae_outcome'] == 0) { ?>
                                                                        <option value="<?= $st['ae_outcome'] ?>">Fatal</option>
                                                                    <?php } else if ($st['ae_outcome'] == 1) { ?>
                                                                            <option value="<?= $st['ae_outcome'] ?>">Intervention
                                                                                continues</option>
                                                                    <?php } else if ($st['ae_outcome'] == 2) { ?>
                                                                                <option value="<?= $st['ae_outcome'] ?>">Not recovered/not
                                                                                    resolved </option>
                                                                    <?php } else if ($st['ae_outcome'] == 3) { ?>
                                                                                    <option value="<?= $st['ae_outcome'] ?>">Recovered
                                                                                        w/sequelae</option>
                                                                    <?php } else if ($st['ae_outcome'] == 4) { ?>
                                                                                        <option value="<?= $st['ae_outcome'] ?>">Recovered w/o
                                                                                            sequelae</option>
                                                                    <?php } else if ($st['ae_outcome'] == 5) { ?>
                                                                                            <option value="<?= $st['ae_outcome'] ?>">Recovered/
                                                                                                Resolving</option>
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
                                                            <select name="ae_severity" id="ae_severity" style="width: 100%;"
                                                                required>
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
                                                                                    <option value="<?= $st['ae_severity'] ?>">Life-threatening
                                                                                    </option>
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
                                                            <select name="ae_serious" id="ae_serious" style="width: 100%;"
                                                                required>
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
                                                            <select name="ae_expected" id="ae_expected" style="width: 100%;"
                                                                required>
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
                                                            <select name="ae_treatment" id="ae_treatment"
                                                                style="width: 100%;" required>
                                                                <?php
                                                                $data = $override->get1('crf5', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                                foreach ($data as $st) {
                                                                    if ($st['ae_treatment'] == 1) { ?>
                                                                        <option value="<?= $st['ae_treatment'] ?>">Medication(s)
                                                                        </option>
                                                                    <?php } else if ($st['ae_treatment'] == 2) { ?>
                                                                            <option value="<?= $st['ae_treatment'] ?>">Non-medication TX
                                                                            </option>
                                                                    <?php } else if ($st['ae_treatment'] == 3) { ?>
                                                                                <option value="<?= $st['ae_treatment'] ?>">Subject
                                                                                    discontinued</option>
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
                                                            <select name="ae_taken" id="ae_taken" style="width: 100%;"
                                                                required>
                                                                <?php
                                                                $data = $override->get1('crf5', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                                foreach ($data as $st) {
                                                                    if ($st['ae_taken'] == 0) { ?>
                                                                        <option value="<?= $st['ae_taken'] ?>">Not Applicable
                                                                        </option>
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
                                                            <select name="ae_relationship" id="ae_relationship"
                                                                style="width: 100%;" required>
                                                                <?php
                                                                $data = $override->get1('crf5', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                                foreach ($data as $st) {
                                                                    if ($st['ae_relationship'] == 1) { ?>
                                                                        <option value="<?= $st['ae_relationship'] ?>">Unrelated
                                                                        </option>
                                                                    <?php } else if ($st['ae_relationship'] == 2) { ?>
                                                                            <option value="<?= $st['ae_relationship'] ?>">Unlikely
                                                                            </option>
                                                                    <?php } else if ($st['ae_relationship'] == 3) { ?>
                                                                                <option value="<?= $st['ae_relationship'] ?>">Possible
                                                                                </option>
                                                                    <?php } else if ($st['ae_relationship'] == 4) { ?>
                                                                                    <option value="<?= $st['ae_relationship'] ?>">Probable
                                                                                    </option>
                                                                    <?php } else if ($st['ae_relationship'] == 5) { ?>
                                                                                        <option value="<?= $st['ae_relationship'] ?>">Definite
                                                                                        </option>
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
                                                                    <input value="<?= $st['ae_staff_initial'] ?>" type="text"
                                                                        name="ae_staff_initial" id="ae_staff_initial" />
                                                                    <?php
                                                                } else {
                                                                    ?>
                                                                    <input value="" type="text" name="ae_staff_initial"
                                                                        id="ae_staff_initial" />
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
                                                                    <input value="<?= $st['ae_date'] ?>" type="text" name="ae_date"
                                                                        id="ae_date" />

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
                                        </div>
                                        <div class="card-footer">
                                            <div class="form-group">
                                                <input type="hidden" name="id" value="<?= $patient['id'] ?>">
                                                <input type="submit" name="add_crf5" value="Submit" class="btn btn-info">
                                                <a href="index1.php" class="btn btn-default">Cancel</a>
                                            </div>
                                        </div>
                                    </form>
                                    <!-- Form End -->
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->
        <?php } elseif ($_GET['id'] == 13) { ?>

            <?php $patient = $override->get1('crf6', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode'])[0] ?>
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
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>Add New Position</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item">
                                        <a href="info.php?id=2">
                                            < Back </a>
                                    </li>&nbsp;&nbsp;
                                    <li class="breadcrumb-item"><a href="index1.php">Home</a></li>&nbsp;&nbsp;
                                    <li class="breadcrumb-item">
                                        <a href="info.php?id=2">
                                            Go to Position list >
                                        </a>
                                    </li>&nbsp;&nbsp;
                                    <li class="breadcrumb-item active">Add New Position</li>
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
                            // $staff = $override->getNews('user', 'status', 1, 'id', $_GET['staff_id'])[0];
                            // $site = $override->get('site', 'id', $staff['site_id'])[0];
                            $position = $override->get('position', 'id', $_GET['position_id'])[0];
                            ?>
                            <!-- right column -->
                            <div class="col-md-12">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title">CRF 6: TERMINATION OF STUDY
                                        </h3>
                                    </div>
                                    <form id="crf6" method="post">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label for="today_date">1.a Today's date:</label>
                                                        <?php
                                                        $data = $override->get1('crf6', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                        if ($data) {
                                                            foreach ($data as $st) {
                                                                if ($st['today_date'] != "") {
                                                                    ?>
                                                                    <input value="<?= $st['today_date'] ?>" class="form-control"
                                                                        type="date" name="today_date" id="today_date" required />
                                                                    <span class="form-text text-muted">Example: 2023-01-01</span>
                                                                    <?php
                                                                }
                                                            }
                                                        } else {
                                                            ?>
                                                            <input value="" class="form-control" type="date" name="today_date"
                                                                id="today_date" required />
                                                            <span class="form-text text-muted">Example: 2023-01-01</span>
                                                            <?php
                                                        } ?>
                                                    </div>
                                                </div>

                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label for="terminate_date">1.b Date patient terminated the
                                                            study:</label>
                                                        <?php
                                                        $data = $override->get1('crf6', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                        if ($data) {
                                                            foreach ($data as $st) {
                                                                if ($st['terminate_date'] != "") {
                                                                    ?>
                                                                    <input value="<?= $st['terminate_date'] ?>" class="form-control"
                                                                        type="date" name="terminate_date" id="terminate_date"
                                                                        required />
                                                                    <span class="form-text text-muted">Example: 2023-01-01</span>
                                                                    <?php
                                                                }
                                                            }
                                                        } else {
                                                            ?>
                                                            <input value="" class="form-control" type="date"
                                                                name="terminate_date" id="terminate_date" required />
                                                            <span class="form-text text-muted">Example: 2023-01-01</span>
                                                            <?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label>2. Reason for study termination</label>
                                                    </div>
                                                </div>

                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label for="completed120days">2. a. Patient completed 120 days of
                                                            follow-up</label>
                                                        <select name="completed120days" id="completed120days"
                                                            class="form-control" required>
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

                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label for="reported_dead">2. b. Patient is reported/known to have
                                                            died</label>
                                                        <select name="reported_dead" id="reported_dead" class="form-control"
                                                            required>
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

                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label for="withdrew_consent">2. c. Patient withdrew consent to
                                                            participate</label>
                                                        <select name="withdrew_consent" id="withdrew_consent"
                                                            class="form-control" required>
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

                                            <div class="row" id="start_end_date">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label for="start_date">2.a.i Start date</label>
                                                        <?php
                                                        $data = $override->get1('crf6', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                        if ($data) {
                                                            foreach ($data as $st) {
                                                                if ($st['start_date'] != "") {
                                                                    ?>
                                                                    <input value="<?= $st['start_date'] ?>" class="form-control"
                                                                        type="date" name="start_date" id="start_date" required />
                                                                    <span class="form-text text-muted">Example: 2023-01-01</span>
                                                                    <?php
                                                                } else { ?>
                                                                    <input value="" class="form-control" type="date" name="start_date"
                                                                        id="start_date" />
                                                                    <span class="form-text text-muted">Example: 2023-01-01</span>
                                                                    <?php
                                                                }
                                                            }
                                                        } else {
                                                            ?>
                                                            <input value="" class="form-control" type="date" name="start_date"
                                                                id="start_date" required />
                                                            <span class="form-text text-muted">Example: 2023-01-01</span>
                                                            <?php
                                                        } ?>
                                                    </div>
                                                </div>

                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label for="end_date">2.a.ii End date:</label>
                                                        <?php
                                                        $data = $override->get1('crf6', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                        if ($data) {
                                                            foreach ($data as $st) {
                                                                if ($st['end_date'] != "") {
                                                                    ?>
                                                                    <input value="<?= $st['end_date'] ?>" class="form-control"
                                                                        type="date" name="end_date" id="end_date" required />
                                                                    <span class="form-text text-muted">Example: 2023-01-01</span>
                                                                    <?php
                                                                } else { ?>
                                                                    <input value="" class="form-control" type="date" name="end_date"
                                                                        id="end_date" />
                                                                    <span class="form-text text-muted">Example: 2023-01-01</span>
                                                                    <?php
                                                                }
                                                            }
                                                        } else {
                                                            ?>
                                                            <input value="" class="form-control" type="date" name="end_date"
                                                                id="end_date" />
                                                            <?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row" id="death_details">
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label for="date_death">2. b.i When was the date of death?</label>
                                                        <?php
                                                        $data = $override->get1('crf6', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                        if ($data) {
                                                            foreach ($data as $st) {
                                                                if ($st['date_death'] != "") {
                                                                    ?>
                                                                    <input value="<?= $st['date_death'] ?>" class="form-control"
                                                                        type="date" name="date_death" id="date_death" />
                                                                    <?php
                                                                }
                                                            }
                                                        } else {
                                                            ?>
                                                            <input value="" class="form-control" type="date" name="date_death"
                                                                id="date_death" />
                                                            <?php
                                                        } ?>
                                                    </div>
                                                </div>

                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label for="primary_cause">2. b.ii The primary cause of
                                                            death</label>
                                                        <?php
                                                        $data = $override->get1('crf6', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                        if ($data) {
                                                            foreach ($data as $st) {
                                                                if ($st['primary_cause'] != "") {
                                                                    ?>
                                                                    <textarea class="form-control" name="primary_cause"
                                                                        rows="4"><?= $st['primary_cause'] ?></textarea>
                                                                    <?php
                                                                }
                                                            }
                                                        } else {
                                                            ?>
                                                            <textarea class="form-control" name="primary_cause"
                                                                rows="4"></textarea>
                                                            <?php
                                                        } ?>
                                                    </div>
                                                </div>

                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label for="secondary_cause">2. b.iii The secondary cause of
                                                            death</label>
                                                        <?php
                                                        $data = $override->get1('crf6', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                        if ($data) {
                                                            foreach ($data as $st) {
                                                                if ($st['secondary_cause'] != "") {
                                                                    ?>
                                                                    <textarea class="form-control" name="secondary_cause"
                                                                        rows="4"><?= $st['secondary_cause'] ?></textarea>
                                                                    <?php
                                                                }
                                                            }
                                                        } else {
                                                            ?>
                                                            <textarea class="form-control" name="secondary_cause"
                                                                rows="4"></textarea>
                                                            <?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row" id="withdrew_reason1">
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label for="withdrew_reason">2. b). Reason for withdrawal</label>
                                                        <select name="withdrew_reason" id="withdrew_reason"
                                                            class="form-control">
                                                            <?php
                                                            $data = $override->get1('crf6', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                            foreach ($data as $st) {
                                                                if ($st['withdrew_reason'] == 1) { ?>
                                                                    <option value="<?= $st['withdrew_reason'] ?>">Unwilling to say
                                                                    </option>
                                                                <?php } else if ($st['withdrew_reason'] == 2) { ?>
                                                                        <option value="<?= $st['withdrew_reason'] ?>">Side effects of
                                                                            the herbal preparation (NIMRCAF/ Covidol / Bupiji )</option>
                                                                <?php } else if ($st['withdrew_reason'] == 3) { ?>
                                                                            <option value="<?= $st['withdrew_reason'] ?>">Side effects of
                                                                                Standard Care</option>
                                                                <?php } else if ($st['withdrew_reason'] == 4) { ?>
                                                                                <option value="<?= $st['withdrew_reason'] ?>">Moving to another
                                                                                    area</option>
                                                                <?php } else if ($st['withdrew_reason'] == 5) { ?>
                                                                                    <option value="<?= $st['withdrew_reason'] ?>">Other
                                                                                        {withdrew_other}</option>
                                                                <?php } else { ?>
                                                                                    <option value="">Select</option>
                                                                <?php }
                                                            } ?>
                                                            <option value="1">Unwilling to say</option>
                                                            <option value="2">Side effects of the herbal preparation
                                                                (NIMRCAF/ Covidol / Bupiji )</option>
                                                            <option value="3">Side effects of Standard Care</option>
                                                            <option value="4">Moving to another area</option>
                                                            <option value="5">Other {withdrew_other}</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-sm-12" id="withdrew_other">
                                                    <div class="form-group">
                                                        <label for="withdrew_other">2 d) Specify the reason</label>
                                                        <?php
                                                        $data = $override->get1('crf6', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                        if ($data) {
                                                            foreach ($data as $st) {
                                                                if ($st['withdrew_other'] != "") {
                                                                    ?>
                                                                    <textarea class="form-control" name="withdrew_other"
                                                                        rows="4"><?= $st['withdrew_other'] ?></textarea>
                                                                    <?php
                                                                }
                                                            }
                                                        } else {
                                                            ?>
                                                            <textarea class="form-control" name="withdrew_other"
                                                                rows="4"></textarea>
                                                            <?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label for="outcome">4. Outcome</label>
                                                        <select name="outcome" id="outcome" class="form-control" required>
                                                            <?php
                                                            $data = $override->get1('crf6', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                            foreach ($data as $st) {
                                                                if ($st['outcome'] == 1) { ?>
                                                                    <option value="<?= $st['outcome'] ?>">Recovered/Resolved
                                                                    </option>
                                                                <?php } else if ($st['outcome'] == 2) { ?>
                                                                        <option value="<?= $st['outcome'] ?>">Recovered with sequelae
                                                                        </option>
                                                                <?php } else if ($st['outcome'] == 3) { ?>
                                                                            <option value="<?= $st['outcome'] ?>">Severity worsened</option>
                                                                <?php } else if ($st['outcome'] == 4) { ?>
                                                                                <option value="<?= $st['outcome'] ?>">Recovering/Resolving at
                                                                                    the end of study</option>
                                                                <?php } else if ($st['outcome'] == 5) { ?>
                                                                                    <option value="<?= $st['outcome'] ?>">Not recovered/resolved at
                                                                                        the end of study</option>
                                                                <?php } else if ($st['outcome'] == 6) { ?>
                                                                                        <option value="<?= $st['outcome'] ?>">Unknown/Lost to follow up
                                                                                        </option>
                                                                <?php } else { ?>
                                                                                        <option value="">Select</option>
                                                                <?php }
                                                            } ?>
                                                            <option value="1">Recovered/Resolved</option>
                                                            <option value="2">Recovered with sequelae</option>
                                                            <option value="3">Severity worsened</option>
                                                            <option value="4">Recovering/Resolving at the end of study
                                                            </option>
                                                            <option value="5">Not recovered/resolved at the end of study
                                                            </option>
                                                            <option value="6">Unknown/Lost to follow up</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label for="outcome_date">5. Outcome date</label>
                                                        <?php
                                                        $data = $override->get1('crf6', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                        if ($data) {
                                                            foreach ($data as $st) {
                                                                if ($st['outcome_date'] != "") {
                                                                    ?>
                                                                    <input value="<?= $st['outcome_date'] ?>" class="form-control"
                                                                        type="date" name="outcome_date" id="outcome_date" />
                                                                    <?php
                                                                }
                                                            }
                                                        } else {
                                                            ?>
                                                            <input value="" class="form-control" type="date" name="outcome_date"
                                                                id="outcome_date" />
                                                            <?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label>6. Provide/summarise the adverse event</label>
                                                        <?php
                                                        $data = $override->get1('crf6', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                        if ($data) {
                                                            foreach ($data as $st) {
                                                                if ($st['summary'] != "") {
                                                                    ?>
                                                                    <textarea class="form-control" name="summary"
                                                                        rows="4"><?= $st['summary'] ?></textarea>
                                                                    <?php
                                                                }
                                                            }
                                                        } else {
                                                            ?>
                                                            <textarea class="form-control" name="summary" rows="4"></textarea>
                                                            <?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>7. Responsible Clinician Name</label>
                                                        <?php
                                                        $data = $override->get1('crf6', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                        if ($data) {
                                                            foreach ($data as $st) {
                                                                if ($st['clinician_name'] != "") {
                                                                    ?>
                                                                    <input value="<?= $st['clinician_name'] ?>" type="text"
                                                                        name="clinician_name" id="clinician_name"
                                                                        class="form-control" />
                                                                    <?php
                                                                } else { ?>
                                                                    <input value="" type="text" name="clinician_name"
                                                                        id="clinician_name" class="form-control" />
                                                                    <?php
                                                                }
                                                            }
                                                        } else {
                                                            ?>
                                                            <input value="" type="text" name="clinician_name"
                                                                id="clinician_name" class="form-control" />
                                                            <?php
                                                        } ?>
                                                    </div>
                                                </div>

                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Date of Completion</label>
                                                        <?php
                                                        $data = $override->get1('crf6', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                        if ($data) {
                                                            foreach ($data as $st) {
                                                                if ($st['crf6_cmpltd_date'] != "") {
                                                                    ?>
                                                                    <input value="<?= $st['crf6_cmpltd_date'] ?>" type="date"
                                                                        name="crf6_cmpltd_date" id="crf6_cmpltd_date"
                                                                        class="form-control" />
                                                                    <span class="form-text text-muted">Example: 2002-08-21</span>
                                                                    <?php
                                                                }
                                                            }
                                                        } else {
                                                            ?>
                                                            <input value="" type="date" name="crf6_cmpltd_date"
                                                                id="crf6_cmpltd_date" class="form-control" />
                                                            <span class="form-text text-muted">Example: 2002-08-21</span>
                                                            <?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <div class="form-group">
                                                <input type="hidden" name="id" value="<?= $patient['id'] ?>">
                                                <input type="submit" name="add_crf6" value="Submit" class="btn btn-info">
                                                <a href="index1.php" class="btn btn-default">Cancel</a>
                                            </div>
                                        </div>
                                    </form>
                                    <!-- Form End -->
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->
        <?php } elseif ($_GET['id'] == 15) { ?>

            <?php $patient = $override->get1('crf7', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode'])[0] ?>
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
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>Add New Position</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item">
                                        <a href="info.php?id=2">
                                            < Back </a>
                                    </li>&nbsp;&nbsp;
                                    <li class="breadcrumb-item"><a href="index1.php">Home</a></li>&nbsp;&nbsp;
                                    <li class="breadcrumb-item">
                                        <a href="info.php?id=2">
                                            Go to Position list >
                                        </a>
                                    </li>&nbsp;&nbsp;
                                    <li class="breadcrumb-item active">Add New Position</li>
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
                            // $staff = $override->getNews('user', 'status', 1, 'id', $_GET['staff_id'])[0];
                            // $site = $override->get('site', 'id', $staff['site_id'])[0];
                            $position = $override->get('position', 'id', $_GET['position_id'])[0];
                            ?>
                            <!-- right column -->
                            <div class="col-md-12">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title">CRF 7: Quality of Life Questionnaire
                                        </h3>
                                    </div>
                                    <form id="crf7" method="post">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="tdate">Tarehe ya Leo:</label>
                                                        <?php
                                                        $data = $override->get1('crf7', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                        foreach ($data as $st) {
                                                            if ($st['tdate'] != "") {
                                                                ?>
                                                                <input type="date" class="form-control" id="tdate" name="tdate"
                                                                    value="<?= $st['tdate'] ?>" placeholder="Example: 2023-01-01">
                                                                <?php
                                                            } else {
                                                                ?>
                                                                <input type="date" class="form-control" id="tdate" name="tdate"
                                                                    placeholder="Example: 2023-01-01">
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="mobility">A. Uwezo wa kutembea</label>
                                                        <select class="form-control select2" id="mobility" name="mobility"
                                                            style="width: 100%;">
                                                            <?php
                                                            $data = $override->get1('crf7', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                            foreach ($data as $st) {
                                                                if ($st['mobility'] == 1) { ?>
                                                                    <option value="<?= $st['mobility'] ?>">Sina tatizo katika
                                                                        kutembea</option>
                                                                <?php } else if ($st['mobility'] == 2) { ?>
                                                                        <option value="<?= $st['mobility'] ?>">Nina matatizo kiasi
                                                                            katika kutembea</option>
                                                                <?php } else if ($st['mobility'] == 3) { ?>
                                                                            <option value="<?= $st['mobility'] ?>">Siwezi kutembea kabisa
                                                                            </option>
                                                                <?php } else { ?>
                                                                            <option value="">Select</option>
                                                                <?php }
                                                            } ?>
                                                            <option value="1">Sina tatizo katika kutembea</option>
                                                            <option value="2">Nina matatizo kiasi katika kutembea</option>
                                                            <option value="3">Siwezi kutembea kabisa</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="self_care">B. Uwezo wa kujihudumia</label>
                                                        <select class="form-control select2" id="self_care" name="self_care"
                                                            style="width: 100%;">
                                                            <?php
                                                            $data = $override->get1('crf7', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                            foreach ($data as $st) {
                                                                if ($st['self_care'] == 1) { ?>
                                                                    <option value="<?= $st['self_care'] ?>">Sina tatizo kujihudumia
                                                                        mwenyewe</option>
                                                                <?php } else if ($st['self_care'] == 2) { ?>
                                                                        <option value="<?= $st['self_care'] ?>">Nina matatizo kiasi
                                                                            katika kujisafisha au kuvaa mwenyewe</option>
                                                                <?php } else if ($st['self_care'] == 3) { ?>
                                                                            <option value="<?= $st['self_care'] ?>">Siwezi kujisafisha wala
                                                                                kuvaa mwenyewe</option>
                                                                <?php } else { ?>
                                                                            <option value="">Select</option>
                                                                <?php }
                                                            } ?>
                                                            <option value="1">Sina tatizo kujihudumia mwenyewe</option>
                                                            <option value="2">Nina matatizo kiasi katika kujisafisha au
                                                                kuvaa mwenyewe</option>
                                                            <option value="3">Siwezi kujisafisha wala kuvaa mwenyewe
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="usual_active">C. Shughuli za kila siku</label>
                                                        <select class="form-control select2" id="usual_active"
                                                            name="usual_active" style="width: 100%;">
                                                            <?php
                                                            $data = $override->get1('crf7', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                            foreach ($data as $st) {
                                                                if ($st['usual_active'] == 1) { ?>
                                                                    <option value="<?= $st['usual_active'] ?>">Sina tatizo katika
                                                                        kufanya shughuli zangu za kila siku</option>
                                                                <?php } else if ($st['usual_active'] == 2) { ?>
                                                                        <option value="<?= $st['usual_active'] ?>">Nina matatizo kiasi
                                                                            katika kufanya shughuli zangu za kila siku</option>
                                                                <?php } else if ($st['usual_active'] == 3) { ?>
                                                                            <option value="<?= $st['usual_active'] ?>">Siwezi kabisa kufanya
                                                                                shughuli zangu za kila siku</option>
                                                                <?php } else { ?>
                                                                            <option value="">Select</option>
                                                                <?php }
                                                            } ?>
                                                            <option value="1">Sina tatizo katika kufanya shughuli zangu za
                                                                kila siku</option>
                                                            <option value="2">Nina matatizo kiasi katika kufanya shughuli
                                                                zangu za kila siku</option>
                                                            <option value="3">Siwezi kabisa kufanya shughuli zangu za kila
                                                                siku</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="pain">D. Maumivu/Kutojisikia vizuri</label>
                                                        <select class="form-control select2" id="pain" name="pain"
                                                            style="width: 100%;">
                                                            <?php
                                                            $data = $override->get1('crf7', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                            foreach ($data as $st) {
                                                                if ($st['pain'] == 1) { ?>
                                                                    <option value="<?= $st['pain'] ?>">Sina maumivu au najisikia
                                                                        vizuri</option>
                                                                <?php } else if ($st['pain'] == 2) { ?>
                                                                        <option value="<?= $st['pain'] ?>">Nina maumivu kiasi au
                                                                            najisikia vibaya kiasi</option>
                                                                <?php } else if ($st['pain'] == 3) { ?>
                                                                            <option value="<?= $st['pain'] ?>">Nina maumivu makali au
                                                                                najisikia vibaya sana</option>
                                                                <?php } else { ?>
                                                                            <option value="">Select</option>
                                                                <?php }
                                                            } ?>
                                                            <option value="1">Sina maumivu au najisikia vizuri</option>
                                                            <option value="2">Nina maumivu kiasi au najisikia vibaya kiasi
                                                            </option>
                                                            <option value="3">Nina maumivu makali au najisikia vibaya sana
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="anxiety">E. Wasiwasi/sonona</label>
                                                        <select class="form-control select2" id="anxiety" name="anxiety"
                                                            style="width: 100%;">
                                                            <?php
                                                            $data = $override->get1('crf7', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                            foreach ($data as $st) {
                                                                if ($st['anxiety'] == 1) { ?>
                                                                    <option value="<?= $st['anxiety'] ?>">Sina wasiwasi au sonona
                                                                    </option>
                                                                <?php } else if ($st['anxiety'] == 2) { ?>
                                                                        <option value="<?= $st['anxiety'] ?>">Nina wasiwasi kiasi au
                                                                            sonona kiasi</option>
                                                                <?php } else if ($st['anxiety'] == 3) { ?>
                                                                            <option value="<?= $st['anxiety'] ?>">Nina wasiwasi sana au nina
                                                                                sonona sana</option>
                                                                <?php } else { ?>
                                                                            <option value="">Select</option>
                                                                <?php }
                                                            } ?>
                                                            <option value="1">Sina wasiwasi au sonona</option>
                                                            <option value="2">Nina wasiwasi kiasi au sonona kiasi</option>
                                                            <option value="3">Nina wasiwasi sana au nina sonona sana
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card-header">
                                                <h3 class="card-title">ON-SITE MONITORING</h3>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="FDATE">DATE FORM COMPLETED:</label>
                                                        <?php
                                                        $data = $override->get1('crf7', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                        foreach ($data as $st) {
                                                            if ($st['FDATE'] != "") {
                                                                ?>
                                                                <input type="date" class="form-control" id="FDATE" name="FDATE"
                                                                    value="<?= $st['FDATE'] ?>" placeholder="Example: 2023-01-01">
                                                                <?php
                                                            } else {
                                                                ?>
                                                                <input type="date" class="form-control" id="FDATE" name="FDATE"
                                                                    placeholder="Example: 2023-01-01">
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="cpersid">NAME OF PERSON CHECKING FORM:</label>
                                                        <?php
                                                        $data = $override->get1('crf7', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                        foreach ($data as $st) {
                                                            if ($st['cpersid'] != "") {
                                                                ?>
                                                                <input type="text" class="form-control" id="cpersid" name="cpersid"
                                                                    value="<?= $st['cpersid'] ?>">
                                                                <?php
                                                            } else {
                                                                ?>
                                                                <input type="text" class="form-control" id="cpersid" name="cpersid">
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="cDATE">DATE FORM CHECKED:</label>
                                                        <?php
                                                        $data = $override->get1('crf7', 'patient_id', $_GET['cid'], 'vcode', $_GET['vcode']);
                                                        foreach ($data as $st) {
                                                            if ($st['cDATE'] != "") {
                                                                ?>
                                                                <input type="date" class="form-control" id="cDATE" name="cDATE"
                                                                    value="<?= $st['cDATE'] ?>" placeholder="Example: 2023-01-01">
                                                                <?php
                                                            } else {
                                                                ?>
                                                                <input type="date" class="form-control" id="cDATE" name="cDATE"
                                                                    placeholder="Example: 2023-01-01">
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card-footer">
                                            <div class="form-group">
                                                <input type="hidden" name="id" value="<?= $patient['id'] ?>">
                                                <input type="submit" name="add_crf7" value="Submit" class="btn btn-info">
                                                <a href="index1.php" class="btn btn-default">Cancel</a>
                                            </div>
                                        </div>
                                    </form>
                                    <!-- Form End -->
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->
        <?php } ?>
        <!-- footer -->
        <?php include 'footer.php'; ?>
        <!-- footer -->


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

    <!-- Staff Signs Js -->
    <script src="myjs/add/staff/staff.js"></script>

    <!-- Clients Signs Js -->
    <script src="myjs/add/clients/clients.js"></script>
    <script src="myjs/add/clients/relation.js"></script>

    <!-- demographic Js -->
    <script src="myjs/add/demographic/chw.js"></script>
    <script src="myjs/add/demographic/referred.js"></script>


    <!-- Vital Signs Js -->
    <script src="myjs/add/vital/vital.js"></script>

    <!-- main_diagnosis Signs Js -->
    <script src="myjs/add/main_diagnosis/main_diagnosis.js"></script>

    <!-- Medications Js -->
    <script src="myjs/add/medications/basal_changed.js"></script>
    <script src="myjs/add/medications/prandial_changed.js"></script>
    <script src="myjs/add/medications/fluid_restriction.js"></script>
    <script src="myjs/add/medications/support.js"></script>
    <script src="myjs/add/medications/cardiology.js"></script>
    <script src="myjs/add/medications/referrals.js"></script>
    <script src="myjs/add/medications/social_support.js"></script>
    <script src="myjs/add/medications/transfusion.js"></script>
    <script src="myjs/add/medications/vaccination.js"></script>
    <script src="myjs/add/medications/completed.js"></script>
    <!-- <script src="myjs/add/medications/medication.js"></script> -->
    <!-- <script src="myjs/add/medications/medication2.js"></script> -->



    <!-- History Js -->
    <script src="myjs/add/history/active_smoker.js"></script>
    <script src="myjs/add/history/cardiovascular.js"></script>
    <script src="myjs/add/history/retinopathy.js"></script>
    <script src="myjs/add/history/alcohol.js"></script>
    <script src="myjs/add/history/alcohol_type.js"></script>
    <script src="myjs/add/history/art.js"></script>
    <script src="myjs/add/history/blood_transfusion.js"></script>
    <script src="myjs/add/history/hepatitis.js"></script>
    <script src="myjs/add/history/history_other.js"></script>
    <script src="myjs/add/history/hiv.js"></script>
    <script src="myjs/add/history/neuropathy.js"></script>
    <script src="myjs/add/history/other_complication.js"></script>
    <script src="myjs/add/history/pvd.js"></script>
    <script src="myjs/add/history/renal.js"></script>
    <script src="myjs/add/history/sexual_dysfunction.js"></script>
    <script src="myjs/add/history/smoking.js"></script>
    <script src="myjs/add/history/stroke_tia.js"></script>
    <script src="myjs/add/history/surgery.js"></script>
    <script src="myjs/add/history/surgery_type.js"></script>
    <script src="myjs/add/history/tb.js"></script>
    <script src="myjs/add/history/type_smoked.js"></script>



    <!-- Symptoms Js -->


    <script src="myjs/add/symptoms/abnorminal_pain.js"></script>
    <script src="myjs/add/symptoms/chest_pain.js"></script>
    <script src="myjs/add/symptoms/foot_exam.js"></script>
    <script src="myjs/add/symptoms/foot_exam_finding.js"></script>
    <script src="myjs/add/symptoms/headache.js"></script>
    <script src="myjs/add/symptoms/hypoglycemia_severe.js"></script>
    <script src="myjs/add/symptoms/joints.js"></script>
    <script src="myjs/add/symptoms/lower_arms.js"></script>
    <script src="myjs/add/symptoms/lungs.js"></script>
    <script src="myjs/add/symptoms/other_pain.js"></script>
    <script src="myjs/add/symptoms/other_symptoms.js"></script>
    <script src="myjs/add/symptoms/upper_arms.js"></script>
    <script src="myjs/add/symptoms/waist.js"></script>
    <script src="myjs/add/symptoms/other_lab.js"></script>

    <!-- Results Js -->

    <script src="myjs/add/results/confirmatory_test.js"></script>
    <script src="myjs/add/results/ecg.js"></script>
    <script src="myjs/add/results/ecg_performed.js"></script>
    <script src="myjs/add/results/echo_other.js"></script>
    <script src="myjs/add/results/echo_performed.js"></script>
    <script src="myjs/add/results/scd_done.js"></script>
    <script src="myjs/add/results/scd_test.js"></script>


    <!-- hospitalizations Js -->

    <script src="myjs/add/hospitalizations/hospitalizations.js"></script>
    <script src="myjs/add/hospitalizations/hydroxyurea.js"></script>
    <script src="myjs/add/hospitalizations/injection_sites.js"></script>
    <script src="myjs/add/hospitalizations/opioid.js"></script>
    <script src="myjs/add/hospitalizations/ncd_hospitalizations.js"></script>
    <script src="myjs/add/hospitalizations"></script>


    <!-- hospitalization_details Js -->

    <script src="myjs/add/hospitalization_details/hospitalization_ncd.js"></script>

    <!-- Diagnosis, Complications & Comorbidities Js -->

    <script src="myjs/add/diagnosis_complications_comorbidities/diagns_changed.js"></script>
    <script src="myjs/add/diagnosis_complications_comorbidities/diagns_specify.js"></script>
    <script src="myjs/add/diagnosis_complications_comorbidities/new_complications.js"></script>
    <script src="myjs/add/diagnosis_complications_comorbidities/new_ncd_diagnosis.js"></script>
    <script src="myjs/add/diagnosis_complications_comorbidities/other_complications.js"></script>
    <script src="myjs/add/diagnosis_complications_comorbidities"></script>


    <!-- RISKS Js -->

    <script src="myjs/add/risks/risk_art.js"></script>
    <script src="myjs/add/risks/risk_hiv.js"></script>
    <script src="myjs/add/risks/risk_tb.js"></script>
    <script src="myjs/add/risks"></script>


    <!-- LAB DETAILS Js -->

    <script src="myjs/add/lab_details/cardiac_surgery.js"></script>
    <script src="myjs/add/lab_details/chemistry_test.js"></script>
    <script src="myjs/add/lab_details/chemistry_test2.js"></script>
    <script src="myjs/add/lab_details/hematology_test.js"></script>
    <script src="myjs/add/lab_details/hematology_test.js"></script>
    <script src="myjs/add/lab_details/lab_Other.js"></script>
    <script src="myjs/add/lab_details/other_lab_diabetes.js"></script>

    <!-- CARDIACS Js -->

    <script src="myjs/add/cardiac/arrhythmia.js"></script>
    <script src="myjs/add/cardiac/cardiomyopathy.js"></script>
    <script src="myjs/add/cardiac/congenital.js"></script>
    <script src="myjs/add/cardiac/diagnosis_other.js"></script>
    <script src="myjs/add/cardiac/heart_failure.js"></script>
    <script src="myjs/add/cardiac/heumatic.js"></script>
    <script src="myjs/add/cardiac/pericardial.js"></script>
    <script src="myjs/add/cardiac/stroke.js"></script>
    <script src="myjs/add/cardiac/sub_arrhythmia.js"></script>
    <script src="myjs/add/cardiac/sub_cardiomyopathy.js"></script>
    <script src="myjs/add/cardiac/sub_congenital.js"></script>
    <script src="myjs/add/cardiac/sub_heumatic.js"></script>
    <script src="myjs/add/cardiac/sub_pericardial.js"></script>
    <script src="myjs/add/cardiac/sub_thromboembolic.js"></script>
    <script src="myjs/add/cardiac/thromboembolic.js"></script>

    <!-- DIABETIC Js -->

    <script src="myjs/add/diabetic/diagnosis_other.js"></script>
    <script src="myjs/add/diabetic/hypertension.js"></script>

    <!-- SICKLE CELL Js -->

    <script src="myjs/add/sickle_cell/diagnosis.js"></script>

    <!-- SUMMARY Js -->

    <script src="myjs/add/economics/transport_mode.js"></script>
    <script src="myjs/add/economics"></script>
    <script src="myjs/add/economics"></script>
    <script src="myjs/add/economics"></script>
    <script src="myjs/add/economics"></script>
    <script src="myjs/add/economics"></script>
    <script src="myjs/add/economics"></script>
    <script src="myjs/add/economics"></script>
    <script src="myjs/add/economics"></script>
    <script src="myjs/add/economics"></script>
    <script src="myjs/add/economics"></script>
    <script src="myjs/add/economics"></script>
    <script src="myjs/add/economics"></script>
    <script src="myjs/add/economics"></script>
    <script src="myjs/add/economics"></script>

    <!-- TREATMENT PALN Js -->

    <script src="myjs/add/treatment/basal.js"></script>
    <script src="myjs/add/treatment/cardiology.js"></script>
    <script src="myjs/add/treatment/other_support.js"></script>
    <script src="myjs/add/treatment/prandial.js"></script>
    <script src="myjs/add/treatment/referral.js"></script>
    <script src="myjs/add/treatment/restriction.js"></script>
    <script src="myjs/add/treatment/support.js"></script>
    <script src="myjs/add/treatment/transfusion.js"></script>
    <script src="myjs/add/treatment/vaccination.js"></script>
    <script src="myjs/add/treatment"></script>
    <script src="myjs/add/treatment"></script>
    <script src="myjs/add/treatment"></script>
    <script src="myjs/add/treatment"></script>
    <script src="myjs/add/treatment"></script>
    <script src="myjs/add/treatment"></script>
    <script src="myjs/add/treatment"></script>
    <script src="myjs/add/treatment"></script>
    <script src="myjs/add/treatment"></script>
    <script src="myjs/add/treatment"></script>
    <script src="myjs/add/treatment"></script>



    <!-- SUMMARY Js -->

    <script src="myjs/add/summary/cause_death.js"></script>
    <script src="myjs/add/summary/diagnosis_summary.js"></script>
    <script src="myjs/add/summary/outcome.js"></script>
    <script src="myjs/add/summary/transfer_out.js"></script>
    <script src="myjs/add/summary/set_next.js"></script>



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


            $('#regions_id').change(function () {
                var region_id = $(this).val();
                $.ajax({
                    url: "process.php?content=region_id",
                    method: "GET",
                    data: {
                        region_id: region_id
                    },
                    dataType: "text",
                    success: function (data) {
                        $('#districts_id').html(data);
                    }
                });
            });

            $('#region').change(function () {
                var region = $(this).val();
                $.ajax({
                    url: "process.php?content=region_id",
                    method: "GET",
                    data: {
                        region_id: region
                    },
                    dataType: "text",
                    success: function (data) {
                        $('#district').html(data);
                    }
                });
            });

            $('#district').change(function () {
                var district_id = $(this).val();
                $.ajax({
                    url: "process.php?content=district_id",
                    method: "GET",
                    data: {
                        district_id: district_id
                    },
                    dataType: "text",
                    success: function (data) {
                        $('#ward').html(data);
                    }
                });
            });

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


        var items = 0;

        function add_Medication() {
            items++;

            var html = "<tr>";
            html += '<td><input type="hidden" name="medication[]" value=""></td>';
            // html += "<td>" + items + "</td>";
            html += "<td><?= $_GET['vday']; ?></td>";
            html += '<td><input class="form-control"  type="date" name="date[]" value=""  max="<?= date('Y-m-d') ?>" required></td>';
            html += '<td><input class="form-control"  type="date" name="start_date[]" value=""></td>';
            html += '<td><select class="form-control select2" name="medication_id[]" id="medication_id[]" style="width: 100%;" required><option value="">Select</option><?php foreach ($override->get('medications', 'status', 1) as $medication) { ?><option value="<?= $medication['id']; ?>"><?= $medication['name']; ?></option> <?php } ?></select></td>';
            // html += '<td><select class="form-control select2" name="batch_id[]" id="batch_id[]" style="width: 100%;" required><option value="">Select</option><?php foreach ($override->get('batch', 'status', 1) as $batch) { ?><option value="<?= $batch['id']; ?>"><?= $override->getNews('medications', 'status', 1, 'id', $batch['medication_id'])[0]['name'] . ' - ( ' . $batch['serial_name'] . ' ) : ( ' . $batch['amount'] . ' )'; ?></option> <?php } ?></select></td>';
            html += '<td><select class="form-control" name="medication_action[]" id="medication_action[]" style="width: 100%;" required><option value="">Select</option><option value="1">Continue</option><option value="2">Start</option><option value="3">Stop</option><option value="4">Not Eligible</option></select></td>';
            html += '<textarea class="form-control" name="medication_units" id="medication_units" rows="3" placeholder="Type medication dose here..." required></textarea>'
            html += '<td><input class="form-control"  min="0" max="10000" type="number" name="medication_dose[]" value="" required></td>';
            html += '<td><input class="form-control"  type="date" name="end_date[]" value=""></td>';
            html += "<td><button type='button' onclick='deleteRow(this);'>Remove</button></td>"
            html += "</tr>";



            var row = document.getElementById("tbody").insertRow();
            row.innerHTML = html;
        }

        function deleteRow(button) {
            items--
            button.parentElement.parentElement.remove();
            // first parentElement will be td and second will be tr.
        }

        var items2 = 0;

        function add_Admission() {
            items2++;

            var html = "<tr>";
            html += '<td><input type="hidden" name="admission[]" value=""></td>';
            // html += "<td>" + items2 + "</td>";
            html += "<td><?= $_GET['vday']; ?></td>";
            html += '<td><input class="form-control" type="date" name="entry_date[]" value="" max="<?= date('Y-m-d') ?>" required></td>';
            html += '<td><input class="form-control" type="date" name="admission_date[]" value="" required></td>';
            html += '<td><textarea class="form-control"  type="text" name="admission_reason[]" rows="3" placeholder="Type admission reason here..." required></textarea></td>';
            html += '<td><textarea class="form-control"  type="text" name="discharge_diagnosis[]" rows="3" placeholder="Type Discharge diagnosis here..." required></textarea></td>';
            html += '<td><input class="form-control"  type="date" name="discharge_date[]" value=""></td>';
            html += "<td><button type='button' onclick='deleteRow2(this);'>Remove</button></td>"
            html += "</tr>";

            var row = document.getElementById("tbody_2").insertRow();
            row.innerHTML = html;
        }

        function deleteRow2(button) {
            items--
            button.parentElement.parentElement.remove();
            // first parentElement will be td and second will be tr.
        }


        var items3 = 0;

        function add_Siblings() {
            items3++;

            var html = "<tr>";
            html += '<td><input type="hidden" name="sibling[]" value=""></td>';
            // html += "<td>" + items3 + "</td>";
            html += "<td><?= $_GET['vday']; ?></td>";
            html += '<td><input class="form-control" type="date" name="entry_date[]" value="" max="<?= date('Y-m-d') ?>" required></td>';
            html += '<td><input class="form-control" type="number" name="age[]" value="" min="0" max="100" required></td>';
            html += '<td><select class="form-control" name="sex[]" id="sex[]" style="width: 100%;" required><option value="">Select</option><option value="1">Male</option><option value="2">Female</option></select></td>';
            html += '<td><select class="form-control" name="sickle_status[]" id="sickle_status[]" style="width: 100%;" required><option value="">Select</option><option value="1">Positive</option><option value="2">Negative</option><option value="99">Unknown</option><option value="96">Other</option></select></td>';
            html += '<td><textarea class="form-control"  type="text" name="other[]" rows="3" placeholder="Type other here..."></textarea></td>';
            html += "<td><button type='button' onclick='deleteRow3(this);'><ion-icon name='remove-circle-outline'></ion-icon>Remove</button></td>"
            html += "</tr>";

            var row = document.getElementById("tbody_3").insertRow();
            row.innerHTML = html;
        }

        function deleteRow3(button) {
            items--
            button.parentElement.parentElement.remove();
            // first parentElement will be td and second will be tr.
        }

        // $(document).ready(function() {

        // });
    </script>

    <script>
        function fillUpdateModal(data) {
            document.querySelector('#updateModal [name="id"]').value = data.id;
            document.querySelector('#updateModal [name="nimregenin_preparation"]').value = data.nimregenin_preparation;
            document.querySelector('#updateModal [name="nimregenin_start"]').value = data.nimregenin_start;
            document.querySelector('#updateModal [name="nimregenin_ongoing"]').value = data.nimregenin_ongoing;
            document.querySelector('#updateModal [name="nimregenin_end"]').value = data.nimregenin_end;
            document.querySelector('#updateModal [name="nimregenin_dose"]').value = data.nimregenin_dose;
            document.querySelector('#updateModal [name="nimregenin_frequency"]').value = data.nimregenin_frequency;
            document.querySelector('#updateModal [name="nimregenin_remarks"]').value = data.nimregenin_remarks;
        }
        function setDeleteId(id) {
            document.querySelector('#deleteModal [name="id"]').value = id;
        }
    </script>


    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>

</html>

</html>