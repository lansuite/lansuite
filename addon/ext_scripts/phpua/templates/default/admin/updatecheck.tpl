<table width="100%" border="0" cellspacing="0" cellpadding="4">
  <tr>
    <td class="backpath" nowrap>
      <strong>{$LANG.UpdateCheck}</strong>
    </td>
    <td class="backpath" nowrap>
      <div align="right">{$SITE_ADMIN_NAV}</div>
    </td>
  </tr>
</table>
<br>
<table width="100%" border="0" cellspacing="0" cellpadding="10">
  <tr>
    <td>
          <p class="title"><strong>{$LANG.UpdateCheck}</strong></p>
          {if $UPDATEAVAILABLE eq true}
          <p>{$LANG.MSG_NEWVERSIONAVAILABLE} {$LANG.Version} {$LATESTVERSION}: <a href="http://www.unitedadmins.com/" target="_blank">http://www.unitedadmins.com/</a></p>
          {else}
          <p>{$LANG.MSG_LATESTVERSION}</p>
          {/if}
          <p><a href="index.php?mode=index">{$LANG.ReturntoServerIndex}</a></p>
        </td>
  </tr>
</table>