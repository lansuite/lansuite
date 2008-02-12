<table width="100%" border="0" cellspacing="0" cellpadding="4">
  <tr>
    <td class="backpath" nowrap>
      <a href="{$SITE_HOME_URL}">{$SITE_HOME_NAME}</a>: phpUA: <strong>{$LANG.Login}</strong>
    </td>
    <td class="backpath" nowrap>
      <div align="right">{$SITE_NAV}</div>
    </td>
  </tr>
</table>
<br>
<table width="100%" border="0" cellspacing="0" cellpadding="10">
  <tr>
    <td>
          <p class="title"><strong>{$LANG.Login}</strong></p>
          <form name="login" action="index.php?mod=phpua&action=show" method="post">
          <input type="hidden" name="mode" value="login">
      <table width="50%" cellspacing="1" cellpadding="3" border="0">
        <tr>
          <td class="darkcell">{$LANG.AdministratorLogin}</td>
        </tr>
        <tr>
          <td class="lightcell">
                            <input name="mode" value="authenticate" type="hidden">
                              {$LANG.Username}: <input name="username" size="10" type="text"> {$LANG.Password}: <input name="password" size="10" type="password"> <input name="Submit" value="{$LANG.Login}" type="submit">
                  </td>
        </tr>
      </table>
          </form>
    </td>
  </tr>
</table>