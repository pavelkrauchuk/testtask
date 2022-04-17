<?php
/**
 * @author Pavel Krauchu <pavel.krauchuk@gmail.com>
 */

namespace Pavelkrauchuk\Testtask\Controller;

use JetBrains\PhpStorm\NoReturn;
use JetBrains\PhpStorm\Pure;
use Pavelkrauchuk\Testtask\Entity\User;
use Pavelkrauchuk\Testtask\Router;
use Pavelkrauchuk\Testtask\Security\Authenticator;
use Pavelkrauchuk\Testtask\View;

class DefaultController
{
    /** @var View Объект представления */
    private View $view;

    #[Pure]
    public function __construct()
    {
        $this->view = new View();
    }

    /**
     * Переход пользователя по неизвестному URL
     *
     * @return void
     */
    #[NoReturn]
    public function process404() : void
    {
        $this->view->render('message/404.php');
        die();
    }

    /**
     * Переход на главную страницу
     */
    public function getMainPage() : void
    {
        $this->view->render('main.php');
    }
    /**
     * Переход на страницу с формой авторизации
     *
     * @return void
     */
    public function getLoginPage() : void
    {
        $this->view->render('login/login_form.php');
    }

    /**
     * Проверка данных для входа пользователя
     *
     * @return void
     */
    public function authenticate() : void
    {
        if (!isset($_POST['login']) || !isset($_POST['password'])) {
            $this->process404();
        }

        $login = htmlentities($_POST['login']);
        $password = htmlentities($_POST['password']);

        $authenticator = new Authenticator();

        if ($authenticator->authenticate($login, $password)) {
            header('Location: /list');
        } else {
            $this->view->render('login/login_form.php', array(
                'message' => 'Неправильный логин и/или пароль',
                'posted' => array(
                    'login' => $login,
                ),
            ));
        }
    }

    /**
     * Выход пользователя из системы
     *
     * @return void
     */
    public function logout() : void
    {
        $user = new User();
        $user->logOut();
        $router = new Router();
        $router->get('/login');
    }
}