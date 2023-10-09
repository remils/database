<?php

declare(strict_types=1);

namespace Remils\Database\SQLite3;

use Remils\Database\Contract\ResultContract;
use Remils\Database\Contract\StatementContract;
use Remils\Database\Enum\ParameterType;
use Remils\Database\Exception\DatabaseException;
use SQLite3Stmt;

final class Statement implements StatementContract
{
    public function __construct(
        protected SQLite3Stmt $statement,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function setParameter(
        string $key,
        mixed $value,
        ParameterType $type = ParameterType::STRING
    ): StatementContract {
        switch ($type) {
            case ParameterType::INTEGER:
                $this->statement->bindParam($key, $value, SQLITE3_INTEGER);
                break;
            case ParameterType::FLOAT:
                $this->statement->bindParam($key, $value, SQLITE3_FLOAT);
                break;
            case ParameterType::STRING:
                $this->statement->bindParam($key, $value, SQLITE3_TEXT);
                break;
            case ParameterType::BOOLEAN:
                $this->statement->bindParam($key, $value, SQLITE3_INTEGER);
                break;
            case ParameterType::BLOB:
                $this->statement->bindParam($key, $value, SQLITE3_BLOB);
                break;
            case ParameterType::NULL:
                $this->statement->bindParam($key, $value, SQLITE3_NULL);
                break;
            case ParameterType::JSON:
                $json = json_encode($value);
                $this->statement->bindParam($key, $json, SQLITE3_TEXT);
                break;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function execute(): ResultContract
    {
        $query = $this->statement->execute();

        if ($query) {
            return new Result($query);
        }

        throw new DatabaseException('Ошибка SQLite3.');
    }
}
