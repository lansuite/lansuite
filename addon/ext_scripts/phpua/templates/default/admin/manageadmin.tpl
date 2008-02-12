<table width="100%" border="0" cellspacing="0" cellpadding="4">
  <tr>
    <td class="backpath" nowrap>
      <a href="{$SITE_HOME_URL}">{$SITE_HOME_NAME}</a>: phpUA: {$LANG.Administration}: <strong>{$LANG.Admin}</strong>
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
	  <p class="title"><strong>{$LANG.Admin}</strong></p>
	  <table width="75%" border="0" cellspacing="1" cellpadding="3">
	    <tr>
	      <td class="darkcell" colspan="3">{$LANG.Username}</td>
	      <td class="darkcell"></td>
	    </tr>
	    {section name=loop loop=$ADMIN}
	    <tr>
	      <form name="manageadmin_{$ADMIN[loop].id}" action="index.php" method="post">
	      <input type="hidden" name="mode" value="manageadmin">
	      <input type="hidden" name="admin_id" value="{$ADMIN[loop].id}">
	      <td class="lightcell" colspan="3">{$ADMIN[loop].name}</td>
	      <td class="lightcell"><input type="submit" name="action" value="{$LANG.Remove}"></td>
	      </form>
	    </tr>
	    {/section}
	    <tr>
	      <td class="darkcell">{$LANG.Username}</td>
	      <td class="darkcell">{$LANG.Password}</td>
	      <td class="darkcell">{$LANG.ConfirmPassword}</td>
	      <td class="darkcell"></td>
	    </tr>
	    <tr>
	      <form name="manageadmin_new" action="index.php" method="post">
	      <input type="hidden" name="mode" value="manageadmin">
	      <td class="darkcell"><input type="text" name="new_name" size="15"></td>
	      <td class="darkcell"><input type="password" name="new_pswd" size="15"></td>
	      <td class="darkcell"><input type="password" name="new_pswd_confirm" size="15"></td>
	      <td class="darkcell"><input type="submit" name="action" value="{$LANG.Add}"> <input type="reset" name="Reset" value="{$LANG.Reset}"></td>
	      </form>
	    </tr>
	  </table>
    </td>
  </tr>
</table>