<table width="100%" border="0" cellspacing="0" cellpadding="4">
  <tr>
    <td class="backpath" nowrap>
      <strong>{$LANG.Plugins}</strong>
    </td>
    <td class="backpath" nowrap>
      <div align="right">{$SITE_ADMIN_NAV}</div>
    </td>
  </tr>
</table>
<br>
<table width="100%" border="0" cellspacing="0" cellpadding="10">
  <tr>
    <td valign="top">
          <form name="manageplugins" action="index.php" method="post">
      <input type="hidden" name="mode" value="manageplugins">
      {section name=plugin loop=$PLUGINS}
      <input type="hidden" name="plugin_{$PLUGINS[plugin].id}" value="{$PLUGINS[plugin].filename}">
      {/section}
          <p class="title"><strong>{$LANG.Plugins}</strong></p>
          <p>
          {$LANG.MSG_IMPROVEPERFORMANCE}
          </p>
          <table width="50%" border="0" cellspacing="1" cellpadding="3">
            <tr>
              <td class="darkcell">{$LANG.Type}</td>
              <td class="darkcell">{$LANG.Game}</td>
              <td class="darkcell">{$LANG.Enabled}</td>
            </tr>
            {if $NO_PLUGINS eq true}
            <tr>
              <td class="lightcell" colspan="3">{$LANG.ERROR_NOPLUGINS}</td>
                </tr>
            {else}
            {section name=plugin loop=$PLUGINS}
            <tr>
              <td class="lightcell">{if $PLUGINS[plugin].type eq "Game"}<strong>{/if}{$PLUGINS[plugin].type}{if $PLUGINS[plugin].type eq "Game"}</strong>{/if}</td>
              <td class="lightcell">{if $PLUGINS[plugin].type eq "Game"}<strong>{/if}{$PLUGINS[plugin].name}{if $PLUGINS[plugin].type eq "Game"}</strong>{/if}</td>
              <td class="lightcell"><input name="plugin_{$PLUGINS[plugin].id}_enabled" type="checkbox" value="1"{if $PLUGINS[plugin].enabled eq 1} checked{/if}></td>
            </tr>
            {/section}
            {/if}
            <tr>
              <td class="darkcell" colspan="4">
                      <input type="submit" name="action" value="{$LANG.Modify}"> <input type="reset" name="Reset" value="{$LANG.Reset}">
              </td>
            </tr>
          </table>
          </form>
    </td>
  </tr>
</table>