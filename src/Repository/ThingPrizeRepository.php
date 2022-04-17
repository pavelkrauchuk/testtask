<?php
/**
 * @author Pavel Krauchuk <pavel.krauchuk@gmail.com>
 */

namespace Pavelkrauchuk\Testtask\Repository;

use Pavelkrauchuk\Testtask\Database;
use Pavelkrauchuk\Testtask\Entity\ThingPrize;
use Pavelkrauchuk\Testtask\Helper\ThingHelper;

class ThingPrizeRepository extends PrizeRepository
{
    /**
     * Получение данных о подарке-предмете по его Id
     *
     * Метод осуществляет добор из БД дополнительных данных о подарке, зачастую необходимых при других операциях с
     * объектом в контроллерах, дополнительно проводится проверка, является ли объект подарка с таким Id
     * принадлежащим пользователю, от аккаунта которого осуществляются действия
     *
     * @param ThingPrize $thingPrize Объект подарка, о котором запрашиваются данные. Должен иметь установленное
     * свойство Id
     * @return ThingPrize|false Объект с полученными данными, в случае отсутствия в БД записи - false
     */
    public function getById(ThingPrize $thingPrize) : ThingPrize|false
    {
        if (!$this->user->getId()) {
            return false;
        }

        $pdo = Database::getPDO();
        $query = $pdo->prepare('SELECT * FROM `thing_prizes` LEFT JOIN `prizes_received` ON
            `thing_prizes`.`prize_id` = `prizes_received`.`id` WHERE `thing_prizes`.`prize_id` = :prizeId AND 
            `prizes_received`.`user_id` = :userId');
        $query->execute(array('prizeId' => $thingPrize->getId(), 'userId' => $this->user->getId()));

        if ($query->rowCount()) {
            $data = $query->fetch(\PDO::FETCH_ASSOC);

            $thingPrize->setName($data['name']);
            $thingPrize->setShipped($data['shipped']);

            return $thingPrize;
        }

        return false;
    }

    /**
     * Добавление в БД новой записи о полученном пользователем подарке-предмете
     *
     * @param ThingPrize $thingPrize Объект, информация о котором добавляется в БД. Единственным свойством объекта,
     * значение которого может быть записано в БД является "shipped". Иные свойства, например, "name"
     * (генерируется динамически) или "id" (используется значение БД) будут проигнорированы.
     * @return bool При успешном добавлении записи возвращается true, иначе - false
     */
    public function add(ThingPrize $thingPrize): bool
    {
        if (!ThingHelper::isAvailable()) {
            return false;
        }

        $randomThing = ThingHelper::getRandomThingValue();
        $rowId = $this->addNewEntry($thingPrize);
        if ($rowId) {
            $pdo = Database::getPDO();
            $query = $pdo->prepare('INSERT INTO `thing_prizes` VALUES(:prizeId, :name, :shipped)');
            $result = $query->execute(array(
                'prizeId' => $rowId,
                'name' => $randomThing['name'],
                'shipped' => $thingPrize->getShipped() ?? 0,
            ));

            if ($result) {
                ThingHelper::deleteFromAvailable($randomThing['id']);
                return true;
            }
        }

        return false;
    }

    /**
     * Удаление информации из БД при отказе пользователя от подарка-предмета
     *
     * @param ThingPrize $thingPrize Удаляемый объект, должен иметь установленное свойство Id
     * @return bool При успешном удалении возвращается true, иначе - false
     */
    public function delete(ThingPrize $thingPrize) : bool
    {

        $pdo = Database::getPDO();
        $query = $pdo->prepare('DELETE FROM `thing_prizes` WHERE `prize_id` = :prizeId');
        $result = $query->execute(array('prizeId' => $thingPrize->getId()));

        if ($result) {
            $this->deleteEntry($thingPrize);
            ThingHelper::insertToAvailable($thingPrize->getName());
            return true;
        }

        return false;
    }

    /**
     * Обновление информации о подарке-предмете в БД
     *
     * @param ThingPrize $thingPrize Объект подарка, должен иметь установленные свойства "shipped" и "id"
     * @return bool При успешном обновлении возвращается true, иначе - false
     */
    public function update(ThingPrize $thingPrize) : bool
    {
        $pdo = Database::getPDO();
        $query = $pdo->prepare('UPDATE `thing_prizes` SET `shipped` = :shipped WHERE `prize_id` = :prizeId');
        $result = $query->execute(array(
            'shipped' => $thingPrize->getShipped(),
            'prizeId' => $thingPrize->getId(),
        ));

        if ($result) {
            return true;
        }

        return false;
    }
}