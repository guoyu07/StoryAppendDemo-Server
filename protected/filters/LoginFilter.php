<?php
/**
 * Created by PhpStorm.
 * User: app
 * Date: 14-5-4
 * Time: 下午3:21
 */

class LoginFilter extends CFilter {
    protected function preFilter($filterChain)
    {
        if (Yii::app()->user->isGuest) {
            Yii::app()->user->loginRequired();
            $filterChain->run();
        }else{
            return true;
        }
    }
} 