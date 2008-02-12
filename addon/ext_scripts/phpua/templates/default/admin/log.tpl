<table width="100%" border="0" cellspacing="0" cellpadding="4">
  <tr>
    <td class="backpath" nowrap>
      <a href="{$SITE_HOME_URL}">{$SITE_HOME_NAME}</a>: phpUA: {$LANG.Administration}: <strong>{$LANG.Log}</strong>
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
	  <p class="title"><strong>{$LANG.Log}</strong></p>
	  <table width="75%" border="0" cellspacing="1" cellpadding="3">
	    <tr>
	      <td class="darkcell">{$LANG.Timestamp}</td>
	      <td class="darkcell">{$LANG.Username}</td>
	      <td class="darkcell">{$LANG.Event}</td>
	    </tr>
	    {section name=loop loop=$LOG}
	    <tr>
	      <td class="lightcell">{$LOG[loop].timestamp}</td>
	      <td class="lightcell">{$LOG[loop].username}</td>
	      <td class="lightcell">{$LOG[loop].event}</td>
	    </tr>
	    {/section}
	    <tr>
	      <form name="log_{$ADMIN[loop].id}" action="index.php" method="post">
	      <input type="hidden" name="mode" value="log">
	      <td class="darkcell" colspan="3"><input type="submit" name="action" value="{$LANG.Clear}"></td>
	      </form>
	    </tr>
	  </table>
    </td>
  </tr>
</table>