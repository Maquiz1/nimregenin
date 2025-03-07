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
        #medication_table {
            border-collapse: collapse;
        }

        #medication_table th,
        #medication_table td {
            padding: 8px;
            border: 1px solid #ddd;
        }

        #medication_table th {
            text-align: left;
            background-color: #f2f2f2;
        }

        #medication_table {
            border-collapse: collapse;
        }

        #medication_list th,
        #medication_list td {
            padding: 8px;
            border: 1px solid #ddd;
        }

        #medication_list th {
            text-align: left;
            background-color: #f2f2f2;
        }

        .remove-row {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 8px 16px;
            font-size: 14px;
            cursor: pointer;
        }

        .remove-row:hover {
            background-color: #da190b;
        }

        .edit-row {
            background-color: #3FF22F;
            color: white;
            border: none;
            padding: 8px 16px;
            font-size: 14px;
            cursor: pointer;
        }

        .edit-row:hover {
            background-color: #da190b;
        }

        #hospitalization_details_table {
            border-collapse: collapse;
        }

        #hospitalization_details_table th,
        #hospitalization_details_table td {
            padding: 8px;
            border: 1px solid #ddd;
        }

        #hospitalization_details_table th,
        #hospitalization_details_table td {
            padding: 8px;
            border: 1px solid #ddd;
        }

        #hospitalization_details_table th {
            text-align: left;
            background-color: #f2f2f2;
        }

        #sickle_cell_table {
            border-collapse: collapse;
        }

        #sickle_cell_table th,
        #sickle_cell_table td {
            padding: 8px;
            border: 1px solid #ddd;
        }

        #sickle_cell_table th,
        #sickle_cell_table td {
            padding: 8px;
            border: 1px solid #ddd;
        }

        #sickle_cell_table th {
            text-align: left;
            background-color: #f2f2f2;
        }

        .hidden {
            display: none;
        }
    </style>
