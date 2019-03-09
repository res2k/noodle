<?php
ob_start("ob_gzhandler");

require("dbconfig.php");
require("config.php");
require("helper.php");
require("german.php");
require_once("Smarty/libs/Smarty.class.php");

date_default_timezone_set('Europe/Berlin');

// connect to mysql database
$link = mysqli_connect ($dbhost,$dbuser,$dbpassword,$dbname);

/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

/* change character set to utf8mb4 */
if (!$link->set_charset("utf8mb4")) {
    printf("Error loading character set utf8mb4: %s\n", $link->error);
    exit();
}

// start template engine
$smarty = new Smarty;

// get path
if ( isset($_GET['q']) && $_GET['q'] == 'admin' )
{
    $query = 'admin';
}
else
{
    $query = 'default';
}

$smarty->assign('query',$query);

if ( $query == 'default' )
{
    include('default.php');
}

if ( $query == 'admin' )
{
    include('admin.php');
}


// show!
$smarty->display('noodle.tpl');

mysqli_close($link);
