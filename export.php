<?php

require("dbconfig.php");
require("config.php");
require("helper.php");
require("german.php");
require_once("Smarty/libs/Smarty.class.php");

date_default_timezone_set('Europe/Berlin');

$link = mysqli_connect ($dbhost,$dbuser,$dbpassword,$dbname);

/* change character set to utf8mb4 */
if (!$link->set_charset("utf8mb4")) {
    printf("Error loading character set utf8mb4: %s\n", $link->error);
    exit();
}

$tid = filter_var($_GET['tid']);

$sql = "SELECT * FROM `training`
    LEFT JOIN semester ON semester.ID = training.semesterID
    WHERE training.ID = '".$tid."'";

$result = mysqli_query($link, $sql);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

$from = $row['from'];
$to = $row['to'];
$dow = $row['day'];

$days = noodle_create_calendar($from,$to,array($dow),$tid);
$days_string = implode($days['days'],"','");

$sql = "SELECT * FROM `user`
    LEFT JOIN attendance ON user.name = attendance.user
    WHERE attendance.tid = ".$tid."
        AND attendance.date IN ('".$days_string."')
    ORDER BY `name`,`date`
";
$result = mysqli_query($link, $sql);

$tbl = array();
$dates = $days['days'];
$users = array();

while ( $row = mysqli_fetch_array($result))
{
    if ( $row['type'] == 'yes' )
        $symbol = 'J';
    elseif ( $row['type'] == 'no' )
         $symbol = 'n';
    else
        $symbol = '';

    $tbl[$row['name']][$row['date']]['symbol'] = $symbol;
    $tbl[$row['name']][$row['date']]['type'] = $row['type'];
    $users[] = $row['name'];
}

$users = array_unique($users);

$datesF = array();
foreach ( $dates as $date )
{
    $datesF[] = substr($date,8,2).".".substr($date,5,2);
}

// start template engine
$smarty = new Smarty;


$smarty->assign('table', $tbl);
$smarty->assign('dates', $dates);
$smarty->assign('datesF', $datesF);
$smarty->assign('users', $users);

$smarty->display('export.tpl');

mysqli_close($link);

