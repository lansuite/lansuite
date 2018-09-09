<?php

/**
 * @param string $last_read
 * @return string
 */
function NewPosts($last_read)
{
    global $func, $line, $auth;

    if ($func->CheckNewPosts($line['LastPost'], 'board', $line['tid'])) {
        return "<a class=\"menu\" href=\"index.php?mod=board&action=thread&tid={$line['tid']}\"><img src=\"design/{$auth["design"]}/images/forum_new.png\" alt=\"".t('Neue Beiträge')."\" border=\"0\"></a>";
    } else {
        return "<a class=\"menu\" href=\"index.php?mod=board&action=thread&tid={$line['tid']}\"><img src=\"design/{$auth["design"]}/images/forum_old.png\" alt=\"".t('Kein neuer Beitrag')."\" border=\"0\"></a>";
    }
}
