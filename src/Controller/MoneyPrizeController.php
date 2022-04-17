<?php
/**
 * @author Pavel Krauchuk <pavel.krauchuk@gmail.com>
 */

namespace Pavelkrauchuk\Testtask\Controller;

use Pavelkrauchuk\Testtask\Entity\MoneyPrize;
use Pavelkrauchuk\Testtask\Entity\User;
use Pavelkrauchuk\Testtask\Helper\MoneyHelper;

class MoneyPrizeController extends PrizeController
{
    /** @var MoneyPrize|null Объект денежного подарка */
    private MoneyPrize|null $moneyPrize = null;

    /**
     * Конструктор контроллера денежных подарков
     *
     * Осуществляется валидация переданного в запросе Id подарка, создание объекта подарка, а затем проверка
     * принаблежности данного подарка авторизованному пользователю. При успешной проверке объект подарка сохраняется
     * в свойстве $moneyPrize класса
     */
    public function __construct()
    {
        parent::__construct();

        if (isset($this->router->getQuery()['id']) && is_numeric($this->router->getQuery()['id'])) {
            $this->moneyPrize = new MoneyPrize($this->router->getQuery()['id']);
            $this->moneyPrize = $this->moneyPrize->repository->getById($this->moneyPrize) ?: null;
        }
    }


    /**
     * Отказ пользователя от полученного подарка
     *
     * @return void
     */
    public function rejectMoney() : void
    {
        if ($this?->moneyPrize?->isOperable()) {
            $this->moneyPrize->repository->delete($this->moneyPrize);
        }

        $this->router->get('/list');
    }

    /**
     * Перевод денежного подарка пользователя в банк
     *
     * @return void
     */
    public function transferToBank() : void
    {
        if ($this?->moneyPrize?->isOperable()) {

            $json = json_encode($this->moneyPrize);
            $curl = curl_init('http://test/api.php');
            curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($json),
            ));

            $result = curl_exec($curl);

            $this->moneyPrize->setTransferred(1);
            $this->moneyPrize->repository->update($this->moneyPrize);
        }

        $this->router->get('/list');
    }

    /**
     * Конвертирование денежного подарка в бонусные баллы
     *
     * @return void
     */
    public function convertInBonuses() : void
    {
        if ($this?->moneyPrize?->isOperable()) {
            $bonus = MoneyHelper::convertToBonus($this->moneyPrize);

            $user = new User();
            $user->repository->getById($user);
            $user->setBonusAccount($user->getBonusAccount() + $bonus);
            $user->repository->update($user);

            $this->moneyPrize->setConverted(1);
            $this->moneyPrize->repository->update($this->moneyPrize);
        }

        $this->router->get('/list');
    }
}