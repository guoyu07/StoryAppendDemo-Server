<?php

/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class MController extends CController
{
    public $layout = '//layouts/common';
    public $base_url;
    public $request_urls;
    public $current_page;
    public $header_info = array();
    public $breadcrumbs = array();

    public function init()
    {
        Yii::app()->theme = 'mobile';

        $this->base_url = Yii::app()->params['WEB_PREFIX'];
        $this->setPageTitle('玩途自由行');
        $this->request_urls = array(
            'getAllCities' => $this->createUrl('home/cities'),
            /* Login/Register/Reset */
            'loginUser' => $this->createUrl('account/login'),
            'logoutUser' => $this->createUrl('account/logout'),
            'registerUser' => $this->createUrl('account/register'),
            'resetPassword' => $this->createUrl('account/resetPassword'),
            'addFavoriteProduct' => $this->createUrl('account/addFavoriteProduct'),
            'deleteFavoriteProduct' => $this->createUrl('account/deleteFavoriteProduct'),
        );
    }

    protected function getPostJsonData()
    {
        return json_decode(file_get_contents("php://input"), true);
    }

    public function getActionParams()
    {
        return $_GET + $_POST;
    }

    public function getParam($name, $defaultValue = null)
    {
        $result = Yii::app()->request->getParam($name, null);
        if (is_null($result)) {
            $result = $this->getAngularPost($name, $defaultValue);
        }

        return $result;
    }

    public function getParamOrSession($name, $defaultValue = null)
    {
        if (isset($_GET[$name]) || isset($_POST[$name])) {
            return $this->getParam($name);
        } else {
            return $this->getSession($name, $defaultValue);
        }
    }

    public function getSession($name, $defaultValue)
    {
        if (isset(Yii::app()->session[$name])) return Yii::app()->session[$name];

        return $defaultValue;
    }

    public function createUrl($route, $params = array(), $ampersand = '&')
    {
        if ($route === '')
            $route = $this->getId() . '/' . $this->getAction()->getId();
        elseif (strpos($route, '/') === false)
            $route = $this->getId() . '/' . $route;
        if ($route[0] !== '/' && ($module = $this->getModule()) !== null)
            $route = $module->getId() . '/' . $route;

        $url = Yii::app()->createUrl(trim($route, '/'), $params, $ampersand);
        if (!empty($params) && Yii::app()->urlManager->urlFormat == 'path' && strpos($url, '=') === false) {
            $url .= '/';
        }

        return $url;
    }

    private function getAngularPost($name, $defaultValue)
    {
        $result = $defaultValue;
        if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
            $data = $this->getPostJsonData();
            $result = isset($data[$name]) ? $data[$name] : $defaultValue;
        }

        return $result;
    }
}