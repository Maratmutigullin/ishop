<?php
namespace app\widgets\language;

use RedBeanPHP\R;
use wfm\App;

class Language
{
    protected $tpl;
    protected $languages;
    protected $language;

    public function __construct()
    {
        $this->tpl = __DIR__ . '/lang_tpl.php';
        $this->run();
    }

    protected function run()
    {
        $this->languages = App::$app->getProperty('languages');
        $this->language = App::$app->getProperty('language');

        echo $this->getHtml();
    }
    //возвращаем все языки
    public static function getLanguages(): array
    {
        return R::getAssoc("SELECT code, title, base, id FROM language ORDER BY  base DESC");
    }
    public static function getLanguage($languages)
    {
        //получаем текуший код языка из url
        $lang = App::$app->getProperty('lang');
         if($lang && array_key_exists($lang, $languages)){
             $key = $lang;
             //debug($key);
         }elseif (!$lang){
             //берем текущий ключ массива. язык по умолчанию
             $key = key($languages);
         }else{
             $lang = h($lang);
             throw new \Exception("Not found {$lang}", 404);
         }
         $lang_info = $languages[$key];
         $lang_info['code'] = $key;

         return $lang_info;
    }

    protected function getHtml(): string
    {
        ob_start();
        require_once $this->tpl;
        return ob_get_clean();
    }
}