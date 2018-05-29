<?php

declare(strict_types=1);

namespace Application\Api\Field;

use Application\Api\Enum\OrderType;
use Application\Api\Helper;
use Application\Api\Input\Filter\Filters;
use Application\Api\Input\PaginationInputType;
use Application\Model\AbstractModel;
use Application\Model\Card;
use Application\Model\Collection;
use GraphQL\Type\Definition\Type;
use ReflectionClass;

/**
 * Provide easy way to build standard fields to query and mutate objects
 */
abstract class Standard
{
    /**
     * Returns standard fields to query the object
     *
     * @param string $class
     *
     * @return array
     */
    public static function buildQuery(string $class): array
    {
        $reflect = new ReflectionClass($class);
        $name = lcfirst($reflect->getShortName());
        $shortName = $reflect->getShortName();
        $plural = self::makePlural($name);

        $listArgs = self::getListArguments($class, $name);
        $singleArgs = self::getSingleArguments($class);

        return [
            [
                'name' => $plural,
                'type' => _types()->get($shortName . 'Pagination'),
                'args' => $listArgs,
                'resolve' => function ($root, array $args) use ($class): array {
                    $queryArgs = [$args['filters'] ?? []];
                    $queryArgs[] = $args['sort'];
                    $queryArgs[] = $args['order'];

                    $query = _em()->getRepository($class)->getFindAllQuery(...$queryArgs);
                    $result = Helper::paginate($args['pagination'], $query);

                    return $result;
                },
            ],
            [
                'name' => $name,
                'type' => _types()->getOutput($class),
                'args' => $singleArgs,
                'resolve' => function ($root, array $args): ?AbstractModel {
                    $object = $args['id']->getEntity();

                    Helper::throwIfDenied($object, 'read');

                    return $object;
                },
            ],
        ];
    }

    /**
     * Returns standard fields to mutate the object
     *
     * @param string $class
     *
     * @return array
     */
    public static function buildMutation(string $class): array
    {
        $reflect = new ReflectionClass($class);
        $name = $reflect->getShortName();
        $plural = self::makePlural($name);
        $lowerName = lcfirst($name);

        return [
            [
                'name' => 'create' . $name,
                'type' => Type::nonNull(_types()->getOutput($class)),
                'description' => 'Create a new ' . $name,
                'args' => [
                    'input' => Type::nonNull(_types()->getInput($class)),
                ],
                'resolve' => function ($root, array $args) use ($class, $lowerName): AbstractModel {
                    // Check ACL
                    $object = new $class();
                    Helper::throwIfDenied($object, 'create');

                    // Do it
                    $input = $args['input'];
                    Helper::hydrate($object, $input);
                    _em()->persist($object);
                    _em()->flush();

                    return $object;
                },
            ],
            [
                'name' => 'update' . $name,
                'type' => Type::nonNull(_types()->getOutput($class)),
                'description' => 'Update an existing ' . $name,
                'args' => [
                    'id' => Type::nonNull(_types()->getId($class)),
                    'input' => Type::nonNull(_types()->getInput($class)),
                ],
                'resolve' => function ($root, array $args) use ($lowerName): AbstractModel {
                    $object = $args['id']->getEntity();

                    // Check ACL
                    Helper::throwIfDenied($object, 'update');

                    // Do it
                    $input = $args['input'];
                    Helper::hydrate($object, $input);

                    _em()->flush();

                    return $object;
                },
            ],
            [
                'name' => 'delete' . $plural,
                'type' => Type::nonNull(Type::boolean()),
                'description' => 'Delete one or several existing ' . $name,
                'args' => [
                    'ids' => Type::nonNull(Type::listOf(Type::nonNull(_types()->getId($class)))),
                ],
                'resolve' => function ($root, array $args) use ($lowerName): bool {
                    foreach ($args['ids'] as $id) {
                        $object = $id->getEntity();

                        // Check ACL
                        Helper::throwIfDenied($object, 'update');

                        // Do it
                        _em()->remove($object);
                    }

                    _em()->flush();

                    return true;
                },
            ],
        ];
    }

