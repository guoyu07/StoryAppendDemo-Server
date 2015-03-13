<?php

/**
 * Created by PhpStorm.
 * User: wenzi
 * Date: 14-07-15
 * Time: 上午10:39
 */
class DetailController extends Controller
{
    public function actionDetail()
    {
        $product_id = (int)$this->getParam('product_id');
        $this->redirect($this->createUrl('product/index', array('product_id' => $product_id)), true, 301);
    }
}
