# Менеджер для работы с БД

Достаточно простой в использовании и легко расширяется для создания адаптеров подключения. В данный момент описаны адаптеры, которые работают с PDO, MySQLi и SQLite3.

## Установка в проект

Выполните команду в консоли:

```ssh
composer require remils/database
```

## Менеджер БД

Инициализируйте класс менеджера в нужном вам месте, либо добавьте его в контейнер зависимостей.

```php
<?php

use Remils\Database\Manager;

$manager = new Manager();
```

Методы менеджера:

| Метод                                                       | Описание                                   |
| ----------------------------------------------------------- | ------------------------------------------ |
| setConnect(string $name, ConnectContract $connect): Manager | Добавляет подключение в менеджер           |
| getConnect(string $name): ConnectContract                   | Возвращает объект подключения из менеджера |

## Адаптеры подключения

Доступные адаптеры:

| Пространство имени              | Конструктор                                                                                                                                          | Описание                        |
| ------------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------- | ------------------------------- |
| Remils\Database\MySQLi\Connect  | \_\_construct(string $host, ?string $username = null, ?string $password = null, ?string $database = null, ?int $port = null, ?string $socket = null) | Адаптер подключения для MySQLi  |
| Remils\Database\PDO\Connect     | \_\_construct(string $dsn, ?string $username = null, ?string $password = null, ?array $options = null)                                               | Адаптер подключения для PDO     |
| Remils\Database\SQLite3\Connect | \_\_construct(string $filename, int $flags = SQLITE3_OPEN_READWRITE \| SQLITE3_OPEN_CREATE, string $encryptionKey = null)                            | Адаптер подключения для SQLite3 |

Все адаптеры реализуют интерфейс Remils\Database\Contract\ConnectContract.

Методы интерфейса:

| Метод                                   | Описание                                                                   |
| --------------------------------------- | -------------------------------------------------------------------------- |
| customizer(callable $callback): void    | Настройка пользователя (Возвращает объект подключения)                     |
| transaction(Closure $closure): mixed    | Вызов транзакции                                                           |
| lastInsertId(): mixed                   | Возвращает ID последней вставленной строки или значение последовательности |
| prepare(string $sql): StatementContract | Подготавливает SQL выражение к выполнению                                  |
| execute(string $sql): ResultContract    | Выполняет запрос к базе данных                                             |

## Подготовка запроса

Каждый адаптер реализует свою подготовку запроса интерфейса Remils\Database\Contract\StatementContract.

Методы интерфейса:

| Метод                                                                                                   | Описание                                                   |
| ------------------------------------------------------------------------------------------------------- | ---------------------------------------------------------- |
| setParameter(string $key, mixed $value, ParameterType $type = ParameterType::STRING): StatementContract | Привязка переменных к параметрам подготавливаемого запроса |
| execute(): ResultContract                                                                               | Выполняет запрос к базе данных                             |

## Типизация параметров в подготавливаемом запросе

Для преобразования типов в подготовительном запросе используются константы из перечисления Remils\Database\Enum\ParameterType.

| Константа              | Описание                                             |
| ---------------------- | ---------------------------------------------------- |
| ParameterType::INTEGER | Преобразование подготовительного выражения в INTEGER |
| ParameterType::FLOAT   | Преобразование подготовительного выражения в FLOAT   |
| ParameterType::STRING  | Преобразование подготовительного выражения в STRING  |
| ParameterType::BOOLEAN | Преобразование подготовительного выражения в BOOLEAN |
| ParameterType::BLOB    | Преобразование подготовительного выражения в BLOB    |
| ParameterType::NULL    | Преобразование подготовительного выражения в NULL    |
| ParameterType::JSON    | Преобразование подготовительного выражения в JSON    |

## Обработка запроса

После выполнения execute метода, для обработки результатов выполненного запроса используются методы интерфейса Remils\Database\Contract\ResultContract.

Методы интерфейса:

| Метод                                                      | Описание                                                                           |
| ---------------------------------------------------------- | ---------------------------------------------------------------------------------- |
| fetch(): ?array                                            | Выбирает строку из набора результатов и помещает её в ассоциативный массив         |
| fetchObject(string $className = stdClass::class): ?object  | Выбирает строку из набора результатов в виде объекта                               |
| fetchAll(): array                                          | Выбирает все строки из результирующего набора и помещает их в ассоциативный массив |
| fetchAllObject(string $className = stdClass::class): array | Выбирает все строки из результирующего набора и помещает их в объект               |
| fetchColumn(int $column = 0): mixed                        | Получает один столбец из строки набора результатов                                 |
| fetchAllColumn(int $column = 0): array                     | Получает все строки, в виде массива, содержащие значения столбца                   |

## Репозиторий

Репозитории реализуют интерфейс Remils\Database\Contract\RepositoryContract.

Методы интерфейса:

| Метод                         | Описание                |
| ----------------------------- | ----------------------- |
| getTableName(): string        | Возвращает имя таблицы  |
| getEntityClassName(): string  | Возвращает имя модели   |
| getConnectName(): string      | Возвращает имя коннекта |
| getConnect(): ConnectContract | Возвращает подключение  |

Для удобства был реализован абстрактный репозиторий, в котором описаны наиболее распространеные методы.
Если требуются другие методы, то смело расширяйте свой репозиторий.

Методы абстрактного репозитория:

| Метод                                              | Описание                                                                  |
| -------------------------------------------------- | ------------------------------------------------------------------------- |
| all(): array                                       | Возвращает все записи                                                     |
| first(array $credentials = []): ?object            | Возвращает первую запись, либо первую подходящую запись по учетным данным |
| paginate(int $offset, int $limit = 15): array      | Постраничный вывод записей                                                |
| count(): int                                       | Возвращает количество записей в таблице                                   |
| insert(array $data): object                        | Вставка записи                                                            |
| delete(array $credentials = []): void              | Удаляет все записи, либо записи которые удовлетворяют учетным данным      |
| update(array $data, array $credentials = []): void | Обновляет записи, либо записи которые удовлетворяют учетным данным        |

## Пример

```php
<?php

use Remils\Database\AbstractRepository;
use Remils\Database\Manager;
use Remils\Database\PDO\Connect;

require 'vendor/autoload.php';

$manager = new Manager();

$manager->setConnect('default', new Connect('sqlite:test.db'));

$connect = $manager->getConnect('default');

$connect->execute(<<<SQL
    CREATE TABLE IF NOT EXISTS `users` (
        `id` INTEGER PRIMARY KEY AUTOINCREMENT,
        `name` VARCHAR(255)
    );
SQL);

class User
{
    private int $id;

    private string $name;

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}

class UserRepository extends AbstractRepository
{
    public function getConnectName(): string
    {
        return 'default';
    }

    public function getTableName(): string
    {
        return 'users';
    }

    public function getEntityClassName(): string
    {
        return User::class;
    }
}

$userRepository = new UserRepository($manager);

$user = $userRepository->insert([
    'name' => 'Иван',
]);

var_dump($user);

$userRepository->update([
    'name' => 'Василий',
], [
    'id' => $user->getId(),
]);

$user = $userRepository->first([
    'id' => $user->getId(),
]);

var_dump($user);

$userRepository->delete([
    'id' => $user->getId(),
]);
```
