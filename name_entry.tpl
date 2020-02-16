<div class="mdl-card__supporting-text">
<div class="data" id="nameContainer">
    <div class="mdl-textfield mdl-js-textfield">
        <input class="mdl-textfield__input" type="text" id="inputName" />
        <label class="mdl-textfield__label" for="inputName">Namen eingeben</label>
        <span class="mdl-textfield__error">Bitte Namen eingeben</span>
    </div>
    <button class="mdl-color-text--grey-600 mdl-button mdl-js-button mdl-button--icon optional" id="changeName">
      <i class="material-icons">edit</i>
    </button>
    <div class="mdl-tooltip" for="changeName">
    Name &auml;ndern
    </div>
    <button class="mdl-color-text--grey-600 mdl-button mdl-js-button mdl-button--icon optional" id="deleteUser">
      <i class="material-icons">delete</i>
    </button>
    <div class="mdl-tooltip" for="deleteUser">
    Löschen
    </div>
</div>
<div class="userAttributes data" id="catContainer">
    <div class="catLabel">DFV-Turniere:</div>
    {foreach $user.attributes as $attribute => $value}
        <div class="catCheck">
            <label id="attr_{$attribute}" class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="attr_{$attribute}_check">
              <input type="checkbox" id="attr_{$attribute}_check" class="mdl-checkbox__input" {if $value eq 'yes'}checked{/if} attr_id="{$attribute}" />
              <span class="mdl-checkbox__label">{$attribute}</span>
            </label>
        </div>
    {/foreach}
</div>
</div>
