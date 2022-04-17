<?php
/**
 * @author Pavel Krauchuk <pavel.krauchuk@gmail.com>
 */

namespace Pavelkrauchuk\Testtask\Controller;

use Pavelkrauchuk\Testtask\Entity\ThingPrize;
use Pavelkrauchuk\Testtask\View;

class ThingPrizeController extends PrizeController
{
    /**
     * @var ThingPrize|null Объект подарка
     */
    private ThingPrize|null $thingPrize = null;

    /**
     * Конструктор контроллера подарков-предметов
     *
     * Осуществляется валидация переданного в запросе Id подарка, создание объекта подарка, а затем проверка
     * принаблежности данного подарка авторизованному пользователю. При успешной проверке объект подарка сохраняется
     * в свойстве $thingPrize класса
     */
    public function __construct()
    {
        parent::__construct();
        if (isset($this->router->getQuery()['id']) && is_numeric($this->router->getQuery()['id'])) {
            $this->thingPrize = new ThingPrize($this->router->getQuery()['id']);
            $this->thingPrize = $this->thingPrize->repository->getById($this->thingPrize) ?: null;
        }
    }

    /**
     * Отказ пользователя от полученного подарка
     *
     * @return void
     */
    public function rejectThing() : void
    {
        if ($this?->thingPrize?->isOperable()) {
            $this->thingPrize->repository->delete($this->thingPrize);
        }

        $this->router->get('/list');
    }

    /**
     * Отправка подарка пользователю по почте
     *
     * @return void
     */
    public function sendThing() : void
    {
        if ($this?->thingPrize?->isOperable()) {

            $this->thingPrize->setShipped(1);
            $this->thingPrize->repository->update($this->thingPrize);

            $view = new View();
            $view->render('message/by_post.php');
        }
    }
}