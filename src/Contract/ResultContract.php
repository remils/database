<?php

declare(strict_types=1);

namespace Remils\Database\Contract;

use stdClass;

/**
 * Представляет результирующий набор, полученный из запроса в базу данных
 */
interface ResultContract
{
    /**
     * Выбирает строку из набора результатов и помещает её в ассоциативный массив
     *
     * @return array<mixed>|null
     */
    public function fetch(): ?array;

    /**
     * Выбирает строку из набора результатов в виде объекта
     *
     * @param string $className Имя объекта
     * @return object|null
     */
    public function fetchObject(string $className = stdClass::class): ?object;

    /**
     * Выбирает все строки из результирующего набора и помещает их в ассоциативный массив
     *
     * @return array<array<mixed>>
     */
    public function fetchAll(): array;

    /**
     * Выбирает все строки из результирующего набора и помещает их в объект
     *
     * @param string $className Имя объекта
     * @return array<object>
     */
    public function fetchAllObject(string $className = stdClass::class): array;

    /**
     * Получает один столбец из строки набора результатов
     *
     * @param integer $column Номер колонки (начиная с 0)
     * @return mixed
     */
    public function fetchColumn(int $column = 0): mixed;

    /**
     * Получает все строки, в виде массива, содержащие значения столбца
     *
     * @param integer $column Номер колонки (начиная с 0)
     * @return array<mixed>
     */
    public function fetchAllColumn(int $column = 0): array;
}
