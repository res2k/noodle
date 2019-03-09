{foreach $calendar as $semester}
<div class="mdl-card mdl-shadow--2dp main-card semester">
<a name="semester-{$semester.ID}"></a>
<div class="mdl-card__supporting-text">
  <h2 class="sectionHeadline mdl-card__title-text">{$semester.title|escape}</h2>
<table class="dateSelect{for $n=5 to 7}{if $semester.training|@count >= $n} days-count-{$n}{/if}{/for}">
    <thead>
        <tr class="first">
            <th class="woyHeader">&nbsp;</th>
            {foreach $semester.training as $training}
            <th id="tid-{$training.ID}" style="width: {math equation="96/n" n=$semester.training|@count}%;">
                <div class="training-info" id="training-info-{$training.ID}">
                  <div class="training-day large">{$training.dayF}</div><div class="training-day small">{$training.dayFshort}{if $training.is_hsp}<span class="hsp-tag">❗</span>{/if}</div>
                  <div class="training-name"><i>{$training.title|escape}</i>{if $training.is_hsp} <span class="hsp-tag">❗</span>{/if}</br></div>
                  <span class="time">{$training.fromF} - {$training.toF}</span>
                </div>
                <div class="mdl-tooltip training-info-tooltip" for="training-info-{$training.ID}">
                {$training.title|escape}{if $training.is_hsp} <span class="hsp-tag">❗</span>{/if}
                </div>
                <button class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect attend large" tid="{$training.ID}">
                    Teilnehmen
                </button>
                <button class="mdl-button mdl-js-button mdl-button--fab mdl-button--mini-fab attend small" tid="{$training.ID}" id="attend-small-{$training.ID}">
                    <i class="material-icons">done_all</i>
                </button>
                <div class="mdl-tooltip" for="attend-small-{$training.ID}">
                Teilnehmen
                </div>
            </th>
            {/foreach}
        </tr>
    </thead>
    <tbody>
    {foreach $semester.weeks as $week}
        <tr>
            <td class="woy">{$week.woy}</td>
            {foreach $week.days as $day}
{*
                {if $day.activ eq true}
                    {assign 'class' $day.attend}
                {else}
                    {assign 'class' $day.attend|cat:' noclick'}
                {/if}
*}
                {if $day.activ eq true}
                    {assign 'class' ''}
                {else}
                    {assign 'class' 'noclick'}
                {/if}
            <td id="{$day.did}" class="{$class}">{$day.dateF}</td>
            {/foreach}
        </tr>
    {/foreach}
    </tbody>
</table>
</div>
</div>
{/foreach}

