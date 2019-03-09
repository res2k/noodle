<?php
ob_start("ob_gzhandler");

require("dbconfig.php");
require("config.php");
require("helper.php");
require("german.php");

// Hyphenator setup
$GLOBALS["language"] = "de";
$GLOBALS["path_to_patterns"] = "phpHyphenator/patterns/";
$SHY = "­"; // Note: *not* empty, contains soft hyphen
$GLOBALS["hyphen"] = $SHY;
include("phpHyphenator/hyphenation.php");

$link = mysqli_connect ($dbhost,$dbuser,$dbpassword,$dbname);

/* change character set to utf8mb4 */
if (!$link->set_charset("utf8mb4")) {
    printf("Error loading character set utf8mb4: %s\n", $link->error);
    exit();
}

function noodle_check_login ()
{
    global $hash_pass;
    if ( isset($_COOKIE['password']) AND password_verify ($hash_pass, $_COOKIE['password']) )
        return(true);
    else
        return(false);
}


if ( $_GET['action'] == 'attendanceUpdate' )
{
    $user = $_POST['user'];
    $attendance = array_combine($_POST['did'],$_POST['attend']);
    noodle_set_attendance($user,$attendance);

    echo json_encode($attendance);
}

if ( $_GET['action'] == 'attributeUpdate' )
{
    $user = $_POST['user'];
    $user_attributes = noodle_get_attributes($user);
    $user_attributes[$_POST['attr']] = $_POST['choice'];
    noodle_set_attributes($user, $user_attributes);
    echo json_encode($user_attributes);
}

if ( $_GET['action'] == 'attendUpdate' )
{
    $user = $_POST['user'];
    $user_attend = noodle_get_attend($user);
    $user_attend[$_POST['tid']] = $_POST['choice'];
    noodle_set_attend($user, $user_attend);
    echo json_encode($user_attend);
}

if ( $_GET['action'] == 'getParticipants' )
{
    $day = substr($_POST['did'],0,10);
    $tid = substr($_POST['did'],11);
    echo json_encode(noodle_get_participants($day,$tid));
}

if ( $_GET['action'] == 'getAttendance')
{
    echo json_encode(noodle_get_attendance($_POST['user']));
}

if ( $_GET['action'] == 'getAttributes')
{
    echo json_encode(noodle_get_attributes($_POST['user']));
}

if ( $_GET['action'] == 'getAttend')
{
    echo json_encode(noodle_get_attend($_POST['user']));
}


if ( $_GET['action'] == 'getNames' )
{
    echo json_encode(noodle_names_like($_GET['query']));
}

if ( $_GET['action'] == 'deleteUser' )
{
    $ret = noodle_delete_user($_POST['user']);
    echo json_encode($_POST['user']);
}

if ( $_GET['action'] == 'changeName' )
{
    $ret = noodle_change_name($_POST['oldName'], $_POST['newName']);
    echo json_encode(array('success'=>$ret));
}

