<?php

/**
 * @param int $last
 * @return string
 */
function getTimeDiffAsName($last)
{
    if ((time()-$last) < 60*10) {
        return t("Online");
    } else {
        return t("Untätig");
    }
}
