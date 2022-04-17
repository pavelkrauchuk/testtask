<?php
/**
 * @author Pavel Krauchuk <pavel.krauchuk@gmail.com>
 */

namespace Pavelkrauchuk\Testtask\Controller;

use Pavelkrauchuk\Testtask\Entity\Prize;
use Pavelkrauchuk\Testtask\Entity\User;
use Pavelkrauchuk\Testtask\Repository\PrizeRepository;
use Pavelkrauchuk\Testtask\Router;
use Pavelkrauchuk\Testtask\View;

class PrizeController
{
    /** @var Router */
    protected Router $router;

    public function __construct()
    {
        $this->router = new Router();
    }

    /**
     * Получение списка подарков пользователя
     *
     * @return void
     */
    public function getPrizesList() : void
    {
        $user = new User();
        $user->repository->getById($user);
        $data = array('bonus_account' => $user->getBonusAccount());

        $prizeRepository = new PrizeRepository();
        $data['prizes'] = $prizeRepository->getAllEntries();

        $view = new View();
        $view->render('prize/prize_list.php', $data);
    }

    /**
     * Получение пользователем случайного подарка
     *
     * @throws \Exception
     * @return void
     */
    public function getPrize() : void
    {
        header('Location: /list');
        $prize = new Prize();
        $prize->generate();
        $this->getPrizesList();
    }
}