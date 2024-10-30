<?php
define( 'BOTMON__PLUGIN_DIR', dirname( __FILE__ ).'/' );
file_exists(BOTMON__PLUGIN_DIR.'d/isactive.txt') or die('');
require_once(BOTMON__PLUGIN_DIR.'botmon.class.php');
$ip = @$_SERVER['REMOTE_ADDR'];
$postData = file_get_contents("php://input");

switch( @$_REQUEST['a'] ) 
{
	case 'f':
		Botmon_releaseClient();
		Botmon::collectFingerprintData($ip, $postData);
		break;
	
	case 'r':
		Botmon_releaseClient();
		Botmon::collectRefererData($ip, $postData);
		break;
	
	case 'c':
		Botmon_releaseClient();
		Botmon::logAdClick($ip, $postData);
		break;
	
	case 'h':
		$result = Botmon::isBlacklistedIp($ip)?'B':' ';
		header("Connection: close");ignore_user_abort(); ob_start(); echo($result); $size=ob_get_length();
		header("Content-Length: ".$size); ob_end_flush(); flush();
		if( $result=='B' ) Botmon::collectBlockedCase($ip, $postData);
		break;
	
	case 'b':
		Botmon_releaseClient();
		Botmon::logClientBlockedAdClick($ip, $postData);
		break;
		
}
exit();

function Botmon_releaseClient()
{
	header("Connection: close");ignore_user_abort();ob_start();echo(' ');
	header("Content-Length: 1");ob_end_flush();flush();
}
