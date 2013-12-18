# NNTP

Client for communicating with servers throught the Network News Transfer Protocol (NNTP) protocol.

[![Build Status](https://travis-ci.org/RobinvdVleuten/php-nntp.png?branch=master)](https://travis-ci.org/RobinvdVleuten/php-nntp)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/RobinvdVleuten/php-nntp/badges/quality-score.png?s=f75dede4b0dfd176b1448b72e9acc8345f132a52)](https://scrutinizer-ci.com/g/RobinvdVleuten/php-nntp/)
[![Code Coverage](https://scrutinizer-ci.com/g/RobinvdVleuten/php-nntp/badges/coverage.png?s=e60c63bee8c99a655f821051fee3b7be45ffbb3c)](https://scrutinizer-ci.com/g/RobinvdVleuten/php-nntp/)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/115c5524-7c3a-4463-a48c-2e21257f25b4/mini.png)](https://insight.sensiolabs.com/projects/115c5524-7c3a-4463-a48c-2e21257f25b4)

## Installation

The recommended way to install the library is [through composer](http://getcomposer.org).

```JSON
{
    "require": {
        "rvdv/nntp": "0.1.*@dev"
    }
}
```

## NNTP?

> NNTP specifies a protocol for the distribution, inquiry, retrieval,
> and posting of news articles using a reliable stream (such as TCP)
> server-client model. NNTP is designed so that news articles need only
> be stored on one (presumably central) host, and subscribers on other
> hosts attached to the LAN may read news articles using stream
> connections to the news host.

> -- RFC Abstract ([source](http://tools.ietf.org/html/rfc977))

## Usage

```php
<?php

require_once __DIR__.'/../vendor/autoload.php';

use Rvdv\Nntp\Connection\Connection;
use Rvdv\Nntp\Client;

$client = new Client();

$connection = new Connection();
$client->setConnection($connection);

$client->connect('news.php.net', 119);
$client->authenticate('username', 'password');

$command = $client->overviewFormat();
$overviewFormat = $command->getResult();

$command = $client->group('alt.binaries.moovee');
$group = $command->getResult();

$command = $client->overview($group['first'], $group['first'] + 100, $overviewFormat);
$articles = $command->getResult();

var_dump(count($articles));

// Send the QUIT command first before disconnecting.
$client->quit();

// Disconnect the established socket connection.
$client->disconnect();
```

## License

MIT, see LICENSE
