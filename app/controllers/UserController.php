<?php


namespace app\controllers;

use app\models\User;
use PHPMailer\PHPMailer\Exception;
use wfm\App;
use wfm\Pagination;

/** @property User $model */
class UserController extends AppController
{
    //учетные данные
    public function credentialsAction()
    {
        if (!User::checkAuth()) {
            redirect(base_url() . 'ueser/login');
        }
        $this->model->load();
        if (empty($_POST)) {
            if (empty($this->model->attributes['password'])) {
                unset($this->model->attributes['password']);
            }
            if (empty($this->model->attributes['email'])) {
                unset($this->model->attributes['email']);
            }
            if (!$this->model->validate($this->model->attributes)) {
                $this->model->getErrors();

            } else {
                if (!empty($this->model->attributes['password'])) {
                    $this->model->attributes['password'] = password_hash($this->model->attributes['password'], PASSWORD_DEFAULT);
                }
                //обновляем данные о пользователе
                if ($this->model->update('user', $_SESSION['user']['id'])) {
                    $_SESSION['success'] = ___('user_credentials_success');
                    foreach ($this->model->attributes as $k => $v) {
                        if (!empty($v) && $k != 'password') {
                            $_SESSION['user'][$k] = $v;
                        }
                    }
                } else {
                    $_SESSION['errors'] = ___('user_credentials_error');

                }
            }
            redirect();
        }
        $this->setMeta(___('user_credentials_title'));
    }

    public function signupAction()
    {
        if (User::checkAuth()) {
            redirect(base_url());
        }
        if (!empty($_POST)) {
            $this->model->load();
            if (!$this->model->validate($this->model->attributes) || !$this->model->checkUnique()) {
                $this->model->getErrors();
                $_SESSION['form_data'] = $this->model->attributes;
            } else {
                $this->model->attributes['password'] = password_hash($this->model->attributes['password'], PASSWORD_DEFAULT);
                if ($this->model->save('user')) {
                    $_SESSION['success'] = ___('user_signup_success_register');
                } else {
                    $_SESSION['errors'] = ___('user_signup_error_register');

                }
            }
            redirect();
        }
        $this->setMeta(___('tpl_signup'));
    }

    public function loginAction()
    {
        if (User::checkAuth()) {
            redirect(base_url());
        }

        if (!empty($_POST)) {
            if ($this->model->login()) {
                $_SESSION['success'] = ___('user_login_success_login');
                redirect(base_url());
            } else {
                $_SESSION['errors'] = ___('user_login_error_login');
                redirect();
            }
        }

        $this->setMeta(___('tpl_login'));
    }

    public function logoutAction()
    {
        if (User::checkAuth()) {
            unset($_SESSION['user']);
        }
        redirect(base_url()) . 'user/login';
    }

    public function cabinetAction()
    {
        if (!User::checkAuth()) {
            redirect(base_url()) . 'user/login';
        }
        $this->setMeta(___('tpl_cabinet'));
    }

    //меню личного кабинета => Заказы
    public function ordersAction()
    {
        if (!User::checkAuth()) {
            redirect(base_url() . 'user/login');
        }
        $page = get('page');
        //$perpage = App::$app->getProperty('pagination');
        $perpage = 1;
        $total = $this->model->get_count_orders($_SESSION['user']['id']);
        $pagination = new Pagination($page, $perpage, $total);
        $start = $pagination->getStart();
        $orders = $this->model->getUserOrders($start, $perpage, $_SESSION['user']['id']);
        $this->setMeta(___('user_orders_title'));
        $this->set(compact('orders', 'pagination', 'total'));
    }

    //страница заказа
    public function orderAction()
    {
        if (!User::checkAuth()) {
            redirect(base_url() . 'user/login');
        }
        //id заказа
        $id = get('id');
        $order = $this->model->getUserOrder($id);

        if (!$order) {
            throw new \Exception('Not found order', 404);
        }
        $this->setMeta(___('user_order_title'));
        $this->set(compact('order'));
    }

    //файлы
    public function filesAction()
    {
        if (!User::checkAuth()) {
            redirect(base_url() . 'user/login');
        }
        $lang = App::$app->getProperty('language');
        $page = get('page');
        $perpage = App::$app->getProperty('pagination');
        $total = $this->model->getCountFiles();
        $pagination = new Pagination($page, $perpage, $total);
        $start = $pagination->getStart();

        $files = $this->model->getUserFiles($start, $perpage, $lang);
        $this->setMeta(___('user_files_title'));
        $this->set(compact('files', 'pagination', 'total'));
    }

    public function downloadAction()
    {
        if (!User::checkAuth()) {
            redirect(base_url() . 'user/login');
        }

        $id = get('id');
        $lang = App::$app->getProperty('language');
        $file = $this->model->getUserFile($id, $lang);
        if ($file) {
            $path = WWW . "/downloads/{$file['filename']}";
            if (file_exists($path)) {
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . basename($file['original_name']) . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
                header('Content-Length: ' . filesize($path));
                readfile($path);
                exit();
            } else {
                $_SESSION['errors'] = ___('user_download_error');
            }
        }
        redirect();
    }
}

//45 урок 26 мин
//view + language => credentials.php