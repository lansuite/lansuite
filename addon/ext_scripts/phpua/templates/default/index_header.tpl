<table width="100%" border="0" cellspacing="0" cellpadding="4">
  <tr>
    <td class="backpath" nowrap>
       Du bist hier: <strong>{$LANG.ServerIndex}</strong>
    </td>
    <td class="backpath" nowrap>
      <div align="right">{$INDEX_ADMIN_NAV}</div>
    </td>
  </tr>
</table>
<br>
<table width="100%" border="0" cellspacing="0" cellpadding="10">
  <tr>
    <td>
          <p class="title"><strong>{$LANG.ServerIndex}</strong></p>
          {if $AUTHENTICATED eq 1}
      <table width="50%" cellspacing="1" cellpadding="3" border="0">
        <tr>
          <td class="darkcell">{$LANG.Administration}</td>
        </tr>
        <tr>
          <td class="lightcell" nowrap>
                              {$INDEX_ADMIN_NAV}
                  </td>
        </tr>
      </table>
      {/if}
    </td>
  </tr>
</table>