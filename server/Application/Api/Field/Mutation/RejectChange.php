<?php

declare(strict_types=1);

namespace Application\Api\Field\Mutation;

use Application\Api\Exception;
use Application\Api\Field\FieldInterface;
use Application\Api\Helper;
use Application\Model\Change;
use GraphQL\Type\Definition\Type;

class RejectChange implements FieldInterface
{
    public static function build(): array
    {
        return [
            'name' => 'rejectChange',
            'type' => _types()->get(Change::class),
            'description' => 'Reject the change',
            'args' => [
                'id' => Type::nonNull(_types()->getId(Change::class)),
                'response' => Type::nonNull(Type::string()),
            ],
            'resolve' => function ($root, array $args): Change {
//                Helper::throwIfDenied('change', 'update');

                /** @var Change $change */
                $change = $args['id']->getEntity();

                if ($change->getStatus() !== Change::STATUS_NEW) {
                    throw new Exception('Only a change that has not been accepted or rejected can be rejected');
                }

                $change->setStatus(Change::STATUS_REJECTED);
                $change->setResponse($args['response']);

                _em()->flush();

                return $change;
            },
        ];
    }
}
