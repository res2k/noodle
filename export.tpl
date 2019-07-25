<!DOCTYPE html>
<html>
<head>
<title>Noodle-Export</title>
{include file="head-common.tpl"}
{include file="export-css.tpl"}
</head>
<body>
<table border="1">
    <tr>
        <th>&nbsp;</th>
        {foreach $datesF as $date}
            <th>{$date}</th>
        {/foreach}
    </tr>
{foreach $users as $user}
    {assign 'row' $table.$user}
    <tr>
        <td>{$user}</td>
        {foreach $dates as $date}
            {if array_key_exists($date, $row)}
            <td align="center" class="attendance-{$row.$date.type}">{$row.$date.symbol}</td>
            {else}
            <td></td>
            {/if}
        {/foreach}
    </tr>
{/foreach} 
</table>
{include file='foot-common.tpl'}
</body>
</html>

