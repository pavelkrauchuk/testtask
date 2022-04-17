<?php
/**
 * @author Pavel Krauchuk <pavel.krauchuk@gmail.com>
 */

namespace Pavelkrauchuk\Testtask;

class View
{
    /**
     * Подключает необходимый шаблон представления
     *
     * @param string $template Путь к файлу без "templates/"
     * @param array|null $data Массив передаваемых в шаблон представления данных
     * @return void
     */
    public function render(string $template, array $data = null) : void
    {
        require 'templates/header.php';
        require 'templates/' . $template;
        require 'templates/footer.php';
    }
}