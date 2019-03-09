<!DOCTYPE html>
<html lang="de">
<head>
<title>Noodle</title>
{include file="head-common.tpl"}
{include file="help-css.tpl"}
</head>
<body>
{capture main_content}
    <div class="noodle-grid mdl-grid">
        {include "noscript.tpl"}
        <div class="mdl-cell mdl-cell--12-col">
            <div class="mdl-card mdl-shadow--2dp main-card help">
                <div class="mdl-card__supporting-text">
                {include file='helptext.tpl'}
                </div>
                <div class="mdl-card__actions mdl-card--border">
                    <a class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect" href="index.php">
                      Losnudeln
                      <i class="material-icons">arrow_forward</i>
                    </a>
                </div>
            </div>
        </div>
    </div>
{/capture}

{capture drawer_links}
<a class="mdl-navigation__link" href=".">Start</a>
{/capture}

{$back_button=on}
{include file='layout.tpl'}

{include file='foot-common.tpl'}
{include file='help-js.tpl'}
<script type="text/javascript">
$(document).ready(function(){
    $("#my_back_button").click(function() {
        $(".mdl-layout__obfuscator").hide();
        location.href = ".";
    });
});
</script>
</body>
</html>
