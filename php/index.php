<?php

//ob_start("ob_gzhandler");
if ($_SERVER['SERVER_ADDR'] == '10.0.9.114' || $_SERVER['SERVER_ADDR'] == '172.21.0.3'):
    error_reporting(E_ALL);
    ini_set('display_errors', "1");
endif;

if (isset($_REQUEST['SCREEN']))
    $SCREEN = $_REQUEST['SCREEN'];
else
    $SCREEN = null;

define("SERVER_NAME", $_SERVER['SERVER_NAME']);

//This will usually find index.html, but you can call your
//script anything and it will work.
define("SCRIPT_FULLPATH", $_SERVER["SCRIPT_FILENAME"]);
$arrFileName = explode("/", $_SERVER["SCRIPT_FILENAME"]);
//preg_match("([^/\\]*)$i", SCRIPT_FULLPATH, $match);
define("SCRIPT_NAME", $arrFileName[count($arrFileName) - 1]);

//ini_set("session.use_cookies","1");
session_cache_limiter('nocache');
session_start();
if ($_SERVER['SERVER_ADDR'] == '10.0.9.114' || $_SERVER['SERVER_ADDR'] == '172.21.0.3'):
    define("EXTERNAL_PATH", "/var/www/horseandponydatabase.uk/");
    define("MODULE", "/var/www/vhosts/hpd.harrypi.local/modules");
else:
    define("EXTERNAL_PATH", "/var/www/vhosts/horseandponydatabase.uk");
    define("MODULE", "/var/www/vhosts/horseandponydatabase.uk/modules");
endif;

if (!include(MODULE . "/utility/configuration.php"))
    throw new Exception("Unable to include 'configuration'");
/*
 * * get library of standard functions
 */
if (!include(MODULE . "/utility/standard_library.php"))
    throw new Exception("Unable to include 'standard_library'<br>\n");


if (!include(MODULE . "/utility/initialisation.php"))
    throw new Exception("Unable to include 'initialisation'<br>\n");

$registry = new cfg_registry();
$registry->controller = MODULE . "/controller/";
$registry->screens = MODULE . "/screen/";
$registry->layout = MODULE . "/layout/";


if (isset($_REQUEST['SCREEN']))
    $strScreen = $_REQUEST['SCREEN'];
else
    $strScreen = "welcome";

/* test for login  */

function XSSFilterHtmlString($strInput) {
    return $strInput;
}

try {
    $screen = new cfg_screen($strScreen, $registry);
    //print_object($screen);
    $screen->checkAuthorisation();
    include($screen->runController());

    if (isset($SCREEN) && ($SCREEN != $screen->get_screen()))
        $screen->changeScreen($SCREEN);


    //$_SESSION['prevScreen'] = $screen->get_screen();
    $screen->set_messages();
    include_once($screen->displayScreen());
} catch (Exception $e) {
    $screen = new cfg_screen("welcome", $registry);
    $arrActionResults[] = $e->getMessage();
    $screen->set_messages();
    include_once($screen->displayScreen());
}