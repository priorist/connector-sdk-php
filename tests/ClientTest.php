<?php

namespace Priorist\Connctor\Test;

use Priorist\Connector\Client;

/**
 * @covers Priorist\Connector\Client
 */
class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function testCanFetchStream()
    {
        $this->assertEquals(Client::fetchStream('test'), 1);
    }
}
