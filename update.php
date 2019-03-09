<?php
require("dbconfig.php");
$link = mysqli_connect ($dbhost,$dbuser,$dbpassword,$dbname);

/*
mysqli_query($link,"INSERT INTO `training` (`ID`, `day`, `semesterID`, `from`, `to`) VALUES
(7, 7, 1, '18:30:00', '20:00:00')");
echo mysqli_error($link);
*/
/*
mysqli_query($link,"ALTER TABLE `user` DROP `test`;");
mysqli_query($link,"ALTER TABLE `user` ADD `trainings` TEXT NOT NULL ;");
echo mysqli_error($link);
*/
/*
mysqli_query($link,"ALTER TABLE `training` ADD `title` VARCHAR(100) NOT NULL ;");
mysqli_query($link,"ALTER TABLE `attendance` ADD `tid` INT NOT NULL AFTER `date`;");
echo mysqli_error($link);
*/

/*
mysqli_query($link,"DELETE FROM attendance WHERE `date` > '2015-04-12'");
*/

/*
mysqli_query($link,"INSERT INTO `training` (`day`,
`semesterID`, `from`, `to`, `title`) VALUES ('1', '5', '18:00', '20:00', 'F/A spielorientiert'),
('2', '5', '18:00', '20:00', 'Vereinstraining'),
('4', '5', '18:30', '20:00', 'Taktik A/F');");
echo mysqli_error($link);
*/

/*
$query = mysqli_query($link, "INSERT INTO `semester`
     (`title`,`from`,`to`) VALUES
     ('Semesterferien 2015','2015-07-20','2015-18-10')");
*/

/*
$query = mysqli_query($link, "SELECT MAX(ID) FROM `training`");
echo "max semester:".mysqli_fetch_array($query)[0][0];
*/

$query = mysqli_query($link, "UPDATE `semester` SET `to` = '2015-07-19'
WHERE `ID`=4");

/*
$query = mysqli_query($link, "SELECT * FROM `training`");
while($row = mysqli_fetch_array($query))
{
	print_r($row);
}

*/
$query = mysqli_query($link, "SELECT * FROM `semester`");
while($row = mysqli_fetch_array($query))
{
	print_r($row);
}


/*
mysqli_query($link, "INSERT INTO `training` (`title`,`from`,`to`,`day`,`semesterID`)
		    VALUES ('Freies Spiel','18:30','20:00',6,5)")
*/
// mysqli_query($link, "UPDATE training SET day = 7 where ID = 18");
?>
