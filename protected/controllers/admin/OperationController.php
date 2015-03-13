<?php

class OperationController extends AdminController
{
    public $layout = '//layouts/common';

    public function actionTodo() {
        $this->pageTitle = '待办事项';

        $request_urls = array(
            'fetchIncompleteSeo' => $this->createUrl('operation/getIncompleteSeo')
        );

        $this->request_urls = array_merge(
            $this->request_urls,
            $request_urls
        );
        $this->render('todo');
    }

    public function actionGetIncompleteSeo() {
        $connection = Yii::app()->db;
        $result = array(
            'home' => '',
            'countries' => '',
            'cities' => '',
            'products' => '',
            'product_groups' => '',
            'promotions' => '',
        );

        $result['home'] = !$this->isSeoMissing( 'home' );

        $country_sql = '
            SELECT c.country_code, c.cn_name FROM  (
                SELECT DISTINCT(co.country_code) AS country_code, co.cn_name as cn_name
                FROM `ht_country` AS co
                LEFT JOIN `ht_city` AS ci
                ON co.country_code = ci.country_code AND ci.has_online_product = 1
                WHERE ci.city_code IS NOT NULL
            ) c
            LEFT JOIN `ht_seo_setting` AS s
            ON c.country_code = s.id AND s.type = 2
            WHERE ( s.title IS NULL OR s.description IS NULL OR s.keywords IS NULL )
        ';
        $command = $connection->createCommand($country_sql);
        $result['countries'] = $command->queryAll();

        $city_sql = "
            SELECT c.city_code, c.cn_name FROM `ht_city` AS c
            LEFT JOIN `ht_seo_setting` AS s
            ON c.`city_code` = s.id AND s.type = 3
            WHERE c.has_online_product = 1
            AND ( s.title IS NULL OR s.description IS NULL OR s.keywords IS NULL )
        ";
        $command = $connection->createCommand($city_sql);
        $result['cities'] = $command->queryAll();

        /*$product_sql = "
            SELECT p.product_id, pd.name FROM `ht_product` AS p
            LEFT JOIN `ht_seo_setting` AS s
            ON p.`product_id` = s.id AND s.type = 4
            LEFT JOIN `ht_product_description` AS pd
            ON p.`product_id` = pd.`product_id`
            WHERE p.status = 3
            AND pd.language_id = 2
            AND ( s.title IS NULL OR s.description IS NULL OR s.keywords IS NULL )
        ";
        $command = $connection->createCommand($product_sql);
        $result['products'] = $command->queryAll();*/

        $product_group_sql = "
            SELECT pg.city_code, pg.group_id, pg.name, c.cn_name AS city_name FROM `ht_product_group` AS pg
            LEFT JOIN `ht_seo_setting` AS s
            ON pg.group_id = s.id AND s.type = 5
            LEFT JOIN `ht_city` AS c
            ON pg.city_code = c.city_code
            WHERE pg.status = 2
            AND pg.type IN (99)
            AND c.has_online_product = 1
            AND ( s.title IS NULL OR s.description IS NULL OR s.keywords IS NULL
             OR s.title = '' OR s.description = '' OR s.keywords = '')
        ";
        $command = $connection->createCommand($product_group_sql);
        $result['product_groups'] = $command->queryAll();

        $promotion_sql = "
            SELECT p.promotion_id, p.name FROM `ht_promotion` AS p
            LEFT JOIN `ht_seo_setting` AS s
            ON p.promotion_id = s.id AND s.type = 6
            WHERE p.status = 1
            AND ( s.title IS NULL OR s.description IS NULL OR s.keywords IS NULL
             OR s.title = '' OR s.description = '' OR s.keywords = '')
        ";
        $command = $connection->createCommand($promotion_sql);
        $result['promotions'] = $command->queryAll();

        EchoUtility::echoMsgTF( true, '获取SEO', $result );
    }

    private function isSeoMissing( $type, $identifier = false ) {
        $result_seo = array();
        if( $type == 'home' ) {
            $result_seo = HtSeoSetting::model()->findHomeSeoSetting();
        } else if( $type == 'country' ) {
            $result_seo = HtSeoSetting::model()->findByCountryCode( $identifier );
        } else if( $type == 'city' ) {
            $result_seo = HtSeoSetting::model()->findByCityCode( $identifier );
        } else if( $type == 'product' ) {
            $result_seo = HtSeoSetting::model()->findByProductId( $identifier );
        }
        return $result_seo['title'] && $result_seo['description'] && $result_seo['keywords'];
    }
}