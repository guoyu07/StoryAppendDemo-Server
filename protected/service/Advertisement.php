<?php

/**
 * Created by PhpStorm.
 * User: wenzi
 * Date: 5/22/14
 * Time: 16:05 PM
 */
class Advertisement extends CComponent
{

    public function init()
    {
        return true;
    }


    public function getAdByCountry($country_code,$product_id)
    {
        $ad = array();
        $default_ad = array('image_url'=>'',);
        $ads_all = array(
            'HK'=>array(
            ),
        );

        if(isset($ads_all[$country_code])){
            $ads = $ads_all[$country_code];
            $idx = rand(0,count($ads));
            if(isset($ads[$idx])){
                $ad= $ads[$idx];
            }
        }else{
            $ad = $default_ad;
        }

        if(isset($ad['seller'])){
            $ad['link_url'] = Yii::app()->urlmanager->createUrl('ad/index',['url'=>'http://www.unionpayintl.com/shopping','src'=>$product_id]);
        }else{
            $ad['link_url'] = Yii::app()->urlmanager->createUrl('activity/summersale');
        }

        return $default_ad;
    }

}