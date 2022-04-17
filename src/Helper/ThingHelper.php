<?php
/**
 * @author Pavel Krauchuk <pavel.krauchuk@gmail.com>
 */

namespace Pavelkrauchuk\Testtask\Helper;

use Pavelkrauchuk\Testtask\Database;

class ThingHelper
{
    /**
     * Проверка наличия доступных предметов для генерации подарка
     *
     * Получает количество предметов в таблице "available_things", при наличии положительного значения возвращает true
     *
     * @return bool
     */
    public static function isAvailable() : bool
    {
        $pdo = Database::getPDO();
        $query = $pdo->prepare("SELECT COUNT(*) FROM `available_things`");
        $query->execute();

        $data = $query->fetch(\PDO::FETCH_ASSOC);
        if (isset($data['COUNT(*)']) && $data['COUNT(*)'] > 0) {
            return true;
        }

        return false;
    }

    /**
     * Получение случайного предмета из таблицы "available_things" для генерации подарка
     *
     * @return array вида ['id' => value, 'name' => value]
     */
    public static function getRandomThingValue() : array
    {
        $pdo = Database::getPDO();
        $query = $pdo->prepare("SELECT * FROM `available_things` ORDER BY RAND() LIMIT 1");
        $query->execute();

        return $query->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Удаляет предмет из таблицы "available_things" по его Id
     *
     * @param int $rowId Id строки в таблице
     * @return void
     */
    public static function deleteFromAvailable(int $rowId) : void
    {
        $pdo = Database::getPDO();
        $query = $pdo->prepare("DELETE FROM `available_things` WHERE `id` = :rowId");
        $query->execute(array('rowId' => $rowId));
    }

    /**
     * Добавляет запись в таблицу доступных для подарков предметов (available_things)
     *
     * @param string $name Название предмета
     * @return void
     */
    public static function insertToAvailable(string $name) : void
    {
        $pdo = Database::getPDO();
        $query = $pdo->prepare("INSERT INTO `available_things` VALUES (null, :name)");
        $query->execute(array('name' => $name));
    }
}