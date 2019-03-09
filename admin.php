<?php

// login form??
$login = false;
if ( isset($_GET['action']) AND $_GET['action'] == 'login' )
{
    $password_value = password_hash($hash_pass, PASSWORD_DEFAULT);
    if (password_verify ($_POST['password'], $hash_pass))
    {
        $login = true;
        // Use hash itself as 'magic' value stored in cookie (don't store the password)
        setcookie('password', $password_value, time()+3600);
    }
    else
    {
        $login = false;
    }
}
// If cookie is set, compare agains 'magic' value
if ( isset($_COOKIE['password']) AND password_verify ($hash_pass, $_COOKIE['password']) )
{
    $login = true;
    setcookie('password', $_COOKIE['password'], time()+3600);

}


if ( isset($_GET['action']) AND $_GET['action'] == 'logoff' )
{
    setcookie("password", "", time() - 3600);
    $login = false;
}

$smarty->assign('login',$login);

if ( $login == true )
{
    $semesters = array_reverse(noodle_get_dates("9999-12-31"));

    $trainings = noodle_get_trainings();
   
    $smarty->assign('semesters',$semesters);
    $smarty->assign('trainings',$trainings);

    $smarty->assign('days', $tage);
}

