<?php
session_start();

$GLOBALS['config'] = array(
  'mysql' => array(
      'host' => 'localhost',
      'username' => 'root',
      'password' => 'Data@2020',
      'db' => 'nimregenin'
  	),
  'remember' =>array(
      'cookie_name' => 'hash',
      'cookie_expiry' => '3600'
  	),
  'session' =>array (
     'session_name' =>'user',
     'token_name' => 'token',
      'session_table' => 'tableName'
  	 )
);

spl_autoload_register(function($class){
	require_once 'php/classes/'.$class.'.php';
});

// Include Dompdf dependencies
require_once 'dompdf/lib/php-font-lib/src/FontLib/Autoloader.php';
require_once 'dompdf/lib/php-svg-lib/src/autoload.php';
require_once 'dompdf/src/Autoloader.php';

Dompdf\Autoloader::register();

date_default_timezone_set("Africa/Dar_es_Salaam");
include_once 'php/functions/sanitize.php';
include_once 'php/classes/OverideData.php';
include_once 'php/classes/class.upload.php';
include_once "php/phpMailer/PHPMailer.php";
include_once "php/phpMailer/Exception.php";
include_once "php/phpMailer/SMTP.php";
include_once "php/phpMailer/POP3.php";
include_once "php/phpMailer/OAuth.php";


if(Cookie::exists(config::get('remember/cookie_name')) && !Session::exists(config::get('session/session_name'))){
$hash= Cookie::get(config::get('remember/cookie_name'));
$hashCheck = DB::getInstance()->get('user_session',array('hash','=',$hash));
if($hashCheck->count()){

 $user = new User($hashCheck->first()->user_id);
 $user->login();
 }
}