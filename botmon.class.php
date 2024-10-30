<?php


class Botmon {

	private static $apiKey = null;
	//private static $apiServerBaseUrl = 'http://api.botmon/v100/'; // DEV server
	private static $apiServerBaseUrl = 'http://api.botmonitoring.com/v100/'; // LIVE server

	public static function plugin_activation()
	{
		$options = get_option('botmon_options', array('ads' => array(), 'api_key' => ''));
		
		// init files
		
		if( !file_exists(BOTMON__PLUGIN_DIR.'d') ) @mkdir(BOTMON__PLUGIN_DIR.'d', 0777);
		if( !is_writable(BOTMON__PLUGIN_DIR.'d') ) trigger_error(BOTMON__PLUGIN_DIR.' should be server writable directory',E_USER_ERROR);
			
		file_put_contents(BOTMON__PLUGIN_DIR.'d/apikey.txt', $options['api_key']);
		file_put_contents(BOTMON__PLUGIN_DIR.'d/botmonitoring.dat', '');
		self::updateDataFile();
		file_put_contents(BOTMON__PLUGIN_DIR.'d/isactive.txt', '1');
	}

	public static function plugin_deactivation()
	{
		unlink(BOTMON__PLUGIN_DIR.'d/isactive.txt');
		unlink(BOTMON__PLUGIN_DIR.'d/apikey.txt');
		unlink(BOTMON__PLUGIN_DIR.'d/botmonitoring.dat');
		rmdir(BOTMON__PLUGIN_DIR.'d');
	}

	public static function load_resources()
	{
		wp_register_script( 'bma.js', BOTMON__PLUGIN_URL . 'bma.js', array('jquery'), BOTMON_VERSION );
		wp_enqueue_script( 'bma.js' );
	}

	public static function header()
	{
		$adsToControl = self::getAdsControlled();
		echo("<script>jQuery(document).ready(function($){(new Bma('".BOTMON_JS_CLIENT_VERSION."','".BOTMON__PLUGIN_URL."','".$adsToControl."')).a();});</script>");
	}

	public static function collectFingerprintData($ip, $postData)
	{
		$data = "FP\n".$ip."\n".$postData;

		self::sendMainApiData($data);
	}

	public static function collectRefererData($ip, $postData)
	{
		$data = "RF\n".$ip."\n".$postData;

		self::sendMainApiData($data);
	}

	private static function sendMainApiData($data)
	{
		$key = self::getMainApiKey();
		if( empty($key) ) return;
		$urlRequest = self::$apiServerBaseUrl.'collectData/'.$key;

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $urlRequest );
		curl_setopt($ch, CURLOPT_HEADER, 0 );
		curl_setopt($ch, CURLOPT_VERBOSE, 0 );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_POST, true );
		curl_setopt($ch, CURLOPT_TIMEOUT, 3 );
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_exec($ch);
		curl_close($ch);
	}

	private static function getMainApiKey()
	{
		if( is_null(self::$apiKey) )
		{
			self::$apiKey = trim(file_get_contents(BOTMON__PLUGIN_DIR.'d/apikey.txt'));
		}

		return self::$apiKey;
	}

	public static function isBlacklistedIp($ip)
	{
		$fnameDataFile=BOTMON__PLUGIN_DIR.'d/botmonitoring.dat';
		if(!is_file($fnameDataFile))return false;
		if(filesize($fnameDataFile)<10) return false;
		$f=fopen($fnameDataFile,'r');if(!$f)return null;
		$n=current(unpack('n',fread($f,2)));$o=explode('.',$ip);fseek($f,2+2*((256*$o[0])+$o[1]));
		$d=current(unpack('n',fread($f,2)));if($d==0x8000)return false;fseek($f,131074+($d*512)+($o[2]*2));
		$e=current(unpack('n',fread($f,2)));if($e==0x8000)return true;if($e==0)return false;
		$l=floor($o[3]/8);fseek($f,131074+($n)*512+(($e-1)*32)+$l);$y=current(unpack('C',fread($f,1)));
		if($y&pow(2,(8-($o[3]-$l*8))-1))return true;return false;
	}

	public static function updateDataFile()
	{
		$fnameDataFile = BOTMON__PLUGIN_DIR.'d/botmonitoring.dat';

		if(!is_writable($fnameDataFile))
		{
			echo('not writable data file');
			return;
		}

		$apiKey = self::getMainApiKey();
		if( empty($apiKey) ) return;
		$urlRequest = self::$apiServerBaseUrl.'standaloneModuleDatabaseFetch/'.$apiKey.'?md5='.md5_file($fnameDataFile);

		$tmpFname = tempnam(sys_get_temp_dir(), 'botmon_data');

		if( false !== $tmpFname )
		{
			$result = copy($urlRequest, $tmpFname);

			if( $result===false) echo('error fetching dat file');

			if( filesize($tmpFname)>100000 )
			{
				copy($tmpFname, $fnameDataFile);
			}
			else
			{
				// no updates	
			}

			unlink($tmpFname);
		}
		else
		{
			echo('error creating temp file name');
		}
	}

	private static function getAdsControlled()
	{
		static $ads = null;
		if(is_null($ads)) {
			$options = get_option('botmon_options', array('ads' => array()));
			$ads = join(' ', $options['ads']);
		}
		return $ads;
	}

	public static function logAdClick($ip, $fp)
	{
		$key = self::getMainApiKey();
		if( empty($key) ) return;
		$urlRequest = self::$apiServerBaseUrl.'logAdClick/'.$key.'?I='.$ip.'&fp='.$fp;

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $urlRequest );
		curl_setopt($ch, CURLOPT_HEADER, 0 );
		curl_setopt($ch, CURLOPT_VERBOSE, 0 );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_POST, true );
		curl_setopt($ch, CURLOPT_TIMEOUT, 3 );
		curl_exec($ch);
		curl_close($ch);
	}

	public static function logClientBlockedAdClick($ip, $fp)
	{
		$key = self::getMainApiKey();
		if( empty($key) ) return;
		$urlRequest = self::$apiServerBaseUrl.'logClientBlocked/'.$key.'?I='.$ip.'&fp='.$fp;

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $urlRequest );
		curl_setopt($ch, CURLOPT_HEADER, 0 );
		curl_setopt($ch, CURLOPT_VERBOSE, 0 );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_POST, true );
		curl_setopt($ch, CURLOPT_TIMEOUT, 3 );
		curl_exec($ch);
		curl_close($ch);
	}

	public static function collectBlockedCase($ip, $fp)
	{
		$key = self::getMainApiKey();
		if( empty($key) ) return;
		$urlRequest = self::$apiServerBaseUrl.'logIpBlocked/'.$key.'?I='.$ip.'&fp='.$fp;

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $urlRequest );
		curl_setopt($ch, CURLOPT_HEADER, 0 );
		curl_setopt($ch, CURLOPT_VERBOSE, 0 );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_POST, true );
		curl_setopt($ch, CURLOPT_TIMEOUT, 3 );
		curl_exec($ch);
		curl_close($ch);

	}

}

