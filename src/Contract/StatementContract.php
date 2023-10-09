<?php

declare(strict_types=1);

namespace Remils\Database\Contract;

use Remils\Database\Enum\ParameterType;

/**
 * Представляет подготовленное выражение
 */
interface StatementContract
{
    /**
     * Привязка переменных к параметрам подготавливаемого запроса
     *
     * @param string $key Имя параметра
     * @param mixed $value Значение параметра
     * @param ParameterType $type Явно заданный тип данных параметра
     * @return StatementContract
     */
    public function setParameter(
        string $key,
        mixed $value,
        ParameterType $type = ParameterType::STRING
    ): StatementContract;

    /**
     * Выполняет запрос к базе данных
     *
     * @return ResultContract
     */
    public function execute(): ResultContract;
}
