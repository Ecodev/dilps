<?php

declare(strict_types=1);

use Application\Model\Image;
use Zend\Diactoros\UploadedFile;

return [
    [
        'query' => 'mutation ($inputImage: ImageInput! $file: Upload!) {
            createImage(file: $file input: $inputImage) {
                name
                fileSize
                width
                height
            }
        }',
        'variables' => [
            // Fake a a file uploaded with incorrect data, to check if we trust them (we should not)
            'file' => new UploadedFile('data/images/dw4jV3zYSPsqE2CB8BcP8ABD0.jpg', 999, UPLOAD_ERR_OK, 'image.jpg', 'text/plain'),
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
    ],
    [
        'data' => [
            'createImage' => [
                'name' => 'test name',
                'fileSize' => 90188,
                'width' => 960,
                'height' => 425,
            ],
        ],
    ],
];
