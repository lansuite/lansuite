<?php

$dsp->NewContent('Wähle den Eintrag aus, der Verlinkt werden soll');

function GetLinks($caption, $mod, $table, $id, $name, $link)
{
    global $func, $db, $dsp;

    if ($func->isModActive($mod)) {
        $out = '<select name="link" onChange="javascript:if (this.options[this.selectedIndex].value != \'\') InsertCode(opener.document.'. $_GET['form'] .'.'. $_GET['textarea'] .', \'[url='. $link .'\' + this.options[this.selectedIndex].value + \']\', \'[/url]\')">';
        $out .= '<option value="">'. t('Bitte Link auswählen') .'</option>';
        $res = $db->qry("SELECT %plain%, %plain% FROM %prefix%%plain%", $id, $name, $table);
        while ($row = $db->fetch_array($res)) {
            $out .= '<option value="'. $row[$id] .'">'. $row[$name] .'</option>';
        }
        $out .= '</select>';
        $dsp->AddDoubleRow($caption, $out);
    }
}

GetLinks(t('News'), 'news', 'news', 'newsid', 'caption', 'index.php?mod=news&action=comment&newsid=');
GetLinks(t('Board'), 'board', 'board_forums', 'fid', 'name', 'index.php?mod=board&action=forum&fid=');
GetLinks(t('Board-Thread'), 'board', 'board_threads', 'tid', 'caption', 'index.php?mod=board&action=thread&tid=');
GetLinks(t('Bug-Eintrag'), 'bugtracker', 'bugtracker', 'bugid', 'caption', 'index.php?mod=bugtracker&bugid=');
GetLinks(t('Server'), 'server', 'server', 'serverid', 'caption', 'index.php?mod=server&action=show_details&serverid=');
GetLinks(t('Sitzblock'), 'seating', 'seat_block', 'blockid', 'name', 'index.php?mod=seating&action=show&step=2&blockid=');
GetLinks(t('Turnier'), 'tournament2', 'tournament_tournaments', 'tournamentid', 'name', 'index.php?mod=tournament2&action=details&tournamentid=');
GetLinks(t('Turnier-Paarungen'), 'tournament2', 'tournament_tournaments', 'tournamentid', 'name', 'index.php?mod=tournament2&action=games&step=2&tournamentid=');
GetLinks(t('Turnier-Spielbaum'), 'tournament2', 'tournament_tournaments', 'tournamentid', 'name', 'index.php?mod=tournament2&action=tree&step=2&tournamentid=');
GetLinks(t('Turnier-Ranking'), 'tournament2', 'tournament_tournaments', 'tournamentid', 'name', 'index.php?mod=tournament2&action=rangliste&step=2&tournamentid=');
