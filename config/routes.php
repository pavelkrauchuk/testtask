<?php
/**
 * Маршруты для объекта класса Router
 *
 * Структура массива:
 * 'запрошенный URL' => array(
 *      'class' => 'Название класса контроллера',
 *      'method' => 'Название метода контролера'
 * )
 */

return array(
    '/' => array(
        'class' => 'DefaultController',
        'method' => 'getMainPage'
    ),
    '/login' => array(
        'class' => 'DefaultController',
        'method' => 'getLoginPage'
    ),
    '/authenticate' => array(
        'class' => 'DefaultController',
        'method' => 'authenticate'
    ),
    '/list' => array(
        'class' => 'PrizeController',
        'method' => 'getPrizesList'
    ),
    '/logout' => array(
        'class' => 'DefaultController',
        'method' => 'logout'
    ),
    '/convertMoney' => array(
        'class' => 'MoneyPrizeController',
        'method' => 'convertInBonuses'
    ),
    '/transferMoney' => array(
        'class' => 'MoneyPrizeController',
        'method' => 'transferToBank'
    ),
    '/rejectMoney' => array(
        'class' => 'MoneyPrizeController',
        'method' => 'rejectMoney'
    ),
    '/admissBonus' => array(
        'class' => 'BonusPrizeController',
        'method' => 'admissToAccount'
    ),
    '/rejectBonus' => array(
        'class' => 'BonusPrizeController',
        'method' => 'rejectBonus'
    ),
    '/sendThing' => array(
        'class' => 'ThingPrizeController',
        'method' => 'sendThing'
    ),
    '/rejectThing' => array(
        'class' => 'ThingPrizeController',
        'method' => 'rejectThing'
    ),
    '/getPrize' => array(
        'class' => 'PrizeController',
        'method' => 'getPrize'
    ),
);