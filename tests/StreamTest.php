<?php

namespace Priorist\Connctor\Test;

use Priorist\Connector\Client;
use Priorist\Connector\StreamInterface;
use Priorist\Connector\ElementInterface;

/**
 * @covers Priorist\Connector\Stream
 */
class StreamTest extends \PHPUnit_Framework_TestCase
{
    public function testForValidStream()
    {
        $stream = Client::fetchStream($_SERVER['validStreamId']);

        $this->assertInstanceOf(StreamInterface::class, $stream);

        return $stream;
    }


    /**
     * @depends testForValidStream
     */
    public function testForValidProperties(StreamInterface $stream)
    {
        $this->assertInternalType('string', $stream->alias);
        $this->assertInternalType('integer', $stream->amount);
        $this->assertGreaterThanOrEqual(0, $stream->amount, 'Element amount is >= 0');
        $this->assertEquals($stream->amount, count($stream), 'count($stream) is equal to $stream->amount');
        $this->assertEquals($stream->amount > 0, $stream->hasElements(), '$stream->hasElements() is true, if elements exist');
    }


    /**
     * @depends testForValidStream
     */
    public function testForValidElements(StreamInterface $stream)
    {
        $this->assertInstanceOf(\ArrayObject::class, $stream->elements);
        $this->assertContainsOnlyInstancesOf(ElementInterface::class, $stream->elements);
    }
}
