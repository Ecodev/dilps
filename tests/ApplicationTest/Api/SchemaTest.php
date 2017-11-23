<?php

declare(strict_types=1);

namespace AppTest\Api;

use Application\Api\Schema;
use GraphQL\Error\Debug;
use GraphQL\GraphQL;
use GraphQL\Server\OperationParams;
use GraphQL\Server\StandardServer;
use PHPUnit\Framework\TestCase;

class SchemaTest extends TestCase
{
    public function testSchemaIsValid(): void
    {
        $schema = new Schema();
        $schema->assertValid();

        self::assertTrue(true, 'schema passes validation');
    }

    /**
     * @dataProvider providerQuery
     *
     * @param null|string $user
     * @param string $query
     * @param array $variables
     * @param array $expected
     */
    public function testQuery(?string $user, string $query, array $variables, array $expected): void
    {
        // Use this flag to easily debug API test issues
        $debug = false;

        // Configure server
        GraphQL::setDefaultFieldResolver(new \GraphQL\Doctrine\DefaultFieldResolver());
        $schema = new Schema();
        $server = new StandardServer([
            'schema' => $schema,
            'debug' => $debug ? Debug::INCLUDE_DEBUG_MESSAGE | Debug::INCLUDE_TRACE : false,
        ]);

        // Execute query
        $params = OperationParams::create([
            'query' => $query,
            'variables' => $variables,
        ]);
        $actual = $server->executeRequest($params)->toArray();

        if ($debug) {
            ve($actual);
            unset($actual['errors'][0]['trace']);
        }

        $this->assertEquals($expected, $actual);
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
            array_unshift($args, $user);
            $data[$name] = $args;
        }

        return $data;
    }
}
