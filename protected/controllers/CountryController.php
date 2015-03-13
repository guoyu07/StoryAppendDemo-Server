<?php

/**
 * Created by PhpStorm.
 * User: wenzi
 * Date: 14-5-9
 * Time: 上午10:39
 */
class CountryController extends Controller
{
    public $resource_refs = 'country.res';
    public $staticData;

    public function actionIndex()
    {
        $en_name = Yii::app()->request->getParam('en_name');
        $en_name = str_replace('_', ' ', $en_name);

        $country_info = HtCountry::model()->getByEnName($en_name);
        if (empty($country_info)) {
            $this->redirect($this->createUrl('site/error'));
        }

        $country_code = $country_info['country_code'];

//        $country_info = HtCountry::model()->findByPk($country_code);
        $data = $this->initData();
        $this->current_page = 'country';
        $this->header_info = array(
            'country' => array(
                'cn_name'      => $country_info['cn_name'],
                'country_code' => $country_info['country_code'],
                'link_url'     => ''
            ),
            'city'    => ''
        );

        $has_tab = HtCountryTab::hasTab($country_code);
        $view_name = 'main';
        $request_urls = ['getCities' => $this->createUrl('country/cities', ["country_code" => $country_code])];
        if ($has_tab) {
            $this->resource_refs = 'country_extra.res';
            $request_urls = ['countryTabs' => $this->createUrl('country/countryTabs',
                                                               ["country_code" => $country_code])];
            $view_name = 'country_extra';
        }

        $this->request_urls = array_merge($this->request_urls, $request_urls);

        $this->staticData = array(//            str_replace(array("/","?","=","&"),'_',$this->request_urls['getCities'])=>$this->datamation($this->actionCities(true,$country_code)),
        );
        $seo_setting = HtSeoSetting::model()->findByCountryCode($country_info['country_code']);
        $this->initDataBySEOSetting($seo_setting);

        $this->render($view_name, $data);
    }

    public function actionCities($rawData = false, $country_code = false)
    {
        if (!$country_code) {
            $country_code = $this->getParam('country_code');
        }

        $country = HtCountry::model()->getCountryWithCountryImage($country_code);

        if (!$country) {
            $this->redirect($this->createUrl('site/error'));

            return;
        }

        $country_cities = $this->getCountryCities($country_code);
        $country = array_merge($country, $country_cities);

        if ($rawData) {
            return $country;
        } else {
            EchoUtility::echojson($country);
        }
    }

    private function getCountryCities($country_code)
    {
        $result = [];
        $result['city_groups'] = HtCityGroup::model()->getAllOfCountry($country_code);

        if (isset($result['city_groups']) && count($result['city_groups'])) {
            foreach ($result['city_groups'] as &$cg) {
                $cg['cities'] = array();
                $city_codes = explode(',', $cg['city_codes']);
                foreach ($city_codes as $c) {
                    $cg['cities'][] = HtCity::model()->getCityWithCityImage($c);
                }
            }
        } else {
            $result['cities'] = HtCity::model()->getCitiesWithCityImageHasProductsOnline($country_code);
        }

        return $result;
    }

    public function actionCountryTabs()
    {
        $country_code = $this->getParam('country_code');

        $country = HtCountry::model()->getCountryWithCountryImage($country_code);

        $tabs_data = HtCountryTab::getTabs($country_code);

        EchoUtility::echoCommonMsg(200, '', ['country' => $country, 'tabs' => $tabs_data]);
    }

    public function actionMobileCountryTabs()
    {
        $country_code = $this->getParam('country_code');
        $country = HtCountry::model()->getCountryWithCountryImage($country_code);

        $has_tab = HtCountryTab::hasTab($country_code);
        if ($has_tab) {

            $tabs_data = HtCountryTab::getTabs($country_code);

            $result = ['country' => $country, 'tabs' => $tabs_data, 'has_tab' => 1];
        } else {
            $country_cities = $this->getCountryCities($country_code);
            $country = array_merge($country, $country_cities);
            $result = ['country' => $country, 'has_tab' => 0];
        }

        EchoUtility::echoCommonMsg(200, '', $result);
    }
}