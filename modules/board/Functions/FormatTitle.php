<?php

/**
 * @param string $title
 * @return string
 */
function FormatTitle($title)
{
    global $dsp, $line, $func;

    $icon = '';
    if ($line['closed']) {
        $icon = $dsp->FetchIcon('locked', '', t('Nicht bezahlt!'));
    }
    if ($line['sticky']) {
        $icon = $dsp->FetchIcon('signon', '', t('Wichtig!'));
    }
    return $icon . "<a class=\"menu\" href=\"index.php?mod=board&action=thread&tid={$line['tid']}\">{$func->AllowHTML($title)}</a>";
}
