<?php

declare(strict_types=1);

namespace AppTest\Api;

use Application\Api\Schema;
use Application\Model\Image;
use ApplicationTest\Traits\TestWithTransaction;
use GraphQL\Error\Debug;
use GraphQL\Executor\ExecutionResult;
use GraphQL\GraphQL;
use GraphQL\Server\OperationParams;
use GraphQL\Server\StandardServer;
use PHPUnit\Framework\TestCase;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\UploadedFile;

class SchemaTest extends TestCase
{
    use TestWithTransaction;

    public function testSchemaIsValid(): void
    {
        $schema = new Schema();
        $schema->assertValid();

        self::assertTrue(true, 'schema passes validation');
    }

    public function testCanUploadImage(): void
    {
        // Fake a a file uploaded with incorrect data, to check if we trust them (we should not)
        $file = new UploadedFile('data/images/dw4jV3zYSPsqE2CB8BcP8ABD0.jpg', 999, UPLOAD_ERR_OK, 'image.jpg', 'text/plain');

        // Fake request that already has its file processed (in the correct place in variables)
        $request = new ServerRequest();
        $request = $request
            ->withMethod('POST')
            ->withHeader('content-type', ['application/json'])
            ->withParsedBody([
                'query' => 'mutation ($inputImage: ImageInput!
        $file: Upload!) {

        createImage(file: $file input: $inputImage) {
            name
            fileSize
            width
            height
        }
    }',
                'variables' => [
                    'file' => $file,
                    'inputImage' => [
                        'isPublic' => true,
                        'type' => Image::TYPE_DEFAULT,
                        'status' => Image::STATUS_NEW,
                        'dating' => 'test dating',
                        'addition' => 'test addition',
                        'expandedName' => 'test expandedName',
                        'material' => 'test material',
                        'technique' => 'test technique',
                        'techniqueAuthor' => 'test techniqueAuthor',
                        'format' => 'test format',
                        'literature' => 'test literature',
                        'page' => 'test page',
                        'figure' => 'test figure',
                        'table' => 'test table',
                        'isbn' => 'test isbn',
                        'comment' => 'test comment',
                        'rights' => 'test rights',
                        'muserisUrl' => 'test muserisUrl',
                        'muserisCote' => 'test muserisCote',
                        'name' => 'test name',
                    ],
                ],
            ]);

        // Use this flag to easily debug API test issues
        $debug = false;

        // Execute query
        $server = $this->createServer($debug);
        $actual = $server->executePsrRequest($request);

        $expected = [
            'data' => [
                'createImage' => [
                    'name' => 'test name',
                    'fileSize' => 90188,
                    'width' => 960,
                    'height' => 425,
                ],
            ],
        ];

        $this->assertResponse($expected, $actual, $debug);
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
        $server = $this->createServer($debug);

        // Execute query
        $params = OperationParams::create([
            'query' => $query,
            'variables' => $variables,
        ]);
        $actual = $server->executeRequest($params);

        $this->assertResponse($expected, $actual, $debug);
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

    private function createServer(bool $debug): StandardServer
    {
        GraphQL::setDefaultFieldResolver(new \GraphQL\Doctrine\DefaultFieldResolver());
        $schema = new Schema();
        $server = new StandardServer([
            'schema' => $schema,
            'debug' => $debug ? Debug::INCLUDE_DEBUG_MESSAGE | Debug::INCLUDE_TRACE : false,
        ]);

        return $server;
    }

    private function assertResponse(array $expected, ExecutionResult $actual, bool $debug): void
    {
        $actual = $actual->toArray();
        if ($debug) {
            ve($actual);
            unset($actual['errors'][0]['trace']);
        }

        self::assertEquals($expected, $actual);
    }
}
