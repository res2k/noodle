{if $login eq false}
<div id="loginForm">
<form name="loginForm" action="?q=admin&amp;action=login" method="POST">
    <div class="mdl-textfield mdl-js-textfield">
        <input class="mdl-textfield__input" type="password" id="inputPassword" name="password"/>
        <label class="mdl-textfield__label" for="inputPassword">Zauberwort</label>
    </div>
    <br/>
    <button type="button" id="buttonSubmit" class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored mdl-js-ripple-effect"
            onClick="document.loginForm.submit();">
      Login
    </button>
    <button type="button" id="buttonSubmit" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect"
            onClick="location.href = '?';">
      Zur&uuml;ck
    </button>
</form><br>
<div class="info">Das Passwort gibts bei Andreas.<br>
Man wird nach einer Stunde automatisch abgemeldet.</div>
</div>
{else}
<h3>Semestertermine</h3>
<div class="info">Bitte achtet selbstständig darauf, dass es keine
Überschneidungen bei den Semesterzeiten gibt. Sonst gibt es Probleme mit
den Anwesenheiten.</div>
<br>
<div id="adminSemester">
    <div class="mdl-grid table-head">
        <div class="mdl-cell mdl-cell--5-col">Titel</div>
        <div class="mdl-cell mdl-cell--2-col">Beginn</div>
        <div class="mdl-cell mdl-cell--2-col">Ende</div>
    </div>
    {capture inputSemesterTitle}
        <div class="mdl-textfield mdl-js-textfield">
            <input class="mdl-textfield__input" type="text" id="inputSemesterTitle"/>
            <label class="mdl-textfield__label" for="inputSemesterTitle">Titel</label>
        </div>
    {/capture}
    {capture inputSemesterFrom}
        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
            <input class="mdl-textfield__input" type="text" id="inputSemesterFrom" pattern="[0-9][0-9]?\.[0-9][0-9]?\.[0-9][0-9][0-9][0-9]" />
            <label class="mdl-textfield__label" for="inputSemesterFrom">TT.MM.JJJJ</label>
            <span class="mdl-textfield__error">Ung&uuml;ltiges Format!</span>
        </div>
    {/capture}
    {capture inputSemesterTo}
        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
            <input class="mdl-textfield__input" type="text" id="inputSemesterTo" pattern="[0-9][0-9]?\.[0-9][0-9]?\.[0-9][0-9][0-9][0-9]" />
            <label class="mdl-textfield__label" for="inputSemesterTo">TT.MM.JJJJ</label>
            <span class="mdl-textfield__error">Ung&uuml;ltiges Format!</span>
        </div>
    {/capture}
    <div class="mdl-grid semester_new">
        <div class="mdl-cell mdl-cell--5-col">{$smarty.capture.inputSemesterTitle}</div>
        <div class="mdl-cell mdl-cell--2-col">{$smarty.capture.inputSemesterFrom}</div>
        <div class="mdl-cell mdl-cell--2-col">{$smarty.capture.inputSemesterTo}</div>
        <div class="mdl-cell mdl-cell--3-col buttons">
            <button id="submitSemester" class="mdl-button mdl-js-button mdl-button--icon mdl-button--colored">
              <i class="material-icons">add_circle</i>
            </button>
            <div class="mdl-tooltip" for="submitSemester">
            Hinzuf&uuml;gen
            </div>
        </div>
    </div>
    {foreach $semesters as $semester}
    <div id="semester_{$semester.ID}" class="mdl-grid semester_edit normalMode"> 
        <div class="mdl-cell mdl-cell--5-col mdl-cell--middle semesterTitle">
            <span class="normalMode">{$semester.title|escape}</span>
            {$smarty.capture.inputSemesterTitle}
        </div>
        <div class="mdl-cell mdl-cell--2-col mdl-cell--middle semesterFrom">
            <span class="normalMode">{$semester.fromF}</span>
            {$smarty.capture.inputSemesterFrom}
        </div>
        <div class="mdl-cell mdl-cell--2-col mdl-cell--middle semesterTo">
            <span class="normalMode">{$semester.toF}</span>
            {$smarty.capture.inputSemesterTo}
        </div>
        <div class="mdl-cell mdl-cell--3-col mdl-cell--middle buttons">
            <div class="normalButtons">
                <button class="mdl-color-text--grey-600 mdl-button mdl-js-button mdl-button--icon changeSemester" id="changeSemester_{$semester.ID}">
                  <i class="material-icons">edit</i>
                </button>
                <div class="mdl-tooltip" for="changeSemester_{$semester.ID}">
                &Auml;ndern
                </div>
                <button class="mdl-color-text--grey-600 mdl-button mdl-js-button mdl-button--icon deleteSemester" id="deleteSemester_{$semester.ID}">
                  <i class="material-icons">delete</i>
                </button>
                <div class="mdl-tooltip" for="deleteSemester_{$semester.ID}">
                L&ouml;schen
                </div>
            </div>
            <div class="mdl-spinner mdl-spinner--single-color mdl-js-spinner is-active"></div>
            <div class="editButtons">
                <button type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored mdl-js-ripple-effect large submit">
                  &Auml;ndern
                </button>
                <button class="mdl-button mdl-js-button mdl-button--fab mdl-button--mini-fab mdl-button--primary mdl-js-ripple-effect small submit" id="submit-small">
                    <i class="material-icons">done</i>
                </button>
                <div class="mdl-tooltip" for="submit-small">
                  &Auml;ndern
                </div>
                <button type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect large reset">
                  Abbrechen
                </button>
                <button class="mdl-button mdl-js-button mdl-button--fab mdl-button--mini-fab mdl-js-ripple-effect small reset" id="reset-small">
                    <i class="material-icons">close</i>
                </button>
                <div class="mdl-tooltip" for="reset-small">
                  Abbrechen
                </div>
            </div>
        </div>
    </div>
    {foreachelse}
    <div class="mdl-grid">
        <div class="mdl-cell mdl-cell--12-col list-empty">Keine Semestertermine vorhanden</td>
    </div>
    {/foreach} 
