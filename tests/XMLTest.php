<?php

namespace LanSuite\Tests;

use PHPUnit\Framework\TestCase;

class XMLTest extends TestCase
{

    /**
     * @covers xml::get_tag_content
     */
    public function testGetTagContent()
    {
        $tagToGrab = 'name';
        $input = '<?xml version="1.0" encoding="UTF-8"?>
<design>
	<name>simple</name>
	<description>Schlichtes Design, mit wenig Grafiken für optimiertes Laden</description>
	<version>1.0</version>
	<author>Jochen Jung</author>
	<contact>knox(at)orgapage.de</contact>
	<website>www.orgapage.de</website>
	<comments></comments>
</design>';

        $xml = new \LanSuite\XML();
        $actual = $xml->get_tag_content($tagToGrab, $input);

        $this->assertEquals('simple', $actual);
    }

    /**
     * @covers xml::write_tag
     */
    public function testWriteTag()
    {
        $GLOBALS['func'] = new \LanSuite\Func();

        $content = 'Super news. Now now now.';
        $expected = "\t\t\t<title>$content</title>\r\n";

        $xml = new \LanSuite\XML();
        $actual = $xml->write_tag('title', $content, 3);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers xml::write_master_tag
     */
    public function testWriteMasterTag()
    {
        $content = "\t\t\t<title>This is my news</title>
			<description>And this is the story. You won't believe it!</description>
			<author>ADMIN ADMIN (ADMIN)</author>
			<pubDate>Sat, 17 Mar 2018 19:45:11 +0000</pubDate>
			<link>http://localhost:80/index.php?mod=news&action=comment&newsid=4</link>";

        $expected = "\t\t<item>\r
			<title>This is my news</title>
			<description>And this is the story. You won't believe it!</description>
			<author>ADMIN ADMIN (ADMIN)</author>
			<pubDate>Sat, 17 Mar 2018 19:45:11 +0000</pubDate>
			<link>http://localhost:80/index.php?mod=news&action=comment&newsid=4</link>\t\t</item>\r\n";

        $xml = new \LanSuite\XML();
        $actual = $xml->write_master_tag("item", $content, 2);

        $this->assertEquals($expected, $actual);
    }

    public function dataProviderConvertInputString()
    {
        return array(
            array('', ''),
            array('This is "Sparta" with double quotes', 'This is Sparta with double quotes'),
            array('This is \\slash land', 'This is slash land'),
            array("This is 'Sparta' with single quotes", 'This is Sparta with single quotes'),
            array('"All" To\'get\\her', 'All Together'),
        );
    }

    /**
     * @dataProvider dataProviderConvertInputString
     * @covers xml::convertinputstr
     */
    public function testConvertInputString($string, $expected)
    {
        $xml = new \LanSuite\XML();
        $actual = $xml->convertinputstr($string);

        $this->assertEquals($expected, $actual);
    }
}
