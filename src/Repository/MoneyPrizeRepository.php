<?php
/**
 * @author Pavel Krauchuk <pavel.krauchuk@gmail.com>
 */

namespace Pavelkrauchuk\Testtask\Repository;

use Pavelkrauchuk\Testtask\Database;
use Pavelkrauchuk\Testtask\Entity\MoneyPrize;
use Pavelkrauchuk\Testtask\Helper\MoneyHelper;

class MoneyPrizeRepository extends PrizeRepository
{
    /**
     * Получение данных о денежном подарке по его Id
     *
     * Метод осуществляет добор из БД дополнительных данных о подарке, зачастую необходимых при других операциях с
     * объектом в контроллерах, дополнительно проводится проверка, является ли объект подарка с таким Id
     * принадлежащим пользователю, от аккаунта которого осуществляются действия
     *
     * @param MoneyPrize $moneyPrize Объект подарка, о котором запрашиваются данные. Должен иметь установленное
     * свойство Id
     * @return MoneyPrize|false Объект с полученными данными, в случае отсутствия в БД записи - false
     */
    public function getById(MoneyPrize $moneyPrize) : MoneyPrize|false
    {
        if (!$this->user->getId()) {
            return false;
        }

        $pdo = Database::getPDO();
        $query = $pdo->prepare('SELECT * FROM `money_prizes` LEFT JOIN `prizes_received` ON
            `money_prizes`.`prize_id` = `prizes_received`.`id` WHERE `money_prizes`.`prize_id` = :prizeId AND 
            `prizes_received`.`user_id` = :userId');
        $query->execute(array('prizeId' => $moneyPrize->getId(), 'userId' => $this->user->getId()));

        if ($query->rowCount()) {
            $data = $query->fetch(\PDO::FETCH_ASSOC);

            $moneyPrize->setAmount($data['amount']);
            $moneyPrize->setConverted($data['converted']);
            $moneyPrize->setTransferred($data['transferred']);

            return $moneyPrize;
        }

        return false;
    }

    /**
     * Добавление в БД новой записи о полученном пользователем денежном подарке
     *
     * @param MoneyPrize $moneyPrize Объект, информация о котором добавляется в БД. Единственными свойствами объекта,
     * значения которых могут быть записаны в БД являются "converted" и "transferred". Иные свойства, например,
     * "amount" (генерируется динамически) или "id" (используется значение БД) будут проигнорированы.
     * @return bool При успешном добавлении записи возвращается true, иначе - false
     * @throws \Exception
     */
    public function add(MoneyPrize $moneyPrize): bool
    {
        if (!MoneyHelper::isAvailable()) {
            return false;
        }

        $rowId = $this->addNewEntry($moneyPrize);
        if ($rowId) {
            $pdo = Database::getPDO();
            $moneyPrize->setAmount(MoneyHelper::getRandomMoneyValue());
            $query = $pdo->prepare('INSERT INTO `money_prizes` VALUES(:prizeId, :amount, :converted, :transferred)');
            $result = $query->execute(array(
                'prizeId' => $rowId,
                'amount' => $moneyPrize->getAmount(),
                'converted' => $moneyPrize->getConverted() ?? 0,
                'transferred' => $moneyPrize->getTransferred() ?? 0,
            ));

            if ($result) {
                MoneyHelper::decreaseAvailableMoney($moneyPrize);
                return true;
            }
        }

        return false;
    }

    /**
     * Удаление информации из БД при отказе пользователя от денежного подарка
     *
     * @param MoneyPrize $moneyPrize Удаляемый объект, должен иметь установленное свойство Id
     * @return bool При успешном удалении возвращается true, иначе - false
     */
    public function delete(MoneyPrize $moneyPrize) : bool
    {
        $pdo = Database::getPDO();
        $query = $pdo->prepare('DELETE FROM `money_prizes` WHERE `prize_id` = :prizeId');
        $result = $query->execute(array('prizeId' => $moneyPrize->getId()));

        if ($result) {
            $this->deleteEntry($moneyPrize);
            MoneyHelper::increaseAvailableMoney($moneyPrize);
            return true;
        }

        return false;
    }

    /**
     * Обновление информации о денежном подарке в БД
     *
     * @param MoneyPrize $moneyPrize Объект подарка, должен иметь установленные свойства "converted", "transferred"
     * и "id"
     * @return bool При успешном обновлении возвращается true, иначе - false
     */
    public function update(MoneyPrize $moneyPrize) : bool
    {
        $pdo = Database::getPDO();
        $query = $pdo->prepare('UPDATE `money_prizes` SET `converted` = :converted, `transferred` = :transferred
            WHERE `prize_id` = :prizeId');
        $result = $query->execute(array(
            'converted' => $moneyPrize->getConverted(),
            'transferred' => $moneyPrize->getTransferred(),
            'prizeId' => $moneyPrize->getId(),
        ));

        if ($result) {
            return true;
        }

        return false;
    }

    /**
     * Получает информацию о денежных подарках всех пользователей, которые не были переведены на счет в банке
     *
     * Используется при пакетной отправке денежных призов из cli (bin/cli.php)
     *
     * @param int|null $limit Необязательный числовой параметр, устанавливает количество записей получаемых из БД
     * @return array|false Ассоциативный массив с информацией о подарках пользователей, при их отсутствии - false
     */
    public function getNotTransferredCollection(int $limit = null) : array|false
    {
        $pdo = Database::getPDO();
        $queryString = 'SELECT `prizes_received`.`id`, `prizes_received`.`type`, `amount` FROM
            `money_prizes` LEFT JOIN `prizes_received` ON `money_prizes`.`prize_id` = `prizes_received`.`id` WHERE
            `money_prizes`.`converted` = 0 AND `money_prizes`.`transferred` = 0';

        if ($limit) {
            $queryString .= " LIMIT :limit";
            $query = $pdo->prepare($queryString);
            $query->bindValue(':limit', $limit, \PDO::PARAM_INT);
        } else {
            $query = $pdo->prepare($queryString);
        }

        $query->execute();

        $data = array();
        if ($query->rowCount()) {
            while ($row = $query->fetch(\PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }

            return $data;
        }

        return false;
    }

    /**
     * Массово обновляет информацию о переводе денежных подарков в банк
     *
     * Используется при пакетной отправке денежных призов из cli (bin/cli.php)
     *
     * @param array $prizesId Массив id подарков для обновления
     * @return int|false Количество обновленных записей при успешной операции, иначе - false
     */
    public function updateTransferredCollection(array $prizesId) : int|false
    {
        $pdo = Database::getPDO();

        $query = $pdo->prepare('UPDATE `money_prizes` SET `transferred` = :transferred WHERE `prize_id` IN
            ('. implode(', ', $prizesId) .')');

        $query->execute(array('transferred' => 1, ));

        if ($rows = $query->rowCount()) {
            return $rows;
        }

        return false;
    }
}