<?php
/**
 * @author Pavel Krauchuk <pavel.krauchuk@gmail.com>
 */

namespace Pavelkrauchuk\Testtask\Repository;

use Pavelkrauchuk\Testtask\Database;
use Pavelkrauchuk\Testtask\Entity\User;

class UserRepository
{
    /**
     * Получение информации о пользователе по его Id
     *
     * @param User $user Объект пользователя, должен иметь устаналенное свойство Id
     * @return User|false Объект с полученными данными, в случае отсутствия в БД записи - false
     */
    public function getById(User $user) : User|false
    {
        $pdo = Database::getPDO();
        $query = $pdo->prepare('SELECT `name`, `login`, `bonus_account` FROM `users` WHERE `id` = :userId');
        $query->execute(array('userId' => $user->getId()));

        if ($query->rowCount()) {
            $data = $query->fetch(\PDO::FETCH_ASSOC);
            $user->setName($data['name']);
            $user->setLogin($data['login']);
            $user->setBonusAccount($data['bonus_account']);

            return $user;
        }

        return false;
    }

    /**
     * Обновление информации о бонусном счете пользователя
     *
     * @param User $user Объект обновляемого пользователя, должен иметь устаналенное свойство Id
     * @return bool Возвращает true при успешном обновлении, иначе - false
     */
    public function update(User $user) : bool
    {
        $pdo = Database::getPDO();
        $query = $pdo->prepare('UPDATE `users` SET `bonus_account` = :bonus WHERE `id` = :userId');
        $result = $query->execute(array('bonus' => $user->getBonusAccount(), 'userId' => $user->getId()));

        if ($result) {
            return true;
        }

        return false;
    }
}