<?php
/**
 * @author Pavel Krauchuk <pavel.krauchuk@gmail.com>
 */

namespace Pavelkrauchuk\Testtask\Security;

use Pavelkrauchuk\Testtask\Database;

class Authenticator
{
    /**
     * Проверка введенных пользователем логина и пароля на соответствие хранящимся в БД
     *
     * @param string $login Логин пользователя
     * @param string $password Пароль пользователя
     * @return bool Возвращает true при успешной аутентификации, иначе - false
     */
    public function authenticate(string $login, string $password) : bool
    {
        $pdo = Database::getPDO();
        $query = $pdo->prepare('SELECT * FROM `users` WHERE `login` = :login');
        $query->execute(array('login' => $login));

        if ($query->rowCount()) {
            $data = $query->fetch(\PDO::FETCH_ASSOC);

            if (password_verify($password, $data['password_hash'])) {
                $_SESSION['userId'] = $data['id'];
                return true;
            }
        }

        return false;
    }
}