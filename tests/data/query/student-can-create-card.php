<?php

declare(strict_types=1);

use Application\Model\Card;
use Zend\Diactoros\UploadedFile;

return [
    [
        'query' => 'mutation ($inputCard: CardInput!) {
            createCard(input: $inputCard) {
                name
                fileSize
                width
                height
                artists {
                    name
                }
            }
        }',
        'variables' => [
            'inputCard' => [
                // Fake a a file uploaded with incorrect data, to check if we trust them (we should not)
                'file' => new UploadedFile('data/images/dw4jV3zYSPsqE2CB8BcP8ABD0.jpg', 999, UPLOAD_ERR_OK, 'card.jpg', 'text/plain'),
                'visibility' => Card::VISIBILITY_MEMBER,
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
                'street' => 'test street',
                'postcode' => 'test postcode',
                'locality' => 'test locality',
                'area' => 'test street',
                'artists' => [
                    'Test artist 3000',
                    'New artist',
                ],
            ],
        ],
    ],
    [
        'data' => [
            'createCard' => [
                'name' => 'test name',
                'fileSize' => 90188,
                'width' => 960,
                'height' => 425,
                'artists' => [
                    [
                        'name' => 'Test artist 3000',
                    ],
                    [
                        'name' => 'New artist',
                    ],
                ],
            ],
        ],
    ],
];
