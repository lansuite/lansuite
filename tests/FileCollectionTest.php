<?php

namespace LanSuite\Tests;

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
        $fs = new \LanSuite\FileCollection();
        $this->assertEquals('/foo/bar/test',$fs->getFullPath('test'));
        $this->assertEquals('/foo/bar/test',$fs->getFullPath('/test'));
        //deactivating these two, as not correctly evaluated due to internal use of is_dir()
        //$this->assertEquals('/foo/bar/test/',$fs->getFullPath('test/'));    
        //$this->assertEquals('/foo/bar/test/',$fs->getFullPath('/test/'));

    
        $this->expectException(\InvalidArgumentException::class);           
        $fs->getFullPath('../test');
    }
 
}
