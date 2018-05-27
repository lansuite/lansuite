<?php

namespace LanSuite\Validator;

class Email extends Validator
{
    /**
     * @internal
     */
    const PATTERN_HTML5 = '/^[a-zA-Z0-9.!#$%&\'*+\\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)+$/';

    /**
     * @internal
     */
    const PATTERN_LOOSE = '/^.+\@\S+\.\S+$/';

    private static $emailPatterns = array(
        self::VALIDATION_MODE_LOOSE => self::PATTERN_LOOSE,
        self::VALIDATION_MODE_HTML5 => self::PATTERN_HTML5,
    );

    /**
     * Validation type "html5".
     *
     * Performs a regular expression according the W3C HTML5 standard.
     */
    const VALIDATION_MODE_HTML5 = 'html5';

    /**
     * Validation type "loose".
     *
     * Performs a loose regular expression and only checks the basics.
     */
    const VALIDATION_MODE_LOOSE = 'loose';

    /**
     * Error type "empty"
     *
     * When an email is empty
     */
    const EMAIL_EMPTY_ERROR = 1;

    /**
     * Error type "format"
     *
     * When an email has a wrong format (e.g. @ is missing)
     */
    const INVALID_FORMAT_ERROR = 2;

    /**
     * Error type "mx record"
     *
     * When the host doesn't have a MX DNS record entered
     */
    const MX_CHECK_FAILED_ERROR = 3;

    /**
     * Error type "host record"
     *
     * When the host doesn't have a MX, A or AAAA DNS record entered
     */
    const HOST_CHECK_FAILED_ERROR = 4;

    /**
     * Option "MX record"
     *
     * If enabled, the MX DNS record will be checked for the host
     */
    const OPTION_MX_CHECK = 'mx-record-check';

    /**
     * Option "Host record"
     *
     * If enabled, the MX, A or AAAA DNS record will be checked for the host
     */
    const OPTION_HOST_CHECK = 'host-record-check';

    /**
     * Stores the validation mode.
     * See VALIDATION_MODE_* constants.
     *
     * @var string
     */
    private $mode;

    /**
     * @param string $mode
     */
    public function __construct($mode = self::VALIDATION_MODE_HTML5)
    {
        $this->mode = $mode;

        $this->options = [
            self::OPTION_MX_CHECK => false,
            self::OPTION_HOST_CHECK => false,
        ];
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param string      $value      The value that should be validated
     * @return boolean
     */
    public function validate($value)
    {
        if (null === $value || '' === $value) {
            $this->errorCode = self::EMAIL_EMPTY_ERROR;
            return false;
        }

        $value = (string) $value;

        if (!preg_match(self::$emailPatterns[$this->mode], $value)) {
            $this->errorCode = self::INVALID_FORMAT_ERROR;
            return false;
        }

        $host = (string) substr($value, strrpos($value, '@') + 1);

        // Check for host MX DNS resource records
        if ($this->isOptionEnabled(self::OPTION_MX_CHECK)) {
            if (!$this->checkMX($host)) {
                $this->errorCode = self::MX_CHECK_FAILED_ERROR;
                return false;
            }

            return true;
        }

        // Check for host MX, A or AAAA DNS resource records
        if ($this->isOptionEnabled(self::OPTION_HOST_CHECK) && !$this->checkHost($host)) {
            $this->errorCode = self::HOST_CHECK_FAILED_ERROR;
            return false;
        }

        return true;
    }

    /**
     * Check DNS Records for MX type.
     *
     * @param string $host
     * @return bool
     */
    private function checkMX(string $host): bool
    {
        return '' !== $host && checkdnsrr($host, 'MX');
    }

    /**
     * Check if one of MX, A or AAAA DNS RR exists.
     *
     * @param string $host
     * @return bool
     */
    private function checkHost(string $host): bool
    {
        return '' !== $host && ($this->checkMX($host) || (checkdnsrr($host, 'A') || checkdnsrr($host, 'AAAA')));
    }
}
