<?php

declare(strict_types=1);

namespace Application\Api\Field\Mutation;

use Application\Api\Field\FieldInterface;
use Application\Api\Helper;
use Application\Model\Change;
use Application\Model\Image;
use GraphQL\Type\Definition\Type;

class SuggestDeletion implements FieldInterface
{
    public static function build(): array
    {
        return [
            'name' => 'suggestDeletion',
            'type' => Type::nonNull(_types()->get(Change::class)),
            'description' => 'Suggest the deletion of an existing image',
            'args' => [
                'id' => Type::nonNull(_types()->getId(Image::class)),
                'request' => Type::nonNull(Type::string()),
            ],
            'resolve' => function ($root, array $args): Change {
//                Helper::throwIfDenied('change', 'create');

                $original = $args['id']->getEntity();

                $change = _em()->getRepository(Change::class)->getOrCreate(Change::TYPE_DELETE, $original, $args['request']);

                if (!$change->getId()) {
                    _em()->flush();
                }

                return $change;
            },
        ];
    }
}
