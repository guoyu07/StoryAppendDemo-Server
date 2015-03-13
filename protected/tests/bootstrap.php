<?php

// change the following paths if necessary
$yiit='yii/framework/yiit.php';
$config=dirname(__FILE__).'/../config/test.php';

require_once($yiit);
require_once(dirname(__FILE__).'/HiWebTestCase.php');
//require_once(dirname(__FILE__).'/HiDbTestCase.php');

Yii::createWebApplication($config);
