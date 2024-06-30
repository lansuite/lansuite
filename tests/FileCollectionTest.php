<?php

namespace LanSuite\Tests;

use LanSuite\FileCollection;
use PHPUnit\Framework\TestCase;

class FileCollectionTest extends TestCase
{

    public function setUp() :void
    {
        if (!defined('ROOT_DIRECTORY'))
        {
            define('ROOT_DIRECTORY', '/foo/bar');
        }
    }

    /**
     * @covers FileCollection::__construct
     */
    public function testConstruct() :void
    {
        $fs = new \LanSuite\FileCollection();
        $this->assertEquals('/foo/bar/', $fs->getBasePath());
    }

    /**
     * @covers FileCollection::setRelativePath
     * @covers FileCollection::getRelativePath
     * @covers FileCollection::getCurrentPath
     */
    public function testRelativePath() :void
    {
        $fs = new \LanSuite\FileCollection();
        $fs->setRelativePath('foo');
        $this->assertEquals('/foo/bar/foo/', $fs->getCurrentPath());
        $this->assertEquals('foo/', $fs->getRelativePath());

        $fs->setRelativePath('/foo');
        $this->assertEquals('/foo/bar/foo/', $fs->getCurrentPath());
        $this->assertEquals('foo/', $fs->getRelativePath());

        $fs->setRelativePath('foo/');
        $this->assertEquals('/foo/bar/foo/', $fs->getCurrentPath());
        $this->assertEquals('foo/', $fs->getRelativePath());

        $fs->setRelativePath('foo/../bar');
        $this->assertEquals('/foo/bar/bar/', $fs->getCurrentPath());
        $this->assertEquals('bar/', $fs->getRelativePath());

        $fs->setRelativePath('foo/../bar/');
        $this->assertEquals('/foo/bar/bar/', $fs->getCurrentPath());
        $this->assertEquals('bar/', $fs->getRelativePath());

        $this->assertEquals(true, $fs->setRelativePath('foo'));
        $this->assertEquals(false, $fs->setRelativePath('..'));
        $this->assertEquals(false, $fs->setRelativePath('/..'));
        $this->assertEquals(false, $fs->setRelativePath('../'));

    }

    /**
     * @covers FileCollection::getFullPath
     */
    public function testgetFullPath()
    {
        $fc = new \LanSuite\FileCollection();
        $this->assertEquals('/foo/bar/test', $fc->getFullPath('test'));
        $this->assertEquals('/foo/bar/test', $fc->getFullPath('/test'));
        //deactivating these two, as not correctly evaluated due to internal use of is_dir()
        //$this->assertEquals('/foo/bar/test/',$fc->getFullPath('test/'));
        //$this->assertEquals('/foo/bar/test/',$fc->getFullPath('/test/'));


        $this->expectException(\InvalidArgumentException::class);
        $fc->getFullPath('../test');
    }

    /**
     * @covers FileCollection:constructCollection
     */
    public function testConstructFromFileList()
    {
        $pathArray = ['/foo/bar/foo/', '/foo/bar/foo/foo2', '/foo/bar/foo/foo3/'];
        $fc = \LanSuite\FileCollection::constructCollection($pathArray);
        $this->assertEquals('foo/', $fc->getRelativePath());
    }

    /**
     * @covers FileCollection:setWhitelist
     * @covers FileCollection:getWhitelist
     */
    public function testWhitelistSetGet()
    {
        $fc = new \LanSuite\FileCollection();
        //check if initialized correctly
        $this->assertEquals([], $fc->getWhitelist());
        //set and readback
        $fc->setWhitelist(['test','test2']);
        $this->assertEquals(['test','test2'], $fc->getWhitelist());
    }

    /**
     * @covers FileCollection:setBlackList
     * @covers FileCollection:getBlackList
     */
    public function testBlacklistSetGet()
    {
        $fc = new \LanSuite\FileCollection();
        //check if initialized correctly
        $this->assertEquals(FileCollection::SECURITY_BLACKLIST, $fc->getBlacklist());
        //set and readback
        $fc->setBlacklist(['test','test2']);
        $this->assertEquals(['test','test2'], $fc->getBlacklist());
    }

    /**
     * @covers FileCollection:checkLists
     */
    public function testCheckLists()
    {
        $fc = new \LanSuite\FileCollection();
        //.htaccess and user.ini should be blocked by default, let's test that
        $this->assertEquals(false, $fc->checkLists('.htaccess'));
        $this->assertEquals(false, $fc->checkLists('user.ini'));
        $this->assertEquals(false, $fc->checkLists('test.php'));
        //clean blacklist and try again
        $fc->setBlacklist([]);
        $this->assertEquals(true, $fc->checkLists('.htaccess'));
        $this->assertEquals(true, $fc->checkLists('user.ini'));
        $this->assertEquals(true, $fc->checkLists('test.php'));

        //now test whitelist matching
        $fc->setWhitelist(['/test\.php/']);
        $this->assertEquals(true, $fc->checkLists('test.php'));
        $this->assertEquals(false, $fc->checkLists('testfail.php'));
        $this->assertEquals(true, $fc->checkLists('goodtest.php5'));

        //test image whitelist constant
        $fc->setWhitelist(FileCollection::IMAGE_WHITELIST);
        $this->assertEquals(false, $fc->checkLists('test.php'));
        $this->assertEquals(false, $fc->checkLists('test.bmp.php'));
        $this->assertEquals(false, $fc->checkLists('test.jpg.php'));
        $this->assertEquals(false, $fc->checkLists('test.docx'));
        $this->assertEquals(true, $fc->checkLists('test.jpg'));
        $this->assertEquals(true, $fc->checkLists('test.bmp'));
        $this->assertEquals(true, $fc->checkLists('test.jpeg'));
        $this->assertEquals(true, $fc->checkLists('test.webp'));
        $this->assertEquals(true, $fc->checkLists('test.png'));
        $this->assertEquals(true, $fc->checkLists('test.ico'));
    }


}
