<?php

use LanSuite\Validator\Email;

/**
 * CheckValidEmail is an error callback function.
 *
 * @param string    $email
 * @return bool|mixed|string
 */
function CheckValidEmail($email)
{
    global $cfg, $config;

    // Check which validation mode to validate
    // an email address is configured
    switch ($config['validation']['email']['mode']) {
        case 'loose':
            $emailValidator = new Email(Email::VALIDATION_MODE_LOOSE);
        break;
        case 'html5':
        default:
            $emailValidator = new Email(Email::VALIDATION_MODE_HTML5);
    }

    // Enable features based on user configuration
    if ($config['validation']['email']['mx_check']) {
        $emailValidator->enableOption(Email::OPTION_MX_CHECK);
    }
    if ($config['validation']['email']['host_check']) {
        $emailValidator->enableOption(Email::OPTION_HOST_CHECK);
    }

    $email = trim($email);

    // We are not interested in the result (bool) of the validation function
    // because if we proceed further checks in this function
    $emailValidator->validate($email);

    // Evaluate the error code from the validation
    switch ($emailValidator->getErrorCode()) {
        case Email::EMAIL_EMPTY_ERROR:
            return t('Bitte gib deine E-Mail-Adresse ein');
            break;

        case Email::INVALID_FORMAT_ERROR:
            return t('Diese E-Mail-Adresse ist ungültig (falsches Format)');
            break;

        case Email::MX_CHECK_FAILED_ERROR:
            if ($emailValidator->isOptionEnabled(Email::OPTION_MX_CHECK)) {
                return t('Diese E-Mail-Adresse ist ungültig (falscher Host-Teil, MX DNS Record fehlerhaft)');
            }

            break;
        case Email::HOST_CHECK_FAILED_ERROR:
            if ($emailValidator->isOptionEnabled(Email::OPTION_HOST_CHECK)) {
                return t('Diese E-Mail-Adresse ist ungültig (falscher Host-Teil, MX, A oder AAAA DNS Record fehlerhaft)');
            }
            break;
    }

    // Compare first and second email entry.
    // As only first entry is passed to this function, we have to get the second one from $_POST directly
    if (!isset($_POST['email2']) || $email !== $_POST['email2']) {
        return t('E-Mail-Adressen stimmen nicht überein. Bitte überprüfe deine Eingabe');
    }

    // Check for forbidden trash mail services
    $TrashMailDomains = explode("\n", $cfg['mf_forbidden_trashmail_domains']);
    foreach ($TrashMailDomains as $key => $val) {
        $TrashMailDomains[$key] = trim($val);
    }

    list(, $hostName) = explode('@', $email);
    if (in_array($hostName, $TrashMailDomains)) {
        return t('Die E-Mail-Domain %1 ist nicht erlaubt, da sie ein Anbieter von "Wegwerf-Mails" ist', $hostName);
    }

    return false;
}