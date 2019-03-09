<!DOCTYPE html>
<html>
<head>
<title>Noodleexport</title>
<meta charset="utf-8">
</head>
<body>
<table border="1">
    <tr>
        <td>&nbsp;</td>
        {foreach $datesF as $date}
            <td>{$date}</td>
        {/foreach}
    </tr>
{foreach $users as $user}
    {assign 'row' $table.$user}
    <tr>
        <td>{$user}</td>
        {foreach $dates as $date}
            <td align="center">
                {if array_key_exists($date, $row)}
                    {$row.$date}
                {/if}
            </td>
        {/foreach}
    </tr>
{/foreach} 
</table>
</body>
</html>

