<?php

/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class AController extends CController
{
    public $layout = '//layouts/common';
    public $base_url;

    public function init()
    {
        Yii::app()->theme = 'app';

        $this->base_url = Yii::app()->params['WEB_PREFIX'];
    }
}