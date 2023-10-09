<?php

declare(strict_types=1);

namespace Remils\Database\MySQLi;

use mysqli_result;
use Remils\Database\Contract\ResultContract;
use stdClass;

final class Result implements ResultContract
{
    public function __construct(
        protected mysqli_result|bool $query
    ) {
    }

    /**
     * @inheritDoc
     */
    public function fetch(): ?array
    {
        if ($this->query instanceof mysqli_result) {
            return $this->query->fetch_array(MYSQLI_ASSOC);
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function fetchObject(string $className = stdClass::class): ?object
    {
        if ($this->query instanceof mysqli_result) {
            return $this->query->fetch_object($className);
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function fetchAll(): array
    {
        if ($this->query instanceof mysqli_result) {
            return $this->query->fetch_all(MYSQLI_ASSOC);
        }

        return [];
    }

    /**
     * @inheritDoc
     */
    public function fetchAllObject(string $className = stdClass::class): array
    {
        if ($this->query instanceof mysqli_result) {
            $items = [];

            while ($item = $this->fetchObject($className)) {
                $items[] = $item;
            }

            return $items;
        }

        return [];
    }

    /**
     * @inheritDoc
     */
    public function fetchColumn(int $column = 0): mixed
    {
        if ($this->query instanceof mysqli_result) {
            return $this->query->fetch_column($column);
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function fetchAllColumn(int $column = 0): array
    {
        if ($this->query instanceof mysqli_result) {
            $items = [];

            while ($item = $this->fetchColumn($column)) {
                $items[] = $item;
            }

            return $items;
        }

        return [];
    }
}
