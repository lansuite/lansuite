<?php

namespace LanSuite\Tests;

use PHPUnit\Framework\TestCase;

class TranslationTest extends TestCase
{
    /**
     * @covers \LanSuite\Translation::ReplaceParameters
     */
    public function testReplaceParameters_WithParametersAndKeyAndAuthTwentyTwo()
    {
        $input = 'This is %1 foo %2 bar baz';
        $parameters = ['Sparta', 'not'];
        $key = md5(__METHOD__);
        $GLOBALS['auth']['type'] = 5;
        $GLOBALS['cfg'] = [
            'show_translation_links' => true,
            'sys_language' => '',
        ];
        $expected = 'This is Sparta foo not bar baz <a href=index.php?mod=misc&action=translation&step=40&id=d81cf8faf0a9b58c77ec6b8c83d75700><img src=design/images/icon_translate.png height=10 width=10 border=0></a>';

        $translation = new \LanSuite\Translation();
        $actual = $translation->ReplaceParameters($input, $parameters, $key);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers \LanSuite\Translation::ReplaceParameters
     */
    public function testReplaceParameters_WithParametersAndKeyAndAuthTwo()
    {
        $input = 'This is %1 foo %2 bar baz';
        $parameters = ['Sparta', 'not'];
        $key = md5(__METHOD__);
        $GLOBALS['auth']['type'] = 2;
        $GLOBALS['cfg'] = [
            'show_translation_links' => true,
            'sys_language' => '',
        ];
        $expected = 'This is Sparta foo not bar baz <a href=index.php?mod=misc&action=translation&step=40&id=c397dc81823c06c7a52a5c8fa32e340f><img src=design/images/icon_translate.png height=10 width=10 border=0></a>';

        $translation = new \LanSuite\Translation();
        $actual = $translation->ReplaceParameters($input, $parameters, $key);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers \LanSuite\Translation::ReplaceParameters
     */
    public function testReplaceParameters_WithParameters()
    {
        $input = 'This is %1 foo %2 bar baz';
        $parameters = ['Sparta', 'not'];
        $expected = 'This is Sparta foo not bar baz';

        $translation = new \LanSuite\Translation();
        $actual = $translation->ReplaceParameters($input, $parameters);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers \LanSuite\Translation::get_lang
     */
    public function testGetLanguage_Invalid()
    {
        $_GET['language'] = 'foo';
        $translation = new \LanSuite\Translation();
        $actual = $translation->get_lang();

        $this->assertEquals('de', $actual);
    }

    /**
     * @covers \LanSuite\Translation::get_lang
     */
    public function testGetLanguage_FromConfig()
    {
        $GLOBALS['cfg']['sys_language'] = 'it';
        $translation = new \LanSuite\Translation();
        $actual = $translation->get_lang();

        $this->assertEquals('it', $actual);
    }

    /**
     * @covers \LanSuite\Translation::get_lang
     */
    public function testGetLanguage_FromSession()
    {
        $_SESSION['language'] = 'nl';
        $translation = new \LanSuite\Translation();
        $actual = $translation->get_lang();

        $this->assertEquals('nl', $actual);
    }

    /**
     * @covers \LanSuite\Translation::get_lang
     */
    public function testGetLanguage_FromGET()
    {
        $_GET['language'] = 'es';
        $translation = new \LanSuite\Translation();
        $actual = $translation->get_lang();

        $this->assertEquals('es', $actual);
    }

    /**
     * @covers \LanSuite\Translation::get_lang
     */
    public function testGetLanguage_Default()
    {
        $translation = new \LanSuite\Translation();
        $actual = $translation->get_lang();

        $this->assertEquals('de', $actual);
    }

    /**
     * @covers \LanSuite\Translation::get_lang
     */
    public function testGetLanguage_FromPOST()
    {
        $_POST['language'] = 'fr';
        $translation = new \LanSuite\Translation();
        $actual = $translation->get_lang();

        $this->assertEquals('fr', $actual);
    }
}
