<?php

declare(strict_types=1);

namespace Remils\Database\Exception;

final class ConnectAlreadyExistsException extends DatabaseException
{
    public function __construct(string $name)
    {
        parent::__construct(sprintf('Подключение "%s" уже существует.', $name));
    }
}