</head>

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
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title">Client Information Form</h3>
                                    </div>
                                    <!-- Form Start -->
                                    <form id="validation" enctype="multipart/form-data" method="post" autocomplete="off">
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

                                                <!-- Date -->
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="clinic_date">Date:</label>
                                                        <div class="col-sm-6">
                                                            <input type="date" class="form-control" name="clinic_date"
                                                                id="clinic_date" required />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- First Name -->
                                            <div class="form-group">
                                                <label for="firstname">First Name</label>
                                                <input type="text" class="form-control" name="firstname" id="firstname"
                                                    placeholder="Type firstname..." onkeyup="myFunction()" required>
                                            </div>
                                            <!-- Middle Name -->
                                            <div class="form-group">
                                                <label for="middlename">Middle Name</label>
                                                <input type="text" class="form-control" name="middlename" id="middlename"
                                                    placeholder="Type middlename..." onkeyup="myFunction()" required>
                                            </div>
                                            <!-- Last Name -->
                                            <div class="form-group">
                                                <label for="lastname">Last Name</label>
                                                <input type="text" class="form-control" name="lastname" id="lastname"
                                                    placeholder="Type lastname..." onkeyup="myFunction()" required>
                                            </div>
                                            <!-- Date of Birth -->
                                            <div class="form-group row">
                                                <label for="dob" class="col-sm-3 col-form-label">Date of Birth:</label>
                                                <div class="col-sm-9">
                                                    <input type="date" class="form-control" name="dob" id="dob" required />
                                                </div>
                                            </div>
                                            <!-- Age -->
                                            <div class="form-group">
                                                <label for="age">Age</label>
                                                <input type="number" class="form-control" name="age" id="age" required>
                                            </div>
                                            <!-- Initials -->
                                            <div class="form-group">
                                                <label for="initials">Initials</label>
                                                <input type="text" class="form-control" name="initials" id="initials"
                                                    required>
                                            </div>
                                            <!-- Gender -->
                                            <div class="form-group">
                                                <label for="gender">Gender</label>
                                                <select name="gender" class="form-control" required>
                                                    <option value="">Select</option>
                                                    <option value="male">Male</option>
                                                    <option value="female">Female</option>
                                                </select>
                                            </div>
                                            <!-- Hospital ID Number -->
                                            <div class="form-group">
                                                <label for="id_number">Hospital ID Number</label>
                                                <input type="text" class="form-control" name="id_number" id="id_number">
                                            </div>
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
                                            <!-- Occupation -->
                                            <div class="form-group">
                                                <label for="occupation">Occupation</label>
                                                <input type="text" class="form-control" name="occupation" id="occupation"
                                                    required>
                                            </div>
                                            <!-- National ID -->
                                            <div class="form-group">
                                                <label for="national_id">National ID</label>
                                                <input type="text" class="form-control" name="national_id" id="national_id">
                                            </div>
                                            <!-- Phone Number -->
                                            <div class="form-group">
                                                <label for="phone">Phone Number</label>
                                                <input type="text" class="form-control" name="phone_number" id="phone"
                                                    placeholder="Example: 0700 000 111" required>
                                            </div>
                                            <!-- Relative's Phone Number -->
                                            <div class="form-group">
                                                <label for="other_phone">Relative's Phone Number</label>
                                                <input type="text" class="form-control" name="other_phone" id="other_phone"
                                                    placeholder="Example: 0700 000 111">
                                            </div>
                                            <!-- Residence Street -->
                                            <div class="form-group">
                                                <label for="street">Residence Street</label>
                                                <input type="text" class="form-control" name="street" id="street" required>
                                            </div>
                                            <!-- Region -->
                                            <div class="form-group">
                                                <label for="region">Region</label>
                                                <input type="text" class="form-control" name="region" id="region" required>
                                            </div>
                                            <!-- District -->
                                            <div class="form-group">
                                                <label for="district">District</label>
                                                <input type="text" class="form-control" name="district" id="district"
                                                    required>
                                            </div>
                                            <!-- Ward -->
                                            <div class="form-group">
                                                <label for="ward">Ward</label>
                                                <input type="text" class="form-control" name="ward" id="ward" required>
                                            </div>
                                            <!-- Comments -->
                                            <div class="form-group">
                                                <label for="comments">Comments</label>
                                                <textarea name="comments" class="form-control" rows="4"></textarea>
                                            </div>
                                        </div>
                                        <!-- Submit Button -->
                                        <div class="card-footer">
                                            <button type="submit" name="add_client" class="btn btn-primary">Submit</button>
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
            <div class="col-md-offset-1 col-md-8">
                <div class="head clearfix">
                    <div class="isw-ok"></div>
                    <h1>CRF 1: MEDICAL HISTORY, USE OF HERBAL MEDICINES AND STANDARD TREATMENT</h1>
                </div>
                <div class="block-fluid">
                    <form id="crf1" method="post">

                        <div class="head clearfix">
                            <div class="isw-ok"></div>
                            <h1>Medical History</h1>
                        </div>

                        <div class="row-form clearfix">
                            <div class="col-md-3">Date of diagnosis:</div>
                            <div class="col-md-9">
                                <input value="<?= $patient['diagnosis_date'] ?>" type="text" name="diagnosis_date"
                                    id="diagnosis_date" />
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
                            <div class="col-md-9"><textarea value="<?= $patient['diabetic_medicatn_name'] ?>"
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
                            <div class="col-md-9"><textarea value="<?= $patient['hypertension_medicatn_name'] ?>"
                                    name="hypertension_medicatn_name" rows="4"></textarea> </div>
                        </div>


                        <div class="row-form clearfix">
                            <div class="col-md-3">3. Any other heart problem apart from hypertension?:</div>
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
                            <div class="col-md-9"><textarea value="<?= $patient['heart_medicatn_name'] ?>"
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
                            <div class="col-md-9"><textarea value="<?= $patient['asthma_medicatn_name'] ?>"
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
                            <div class="col-md-9"><textarea value="<?= $patient['hiv_aids_medicatn_name'] ?>"
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
                                            <td><input value='<?= $medication['other_specify'] ?>' type="text"
                                                    name="other_specify[]"></td>
                                            <td>
                                                <select name="other_medical_medicatn[]" id="other_medical_medicatn[]"
                                                    style="width: 100%;">
                                                    <?php if ($medication['other_medical_medicatn'] == "1") { ?>
                                                        <option value="<?= $medication['other_medical_medicatn'] ?>">Yes</option>
                                                    <?php } elseif ($medication['other_medical_medicatn'] == "2") { ?>
                                                        <option value="<?= $medication['other_medical_medicatn'] ?>">No</option>
                                                    <?php } else { ?>
                                                        <option value="">Select</option>
                                                    <?php } ?>
                                                    <option value="1">Yes</option>
                                                    <option value="2">No</option>
                                                </select>
                                            </td>
                                            <td><input value='<?= $medication['other_medicatn_name'] ?>' type="text"
                                                    name="other_medicatn_name[]"></td>
                                            <!-- <td><button type="button" class="remove-row">Remove</button></td> -->
                                            <td><input value='<?= $medication['id'] ?>' type="hidden" name="medication_id[]">
                                            </td>

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
                                            <td><input value='<?= $nimregenin['nimregenin_preparation'] ?>' type="text"
                                                    name="nimregenin_preparation[]"></td>
                                            <td><input value='<?= $nimregenin['nimregenin_start'] ?>' type="text"
                                                    name="nimregenin_start[]"><br><span>Example: 2010-12-01</span></td>
                                            <td>
                                                <select name="nimregenin_ongoing[]" id="nimregenin_ongoing[]"
                                                    style="width: 100%;">
                                                    <?php if ($nimregenin['nimregenin_ongoing'] == "1") { ?>
                                                        <option value="<?= $nimregenin['nimregenin_ongoing'] ?>">Yes</option>
                                                    <?php } elseif ($nimregenin['nimregenin_ongoing'] == "2") { ?>
                                                        <option value="<?= $nimregenin['nimregenin_ongoing'] ?>">No</option>
                                                    <?php } else { ?>
                                                        <option value="">Select</option>
                                                    <?php } ?>
                                                    <option value="1">Yes</option>
                                                    <option value="2">No</option>
                                                </select>
                                            </td>
                                            <td><input value='<?= $nimregenin['nimregenin_end'] ?>' type="text"
                                                    name="nimregenin_end[]"><br><span>Example: 2010-12-01</span></td>
                                            <td><input value='<?= $nimregenin['nimregenin_dose'] ?>' type="text"
                                                    name="nimregenin_dose[]"><br><span>(mls)</span></td>
                                            <td><input value='<?= $nimregenin['nimregenin_frequency'] ?>' type="text"
                                                    name="nimregenin_frequency[]"><br><span>(per day)</span></td>
                                            <td><input value='<?= $nimregenin['nimregenin_remarks'] ?>' type="text"
                                                    name="nimregenin_remarks[]"><br></td>
                                            <td><input value='<?= $nimregenin['id'] ?>' type="hidden" name="nimregenin_id[]">
                                            </td>
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
                                            <td><input value='<?= $herbal_treatment['herbal_preparation'] ?>' type="text"
                                                    name="herbal_preparation[]"></td>
                                            <td><input value='<?= $herbal_treatment['herbal_start'] ?>' type="text"
                                                    name="herbal_start[]"><br><span>Example: 2010-12-01</span></td>
                                            <td>
                                                <select name="herbal_ongoing[]" id="herbal_ongoing[]" style="width: 100%;">
                                                    <?php if ($herbal_treatment['herbal_ongoing'] == "1") { ?>
                                                        <option value="<?= $herbal_treatment['herbal_ongoing'] ?>">Yes</option>
                                                    <?php } elseif ($herbal_treatment['herbal_ongoing'] == "2") { ?>
                                                        <option value="<?= $herbal_treatment['herbal_ongoing'] ?>">No</option>
                                                    <?php } else { ?>
                                                        <option value="">Select</option>
                                                    <?php } ?>
                                                    <option value="1">Yes</option>
                                                    <option value="2">No</option>
                                                </select>
                                            </td>
                                            <td><input value='<?= $herbal_treatment['herbal_end'] ?>' type="text"
                                                    name="herbal_end[]"><br><span>Example: 2010-12-01</span></td>
                                            <td><input value='<?= $herbal_treatment['herbal_dose'] ?>' type="text"
                                                    name="herbal_dose[]"><br><span>(per day)</span></td>
                                            <td><input value='<?= $herbal_treatment['herbal_frequency'] ?>' type="text"
                                                    name="herbal_frequency[]"><br><span>(per day)</span></td>
                                            <td><input value='<?= $herbal_treatment['herbal_remarks'] ?>' type="text"
                                                    name="herbal_remarks[]"><br></td>
                                            <!-- <td><button type="button" class="remove-row">Remove</button></td> -->
                                            <td><input value='<?= $herbal_treatment['id'] ?>' type="hidden" name="herbal_id[]">
                                            </td>
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

                        <div class="row-form clearfix">
                            <div class="col-md-3">1. Is there any Radiotherapy performed?:</div>
                            <div class="col-md-9">
                                <select name="radiotherapy_performed" id="radiotherapy_performed" style="width: 100%;"
                                    required>
                                    <?php if ($patient['radiotherapy_performed'] == "1") { ?>
                                        <option value="<?= $patient['radiotherapy_performed'] ?>">Yes</option>
                                    <?php } elseif ($patient['radiotherapy_performed'] == "2") { ?>
                                        <option value="<?= $patient['radiotherapy_performed'] ?>">No</option>
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
                                            <td><input value="<?= $radiotherapy['radiotherapy'] ?>" type="text"
                                                    name="radiotherapy[]"></td>
                                            <td><input value="<?= $radiotherapy['radiotherapy_start'] ?>" type="text"
                                                    name="radiotherapy_start[]"><br><span>Example: 2010-12-01</span></td>
                                            <td>
                                                <select name="radiotherapy_ongoing[]" id="radiotherapy_ongoing[]"
                                                    style="width: 100%;">
                                                    <?php if ($radiotherapy['radiotherapy_ongoing'] == "1") { ?>
                                                        <option value="<?= $radiotherapy['radiotherapy_ongoing'] ?>">Yes</option>
                                                    <?php } elseif ($radiotherapy['radiotherapy_ongoing'] == "2") { ?>
                                                        <option value="<?= $radiotherapy['radiotherapy_ongoing'] ?>">No</option>
                                                    <?php } else { ?>
                                                        <option value="">Select</option>
                                                    <?php } ?>
                                                    <option value="1">Yes</option>
                                                    <option value="2">No</option>
                                                </select>
                                            </td>
                                            <td><input value="<?= $radiotherapy['radiotherapy_end'] ?>" type="text"
                                                    name="radiotherapy_end[]"><br><span>Example: 2010-12-01</span></td>
                                            <td><input value="<?= $radiotherapy['radiotherapy_dose'] ?>" type="text"
                                                    name="radiotherapy_dose[]"><br><span>(Grays)</span></td>
                                            <td><input value="<?= $radiotherapy['radiotherapy_frequecy'] ?>" type="text"
                                                    name="radiotherapy_frequecy[]"><br><span>(numbers)</span></td>
                                            <td><input value="<?= $radiotherapy['radiotherapy_remarks'] ?>" type="text"
                                                    name="radiotherapy_remarks[]"></td>
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
                                <select name="chemotherapy_performed" id="chemotherapy_performed" style="width: 100%;"
                                    required>
                                    <?php if ($patient['chemotherapy_performed'] == "1") { ?>
                                        <option value="<?= $patient['chemotherapy_performed'] ?>">Yes</option>
                                    <?php } elseif ($patient['chemotherapy_performed'] == "2") { ?>
                                        <option value="<?= $patient['chemotherapy_performed'] ?>">No</option>
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
                                            <td><input value="<?= $chemotherapy['chemotherapy'] ?>" type="text"
                                                    name="chemotherapy[]"></td>
                                            <td><input value="<?= $chemotherapy['chemotherapy_start'] ?>" type="text"
                                                    name="chemotherapy_start[]"><br><span>Example: 2010-12-01</span></td>
                                            <td>
                                                <select name="chemotherapy_ongoing[]" id="chemotherapy_ongoing[]"
                                                    style="width: 100%;">
                                                    <?php if ($chemotherapy['chemotherapy_ongoing'] == "1") { ?>
                                                        <option value="<?= $chemotherapy['chemotherapy_ongoing'] ?>">Yes</option>
                                                    <?php } elseif ($chemotherapy['chemotherapy_ongoing'] == "2") { ?>
                                                        <option value="<?= $chemotherapy['chemotherapy_ongoing'] ?>">No</option>
                                                    <?php } else { ?>
                                                        <option value="">Select</option>
                                                    <?php } ?>
                                                    <option value="1">Yes</option>
                                                    <option value="2">No</option>
                                                </select>
                                            </td>
                                            <td><input value="<?= $chemotherapy['chemotherapy_end'] ?>" type="text"
                                                    name="chemotherapy_end[]"><br><span>Example: 2010-12-01</span></td>
                                            <td><input value="<?= $chemotherapy['chemotherapy_dose'] ?>" type="text"
                                                    name="chemotherapy_dose[]"><br><span>(mg)</span></td>
                                            <td><input value="<?= $chemotherapy['chemotherapy_frequecy'] ?>" type="text"
                                                    name="chemotherapy_frequecy[]"><br><span>(numbers)</span></td>
                                            <td><input value="<?= $chemotherapy['chemotherapy_remarks'] ?>" type="text"
                                                    name="chemotherapy_remarks[]"></td>
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
                                <select name="surgery_performed" id="surgery_performed" style="width: 100%;" required>
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
                                                <td><input value="<?= $surgery['surgery'] ?>" type="text" name="surgery[]"></td>
                                                <td><input value="<?= $surgery['surgery_start'] ?>" type="text"
                                                        name="surgery_start[]"><br><span>Example: 2010-12-01</span></td>
                                                <td><input value="<?= $surgery['surgery_number'] ?>" type="text"
                                                        name="surgery_number[]"><br><span>(numbers)</span></td>
                                                <td><input value="<?= $surgery['surgery_remarks'] ?>" type="text"
                                                        name="surgery_remarks[]"></td>
                                                <!-- <td><button type="button" class="remove-row">Remove</button></td> -->
                                                <td><input value="<?= $surgery['id'] ?>" type="hidden" name="surgery_id[]"></td>
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
                                    <input value="<?= $patient['crf1_cmpltd_date'] ?>" type="text" name="crf1_cmpltd_date"
                                        id="crf1_cmpltd_date" />
                                </div>
                            </div>
                        </div>
                        <div class="footer tar">
                            <input type="hidden" name="id" value="<?= $patient['id'] ?>">
                            <input type="submit" name="add_crf1" value="Submit" class="btn btn-default">
                        </div>
                    </form>
                </div>
            </div>
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


    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>

</html>

</html>