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
                $errorM = false;
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
                    if ($errorM == false) {
                        $chk = true;
                        $screening_id = $random->get_rand_alphanumeric(8);
                        $check_screening = $override->get('clients', 'participant_id', $screening_id)[0];
                        while ($chk) {
                            $screening_id = strtoupper($random->get_rand_alphanumeric(8));
                            if (!$check_screening = $override->get('clients', 'participant_id', $screening_id)) {
                                $chk = false;
                            }
                        }
                        $age = $user->dateDiffYears(date('Y-m-d'), Input::get('dob'));
                        $check_clients = $override->countData1('clients', 'firstname', Input::get('firstname'), 'middlename', Input::get('middlename'), 'lastname', Input::get('lastname'));

                        if ($check_clients >= 1) {
                            $errorMessage = 'Client Already Registered';
                        } else {

                            $user->createRecord('clients', array(
                                'participant_id' => $screening_id,
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
                                'client_image' => $attachment_file,
                                'comments' => Input::get('comments'),
                                'initials' => Input::get('initials'),
                                'status' => 1,
                                'created_on' => date('Y-m-d'),
                            ));

                            $successMessage = 'Client Added Successful';
                            Redirect::to('info.php?id=3');
                        }
                    }
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

                        $user->updateRecord('clients', array(
                            'nimregenin' => Input::get('nimregenin_herbal'),
                        ), $_GET['cid']);


                        if (Input::get('other_medical')) {
                            for ($i = 0; $i < count(Input::get('other_specify')); $i++) {
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
                            }
                        }

                        if (Input::get('nimregenin_herbal')) {

                            for ($i = 0; $i < count(Input::get('nimregenin_preparation')); $i++) {
                                $user->createRecord('nimregenin', array(
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
                                ));
                            }
                        }

                        if (Input::get('radiotherapy_performed')) {

                            for ($i = 0; $i < count(Input::get('radiotherapy')); $i++) {
                                $user->createRecord('radiotherapy', array(
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
                                ));
                            }
                        }


                        if (Input::get('other_herbal')) {

                            for ($i = 0; $i < count(Input::get('herbal_preparation')); $i++) {
                                $user->createRecord('herbal_treatment', array(
                                    'vid' => $_GET["vid"],
                                    'vcode' => $_GET["vcode"],
                                    'study_id' => $_GET['sid'],
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
                                ));
                            }
                        }

                        if (Input::get('chemotherapy_performed')) {

                            for ($i = 0; $i < count(Input::get('chemotherapy')); $i++) {
                                $user->createRecord('chemotherapy', array(
                                    'vid' => $_GET["vid"],
                                    'vcode' => $_GET["vcode"],
                                    'study_id' => $_GET['sid'],
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
                                ));
                            }
                        }

                        if (Input::get('surgery_performed') == 1) {

                            for ($i = 0; $i < count(Input::get('surgery')); $i++) {
                                $user->createRecord('surgery', array(
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
                                ));
                            }
                        }

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

        <?php include 'form_header.php'; ?>

        <?php if ($_GET['id'] == 1 && ($user->data()->position == 1 || $user->data()->position == 2)) { ?>

        <?php } elseif ($_GET['id'] == 2) { ?>
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
                                        <option value="<?= $study['id'] ?>"><?= $study['name'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <!-- Column 2 -->
                        <div class="col-md-6">
                            <!-- Date -->
                            <div class="form-group">
                                <label for="clinic_date">Date:</label>
                                <input type="date" class="form-control" name="clinic_date" id="clinic_date" required />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Column 1 -->
                        <div class="col-md-4">
                            <!-- First Name -->
                            <div class="form-group">
                                <label for="firstname">First Name</label>
                                <input type="text" class="form-control" name="firstname" id="firstname" placeholder="Type firstname..." onkeyup="myFunction()" required>
                            </div>
                        </div>

                        <!-- Column 2 -->
                        <div class="col-md-4">
                            <!-- Middle Name -->
                            <div class="form-group">
                                <label for="middlename">Middle Name</label>
                                <input type="text" class="form-control" name="middlename" id="middlename" placeholder="Type middlename..." onkeyup="myFunction()" required>
                            </div>
                        </div>

                        <!-- Column 3 -->
                        <div class="col-md-4">
                            <!-- Last Name -->
                            <div class="form-group">
                                <label for="lastname">Last Name</label>
                                <input type="text" class="form-control" name="lastname" id="lastname" placeholder="Type lastname..." onkeyup="myFunction()" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Column 1 -->
                        <div class="col-md-4">
                            <!-- Date of Birth -->
                            <div class="form-group">
                                <label for="dob">Date of Birth:</label>
                                <input type="date" class="form-control" name="dob" id="dob" required />
                            </div>
                        </div>

                        <!-- Column 2 -->
                        <div class="col-md-4">
                            <!-- Age -->
                            <div class="form-group">
                                <label for="age">Age</label>
                                <input type="number" class="form-control" name="age" id="age" required>
                            </div>
                        </div>

                        <!-- Column 3 -->
                        <div class="col-md-4">
                            <!-- Initials -->
                            <div class="form-group">
                                <label for="initials">Initials</label>
                                <input type="text" class="form-control" name="initials" id="initials" required>
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
                                    <option value="">Select</option>
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
                                <input type="text" class="form-control" name="id_number" id="id_number">
                            </div>
                        </div>

                        <!-- Column 3 -->
                        <div class="col-md-4">
                            <!-- Marital Status -->
                            <div class="form-group">
                                <label for="marital_status">Marital Status</label>
                                <select name="marital_status" class="form-control" required>
                                    <option value="">Select</option>
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
                                    <option value="">Select</option>
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

                        <!-- Column 2 -->
                        <div class="col-md-4">
                            <!-- Occupation -->
                            <div class="form-group">
                                <label for="occupation">Occupation</label>
                                <input type="text" class="form-control" name="occupation" id="occupation" required>
                            </div>
                        </div>

                        <!-- Column 3 -->
                        <div class="col-md-4">
                            <!-- National ID -->
                            <div class="form-group">
                                <label for="national_id">National ID</label>
                                <input type="text" class="form-control" name="national_id" id="national_id">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Column 1 -->
                        <div class="col-md-4">
                            <!-- Phone Number -->
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="text" class="form-control" name="phone_number" id="phone" placeholder="Example: 0700 000 111" required>
                            </div>
                        </div>

                        <!-- Column 2 -->
                        <div class="col-md-4">
                            <!-- Relative's Phone Number -->
                            <div class="form-group">
                                <label for="other_phone">Relative's Phone Number</label>
                                <input type="text" class="form-control" name="other_phone" id="other_phone" placeholder="Example: 0700 000 111">
                            </div>
                        </div>

                        <!-- Column 3 -->
                        <div class="col-md-4">
                            <!-- Residence Street -->
                            <div class="form-group">
                                <label for="street">Residence Street</label>
                                <input type="text" class="form-control" name="street" id="street" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Column 1 -->
                        <div class="col-md-4">
                            <!-- Region -->
                            <div class="form-group">
                                <label for="region">Region</label>
                                <input type="text" class="form-control" name="region" id="region" required>
                            </div>
                        </div>

                        <!-- Column 2 -->
                        <div class="col-md-4">
                            <!-- District -->
                            <div class="form-group">
                                <label for="district">District</label>
                                <input type="text" class="form-control" name="district" id="district" required>
                            </div>
                        </div>

                        <!-- Column 3 -->
                        <div class="col-md-4">
                            <!-- Ward -->
                            <div class="form-group">
                                <label for="ward">Ward</label>
                                <input type="text" class="form-control" name="ward" id="ward" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Column 1 -->
                        <div class="col-md-4">
                            <!-- Comments -->
                            <div class="form-group">
                                <label for="comments">Comments</label>
                                <textarea name="comments" class="form-control" rows="4"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Submit Button -->
                <div class="card-footer">
                    <button type="submit" name="add_client" class="btn btn-primary">Submit</button>
                </div>
            </form>
        <?php } elseif ($_GET['id'] == 8) { ?>
            <!-- /.card-header -->

            <form id="clients" enctype="multipart/form-data" method="post" autocomplete="off">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="row-form clearfix">
                                <div class="form-group">
                                    <label>First Name</label>
                                    <input class="form-control" type="text" name="firstname" id="firstname" value="<?php if ($staff['firstname']) {
                                        print_r($staff['firstname']);
                                    } ?>" required />
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="row-form clearfix">
                                <div class="form-group">
                                    <label>Middle Name</label>
                                    <input class="form-control" type="text" name="middlename" id="middlename" value="<?php if ($staff['middlename']) {
                                        print_r($staff['middlename']);
                                    } ?>" required />
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="row-form clearfix">
                                <div class="form-group">
                                    <label>Last Name</label>
                                    <input class="form-control" type="text" name="lastname" id="lastname" value="<?php if ($staff['lastname']) {
                                        print_r($staff['lastname']);
                                    } ?>" required />
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="row-form clearfix">
                                <div class="form-group">
                                    <label>User Name</label>
                                    <input class="form-control" type="text" name="username" id="username" value="<?php if ($staff['username']) {
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
                                    <input class="form-control" type="tel" pattern=[0]{1}[0-9]{9} minlength="10"
                                        maxlength="10" name="phone_number" id="phone_number" value="<?php if ($staff['phone_number']) {
                                            print_r($staff['phone_number']);
                                        } ?>" required /> <span>Example: 0700 000
                                        111</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="row-form clearfix">
                                <!-- select -->
                                <div class="form-group">
                                    <label>Phone Number 2</label>
                                    <input class="form-control" type="tel" pattern=[0]{1}[0-9]{9} minlength="10"
                                        maxlength="10" name="phone_number2" id="phone_number2" value="<?php if ($staff['phone_number2']) {
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
                                    <input class="form-control" type="email" name="email_address" id="email_address" value="<?php if ($staff['email_address']) {
                                        print_r($staff['email_address']);
                                    } ?>" required />
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="row-form clearfix">
                                <div class="form-group">
                                    <label>SEX</label>
                                    <select class="form-control" name="sex" style="width: 100%;" required>
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
                                    <select class="form-control" name="site_id" style="width: 100%;" required>
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
                                    <select class="form-control" name="position" style="width: 100%;" required>
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
                                    <input class="form-control" type="number" min="0" max="3" name="accessLevel"
                                        id="accessLevel" value="<?php if ($staff['accessLevel']) {
                                            print_r($staff['accessLevel']);
                                        } ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="row-form clearfix">
                                <div class="form-group">
                                    <label>Power</label>
                                    <input class="form-control" type="number" min="0" max="2" name="power" id="power"
                                        value="0" />
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
        <?php } ?>
        <?php include 'form_footer.php'; ?>

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
    <script src="js/screening/screening.js?v={{ timestamp }}"></script>

    <!-- Enrollment Js -->
    <script src="js/enrollment/enrollment.js?v={{ timestamp }}"></script>


    <!-- RESPIRATORY format numbers Js -->
    <script src="js/laboratory_clinic/laboratory_clinic.js?v={{ timestamp }}"></script>

    <!-- Diagnosis Test format numbers Js -->
    <script src="js/laboratory_zonal_ctlr/laboratory_zonal_ctlr.js?v={{ timestamp }}"></script>

    <!-- Diagnosis Js -->
    <script src="js/diagnosis/diagnosis.js?v={{ timestamp }}"></script>
    <script src="js/diagnosis/treatmentChanges.js?v={{ timestamp }}"></script>

    <script src="js/radio.js?v={{ timestamp }}"></script>

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