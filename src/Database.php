<?php
/**
 * @author Pavel Krauchuk <pavel.krauchuk@gmail.com>
 */

namespace Pavelkrauchuk\Testtask;

class Database
{
    /** @var string DSN-строка для подключения к БД */
    private static string $dsn = 'mysql:dbname=testtask;host=127.0.0.1';

    /** @var string Имя пользователя БД */
    private static string $user = 'root';

    /** @var string Пароль пользователя БД */
    private static string $password = '';

    /**
     * Инициализирует объект PDO для доступа к БД с подготовленными параметрами
     * @return \PDO
     */
    public static function getPDO() : \PDO
    {
        return new \PDO(self::$dsn, self::$user, self::$password);
    }
}