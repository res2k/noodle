<?php

function _array_delete($array, $value)
{
    $keys = array_keys($array, $value);
    foreach ( $keys as $key )
    {
        unset($array[$key]);
    }
    return $array;
}

/* Given a database row, returns the display
 * title and the "HSP" flag (Hochschulsport) */
function noodle_check_hsp ($db_row)
{
    $db_title = $db_row['title'];
    // TODO: This should probably be a more generic training "tag" or something
    $hsp_pos = strripos ($db_title, "(HSP)");
    if (! $hsp_pos)
    {
        $title = trim($db_title);
        $is_hsp = false;
    }
    else
    {
        $title = trim(substr($db_title, 0, $hsp_pos) . substr($db_title, $hsp_pos + 5));
        $is_hsp = true;
    }
    return array($title, $is_hsp);
}

function noodle_get_attendance ( $user )
{
    global $link;
    $user = mysqli_real_escape_string($link,$user);

    $result = mysqli_query($link,
        "SELECT * FROM `attendance` WHERE `user` = '".$user."'");
    
    $erg = array();
    while ( $row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        $erg[$row['date'].'_'.$row['tid']] = $row['type'];
    }
    return($erg);
}

function noodle_set_attendance ( $user, $attendance )
{
    global $link;
    $user = mysqli_real_escape_string($link,$user);

    // create user
    $result = mysqli_query($link, "SELECT `name` FROM `user` WHERE
        `name` = '".$user."'");

    if ( mysqli_affected_rows($link) == 0 )
    {
        mysqli_query($link,"INSERT INTO `user` 
            (`name`,`attributes`,`trainings`) VALUES
            ('".$user."','{}','{}')");
    }
    foreach ( $attendance as $string => $attend )
    {
        $date = mysqli_real_escape_string($link, substr($string, 0, 10));
        $tid = mysqli_real_escape_string($link, substr($string, 11));
        $result = mysqli_query($link,"SELECT `ID` FROM `attendance`
            WHERE `user`='".$user."' AND 
            `date`='".$date."' AND `tid`='".$tid."'");

        if ( empty($attend))
        {
             mysqli_query($link,"DELETE FROM `attendance`
                WHERE `user`='".$user."' AND 
                `date`='".$date."' AND `tid`='".$tid."'");
        }
        elseif ( mysqli_affected_rows($link) == 0 )
        {
            mysqli_query($link,"INSERT INTO `attendance` 
                (`user`,`date`,`type`,`tid`) VALUES
                ('".$user."','".$date."','".$attend."','".$tid."')");
        }
        else
        {
            $result = mysqli_query($link,"UPDATE `attendance` SET
                `type`='".$attend."' WHERE `user`='".$user."' AND 
                `date`='".$date."' AND `tid`='".$tid."'");
        }
    }
}

function noodle_get_dates ($time_frame,$now='0000-00-00')
{
    global $link;
    $now = mysqli_real_escape_string($link,$now);

    $erg = array();
    $semester = mysqli_query($link,"SELECT * FROM `semester` 
WHERE `to` >= '".$now."' 
ORDER BY `from`");

    $last_date = new DateTime($now);
    $last_date->modify($time_frame);

    while ( $row = mysqli_fetch_array($semester, MYSQLI_ASSOC))
    {
        $training = mysqli_query($link,"SELECT * FROM `training` WHERE 
            `semesterID` = '".$row['ID']."' ORDER BY `day`,`from`");

        $row['training'] = array();
        while ( $tt = mysqli_fetch_array($training, MYSQLI_ASSOC))
        {                
            $tt['dayF'] = noodle_t_day($tt['day']);
            $tt['fromF'] = substr($tt['from'],0,5);
            $tt['toF'] = substr($tt['to'],0,5);
            $tt['title_full'] = $tt['title'];
            list($title, $is_hsp) = noodle_check_hsp($tt);
            $tt['title'] = $title;
            $tt['is_hsp'] = $is_hsp;
            $row['training'][] = $tt;
        }
        $fromT = strtotime($row['from']);
        $toT = strtotime($row['to']);

        $row['fromF'] = date("d",$fromT).". ".noodle_t_month(date("n",$fromT))." ".date("Y",$fromT);
        $row['toF'] = date("d",$toT).". ".noodle_t_month(date("n",$toT))." ".date("Y",$toT);


        $erg[] = $row;

        if (new DateTime($row['to']) >= $last_date) break;
    }
    return $erg;
}

function noodle_get_trainings ()
{
    global $link;
    
    $query = mysqli_query($link,"SELECT training.*, training.from AS tFrom, 
        training.to as tTo, semester.title FROM `training`
         LEFT JOIN  `semester` ON (training.semesterID = semester.ID) ORDER BY semester.from, day");

    $erg = array();

    while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC))
    {
        $row['dayF'] = noodle_t_day($row['day']);
        $row['fromF'] = substr($row['tFrom'],0,5);
        $row['toF'] = substr($row['tTo'],0,5);

        $erg[] = $row;
    }
 
    return $erg;
}

function noodle_get_attributes($user)
{
    global $link;
    $user = mysqli_real_escape_string($link,$user);
    
    $result = mysqli_query($link, "SELECT * FROM `user`
        WHERE `name` = '".$user."'");

    if ( mysqli_num_rows($result) == 0)
        return false;
    else
    {
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        $attr = json_decode($row['attributes'],true);
        ksort($attr);
        return $attr;
    }
}

function noodle_get_attend($user)
{
    global $link;
    $user = mysqli_real_escape_string($link,$user);
    
    $result = mysqli_query($link, "SELECT * FROM `user`
	WHERE `name` = '".$user."'");

    if ( mysqli_num_rows($result) == 0)
        return false;
    else
    {
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        $attend = json_decode($row['trainings'],true);
        return($attend);
    }	
}

function noodle_get_user_trainings( $user )
{
    global $link;
    $user = mysqli_real_escape_string($link,$user);

    $result = mysqli_query($link,
        "SELECT * FROM `user` WHERE `name` = '".$user."'");
    
    if ( mysqli_num_rows($result) == 0)
        return array();

    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $t = json_decode($row['trainings']);

    if ( !is_array($t))
        return $array;

    $result2 = mysqli_query($link,
        "SELECT * FROM `training`
                LEFT JOIN `semester` ON training.semesterID = semester.ID
                WHERE `training.ID` IN ('".implode("','",$t)."')");

    $erg = array();
//    while ( $
}

function noodle_set_attributes($user, $attr)
{
    global $link;
    $user = mysqli_real_escape_string($link,$user);
    
    $string = mysqli_real_escape_string($link, json_encode($attr));

    $result = mysqli_query($link, "SELECT `name` FROM `user` WHERE
        `name` = '".$user."'");

    if ( mysqli_affected_rows($link) == 0 )
    {
        mysqli_query($link,"INSERT INTO `user` 
            (`name`,`attributes`,`trainings`) VALUES
            ('".$user."','".$string."','{}')");
    }
    else
    {
        mysqli_query($link, "UPDATE `user` SET `attributes` = '".$string."'
            WHERE `name` = '".$user."'");
    }
}

function noodle_set_attend($user, $attend)
{
    global $link;
    $user = mysqli_real_escape_string($link,$user);

    $string = mysqli_real_escape_string($link, json_encode($attend));

    $result = mysqli_query($link, "SELECT `name` FROM `user` WHERE
        `name` = '".$user."'");

    if ( mysqli_affected_rows($link) == 0)
	mysqli_query($link, "INSERT INTO `user`
            (`name`, `trainings`, `attributes`) VALUES
            ('".$user."','".$string."','{}')");
    else
    {
         mysqli_query($link, "UPDATE `user` SET `trainings` = '".$string."'
             WHERE `name` = '".$user."'");
    }
}

/* Create a calendar (list of practice dates) starting on $start,
   ending on $end, with practices on $dow days of a week.
   Note: $dow must not be empty! */
function noodle_create_calendar($start,$end,$dow,$tid)
{
    $start = new DateTime($start);
    $end = new DateTime($end); 

    if ( $start->format("N") > max($dow))
    {
        $start->modify("+".(8-$start->format("N"))." days");
    }
    
    $erg = array('days' => array(),'weeks'=>array());
    $k = 0;

    $t = $start;
    do {
        foreach ( $dow as $j => $training )
        {
            if ( $k == 0 && $training < $t->format("N"))
            {
                $erg['weeks'][$k]['days'][] = array(
                    'date' => '0000-00-00',
                    'dateF' => '',
                    'activ' => 0
                );
                continue;
            }

            // fill up empty days
            $add = (7+$training - $t->format("N")) % 7;
            $t->modify("+".$add." days");
            
            if ( $t > $end )
            {
                $erg['weeks'][$k]['days'][] = array(
                    'date' => '0000-00-00',
                    'dateF' => '',
                    'activ' => 0
                );
            }
            else {
                $erg['days'][] = $t->format("Y-m-d");
                $erg['did'][] = $t->format("Y-m-d")."_".$tid[$j];
                $erg['weeks'][$k]['woy'] = $t->format("W");
                $erg['weeks'][$k]['days'][] = array (
                    'date' => $t->format("Y-m-d"),
                    'did' => $t->format("Y-m-d")."_".$tid[$j],
                    'tid' => $tid[$j],
                    'dateF' => $t->format("d").'. '.noodle_t_month($t->format("n"),"short"),
                    'activ' => 1
                );
            }
        }
        // next day
        $t->modify("+1 day");

        $k ++;
    } while ($t <= $end);
    
    return $erg;
}

function noodle_get_next_trainings($limit, $use_date)
{
    $dates = noodle_get_dates("+".$limit." weeks", $use_date);
    $upcoming = array();
    foreach ( $dates as $semester )
    {
        $dow = array();
        $tid = array();
        foreach ( $semester['training'] as $training )
        {
            $dow[] = $training['day'];
            $tid[] = $training['ID'];
        }
        $start = max($semester['from'], $use_date);
        $end = $semester['to'];
       
        if (empty($dow) OR empty($tid)) continue;
        $calendar = noodle_create_calendar($start,$end,$dow,$tid);
        if ($calendar['days'])
            $upcoming = array_merge($upcoming,$calendar['did']);
    }
    return(array_slice($upcoming,0,$limit));
}

function noodle_get_participants($day, $tid)
{
    global $link;
    $day = mysqli_real_escape_string($link,$day);
    $tid = mysqli_real_escape_string($link,$tid);

    $result = mysqli_query($link,"SELECT * FROM `attendance`
        LEFT JOIN  `user` ON (attendance.user = user.name)
        WHERE `date` = '".$day."' AND `tid`='".$tid."'
        ORDER BY type DESC, LOWER(user.name)
    ");

    $erg = array();
    $nYes = 0;
    $nMaybe = 0;
    $nNo = 0;
    while ( $row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        if($row['type']=='yes')
            $nYes ++;
        elseif($row['type'] == 'maybe')
            $nMaybe ++;
        elseif($row['type'] == 'no')
            $nNo ++;

        $attrArray = json_decode($row['attributes'],true);
        $attr = array_keys($attrArray,'yes');
        asort($attr);
        $attr = array_values($attr);
        $erg[] = array(
            'user' => htmlentities($row['user']),
            'type' => $row['type'],
            'attr' => $attr,
        );
    }
    $erg['yes'] = $nYes;
    $erg['maybe'] = $nMaybe;
    $erg['no'] = $nNo;

    return($erg);
}

function noodle_delete_user ($user)
{
    global $link;
    $user = mysqli_real_escape_string($link,$user);
    mysqli_query($link,"DELETE FROM `user` WHERE `name` = '".$user."'");
    mysqli_query($link,"DELETE FROM `attendance` WHERE `user` = '".$user."'");

}

function noodle_get_statistic ($days)
{
    global $link;
    $days = mysqli_real_escape_string($link,$days);
    $where = "(`date` = '".implode("' OR `date` = '",$days)."')
        AND `type` = 'yes'";
    $qry = "SELECT `user`, COUNT(*) as `count` FROM `attendance` ".
        " WHERE ".$where.
        "GROUP BY `user` ORDER BY `count` DESC";
    $result = mysqli_query($link,$qry);
    $arr = array();
    while ( $row = mysqli_fetch_array($result,MYSQLI_ASSOC))
    {
        $arr[$row['user']] = $row['count'];
    }
    return $arr;
}

function noodle_change_name ($oldName, $newName)
{
    global $link;
    $oldName = mysqli_real_escape_string($link,$oldName);
    $newName = mysqli_real_escape_string($link,$newName);
    
    $result = mysqli_query($link,"SELECT * FROM `user`
        WHERE `name` = '".$newName."'");

    if ( mysqli_num_rows($result) == 1 )
        return false;

    mysqli_query($link,"UPDATE `user`
        SET `name` = '".$newName."'
        WHERE `name` = '".$oldName."'");

    mysqli_query($link,"UPDATE `attendance`
        SET `user` = '".$newName."'
        WHERE `user` = '".$oldName."'");

    return true;
}

function noodle_names_like ($name)
{
    global $link;
    $name_query_limit = 20;
    $name = mysqli_real_escape_string($link,$name);
    $result = mysqli_query($link,"SELECT * FROM `user`
        WHERE `name` LIKE '".$name."%' LIMIT ".$name_query_limit);

    $erg = array('query' => 'Unit','suggestions' => array());
    while ( $row = mysqli_fetch_array($result,MYSQLI_ASSOC))
    {
        $erg['suggestions'][] = $row['name'];
    }
    if (!empty($erg['suggestions']))
    {
        return $erg;
    }

    // If there are no names that begin with the query, look inside the names as well
    $name = mysqli_real_escape_string($link,$name);
    $result = mysqli_query($link,"SELECT * FROM `user`
        WHERE `name` LIKE '%".$name."%' LIMIT ".$name_query_limit);

    $erg = array('query' => 'Unit','suggestions' => array());
    while ( $row = mysqli_fetch_array($result,MYSQLI_ASSOC))
    {
        $erg['suggestions'][] = $row['name'];
    }
    return $erg;
}

function noodle_t_day($dow, $format = 'long')
{
    global $tage, $tage_kurz;
    if ( $format == 'long' )
    {
        return($tage[$dow]);
    }
    else
    {
        return($tage_kurz[$dow]);
    }
}
function noodle_t_month($m, $format = 'long')
{
    global $monate, $monate_kurz;
    $m = ltrim($m,'0');
    if ( $format == 'long' )
    {
        return($monate[$m]);
    }
    else
    {
        return($monate_kurz[$m]);
    }
}

function noodle_date_ger_eng ($date)
{
    $arr = explode(".",$date);
    if ( count($arr) != 3 )
        return false;

    return $arr[2]."-".$arr[1]."-".$arr[0];
}


function noodle_date_eng_gerF ($date)
{
    $arr = explode("-",$date);
    if ( count($arr) != 3 )
        return false;
   
    return $arr[2].". ".noodle_t_month($arr[1])." ".$arr[0];
}

function noodle_date_eng_ger ($date)
{
    $arr = explode("-",$date);
    if ( count($arr) != 3 )
        return false;
   
    return $arr[2].".".$arr[1].".".$arr[0];
}

function noodle_delete_training($id)
{
    global $link;
    $id = mysqli_real_escape_string($link,$id);

    $query = mysqli_query($link,"SELECT training.day, semester.from, semester.to
        FROM training
        LEFT JOIN semester
        ON ( semester.ID = training.semesterID)
        WHERE training.ID = '".$id."'
    ");

    $row = mysqli_fetch_array($query, MYSQLI_ASSOC);
    $range = noodle_get_day_range($row['from'],$row['to'],$row['day']);

    $where = "`date` = '".implode("' OR `date` = '",$range)."'";
    mysqli_query($link,"DELETE FROM `attendance` WHERE ".$where);
    mysqli_query($link,"DELETE FROM `training` WHERE `ID` = '".$id."'");
        
}

function noodle_next_day($time,$dow)
{
    return $time + ((date("N",$time)-$dow+7)%7)*24*60*60;
}

function noodle_get_day_range($begin,$end,$day)
{
    $from = strtotime($begin) + 12*60*60;
    $to = strtotime($end);

    $start = noodle_next_day($from,$day);

    $days = array();
    for ( $t = $start; $t < $to; $t = $t + 24*60*60)
    {
        $days[] = date("Y-m-d",$t);
    }
    return $days;
}
