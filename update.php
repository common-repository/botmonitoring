<?php
define( 'BOTMON__PLUGIN_DIR', dirname( __FILE__ ).'/' );
file_exists(BOTMON__PLUGIN_DIR.'d/isactive.txt') or die();
require_once(dirname(__FILE__).'/botmon.class.php');
Botmon::updateDataFile();
die(); 