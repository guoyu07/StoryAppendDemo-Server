<?php

/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class AdminController extends CController
{
    /**
     * @var string the default layout for the controller view. Defaults to '//layouts/center',
     * meaning using a single column layout. See 'protected/views/layouts/center.php'.
     */
    public $layout = '//layouts/center';
    public $base_url;
    public $fe_options = array();
    public $request_urls = array();
    public $resource_refs;
    /**
     * @var array context menu items. This property will be assigned to {@link CMenu::items}.
     */
    public $menu = array();
    /**
     * @var array the breadcrumbs of the current page. The value of this property will
     * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
     * for more details on how to specify this property.
     */
    public $breadcrumbs = array();

    public function getActionParams()
    {
        return $_GET + $_POST;
    }

    public function filterAccessControl($filterChain)
    {
        if (Yii::app()->user->isGuest) {
            $this->redirect($this->createUrl('site/login'));
        } else {
            if (Yii::app()->user->checkAccess('HT_View')) {
                $filterChain->run();
            } else {
                $this->redirect($this->createUrl('site/login'));
            }
        }
    }

    public function filters()
    {
        return array(
            'accessControl'
        );
    }

    public function init()
    {
        Yii::app()->theme = 'admin';
        $this->fe_options['is_test'] = Yii::app()->params['IS_TEST'];
        $this->request_urls = array(
            'baseUrl' => Yii::app()->getBaseUrl(true),
            'themeUrl' => Yii::app()->theme->baseUrl,
            'onlineUrl' => 'http://hitour.cc',
            //Site Pages
            'viewHomeUrl' => Yii::app()->getBaseUrl(true),
            'editHomeUrl' => $this->createUrl('home/index'),
            'viewCountryUrl' => $this->createFrontUrl('country/index', array('country_name' => '')),
            'editCountryUrl' => $this->createUrl('country/edit', array('country_code' => '')),
            'viewCityUrl' => $this->createFrontUrl('city/index', array('country_name' => 'XXX', 'city_name' => '')),
            'editCityUrl' => $this->createUrl('city/edit', array('city_code' => '')),
            'viewProductOnTestUrl' => Yii::app()->params['urlPreviewOnTest'],
            'viewProductUrl' => $this->createFrontUrl('product/index', array('product_id' => '')),
            'editProductUrl' => $this->createUrl('product/edit', array('product_id' => '')),
            //Backend Pages - Coupon
            'listCouponUrl' => $this->createUrl('coupon/index'),
            'editCouponUrl' => $this->createUrl('coupon/edit', array('coupon_id' => '')),
            'editCouponTemplateUrl' => $this->createUrl('coupon/template', array('template_id' => '')),
            //Backend Pages - Promotion
            'listPromotionUrl' => $this->createUrl('promotion/index'),
            'editPromotionUrl' => $this->createUrl('promotion/edit', array('promotion_id' => '')),
            'previewPromotionUrl' => $this->createUrl('promotion/edit', array('promotion_id' => '')),
            //Ajax Requests
            'fetchCities' => $this->createUrl('city/getCities'),
            'fetchCountries' => $this->createUrl('country/getCountries'),
            'fetchSuppliers' => $this->createUrl('supplier/getSuppliers'),
        );
    }

    public function getParam($name, $defaultValue = null)
    {
        $result = Yii::app()->request->getParam($name, null);
        if (is_null($result)) {
            $result = $this->getAngularPost($name, $defaultValue);
        }

        return $result;
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

    public function createUrl($route, $params = array(), $ampersand = '&', $isAdmin = true)
    {
        if ($route === '')
            $route = $this->getId() . '/' . $this->getAction()->getId();
        elseif (strpos($route, '/') === false)
            $route = $this->getId() . '/' . $route;
        if ($route[0] !== '/' && ($module = $this->getModule()) !== null)
            $route = $module->getId() . '/' . $route;

        $url = Yii::app()->createUrl(trim($route, '/'), $params, $ampersand);
        if ($isAdmin && strlen($route) > 1) {
            $url = Yii::app()->createUrl(trim('admin/' . $route, '/'), $params, $ampersand);

            // hack to fix issue of path format and with params like array('product_id' => '')
            if (Yii::app()->urlManager->urlFormat == 'path') {
                if (!empty($params)) {
                    $empty_value = false;
                    foreach ($params as $k => $v) {
                        if (empty($v)) {
                            $empty_value = true;
                            break;
                        }
                    }
                    if ($empty_value && strpos($url, '=') === false) {
                        $url .= '/';
                    }
                }
            }
        } else {
            if (!empty($params) && Yii::app()->urlManager->urlFormat == 'path' && strpos($url, '=') === false) {
                $url .= '/';
            }
        }

        return $url;
    }

    public function createFrontUrl($route, $param = array()) {
        $result = Yii::app()->createUrl($route, $param, '', false);
        $base_url = Yii::app()->getBaseUrl(true);
        if(strpos($base_url, 'backend') !== false) {
            $base_url = 'http://hitour.cc';
        } else if(strpos($base_url, 'dev.hitour.cc') !== false) {
            $base_url = 'http://dev.hitour.cc';
        }

        return $base_url . $result;
    }

    protected function getGridOptions($use_filter = true)
    {
        $data = $this->getPostJsonData();

        $c = new CDbCriteria();
        $order = '';
        foreach ($data['sort'] as $order_field => $order_dir) {
            $order .= ', ' . $order_field . ' ' . ($order_dir == 1 ? 'ASC' : 'DESC');
        }
        $c->order = substr($order, 2);
        $c->limit = $data['paging']['limit'];
        $c->offset = $data['paging']['start'];
        if ($use_filter && $data['record_filter']) {
            $c->select = implode(',', $data['record_filter']);
        }

        return $c;
    }

    protected function getPostJsonData()
    {
        return json_decode(file_get_contents("php://input"), true);
    }

    public function getCached($key)
    {
        if (!empty(Yii::app()->cache) && empty(Yii::app()->params['DEBUG'])) {
            return Yii::app()->cache->get($key);
        }

        return false;
    }

    public function setCache($key, $value, $expire = 300)
    {
        if (!empty(Yii::app()->cache)) {
            Yii::app()->cache->set($key, $value, $expire);
        }
    }
}