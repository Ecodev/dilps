#! /usr/bin/env php
<?php

/**
 * A script to run a GraphQL query and print result as JSON
 * This is useful to introspect the entire schema for JS unit tests
 */
use Application\Api\Schema;
use GraphQL\Server\StandardServer;
use Zend\Diactoros\ServerRequest;

require_once __DIR__ . '/../htdocs/index.php';

$server = new StandardServer([
    'schema' => new Schema(),
    'queryBatching' => true,
]);
$request = new ServerRequest();
$request = $request->withMethod('POST')->withHeader('content-type', 'application/json')->withParsedBody([
        'query' => $argv[1] ?? '',
        'variables' => json_decode($argv[2] ?? '{}', true),
    ]);

$result = $server->executePsrRequest($request);
echo json_encode($result, JSON_PRETTY_PRINT) . PHP_EOL;
