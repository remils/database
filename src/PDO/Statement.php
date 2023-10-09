<?php

declare(strict_types=1);

namespace Remils\Database\PDO;

use PDO;
use PDOStatement;
use Remils\Database\Contract\ResultContract;
use Remils\Database\Contract\StatementContract;
use Remils\Database\Enum\ParameterType;
use Remils\Database\Exception\DatabaseException;

final class Statement implements StatementContract
{
    public function __construct(
        protected PDOStatement $statement,
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
                $this->statement->bindParam($key, $value, PDO::PARAM_INT);
                break;
            case ParameterType::FLOAT:
                $this->statement->bindParam($key, $value, PDO::PARAM_STR);
                break;
            case ParameterType::STRING:
                $this->statement->bindParam($key, $value, PDO::PARAM_STR);
                break;
            case ParameterType::BOOLEAN:
                $this->statement->bindParam($key, $value, PDO::PARAM_BOOL);
                break;
            case ParameterType::BLOB:
                $this->statement->bindParam($key, $value, PDO::PARAM_LOB);
                break;
            case ParameterType::NULL:
                $this->statement->bindParam($key, $value, PDO::PARAM_NULL);
                break;
            case ParameterType::JSON:
                $json = json_encode($value);
                $this->statement->bindParam($key, $json, PDO::PARAM_STR);
                break;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function execute(): ResultContract
    {
        if ($this->statement->execute()) {
            return new Result($this->statement);
        }

        throw new DatabaseException('Ошибка PDO.');
    }
}
