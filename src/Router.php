<?php
/**
 * @author Pavel Krauchuk <pavel.krauchuk@gmail.com>
 */

namespace Pavelkrauchuk\Testtask;

use Pavelkrauchuk\Testtask\Controller\DefaultController;

class Router
{
    /**
     * @var string|mixed $path Запрошенный пользователем URL
     * @var array|null $query Параметры запроса
     * @var array $routes Массив маршрутов приложения
     */
    private string $path;
    private array|null $query;
    private array $routes;

    public function __construct()
    {
        $routes = include "config/routes.php";
        $this->routes = $routes;

        $parsedUri = parse_url($_SERVER['REQUEST_URI']);
        $this->path = $parsedUri['path'];
        
        if (isset($parsedUri['query'])) {
            parse_str($parsedUri['query'], $this->query);
        } else {
            $this->query = null;
        }
    }

    /**
     * @return mixed|string
     */
    public function getPath(): mixed
    {
        return $this->path;
    }

    /**
     * @return array|null
     */
    public function getQuery(): ?array
    {
        return $this->query;
    }

    /**
     * Подключает необходимый маршрут, в зависимости от запрошенного пользователем URL
     *
     * @param string|null $path Необязательный параметр, для принудительного перенаправления по указанный URL
     * @param array|null $query
     * @return void
     */
    public function get(string $path = null, array $query = null) : void
    {
        if ($path !== null) {
            $this->path = $path;
            $this->query = $query ?? null;
        }

        $route = $this->routes[$this->path] ?? null;
        if (is_array($route)) {
            $class = "\\Pavelkrauchuk\Testtask\Controller\\" . $route['class'];
            $method = $route['method'];
            $controller = new $class();
            $controller->$method($this->path, $this->query);
        } else {
            $controller = new DefaultController();
            $controller->process404();
        }
    }
}