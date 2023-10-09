<?php

declare(strict_types=1);

namespace Remils\Database\PDO;

use PDO;
use PDOStatement;
use Remils\Database\Contract\ResultContract;
use stdClass;

final class Result implements ResultContract
{
    public function __construct(
        protected PDOStatement $query,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function fetch(): ?array
    {
        $this->query->setFetchMode(PDO::FETCH_ASSOC);

        return $this->query->fetch() ?: null;
    }

    /**
     * @inheritDoc
     */
    public function fetchObject(string $className = stdClass::class): ?object
    {
        $this->query->setFetchMode(PDO::FETCH_CLASS, $className, null);

        return $this->query->fetch() ?: null;
    }

    /**
     * @inheritDoc
     */
    public function fetchAll(): array
    {
        $this->query->setFetchMode(PDO::FETCH_ASSOC);

        return $this->query->fetchAll();
    }

    /**
     * @inheritDoc
     */
    public function fetchAllObject(string $className = stdClass::class): array
    {
        $this->query->setFetchMode(PDO::FETCH_CLASS, $className, null);

        return $this->query->fetchAll();
    }

    /**
     * @inheritDoc
     */
    public function fetchColumn(int $column = 0): mixed
    {
        return $this->query->fetchColumn($column);
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
