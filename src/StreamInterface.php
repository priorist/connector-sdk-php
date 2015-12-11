<?php

namespace Priorist\Connector;

interface StreamInterface extends \Iterator, \Serializable, \Countable
{
    public function fromArray(array $data);
}
