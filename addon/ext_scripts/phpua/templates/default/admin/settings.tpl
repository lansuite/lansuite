<table width="100%" border="0" cellspacing="0" cellpadding="4">
  <tr>
    <td class="backpath" nowrap>
      <strong>{$LANG.Settings}</strong>
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
          <form name="settings" action="index.php" method="post">
      <input type="hidden" name="mode" value="settings">
          <p class="title"><strong>{$LANG.Settings}</strong></p>
          {if $SAFEMODE eq true}
          <p>
          <strong>{$LANG.Note}:</strong> {$LANG.ERROR_SAFEMODE} {$SAFEMODE_TIMELIMIT} {$LANG.sec}.
          </p>
          {/if}
          <table width="50%" border="0" cellspacing="1" cellpadding="3">
            <tr>
              <td class="darkcell">{$LANG.SiteName}:</td>
              <td class="lightcell"><input type="text" name="config_site_home_name" value="{$CONFIG_SITE_HOME_NAME}"></td>
            </tr>
            <tr>
              <td class="darkcell">{$LANG.SiteURL}:</td>
              <td class="lightcell"><input type="text" name="config_site_home_url" value="{$CONFIG_SITE_HOME_URL}"></td>
            </tr>
            <tr>
              <td class="darkcell">{$LANG.ConnectionTimeout}:</td>
              <td class="lightcell"><input type="text" name="config_timeout" value="{$CONFIG_TIMEOUT}" size="2"> {$LANG.sec} ({$LANG.default}: 5)</td>
            </tr>
            <tr>
              <td class="darkcell">{$LANG.StreamInterval}:</td>
              <td class="lightcell"><input type="text" name="config_interval" value="{$CONFIG_INTERVAL}" size="2"> {$LANG.sec} ({$LANG.default}: 5)</td>
            </tr>
            <tr>
              <td class="darkcell">{$LANG.StreamTimeLimit}:</td>
              <td class="lightcell"><input type="text" name="config_timelimit" value="{$CONFIG_TIMELIMIT}" size="2"> {$LANG.sec} ({$LANG.default}: 60)</td>
            </tr>
            <tr>
              <td class="darkcell">{$LANG.Style}:</td>
              <td class="lightcell">
                        <select name="config_style">
                      {section name=style loop=$STYLES}
                          <option value="{$STYLES[style].name}"{if $CONFIG_STYLE eq $STYLES[style].name} selected{/if}>{$STYLES[style].name}</option>
                      {/section}
                          </select>
              </td>
            </tr>
            <tr>
              <td class="darkcell">{$LANG.Language}:</td>
              <td class="lightcell">
                        <select name="config_lang">
                      {section name=lang loop=$LANGUAGES}
                          <option value="{$LANGUAGES[lang].code}"{if $CONFIG_LANG eq $LANGUAGES[lang].code} selected{/if}>{$LANGUAGES[lang].name}</option>
                      {/section}
                          </select>
              </td>
            </tr>
            <tr>
              <td class="darkcell" colspan="2"><input type="submit" name="action" value="{$LANG.Modify}"> <input type="reset" name="Reset" value="{$LANG.Reset}"></td>
            </tr>
      </table>
      </form>
    </td>
  </tr>
</table>