<?php

/**
 * Created by PhpStorm.
 * User: xingminglister
 * Date: 5/28/14
 * Time: 2:14 PM
 */
class AdController extends Controller
{
    public function actionIndex()
    {
        $url = $this->getParam('url');
        if($url){
            $this->redirect($url);
        }
    }

}