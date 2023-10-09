<?php

declare(strict_types=1);

namespace Remils\Database\Contract;

use Closure;

/**
 * Подключение
 */
interface ConnectContract
{
    /**
     * Запускает функцию замыкания в которой прописываются пользовательские настройки
     *
     * @param callable $callback Функция замыкания
     * @return void
     */
    public function customizer(callable $callback): void;

    /**
     * Вызов транзакции
     *
     * @param Closure $closure Функция которая оборачивается транзакцией
     * @return mixed
     */
    public function transaction(Closure $closure): mixed;

    /**
     * Возвращает ID последней вставленной строки или значение последовательности
     *
     * @return mixed
     */
    public function lastInsertId(): mixed;

    /**
     * Подготавливает SQL выражение к выполнению
     *
     * @param string $sql Текст запроса
     * @return StatementContract
     */
    public function prepare(string $sql): StatementContract;

    /**
     * Выполняет запрос к базе данных
     *
     * @param string $sql Текст запроса
     * @return ResultContract
     */
    public function execute(string $sql): ResultContract;
}
