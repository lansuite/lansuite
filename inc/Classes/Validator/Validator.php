<?php

namespace LanSuite\Validator;

abstract class Validator implements ValidatorInterface
{

    /**
     * Options array to keep track which option is enabled.
     *
     * @var array
     */
    protected $options = [];

    /**
     * Error code.
     * Contains a value after calling validate().
     *
     * @var int
     */
    protected $errorCode = 0;

    /**
     * {@inheritdoc}
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * {@inheritdoc}
     */
    public function isOptionEnabled($option)
    {
        return $this->options[$option];
    }

    /**
     * {@inheritdoc}
     */
    public function enableOption($option)
    {
        return $this->setOption($option, true);
    }

    /**
     * {@inheritdoc}
     */
    public function disableOption($option)
    {
        return $this->setOption($option, false);
    }

    /**
     * {@inheritdoc}
     */
    private function setOption($option, $value)
    {
        if (!array_key_exists($option, $this->options)) {
            return false;
        }

        $this->options[$option] = $value;
        return true;
    }
}
