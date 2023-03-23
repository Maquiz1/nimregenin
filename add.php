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
                'street' => array(
                    'required' => true,
                ),
                'phone_number' => array(
                    'required' => true,
                    'unique' => 'clients',
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
                            'phone_number' => Input::get('phone_number'),
                            'other_phone' => Input::get('other_phone'),
                            'street' => Input::get('street'),
                            'ward' => Input::get('ward'),
                            'block_no' => Input::get('block_no'),
                            'site_id' => $user->data()->site_id,
                            'staff_id' => $user->data()->id,
                            'client_image' => $attachment_file,
                            'comments' => Input::get('comments'),
                            'initials' => Input::get('initials'),
                            'status' => 1,
                        ));

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

                        $successMessage = 'Client Added Successful';
                    }
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_crf1')) {
            $validate = $validate->check($_POST, array(
                'diabetic' => array(
                    'required' => true,
                ),

            ));
            if ($validate->passed()) {
                try {
                    $user->createRecord('crf1', array(
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
                        'chronic' => Input::get('chronic'),
                        'chronic_medicatn' => Input::get('chronic_medicatn'),
                        'chronic_medicatn_name' => Input::get('chronic_medicatn_name'),
                        'hiv_aids' => Input::get('hiv_aids'),
                        'hiv_aids_medicatn' => Input::get('hiv_aids_medicatn'),
                        'hiv_aids_medicatn_name' => Input::get('hiv_aids_medicatn_name'),
                        'other_medical' => Input::get('other_medical'),
                        'other_specify' => Input::get('other_specify'),
                        'other_medical_medicatn' => Input::get('other_medical_medicatn'),
                        'other_medicatn_name' => Input::get('other_medicatn_name'),
                        'herbal' => Input::get('herbal'),
                        'herbal_preparation' => Input::get('herbal_preparation'),
                        'herbal_start' => Input::get('herbal_start'),
                        'herbal_ongoing' => Input::get('herbal_ongoing'),
                        'herbal_end' => Input::get('herbal_end'),
                        'herbal_dose' => Input::get('herbal_dose'),
                        'herbal_frequecy' => Input::get('herbal_frequecy'),
                        'herbal_preparation_other' => Input::get('herbal_preparation_other'),
                        'standard_medication' => Input::get('standard_medication'),
                        'standard_start' => Input::get('standard_start'),
                        'standard_ongoing' => Input::get('standard_ongoing'),
                        'standard_end' => Input::get('standard_end'),
                        'standard_dose' => Input::get('standard_dose'),
                        'standard_frequecy' => Input::get('standard_frequecy'),
                        'standard_remarks' => Input::get('standard_remarks'),
                        'crf1_cmpltd_date' => Input::get('crf1_cmpltd_date'),
                        'patient_id' => $_GET['cid'],
                        'staff_id' => $user->data()->id,
                        'status' => 1,
                        'created_on' => date('Y-m-d'),
                        'site_id' => $user->data()->site_id,
                    ));


                    $successMessage = 'CRF1 added Successful';
                    Redirect::to('info.php?id=6&cid=' . $_GET['cid']);
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
                    $user->createRecord('crf2', array(
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
                        'hematological' => Input::get('hematological'),
                        'hematological_comments' => Input::get('hematological_comments'),
                        'hematological_signifcnt' => Input::get('hematological_signifcnt'),
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
                    ));


                    $successMessage = 'CRF2 added Successful';
                    Redirect::to('info.php?id=6&cid=' . $_GET['cid']);
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
                    $user->createRecord('crf3', array(
                        'crf3_date' => Input::get('crf3_date'),
                        'fever' => Input::get('fever'),
                        'shivering' => Input::get('shivering'),
                        'cough' => Input::get('cough'),
                        'pain' => Input::get('pain'),
                        'breath' => Input::get('breath'),
                        'vomit' => Input::get('vomit'),
                        'diarrhoea' => Input::get('diarrhoea'),
                        'headache' => Input::get('headache'),
                        'difficult' => Input::get('difficult'),
                        'nose' => Input::get('nose'),
                        'throat' => Input::get('throat'),
                        'fatigue' => Input::get('fatigue'),
                        'muscle' => Input::get('muscle'),
                        'smell' => Input::get('smell'),
                        'taste' => Input::get('taste'),
                        'sneezing' => Input::get('sneezing'),
                        'dizziness' => Input::get('dizziness'),
                        'blurred' => Input::get('blurred'),
                        'symptoms_other' => Input::get('symptoms_other'),
                        'other_comments' => Input::get('other_comments'),
                        'oxygen_therapy' => Input::get('oxygen_therapy'),
                        'oxygen_therapy_yes' => Input::get('oxygen_therapy_yes'),
                        'oxygen_therapy_herbal' => Input::get('oxygen_therapy_herbal'),
                        'oxygen_therapy_litres' => Input::get('oxygen_therapy_litres'),
                        'oxygen_plant' => Input::get('oxygen_plant'),
                        'oxygen_therapy_start' => Input::get('oxygen_therapy_start'),
                        'oxygen_therapy_ongoing' => Input::get('oxygen_therapy_ongoing'),
                        'oxygen_therapy_end' => Input::get('oxygen_therapy_end'),
                        'oxygen_days' => Input::get('oxygen_days'),
                        'cpap_use' => Input::get('cpap_use'),
                        'cpap_yes' => Input::get('cpap_yes'),
                        'cpap_herbal' => Input::get('cpap_herbal'),
                        'cpap_fi02' => Input::get('cpap_fi02'),
                        'cpap_sat02' => Input::get('cpap_sat02'),
                        'cpap_cylinder' => Input::get('cpap_cylinder'),
                        'cpap_plant' => Input::get('cpap_plant'),
                        'cpap_start' => Input::get('cpap_start'),
                        'cpap_ongoing' => Input::get('cpap_ongoing'),
                        'cpap_end' => Input::get('cpap_end'),
                        'ventilator' => Input::get('ventilator'),
                        'ventilator_yes' => Input::get('ventilator_yes'),
                        'ventilator_herbal' => Input::get('ventilator_herbal'),
                        'ventilator_fi02' => Input::get('ventilator_fi02'),
                        'ventilator_litres' => Input::get('ventilator_litres'),
                        'ventilator_start' => Input::get('ventilator_start'),
                        'ventilator_ongoing' => Input::get('ventilator_ongoing'),
                        'ventilator_end' => Input::get('ventilator_end'),
                        'crf3_cmpltd_date' => Input::get('crf3_cmpltd_date'),
                        'patient_id' => $_GET['cid'],
                        'staff_id' => $user->data()->id,
                        'status' => 1,
                        'created_on' => date('Y-m-d'),
                        'site_id' => $user->data()->site_id,
                    ));


                    $successMessage = 'CRF3 added Successful';
                    Redirect::to('info.php?id=6&cid=' . $_GET['cid']);
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_crf4')) {
            $validate = $validate->check($_POST, array(
                'sample_date' => array(
                    'required' => true,
                ),

            ));
            if ($validate->passed()) {
                try {
                    $user->createRecord('crf4', array(
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
                        'chest_xray' => Input::get('chest_xray'),
                        'ct_chest' => Input::get('ct_chest'),
                        'ct_chest_specify' => Input::get('ct_chest_specify'),
                        'ecg_specify' => Input::get('ecg_specify'),
                        'crf4_cmpltd_date' => Input::get('crf4_cmpltd_date'),
                        'patient_id' => $_GET['cid'],
                        'staff_id' => $user->data()->id,
                        'status' => 1,
                        'created_on' => date('Y-m-d'),
                        'site_id' => $user->data()->site_id,
                    ));


                    $successMessage = 'CRF3 added Successful';
                    Redirect::to('info.php?id=6&cid=' . $_GET['cid']);
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
                print_r($_POST);
                try {
                    $user->createRecord('crf5', array(
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
                    Redirect::to('info.php?id=6&cid=' . $_GET['cid']);
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
                print_r($_POST);
                try {
                    $user->createRecord('crf6', array(
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


                    $successMessage = 'CRF3 added Successful';
                    Redirect::to('info.php?id=6&cid=' . $_GET['cid']);
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
    <title> Add - HetaCov </title>
    <?php include "head.php"; ?>
</head>

<body>
    <div class="wrapper">

        <?php include 'topbar.php' ?>
        <?php include 'menu.php' ?>
        <div class="content">


            <div class="breadLine">

                <ul class="breadcrumb">
                    <li><a href="#">Simple Admin</a> <span class="divider">></span></li>
                    <li class="active">Add Info</li>
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
                        <div class="col-md-offset-1 col-md-8">
                            <div class="head clearfix">
                                <div class="isw-ok"></div>
                                <h1>Add User</h1>
                            </div>
                            <div class="block-fluid">
                                <form id="validation" method="post">

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">First Name:</div>
                                        <div class="col-md-9">
                                            <input value="" class="validate[required]" type="text" name="firstname" id="firstname" />
                                        </div>
                                    </div>
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Last Name:</div>
                                        <div class="col-md-9">
                                            <input value="" class="validate[required]" type="text" name="lastname" id="lastname" />
                                        </div>
                                    </div>
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Username:</div>
                                        <div class="col-md-9">
                                            <input value="" class="validate[required]" type="text" name="username" id="username" />
                                        </div>
                                    </div>
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Site</div>
                                        <div class="col-md-9">
                                            <select name="site_id" style="width: 100%;" required>
                                                <option value="">Select site</option>
                                                <?php foreach ($override->getData('site') as $site) { ?>
                                                    <option value="<?= $site['id'] ?>"><?= $site['name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Position</div>
                                        <div class="col-md-9">
                                            <select name="position" style="width: 100%;" required>
                                                <option value="">Select position</option>
                                                <?php foreach ($override->getData('position') as $position) { ?>
                                                    <option value="<?= $position['id'] ?>"><?= $position['name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Phone Number:</div>
                                        <div class="col-md-9"><input value="" class="" type="text" name="phone_number" id="phone" required /> <span>Example: 0700 000 111</span></div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">E-mail Address:</div>
                                        <div class="col-md-9"><input value="" class="validate[required,custom[email]]" type="text" name="email_address" id="email" /> <span>Example: someone@nowhere.com</span></div>
                                    </div>

                                    <div class="footer tar">
                                        <input type="submit" name="add_user" value="Submit" class="btn btn-default">
                                    </div>

                                </form>
                            </div>

                        </div>
                    <?php } elseif ($_GET['id'] == 2 && $user->data()->position == 1) { ?>
                        <div class="col-md-offset-1 col-md-8">
                            <div class="head clearfix">
                                <div class="isw-ok"></div>
                                <h1>Add Position</h1>
                            </div>
                            <div class="block-fluid">
                                <form id="validation" method="post">
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Name:</div>
                                        <div class="col-md-9">
                                            <input value="" class="validate[required]" type="text" name="name" id="name" />
                                        </div>
                                    </div>

                                    <div class="footer tar">
                                        <input type="submit" name="add_position" value="Submit" class="btn btn-default">
                                    </div>

                                </form>
                            </div>

                        </div>
                    <?php } elseif ($_GET['id'] == 3 && $user->data()->position == 1) { ?>
                        <div class="col-md-offset-1 col-md-8">
                            <div class="head clearfix">
                                <div class="isw-ok"></div>
                                <h1>Add Study</h1>
                            </div>
                            <div class="block-fluid">
                                <form id="validation" method="post">
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Name: </div>
                                        <div class="col-md-9">
                                            <input value="" class="validate[required]" type="text" name="name" id="name" required />
                                        </div>
                                    </div>
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">PI</div>
                                        <div class="col-md-9">
                                            <select name="pi" style="width: 100%;" required>
                                                <option value="">Select staff</option>
                                                <?php foreach ($override->getData('user') as $staff) { ?>
                                                    <option value="<?= $staff['id'] ?>"><?= $staff['firstname'] . ' ' . $staff['lastname'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Coordinator</div>
                                        <div class="col-md-9">
                                            <select name="coordinator" style="width: 100%;" required>
                                                <option value="">Select staff</option>
                                                <?php foreach ($override->getData('user') as $staff) { ?>
                                                    <option value="<?= $staff['id'] ?>"><?= $staff['firstname'] . ' ' . $staff['lastname'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Doctor</div>
                                        <div class="col-md-9">
                                            <select name="doctor" style="width: 100%;" required>
                                                <option value="">Select staff</option>
                                                <?php foreach ($override->getData('user') as $staff) { ?>
                                                    <option value="<?= $staff['id'] ?>"><?= $staff['firstname'] . ' ' . $staff['lastname'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Start Date:</div>
                                        <div class="col-md-9"><input type="text" name="start_date" id="mask_date" required /> <span>Example: 04/10/2012</span></div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">End Date:</div>
                                        <div class="col-md-9"><input type="text" name="end_date" id="mask_date" required /> <span>Example: 04/10/2012</span></div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Study details:</div>
                                        <div class="col-md-9"><textarea name="details" rows="4" required></textarea></div>
                                    </div>

                                    <div class="footer tar">
                                        <input type="submit" name="add_study" value="Submit" class="btn btn-default">
                                    </div>

                                </form>
                            </div>

                        </div>
                    <?php } elseif ($_GET['id'] == 4) { ?>
                        <div class="col-md-offset-1 col-md-8">
                            <div class="head clearfix">
                                <div class="isw-ok"></div>
                                <h1>Add Client</h1>
                            </div>
                            <div class="block-fluid">
                                <form id="validation" enctype="multipart/form-data" method="post">

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
                                        <div class="col-md-3">Date:</div>
                                        <div class="col-md-9">
                                            <input value="" class="validate[required,custom[date]]" type="text" name="clinic_date" id="clinic_date" /> <span>Example: 2010-12-01</span>
                                        </div>
                                    </div>
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">First Name:</div>
                                        <div class="col-md-9">
                                            <input value="" class="validate[required]" type="text" name="firstname" id="firstname" />
                                        </div>
                                    </div>
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Middle Name:</div>
                                        <div class="col-md-9">
                                            <input value="" class="validate[required]" type="text" name="middlename" id="middlename" />
                                        </div>
                                    </div>
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Last Name:</div>
                                        <div class="col-md-9">
                                            <input value="" class="validate[required]" type="text" name="lastname" id="lastname" />
                                        </div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Date of Birth:</div>
                                        <div class="col-md-9">
                                            <input value="" class="validate[required,custom[date]]" type="text" name="dob" id="date" /> <span>Example: 2010-12-01</span>
                                        </div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Age:</div>
                                        <div class="col-md-9"><input value="" type="number" name="age" id="age" required /></div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Initials:</div>
                                        <div class="col-md-9">
                                            <input value="" class="validate[required]" type="text" name="initials" id="initials" />
                                        </div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Gender</div>
                                        <div class="col-md-9">
                                            <select name="gender" style="width: 100%;" required>
                                                <option value="">Select</option>
                                                <option value="male">Male</option>
                                                <option value="female">Female</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Hospital ID Number:</div>
                                        <div class="col-md-9">
                                            <input value="" type="text" name="id_number" id="id_number" />
                                        </div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Marital Status</div>
                                        <div class="col-md-9">
                                            <select name="marital_status" style="width: 100%;" required>
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

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Education Level</div>
                                        <div class="col-md-9">
                                            <select name="education_level" style="width: 100%;" required>
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

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Occupation:</div>
                                        <div class="col-md-9"><input value="" class="" type="text" name="occupation" id="occupation" required /></div>
                                    </div>
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Phone Number:</div>
                                        <div class="col-md-9"><input value="" class="" type="text" name="phone_number" id="phone" required /> <span>Example: 0700 000 111</span></div>
                                    </div>
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Relative's Phone Number:</div>
                                        <div class="col-md-9"><input value="" class="" type="text" name="other_phone" id="phone" /> <span>Example: 0700 000 111</span></div>
                                    </div>
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Residence Street:</div>
                                        <div class="col-md-9"><input value="" class="" type="text" name="street" id="street" required /></div>
                                    </div>
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Ward:</div>
                                        <div class="col-md-9"><input value="" class="" type="text" name="ward" id="ward" required /></div>
                                    </div>



                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Comments:</div>
                                        <div class="col-md-9"><textarea name="comments" rows="4"></textarea> </div>
                                    </div>

                                    <div class="footer tar">
                                        <input type="submit" name="add_client" value="Submit" class="btn btn-default">
                                    </div>

                                </form>
                            </div>

                        </div>
                    <?php } elseif ($_GET['id'] == 5 && $user->data()->position == 1) { ?>
                        <div class="col-md-offset-1 col-md-8">
                            <div class="head clearfix">
                                <div class="isw-ok"></div>
                                <h1>Add Study</h1>
                            </div>
                            <div class="block-fluid">
                                <form id="validation" method="post">
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Name:</div>
                                        <div class="col-md-9">
                                            <input value="" class="validate[required]" type="text" name="name" id="name" />
                                        </div>
                                    </div>
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Code:</div>
                                        <div class="col-md-9">
                                            <input value="" class="validate[required]" type="text" name="code" id="code" />
                                        </div>
                                    </div>
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Sample Size:</div>
                                        <div class="col-md-9">
                                            <input value="" class="validate[required]" type="number" name="sample_size" id="sample_size" />
                                        </div>
                                    </div>
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Start Date:</div>
                                        <div class="col-md-9">
                                            <input value="" class="validate[required,custom[date]]" type="text" name="start_date" id="start_date" /> <span>Example: 2010-12-01</span>
                                        </div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">End Date:</div>
                                        <div class="col-md-9">
                                            <input value="" class="validate[required,custom[date]]" type="text" name="end_date" id="end_date" /> <span>Example: 2010-12-01</span>
                                        </div>
                                    </div>

                                    <div class="footer tar">
                                        <input type="submit" name="add_study" value="Submit" class="btn btn-default">
                                    </div>

                                </form>
                            </div>

                        </div>
                    <?php } elseif ($_GET['id'] == 6 && $user->data()->position == 1) { ?>
                        <div class="col-md-offset-1 col-md-8">
                            <div class="head clearfix">
                                <div class="isw-ok"></div>
                                <h1>Add Site</h1>
                            </div>
                            <div class="block-fluid">
                                <form id="validation" method="post">
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Name:</div>
                                        <div class="col-md-9">
                                            <input value="" class="validate[required]" type="text" name="name" id="name" />
                                        </div>
                                    </div>

                                    <div class="footer tar">
                                        <input type="submit" name="add_site" value="Submit" class="btn btn-default">
                                    </div>

                                </form>
                            </div>

                        </div>
                    <?php } elseif ($_GET['id'] == 7) { ?>
                        <div class="col-md-offset-1 col-md-8">
                            <div class="head clearfix">
                                <div class="isw-ok"></div>
                                <h1>Add Visit</h1>
                            </div>
                            <div class="block-fluid">
                                <form id="validation" method="post">
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Visit Name:</div>
                                        <div class="col-md-9">
                                            <input value="" class="validate[required]" type="text" name="name" id="name" />
                                        </div>
                                    </div>

                                    <div class="footer tar">
                                        <input type="submit" name="add_site" value="Submit" class="btn btn-default">
                                    </div>

                                </form>
                            </div>

                        </div>
                    <?php } elseif ($_GET['id'] == 8) { ?>
                        <div class="col-md-offset-1 col-md-8">
                            <div class="head clearfix">
                                <div class="isw-ok"></div>
                                <h1>CRF 1</h1>
                            </div>
                            <div class="block-fluid">
                                <form id="validation" method="post">

                                    <div class="head clearfix">
                                        <div class="isw-ok"></div>
                                        <h1>Do the patients have any of the following medical conditions</h1>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">1. Diabetic Mellitus:</div>
                                        <div class="col-md-9">
                                            <select name="diabetic" id="diabetic" style="width: 100%;" required>
                                                <option value="">Select</option>
                                                <option value="1">Yes</option>
                                                <option value="2">No</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row-form clearfix" id="diabetic_medicatn1">
                                        <div class="col-md-3">1. Is the patient on Medication?</div>
                                        <div class="col-md-9">
                                            <select name="diabetic_medicatn" id="diabetic_medicatn" style="width: 100%;">
                                                <option value="">Select</option>
                                                <option value="1">Yes</option>
                                                <option value="2">No</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row-form clearfix" id="diabetic_medicatn_name">
                                        <div class="col-md-3">1. Mention the medications:</div>
                                        <div class="col-md-9"><textarea name="diabetic_medicatn_name" rows="4"></textarea> </div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">2. Hypertension:</div>
                                        <div class="col-md-9">
                                            <select name="hypertension" id="hypertension" style="width: 100%;" required>
                                                <option value="">Select</option>
                                                <option value="1">Yes</option>
                                                <option value="2">No</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row-form clearfix" id="hypertension_medicatn1">
                                        <div class="col-md-3">2. Is the patient on Medication?</div>
                                        <div class="col-md-9">
                                            <select name="hypertension_medicatn" id="hypertension_medicatn" style="width: 100%;">
                                                <option value="">Select</option>
                                                <option value="1">Yes</option>
                                                <option value="2">No</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row-form clearfix" id="hypertension_medicatn_name">
                                        <div class="col-md-3">2. Mention the medications:</div>
                                        <div class="col-md-9"><textarea name="hypertension_medicatn_name" rows="4"></textarea> </div>
                                    </div>


                                    <div class="row-form clearfix">
                                        <div class="col-md-3">3. Any other heart problem apart from hypertension?:</div>
                                        <div class="col-md-9">
                                            <select name="heart" id="heart" style="width: 100%;" required>
                                                <option value="">Select</option>
                                                <option value="1">Yes</option>
                                                <option value="2">No</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row-form clearfix" id="heart_medicatn1">
                                        <div class="col-md-3">3. Is the patient on Medication?</div>
                                        <div class="col-md-9">
                                            <select name="heart_medicatn" id="heart_medicatn" style="width: 100%;">
                                                <option value="">Select</option>
                                                <option value="1">Yes</option>
                                                <option value="2">No</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row-form clearfix" id="heart_medicatn_name">
                                        <div class="col-md-3">3. Mention the medications:</div>
                                        <div class="col-md-9"><textarea name="heart_medicatn_name" rows="4"></textarea> </div>
                                    </div>


                                    <div class="row-form clearfix">
                                        <div class="col-md-3">4. Asthma:</div>
                                        <div class="col-md-9">
                                            <select name="asthma" id="asthma" style="width: 100%;" required>
                                                <option value="">Select</option>
                                                <option value="1">Yes</option>
                                                <option value="2">No</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row-form clearfix" id="asthma_medicatn1">
                                        <div class="col-md-3">4. Is the patient on Medication?</div>
                                        <div class="col-md-9">
                                            <select name="asthma_medicatn" id="asthma_medicatn" style="width: 100%;">
                                                <option value="">Select</option>
                                                <option value="1">Yes</option>
                                                <option value="2">No</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row-form clearfix" id="asthma_medicatn_name">
                                        <div class="col-md-3">4. Mention the medications:</div>
                                        <div class="col-md-9"><textarea name="asthma_medicatn_name" rows="4"></textarea> </div>
                                    </div>


                                    <div class="row-form clearfix">
                                        <div class="col-md-3">5. Renal problem?:</div>
                                        <div class="col-md-9">
                                            <select name="chronic" id="chronic" style="width: 100%;" required>
                                                <option value="">Select</option>
                                                <option value="1">Yes</option>
                                                <option value="2">No</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row-form clearfix" id="chronic_medicatn1">
                                        <div class="col-md-3">5. Is the patient on Medication?</div>
                                        <div class="col-md-9">
                                            <select name="chronic_medicatn" id="chronic_medicatn" style="width: 100%;">
                                                <option value="">Select</option>
                                                <option value="1">Yes</option>
                                                <option value="2">No</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row-form clearfix" id="chronic_medicatn_name">
                                        <div class="col-md-3">5. Mention the medications:</div>
                                        <div class="col-md-9"><textarea name="chronic_medicatn_name" rows="4"></textarea> </div>
                                    </div>


                                    <div class="row-form clearfix">
                                        <div class="col-md-3">6. HIV/AIDS:</div>
                                        <div class="col-md-9">
                                            <select name="hiv_aids" id="hiv_aids" style="width: 100%;" required>
                                                <option value="">Select</option>
                                                <option value="1">Yes</option>
                                                <option value="2">No</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row-form clearfix" id="hiv_aids_medicatn1">
                                        <div class="col-md-3">6. Is the patient on Medication?</div>
                                        <div class="col-md-9">
                                            <select name="hiv_aids_medicatn" id="hiv_aids_medicatn" style="width: 100%;">
                                                <option value="">Select</option>
                                                <option value="1">Yes</option>
                                                <option value="2">No</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row-form clearfix" id="hiv_aids_medicatn_name">
                                        <div class="col-md-3">6. Mention the medications:</div>
                                        <div class="col-md-9"><textarea name="hiv_aids_medicatn_name" rows="4"></textarea> </div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">7. Any other medical condition:</div>
                                        <div class="col-md-9">
                                            <select name="other_medical" id="other_medical" style="width: 100%;" required>
                                                <option value="">Select</option>
                                                <option value="1">Yes</option>
                                                <option value="2">No</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row-form clearfix" id="other_specify">
                                        <div class="col-md-3">7. Specify the medical conditions?:</div>
                                        <div class="col-md-9"><textarea name="other_specify" rows="4"></textarea> </div>
                                    </div>

                                    <div class="row-form clearfix" id="other_medical_medicatn1">
                                        <div class="col-md-3">7. Is the patient on Medication?</div>
                                        <div class="col-md-9">
                                            <select name="other_medical_medicatn" id="other_medical_medicatn" style="width: 100%;">
                                                <option value="">Select</option>
                                                <option value="1">Yes</option>
                                                <option value="2">No</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row-form clearfix" id="other_medicatn_name">
                                        <div class="col-md-3">7. Mention the medications:</div>
                                        <div class="col-md-9"><textarea name="other_medicatn_name" rows="4"></textarea> </div>
                                    </div>


                                    <div class="head clearfix">
                                        <div class="isw-ok"></div>
                                        <h1>USE OF HERBAL MEDICINES</h1>
                                    </div>


                                    <div class="row-form clearfix">
                                        <div class="col-md-3">8. Are you using NIMREGENIN herbal preparation?:</div>
                                        <div class="col-md-9">
                                            <select name="herbal" id="herbal" style="width: 100%;" required>
                                                <option value="">Select</option>
                                                <option value="1">Yes</option>
                                                <option value="2">No</option>
                                            </select>
                                        </div>
                                    </div>


                                    <div class="head clearfix" id="herbal_header">
                                        <div class="isw-ok"></div>
                                        <h1>Herbal preparation</h1>
                                    </div>

                                    <div class="row" id="herbal_preparation">
                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>NIMREGENIN</label>
                                                    <input value="NIMREGENIN" type="text" name="herbal_preparation" readonly />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Start Date:</label>
                                                    <input value="" class="validate[required]" type="text" name="herbal_start" id="herbal_start" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Ongoing?:</label>
                                                    <select name="herbal_ongoing" id="herbal_ongoing" style="width: 100%;">
                                                        <option value="">Select</option>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-2" id="herbal_end">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>End:</label>
                                                    <input value="" class="validate[required]" type="text" name="herbal_end" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Dose:</label>
                                                    <input value="" class="validate[required]" type="text" name="herbal_dose" id="herbal_dose" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Frequecy:</label>
                                                    <input value="" class="validate[required]" type="text" name="herbal_frequecy" id="herbal_frequecy" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="head clearfix">
                                        <div class="isw-ok"></div>
                                        <h1>DSTANDARD OF CARE TREATMENT
                                            Provide lists of medications
                                            and supportive care given to the COVID-19 patient (To be
                                            retrieved from patient file/medical personnel)</h1>
                                    </div>

                                    <div class="head clearfix">
                                        <div class="isw-ok"></div>
                                        <h1>(all medication should be in generic names)</h1>
                                    </div>


                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Medications :</label>
                                                    <input value="" type="text" name="standard_medication" id="standard_medication" required />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Start:</label>
                                                    <input value="" class="validate[required]" type="text" name="standard_start" id="standard_start" required />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Ongoing?:</label>
                                                    <select name="standard_ongoing" id="standard_ongoing" style="width: 100%;" required>
                                                        <option value="">Select</option>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-2" id="standard_end">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>End:</label>
                                                    <input value="" class="validate[required]" type="text" name="standard_end" />
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Dose:</label>
                                                    <input value="" class="validate[required]" type="text" name="standard_dose" id="standard_dose" required />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Frequecy:</label>
                                                    <input value="" class="validate[required]" type="text" name="standard_frequecy" id="standard_frequecy" required />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="row-form clearfix">
                                            <!-- select -->
                                            <div class="form-group">
                                                <label>Remarks:</label>
                                                <input value="" class="validate[required]" type="text" name="standard_remarks" id="standard_remarks" required />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="row-form clearfix">
                                            <!-- select -->
                                            <div class="form-group">
                                                <label>Date of Completion:</label>
                                                <input value="" class="validate[required]" type="text" name="crf1_cmpltd_date" id="crf1_cmpltd_date" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="footer tar">
                                        <input type="submit" name="add_crf1" value="Submit" class="btn btn-default">
                                    </div>
                                </form>
                            </div>
                        </div>

                    <?php } elseif ($_GET['id'] == 9) { ?>
                        <div class="col-md-offset-1 col-md-8">
                            <div class="head clearfix">
                                <div class="isw-ok"></div>
                                <h1>CRF 2</h1>
                            </div>
                            <div class="block-fluid">
                                <form id="validation" method="post">

                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Date</label>
                                                    <input value="" class="validate[required,custom[date]]" type="text" name="crf2_date" id="crf2_date" required /> <span>Example: 2023-01-01</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Height</label>
                                                    <input value="" type="text" name="height" id="height" required /> <span>cm</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Weight</label>
                                                    <input value="" type="text" name="weight" id="weight" required /> <span>kg</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>BMI</label>
                                                    <input value="" type="text" name="bmi" id="bmi" required /> <span>kg/m2:</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Time</label>
                                                    <input value="" type="text" name="time" id="time" required /> <span>(using the 24-hour format of hh: mm):</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Temperature</label>
                                                    <input value="" type="text" name="temperature" id="temperature" required /> <span>Celsius:</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Method</label>
                                                    <select name="method" id="method" style="width: 100%;" required>
                                                        <option value="">Select</option>
                                                        <option value="1">Oral</option>
                                                        <option value="2">Axillary</option>
                                                        <option value="3">Tympanic</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Oxygen Saturation</label>
                                                    <input value="" type="text" name="oxygen" id="oxygen" required /><span> % </span>
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
                                                    <input value="" type="text" name="respiratory_rate" id="respiratory_rate" required /> <span>breaths/min:</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Heart Rate</label>
                                                    <input value="" type="text" name="heart_rate" id="heart_rate" required /> <span>beats/min:</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Systolic Blood Pressure:</label>
                                                    <input value="" type="text" name="systolic" id="systolic" required /> <span>mmHg:</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Diastolic Blood Pressure</label>
                                                    <input value="" type="text" name="diastolic" id="diastolic" required /> <span>mmHg:</span>
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
                                        <div class="col-md-9"><input value="" type="text" name="time2" id="time2" required /> <span>(using the 24-hour format of hh: mm):</span></div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>General Appearance:</label>
                                                    <select name="appearance" id="appearance" style="width: 100%;" required>
                                                        <option value="">Select</option>
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
                                                    <input value="" type="text" name="appearance_comments" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Clinically Significant?:</label>
                                                    <select name="appearance_signifcnt" id="appearance_signifcnt" style="width: 100%;" required>
                                                        <option value="">Select</option>
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
                                                        <option value="">Select</option>
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
                                                    <input value="" type="text" name="heent_comments" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Clinically Significant?:</label>
                                                    <select name="heent_signifcnt" id="heent_signifcnt" style="width: 100%;" required>
                                                        <option value="">Select</option>
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
                                                        <option value="">Select</option>
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
                                                    <input value="" type="text" name="respiratory_comments" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Clinically Significant?:</label>
                                                    <select name="respiratory_signifcnt" id="respiratory_signifcnt" style="width: 100%;" required>
                                                        <option value="">Select</option>
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
                                                        <option value="">Select</option>
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
                                                    <input value="" type="text" name="cardiovascular_comments" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Clinically Significant?:</label>
                                                    <select name="cardiovascular_signifcnt" id="cardiovascular_signifcnt" style="width: 100%;" required>
                                                        <option value="">Select</option>
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
                                                        <option value="">Select</option>
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
                                                    <input value="" type="text" name="abdnominal_comments" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Clinically Significant?:</label>
                                                    <select name="abdnominal_signifcnt" id="abdnominal_signifcnt" style="width: 100%;" required>
                                                        <option value="">Select</option>
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
                                                        <option value="">Select</option>
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
                                                    <input value="" type="text" name="urogenital_comments" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Clinically Significant?:</label>
                                                    <select name="urogenital_signifcnt" id="urogenital_signifcnt" style="width: 100%;" required>
                                                        <option value="">Select</option>
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
                                                        <option value="">Select</option>
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
                                                    <input value="" type="text" name="musculoskeletal_comments" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Clinically Significant?:</label>
                                                    <select name="musculoskeletal_signifcnt" id="musculoskeletal_signifcnt" style="width: 100%;" required>
                                                        <option value="">Select</option>
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
                                                        <option value="">Select</option>
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
                                                    <input value="" type="text" name="neurological_comments" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Clinically Significant?:</label>
                                                    <select name="neurological_signifcnt" id="neurological_signifcnt" style="width: 100%;" required>
                                                        <option value="">Select</option>
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
                                                        <option value="">Select</option>
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
                                                    <input value="" type="text" name="psychological_comments" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Clinically Significant?:</label>
                                                    <select name="psychological_signifcnt" id="psychological_signifcnt" style="width: 100%;" required>
                                                        <option value="">Select</option>
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
                                                        <option value="">Select</option>
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
                                                    <input value="" type="text" name="endocrime_comments" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Clinically Significant?:</label>
                                                    <select name="endocrime_signifcnt" id="endocrime_signifcnt" style="width: 100%;" required>
                                                        <option value="">Select</option>
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
                                                    <label>Hematological/Lymphatic:</label>
                                                    <select name="hematological" id="hematological" style="width: 100%;" required>
                                                        <option value="">Select</option>
                                                        <option value="1">Normal</option>
                                                        <option value="2">Abnormal</option>
                                                        <option value="3">Not examined</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-sm-4" id="hematological_comments">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Comments:</label>
                                                    <input value="" type="text" name="hematological_comments" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Clinically Significant?:</label>
                                                    <select name="hematological_signifcnt" id="hematological_signifcnt" style="width: 100%;" required>
                                                        <option value="">Select</option>
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
                                                        <option value="">Select</option>
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
                                                    <input value="" type="text" name="skin_comments" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Clinically Significant?:</label>
                                                    <select name="skin_signifcnt" id="skin_signifcnt" style="width: 100%;" required>
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
                                                    <label>Other System?:</label>
                                                    <select name="physical_exams_other" id="physical_exams_other" style="width: 100%;" required>
                                                        <option value="">Select</option>
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
                                                    <input value="" type="text" name="physical_other_specify" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-3" id="physical_other_system1">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Finding:</label>
                                                    <select name="physical_other_system" id="physical_other_system" style="width: 100%;" required>
                                                        <option value="">Select</option>
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
                                                    <input value="" type="text" name="physical_other_comments" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Clinically Significant?:</label>
                                                    <select name="physical_other_signifcnt" id="physical_other_signifcnt" style="width: 100%;" required>
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
                                                    <label>Additional Notes:</label>
                                                    <input value="" type="text" name="additional_notes" id="additional_notes" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Physical Examination performed by:</label>
                                                    <input value="" type="text" name="physical_performed" id="physical_performed" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row-form clearfix" id="crf2_cmpltd_date">
                                        <div class="col-md-3">Date of Completion</div>
                                        <input value="" class="validate[required]" type="text" name="crf2_cmpltd_date" />
                                    </div>

                                    <div class="footer tar">
                                        <input type="submit" name="add_crf2" value="Submit" class="btn btn-default">
                                    </div>
                                </form>
                            </div>
                        </div>

                    <?php } elseif ($_GET['id'] == 10) { ?>
                        <div class="col-md-offset-1 col-md-8">
                            <div class="head clearfix">
                                <div class="isw-ok"></div>
                                <h1>CRF 3</h1>
                            </div>
                            <div class="block-fluid">
                                <form id="validation" method="post">
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Date:</div>
                                        <div class="col-md-9"><input value="" class="validate[required,custom[date]]" type="text" name="crf3_date" id="crf3_date" required /> <span>Example: 2023-01-01</span></div>
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
                                                    <label>F. Vomiting:</label>
                                                    <select name="vomit" id="vomit" style="width: 100%;" required>
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
                                                    <select name="headache" id="headache" style="width: 100%;" required>
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
                                                    <select name="difficult" id="difficult" style="width: 100%;" required>
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
                                                    <select name="throat" id="throat" style="width: 100%;" required>
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
                                                    <select name="muscle" id="muscle" style="width: 100%;" required>
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
                                                    <select name="smell" id="smell" style="width: 100%;" required>
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
                                                    <select name="smell" id="smell" style="width: 100%;" required>
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
                                                    <select name="smell" id="smell" style="width: 100%;" required>
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
                                                    <select name="smell" id="smell" style="width: 100%;" required>
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
                                                    <select name="smell" id="smell" style="width: 100%;" required>
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
                                                    <select name="smell" id="smell" style="width: 100%;" required>
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
                                                    <select name="smell" id="smell" style="width: 100%;" required>
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
                                                    <select name="smell" id="smell" style="width: 100%;" required>
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
                                                    <select name="smell" id="smell" style="width: 100%;" required>
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
                                                    <select name="smell" id="smell" style="width: 100%;" required>
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
                                                    <input value="" type="text" name="symptoms_other_specify" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="row-form clearfix">
                                            <!-- select -->
                                            <div class="form-group">
                                                <label>S. Comments:</label>
                                                <input value="" type="text" name="other_comments" id="other_comments" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="row-form clearfix">
                                            <!-- select -->
                                            <div class="form-group">
                                                <label>Date of Completion:</label>
                                                <input value="" class="validate[required]" type="text" name="crf4_cmpltd_date" id="crf1_cmpltd_date" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="footer tar">
                                        <input type="submit" name="add_crf3" value="Submit" class="btn btn-default">
                                    </div>
                                </form>
                            </div>
                        </div>

                    <?php } elseif ($_GET['id'] == 11) { ?>
                        <div class="col-md-offset-1 col-md-8">
                            <div class="head clearfix">
                                <div class="isw-ok"></div>
                                <h1>CRF 4</h1>
                            </div>
                            <div class="block-fluid">
                                <form id="validation" method="post">
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Date of Sample Collection:</div>
                                        <div class="col-md-9"><input value="" class="validate[required,custom[date]]" type="text" name="sample_date" id="sample_date" required /> <span>Example: 2023-01-01</span></div>
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
                                                    <input value="" type="text" name="renal_urea" id="renal_urea" />
                                                    <SPan>XX.X ( mg/dl )</SPan>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Serum creatinine levels</label>
                                                    <input value="" type="text" name="renal_creatinine" id="renal_creatinine" />
                                                    <SPan>X.X ( mg/dl )</SPan>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>eGFR mL/min per 1.73 m2</label>
                                                    <input value="" type="text" name="renal_egfr" id="renal_egfr" />
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
                                                    <input value="" type="text" name="liver_ast" id="liver_ast" />
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
                                                        <option value="">Select</option>
                                                        <option value="1">Zero</option>
                                                        <option value="2">One</option>
                                                        <option value="3">Two</option>
                                                        <option value="4">Three</option>
                                                        <option value="5">Four</option>
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
                                                    <input value="" type="text" name="liver_alt" id="liver_alt" />
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
                                                        <option value="">Select</option>
                                                        <option value="1">Zero</option>
                                                        <option value="2">One</option>
                                                        <option value="3">Two</option>
                                                        <option value="4">Three</option>
                                                        <option value="5">Four</option>
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
                                                    <input value="" type="text" name="liver_alp" id="liver_alp" />
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
                                                        <option value="">Select</option>
                                                        <option value="1">Zero</option>
                                                        <option value="2">One</option>
                                                        <option value="3">Two</option>
                                                        <option value="4">Three</option>
                                                        <option value="5">Four</option>
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
                                                    <input value="" type="text" name="liver_pt" id="liver_pt" />
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
                                                        <option value="">Select</option>
                                                        <option value="1">Zero</option>
                                                        <option value="2">One</option>
                                                        <option value="3">Two</option>
                                                        <option value="4">Three</option>
                                                        <option value="5">Four</option>
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
                                                    <input value="" type="text" name="liver_ptt" id="liver_ptt" />
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
                                                        <option value="">Select</option>
                                                        <option value="1">Zero</option>
                                                        <option value="2">One</option>
                                                        <option value="3">Two</option>
                                                        <option value="4">Three</option>
                                                        <option value="5">Four</option>
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
                                                    <input value="" type="text" name="liver_inr" id="liver_inr" />
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
                                                        <option value="">Select</option>
                                                        <option value="1">Zero</option>
                                                        <option value="2">One</option>
                                                        <option value="3">Two</option>
                                                        <option value="4">Three</option>
                                                        <option value="5">Four</option>
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
                                                    <input value="" type="text" name="liver_ggt" id="liver_ggt" />
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
                                                    <input value="" type="text" name="liver_albumin" id="liver_albumin" />
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
                                                        <option value="">Select</option>
                                                        <option value="1">Zero</option>
                                                        <option value="2">One</option>
                                                        <option value="3">Two</option>
                                                        <option value="4">Three</option>
                                                        <option value="5">Four</option>
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
                                                    <input value="" type="text" name="liver_bilirubin_total" id="liver_bilirubin_total" />
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
                                                        <option value="">Select</option>
                                                        <option value="1">Zero</option>
                                                        <option value="2">One</option>
                                                        <option value="3">Two</option>
                                                        <option value="4">Three</option>
                                                        <option value="5">Four</option>
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
                                                    <input value="" type="text" name="liver_bilirubin_direct" id="liver_bilirubin_direct" />
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
                                                        <option value="">Select</option>
                                                        <option value="1">Zero</option>
                                                        <option value="2">One</option>
                                                        <option value="3">Two</option>
                                                        <option value="4">Three</option>
                                                        <option value="5">Four</option>
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
                                                    <input value="" type="text" name="rbg" id="rbg" />
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
                                                        <option value="">Select</option>
                                                        <option value="1">Zero</option>
                                                        <option value="2">One</option>
                                                        <option value="3">Two</option>
                                                        <option value="4">Three</option>
                                                        <option value="5">Four</option>
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
                                                    <input value="" type="text" name="hb" id="hb" />
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
                                                        <option value="">Select</option>
                                                        <option value="1">Zero</option>
                                                        <option value="2">One</option>
                                                        <option value="3">Two</option>
                                                        <option value="4">Three</option>
                                                        <option value="5">Four</option>
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
                                                    <input value="" type="text" name="hct" id="hct" />
                                                    <SPan>XX ( % )</SPan>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Red blood cell count (RBC)</label>
                                                    <input value="" type="text" name="rbc" id="rbc" />
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
                                                    <input value="" type="text" name="wbc" id="wbc" />
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
                                                        <option value="">Select</option>
                                                        <option value="1">Zero</option>
                                                        <option value="2">One</option>
                                                        <option value="3">Two</option>
                                                        <option value="4">Three</option>
                                                        <option value="5">Four</option>
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
                                                    <input value="" type="text" name="abs_lymphocytes" id="abs_lymphocytes" />
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
                                                        <option value="">Select</option>
                                                        <option value="1">Zero</option>
                                                        <option value="2">One</option>
                                                        <option value="3">Two</option>
                                                        <option value="4">Three</option>
                                                        <option value="5">Four</option>
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
                                                    <input value="" type="text" name="plt" id="plt" />
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
                                                        <option value="">Select</option>
                                                        <option value="1">Zero</option>
                                                        <option value="2">One</option>
                                                        <option value="3">Two</option>
                                                        <option value="4">Three</option>
                                                        <option value="5">Four</option>
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
                                                    <input value="" type="text" name="cancer" id="cancer" />
                                                    <SPan>XX ( U/ml )</SPan>

                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>9. PSA (Prostate specific antigen)</label>
                                                    <input value="" type="text" name="prostate" id="prostate" />
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
                                                        <option value="">Select</option>
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
                                                    <input value="" type="text" name="chest_specify" />
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
                                                        <option value="">Select</option>
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
                                                    <input value="" type="text" name="ct_chest_specify" />
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
                                                    <select name="ecg" id="ecg" style="width: 100%;" required>
                                                        <option value="">Select</option>
                                                        <option value="1">Normal</option>
                                                        <option value="2">Abnormal</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6" id="ecg_specify">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>11. Specify (Report from Radiologist)</label>
                                                    <input value="" type="text" name="ecg_specify" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row-form clearfix" id="crf4_cmpltd_date">
                                        <div class="col-md-3">Date of Completion</div>
                                        <input value="" class="validate[required]" type="text" name="crf4_cmpltd_date" id="crf1_cmpltd_date" />
                                    </div>

                                    <div class="footer tar">
                                        <input type="submit" name="add_crf4" value="Submit" class="btn btn-default">
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php } elseif ($_GET['id'] == 12) { ?>
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
                                                    <div class="col-md-9"><input value="" class="validate[required,custom[date]]" type="text" name="date_reported" id="date_reported" required /> <span>Example: 2023-01-01</span></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-8">
                                            <div class="row-form clearfix">
                                                <div class="form-group">
                                                    <label>Adverse Event Description:</label>
                                                    <textarea value="" name="ae_description" rows="4"></textarea>
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
                                                    <input value="" type="text" name="ae_category" id="ae_category" required />
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
                                                    <input value="" type="text" name="ae_start_date" id="ae_start_date" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Ongoing ?</label>
                                                    <select name="ae_ongoing" id="ae_ongoing" style="width: 100%;" required>
                                                        <option value="">Select</option>
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
                                                    <input value="" type="text" name="ae_end_date" />
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
                                                        <option value="">Select</option>
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
                                                        <option value="">Select</option>
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
                                                    <label>Expected</label>
                                                    <select name="ae_expected" id="ae_expected" style="width: 100%;" required>
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
                                                    <label>Treatment</label>
                                                    <select name="ae_treatment" id="ae_treatment" style="width: 100%;" required>
                                                        <option value="">Select</option>
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
                                                        <option value="">Select</option>
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
                                                        <option value="">Select</option>
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
                                                    <input value="" type="text" name="ae_staff_initial" id="ae_staff_initial" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Date:</label>
                                                    <input value="" type="text" name="ae_date" id="ae_date" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="footer tar">
                                        <input type="submit" name="add_crf5" value="Submit" class="btn btn-default">
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php } elseif ($_GET['id'] == 13) { ?>
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
                                                    <div class="col-md-9"><input value="" class="validate[required,custom[date]]" type="text" name="today_date" id="today_date" required /> <span>Example: 2023-01-01</span></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <div class="form-group">
                                                    <label>1.b Date patient terminated the study:</label>
                                                    <div class="col-md-9"><input value="" class="validate[required,custom[date]]" type="text" name="terminate_date" id="terminate_date" required /> <span>Example: 2023-01-01</span></div>
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
                                                        <option value="">Select</option>
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
                                                        <option value="">Select</option>
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
                                                        <option value="">Select</option>
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
                                                    <input value="" type="text" name="start_date" id="start_date" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>2.a.ii End date:</label>
                                                    <input value="" type="text" name="end_date" id="end_date" />
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
                                                    <input value="" type="text" name="date_death" id="date_death" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>2. b.ii The primary cause of death</label>
                                                    <textarea value="" name="primary_cause" rows="4"></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>2. b.iii The secondary cause of death</label>
                                                    <textarea value="" name="secondary_cause" rows="4"></textarea>
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
                                                    <select name="withdrew_reason" id="withdrew_reason" style="width: 100%;" >
                                                        <option value="">Select</option>
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
                                                    <textarea value="" name="withdrew_other" rows="4"></textarea>
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
                                                        <option value="">Select</option>
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
                                                    <input value="" type="text" name="outcome_date" id="outcome_date" />
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
                                                    <textarea value="" name="summary" rows="4"></textarea>
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
                                                    <input value="" type="text" name="clinician_name" id="clinician_name" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row-form clearfix">
                                                <!-- select -->
                                                <div class="form-group">
                                                    <label>Date of Completion</label>
                                                    <input value="" class="validate[required]" type="text" name="crf6_cmpltd_date" id="crf6_cmpltd_date" />
                                                    <span>Example : 2002-08-21</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="footer tar">
                                        <input type="submit" name="add_crf6" value="Submit" class="btn btn-default">
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php } elseif ($_GET['id'] == 14) { ?>
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
                    <?php } elseif ($_GET['id'] == 15) { ?>
                    <?php } elseif ($_GET['id'] == 16 && $user->data()->position == 1) { ?>

                    <?php } ?>
                    <div class="dr"><span></span></div>
                </div>

            </div>
        </div>
    </div>


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

        $(document).ready(function() {
            $('#fl_wait').hide();
            $('#wait_ds').hide();
            $('#region').change(function() {
                var getUid = $(this).val();
                $('#wait_ds').show();
                $.ajax({
                    url: "process.php?cnt=region",
                    method: "GET",
                    data: {
                        getUid: getUid
                    },
                    success: function(data) {
                        $('#ds_data').html(data);
                        $('#wait_ds').hide();
                    }
                });
            });
            $('#wait_wd').hide();
            $('#ds_data').change(function() {
                $('#wait_wd').hide();
                var getUid = $(this).val();
                $.ajax({
                    url: "process.php?cnt=district",
                    method: "GET",
                    data: {
                        getUid: getUid
                    },
                    success: function(data) {
                        $('#wd_data').html(data);
                        $('#wait_wd').hide();
                    }
                });

            });

            $('#a_cc').change(function() {
                var getUid = $(this).val();
                $('#wait').show();
                $.ajax({
                    url: "process.php?cnt=payAc",
                    method: "GET",
                    data: {
                        getUid: getUid
                    },
                    success: function(data) {
                        $('#cus_acc').html(data);
                        $('#wait').hide();
                    }
                });

            });


            // $('#study_id').change(function() {
            //     var getUid = $(this).val();
            //     var type = $('#type').val();
            //     $('#fl_wait').show();
            //     $.ajax({
            //         url: "process.php?cnt=study",
            //         method: "GET",
            //         data: {
            //             getUid: getUid,
            //             type: type
            //         },
            //         success: function(data) {
            //             $('#s2_2').html(data);
            //             $('#fl_wait').hide();
            //         }
            //     });

            // });


            $('#study_id').change(function() {
                var getUid = $(this).val();
                var type = $('#type').val();
                $('#fl_wait').show();
                $.ajax({
                    url: "process.php?cnt=study",
                    method: "GET",
                    data: {
                        getUid: getUid,
                        type: type
                    },

                    success: function(data) {
                        console.log(data);
                        $('#s2_2').html(data);
                        $('#fl_wait').hide();
                    }
                });

            });

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


            $('#herbal_preparation').hide();
            $('#herbal_header').hide();
            $('#herbal').change(function() {
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
                if (getUid === "1") {
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


            $('#appearance_comments').hide();
            $('#appearance').change(function() {
                var getUid = $(this).val();
                if (getUid === "2") {
                    $('#appearance_comments').show();
                } else {
                    $('#appearance_comments').hide();
                }
            });

            $('#heent_comments').hide();
            $('#heent').change(function() {
                var getUid = $(this).val();
                if (getUid === "2") {
                    $('#heent_comments').show();
                } else {
                    $('#heent_comments').hide();
                }
            });

            $('#respiratory_comments').hide();
            $('#respiratory').change(function() {
                var getUid = $(this).val();
                if (getUid === "2") {
                    $('#respiratory_comments').show();
                } else {
                    $('#respiratory_comments').hide();
                }
            });

            $('#cardiovascular_comments').hide();
            $('#cardiovascular').change(function() {
                var getUid = $(this).val();
                if (getUid === "2") {
                    $('#cardiovascular_comments').show();
                } else {
                    $('#cardiovascular_comments').hide();
                }
            });

            $('#abdnominal_comments').hide();
            $('#abdnominal').change(function() {
                var getUid = $(this).val();
                if (getUid === "2") {
                    $('#abdnominal_comments').show();
                } else {
                    $('#abdnominal_comments').hide();
                }
            });

            $('#urogenital_comments').hide();
            $('#urogenital').change(function() {
                var getUid = $(this).val();
                if (getUid === "2") {
                    $('#urogenital_comments').show();
                } else {
                    $('#urogenital_comments').hide();
                }
            });


            $('#musculoskeletal_comments').hide();
            $('#musculoskeletal').change(function() {
                var getUid = $(this).val();
                if (getUid === "2") {
                    $('#musculoskeletal_comments').show();
                } else {
                    $('#musculoskeletal_comments').hide();
                }
            });

            $('#neurological_comments').hide();
            $('#neurological').change(function() {
                var getUid = $(this).val();
                if (getUid === "2") {
                    $('#neurological_comments').show();
                } else {
                    $('#neurological_comments').hide();
                }
            });

            $('#psychological_comments').hide();
            $('#psychological').change(function() {
                var getUid = $(this).val();
                if (getUid === "2") {
                    $('#psychological_comments').show();
                } else {
                    $('#psychological_comments').hide();
                }
            });

            $('#endocrime_comments').hide();
            $('#endocrime').change(function() {
                var getUid = $(this).val();
                if (getUid === "2") {
                    $('#endocrime_comments').show();
                } else {
                    $('#endocrime_comments').hide();
                }
            });

            $('#hematological_comments').hide();
            $('#hematological').change(function() {
                var getUid = $(this).val();
                if (getUid === "2") {
                    $('#hematological_comments').show();
                } else {
                    $('#hematological_comments').hide();
                }
            });

            $('#skin_comments').hide();
            $('#skin').change(function() {
                var getUid = $(this).val();
                if (getUid === "2") {
                    $('#skin_comments').show();
                } else {
                    $('#skin_comments').hide();
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
            $('#physical_other_system').change(function() {
                var getUid = $(this).val();
                if (getUid === "2") {
                    $('#physical_other_comments').show();
                } else {
                    $('#physical_other_comments').hide();
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

            $('#ae_end_date').hide();
            $('#ae_ongoing').change(function() {
                var getUid = $(this).val();
                if (getUid === "1") {
                    $('#ae_end_date').show();
                } else {
                    $('#ae_end_date').hide();
                }
            }); 
            
            $('#start_end_date').hide();
            $('#completed120days').change(function() {
                var getUid = $(this).val();
                if (getUid === "1") {
                    $('#start_end_date').show();
                } else {
                    $('#start_end_date').hide();
                }
            }); 


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
            
            

        });
    </script>
</body>

</html>