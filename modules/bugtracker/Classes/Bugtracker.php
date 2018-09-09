<?php

namespace LanSuite\Module\Bugtracker;

class Bugtracker
{
    /**
     * @var array
     */
    public $stati = [];

    public function __construct()
    {
        $this->stati[0] = t('Neu');
        $this->stati[1] = t('Bestätigt');
        $this->stati[2] = t('In Bearbeitung');
        $this->stati[3] = t('Feedback benötigt');
        $this->stati[4] = t('Behoben');
        $this->stati[5] = t('Aufgeschoben');
        $this->stati[6] = t('Geschlossen');
        $this->stati[7] = t('Wiedereröffnet');
    }

    /**
     * @param int $bugid
     * @param int $state
     * @return void
     */
    private function SetBugStateInternal($bugid, $state)
    {
        global $db, $func, $auth;

        if ($auth['type'] <= 1) {
            $row = $db->qry_first("
              SELECT
                reporter,
                caption,
                state
              FROM %prefix%bugtracker
              WHERE
                bugid = %int%", $bugid);
            if (!(($row['state'] == 0 and $state == 1) or ($row['state'] == 4 and $state == 7) or ($row['state'] == 3 and $state == 2))) {
                $func->information(t('Der Status des Bugreports <b>"%1"</b> konnte nicht geändert werden, da du nur von <b>"Neu" auf "Bestätigt"</b>, von <b>"Feedback benötigt" auf "In Bearbeitung"</b> und von <b>"Behoben" auf "Wiedereröffnet"</b> wechseln darfst.', array($row['caption'])));
                return;
            }
            if ($state == 1 and $row['reporter'] == $auth['userid']) {
                $func->information(t('Du darfst nicht deinen eigenen Bugreport bestätigen'));
                return;
            }
        }

        $row = $db->qry_first("SELECT 1 AS found FROM %prefix%bugtracker WHERE state = %int% AND bugid = %int%", $state, $bugid);
        if (!$row['found']) {
            $mail = new Lansuite\Module\Mail\Mail();

            $db->qry("UPDATE %prefix%bugtracker SET state = %int% WHERE bugid = %int%", $state, $bugid);
            $func->log_event(t('Bugreport auf Status "%1" geändert', array($this->stati[$state])), 1, '', $bugid);

            // Mails
            $AddLink = '

[url=index.php?mod=bugtracker&bugid=%2]'. t('Zum Bug-Eintrag') .'[/url]';
            if ($state == 1 or $state == 2 or $state == 3 or $state == 4 or $state == 5 or $state == 6) {
                $row = $db->qry_first("SELECT reporter, caption FROM %prefix%bugtracker WHERE bugid = %int%", $bugid);
                if ($row['reporter'] != $auth['userid']) {
                    switch ($state) {
                        case 1:
                            $mail->create_sys_mail($row['reporter'], t('Dein Bugreport wurde bestätigt'), t('Der Status deines Bugreports [b]"%1"[/b] wurde auf [b]"Bestätigt"[/b] gesetzt. Dies bedeutet, dass der Fehler bekannt ist (bzw. der Feature-Wunsch anerkannt wurde), sich jedoch noch kein Bearbeiter gefunden hat.'. $AddLink, array($row['caption'], $bugid)));
                            break;
                        case 2:
                            $mail->create_sys_mail($row['reporter'], t('Dein Bugreport wird nun bearbeitet'), t('Der Status deines Bugreports [b]"%1"[/b] wurde auf [b]"In Bearbeitung"[/b] gesetzt. Dies bedeutet, dass jemand an dem Problem arbeitet und es vorraussichtlich in Kürze behoben sein wird.'. $AddLink, array($row['caption'], $bugid)));
                            break;
                        case 3:
                            $mail->create_sys_mail($row['reporter'], t('Feedback zu deinem Bugreport benötigt'), t('Der Status deines Bugreports [b]"%1"[/b] wurde auf [b]"Feedback benötigt"[/b] gesetzt. Bitte schaue dir den Bugreport noch einmal an und hilf, deine Angaben zu vervollständigen.'. $AddLink, array($row['caption'], $bugid)));
                            break;
                        case 4:
                            $mail->create_sys_mail($row['reporter'], t('Das Problem aus deinem Bugreport wurde behoben'), t('Gute Nachrichten: Der Status deines Bugreports [b]"%1"[/b] wurde auf [b]"Behoben"[/b] gesetzt. Bitte prüfe nochmals, ob nun auch wirklich alles korrekt funktioniert und setze den Status auf "Wiedereröffnet", falls weiterhin noch Probleme bestehen.'. $AddLink, array($row['caption'], $bugid)));
                            break;
                        case 5:
                            $mail->create_sys_mail($row['reporter'], t('Dein Bugreport wurde aufgeschoben'), t('Der Status deines Bugreports [b]"%1"[/b] wurde auf [b]"Aufgeschoben"[/b] gesetzt. Dies ist in aller Regel dann der Fall, wenn der Aufwand unverhältnismäßig hoch gegenüber dem Nutzen eines Fixes sein würde, oder der Wunsch mit aktuellen Boardmitteln von Lansuite nur sehr schwer realisierbar ist. Näheres erfährst du eventuell in den Kommentaren dieses Bug-Reports.'. $AddLink, array($row['caption'], $bugid)));
                            break;
                        case 6:
                            $mail->create_sys_mail($row['reporter'], t('Dein Bugreport wurde geschlossen'), t('Der Status deines Bugreports [b]"%1"[/b] wurde auf [b]"Geschlossen"[/b] gesetzt. Dies bedeutet in den meisten Fällen, dass sich das Problem auf Grund eines Irrtums von selbst behoben hat und kein Fix für dieses Problem notwendig bzw. vorgesehen ist. Näheres erfährst du eventuell in den Kommentaren dieses Bug-Reports.'. $AddLink, array($row['caption'], $bugid)));
                            break;
                    }
                    $func->log_event(t('Benachrichtigungsmail an Reporter versandt'), 1, '', $bugid);
                }
            }
            if ($state == 2 or $state == 7) {
                $row = $db->qry_first("SELECT agent, caption FROM %prefix%bugtracker WHERE bugid = %int%", $bugid);
                if ($row['agent'] != $auth['userid']) {
                    switch ($state) {
                        case 2:
                            $mail->create_sys_mail($row['agent'], t('Ein dir zugewiesener Bugreport wartet auf seine Bearbeitung'), t('Der Status des Bugreports [b]"%1"[/b] wurde auf [b]"In Bearbeitung"[/b] gesetzt. Entweder hat ein Administrator dir den Eintrag zugewiesen oder ein Benutzer hat das von dir erwartete Feedback übermittelt und den Status anschließend geändert. Näheres erfährst du eventuell in den Kommentaren dieses Bug-Reports.'. $AddLink, array($row['caption'], $bugid)));
                            break;
                        case 7:
                            $mail->create_sys_mail($row['agent'], t('Ein dir zugewiesener Bugreport wurde wiedereröffnet'), t('Der Status des Bugreports [b]"%1"[/b] wurde auf [b]"Wiedereröffnet"[/b] gesetzt. Bitte schaue dir den Bugreport noch einmal an und behebe nach Möglichkeit die weiteren Probleme.'. $AddLink, array($row['caption'], $bugid)));
                            break;
                    }
                    $func->log_event(t('Benachrichtigungsmail an Bearbeiter versandt'), 1, '', $bugid);
                }
            }
        }
    }

    /**
     * @param int $bugid
     * @param int $userid
     * @return void
     */
    private function AssignBugToUserInternal($bugid, $userid)
    {
        global $db, $func, $auth;

        $row = $db->qry_first("SELECT 1 AS found FROM %prefix%bugtracker WHERE agent = %int% AND bugid = %int%", $userid, $bugid);
        if (!$row['found']) {
            if ($auth['type'] > 1) {
                $db->qry("UPDATE %prefix%bugtracker SET agent = %int% WHERE bugid = %int%", $userid, $bugid);
            }

            if ($userid == 0) {
                $func->log_event(t('Benutzerzuordnung gelöscht'), 1, '', $bugid);
            } else {
                $row = $db->qry_first("SELECT username FROM %prefix%user WHERE userid = %int%", $userid);
                $func->log_event(t('Bugreport Benutzer "%1" zugeordnet', array($row['username'])), 1, '', $bugid);
            }
        }
    }

    /**
     * @param int $bugid
     * @param int $userid
     * @return bool
     */
    public function AssignBugToUser($bugid, $userid)
    {
        if (!$bugid) {
            return false;
        }

        $this->AssignBugToUserInternal($bugid, $userid);

        if ($userid == 0) {
            $this->SetBugStateInternal($bugid, 0);
        } else {
            $this->SetBugStateInternal($bugid, 2);
        }

        return true;
    }

    /**
     * @param int $bugid
     * @param int $state
     * @return bool
     */
    public function SetBugState($bugid, $state)
    {
        global $auth;

        if (!$bugid) {
            return false;
        }
        if ($state == '') {
            return false;
        }

        $this->SetBugStateInternal($bugid, $state);
        if ($state == 2 or $state == 4 or $state == 6) {
            $this->AssignBugToUserInternal($bugid, $auth['userid']);
        }

        return true;
    }
}
