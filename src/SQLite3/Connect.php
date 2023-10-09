<?php

declare(strict_types=1);

namespace Remils\Database\SQLite3;

use Closure;
use Remils\Database\Contract\ConnectContract;
use Remils\Database\Contract\ResultContract;
use Remils\Database\Contract\StatementContract;
use Remils\Database\Exception\DatabaseException;
use SQLite3;
use Throwable;

final class Connect implements ConnectContract
{
    protected SQLite3 $connect;

    /**
     * @param string $filename
     * @param int|null $flags
     * @param string|null $encryptionKey
     */
    public function __construct(
        string $filename,
        ?int $flags = SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE,
        ?string $encryptionKey = null
    ) {
        $this->connect = new SQLite3($filename, $flags, $encryptionKey);
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
            $this->connect->exec('BEGIN;');

            $result = $closure->call($this);

            $this->connect->exec('COMMIT;');

            return $result;
        } catch (Throwable $exception) {
            $this->connect->exec('ROLLBACK;');

            throw $exception;
        }
    }

    /**
     * @inheritDoc
     */
    public function lastInsertId(): mixed
    {
        return $this->connect->lastInsertRowID();
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

        throw new DatabaseException('Ошибка SQLite3.');
    }

    /**
     * @inheritDoc
     */
    public function execute(string $sql): ResultContract
    {
        $query = $this->connect->query($sql);

        if ($query) {
            return new Result($query);
        }

        throw new DatabaseException('Ошибка SQLite3.');
    }
}
