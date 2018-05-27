<?php

namespace LanSuite\Tests;

use PHPUnit\Framework\TestCase;

class SecTest extends TestCase
{
    /**
     * @covers \LanSuite\Security::check_blacklist
     */
    public function testCheckBlacklist_InGlobalBlacklist()
    {
        $expected = 'Deine IP wird von LanSuite geblockt. Melde dich bitte bei den Administratoren';

        $_SERVER['REMOTE_ADDR'] = '1.2.3.4';
        $GLOBALS['cfg']['ip_blacklist'] = '1.2.3.4';

        $sec = new \LanSuite\Security();
        $actual = $sec->check_blacklist();
        $this->assertEquals($expected, $actual);

        $GLOBALS['cfg']['ip_blacklist'] = '192.168.66.44,1.2.3.4';
        $actual = $sec->check_blacklist();
        $this->assertEquals($expected, $actual);

        $GLOBALS['cfg']['ip_blacklist'] = '192.168.66.44,1.2.3.4,10.87.65.23';
        $actual = $sec->check_blacklist();
        $this->assertEquals($expected, $actual);
    }
}
