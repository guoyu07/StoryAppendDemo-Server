<?php
/**
 * Created by PhpStorm.
 * User: wenzi
 * Date: 14-5-26
 * Time: 上午10:39
 */
class CommonController extends Controller{
    public function actionCities()
    {
        $continents = HtContinent::model()->findAllWithContriesCities();

        echo CJSON::encode(array(
            'code' => 200,
            'msg' => '',
            'data' => $continents,
        ));
    }
} 
