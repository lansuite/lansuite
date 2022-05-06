<?php

use LanSuite\Validator\Email;

/**
 * CheckValidEmail is a callback function for e.g. MasterForm to verify a given email address against a set of requirements.
 * So far it uses a generic (configurable) Validator
 *
 * @param string    $email
 * @return bool|mixed|string
 */
function CheckValidEmail($email)
{
    global $cfg,$db;

    // Check which validation mode to validate
    // an email address is configured
    switch ($cfg['sys_email_regex_verification']) {
        case 'loose':
            $emailValidator = new Email(Email::VALIDATION_MODE_LOOSE);
            break;
        case 'html5':
        default:
            $emailValidator = new Email(Email::VALIDATION_MODE_HTML5);
    }

    // Enable dns verification features based on user configuration
    switch ($cfg['sys_email_dns_verification']) {
        case 'mx_check':
            $emailValidator->enableOption(Email::OPTION_MX_CHECK);
            break;
        case 'host_check':
            $emailValidator->enableOption(Email::OPTION_HOST_CHECK);
            break;
        case 'none':
        default:
            // Nothing to activate here
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
