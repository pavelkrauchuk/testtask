<?php
/**
 * @author Pavel Krauchuk <pavel.krauchuk@gmail.com>
 */

namespace Pavelkrauchuk\Testtask\Controller;

use Pavelkrauchuk\Testtask\Entity\BonusPrize;
use Pavelkrauchuk\Testtask\Entity\User;

class BonusPrizeController extends PrizeController
{
    /** @var BonusPrize|null Объект бонусного подарка */
    private BonusPrize|null $bonusPrize = null;

    /**
     * Конструктор контроллера бонусных подарков
     *
     * Осуществляется валидация переданного в запросе Id подарка, создание объекта подарка, а затем проверка
     * принаблежности данного подарка авторизованному пользователю. При успешной проверке объект подарка сохраняется
     * в свойстве $bonusPrize класса
     */
    public function __construct()
    {
        parent::__construct();
        if (isset($this->router->getQuery()['id']) && is_numeric($this->router->getQuery()['id'])) {
            $this->bonusPrize = new BonusPrize($this->router->getQuery()['id']);
            $this->bonusPrize = $this->bonusPrize->repository->getById($this->bonusPrize) ?: null;
        }
    }

    /**
     * Отказ пользователя от полученного подарка
     *
     * @return void
     */
    public function rejectBonus() : void
    {
        if ($this?->bonusPrize?->isOperable()) {
            $this->bonusPrize->repository->delete($this->bonusPrize);
        }

        $this->router->get('/list');
    }

    /**
     * Зачисление полученных бонусных баллов на счет пользователя
     *
     * @return void
     */
    public function admissToAccount() : void
    {
        if ($this?->bonusPrize?->isOperable()) {
            $user = new User();
            $user = $user->repository->getById($user);
            $user->setBonusAccount($user->getBonusAccount() + $this->bonusPrize->getAmount());
            $user->repository->update($user);

            $this->bonusPrize->setAdmissed(1);
            $this->bonusPrize->repository->update($this->bonusPrize);
        }

        $this->router->get('/list');
    }

}