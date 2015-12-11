<?php

namespace Priorist\Connctor\Test;

use Priorist\Connector\Client;
use Priorist\Connector\StreamInterface;

/**
 * @covers Priorist\Connector\Client
 */
class ClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testForInvalidStreamId()
    {
        Client::fetchStream(1);
    }


    /**
     * @expectedException InvalidArgumentException
     */
    public function testForInvalidStreamIdFormat()
    {
        Client::fetchStream(' ');
    }


    /**
     * @expectedException InvalidArgumentException
     */
    public function testForStreamNotFound()
    {
        Client::fetchStream('x');
    }


    public function testForValidStream()
    {
        $stream = Client::fetchStream($GLOBALS['validStreamId']);

        $this->assertInstanceOf(StreamInterface::class, $stream);
    }
}
