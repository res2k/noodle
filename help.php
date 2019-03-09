<?php
require_once("Smarty/libs/Smarty.class.php");

date_default_timezone_set('Europe/Berlin');

// start template engine
$smarty = new Smarty;

// show!
$smarty->display('helppage.tpl');
