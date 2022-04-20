<?php
namespace app\controllers;

use app\models\Product;
use wfm\App;
use wfm\Controller;

/** @property Product $model */
class ProductController extends AppController
{

    public function viewAction()
    {
        $lang = App::$app->getProperty('language');
        $product = $this->model->get_product($this->route['slug'], $lang);
        if(!$product){
            throw  new \Exception("Товар {$this->route['slug']} не найден", 404);
        }
        $gallery = $this->model->get_gallery($product['id']);
        $this->setMeta($product['title'], $product['description'], $product['keywords']);
        $this->set(compact('product', 'gallery'));
    }
}

//посмотрел 19 урок