<?php
ob_start("ob_gzhandler");

require("config.php");
require_once("Smarty/libs/Smarty.class.php");

date_default_timezone_set('Europe/Berlin');

// start template engine
$smarty = new Smarty;

$smarty->assign('feedback_mail', $feedback_mail);

// show!
$smarty->display('helppage.tpl');
