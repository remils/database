<?php

declare(strict_types=1);

namespace Remils\Database\Contract;

/**
 * Репозиторий
 */
interface RepositoryContract
{
    /**
     * Возвращает имя таблицы
     *
     * @return string
     */
    public function getTableName(): string;

    /**
     * Возвращает имя модели
     *
     * @return string
     */
    public function getEntityClassName(): string;

    /**
     * Возвращает имя коннекта
     *
     * @return string
     */
    public function getConnectName(): string;

    /**
     * Возвращает подключение
     *
     * @return ConnectContract
     */
    public function getConnect(): ConnectContract;
}
