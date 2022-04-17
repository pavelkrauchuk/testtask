<?php
/**
 * @author Pavel Krauchuk <pavel.krauchuk@gmail.com>
 */

namespace Pavelkrauchuk\Testtask\Helper;

class BonusHelper
{
    /** @var int Максимальное количесво бонуссных баллов при генерации подарка */
    private const MAX_BONUS = 300;

    /**
     * Генерация случайной величины бонусных баллов для подарка с учетом MAX_BONUS
     *
     * @return int Сгенерированная величина бонуса
     * @throws \Exception
     */
    public static function getRandomBonusValue() : int
    {
        return random_int(1, self::MAX_BONUS);
    }
}