<?php

/**
 * @param string $status
 * @return string
 */
function MailStatus($status)
{
    if ($status == "new") {
        return t('Ungelesen');
    }
    if ($status == "read") {
        return t('Gelesen');
    }
    if ($status == "reply") {
        return t('Beantwortet');
    }
}
