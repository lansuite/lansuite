<?php

/**
 * @param string $status
 * @return mixed
 */
function GetTournamentStatus($status)
{
    $status_descriptor["open"]      = t('Anmeldung offen');
    $status_descriptor["locked"]    = t('Anmeldung geschlossen');
    $status_descriptor["invisible"] = t('Unsichtbar');
    $status_descriptor["process"]   = t('Wird gespielt');
    $status_descriptor["closed"]    = t('Beendet');

    return $status_descriptor[$status];
}
