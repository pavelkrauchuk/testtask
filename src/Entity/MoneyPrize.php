<?php
/**
 * @author Pavel Krauchuk <pavel.krauchuk@gmail.com>
 */

namespace Pavelkrauchuk\Testtask\Entity;

use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use Pavelkrauchuk\Testtask\Repository\MoneyPrizeRepository;

class MoneyPrize extends Prize implements \JsonSerializable
{
    /** @var MoneyPrizeRepository Объект модели для операций с БД */
    public MoneyPrizeRepository $repository;

    /** @var string Тип подарка */
    protected string $type = 'money';

    /**
     * @var int|null $id Id подарка в базе данных
     * @var int|null $amount Количество денег
     * @var int|null $converted Конвертирован ли в бонусные баллы (1 = да, 0 - нет)
     * @var int|null $transferred Переведен ли на счет пользователя в банке (1 = да, 0 - нет)
     */
    private int|null $id;
    private int|null $amount;
    private int|null $converted;
    private int|null $transferred;

    /**
     * @param int|null $id
     * @param int|null $amount
     * @param int|null $converted
     * @param int|null $transferred
     */
    #[Pure]
    public function __construct(int $id = null, int $amount = null, int $converted = null, int $transferred = null)
    {
        $this->id = $id;
        $this->amount = $amount;
        $this->converted = $converted;
        $this->transferred = $transferred;

        $this->repository = new MoneyPrizeRepository();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return int|null
     */
    public function getAmount(): ?int
    {
        return $this->amount;
    }

    /**
     * @param int $amount
     */
    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return int|null
     */
    public function getConverted(): ?int
    {
        return $this->converted;
    }

    /**
     * @param int $converted
     */
    public function setConverted(int $converted): void
    {
        $this->converted = $converted;
    }

    /**
     * @return int|null
     */
    public function getTransferred(): ?int
    {
        return $this->transferred;
    }

    /**
     * @param int $transferred
     */
    public function setTransferred(int $transferred): void
    {
        $this->transferred = $transferred;
    }

    /**
     * Проверяет был ли конвертирован подарок в бонусные баллы
     *
     * @return bool
     */
    #[Pure]
    public function isConverted() : bool
    {
        return (bool) $this->getConverted();
    }

    /**
     * Проверяет был ли переведен подарок на счет пользователя в банке
     *
     * @return bool
     */
    #[Pure]
    public function isTransferred() : bool
    {
        return (bool) $this->getTransferred();
    }

    /**
     * Проверяет доступность подарка для каких-либо действий с ним
     *
     * Подарок считается доступным для действий, если пользователь не конвертировал его в бонусные баллы или
     * не перечислил на счет в банке
     *
     * @return bool
     */
    #[Pure]
    public function isOperable() : bool
    {
        return !$this->isConverted() && !$this->isTransferred();
    }

    /**
     * Реализация метода для преобразования объекта в json при отправке на счет пользователя в банк
     *
     * @return array
     */
    #[Pure]
    #[ArrayShape(['id' => "int|null", 'type' => "string", 'amount' => "int|null"])]
    public function jsonSerialize() : array
    {
        return array(
            'id' => $this->getId(),
            'type' => $this->getType(),
            'amount' => $this->getAmount(),
        );
    }
}