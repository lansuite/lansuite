<table width="100%" border="0" cellspacing="0" cellpadding="4">
  <tr>
    <td class="backpath" nowrap>
      <strong>{$LANG.Servers}</strong>
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
          <p class="title"><strong>{$LANG.Servers}</strong></p>
          <table width="75%" border="0" cellspacing="1" cellpadding="3">
            <tr>
              <td class="darkcell">{$LANG.IP}</td>
              <td class="darkcell">{$LANG.Port}</td>
              <td class="darkcell">{$LANG.Game}</td>
              <td class="darkcell"></td>
            </tr>
            {if $NO_SERVERS eq true}
            <tr>
              <td class="lightcell" colspan="4">{$LANG.ERROR_NOSERVERS}</td>
            </tr>
            {else}
            {section name=server loop=$SERVERS}
            <tr>
              <form name="manageservers_{$SERVERS[server].id}" action="index.php" method="post">
              <input type="hidden" name="mode" value="manageservers">
              <input type="hidden" name="server_id" value="{$SERVERS[server].id}">
              <td class="lightcell"><input type="text" name="server_ip" value="{$SERVERS[server].ip}" size="15"></td>
              <td class="lightcell"><input type="text" name="server_port" value="{$SERVERS[server].port}" size="5"></td>
              <td class="lightcell">
                          <select name="server_game">
                      {section name=game loop=$GAMES}
                          <option value="{$GAMES[game].id}"{if $SERVERS[server].game eq $GAMES[game].id} selected{/if}>{$GAMES[game].name}</option>
                      {/section}
                          </select>
              </td>
              <td class="lightcell"><input type="submit" name="action" value="{$LANG.Modify}"> <input type="submit" name="action" value="{$LANG.Remove}"> <input type="reset" name="Reset" value="{$LANG.Reset}"></td>
                  </form>
            </tr>
            {/section}
            {/if}
            {if $NO_PLUGINS eq true}
            <tr>
          <td class="darkcell" colspan="4">{$LANG.ERROR_NOPLUGINSCANNOTADD}</td>
        </tr>
            {else}
            <tr>
              <form name="manageservers_new" action="index.php" method="post">
              <input type="hidden" name="mode" value="manageservers">
              <td class="darkcell"><input type="text" name="new_ip" value="{$SERVERS[server].ip}" size="15"></td>
                  <td class="darkcell"><input type="text" name="new_port" value="{$SERVERS[server].port}" size="5"></td>
                  <td class="darkcell">
                        <select name="new_game">
                      {section name=game loop=$GAMES}
                          <option value="{$GAMES[game].id}">{$GAMES[game].name}</option>
                      {/section}
                          </select>
                  </td>
                  <td class="darkcell"><input type="submit" name="action" value="{$LANG.Add}"> <input type="reset" name="Reset" value="{$LANG.Reset}"></td>
                  </form>
            </tr>
            {/if}
          </table>
    </td>
  </tr>
</table>