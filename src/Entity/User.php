<?php
/**
 * @author Pavel Krauchuk <pavel.krauchuk@gmail.com>
 */

namespace Pavelkrauchuk\Testtask\Entity;

use JetBrains\PhpStorm\Pure;
use Pavelkrauchuk\Testtask\Repository\UserRepository;

class User
{
    /** @var UserRepository Объект модели для операций с БД */
    public UserRepository $repository;

    /**
     * @var int|null $id Id пользователя в БД
     * @var string|null $name Имя пользователя
     * @var string|null $login Логин пользователя
     * @var int|null $bonusAccount Количество бонусных баллов в аккаунте пользователя
     */
    private int|null $id;
    private string|null $name;
    private string|null $login;
    private int|null $bonusAccount;

    #[Pure]
    public function __construct()
    {
        $this->id = $_SESSION['userId'] ?? null;
        $this->repository = new UserRepository();
    }

    /**
     * @return int|null
     */
    public function getId() : ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getLogin(): ?string
    {
        return $this->login;
    }

    /**
     * @param string|null $login
     */
    public function setLogin(?string $login): void
    {
        $this->login = $login;
    }

    /**
     * @return int|null
     */
    public function getBonusAccount(): ?int
    {
        return $this->bonusAccount;
    }

    /**
     * @param int|null $bonusAccount
     */
    public function setBonusAccount(?int $bonusAccount): void
    {
        $this->bonusAccount = $bonusAccount;
    }

    /**
     * @return bool
     */
    public function isLogged() : bool
    {
        if ($this->id === null) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Завершение сессии пользователя
     *
     * @return void
     */
    public function logOut() : void
    {
        unset($_SESSION[$this->id]);
        session_destroy();
    }
}