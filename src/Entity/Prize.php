<?php
/**
 * @author Pavel Krauchuk <pavel.krauchuk@gmail.com>
 */

namespace Pavelkrauchuk\Testtask\Entity;

use Pavelkrauchuk\Testtask\Helper\MoneyHelper;
use Pavelkrauchuk\Testtask\Helper\ThingHelper;

class Prize
{
    /** @var string Тип подарка, используется только в дочерних классах */
    protected string $type = 'basic';

    /**
     * Массив типов подарка, на основе которого генерируется случайный подарок. Содержит начальное значение "bonus",
     * так как подарки данного типа всегда доступны.
     *
     * @var array|string[]
     */
    private array $types = array('bonus');

    /**
     * Плучение доступных для генерации типов подарка
     *
     * @return void
     */
    public function getAvailableTypes() : void
    {
        if (MoneyHelper::isAvailable()) {
            $this->types[] = 'money';
        }

        if (ThingHelper::isAvailable()) {
            $this->types[] = 'thing';
        }
    }

    /**
     * Генерирует случайный подарок из доступных типов
     *
     * @return void
     * @throws \Exception
     */
    public function generate() : void
    {
        $this->getAvailableTypes();
        $randomInt = random_int(0, count($this->types) - 1);
        $type = $this->types[$randomInt];

        switch ($type) {
            case 'money':
                $moneyPrize = new MoneyPrize();
                $moneyPrize->repository->add($moneyPrize);
                break;
            case 'thing':
                $thingPrize = new ThingPrize();
                $thingPrize->repository->add($thingPrize);
                break;
            case 'bonus':
                $bonusPrize = new BonusPrize();
                $bonusPrize->repository->add($bonusPrize);
                break;
        }
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}