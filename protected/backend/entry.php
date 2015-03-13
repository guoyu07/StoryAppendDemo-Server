<?php
/**
 * Created by PhpStorm.
 */
/**
 * @project hitour.server
 * @file entry.php
 * @author xudong(zxd@hitour.cc)
 * @version 1.0
 * @date 14-5-24 下午6:11
**/
$yii = 'yii/framework/yiic.php';
$config = dirname(__FILE__) . '/../config/console.php';
@putenv('YII_CONSOLE_COMMANDS='. dirname(__FILE__).'/../backend' );

require_once($yii);