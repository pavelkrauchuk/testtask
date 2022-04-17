<?php
/**
 * @author Pavel Krauchuk <pavel.krauchuk@gmail.com>
 */

namespace Pavelkrauchuk\Testtask\Entity;

use JetBrains\PhpStorm\Pure;
use Pavelkrauchuk\Testtask\Repository\ThingPrizeRepository;

class ThingPrize extends Prize
{
    /** @var ThingPrizeRepository Объект модели для операций с БД */
    public ThingPrizeRepository $repository;

    /** @var string Тип подарка */
    protected string $type = 'thing';

    /**
     * @var int|null $id Id подарка в базе данных
     * @var int|null $name Название предмета
     * @var int|null $shipped Отправлен ли по пользователю по почте (1 = да, 0 - нет)
     */
    private int|null $id;
    private string|null $name;
    private int|null $shipped;

    /**
     * @param int|null $id
     * @param string|null $name
     * @param int|null $shipped
     */
    #[Pure]
    public function __construct(int $id = null, string $name = null, int $shipped = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->shipped = $shipped;

        $this->repository = new ThingPrizeRepository();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
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
     * @return int|null
     */
    public function getShipped(): ?int
    {
        return $this->shipped;
    }

    /**
     * @param int|null $shipped
     */
    public function setShipped(?int $shipped): void
    {
        $this->shipped = $shipped;
    }

    /**
     * Проверет был ли отправлен подарок пользователю по почте
     *
     * @return bool
     */
    #[Pure]
    public function isShipped() : bool
    {
        return (bool) $this->getShipped();
    }

    /**
     * Проверят доступен ли подарок для каких-либо действий
     *
     * Подарок считается доступным для действий если он не был выслан пользователю по почте
     *
     * @return bool
     */
    #[Pure]
    public function isOperable() : bool
    {
        return (bool) !$this->getShipped();
    }
}