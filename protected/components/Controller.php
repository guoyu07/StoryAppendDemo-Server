<?php

/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
    /**
     * @var string the default layout for the controller view. Defaults to '//layouts/center',
     * meaning using a single column layout. See 'protected/views/layouts/center.php'.
     */
    public $layout = '//layouts/common';
    /**
     * @var array context menu items. This property will be assigned to {@link CMenu::items}.
     */
    public $request_urls;
    public $menu = array();
    public $navigator_data;
    /**
     * @var array the breadcrumbs of the current page. The value of this property will
     * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
     * for more details on how to specify this property.
     */
    public $base_url;
    public $current_page;
    public $header_info = array();
    public $breadcrumbs = array();

    private $_keywords;
    private $_description;

    public function isMobile()
    {
        return HTTPRequest::isMobile();
    }

    private function requestNeedRedirect()
    {
        $result = false;
        $url = parse_url($_SERVER['REQUEST_URI']);
        $path = $url['path'];
        if($path == '/') { // 首页
            $result = true;
        } else if(isset($_REQUEST['country_name']) && isset($_REQUEST['city_name'])) { // 城市
            $result = true;
        } else if(false !== stripos($path, 'sightseeing')) { // 产品
            $result = true;
        } else if(strrpos($path, '/') == 0 && isset($_REQUEST['en_name'])) { // 国家
            $result = true;
        } else if(false !== stripos($path, 'group/index') && isset($_REQUEST['group_id'])) { // 分组
            if(stripos($url['path'], '/group/index') === 0) {
                $result = true;
            }
        } else if(false !== stripos($path, '/activity/')) { // 活动页
            if(in_array($url['path'],
                        ['/activity/fridaysale', '/activity/newyearsale', '/activity/summersale', '/activity/kidadult', '/activity/1111', '/activity/1212', '/activity/shopping', '/activity/ccbsale', '/activity/happymuseum'])) {
                $result = true;
            }
        } else if(stripos($path, '/promotion/hotelplus') !== false && stripos($path, '/promotion/hotelplusdata') === false) {
            $result = true;
        } else if(!(stripos($path, '/promotion/') === false) && (stripos($path, '/promotion/promotionDetail') === false) && stripos($path, '/promotion/hotelplusdata') === false) {
            $result = true;
        } else if((false !== stripos($path, 'column/index') || preg_match('/column\/\d+/', $path)) && isset($_REQUEST['column_id'])) { // 分组
            $result = true;
        }

        return $result;
    }

    private function needRedirect()
    {
        $result = $this->isMobile();
        if($result) {
            $result = $this->requestNeedRedirect();
        }

        return $result;
    }

    private function redirectToMobile()
    {
        $url = parse_url($_SERVER['REQUEST_URI']);
        $path = $url['path'];
        $query = empty($url['query']) ? '' : ('?' . $url['query']);

        if(isset($_REQUEST['en_name'])) { // -- No country page on mobile
            $country_name = str_replace('_', ' ', $this->getParam('en_name'));
            $country_info = HtCountry::model()->getByEnName($country_name);
            if(empty($country_info)) {
                $this->redirect($this->createUrl('mobile/index' . $query));
            }

            $this->redirect($this->createUrl('mobile' . $query . '#/country/' . $country_info['country_code']));
        } else if(isset($_REQUEST['country_name']) && isset($_REQUEST['city_name'])) { // -- could not get city code.
            $country_name = str_replace('_', ' ', $this->getParam('country_name'));
            $city_name = str_replace('_', ' ', $this->getParam('city_name'));
            $country_info = HtCountry::model()->getByEnName($country_name);
            if(empty($country_info)) {
                $this->redirect($this->createUrl('mobile/index' . $query));
            }
            $city_info = HtCity::model()->getByCountryCodeEnName($country_info['country_code'], $city_name);
            if(empty($city_info)) {
                $this->redirect($this->createUrl('mobile/index' . $query));
            }

            $this->redirect($this->createUrl('mobile' . $query . '#/city/' . $city_info['city_code']));
        } else if(isset($_REQUEST['product_id'])) { // -- redirect to product
            $hash = str_replace('/sightseeing/', '/product/', $path);
            $this->redirect($this->createUrl('mobile' . $query . '#' . $hash));
        } else if(!(stripos($path, '/group/') === false)) {
            $this->redirect($this->createUrl('mobile' . $query . '#/product_group/city/' . $_REQUEST['group_id']));
        } else if(!(stripos($path, '/activity/') === false)) {
            $this->redirect($this->createUrl('mobile' . $query . '#' . $path));
        } else if(!(stripos($path, '/promotion/') === false)) { // -- redirect to promotion
            if(!(stripos($path, '/promotion/hotelplus') === false)) {
                $this->redirect($this->createUrl('mobile' . $query . '#/promotion_hotel'));
            } else {
                $this->redirect($this->createUrl('mobile' . $query . '#/promotion/' . $_REQUEST['promotion_id']));
            }
        } else if((false !== stripos($_SERVER['REQUEST_URI'], 'column/index') || preg_match('/column\/\d+/', $_SERVER['REQUEST_URI'])) && isset($_REQUEST['column_id'])) { // 分组
            $this->redirect($this->createUrl('mobile' . $query . '#/article/' . $_REQUEST['column_id']));
        } else {
            $this->redirect($this->createUrl('mobile/index' . $query));
        }
    }

    public function filterAccessControl($filterChain)
    {
        //记录 Session 建立时第一个 uri 以及 url_referer
        if(!isset(Yii::app()->session['url_referer'])) {
            $url_referer = empty($_SERVER['HTTP_REFERER']) ? '' : $_SERVER['HTTP_REFERER'];
            $uri = empty($_SERVER['REQUEST_URI']) ? '' : $_SERVER['REQUEST_URI'];
            Yii::app()->session->add('url_referer', $url_referer);
            Yii::app()->session->add('first_uri', $uri);
//            Yii::log('SessionX:' . Yii::app()->session->sessionID . ',urlRef:' . $url_referer . ',uri:' . $uri, CLogger::LEVEL_ERROR);
        }

        if(!(false === stripos($_SERVER['REQUEST_URI'], 'alpaca'))) {
            $this->redirect('http://hiworld.hitour.cc/' . $_SERVER['REQUEST_URI'], true, 301);
        } else if($this->needRedirect()) {
            $this->redirectToMobile();
        } else {
            $filterChain->run();
        }
    }

    public function filters()
    {
        return array(
            'accessControl'
        );
    }

    private function hasGroup($country_code, $city_groups)
    {
        $found = false;
        foreach($city_groups as $city_group) {
            if($country_code == $city_group['country_code']) {
                $found = true;

                break;
            }
        }

        return $found;
    }

    private function getGroups($country, $city_groups)
    {
        $result = array();
        foreach($city_groups as $city_group) {
            if($country['country_code'] == $city_group['country_code']) {
                $result[] = array('name' => $city_group['name'],
                    'cities' => $this->getCities($city_group['city_codes'], $country['cities']));
            }
        }

        return $result;
    }

    private function getCities($city_codes, $cities)
    {
        $result = array();
        $city_code_list = explode(",", $city_codes);
        foreach($cities as $city) {
            if(in_array($city['city_code'], $city_code_list)) {
                array_push($result, $city);
            }
        }

        return $result;
    }

    private function getNavigatorData($rawData = false)
    {
        $continents = HtContinent::model()->findAllWithContriesCities();

        $city_groups = HtCityGroup::model()->getAllCached();

        foreach($continents as &$continent) {
            foreach($continent['countries'] as &$country) {
                if($this->hasGroup($country['country_code'], $city_groups)) {
                    $country['city_groups'] = $this->getGroups($country, $city_groups);
                } else {
                    $cities = $country['cities'];
                    $country['city_groups'] = array(array('name' => '全部', 'cities' => $cities));
                }
                unset($country['cities']);
            }
        }

        return $continents;
    }

    function initData()
    {

        $this->base_url = Yii::app()->params['WEB_PREFIX'];
        $this->setPageTitle('玩途自由行');
        $this->request_urls = array(
            'headerInfo' => $this->createUrl('home/cities'),
            'headerCityInfo' => $this->createUrl('home/citiesInGroup'),
            'headerLogin' => $this->createUrl('account/login'),
            'headerLogout' => $this->createUrl('account/logout'),
            'headerForget' => $this->createUrl('account/resetPassword'),
            'headerRegister' => $this->createUrl('account/register'),
            'headerRegisterByPhone' => $this->createUrl('account/registerByPhone'),
            'verifyPhone' => $this->createUrl('mobile/verifyPhone'),
            'headerAccount' => $this->createUrl('account/account') . '#account',
            'headerOrders' => $this->createUrl('account/account') . '#orders',
            'headerFavorite' => $this->createUrl('account/favorite'),
            'headerCoupon' => $this->createUrl('account/account') . '#coupon',
            'headerFund' => $this->createUrl('account/tourfund'),
            'home' => $this->createUrl('home/index'),
            'bindThird' => $this->createUrl('account/bindThird'),
            'error' => $this->createUrl('site/error'),
            'addFavoriteProduct' => $this->createUrl('account/addFavoriteProduct'),
            'deleteFavoriteProduct' => $this->createUrl('account/deleteFavoriteProduct'),
            'getFavoriteProducts' => $this->createUrl('account/getFavoriteProducts'),
        );
        $this->navigator_data = $this->getNavigatorData();

        return array();
    }

    public function datamation($data, $k = '', $wrap = 'div')
    {
        $html = '';
        $firstKey = false;
        $nextWrap = 'div';
        if(empty($data)) {
            $wrap = 'ul';
        }

        foreach($data as $key => $item) {
            if($firstKey === false) {
                $firstKey = $key;
            }

            if($firstKey === 0) {
                $wrap = 'ul';
                $nextWrap = 'li';
            }

            // echo $firstKey,'->',$key,"  [",gettype($item),']===',htmlentities($html),'<br>';
            if(gettype($item) == 'array') {
                $html .= self::datamation($item, ' data-n="' . $key . '"', $nextWrap);
            } else {
                if($key == 'link_url') {
                    $html .= '<a data-n="' . $key . '" href="' . $item . '">地址</a>';
                } else {
                    $html .= '<span data-n="' . $key . '">' . htmlentities(html_entity_decode($item)) . '</span>';
                }

            }
            /*if($firstKey===0){
              $html.='</li>';
            }*/
            // echo $firstKey,'->',$key,"  [",gettype($item),']===',htmlentities($html),'<br>';
        }

        return '<' . $wrap . $k . '>' . $html . '</' . $wrap . '>';
        //return $outer==1?'<ul>'.$html.'</ul>':$html;
    }

    public function getActionParams()
    {
        return $_GET + $_POST;
    }

    public function getPost($name, $defaultValue = null)
    {
        $result = Yii::app()->request->getPost($name, null);
        if(is_null($result)) {
            $result = $this->getAngularPost($name, $defaultValue);
        }

        return $result;
    }

    private function getAngularPost($name, $defaultValue)
    {
        $result = $defaultValue;
        if(strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
            $data = $this->getPostJsonData();
            $result = isset($data[$name]) ? $data[$name] : $defaultValue;
        }

        return $result;
    }

    protected function getPostJsonData()
    {
        return json_decode(file_get_contents("php://input"), true);
    }

    public function getParamOrSession($name, $defaultValue = null)
    {
        if(isset($_GET[$name]) || isset($_POST[$name])) {
            return $this->getParam($name);
        } else {
            return $this->getSession($name, $defaultValue);
        }
    }

    public function getParam($name, $defaultValue = null)
    {
        $result = Yii::app()->request->getParam($name, null);
        if(is_null($result)) {
            $result = $this->getAngularPost($name, $defaultValue);
        }

        return $result;
    }

    public function getSession($name, $defaultValue)
    {
        if(isset(Yii::app()->session[$name])) return Yii::app()->session[$name];

        return $defaultValue;
    }

    public function getKeywords()
    {
        if($this->_keywords) {
            return $this->_keywords;
        } else {
            return $this->getPageTitle();
        }
    }

    public function setKeywords($value)
    {
        $this->_keywords = $value;
    }

    public function getDescription()
    {
        if($this->_description) {
            return $this->_description;
        } else {
            return $this->getPageTitle();
        }
    }

    public function setDescription($value)
    {
        $this->_description = $value;
    }

    public function getCached($key)
    {
        if(!empty(Yii::app()->cache) && empty(Yii::app()->params['DEBUG'])) {
            return Yii::app()->cache->get($key);
        }

        return false;
    }

    public function setCache($key, $value, $expire = 300)
    {
        if(!empty(Yii::app()->cache)) {
            Yii::app()->cache->set($key, $value, $expire);
        }
    }

    protected function getFromCacheFirst($key, $function, $params = [], $expire = 300)
    {
        $result = $this->getCached($key);
        if(empty($result)) {
            $result = call_user_func_array($function, $params);

            $this->setCache($key, $result, $expire);
        }

        return $result;
    }

    public function initDataBySEOSetting($seo_setting)
    {
        if($seo_setting) {
            $this->setPageTitle($seo_setting['title']);
            $this->setKeywords($seo_setting['keywords']);
            $this->setDescription($seo_setting['description']);
        }
    }

}
