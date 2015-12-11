<?php

namespace Priorist\Connector;

/**
 * Entrypoint to Connector’s API.
 *
 * Requires PHP >= 5.2.0 with cURL
 */
class Client implements ClientInterface
{
    const API_VERSION = 1;
    const API_URL     = 'https://stream-v%u.connector.io/%s';


    /**
     * Fetches all elements of the stream and returns them as an ArrayObject.
     *
     * @param string $streamId ID of the stream to access items from
     *
     * @return ArrayObject Elements of the stream (e.g. posts or users).
     */
    public static function fetchStream($streamId)
    {
        if (!is_string($streamId) || trim($streamId) == '') {
            throw new \InvalidArgumentException('Please provide a valid stream ID.');
        }

        $streamJson = static::fetchJson(static::getUrlForStreamId(trim($streamId)));

        return new Stream($streamJson);
    }


    /**
     * Builds an API URL for a given stream ID. No validation of stream ID
     * is performed here.
     *
     * @param string $streamId ID of the stream to access items from
     *
     * @return string URL to access stream contents
     */
    protected static function getUrlForStreamId($streamId)
    {
        return sprintf(static::API_URL, static::API_VERSION, urlencode($streamId));
    }


    /**
     * Connects to API and fetches stream contents as JSON.
     *
     * @param string $streamId ID of the stream to access items from
     *
     * @throws RuntimeException if the stream is not accessible.
     * @throws UnexpectedValueException if there was an error within the API.
     * @throws InvalidArgumentException if the stream ID is invalid.
     *
     * @return string Raw JSON of the retrieved stream
     */
    protected static function fetchJson($url)
    {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($httpStatus == 200 && $response !== false) {
            return $response;
        }

        switch ($httpStatus) {
            case 404: throw new \InvalidArgumentException('Stream not found.');
            case 500: throw new \UnexpectedValueException('Internal API error: ' . $response);
            default : throw new \RuntimeException('Could not connect to API. HTTP status: ' . $httpStatus);
        }

        return null;
    }
}
