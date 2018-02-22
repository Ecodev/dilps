<?php

declare(strict_types=1);

namespace Application\Api\Field\Mutation;

use Application\Api\Exception;
use Application\Api\Field\FieldInterface;
use Application\Api\Helper;
use Application\Model\Change;
use Application\Model\Image;
use GraphQL\Type\Definition\Type;

class AcceptChange implements FieldInterface
{
    public static function build(): array
    {
        return [
            'name' => 'acceptChange',
            'type' => _types()->get(Image::class),
            'description' => 'Accept the change and return the modified Image, unless if it has been deleted',
            'args' => [
                'id' => Type::nonNull(_types()->getId(Change::class)),
                'response' => Type::nonNull(Type::string()),
            ],
            'resolve' => function ($root, array $args): ?Image {
//                Helper::throwIfDenied('change', 'update');

                /** @var Change $change */
                $change = $args['id']->getEntity();

                if ($change->getStatus() !== Change::STATUS_NEW) {
                    throw new Exception('Only a change that has not been accepted or rejected can be accepted');
                }

                $change->setStatus(Change::STATUS_ACCEPTED);
                $change->setResponse($args['response']);

                $image = null;
                switch ($change->getType()) {
                    case Change::TYPE_CREATE:
                        $image = new Image();
                        _em()->persist($image);
                        $change->getSuggestion()->copyInto($image);

                        break;
                    case Change::TYPE_UPDATE:
                        $image = $change->getOriginal();
                        $change->getSuggestion()->copyInto($image);

                        break;
                    case Change::TYPE_DELETE:
                        _em()->remove($change->getOriginal());

                        break;
                    default:
                        throw new \Exception('Unsupported change type: ' . $change->getType());
                }

                _em()->flush();

                return $image;
            },
        ];
    }
}
