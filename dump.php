<?php
require("dbconfig.php");
$link = mysqli_connect ($dbhost,$dbuser,$dbpassword,$dbname);

$result = mysqli_query($link,"SELECT * FROM attendance");

echo mysqli_error($link);
while ( $row = mysqli_fetch_array($result,MYSQLI_NUM))
{
    echo implode(",",$row)."\n";
}

?>
