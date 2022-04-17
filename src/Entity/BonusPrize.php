<?php
/**
 * @author Pavel Krauchuk <pavel.krauchuk@gmail.com>
 */

namespace Pavelkrauchuk\Testtask\Entity;

use JetBrains\PhpStorm\Pure;
use Pavelkrauchuk\Testtask\Repository\BonusPrizeRepository;

class BonusPrize extends Prize
{
    /** @var BonusPrizeRepository Объект модели для операций с БД  */
    public BonusPrizeRepository $repository;

    /** @var string Тип подарка */
    protected string $type = 'bonus';

    /**
     * @var int|null $id Id подарка в базе данных
     * @var int|null $amount Количество бонусных баллов
     * @var int|null $admissed Зачислен ли подарок на бонусный счет пользователя (1 = да, 0 - нет)
     */
    private int|null $id;
    private int|null $amount;
    private int|null $admissed;

    /**
     * @param int|null $id
     * @param int|null $amount
     * @param int|null $admissed
     */
    #[Pure]
    public function __construct(int $id = null, int $amount = null, int $admissed = null)
    {
        $this->id = $id;
        $this->amount = $amount;
        $this->admissed = $admissed;

        $this->repository = new BonusPrizeRepository();
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
     * @param int|null $amount
     */
    public function setAmount(?int $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return int|null
     */
    public function getAdmissed(): ?int
    {
        return $this->admissed;
    }

    /**
     * @param int|null $admissed
     */
    public function setAdmissed(?int $admissed): void
    {
        $this->admissed = $admissed;
    }

    /**
     * @return bool
     */
    public function isAdmissed(): bool
    {
        return (bool) $this->getAdmissed();
    }

    /**
     * @return bool
     */
    public function isOperable() : bool
    {
        return (bool) !$this->getAdmissed();
    }
}