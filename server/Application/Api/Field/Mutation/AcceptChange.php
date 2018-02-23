<?php

declare(strict_types=1);

namespace Application\Api\Field\Mutation;

use Application\Api\Field\FieldInterface;
use Application\Api\Helper;
use Application\Model\Card;
use Application\Model\Change;
use GraphQL\Type\Definition\Type;

class AcceptChange implements FieldInterface
{
    public static function build(): array
    {
        return [
            'name' => 'acceptChange',
            'type' => _types()->get(Card::class),
            'description' => 'Accept the change and return the modified Image, unless if it has been deleted',
            'args' => [
                'id' => Type::nonNull(_types()->getId(Change::class)),
            ],
            'resolve' => function ($root, array $args): ?Card {
//                Helper::throwIfDenied('change', 'update');

                /** @var Change $change */
                $change = $args['id']->getEntity();

                $image = null;
                switch ($change->getType()) {
                    case Change::TYPE_CREATE:
                        $image = new Card();
                        _em()->persist($image);
                        $change->getSuggestion()->copyInto($image);
                        _em()->remove($change->getSuggestion());

                        break;
                    case Change::TYPE_UPDATE:
                        $image = $change->getOriginal();
                        $change->getSuggestion()->copyInto($image);
                        _em()->remove($change->getSuggestion());

                        break;
                    case Change::TYPE_DELETE:
                        _em()->remove($change->getOriginal());

                        break;
                    default:
                        throw new \Exception('Unsupported change type: ' . $change->getType());
                }

                _em()->remove($change);
                _em()->flush();

                return $image;
            },
        ];
    }
}
