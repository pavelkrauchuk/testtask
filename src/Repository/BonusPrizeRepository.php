<?php
/**
 * @author Pavel Krauchuk <pavel.krauchuk@gmail.com>
 */

namespace Pavelkrauchuk\Testtask\Repository;

use Pavelkrauchuk\Testtask\Database;
use Pavelkrauchuk\Testtask\Entity\BonusPrize;
use Pavelkrauchuk\Testtask\Helper\BonusHelper;

class BonusPrizeRepository extends PrizeRepository
{
    /**
     * Получение данных о бонусном подарке по его Id
     *
     * Метод осуществляет добор из БД дополнительных данных о подарке, зачастую необходимых при других операциях с
     * объектом в контроллерах, дополнительно проводится проверка, является ли объект подарка с таким Id
     * принадлежащим пользователю, от аккаунта которого осуществляются действия
     *
     * @param BonusPrize $bonusPrize Объект подарка, о котором запрашиваются данные. Должен иметь установленное
     * свойство Id
     * @return BonusPrize|false Объект с полученными данными, в случае отсутствия в БД записи - false
     */
    public function getById(BonusPrize $bonusPrize) : BonusPrize|false
    {
        if (!$this->user->getId()) {
            return false;
        }

        $pdo = Database::getPDO();
        $query = $pdo->prepare('SELECT * FROM `bonus_prizes` LEFT JOIN `prizes_received` ON
            `bonus_prizes`.`prize_id` = `prizes_received`.`id` WHERE `bonus_prizes`.`prize_id` = :prizeId AND 
            `prizes_received`.`user_id` = :userId');
        $query->execute(array('prizeId' => $bonusPrize->getId(), 'userId' => $this->user->getId()));

        if ($query->rowCount()) {
            $data = $query->fetch(\PDO::FETCH_ASSOC);

            $bonusPrize->setAmount($data['amount']);
            $bonusPrize->setAdmissed($data['admissed']);

            return $bonusPrize;
        }

        return false;
    }

    /**
     * Добавление в БД новой записи о полученном пользователем бонусном подарке
     *
     * @param BonusPrize $bonusPrize Объект, информация о котором добавляется в БД. Единственным свойством объекта,
     * значение которого может быть записано в БД является "admissed". Иные свойства, например, "amount"
     * (генерируется динамически) или "id" (используется значение БД) будут проигнорированы.
     * @return bool При успешном добавлении записи возвращается true, иначе - false
     * @throws \Exception
     */
    public function add(BonusPrize $bonusPrize): bool
    {
        $rowId = $this->addNewEntry($bonusPrize);
        if ($rowId) {
            $pdo = Database::getPDO();
            $query = $pdo->prepare('INSERT INTO `bonus_prizes` VALUES(:prizeId, :amount, :admissed)');
            $result = $query->execute(array(
                'prizeId' => $rowId,
                'amount' => BonusHelper::getRandomBonusValue(),
                'admissed' => $bonusPrize->getAdmissed() ?? 0,
            ));

            if ($result) {
                return true;
            }
        }

        return false;
    }

    /**
     * Удаление информации из БД при отказе пользователя от бонусного подарка
     *
     * @param BonusPrize $bonusPrize Удаляемый объект, должен иметь установленное свойство Id
     * @return bool При успешном удалении возвращается true, иначе - false
     */
    public function delete(BonusPrize $bonusPrize) : bool
    {
        $pdo = Database::getPDO();
        $query = $pdo->prepare('DELETE FROM `bonus_prizes` WHERE `prize_id` = :prizeId');
        $result = $query->execute(array('prizeId' => $bonusPrize->getId()));

        if ($result) {
            $this->deleteEntry($bonusPrize);
            return true;
        }

        return false;
    }

    /**
     * Обновление информации о бонусном подарке в БД
     *
     * @param BonusPrize $bonusPrize Объект подарка, должен иметь установленные свойства "admissed" и "id"
     * @return bool При успешном обновлении возвращается true, иначе - false
     */
    public function update(BonusPrize $bonusPrize) : bool
    {

        $pdo = Database::getPDO();
        $query = $pdo->prepare('UPDATE `bonus_prizes` SET `admissed` = :admissed
            WHERE `prize_id` = :prizeId');
        $result = $query->execute(array(
            'admissed' => $bonusPrize->getAdmissed(),
            'prizeId' => $bonusPrize->getId(),
        ));

        if ($result) {
            return true;
        }

        return false;
    }
}