<?php


namespace app\controllers;


use app\models\Breadcrumbs;
use app\models\Category;
use wfm\App;
use wfm\Pagination;
use function Couchbase\defaultDecoder;

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

        //пагинация
        $page = get('page');
        $countProducts = App::$app->getProperty('pagination');
        $total = $this->model->getCountProducts($ids);
        $pagination = new Pagination($page, $countProducts, $total);
        $start = $pagination->getStart();

        $products = $this->model->getProducts($ids, $lang, $start, $countProducts);
        $this->setMeta($category['title'], $category['description'], $category['keywords']);
        $this->set(compact('products', 'category', 'breadcrumbs', 'total', 'pagination'));

    }

}


