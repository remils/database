<?php

declare(strict_types=1);

namespace Remils\Database\MySQLi;

use mysqli_stmt;
use Remils\Database\Contract\ResultContract;
use Remils\Database\Contract\StatementContract;
use Remils\Database\Enum\ParameterType;

final class Statement implements StatementContract
{
    protected string $types = '';

    /**
     * @var array<mixed>
     */
    protected array $params = [];

    public function __construct(
        protected mysqli_stmt|bool $statement,
    ) {
    }

    public function __destruct()
    {
        unset($this->statement);
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
                $this->types .= 'i';
                break;
            case ParameterType::FLOAT:
                $this->types .= 'd';
                break;
            case ParameterType::STRING:
                $this->types .= 's';
                break;
            case ParameterType::BOOLEAN:
                $this->types .= 'i';
                break;
            case ParameterType::BLOB:
                $this->types .= 'b';
                break;
            case ParameterType::NULL:
                $this->types .= 's';
                break;
            case ParameterType::JSON:
                $this->types .= 's';
                $value = json_encode($value);
                break;
        }

        $this->params[] = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function execute(): ResultContract
    {
        if ($this->statement instanceof mysqli_stmt) {
            $this->statement->bind_param($this->types, ...$this->params);
            $this->statement->execute();
            $result = $this->statement->get_result();

            return new Result($result);
        }

        return new Result(false);
    }
}
