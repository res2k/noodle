<?php

// TODO get and set user
// $user = 'Andreas';
$user = array('name' => '');
$user['attendance'] = noodle_get_attendance($user['name']);

$user['attributes'] = array_flip($attributes);

// $user_attributes = noodle_get_attributes($user);
$user_attributes = array();

foreach ($user['attributes'] as $key => $value )
{
    if ( isset($user_attributes[$key]) && $user_attributes[$key] == 'yes' )
        $user['attributes'][$key] = 'yes';
    else
        $user['attributes'][$key] = 'no';

}

$user['trainings'] = noodle_get_attend($user['name']);

$trainings = noodle_get_user_trainings ($user['name']);

$smarty->assign('user',$user);

$use_date = "";
if (isset($_GET["pretend"])) $use_date = $_GET["pretend"];
if ($use_date)
{
  if (!preg_match ("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $use_date))
  {
    if (preg_match ("/^([0-9]{4})([0-9]{2})([0-9]{2})$/", $use_date, $matches))
    {
      $use_date = $matches[1] . '-' . $matches[2] . '-' . $matches[3];
    }
    else
    {
      // Uber-ugly!
      print "invalid format for 'pretend' arg!";
      $use_date = '';
    }
  }
}
if (!$use_date)
{
  $use_date = date("Y-m-d");
}
// create calendar
$calendar = noodle_get_dates($periodes_show_timeframe, $use_date);

$new_calendar = array();
$new_key = 0;
foreach ( $calendar as $key => $semester )
{
    $start = max($semester['from'], $use_date);
    $end = $semester['to'];

    $cal_sem = $calendar[$key];
    $dow = array();
    $tid = array();
    foreach ( $semester['training'] as $jj => $training ){
        $cal_sem['training'][$jj]['dayF'] = noodle_t_day($training['day']);
        $cal_sem['training'][$jj]['dayFshort'] = noodle_t_day($training['day'], "short");
        $cal_sem['training'][$jj]['fromF'] = substr($training['from'],0,5);
        $cal_sem['training'][$jj]['toF'] = substr($training['to'],0,5);
      
        $dow[] = $training['day'];
        $tid[] = $training['ID'];
    }

    $sem_calendar = noodle_create_calendar($start,$end,$dow,$tid);
    if (!$sem_calendar['days']) continue;

    $new_calendar[$new_key] = $cal_sem;
    $new_calendar[$new_key] = array_merge($new_calendar[$new_key], $sem_calendar);
    $new_key++;
}
$calendar = $new_calendar;

$smarty->assign('calendar', $calendar);


if (count ($calendar) > 0)
{
    $next_sem = $calendar[0];
    $num_trainings = count($next_sem['training']);
    if ($trainings_show < $num_trainings)
        $trainings_show = $num_trainings;
}

$next_trainings = noodle_get_next_trainings($trainings_show, $use_date);
$upcoming = array();

foreach ( $next_trainings as $training )
{
    $date = substr($training,0,10);

    $t = strtotime($date);
    $d = ltrim(substr($training,8,2),'0');
    $m = ltrim(substr($training,5,2),'0');
    $dow = date("N",$t);
    $Y = substr($training,0,4);

    $tid = substr($training,11);

    $participants = noodle_get_participants($date, $tid);

    $query = mysqli_query($link, "SELECT * FROM `training` WHERE
        `ID` = '".$tid."'");

    $row = mysqli_fetch_array($query,MYSQLI_ASSOC);
    list($title, $is_hsp) = noodle_check_hsp($row);

    $pp = array(
      'yes' => array(),
      'maybe' => array(),
      'no' => array()
    );
    foreach ($participants as $key => $par)
    {
        if(is_numeric($key)){
            if ( count($par['attr']) > 0 )
                $par['attrF'] = '('.implode(', ',$par['attr']).')';
            else
                $par['attrF'] = '';

            $pp[$par['type']][] = $par;
        }
    }


    $upcoming[] = array_merge($pp,array (
        'did' => $training,
        'dateF' => $tage[$dow].' '.$d.'. '.$monate[$m],
        'dateFonly' => $d.'. '.$monate[$m],
        'numYes' => $participants['yes'],
        'numMaybe' => $participants['maybe'],
        'numNo' => $participants['no'], 
        'title' => $title,
        'is_hsp' => $is_hsp,
    ));
    // print_r($upcoming);
}

$smarty->assign('upcoming', $upcoming);

$smarty->assign('feedback_mail', $feedback_mail);
