<?php
/**
 * @author Pavel Krauchuk <pavel.krauchuk@gmail.com>
 */

namespace Pavelkrauchuk\Testtask\Helper;

use JetBrains\PhpStorm\Pure;
use Pavelkrauchuk\Testtask\Database;
use Pavelkrauchuk\Testtask\Entity\MoneyPrize;
use Pavelkrauchuk\Testtask\Entity\User;

class MoneyHelper
{
    /** @var int Максимально возможное количество денег при генерации денежного подарка */
    private const MAX_MONEY = 300;

    /** @var float Коэффициент конверсии денег в бонусные баллы */
    private const CONVERSION_RATE = 1.87;

    /** @var int Доступная для генерации денежных подарков сумма */
    private static int $availableMoney;

    /**
     * Проверка наличия денег для генерации подарка
     *
     * Получает значение параметра "available_money" из таблицы "settings", при наличии положительного значения
     * возвращает true
     *
     * @return bool
     */
    public static function isAvailable() : bool
    {
        $pdo = Database::getPDO();
        $query = $pdo->prepare("SELECT * FROM `settings` WHERE `param` = 'available_money'");
        $query->execute();

        $data = $query->fetch(\PDO::FETCH_ASSOC);
        if (isset($data['value']) && $data['value'] > 0) {
            self::$availableMoney = $data['value'];
            return true;
        }

        return false;
    }

    /**
     * Генерирует случайное количесто денег для денежного подарка
     *
     * При генерации учитывается максимальное возможное значение денежного приза (MAX_MONEY) и доступное в настоящее
     * время количество денег ($availableMoney)
     *
     * @return int Сгенерированная сумма денег
     * @throws \Exception
     */
    public static function getRandomMoneyValue() : int
    {
        $randomMoney = random_int(1, self::MAX_MONEY);
        return min($randomMoney, self::$availableMoney);
    }

    /**
     * Уменьшает количество доступных денег ("available_money" в "settings") после получения пользователем подарка
     *
     * @param MoneyPrize $moneyPrize
     * @return void
     */
    public static function decreaseAvailableMoney(MoneyPrize $moneyPrize) : void
    {
        $pdo = Database::getPDO();
        $query = $pdo->prepare("UPDATE `settings` SET `value`=:newMoney WHERE `param` = 'available_money'");
        $query->execute(array('newMoney' => (self::$availableMoney - $moneyPrize->getAmount())));
    }

    /**
     * Увеличивает количество доступных денег ("available_money" в "settings") при отказе пользователя от подарка
     *
     * @param MoneyPrize $moneyPrize
     * @return void
     */
    public static function increaseAvailableMoney(MoneyPrize $moneyPrize) : void
    {
        $pdo = Database::getPDO();
        $query = $pdo->prepare("UPDATE `settings` SET `value`= `value`+:addMoney WHERE `param` = 'available_money'");
        $query->execute(array('addMoney' => $moneyPrize->getAmount()));
    }

    /**
     * Осуществляет конвертацию денег в бонусные баллы с учетом коэффициента
     *
     * @param MoneyPrize $moneyPrize
     * @return int
     */
    #[Pure]
    public static function convertToBonus(MoneyPrize $moneyPrize) : int
    {
        return round($moneyPrize->getAmount() * self::CONVERSION_RATE);
    }
}