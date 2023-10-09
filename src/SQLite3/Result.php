<?php

declare(strict_types=1);

namespace Remils\Database\SQLite3;

use ReflectionClass;
use Remils\Database\Contract\ResultContract;
use SQLite3Result;
use stdClass;
use ValueError;

final class Result implements ResultContract
{
    public function __construct(
        protected SQLite3Result $query,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function fetch(): ?array
    {
        return $this->query->fetchArray(SQLITE3_ASSOC) ?: null;
    }

    /**
     * @inheritDoc
     */
    public function fetchObject(string $className = stdClass::class): ?object
    {
        if ($item = $this->fetch()) {
            $object = new $className();

            $reflectionClass = new ReflectionClass($object);

            foreach ($item as $key => $value) {
                if ($reflectionClass->hasProperty($key)) {
                    $reflectionClass->getProperty($key)->setValue($object, $value);
                } else {
                    $object->{$key} = $value;
                }

                unset($key, $value);
            }

            unset($reflectionClass, $item);

            return $object;
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function fetchAll(): array
    {
        $items = [];

        while ($item = $this->fetch()) {
            $items[] = $item;

            unset($item);
        }

        return $items;
    }

    /**
     * @inheritDoc
     */
    public function fetchAllObject(string $className = stdClass::class): array
    {
        $items = [];

        while ($item = $this->fetchObject($className)) {
            $items[] = $item;

            unset($item);
        }

        return $items;
    }

    /**
     * @inheritDoc
     */
    public function fetchColumn(int $column = 0): mixed
    {
        if ($item = $this->query->fetchArray(SQLITE3_NUM)) {
            if (!array_key_exists($column, $item)) {
                throw new ValueError('Invalid column index');
            }

            return $item[$column];
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function fetchAllColumn(int $column = 0): array
    {
        $items = [];

        while ($item = $this->fetchColumn($column)) {
            $items[] = $item;
        }

        return $items;
    }
}
