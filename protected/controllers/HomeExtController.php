<?php

class HomeExtController extends Controller
{
    public $staticData;

    public function actionGuide2345()
    {
        $data = $this->initData();
        $this->current_page = 'guide2345';
        $this->header_info = array();
        $this->request_urls = array_merge(
            $this->request_urls,
            array(
                'getCarousel' => $this->createUrl('homeExt/carousel'),
                'getData' => $this->createUrl('homeExt/getData'),
            )
        );

        $seo_setting = HtSeoSetting::model()->findHomeSeoSetting();
        $this->initDataBySEOSetting($seo_setting);

        $this->staticData = array(
//            str_replace(array("/","?","=","&"),'_',$this->request_urls['getCities'])=>$this->datamation($this->actionCities(true)),
        );

        $this->render('guide2345', $data);
    }

    public function actionCarousel($rawData = false)
    {
        $carousels = HtHomeCarousel::model()->getAllOnline(HtHomeCarousel::T_2345_URL);

        if ($rawData) {
            return $carousels;
        } else {
            EchoUtility::echoJson($carousels);
        }

    }

    public function actionGetData()
    {
        $key = '2345nav_home';
        $data = Yii::app()->cache->get($key);
        if (!empty($data)) {
            EchoUtility::echoJson($data);
            return;
        }

        $groups = array(
          '0' => array(
              'name' => '全球乐园',
              'ids' => array('1305','884','2106','1480','915','2961'),
          ),
          '1' => array(
              'name' => '浮潜胜地',
              'ids' => array('3178','2866','2368','1562','1996','1806'),
          ),
          '2' => array(
              'name' => '购物血拼',
              'ids' => array('1704','1860','1551','1092','1802','1614'),
          ),
          '3' => array(
              'name' => '城市通票',
              'ids' => array('1484','690','737','236','724','1232'),
          ),
          '4' => array(
              'name' => '新奇体验',
              'ids' => array('1440','1334','1388','3030','1643','3348'),
          ),
        );
        foreach($groups as &$group){
            foreach($group['ids'] as $id){
                if (HtProduct::model()->isProductVisible($id)) {
                    $product_info = HtProduct::getProductInfo($id);
                    $product_info['city_url'] = $this->getProductCityUrl($id);
                    $group['products'][] = $product_info;
                }
            }
            unset($group['ids']);
        }


        $lines_product = array('3379','2503','3472','2586');
        $lines = array();
        foreach($lines_product as &$pid){
            if (HtProduct::model()->isProductVisible($pid)) {
                $product_info = HtProduct::getProductInfo($pid);
                $product_info['city_url'] = $this->getProductCityUrl($pid);
                $line_product_info = HtProductGroup::getLineProductInfo($pid);
                $product_info = array_merge($product_info,$line_product_info);
                $lines[] = $product_info;
            }
        }

        $hotel_products = array('3492','3192','3167','3517','3535','3132');
        $hotels = array();
        foreach($hotel_products as $pid){
            $binds = array();
            if (HtProduct::model()->isProductVisible($pid)) {
                $product_info = HtProduct::getProductInfo($pid);
                $product_info['city_url'] = $this->getProductCityUrl($pid);
                $bundle = Converter::convertModelToArray(HtProductBundle::model()->with('items')->findByAttributes(['product_id' => $pid, 'group_type' => HtProductBundle::GT_SELECTION]));
                if(!empty($bundle['items'])) {
                    foreach($bundle['items'] as $item) {
                        $binds[] = $item['binding_product_id'];
                    }
                }

                $criteria = new CDbCriteria();
                $criteria->addInCondition('product_id', $binds);
                $criteria->addCondition(['order' => 'star_level']);

                $hotel = HtProductHotel::model()->find($criteria);
                if(!empty($hotel)) {
                    $product_info['star_level'] = $hotel['star_level'];
                    $product_info['location'] = $hotel['location'];
                }
                $hotels[] = $product_info;
            }
        }
        $data = array('groups'=>$groups,'lines'=>$lines,'hotels'=>$hotels);
        Yii::app()->cache->set($key, $data, 24 * 60 * 60);
        EchoUtility::echoJson($data);
    }

    private function getProductCityUrl($pid)
    {
        $product = HtProduct::model()->with('city.country')->findByPk($pid);
        $country_name = str_replace(' ', '_', $product['city']['country']['en_name']);
        $city_name = str_replace(' ', '_', $product['city']['en_name']);
        $city_url = Yii::app()->urlManager->createUrl('city/index',
                                                            array('city_name' => $city_name, 'country_name' => $country_name));
        return $city_url;
    }
}