<?php

namespace LanSuite\Tests;

use PHPUnit\Framework\TestCase;

class TS3ServerTest extends TestCase
{
    /**
     * @covers \LanSuite\Module\Teamspeak3\TS3Server::getURI
     */
    public function testGetURI()
    {
        //initialize settings
        $settings = [
            'serveraddress' => 'localhost',
            'serverpassword' => 'testpw',
            'serverudpport' = > 100,
            'serverqueryport' => 200,
            'serverqueryuser' => 'svqryusr',
            'serverquerypassword' => 'srvqrypw',
            'tournamentchannel' => 'tournamentchannel'
        ];

        $ts3Obj = New \LanSuite\Module\Teamspeak3\TS3Server($settings);
        $expectedUri = 'serverquery://srvqryusr:srvqrypw@localhost:200/?server_port=9987';
        $uri = $ts3Obj->getURL();
        $this->assertEquals($expectedUri, $uri);

    }


}