    /**
     * Returns standard mutations to manage many-to-many relations between two given class
     *
     * @param string $ownerClass The class owning the relation
     * @param string $otherClass The other class, not-owning the relation
     * @param bool $byName if true, the name of $other will define the relation instead of its ID
     *
     * @return array
     */
    public static function buildRelationMutation(string $ownerClass, string $otherClass, bool $byName = false): array
    {
        $ownerReflect = new ReflectionClass($ownerClass);
        $ownerName = $ownerReflect->getShortName();
        $lowerOwnerName = lcfirst($ownerName);

        $otherReflect = new ReflectionClass($otherClass);
        $otherName = $otherReflect->getShortName();
        $lowerOtherName = lcfirst($otherName);

        if ($lowerOwnerName === $lowerOtherName) {
            $lowerOwnerName .= 1;
            $lowerOtherName .= 2;
        }

        $args = [
            $lowerOwnerName => Type::nonNull(_types()->getId($ownerClass)),
            $lowerOtherName => Type::nonNull($byName ? Type::string() : _types()->getId($otherClass)),
        ];

        return [
            [
                'name' => 'link' . $ownerName . $otherName,
                'type' => Type::nonNull(_types()->getOutput($ownerClass)),
                'description' => 'Create a relation between ' . $ownerName . ' and ' . $otherName . '.' . PHP_EOL . PHP_EOL .
                    'If the relation already exists, it will have no effect.',
                'args' => $args,
                'resolve' => function ($root, array $args) use ($lowerOwnerName, $lowerOtherName, $otherName, $otherClass, $byName): AbstractModel {
                    $owner = $args[$lowerOwnerName]->getEntity();
                    if ($byName) {
                        $other = self::getByName($otherClass, $args[$lowerOtherName], true);
                    } else {
                        $other = $args[$lowerOtherName]->getEntity();
                    }

                    // Check ACL
                    Helper::throwIfDenied($owner, 'update');

                    // Do it
                    $method = 'add' . $otherName;
                    $owner->$method($other);
                    _em()->flush();

                    return $owner;
                },
            ],
            [
                'name' => 'unlink' . $ownerName . $otherName,
                'type' => Type::nonNull(_types()->getOutput($ownerClass)),
                'description' => 'Delete a relation between ' . $ownerName . ' and ' . $otherName . '.' . PHP_EOL . PHP_EOL .
                    'If the relation does not exist, it will have no effect.',
                'args' => $args,
                'resolve' => function ($root, array $args) use ($lowerOwnerName, $lowerOtherName, $otherName, $otherClass, $byName): AbstractModel {
                    $owner = $args[$lowerOwnerName]->getEntity();
                    if ($byName) {
                        $other = self::getByName($otherClass, $args[$lowerOtherName], false);
                    } else {
                        $other = $args[$lowerOtherName]->getEntity();
                    }

                    // Check ACL
                    Helper::throwIfDenied($owner, 'update');

                    // Do it
                    if ($other) {
                        $method = 'remove' . $otherName;
                        $owner->$method($other);
                        _em()->flush();
                    }

                    return $owner;
                },
            ],
        ];
    }

    /**
     * Load object from DB and optionally create new one if not found
     *
     * @param string $class
     * @param string $name
     * @param bool $createIfNotFound
     *
     * @return null|AbstractModel
     */
    private static function getByName(string $class, string $name, bool $createIfNotFound): ?AbstractModel
    {
        $name = trim($name);
        $other = _em()->getRepository($class)->findOneByName($name);

        if (!$other && $createIfNotFound) {
            $other = new $class();
            $other->setName($name);
            _em()->persist($other);
        }

        return $other;
    }

    /**
     * Returns the plural form of the given name
     *
     * @param string $name
     *
     * @return string
     */
    private static function makePlural(string $name): string
    {
        $plural = $name . 's';
        $plural = preg_replace('/ys$/', 'ies', $plural);
        $plural = preg_replace('/ss$/', 'ses', $plural);

        return $plural;
    }

    /**
     * Return arguments used for the list
     *
     * @param string $class
     *
     * @return array
     */
    private static function getListArguments(string $class, string $name): array
    {
        $listArgs = [];
        $filters = Filters::build($class);
        if ($filters) {
            $listArgs[] = $filters;
        }

        $defaultSort = $name . '.id';
        $defaultOrder = 'ASC';
        if ($class === Collection::class) {
            $defaultSort = 'collection.name';
        } elseif ($class === Card::class) {
            $defaultSort = 'card.creationDate';
            $defaultOrder = 'DESC';
        }

        $listArgs[] = [
            'name' => 'sort',
            'type' => Type::string(),
            'description' => 'Field to sort by, eg: `user.name`, `collection.id`',
            'defaultValue' => $defaultSort,
        ];

        $listArgs[] = [
            'name' => 'order',
            'type' => _types()->get(OrderType::class),
            'description' => 'Order sort by, either: `ASC` or `DESC`',
            'defaultValue' => $defaultOrder,
        ];

        $listArgs[] = PaginationInputType::build();

        return $listArgs;
    }

    /**
     * Return arguments used for single item
     *
     * @param string $class
     *
     * @return array
     */
    private static function getSingleArguments(string $class): array
    {
        $args = [
            'id' => Type::nonNull(_types()->getId($class)),
        ];

        return $args;
    }
}
