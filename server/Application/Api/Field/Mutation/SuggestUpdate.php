<?php

declare(strict_types=1);

namespace Application\Api\Field\Mutation;

use Application\Api\Exception;
use Application\Api\Field\FieldInterface;
use Application\Api\Helper;
use Application\Model\Change;
use Application\Model\Image;
use GraphQL\Type\Definition\Type;

class SuggestUpdate implements FieldInterface
{
    public static function build(): array
    {
        return [
            'name' => 'suggestUpdate',
            'type' => Type::nonNull(_types()->get(Change::class)),
            'description' => 'Suggest the update of an existing image',
            'args' => [
                'id' => Type::nonNull(_types()->getId(Image::class)),
                'request' => Type::nonNull(Type::string()),
            ],
            'resolve' => function ($root, array $args): Change {
//                Helper::throwIfDenied('change', 'create');

                $suggestion = $args['id']->getEntity();
                $original = $suggestion->getOriginal();

                if (!$original) {
                    throw new Exception('An suggestion must have an original defined');
                }

                $change = _em()->getRepository(Change::class)->getOrCreate(Change::TYPE_UPDATE, $suggestion, $args['request']);

                if (!$change->getId()) {
                    _em()->flush();
                }

                return $change;
            },
        ];
    }
}
