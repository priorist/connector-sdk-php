<?php

namespace Priorist\Connector;

/**
 * Represents a stream and provides access to its properties and
 * elements.
 *
 * Requires PHP >= 5.1.0 and PECL json >= 1.2.0.
 */
class Stream implements \Iterator, \Serializable, \Countable
{
    protected $properties;
    protected $elements;


    /**
     * @param string $json JSON string to populate stream
     */
    public function __construct($json)
    {
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
        if (!property_exists($this->properties, $property)) {
            throw new \InvalidArgumentException('Property `' . $property . '` does not exist.');
        }

        switch ($property) {
            case 'elements':
                return new \ArrayObject($this->elements);

            case 'created':
                return new \DateTime($this->properties->$property);

            default:
                return $this->properties->$property;
        }
    }


    /**
     * Checks if the stream contains any elements.
     *
     * @return boolean True if strem has at least one element
     */
    public function hasElements()
    {
        return $this->count() != 0;
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
        return $this->json;
    }


    /**
     * Populates the stream and its elements based on a JSON string.
     *
     * @param string $json JSON string to populate stream
     *
     * @throws UnexpectedValueException if JSON unserializes to unexpected value.
     * @throws InvalidArgumentException if stream data is invalid.
     */
    public function unserialize($json)
    {
        $this->properties = json_decode($json);
        $this->json = $json;

        if (!is_object($this->properties)) {
            throw new \UnexpectedValueException('Object expected from JSON source.');
        }

        if (!property_exists($this->properties, 'elements')) {
            throw new \InvalidArgumentException('Invalid stream data.');
        }

        // Prevent default timezone warning if not set by user
        date_default_timezone_set(@date_default_timezone_get());

        $this->elements = &$this->properties->elements;

        // Parse single elements
        foreach ($this->elements as $element) {
            $element->created = new \DateTime($element->created);
        }
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
