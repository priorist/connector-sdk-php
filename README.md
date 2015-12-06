# Connector SDK for PHP

The official PHP library for Connector’s HTTP-based Stream API makes it easy for
developers to access and integrate their stream contents.

## Requirements

* PHP 5.3+
* PHP [cURL extension](http://php.net/manual/en/curl.installation.php) with SSL
  enabled (it's usually built-in).

## Setup

If you’re using [Composer](http://getcomposer.org/) for your project’s
dependencies, add the following to your _composer.json_:

    "require": {
        "priorist/connector-sdk": "~1.0"
    }

## Usage

This is an example how to fetch and print all elements of a stream.

    use Priorist\Connector\Connector;
    use Priorist\Connector\Stream;

    try {
        $stream = Connector::fetchStream('your-stream-id');
        printStream($stream);
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage() . "\n";
    }

    function printStream(Stream $stream)
    {
        if (!$stream->hasElements()) {
            exit("Stream is empty.\n");
        }

        printf(
            "Stream `%s` has %u elements.\n\n",
            $stream->title,
            $stream->count()
        );

        foreach ($stream as $element) {
            printf(
                "%s on %s:\n%s\n\n",
                $element->author->display_name,
                $element->created->format('F jS'),
                $element->text
            );
        }
    }
