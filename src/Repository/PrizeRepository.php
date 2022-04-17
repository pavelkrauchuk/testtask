<?php
/**
 * @author Pavel Krauchuk <pavel.krauchuk@gmail.com>
 */

namespace Pavelkrauchuk\Testtask\Repository;

use JetBrains\PhpStorm\Pure;
use Pavelkrauchuk\Testtask\Database;
use Pavelkrauchuk\Testtask\Entity\Prize;
use Pavelkrauchuk\Testtask\Entity\User;

class PrizeRepository
{
    /** @var User $user */
    protected User $user;

    #[Pure]
    public function __construct()
    {
        $this->user = new User();
    }

    /**
     * Добавление информации о сгенерированном подарке в таблицу "prizes_received"
     *
     * Поле "type" таблицы заполняется в зависимости от переданного в качестве аргумента типа объекта подарка:
     * "bonus", "money" или "thing"
     *
     * @param Prize $prize Объект одного из дочерних классов: BonusPrize, MoneyPrize или ThingPrize
     * @return int|false При успешном добавлении id строки, иначе - false
     */
    protected function addNewEntry(Prize $prize) : int|false
    {
        $userId = $this->user->getId();
        if (!$userId) {
            return false;
        }

        $pdo = Database::getPDO();
        $query = $pdo->prepare('INSERT INTO `prizes_received` VALUES(null, :userId, :type)');
        $result = $query->execute(array('userId' => $userId, 'type' => $prize->getType()));

        if ($result) {
            return $pdo->lastInsertId();
        }

        return false;
    }

    /**
     * Удаление информации при отказе от подарка из таблицы "prizes_received"
     *
     * @param Prize $prize Объект одного из дочерних классов: BonusPrize, MoneyPrize или ThingPrize. Должен иметь
     * установленное свойство Id
     * @return bool При успешном удалении возвращается true, иначе - false
     */
    protected function deleteEntry(Prize $prize) : bool
    {
        $userId = $this->user->getId();
        if (!$userId) {
            return false;
        }

        $pdo = Database::getPDO();
        $query = $pdo->prepare('DELETE FROM `prizes_received` WHERE `id` = :id AND `user_id` = :userId');
        $result = $query->execute(array('id' => $prize->getId(), 'userId' => $userId));

        if ($result) {
            return true;
        }

        return false;
    }

    /**
     * Получение информации о всех полученных текущим пользователем подарках любых типов
     *
     * @return array|false Массив с информацией о подарках, при отсутствии записей в БД - false
     */
    public function getAllEntries() : array|false
    {
        $userId = $this->user->getId();
        if (!$userId) {
            return false;
        }

        $pdo = Database::getPDO();
        $query = $pdo->prepare('SELECT `id`, `type`, `money_prizes`.`amount` AS `money_amount`, 
            `money_prizes`.`converted`, `money_prizes`.`transferred`, `bonus_prizes`.`amount` AS `bonus_amount`, 
            `bonus_prizes`.`admissed`, `thing_prizes`.`name`, `thing_prizes`.`shipped` FROM `prizes_received` LEFT JOIN
            `money_prizes` ON `prizes_received`.`id` = `money_prizes`.`prize_id` LEFT JOIN `bonus_prizes` ON 
            `prizes_received`.`id` = `bonus_prizes`.`prize_id` LEFT JOIN `thing_prizes` ON `prizes_received`.`id` =
            `thing_prizes`.`prize_id` WHERE `user_id` = :userId ORDER BY `id` ASC');
        $query->execute(array('userId' => $userId));

        if ($query->rowCount()) {

            $data = array();
            while ($row = $query->fetch(\PDO::FETCH_ASSOC)) {
                $data[] = array_filter($row, function ($value){
                    return isset($value);
                });
            }

            return $data;
        }

        return false;
    }
}