<?php

namespace LanSuite\Tests;

use PHPUnit\Framework\TestCase;

class MasterformTest extends TestCase
{
    /**
     * @covers \masterform::IncrementNumber
     */
    public function testIncrementNumber()
    {
        $mf = new \masterform();

        $this->assertEquals(1, $mf->GetNumber());
        $mf->IncrementNumber();
        $this->assertEquals(2, $mf->GetNumber());
    }

    /**
     * @covers \masterform::DecrementNumber
     */
    public function testDecrementNumber()
    {
        $mf = new \masterform();

        $this->assertEquals(1, $mf->GetNumber());
        $mf->DecrementNumber();
        $this->assertEquals(0, $mf->GetNumber());
    }

    /**
     * @covers \masterform::IncrementNumber
     * @covers \masterform::DecrementNumber
     */
    public function testIncrementAndDecrementNumber()
    {
        $mf = new \masterform();

        $mf->IncrementNumber();
        $mf->IncrementNumber();
        $mf->IncrementNumber();
        $this->assertEquals(4, $mf->GetNumber());

        $mf->DecrementNumber();
        $mf->DecrementNumber();
        $this->assertEquals(2, $mf->GetNumber());
    }
}