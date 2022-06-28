<?php
namespace app\controllers;

use app\models\Search;
use wfm\App;
use wfm\Pagination;

/** @property Search $model */
class SearchController extends AppController
{
    public function indexAction()
    {
        $s = get('s', 's');
        $lang = App::$app->getProperty('language');
        $page = get('page');
        $countProducts = App::$app->getProperty('pagination');

        $total = $this->model->getCountFindProducts($s, $lang);
        $pagination = new Pagination($page, $countProducts, $total);
        $start = $pagination->getStart();

        $products = $this->model->getFindProducts($s, $lang, $start, $countProducts);
        $this->setMeta('tpl_search_title');
        $this->set(compact('s', 'products', 'pagination', 'total'));
    }
}