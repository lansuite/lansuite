<?php

namespace LanSuite\Validator;

interface ValidatorInterface
{

    /**
     * Checks if the passed value is valid.
     *
     * @param string      $value      The value that should be validated
     * @return boolean
     */
    public function validate($value);

    /**
     * Returns the error code based on the validate() call.
     *
     * @return int
     */
    public function getErrorCode();

    /**
     * Checks if the given option is enabled.
     * Returns true if the option is enabled.
     * False otherwise.
     *
     * @param string    $option     Option to check for
     * @return boolean
     */
    public function isOptionEnabled($option);

    /**
     * Enables the given option.
     * Returns true if successful.
     * False otherwise.
     *
     * @param string    $option     Option to enable
     * @return boolean
     */
    public function enableOption($option);

    /**
     * Disables the given option.
     * Returns true if successful.
     * False otherwise.
     *
     * @param string    $option     Option to disable
     * @return boolean
     */
    public function disableOption($option);
}
