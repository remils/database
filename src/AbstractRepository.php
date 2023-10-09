<?php

declare(strict_types=1);

namespace Remils\Database;

use Remils\Database\Contract\ConnectContract;
use Remils\Database\Contract\RepositoryContract;
use Remils\Database\Contract\StatementContract;
use Remils\Database\Enum\ParameterType;
use Remils\Database\Exception\RepositoryException;

/**
 * Реализация репозитория
 */
abstract class AbstractRepository implements RepositoryContract
{
    /**
     * Подключение
     *
     * @var ConnectContract
     */
    private ConnectContract $connect;

    /**
     * @param Manager $manager
     */
    public function __construct(Manager $manager)
    {
        $this->connect = $manager->getConnect($this->getConnectName());
    }

    /**
     * @inheritDoc
     */
    public function getConnect(): ConnectContract
    {
        return $this->connect;
    }

    /**
     * Возвращает все записи
     *
     * @return array<int,object>
     */
    public function all(): array
    {
        $sql = sprintf(
            'SELECT * FROM `%s`;',
            $this->getTableName(),
        );

        return $this->getConnect()
            ->execute($sql)
            ->fetchAllObject($this->getEntityClassName());
    }

    /**
     * Возвращает первую запись, либо первую подходящую запись по учетным данным
     *
     * @param array<string,mixed> $credentials
     * @return object|null
     */
    public function first(array $credentials = []): ?object
    {
        $sql = sprintf(
            'SELECT * FROM `%s` /* CREDENTIALS */ LIMIT 1;',
            $this->getTableName(),
        );

        if ($credentials) {
            $sql = strtr($sql, [
                '/* CREDENTIALS */' => 'WHERE ' . implode(' AND ', $this->prepareColumns($credentials)),
            ]);
        }

        $statement = $this->getConnect()->prepare($sql);

        $this->setParameters($statement, $credentials);

        return $statement->execute()
            ->fetchObject($this->getEntityClassName());
    }

    /**
     * Постраничный вывод записей
     *
     * @param integer $offset
     * @param integer $limit
     * @return array<int,object>
     */
    public function paginate(int $offset, int $limit = 15): array
    {
        $sql = sprintf(
            'SELECT * FROM `%s` LIMIT :limit OFFSET :offset;',
            $this->getTableName(),
        );

        return $this->getConnect()
            ->prepare($sql)
            ->setParameter('offset', $offset, ParameterType::INTEGER)
            ->setParameter('limit', $limit, ParameterType::INTEGER)
            ->execute()
            ->fetchAllObject($this->getEntityClassName());
    }

    /**
     * Возвращает количество записей в таблице
     *
     * @return integer
     */
    public function count(): int
    {
        $sql = sprintf(
            'SELECT COUNT(*) FROM `%s`;',
            $this->getTableName(),
        );

        return $this->getConnect()
            ->execute($sql)
            ->fetchColumn(0);
    }

    /**
     * Вставка записи
     *
     * @param array<string,mixed> $data
     * @return object
     */
    public function insert(array $data): object
    {
        if (!$data) {
            throw new RepositoryException('Нет данных для вставки.');
        }

        $keys    = array_keys($data);
        $columns = array_map(fn (string $key) => sprintf('`%s`', $key), $keys);
        $params  = array_map(fn (string $key) => sprintf(':%s', $key), $keys);

        $sql = sprintf(
            'INSERT INTO `%s` (%s) VALUES (%s) RETURNING *;',
            $this->getTableName(),
            implode(', ', $columns),
            implode(', ', $params),
        );

        $statement = $this->getConnect()->prepare($sql);

        $this->setParameters($statement, $data);

        return $statement->execute()
            ->fetchObject($this->getEntityClassName());
    }

    /**
     * Удаляет все записи, либо записи которые удовлетворяют учетным данным
     *
     * @param array<string,mixed> $credentials
     * @return void
     */
    public function delete(array $credentials = []): void
    {
        if ($credentials) {
            $sql = sprintf(
                'DELETE FROM `%s` WHERE %s;',
                $this->getTableName(),
                implode(' AND ', $this->prepareColumns($credentials)),
            );
        } else {
            $sql = sprintf(
                'DELETE FROM `%s`;',
                $this->getTableName(),
            );
        }

        $statement = $this->connect->prepare($sql);

        $this->setParameters($statement, $credentials);

        $statement->execute();
    }

    /**
     * Обновляет записи, либо записи которые удовлетворяют учетным данным
     *
     * @param array<string,mixed> $data
     * @param array<string,mixed> $credentials
     * @return void
     */
    public function update(array $data, array $credentials = []): void
    {
        if (!$data) {
            throw new RepositoryException('Нет данных для обновления.');
        }

        $sql = sprintf(
            'UPDATE `%s` SET %s /* CREDENTIALS */;',
            $this->getTableName(),
            implode(', ', $this->prepareColumns($data)),
        );

        if ($credentials) {
            $sql = strtr($sql, [
                '/* CREDENTIALS */' => 'WHERE ' . implode(' AND ', $this->prepareColumns($credentials)),
            ]);
        }

        $statement = $this->getConnect()->prepare($sql);

        $this->setParameters($statement, $data, $credentials);

        $statement->execute();
    }

    /**
     * Возвращает подготовленый набор колонок с параметрами
     *
     * @param array<string,mixed> $data
     * @return array<int,string>
     */
    protected function prepareColumns(array $data): array
    {
        return array_map(
            fn (string $key) => sprintf(
                '`%s` = :%s',
                $key,
                $key
            ),
            array_keys($data)
        );
    }

    /**
     * Устанавливает параметры в подготовительный запрос
     *
     * @param StatementContract $statement
     * @param array<string,mixed> ...$params
     * @return void
     */
    protected function setParameters(StatementContract $statement, ...$params): void
    {
        $params = array_merge(...$params);

        foreach ($params as $key => $value) {
            $type = match (gettype($value)) {
                'integer' => ParameterType::INTEGER,
                'double'  => ParameterType::FLOAT,
                'string'  => ParameterType::STRING,
                'boolean' => ParameterType::BOOLEAN,
                'NULL'    => ParameterType::NULL,
                'array'   => ParameterType::JSON,
                default   => ParameterType::STRING,
            };

            $statement->setParameter($key, $value, $type);
        }
    }
}
