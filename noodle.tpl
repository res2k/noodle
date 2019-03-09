<!DOCTYPE html>
<html lang="de">
<head>
{if $query eq 'admin'}
<title>Noodle Admin</title>
{else}
<title>Noodle</title>
{/if}
{include file="head-common.tpl"}
{include file="noodle-css.tpl"}
</head>
<body>

{if $query eq 'default'}
{include file='dialogs-default.tpl'}
{/if}

{capture main_content}
<div id="noodle-toast" class="mdl-js-snackbar mdl-snackbar">
  <div class="mdl-snackbar__text"></div>
  <button class="mdl-snackbar__action" type="button"></button>
</div>

<div class="noodle-grid mdl-grid">
<div class="mdl-cell mdl-cell--12-col">
  <div class="mdl-card mdl-shadow--2dp main-card splash">
  </div>
</div>
{include "noscript.tpl"}
{if $query eq 'default'}
<div class="mdl-cell mdl-cell--12-col">
  <div class="mdl-card mdl-shadow--2dp main-card hsp-info">
    <em>Achtung! USV-Mitglieder:</em> <em>Kein Versicherungsschutz</em> für Vereinsmitglieder der Abteilung Ultimate Frisbee des USV Jena e.V. bei einer Teilnahme an den Kursen des <em>Hochschulsports</em> (<span class="hsp-tag">❗</span>) ohne vorherige Online-Anmeldung!
  </div>
</div>

<div id="dates" class="mdl-cell mdl-cell--4-col mdl-cell--8-col-tablet">
    {include file='dates.tpl'}
</div>

<div class="mdl-cell mdl-cell--8-col">
<div class="mdl-card mdl-shadow--2dp main-card name_entry">
<a name="name_entry"></a>
{include file='name_entry.tpl'}
</div>

{include file='calendar.tpl'}
</div>
{/if}

{if $query eq 'admin'}
<div id="content" class="mdl-card mdl-shadow--2dp mdl-cell mdl-cell--12-col">
    <div class="mdl-card__supporting-text admin" id="admin">
        {include file='admin.tpl'}
    </div>
</div>
{/if}
{/capture}

{capture drawer_links}
{if $query eq 'admin'}
<a class="mdl-navigation__link" href="?">Start</a>
{if $login eq true}
<a class="mdl-navigation__link" href="?q=admin&amp;action=logoff">Abmelden</a>
{/if}
{else}
<a class="mdl-navigation__link" href="#name_entry">Eigene Daten</a>
<hr>
{foreach $upcoming as $training}
<a class="mdl-navigation__link" href="#training-{$training.did}">{$training.title|escape} {$training.dateFonly}</a>
{/foreach}
<hr>
{foreach $calendar as $semester}
<a class="mdl-navigation__link" href="#semester-{$semester.ID}">{$semester.title|escape}</a>
{/foreach}
{/if}
{/capture}

{capture top_links}
{if $query eq 'default'}
{if $calendar|@count}
<a class="mdl-navigation__link" href="#name_entry"><i class="material-icons">event</i><span class="optional-navigation-text">{$calendar[0].title|escape}</span></a>
{/if}
<a class="mdl-navigation__link" href="help.php"><i class="material-icons">help</i><span class="optional-navigation-text">Hilfe</span></a>
{/if}
{if $query eq 'admin' and $login eq true}
<a class="mdl-navigation__link" href="?q=admin&amp;action=logoff"><i class="material-icons">lock</i><span class="optional-navigation-text">Abmelden</span></a>
{/if}
{/capture}


{$back_button=0}
{include file='layout.tpl'}

{include file='foot-common.tpl'}
{include file='noodle-js.tpl'}
</body>
</html>
