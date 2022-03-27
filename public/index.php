<?php


require_once dirname(__DIR__) . '/config/init.php';
require_once HELPERS . '/functions.php';
require_once CONFIG . '/routes.php';


new \wfm\App();
//throw new Exception('Ошибка');

//debug(\wfm\Router::getRoutes());