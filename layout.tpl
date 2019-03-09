<div class="mdl-layout mdl-js-layout mdl-layout--fixed-header">
    <header class="mdl-layout__header">
        <div class="mdl-layout__header-row">
        {if $back_button}
        <div class="mdl-layout__drawer-button" id="my_back_button">
            <i class="material-icons">arrow_back</i>
        </div>
        {/if}
        <span class="mdl-layout-title">Noodle</span>
        <div class="mdl-layout-spacer"></div>
          <nav class="mdl-navigation">
              {$smarty.capture.top_links}
          </nav>
        </div>
    </header>
    <div class="mdl-layout__drawer" id="drawer">
        <span class="mdl-layout-title">Noodle</span>
        <nav class="mdl-navigation">
            {$smarty.capture.drawer_links}
        </nav>
    </div>

<main class="mdl-layout__content">
<div class="page-content">
{$smarty.capture.main_content}
</div>

{assign var=silly_strings value=['❤', '😀', '☺', '😃', '🍜', '🍝', 'Das Beste Noodle aller Zeiten.',
                                 'Immer Al Dente seit 2014', 'Noodle hat dich lieb',
                                 '· · · >* )', 'eldooN', 'Enn, Doppel-O, Deh, Ell, Eh']}

<footer class="mdl-mini-footer footer">
  <div class="mdl-logo">{$silly_strings[{0|rand:{$silly_strings|@count}}]}</div>
  <div class="mdl-mini-footer--left-section">
    <ul class="mdl-mini-footer--link-list">
      {if $feedback_mail}<li><a href="mailto:{$feedback_mail}"><i class="material-icons">email</i>Feedback</a></li>{/if}
      <li><a href=".?q=admin"><i class="material-icons">settings</i>Admin</a></li>
    </ul>
  </div>
</footer>
</main>
</div>
