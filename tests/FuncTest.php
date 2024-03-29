<?php

namespace LanSuite\Tests;

use PHPUnit\Framework\TestCase;

class FuncTest extends TestCase
{
    /**
     * @covers \LanSuite\Func::str2time
     */
    public function testStr2time()
    {
        $func = new \LanSuite\Func();

        // Table for unit test values:
        // | Argument | Expected |
        $unitTestValues = [
            ['2024-03-20 00:10:00', 1710893400],
            ['2024-3-20 0:10:0', 1710893400],
            ['2024-3-20 0:10:00', 1710893400],
            ['2024-03-01 1:10:0', 1709255400],
            ['2024-02-29 13:10:05', 1709212205],
            ['2024-03-26 23:59:59', 1711497599],
            ['2025-1-1 0:0:0', 1735689600],
            ['2025-12-31 21:22:23', 1767216143],
            ['2023-11-1 13:0:00', 1698843600],
            ['2024-3-16 10:0:00', 1710583200],
        ];

        foreach ($unitTestValues as $testTableEntry) {
            $argument = $testTableEntry[0];
            $expected = $testTableEntry[1];
            $actualResult = $func->str2time($argument);
            $message = 'For input value "' . $argument . '", we expected "' . $expected . '" (' . date('Y-m-d H:i:s', $expected) . '), got "' . $actualResult . '" (' . date('Y-m-d H:i:s', $actualResult) . ')';
            
            $this->assertEquals($expected, $actualResult, $message);
        }
    }
}
