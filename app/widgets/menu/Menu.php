<?php

namespace app\widgets\menu;
use RedBeanPHP\R;
use wfm\App;


class Menu
{
    //данные полученные из БД
    protected $data;
    //дерево кот формируется из полученных данных
    protected $tree;
    //HTML код сформ меню
    protected $menuHtml;
    //шаблон
    protected $tpl;
    //обертка для меню, теги
    protected $container = 'ul';
    //классы для меню(для CSS. JS)
    protected $class;
    //кол-во времени хранения кэша
    protected $cache = 3600;
    //ключ кэширования меню
    protected $cacheKey = 'ishop_menu';
    //аттрибуты для класса меню
    protected $attrs = [];
    //св-во отв за код который будет перед меню(больше на будущее)
    protected $prepend = '';
    //язык меню
    protected $language;

    public function __construct($options = [])
    {
        $this->language = \wfm\App::$app->getProperty('language');
        $this->tpl = __DIR__ . '/menu_tpl.php';
        $this->getOptions($options);
        $this->run();
    }


    protected function getOptions($options){
        foreach($options as $k => $v){
            if(property_exists($this, $k)){
                $this->$k = $v;
            }
        }
    }

    protected function run()
    {
        $cache = \wfm\Cache::getInstance();
        //если есть меню в кэше то берем его из кэша
        $this->menuHtml = $cache->get("{$this->cacheKey}_{$this->language['code']}");

        if(!$this->menuHtml){
//            $this->data = R::getAssoc("SELECT c.*, cd.* FROM category c
//                        JOIN category_description cd
//                        ON c.id = cd.category_id
//                        WHERE cd.language_id = ?", [$this->language['id']]);
            $this->data = App::$app->getProperty("categories_{$this->language['code']}");

            $this->tree = $this->getTree();
            $this->menuHtml = $this->getMenuHtml($this->tree);
            if($this->cache){
                $cache->set("{$this->cacheKey}_{$this->language['code']}", $this->menuHtml, $this->cache);
            }
        }
        $this->output();
    }

    protected function getTree()
    {
        $tree = [];
        $data = $this->data;
        foreach ($data as $id => &$node) {
            if (!$node['parent_id']){
                $tree[$id] = &$node;
            } else {
                $data[$node['parent_id']]['children'][$id] = &$node;
            }
        }
        return $tree;
    }
    protected function getMenuHtml($tree, $tab = ''){
        $str = '';
        foreach($tree as $id => $category){
            $str .= $this->catToTemplate($category, $tab, $id);
        }
        return $str;
    }

    protected function catToTemplate($category, $tab, $id){
        ob_start();
        require $this->tpl;
        return ob_get_clean();
    }

    protected function output(){
        $attrs = '';
        if(!empty($this->attrs)){
            foreach($this->attrs as $k => $v){
                $attrs .= " $k='$v' ";
            }
        }
        echo "<{$this->container} class='{$this->class}' $attrs>";
        echo $this->prepend;
        echo $this->menuHtml;
        echo "</{$this->container}>";
    }

}