<?php

namespace PhpGame\ECS;

class Registry
{
    private static int $entitiesCount = 0;
    private array $components = [];
    private array $entities = [];

    public function create(): Entity
    {
        return new Entity($this::$entitiesCount++);
    }

    public function add(Entity $entity, $object): void
    {
        if (!isset($this->entities[(string)$entity])) {
            $this->entities[(string)$entity] = [];
        }
        $this->entities[(string)$entity][get_class($object)] = $object;
    }

    public function get(array $classes): array
    {
        return array_map(
            static function ($components) use ($classes) {
                $filteredComponent = [];
                foreach ($classes as $class) {
                    $filteredComponent[$class] = $components[$class];
                }

                return $filteredComponent;
            },
            array_filter(
                $this->entities,
                static function ($components) use ($classes) {
                    foreach ($classes as $class) {
                        if (!isset($components[$class])) {
                            return false;
                        }
                    }
                    return true;
                }
            )
        );
    }
}
