<?php

declare(strict_types=1);

namespace AppTest\Api;

use Application\Api\Server;
use ApplicationTest\Traits\TestWithTransaction;
use PHPUnit\Framework\TestCase;
use Zend\Diactoros\ServerRequest;

class ServerTest extends TestCase
{
    use TestWithTransaction;

    /**
     * @dataProvider providerQuery
     *
     * @param null|string $user
     * @param ServerRequest $request
     * @param array $expected
     */
    public function testQuery(?string $user, ServerRequest $request, array $expected): void
    {
        // Use this flag to easily debug API test issues
        $debug = false;

        // Configure server
        $server = new Server($debug);

        // Execute query
        $actual = $server->execute($request)->toArray();

        if ($debug) {
            ve($actual);
            unset($actual['errors'][0]['trace']);
        }

        self::assertEquals($expected, $actual);
    }

    public function providerQuery(): array
    {
        $data = [];
        foreach (glob('tests/data/query/*.php') as $file) {
            $name = str_replace('-', ' ', basename($file, '.php'));
            $user = preg_replace('/\d/', '', explode(' ', $name)[0]);
            if ($user === 'anonymous') {
                $user = null;
            }

            $args = require $file;

            // Convert arg into request
            $request = new ServerRequest();
            $args[0] = $request
                ->withMethod('POST')
                ->withHeader('content-type', ['application/json'])
                ->withParsedBody($args[0]);

            array_unshift($args, $user);
            $data[$name] = $args;
        }

        return $data;
    }
}