if ( $_GET['action'] == 'addSemester' )
{
    if ( !noodle_check_login())
    {
        echo json_encode(array("error" => "Fehler: Sie sind nicht angemeldet!"));
        exit;
    } 
    $error = array();
    // check if empty?
    if ( empty($_POST['title']) OR empty($_POST['from']) OR empty($_POST['to']))
    {
        $error[] = 'Bitte alle Felder ausfüllen!';
    }

    $title = $_POST['title'];
    $title = hyphenation($title);
    $title = mysqli_real_escape_string($link,$title);
    $query = mysqli_query($link, "SELECT * FROM `semester`
            WHERE `title` = '".$title."'");
    if ( mysqli_num_rows($query) > 0 )
    {
        $error[] = 'Semestertitel schon vorhanden.';
    }

    $from = noodle_date_ger_eng($_POST['from']);
    $to = noodle_date_ger_eng($_POST['to']);

    if ( $from == false )
    {
        $error[] = 'Ungültiges `Beginn` Datum';
    }

    if ( $to == false )
    {
        $error[] = 'Ungültiges `Ende` Datum';
    }


    if ( count($error) > 0 )
    {
        echo json_encode(array('error' => $error));
    }
    else
    {
        $from = mysqli_real_escape_string($link,$from);
        $to = mysqli_real_escape_string($link,$to);
        
        mysqli_query($link,"INSERT INTO `semester` 
                (`title`, `from`, `to`) VALUES
                ('".$title."','".$from."','".$to."')");

        $query = mysqli_query($link,"SELECT * FROM `semester` WHERE
            `ID` = LAST_INSERT_ID()");

        $row = mysqli_fetch_array($query, MYSQLI_ASSOC);
        $row["fromF"] = noodle_date_eng_gerF($row['from']);
        $row["toF"] = noodle_date_eng_gerF($row['to']);
        
        echo json_encode($row);
    }
}


if ( $_GET['action'] == "deleteSemester" )
{
    if ( !noodle_check_login())
    {
        echo json_encode(array("error" => "Fehler: Sie sind nicht angemeldet!"));
        exit;
    } 
    $ID = mysqli_real_escape_string($link,$_POST['ID']);
    $query = mysqli_query($link, "SELECT `ID` FROM `training` WHERE `semesterID` = '".$ID."'");
    while ( $row = mysqli_fetch_array($query, MYSQLI_ASSOC))
    {
        noodle_delete_training($row['ID']);
    }
   
    mysqli_query($link, "DELETE FROM `semester` WHERE `ID` = '".$ID."'");
    
    echo json_encode(array("success" => true, 'ID' => $_POST['ID']));
}

if ( $_GET['action'] == "loadSemester" )
{
    $ID = mysqli_real_escape_string($link,$_POST['ID']);
    $query = mysqli_query($link,"SELECT * FROM `semester` WHERE
        `ID` = '".$ID."'");

    $row = mysqli_fetch_array($query, MYSQLI_ASSOC);
    $row["fromGer"] = noodle_date_eng_ger($row['from']);
    $row["toGer"] = noodle_date_eng_ger($row['to']);
    $row["title"] = str_replace($SHY, "", $row["title"]);
    
    echo json_encode($row);
}

if ( $_GET['action'] == "editSemester" )
{
    if ( !noodle_check_login())
    {
        echo json_encode(array("error" => "Fehler: Sie sind nicht angemeldet!"));
        exit;
    } 
    $error = array();
    // check if empty?
    if ( empty($_POST['title']) OR empty($_POST['from']) OR empty($_POST['to']))
    {
        $error[] = 'Bitte alle Felder ausfüllen!';
    }

    $title = $_POST['title'];
    $title = hyphenation($title);
    $title = mysqli_real_escape_string($link,$title);
    $ID = mysqli_real_escape_string($link,$_POST['ID']);
    $query = mysqli_query($link, "SELECT * FROM `semester`
            WHERE `title` = '".$title."' AND `ID` != '".$ID."'");
    if ( mysqli_num_rows($query) > 0 )
    {
        $error[] = 'Semestertitel schon vorhanden.';
    }

    $from = noodle_date_ger_eng($_POST['from']);
    $to = noodle_date_ger_eng($_POST['to']);

    if ( $from == false )
    {
        $error[] = 'Ungültiges `Beginn` Datum';
    }

    if ( $to == false )
    {
        $error[] = 'Ungültiges `Ende` Datum';
    }


    if ( count($error) > 0 )
    {
        echo json_encode(array('error' => $error));
    }
    else
    { 
        $from = mysqli_real_escape_string($link,$from);
        $to = mysqli_real_escape_string($link,$to);
        mysqli_query($link,"UPDATE `semester` SET 
            `title` = '".$title."',
            `from` = '".$from."',
            `to` = '".$to."'
            WHERE `ID` = '".$ID."'
        ");
        
        $query = mysqli_query($link,"SELECT * FROM `semester` WHERE
            `ID` = '".$ID."'");

        $row = mysqli_fetch_array($query, MYSQLI_ASSOC);
        $row["dayF"] = noodle_t_day($row["day"]); 
        $row['dayFshort'] = noodle_t_day($row['day'], "short");

        $row["fromF"] = noodle_date_eng_gerF($row['from']);
        $row["toF"] = noodle_date_eng_gerF($row['to']);
        
        echo json_encode($row);
    }


}


if ( $_GET['action'] == 'addTraining' )
{
    if ( !noodle_check_login())
    {
        echo json_encode(array("error" => "Fehler: Sie sind nicht angemeldet!"));
        exit;
    } 
    $error = array();
    // check if empty?
    if ( empty($_POST['semesterID']) OR empty($_POST['day']) OR empty($_POST['from']) OR empty($_POST['to']) OR empty($_POST['title']))
    {
        $error[] = 'Bitte alle Felder ausfüllen!';
    }

    $semesterID = mysqli_real_escape_string($link,$_POST['semesterID']);
    $day = mysqli_real_escape_string($link,$_POST['day']);

    $from = $_POST['from'].":00";
    $to = $_POST['to'].":00";

    if ( count($error) > 0 )
    {
        echo json_encode(array('error' => $error));
    }
    else
    {
        $from = mysqli_real_escape_string($link,$from);
        $to = mysqli_real_escape_string($link,$to);
        $title = $_POST['title'];
        $title = hyphenation($title);
        $title = mysqli_real_escape_string($link,$title);
        
        mysqli_query($link,"INSERT INTO `training` 
                (`semesterID`,`day`, `from`, `to`, `title`) VALUES
                ('".$semesterID."','".$day."','".$from."','".$to."','".$title."')");

        $query = mysqli_query($link,"SELECT * FROM `training` WHERE
            `ID` = LAST_INSERT_ID()");

        $row = mysqli_fetch_array($query, MYSQLI_ASSOC);
        $row["fromF"] = substr($row['from'],0,5);
        $row["toF"] = substr($row['to'],0,5);
        $row["dayF"] = noodle_t_day($row["day"]); 
        $row['dayFshort'] = noodle_t_day($row['day'], "short");

        $query = mysqli_query($link, "SELECT * FROM `semester`
            WHERE `ID` = '".$semesterID."'");

        $semester = mysqli_fetch_array($query, MYSQLI_ASSOC);

        $row["semesterF"] = $semester['title'];
        echo json_encode($row);
    }
}


if ( $_GET['action'] == "deleteTraining" )
{
    if ( !noodle_check_login())
    {
        echo json_encode(array("error" => "Fehler: Sie sind nicht angemeldet!"));
        exit;
    } 
    
    $ID = mysqli_real_escape_string($link,$_POST['ID']);
    mysqli_query($link, "DELETE FROM `training` WHERE `ID` = '".$ID."'");
    noodle_delete_training($_POST['ID']); 
    echo json_encode(array("success" => true, 'ID' => $_POST['ID']));
}

if ( $_GET['action'] == "loadTraining" )
{
    $ID = mysqli_real_escape_string($link,$_POST['ID']);
    $query = mysqli_query($link,"SELECT * FROM `training` WHERE
        `ID` = '".$ID."'");

    $row = mysqli_fetch_array($query, MYSQLI_ASSOC);
    $row["fromF"] = substr($row['from'],0,5);
    $row["toF"] = substr($row['to'],0,5);
    $row["title"] = str_replace($SHY, "", $row["title"]);

    echo json_encode($row);
}

if ( $_GET['action'] == "editTraining" )
{
    if ( !noodle_check_login())
    {
        echo json_encode(array("error" => "Fehler: Sie sind nicht angemeldet!"));
        exit;
    } 
    $error = array();
    // check if empty?
    if ( empty($_POST['semester']) OR empty($_POST['day']) OR empty($_POST['from']) OR empty($_POST['to']) OR empty($_POST['title']))
    {
        $error[] = 'Bitte alle Felder ausfüllen!';
    }

    $semester = mysqli_real_escape_string($link,$_POST['semester']);
    $day = mysqli_real_escape_string($link,$_POST['day']);
    $ID = mysqli_real_escape_string($link,$_POST['ID']);

    $from = $_POST['from'].":00";
    $to = $_POST['to'].":00";


    if ( count($error) > 0 )
    {
        echo json_encode(array('error' => $error));
    }
    else
    { 
        $from = mysqli_real_escape_string($link,$from);
        $to = mysqli_real_escape_string($link,$to);
        $title = $_POST['title'];
        $title = hyphenation($title);
        $title = mysqli_real_escape_string($link,$title);
        mysqli_query($link,"UPDATE `training` SET 
            `semesterID` = '".$semester."',
            `day` = '".$day."',
            `from` = '".$from."',
            `to` = '".$to."',
            `title`= '".$title."'
            WHERE `ID` = '".$ID."'
        ");
       
        if (mysqli_error($link))
        {
            echo json_encode(array('error' => mysqli_error($link)));
            exit;
        }
 
        $query = mysqli_query($link,"SELECT * FROM `training` WHERE
            `ID` = '".$ID."'");

        $row = mysqli_fetch_array($query, MYSQLI_ASSOC);
        $row["fromF"] = substr($row['from'],0,5);
        $row["toF"] = substr($row['to'],0,5);
        
        $query = mysqli_query($link, "SELECT * FROM `semester`
            WHERE `ID` = '".$semester."'");

        $semester = mysqli_fetch_array($query, MYSQLI_ASSOC);

        $row["semesterF"] = $semester['title'];
        $row["dayF"] = noodle_t_day($row["day"]); 
        $row['dayFshort'] = noodle_t_day($row['day'], "short");
        
        echo json_encode($row);
    }


}


mysqli_close($link);
