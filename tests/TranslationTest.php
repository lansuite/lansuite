<?php

namespace LanSuite\Tests;

use PHPUnit\Framework\TestCase;

class TranslationTest extends TestCase
{
    /**
     * @covers translation::ReplaceParameters
     */
    public function testReplaceParameters_WithParametersAndKeyAndAuthTwentyTwo()
    {
        $input = 'This is %1 foo %2 bar baz';
        $parameters = ['Sparta', 'not'];
        $key = 22;
        $GLOBALS['auth']['type'] = 5;
        $GLOBALS['cfg'] = [
            'show_translation_links' => true,
            'sys_language' => '',
        ];
        $expected = 'This is Sparta foo not bar baz <a href=index.php?mod=misc&action=translation&step=40&id=22><img src=design/images/icon_translate.png height=10 width=10 border=0></a>';

        $translation = new \translation();
        $actual = $translation->ReplaceParameters($input, $parameters, $key);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers translation::ReplaceParameters
     */
    public function testReplaceParameters_WithParametersAndKeyAndAuthTwo()
    {
        $input = 'This is %1 foo %2 bar baz';
        $parameters = ['Sparta', 'not'];
        $key = 5;
        $GLOBALS['auth']['type'] = 2;
        $GLOBALS['cfg'] = [
            'show_translation_links' => true,
            'sys_language' => '',
        ];
        $expected = 'This is Sparta foo not bar baz <a href=index.php?mod=misc&action=translation&step=40&id=5><img src=design/images/icon_translate.png height=10 width=10 border=0></a>';

        $translation = new \translation();
        $actual = $translation->ReplaceParameters($input, $parameters, $key);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers translation::ReplaceParameters
     */
    public function testReplaceParameters_WithParameters()
    {
        $input = 'This is %1 foo %2 bar baz';
        $parameters = ['Sparta', 'not'];
        $expected = 'This is Sparta foo not bar baz';

        $translation = new \translation();
        $actual = $translation->ReplaceParameters($input, $parameters);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers translation::ReplaceParameters
     */
    public function testReplaceParameters_NoParameters()
    {
        $input = 'This is foo bar baz';

        $translation = new \translation();
        $actual = $translation->ReplaceParameters($input);

        $this->assertEquals($input, $actual);
    }

    /**
     * @covers translation::get_lang
     */
    public function testGetLanguage_Invalid() {
        $_GET['language'] = 'foo';
        $translation = new \translation();
        $actual = $translation->get_lang();

        $this->assertEquals('de', $actual);
    }

    /**
     * @covers translation::get_lang
     */
    public function testGetLanguage_FromConfig() {
        $GLOBALS['cfg']['sys_language'] = 'it';
        $translation = new \translation();
        $actual = $translation->get_lang();

        $this->assertEquals('it', $actual);
    }

    /**
     * @covers translation::get_lang
     */
    public function testGetLanguage_FromSession() {
        $_SESSION['language'] = 'nl';
        $translation = new \translation();
        $actual = $translation->get_lang();

        $this->assertEquals('nl', $actual);
    }

    /**
     * @covers translation::get_lang
     */
    public function testGetLanguage_FromGET() {
        $_GET['language'] = 'es';
        $translation = new \translation();
        $actual = $translation->get_lang();

        $this->assertEquals('es', $actual);
    }

    /**
     * @covers translation::get_lang
     */
    public function testGetLanguage_Default() {
        $translation = new \translation();
        $actual = $translation->get_lang();

        $this->assertEquals('de', $actual);
    }

    /**
     * @covers translation::get_lang
     */
    public function testGetLanguage_FromPOST() {
        $_POST['language'] = 'fr';
        $translation = new \translation();
        $actual = $translation->get_lang();

        $this->assertEquals('fr', $actual);
    }

    public function dataProviderGetTranslationFilename() {
        return array(
            array('DB', 'inc/language/DB_translation.xml'),
            array('System', 'inc/language/System_translation.xml'),
            array('', 'modules//mod_settings/translation.xml'),
            array('about', 'modules/about/mod_settings/translation.xml'),
            array('seating', 'modules/seating/mod_settings/translation.xml')
        );
    }

    /**
     * @dataProvider dataProviderGetTranslationFilename
     * @covers translation::get_trans_filename
     */
    public function testGetTranslationFilename($module, $expected) {
        $translation = new \translation();
        $actual = $translation->get_trans_filename($module);

        $this->assertEquals($expected, $actual);
    }
}