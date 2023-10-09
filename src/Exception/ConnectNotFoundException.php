<?php

declare(strict_types=1);

namespace Remils\Database\Exception;

final class ConnectNotFoundException extends DatabaseException
{
    public function __construct(string $name)
    {
        parent::__construct(sprintf('Подключение "%s" не найдено.', $name));
    }
}
