<?php


namespace wfm;

abstract class Controller
{
    public array $data = [];
    public array $meta = ['title' => '', 'keywords' => '', 'description' => ''];
    public false|string $layout = '';
    public string $view = '';
    public object $model;

    public function __construct(public $route = [])
    {
       // debug($this->route);
    }
    public function getModel()
    {
        $model = 'app\models\\' . $this->route['admin_prefix'] . $this->route['controller'];
        if(class_exists($model)) {
            $this->model = new $model();
        }
    }
    //записываются данные и через метод render передаются в View
    public function getView()
    {
        $this->view = $this->view ?: $this->route['action'];
        (new View($this->route, $this->layout, $this->view, $this->meta))->render($this->data);
    }
    public function set($data)
    {
        $this->data = $data;
    }
    public function setMeta($title = '', $description = '', $keywords = '')
    {
        $this->meta = [
            'title' =>$title,
            'description' => $description,
            'keywords' => $keywords,
        ];
    }
    //определяем был ли отправлен ajax запрос
    public function isAjax(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    public function loadView($view, $vars = [])
    {
        extract($vars);
        $prefix = str_replace('\\', '/', $this->route['admin_prefix']);
        require  APP . "/views/{$prefix}{$this->route['controller']}/{$view}.php";
        die;
       // $view_file = APP . "/views/{$prefix}{$this->route['controller']}/{$this->view}.php";
    }

    public function error_404($folder = 'Error', $view = 404, $response = 404)
    {
        http_response_code($response);
        $this->setMeta(___('tpl_error_404'));
        $this->route['controller'] = $folder;
        $this->view = $view;
    }
 }
 