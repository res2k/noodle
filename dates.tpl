{foreach $upcoming as $training}
<div class="mdl-card mdl-shadow--2dp training" id="training_{$training.did}">
    <a name="training-{$training.did}"></a>
    <div class="date mdl-card__supporting-text">
        {$training.dateF}
    </div>
    <div class="title mdl-card__title">
        <h2 class="mdl-card__title-text">{$training.title|escape}</h2>
    </div>
    {if $training.is_hsp}
    <div class="hsp mdl-card__supporting-text"><span class="hsp-tag">❗</span> Hochschulsport</div>
    {/if}
    <div class="participants  mdl-card__supporting-text">
        <div class="p participants_yes">
            <div class="number_yes {if $training@first}expanded-yes{else}expanded-no{/if}">
                <span class="text">
                    {if $training.numMaybe eq 0}
                        {$training.numYes}
                    {else}
                        {$training.numYes} bis {$training.numYes + $training.numMaybe}
                    {/if}
                    Teilnehmer
                </span>
            </div>
            <div class="mdl-progress mdl-js-progress" id="spinner-yes-{$training.did}"></div>
            <div class="participants-outer">
                <div class="participants-inner {if $training@first}expanded{/if}">
                    <div class="participants-list">
                    {if $training@first and $training.numYes + $training.numMaybe gt 0}
                    {foreach $training.yes as $participant}
                        <div class="participant yes">
                            {$participant.user} {$participant.attrF}
                        </div>
                    {/foreach}
                    {foreach $training.maybe as $participant}
                        <div class="participant maybe">
                            {$participant.user} {$participant.attrF}
                        </div>
                    {/foreach}
                    {/if}
                    </div>
                </div>
            </div>
        </div>
        <div class="p participants_no">    
            <div class="number_no {if $training@first}expanded-yes{else}expanded-no{/if}">
                <span class="text"> {$training.numNo} kommen nicht</span>
            </div>
            <div class="mdl-progress mdl-js-progress" id="spinner-no-{$training.did}"></div>
            <div class="participants-outer">
                <div class="participants-inner {if $training@first}expanded{/if}">
                    <div class="participants-list">
                    {if $training@first and $training.numNo gt 0}                  
                    {foreach $training.no as $participant}
                        <div class="participant no">
                            {$participant.user} {$participant.attrF}
                        </div>
                    {/foreach}
                    {/if}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{/foreach}

{*
<h1 class="sectionHeadline">Statistik</h1>

<div>
    <div>Dienstagstraining</div>
    <div>Andreas 90%</div>
    <div></div>
    <div>
        <span class="red"></span>
        <span class="red"></span>
        <span class="green"></span>
        <span class="red"></span>
        <span class="red"></span>
        <span class="green"></span>
    </div>
</div>
*}
