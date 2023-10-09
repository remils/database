<?php

declare(strict_types=1);

namespace Remils\Database\MySQLi;

use Closure;
use mysqli;
use Remils\Database\Contract\ConnectContract;
use Remils\Database\Contract\ResultContract;
use Remils\Database\Contract\StatementContract;
use Throwable;

final class Connect implements ConnectContract
{
    protected mysqli $connect;

    /**
     * @param string $host
     * @param string|null $username
     * @param string|null $password
     * @param string|null $database
     * @param int|null $port
     * @param string|null $socket
     */
    public function __construct(
        string $host,
        ?string $username = null,
        ?string $password = null,
        ?string $database = null,
        ?int $port = null,
        ?string $socket = null
    ) {
        $this->connect = new mysqli($host, $username, $password, $database, $port, $socket);
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
            $this->connect->begin_transaction();

            $result = $closure->call($this);

            $this->connect->commit();

            return $result;
        } catch (Throwable $exception) {
            $this->connect->rollback();

            throw $exception;
        }
    }

    /**
     * @inheritDoc
     */
    public function lastInsertId(): mixed
    {
        return $this->connect->insert_id;
    }

    /**
     * @inheritDoc
     */
    public function prepare(string $sql): StatementContract
    {
        $statement = $this->connect->prepare($sql);

        return new Statement($statement);
    }

    /**
     * @inheritDoc
     */
    public function execute(string $sql): ResultContract
    {
        $query = $this->connect->query($sql);

        return new Result($query);
    }
}
