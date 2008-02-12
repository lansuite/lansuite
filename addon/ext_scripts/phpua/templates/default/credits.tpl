<table width="100%" border="0" cellspacing="0" cellpadding="4">
  <tr>
    <td class="backpath" nowrap>
      <a href="{$SITE_HOME_URL}">{$SITE_HOME_NAME}</a>: phpUA: <strong>{$LANG.Credits}</strong>
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
	  <p class="title"><strong>{$LANG.Credits}</strong></p>
	  <table width="50%" border="0" cellspacing="1" cellpadding="3">
	    <tr>
	      <td class="darkcell" nowrap>{$LANG.Style}: default</td>
	      <td class="lightcell">Kris Splittgerber</td>
	    </tr>
		{section name=credit loop=$CREDITS}
	    <tr>
	      <td class="darkcell" valign="top" nowrap>{$CREDITS[credit].task}</td>
	      <td class="lightcell" valign="top">{$CREDITS[credit].name}</td>
	    </tr>
	    {/section}
	  </table>
    </td>
  </tr>
</table>