<?php

namespace LanSuite\Tests\Validator;

use PHPUnit\Framework\TestCase;
use LanSuite\Validator\Email;

class EmailTest extends TestCase
{
    /**
     * @covers Email::isOptionEnabled
     * @covers Email::enableOption
     */
    public function testOption_EnableMXCheck()
    {
        $validator = new Email();
        $this->assertFalse($validator->isOptionEnabled(Email::OPTION_MX_CHECK));

        $validator->enableOption(Email::OPTION_MX_CHECK);
        $this->assertTrue($validator->isOptionEnabled(Email::OPTION_MX_CHECK));
    }

    /**
     * @covers Email::isOptionEnabled
     * @covers Email::disableOption
     */
    public function testOption_DisableMXCheck()
    {
        $validator = new Email();
        $this->assertFalse($validator->isOptionEnabled(Email::OPTION_MX_CHECK));

        $validator->enableOption(Email::OPTION_MX_CHECK);
        $this->assertTrue($validator->isOptionEnabled(Email::OPTION_MX_CHECK));

        $validator->disableOption(Email::OPTION_MX_CHECK);
        $this->assertFalse($validator->isOptionEnabled(Email::OPTION_MX_CHECK));
    }

    /**
     * @covers Email::isOptionEnabled
     * @covers Email::enableOption
     */
    public function testOption_EnableHostCheck()
    {
        $validator = new Email();
        $this->assertFalse($validator->isOptionEnabled(Email::OPTION_HOST_CHECK));

        $validator->enableOption(Email::OPTION_HOST_CHECK);
        $this->assertTrue($validator->isOptionEnabled(Email::OPTION_HOST_CHECK));
    }

    /**
     * @covers Email::isOptionEnabled
     * @covers Email::disableOption
     */
    public function testOption_DisableHostCheck()
    {
        $validator = new Email();
        $this->assertFalse($validator->isOptionEnabled(Email::OPTION_HOST_CHECK));

        $validator->enableOption(Email::OPTION_HOST_CHECK);
        $this->assertTrue($validator->isOptionEnabled(Email::OPTION_HOST_CHECK));

        $validator->disableOption(Email::OPTION_HOST_CHECK);
        $this->assertFalse($validator->isOptionEnabled(Email::OPTION_HOST_CHECK));
    }

    public function dataProviderValidEmailsLooseValidation()
    {
        return array(
            array('fabien@symfony.com'),
            array('example@example.co.uk'),
            array('fabien_potencier@example.fr'),
            array('example@example.co..uk'),
            array('{}~!@!@£$%%^&*().!@£$%^&*()'),
            array('example@example.co..uk'),
            array('example@-example.com'),
            array(sprintf('example@%s.com', str_repeat('a', 64))),
        );
    }

    /**
     * @dataProvider dataProviderValidEmailsLooseValidation
     */
    public function testValidEmailsLooseValidation($email)
    {
        $validator = new Email(Email::VALIDATION_MODE_LOOSE);

        $this->assertTrue($validator->validate($email));
        $this->assertSame(0, $validator->getErrorCode());
    }

    public function dataProviderValidEmailsHTML5Validation()
    {
        return array(
            array('fabien@symfony.com'),
            array('example@example.co.uk'),
            array('fabien_potencier@example.fr'),
            array('{}~!@example.com'),
        );
    }

    /**
     * @dataProvider dataProviderValidEmailsHTML5Validation
     */
    public function testValidEmailsHtml5($email)
    {
        $validator = new Email(Email::VALIDATION_MODE_HTML5);

        $this->assertTrue($validator->validate($email));
        $this->assertSame(0, $validator->getErrorCode());
    }

    public function dataProviderInvalidEmailsLooseValidation()
    {
        return array(
            array('example'),
            array('example@'),
            array('example@localhost'),
            array('foo@example.com bar'),
        );
    }

    /**
     * @dataProvider dataProviderInvalidEmailsLooseValidation
     */
    public function testInvalidEmails($email)
    {
        $validator = new Email(Email::VALIDATION_MODE_LOOSE);

        $this->assertFalse($validator->validate($email));
        $this->assertSame(Email::INVALID_FORMAT_ERROR, $validator->getErrorCode());
    }

    public function dataProviderInvalidEmailsHTML5Validation()
    {
        return array(
            array('example'),
            array('example@'),
            array('example@localhost'),
            array('example@example.co..uk'),
            array('foo@example.com bar'),
            array('example@example.'),
            array('example@.fr'),
            array('@example.com'),
            array('example@example.com;example@example.com'),
            array('example@.'),
            array(' example@example.com'),
            array('example@ '),
            array(' example@example.com '),
            array(' example @example .com '),
            array('example@-example.com'),
            array(sprintf('example@%s.com', str_repeat('a', 64))),
        );
    }

    /**
     * @dataProvider dataProviderInvalidEmailsHTML5Validation
     */
    public function testInvalidHtml5Emails($email)
    {
        $validator = new Email(Email::VALIDATION_MODE_HTML5);

        $this->assertFalse($validator->validate($email));
        $this->assertSame(Email::INVALID_FORMAT_ERROR, $validator->getErrorCode());
    }
}
