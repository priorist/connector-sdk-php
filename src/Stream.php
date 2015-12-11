<?php

namespace Priorist\Connector;

/**
 * Represents a stream and provides access to its properties and
 * elements.
 *
 * Requires PHP >= 5.2.0 and PECL json >= 1.2.0
 */
class Stream implements StreamInterface
{
    protected $data;
    protected $elements;


    /**
     * @param string $json JSON string to populate stream
     */
    public function __construct($json)
    {
        // Prevent default timezone warning if not set by user
        date_default_timezone_set(@date_default_timezone_get());

        $this->unserialize($json);
    }


    /**
     * Magic methid to retreive a single property of the stream.
     *
     * @param string $property The name of the property to fetch
     *
     * @throws InvalidArgumentException if no property with the provided name exists.
     *
     * @return mixed Value of the requestet property
     */
    public function __get($property)
    {
        if (!isset($this->data[$property])) {
            throw new \InvalidArgumentException('Property `' . $property . '` does not exist.');
        }

        return $this->data[$property];
    }


    /**
     * Checks if the stream contains any elements.
     *
     * @return boolean True if strem has at least one element
     */
    public function hasElements()
    {
        return $this->count() > 0;
    }


    /**
     * Populates the stream with values from an array.
     *
     * @param array $data The array containing the stream data
     */
    public function fromArray(array $data)
    {
        $this->data = $data;

        foreach ($this->data as $name => &$value) {
            $value = $this->parseProperty($name, $value);
        }

        // Parse single elements
        foreach ($this->data['elements'] as &$element) {
            // TODO: Create and set Element object
        }

        $this->elements = &$this->data['elements'];
    }


    /**
     * Transforms stream properries, if need be
     *
     * @param string $name Name of the property
     * @param mixed $value Value of the property
     *
     * @return mixed Transformed value of property
     */
    protected function parseProperty($name, $value)
    {
        switch ($name) {
            case 'elements':
                return new \ArrayObject($value);

            case 'created':
                return new \DateTime($value);

            default:
                return $value;
        }
    }


    /**
     * Rewinds the element iterator to the first element.
     */
    public function rewind()
    {
        reset($this->elements);
    }


    /**
     * Returns the current element of the stream.
     *
     * @return stdClass The element
     */
    public function current()
    {
        return current($this->elements);
    }


    /**
     * Returns the index of the current element.
     *
     * @return int Index of current element
     */
    public function key()
    {
        return key($this->elements);
    }


    /**
     * Moves forward to the next element.
     */
    public function next()
    {
        next($this->elements);
    }


    /**
     * Checks if the current element position is valid.
     *
     * @return boolean True if current element position is valid
     */
    public function valid()
    {
        return key($this->elements) !== null;
    }


    /**
     * Returns the JSON representation of the stream and its elements.
     *
     * @return string The JSON encoded stream
     */
    public function serialize()
    {
        return json_encode($this->data);
    }


    /**
     * Populates the stream and its elements based on a JSON string.
     *
     * @param string $json JSON string to populate stream
     *
     * @throws UnexpectedValueException if JSON unserializes to unexpected value.
     * @throws InvalidArgumentException if JSON is not valid
     */
    public function unserialize($json)
    {
        $data = json_decode($json, true, 4, JSON_BIGINT_AS_STRING);

        if ($data === null) {
            throw new \InvalidArgumentException('Invalid JSON.');
        }

        if (!is_array($data)) {
            throw new \UnexpectedValueException('Array expected from JSON source.');
        }

        if (!isset($data['elements'])) {
            throw new \UnexpectedValueException('Invalid stream data.');
        }

        $this->fromArray($data);
    }


    /**
     * Returns the amount of elements in the stream.
     *
     * @return int Number of elements
     */
    public function count()
    {
        return $this->amount;
    }
}
