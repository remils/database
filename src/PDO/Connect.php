<?php

declare(strict_types=1);

namespace Remils\Database\PDO;

use Closure;
use PDO;
use Remils\Database\Contract\ConnectContract;
use Remils\Database\Contract\ResultContract;
use Remils\Database\Contract\StatementContract;
use Remils\Database\Exception\DatabaseException;
use Throwable;

final class Connect implements ConnectContract
{
    protected PDO $connect;

    /**
     * @param string $dsn
     * @param string|null $username
     * @param string|null $password
     * @param array<mixed>|null $options
     */
    public function __construct(string $dsn, ?string $username = null, ?string $password = null, ?array $options = null)
    {
        $this->connect = new PDO($dsn, $username, $password, $options);
    }

    /**
     * @inheritDoc
     */
    public function customizer(callable $callback): void
    {
        call_user_func($callback, $this->connect);
    }

    /**
     * @inheritDoc
     */
    public function transaction(Closure $closure): mixed
    {
        try {
            $this->connect->beginTransaction();

            $result = $closure->call($this);

            $this->connect->commit();

            return $result;
        } catch (Throwable $exception) {
            $this->connect->rollBack();

            throw $exception;
        }
    }

    /**
     * @inheritDoc
     * @param string|null $name Имя объекта последовательности, который должен выдать ID
     */
    public function lastInsertId(string $name = null): mixed
    {
        return $this->connect->lastInsertId($name);
    }

    /**
     * @inheritDoc
     */
    public function prepare(string $sql): StatementContract
    {
        $statement = $this->connect->prepare($sql);

        if ($statement) {
            return new Statement($statement);
        }

        throw new DatabaseException('Ошибка PDO.');
    }

    /**
     * @inheritDoc
     */
    public function execute(string $sql): ResultContract
    {
        $query = $this->connect->query($sql);

        if ($query && $query->execute()) {
            return new Result($query);
        }

        throw new DatabaseException('Ошибка PDO.');
    }
}
