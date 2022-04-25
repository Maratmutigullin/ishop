<?php


namespace app\controllers;


use app\models\Breadcrumbs;
use app\models\Category;
use wfm\App;

/** @property Category $model */
class CategoryController extends AppController
{

    public function viewAction()
    {
        $lang = App::$app->getProperty('language');
        $category = $this->model->getCategory($this->route['slug'], $lang);

        if (!$category) {
            $this->error_404();
            return;
        }

        $breadcrumbs = Breadcrumbs::getBreadcrumbs($category['id']);
        $ids = $this->model->getIds($category['id']);
        $ids = !$ids ? $category['id'] : $ids . $category['id'];
        $products = $this->model->getProducts($ids, $lang);
        $this->setMeta($category['title'], $category['description'], $category['keywords']);
        $this->set(compact('products', 'category', 'breadcrumbs'));
    }

}

//посмотрел 22 урок