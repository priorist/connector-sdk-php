<?php

namespace Priorist\Connector;

/**
 * Entrypoint to Connector’s API.
 *
 * Requires PHP >= 5.2.0 with cURL
 */
class Client
{
    const API_VERSION = '1';
    const API_URL     = 'https://%s.stream.connector.io/%s';


    /**
     * Fetches all elements of the stream and returns them as an ArrayObject.
     *
     * @param string $streamId ID of the stream to access items from
     *
     * @return ArrayObject Elements of the stream (e.g. posts or users).
     */
    public static function fetchStream($streamId)
    {
        return self::fetchJson($streamId);
        //return new Stream(self::fetchJson($streamId));
    }


    /**
     * Connects to API and fetches stream contents as JSON.
     *
     * @param string $streamId ID of the stream to access items from
     *
     * @throws RuntimeException if the stream is not accessible.
     * @throws InvalidArgumentException if the stream id is invalid.
     *
     * @return string JSON encoded stream contents
     */
    protected static function fetchJson($streamId)
    {
        $ch = curl_init(sprintf(self::API_URL, self::API_VERSION, urlencode($streamId)));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $json = curl_exec($ch);
        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($httpStatus == 200 && $json !== false) {
            return $json;
        }

        switch ($httpStatus) {
            case 404: throw new \InvalidArgumentException('Stream not found.');
            case 500: throw new \UnexpectedValueException('Interal API error.');
            default : throw new \RuntimeException('Could not connect to API. HTTP status: ' . $httpStatus);
        }

        return null;
    }
}
