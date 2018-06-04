<?php

namespace LanSuite\Tests;

use PHPUnit\Framework\TestCase;

class MasterformTest extends TestCase
{
    /**
     * @covers \LanSuite\MasterForm::IncrementNumber
     */
    public function testIncrementNumber()
    {
        $mf = new \LanSuite\MasterForm();

        $this->assertEquals(1, $mf->GetNumber());
        $mf->IncrementNumber();
        $this->assertEquals(2, $mf->GetNumber());
    }

    /**
     * @covers \LanSuite\MasterForm::DecrementNumber
     */
    public function testDecrementNumber()
    {
        $mf = new \LanSuite\MasterForm();

        $this->assertEquals(1, $mf->GetNumber());
        $mf->DecrementNumber();
        $this->assertEquals(0, $mf->GetNumber());
    }

    /**
     * @covers \LanSuite\MasterForm::IncrementNumber
     * @covers \LanSuite\MasterForm::DecrementNumber
     */
    public function testIncrementAndDecrementNumber()
    {
        $mf = new \LanSuite\MasterForm();

        $mf->IncrementNumber();
        $mf->IncrementNumber();
        $mf->IncrementNumber();
        $this->assertEquals(4, $mf->GetNumber());

        $mf->DecrementNumber();
        $mf->DecrementNumber();
        $this->assertEquals(2, $mf->GetNumber());
    }
}
