<?php
require_once 'php/core/init.php';
$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();

$x = 1;

while($x<417){
    if($x<10){
        $MM = '1-00'.$x;
        $BG = '2-00'.$x;
        $KC = '3-00'.$x;
        $MU = '4-00'.$x;
    }elseif($x<100){
        $MM = '1-0'.$x;
        $BG = '2-0'.$x;
        $KC = '3-0'.$x;
        $MU = '4-0'.$x;
    }else{
        $MM = '1-'.$x;
        $BG = '2-'.$x;
        $KC = '3-'.$x;
        $MU = '4-'.$x;
    }
    if($x<59){
        $user->createRecord('study_id',array(
            'study_id' => $MM,
            'client_id' => 0,
            'site_id' => 1,
            'status' => 0,
        ));
    }
    if($x<105){
        $user->createRecord('study_id',array(
            'study_id' => $BG,
            'client_id' => 0,
            'site_id' => 2,
            'status' => 0,
        ));
    }
    if($x<109){
        $user->createRecord('study_id',array(
            'study_id' => $KC,
            'client_id' => 0,
            'site_id' => 3,
            'status' => 0,
        ));
    }
    if($x<147){
        $user->createRecord('study_id',array(
            'study_id' => $MU,
            'client_id' => 0,
            'site_id' => 4,
            'status' => 0,
        ));
    }
    echo $MM.' : '. $BG.' , '.' : '. $KC.' : '. $MU;
    $x++;
}