</div>

<h3>Trainingstermine</h3>
<div class="info">Um ein Training als <em>Hochschulsport</em> zu markieren bitte <em>&bdquo;(HSP)&ldquo;</em> in den Namen setzen.</div>
{foreach $semesters as $semester}
<div class="semester_trainings" id="semester_trainings_{$semester.ID}">
<h5>{$semester.title|escape}</h5>
<div id="adminTraining">
    <div class="mdl-grid table-head">
        <div class="mdl-cell mdl-cell--5-col">Titel</div>
        <div class="mdl-cell mdl-cell--2-col">Tag</div>
        <div class="mdl-cell mdl-cell--1-col">Beginn</div>
        <div class="mdl-cell mdl-cell--1-col">Ende</div>
    </div>
    {capture inputTrainingTitle}
        <div class="mdl-textfield mdl-js-textfield">
            <input class="mdl-textfield__input" type="text" id="inputTrainingTitle"/>
            <label class="mdl-textfield__label" for="inputTrainingTitle">Titel</label>
        </div>
    {/capture}
    {capture inputTrainingDay}
        <select id="inputTrainingDay">
            <option value="">-- Tag --</option>
            {foreach $days as $index => $day}
            <option value="{$index}">{$day}</option>
            {/foreach}
        </select>
    {/capture}
    {capture inputTrainingFrom}
        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
            <input class="mdl-textfield__input" type="text" id="inputTrainingFrom" pattern="[0-9][0-9]?\:[0-9][0-9]?" />
            <label class="mdl-textfield__label" for="inputTrainingFrom">HH:MM</label>
            <span class="mdl-textfield__error">Ung&uuml;ltiges Format!</span>
        </div>
    {/capture}
    {capture inputTrainingTo}
        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
            <input class="mdl-textfield__input" type="text" id="inputTrainingTo" pattern="[0-9][0-9]?\:[0-9][0-9]?" />
            <label class="mdl-textfield__label" for="inputTrainingTo">HH:MM</label>
            <span class="mdl-textfield__error">Ung&uuml;ltiges Format!</span>
        </div>
    {/capture}
    {foreach $semester.training as $training}
    <div id="training_{$training.ID}" class="mdl-grid training_edit normalMode"> 
        <div class="mdl-cell mdl-cell--5-col mdl-cell--middle trainingTitle">
            <span class="normalMode">{$training.title_full|escape}</span>
            {$smarty.capture.inputTrainingTitle}
        </div>
        <div class="mdl-cell mdl-cell--2-col mdl-cell--middle trainingDay">
            <span class="normalMode">{$training.dayF}</span>
            {$smarty.capture.inputTrainingDay}
        </div>
        <div class="mdl-cell mdl-cell--1-col mdl-cell--middle trainingFrom">
            <span class="normalMode">{$training.fromF}</span>
            {$smarty.capture.inputTrainingFrom}
        </div>
        <div class="mdl-cell mdl-cell--1-col mdl-cell--middle trainingTo">
            <span class="normalMode">{$training.toF}</span>
            {$smarty.capture.inputTrainingTo}
        </div>
        <div class="mdl-cell mdl-cell--3-col mdl-cell--middle button">
            <div class="normalButtons">
                <button class="mdl-color-text--grey-600 mdl-button mdl-js-button mdl-button--icon changeTraining" id="changeTraining_{$training.ID}">
                  <i class="material-icons">edit</i>
                </button>
                <div class="mdl-tooltip" for="changeTraining_{$training.ID}">
                &Auml;ndern
                </div>
                <button class="mdl-color-text--grey-600 mdl-button mdl-js-button mdl-button--icon deleteTraining" id="deleteTraining_{$training.ID}">
                  <i class="material-icons">delete</i>
                </button>
                <div class="mdl-tooltip" for="deleteTraining_{$training.ID}">
                L&ouml;schen
                </div>
                <button class="mdl-color-text--grey-600 mdl-button mdl-js-button mdl-button--icon exportTraining" id="exportTraining_{$training.ID}">
                  <i class="material-icons">file_download</i>
                </button>
                <div class="mdl-tooltip" for="exportTraining_{$training.ID}">
                Export
                </div>
            </div>
            <div class="mdl-spinner mdl-spinner--single-color mdl-js-spinner is-active"></div>
            <div class="editButtons">
                <button type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored mdl-js-ripple-effect large submit">
                  &Auml;ndern
                </button>
                <button class="mdl-button mdl-js-button mdl-button--fab mdl-button--mini-fab mdl-button--primary mdl-js-ripple-effect small submit" id="submit-small">
                    <i class="material-icons">done</i>
                </button>
                <div class="mdl-tooltip" for="submit-small">
                  &Auml;ndern
                </div>
                <button type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect large reset">
                  Abbrechen
                </button>
                <button class="mdl-button mdl-js-button mdl-button--fab mdl-button--mini-fab mdl-js-ripple-effect small reset" id="reset-small">
                    <i class="material-icons">close</i>
                </button>
                <div class="mdl-tooltip" for="reset-small">
                  Abbrechen
                </div>
            </div>
        </div>
    </div>
    {foreachelse}
    <div class="mdl-grid">
        <div class="mdl-cell mdl-cell--12-col mdl-cell--middle list-empty">Keine Trainingstermine vorhanden</div>
    </div>
    {/foreach} 
    <div class="mdl-grid training_new">
        <div class="mdl-cell mdl-cell--5-col">{$smarty.capture.inputTrainingTitle}</div>
        <div class="mdl-cell mdl-cell--2-col">{$smarty.capture.inputTrainingDay}</div>
        <div class="mdl-cell mdl-cell--1-col">{$smarty.capture.inputTrainingFrom}</div>
        <div class="mdl-cell mdl-cell--1-col">{$smarty.capture.inputTrainingTo}</div>
        <div class="mdl-cell mdl-cell--3-col buttons">
            <button id="submitTraining" class="mdl-button mdl-js-button mdl-button--icon mdl-button--colored submitTraining">
              <i class="material-icons">add_circle</i>
            </button>
            <div class="mdl-tooltip" for="submitTraining">
            Hinzuf&uuml;gen
            </div>
        </div>
    </div>
</div>
</div>
{foreachelse}
<div class="info">Bitte erst Semester anlegen!</div>
{/foreach}
{/if}
