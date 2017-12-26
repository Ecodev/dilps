<?php

declare(strict_types=1);

namespace Application\Api\Field;

use Application\Api\Helper;
use Application\Api\Input\Filter\Filters;
use Application\Api\Input\PaginationInputType;
use Application\Api\Output\PaginationType;
use Application\Model\AbstractModel;
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
        $plural = self::makePlural($name);

        $listArgs = self::getListArguments($class);
        $singleArgs = self::getSingleArguments($class);

        return [
            $plural => [
                'type' => PaginationType::build($class),
                'args' => $listArgs,
                'resolve' => function ($root, array $args) use ($class): array {
                    $query = _em()->getRepository($class)->getFindAllQuery($args['filters'] ?? []);
                    $result = Helper::paginate($args['pagination'], $query);

                    return $result;
                },
            ],
            $name => [
                'type' => _types()->get($class),
                'args' => $singleArgs,
                'resolve' => function ($root, array $args): ?AbstractModel {
                    $object = $args['id']->getEntity();

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
        $lowerName = lcfirst($name);

        $createArgs = [];
        $createArgs['input'] = Type::nonNull(_types()->getInput($class));

        return [
            'create' . $name => [
                'type' => Type::nonNull(_types()->get($class)),
                'description' => 'Create a new ' . $name,
                'args' => $createArgs,
                'resolve' => function ($root, array $args) use ($class, $lowerName): AbstractModel {
                    // Check ACL
                    $object = new $class();
//                    Helper::loadContextFromArgs($args, $object);
//                    Helper::throwIfDenied($lowerName, 'add');

                    // Do it
                    $input = $args['input'];
                    Helper::hydrate($object, $input);
                    _em()->persist($object);
                    _em()->flush();

                    return $object;
                },
            ],
            'update' . $name => [
                'type' => Type::nonNull(_types()->get($class)),
                'description' => 'Update an existing ' . $name,
                'args' => [
                    'id' => Type::nonNull(_types()->getId($class)),
                    'input' => Type::nonNull(_types()->getInput($class)),
                ],
                'resolve' => function ($root, array $args) use ($lowerName): AbstractModel {
                    $object = $args['id']->getEntity();

                    // Check ACL
//                    Helper::loadContextFromObject($object);
//                    Helper::throwIfDenied($object, 'edit');

                    // Do it
                    $input = $args['input'];
                    Helper::hydrate($object, $input);

                    _em()->flush();

                    return $object;
                },
            ],
            'delete' . $name => [
                'type' => Type::nonNull(Type::boolean()),
                'description' => 'Delete an existing ' . $name,
                'args' => [
                    'id' => Type::nonNull(_types()->getId($class)),
                ],
                'resolve' => function ($root, array $args) use ($lowerName): bool {
                    $object = $args['id']->getEntity();

                    // Check ACL
//                    Helper::loadContextFromObject($object);
//                    Helper::throwIfDenied($object, 'edit');

                    // Do it
                    _em()->remove($object);
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

        $args = [
            $lowerOwnerName => Type::nonNull(_types()->getId($ownerClass)),
            $lowerOtherName => Type::nonNull($byName ? Type::string() : _types()->getId($otherClass)),
        ];

        return [
            'link' . $ownerName . $otherName => [
                'type' => Type::nonNull(_types()->get($ownerClass)),
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
//                    Helper::loadContextFromObject($owner);
//                    Helper::throwIfDenied($owner, 'edit');

                    // Do it
                    $method = 'add' . $otherName;
                    $owner->$method($other);
                    _em()->flush();

                    return $owner;
                },
            ],
            'unlink' . $ownerName . $otherName => [
                'type' => Type::nonNull(_types()->get($ownerClass)),
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
//                    Helper::loadContextFromObject($owner);
//                    Helper::throwIfDenied($owner, 'edit');

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
    private static function getListArguments(string $class): array
    {
        $listArgs = [];
        $filters = Filters::build($class);
        if ($filters) {
            $listArgs[] = $filters;
        }
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
