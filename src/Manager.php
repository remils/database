<?php

declare(strict_types=1);

namespace Remils\Database;

use Remils\Database\Contract\ConnectContract;
use Remils\Database\Exception\ConnectAlreadyExistsException;
use Remils\Database\Exception\ConnectNotFoundException;

/**
 * Менеджер подключений
 */
final class Manager
{
    /** @var array<ConnectContract> */
    protected array $connections = [];

    /**
     * Добавляет подключение в менеджер
     *
     * @param string $name Имя подключения
     * @param ConnectContract $connect Объект подключения
     * @return Manager Менеджер подключений
     */
    public function setConnect(string $name, ConnectContract $connect): Manager
    {
        if (array_key_exists($name, $this->connections)) {
            throw new ConnectAlreadyExistsException($name);
        }

        $this->connections[$name] = $connect;

        return $this;
    }

    /**
     * Возвращает объект подключения из менеджера
     *
     * @param string $name Имя подключения
     * @return ConnectContract Объект подключения
     */
    public function getConnect(string $name): ConnectContract
    {
        if (array_key_exists($name, $this->connections)) {
            return $this->connections[$name];
        }

        throw new ConnectNotFoundException($name);
    }
}
